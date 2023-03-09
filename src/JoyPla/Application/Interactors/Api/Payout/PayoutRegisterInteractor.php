<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\Payout {
    use App\Model\InHospitalItem as ModelInHospitalItem;
    use Exception;
    use JoyPla\Application\InputPorts\Api\Payout\PayoutRegisterInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Payout\PayoutRegisterInputData;
    use JoyPla\Application\OutputPorts\Api\Payout\PayoutRegisterOutputData;
    use JoyPla\Enterprise\Models\Card;
    use JoyPla\Enterprise\Models\PayoutHId;
    use JoyPla\Enterprise\Models\Payout;
    use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
    use JoyPla\Enterprise\Models\Hospital;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\HospitalName;
    use JoyPla\Enterprise\Models\RequestItemCount;
    use JoyPla\Enterprise\Models\Pref;
    use JoyPla\Enterprise\Models\CardId;
    use JoyPla\Enterprise\Models\Division;
    use JoyPla\Enterprise\Models\InHospitalItem;
    use JoyPla\Enterprise\Models\InHospitalItemId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Lot;
    use JoyPla\Enterprise\Models\LotDate;
    use JoyPla\Enterprise\Models\LotNumber;
    use JoyPla\Enterprise\Models\PayoutItem;
    use JoyPla\Enterprise\Models\PayoutQuantity;
    use JoyPla\Enterprise\Models\UnitPrice;
    use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
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

            $hospitalRow = $this->repositoryProvider
                ->getHospitalRepository()
                ->findRow($hospitalId);

            $hospital = Hospital::create($hospitalRow);

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

            $inHospitalItemIds = array_map(function ($payoutItem) {
                return new InHospitalItemId($payoutItem->inHospitalItemId);
            }, $inputData->payoutItems);

            $inHospitalItems = $this->repositoryProvider
                ->getInHospitalItemRepository()
                ->getByInHospitalItemIds($hospitalId, $inHospitalItemIds);

            $inHospitalItems = array_map(function (
                InHospitalItem $inHospitalItem
            ) {
                return $inHospitalItem;
            },
            $inHospitalItems);

            if (count($inHospitalItems) === 0) {
                throw new Exception("payout items don't exist.", 999);
            }

            $divisions = $this->repositoryProvider
                ->getDivisionRepository()
                ->findByHospitalId($hospitalId);

            $payouts = [];
            foreach ($inputData->payoutItems as $payoutItem) {
                if ((int) $payoutItem->payoutQuantity < 1) {
                    continue;
                }

                $sourceDivisionId = $payoutItem->payoutSourceDivisionId;
                $sourceDivision = array_find($divisions, function (
                    Division $value
                ) use ($sourceDivisionId) {
                    return $value->getDivisionId()->value() ===
                        $sourceDivisionId;
                });

                $targetDivisionId = $payoutItem->payoutTargetDivisionId;
                $targetDivision = array_find($divisions, function (
                    Division $value
                ) use ($targetDivisionId) {
                    return $value->getDivisionId()->value() ===
                        $targetDivisionId;
                });

                if (
                    !!array_find($payouts, function (Payout $value) use (
                        $targetDivision,
                        $sourceDivision
                    ) {
                        return $value->equalDivisions(
                            $sourceDivision,
                            $targetDivision
                        );
                    })
                ) {
                    continue;
                }

                $payouts[] = new Payout(
                    PayoutHId::generate(),
                    new DateYearMonthDayHourMinutesSecond(''),
                    [],
                    $hospital,
                    $sourceDivision,
                    $targetDivision
                );
            }

            $cards = [];

            foreach ($inputData->payoutItems as $payoutItem) {
                if ((int) $payoutItem->payoutQuantity < 1) {
                    continue;
                }
                $sourceDivisionId = $payoutItem->payoutSourceDivisionId;
                $sourceDivision = array_find($divisions, function (
                    Division $value
                ) use ($sourceDivisionId) {
                    return $value->getDivisionId()->value() ===
                        $sourceDivisionId;
                });

                $targetDivisionId = $payoutItem->payoutTargetDivisionId;
                $targetDivision = array_find($divisions, function (
                    Division $value
                ) use ($targetDivisionId) {
                    return $value->getDivisionId()->value() ===
                        $targetDivisionId;
                });

                $inHospitalItemId = $payoutItem->inHospitalItemId;

                $inHospitalItem = array_find($inHospitalItems, function (
                    $value
                ) use ($inHospitalItemId) {
                    return $value->getInHospitalItemId()->value() ===
                        $inHospitalItemId;
                });

                $unitprice = $inHospitalItem->getUnitPrice();

                if ($hospitalRow->payoutUnitPrice !== '1') {
                    if (
                        $inHospitalItem->getQuantity()->getQuantityNum() != 0 &&
                        $inHospitalItem->getPrice()->value() != 0
                    ) {
                        $unitprice =
                            (int) $inHospitalItem->getPrice()->value() /
                            (int) $inHospitalItem
                                ->getQuantity()
                                ->getQuantityNum();
                    } else {
                        $unitprice = 0;
                    }
                }

                foreach ($payouts as &$payout) {
                    if (
                        $payout->equalDivisions(
                            $sourceDivision,
                            $targetDivision
                        )
                    ) {
                        $item = new PayoutItem(
                            $payout->getPayoutHId(),
                            '',
                            $inHospitalItem->getInHospitalItemId(),
                            $inHospitalItem->getItem(),
                            $hospitalId,
                            $sourceDivision,
                            $targetDivision,
                            $inHospitalItem->getQuantity(),
                            $inHospitalItem->getPrice(),
                            new UnitPrice($unitprice),
                            new PayoutQuantity($payoutItem->payoutQuantity),
                            new Lot(
                                new LotNumber($payoutItem->lotNumber),
                                new LotDate($payoutItem->lotDate)
                            ),
                            $inHospitalItem->isLotManagement(),
                            new CardId($payoutItem->card)
                        );
                        $payout = $payout->addPayoutItem($item);
                    }
                }
            }

            $stockViewInstance = ModelRepository::getStockViewInstance()->where(
                'hospitalId',
                $hospitalId->value()
            );

            foreach ($payouts as $payout) {
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

            if ((int) $stocks->count() === 0) {
                throw new Exception("Stocks don't exist.", 998);
            }

            foreach ($payouts as $payout) {
                foreach ($payout->getPayoutItems() as $item) {
                    $stock = array_find($stocks->all(), function ($stock) use (
                        $item
                    ) {
                        return $item
                            ->getSourceDivision()
                            ->getDivisionId()
                            ->value() === $stock->divisionId &&
                            $item->getInHospitalItemId()->value() ===
                                $stock->inHospitalItemId;
                    });

                    if (!$stock) {
                        throw new Exception("Stocks don't exist.", 998);
                    }

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

            $inventoryCalculations = [];
            foreach ($payouts as $payout) {
                foreach ($payout->getPayoutItems() as $item) {
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

            $cardIds = array_map(function ($item) {
                return new CardId($item->card);
            }, $inputData->payoutItems);

            if (!empty($cardIds)) {
                $cards = $this->repositoryProvider
                    ->getCardRepository()
                    ->getCards($hospitalId, $cardIds);

                $updateCards = [];
                foreach ($inputData->payoutItems as $item) {
                    $card = array_find($cards, function ($card) use ($item) {
                        return $card->getCardId()->value() === $item->card;
                    });

                    if (!$card) {
                        throw new Exception("card don't exist.", 998);
                    }

                    $updateCards[] = $card->setLot(
                        new Lot(
                            new LotNumber($item->lotNumber),
                            new LotDate($item->lotDate)
                        )
                    );
                }
            }

            $this->repositoryProvider
                ->getRequestItemCountRepository()
                ->saveToArray($requestItemCounts);

            $this->repositoryProvider
                ->getPayoutRepository()
                ->saveToArray($payouts);

            $this->repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);

            $this->repositoryProvider
                ->getCardRepository()
                ->update($hospitalId, $updateCards);

            $this->presenterProvider->getPayoutRegisterPresenter()->output(
                new PayoutRegisterOutputData(
                    array_map(function (Payout $payout) {
                        return $payout->getPayoutHId()->value();
                    }, $payouts)
                )
            );
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
