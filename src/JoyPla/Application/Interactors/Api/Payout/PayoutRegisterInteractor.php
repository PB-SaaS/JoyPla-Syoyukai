<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\Payout {
    use App\Model\Division;
    use App\SpiralDb\StockView;
    use App\SpiralDb\PayoutItem as SpiralDbPayoutItem;
    use App\SpiralDb\Card;
    use Exception;
    use JoyPla\Application\InputPorts\Api\Payout\PayoutRegisterInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Payout\PayoutRegisterInputData;
    use JoyPla\Application\OutputPorts\Api\Payout\PayoutRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Payout\PayoutRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\PayoutHId;
    use JoyPla\Enterprise\Models\Payout;
    use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
    use JoyPla\Enterprise\Models\Hospital;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\HospitalName;
    use JoyPla\Enterprise\Models\RequestItemCount;
    use JoyPla\Enterprise\Models\Pref;
    use JoyPla\Enterprise\Models\CardId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\InterfaceAdapters\GateWays\Repository\PayoutRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\RequestItemCountRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InventoryCalculationRepositoryInterface;
    use JoyPla\Service\Presenter\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class PayoutRegisterInteractor
     * @package JoyPla\Application\Interactors\Payout\Api
     */
    class PayoutRegisterInteractor implements PayoutRegisterInputPortInterface
    {
        private PresenterProvider $presenterProvider;
        private RepositoryProvider $repositoryProvider;

        public function __construct(
            PresenterProvider $presenterProvider,
            RepositoryProvider $repositoryProvider
        ) {
            $this->presenterProvider = $presenterProvider;
            $this->repositoryProvider = $repositoryProvider;
        }

        /**
         * @param PayoutRegisterInputData $inputData
         */
        public function handle(PayoutRegisterInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            $inputData->payoutItems = array_map(function ($v) use ($inputData) {
                if (
                    $inputData->isOnlyMyDivision &&
                    $inputData->user->divisionId !== $v->payoutSourceDivisionId
                ) {
                    throw new Exception('Illegal request', 403);
                }
                if ($v->payoutSourceDivisionId == $v->payoutTargetDivisionId) {
                    throw new Exception('Invalid request', 999);
                }
                return $v;
            }, $inputData->payoutItems);

            $cardIds = [];
            foreach ($inputData->payoutItems as $i) {
                if ($i->card) {
                    $cardIds[] = new CardId($i->card);
                }
            }

            $payoutItems = $this->repository->findByInHospitalItem(
                $hospitalId,
                $inputData->payoutItems
            );

            if (count($payoutItems) === 0) {
                throw new Exception("payout items don't exist.", 999);
            }

            foreach ($payoutItems as $i) {
                if ($i->getLotManagement() && $i->getLot()->isEmpty()) {
                    throw new Exception('invalid lot.', 100);
                }
            }

            $ids = [];
            $result = [];

            foreach ($payoutItems as $i) {
                $exist = false;
                foreach ($result as $key => $r) {
                    if (
                        $r->equalDivisions(
                            $i->getSourceDivision(),
                            $i->getTargetDivision()
                        )
                    ) {
                        $exist = true;
                        $result[$key] = $r->addPayoutItem($i);
                    }
                }
                if ($exist) {
                    continue;
                }

                $id = PayoutHId::generate();
                $ids[] = $id->value();
                //登録時には病院名は必要ないので、いったんhogeでいい
                $result[] = new Payout(
                    $id,
                    new DateYearMonthDayHourMinutesSecond(''),
                    [$i],
                    new Hospital(
                        $hospitalId,
                        new HospitalName('hoge'),
                        '',
                        '',
                        new Pref(''),
                        ''
                    ),
                    $i->getSourceDivision(),
                    $i->getTargetDivision()
                );
            }

            $stockViewInstance = StockView::where(
                'hospitalId',
                $hospitalId->value()
            );
            foreach ($result as $payout) {
                $stockViewInstance->orWhere(
                    'divisionId',
                    $payout
                        ->getSourceDivision()
                        ->getDivisionId()
                        ->value()
                );
                foreach ($payout->getPayoutItems() as $payoutItem) {
                    $stockViewInstance->orWhere(
                        'inHospitalItemId',
                        $payoutItem->getInHospitalItemId()->value()
                    );
                }
            }

            $stocks = $stockViewInstance->get();

            if ((int) $stocks->count === 0) {
                throw new Exception("Stocks don't exist.", 998);
            }

            $stocks = $stocks->data->all();
            $requestItemCounts = [];
            $payoutItemCounts = [];
            foreach ($result as $payout) {
                foreach ($payout->getPayoutItems() as $item) {
                    $payoutItemCounts[] =
                        $item->getInHospitalItemId()->value() .
                        $payout
                            ->getSourceDivision()
                            ->getDivisionId()
                            ->value() .
                        $payout
                            ->getTargetDivision()
                            ->getDivisionId()
                            ->value();
                    foreach ($stocks as $stock) {
                        if (
                            $payout
                                ->getSourceDivision()
                                ->getDivisionId()
                                ->value() === $stock->divisionId &&
                            $item->getInHospitalItemId()->value() ===
                                $stock->inHospitalItemId
                        ) {
                            $requestItemCounts[] = new RequestItemCount(
                                $stock->recordId,
                                $hospitalId,
                                $item->getInHospitalItemId(),
                                $item->getItem()->getItemId(),
                                (int) $item->getPayoutQuantity()->value() * -1,
                                $payout->getTargetDivision()->getDivisionId(),
                                $payout->getSourceDivision()->getDivisionId()
                            );
                        }
                    }
                }
            }

            if (count($requestItemCounts) !== count($payoutItemCounts)) {
                throw new Exception("Stocks don't exist.", 998);
            }

            $inventoryCalculations = [];
            foreach ($result as $r) {
                foreach ($r->getPayoutItems() as $item) {
                    $inventoryCalculations[] = new InventoryCalculation(
                        $item->getHospitalId(),
                        $item->getSourceDivision()->getDivisionId(),
                        $item->getInHospitalItemId(),
                        0,
                        4,
                        $item->getLot(),
                        $item->getPayoutQuantity()->value() * -1
                    );
                    $inventoryCalculations[] = new InventoryCalculation(
                        $item->getHospitalId(),
                        $item->getTargetDivision()->getDivisionId(),
                        $item->getInHospitalItemId(),
                        0,
                        5,
                        $item->getLot(),
                        $item->getPayoutQuantity()->value()
                    );
                }
            }

            $this->repositoryProvider
                ->getRequestItemCountRepository()
                ->saveToArray($requestItemCounts);

            $this->repository->saveToArray($result);

            $this->repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);

            if (count($cardIds) > 0) {
                $payoutItemInstance = SpiralDbPayoutItem::where(
                    'hospitalId',
                    $hospitalId->value()
                );

                foreach ($result as $r) {
                    $payoutItemInstance->orWhere(
                        'payoutHistoryId',
                        $r->getPayoutHId()->value()
                    );
                }
                foreach ($cardIds as $id) {
                    $payoutItemInstance->orWhere('cardId', $id->value());
                }
                $payoutItems = $payoutItemInstance->get();

                $cardUpdates = [];
                foreach ($payoutItems->data->all() as $payoutItem) {
                    if ($payoutItem->cardId !== '') {
                        $cardUpdates[] = [
                            'cardId' => $payoutItem->cardId,
                            'payoutId' => $payoutItem->payoutId,
                            'updateTime' => 'now',
                        ];
                    }
                }
                if (count($cardUpdates) > 0) {
                    Card::upsert('cardId', $cardUpdates);
                }
            }

            $this->presenterProvider
                ->getPayoutRegisterPresenter()
                ->output(new PayoutRegisterOutputData($ids));
        }
    }
}

/***
 * INPUT
 */

namespace JoyPla\Application\InputPorts\Api\Payout {
    use Auth;
    use stdClass;

    /**
     * Class PayoutRegisterInputData
     * @package JoyPla\Application\InputPorts\Payout\Api
     */
    class PayoutRegisterInputData
    {
        public Auth $user;
        public array $payoutItems;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            array $payoutItems,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->payoutItems = array_map(function ($v) {
                $object = new stdClass();
                $object->recordId = $v['recordId'];
                $object->inHospitalItemId = $v['inHospitalItemId'];
                $object->payoutSourceDivisionId = $v['targetDivisionId'];
                $object->payoutTargetDivisionId = $v['sourceDivisionId'];
                $object->payoutQuantity = $v['payoutQuantity'];
                $object->lotNumber = $v['lotNumber'];
                $object->lotDate = $v['lotDate'];
                $object->card = $v['card'];
                return $object;
            }, $payoutItems);

            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface PayoutRegisterInputPortInterface
     * @package JoyPla\Application\InputPorts\Payout\Api
     */
    interface PayoutRegisterInputPortInterface
    {
        /**
         * @param PayoutRegisterInputData $inputData
         */
        public function handle(PayoutRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\Payout {
    /**
     * Class PayoutRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Payout\Api;
     */
    class PayoutRegisterOutputData
    {
        public array $ids;

        public function __construct(array $ids)
        {
            $this->ids = $ids;
        }
    }

    /**
     * Interface PayoutRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Payout\Api;
     */
    interface PayoutRegisterOutputPortInterface
    {
        /**
         * @param PayoutRegisterOutputData $outputData
         */
        public function output(PayoutRegisterOutputData $outputData);
    }
}
