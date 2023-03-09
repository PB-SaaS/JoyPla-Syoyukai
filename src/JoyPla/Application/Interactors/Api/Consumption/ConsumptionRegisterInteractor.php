<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Consumption {
    use App\Model\Division;
    use Exception;
    use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionRegisterInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionRegisterInputData;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\CardId;
    use JoyPla\Enterprise\Models\Consumption;
    use JoyPla\Enterprise\Models\ConsumptionDate;
    use JoyPla\Enterprise\Models\DateYearMonthDay;
    use JoyPla\Enterprise\Models\ConsumptionId;
    use JoyPla\Enterprise\Models\ConsumptionStatus;
    use JoyPla\Enterprise\Models\DivisionId;
    use JoyPla\Enterprise\Models\Hospital;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\HospitalName;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Pref;
    use JoyPla\InterfaceAdapters\GateWays\Repository\CardRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\consumptionRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InventoryCalculationRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ConsumptionRegisterInteractor
     * @package JoyPla\Application\Interactors\Consumption\Api
     */
    class ConsumptionRegisterInteractor implements
        ConsumptionRegisterInputPortInterface
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
         * @param ConsumptionRegisterInputData $inputData
         */
        public function handle(ConsumptionRegisterInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            $inputData->consumptionItems = array_map(function ($v) use (
                $inputData
            ) {
                if (
                    $inputData->isOnlyMyDivision &&
                    $inputData->user->divisionId !== $v->divisionId
                ) {
                    throw new Exception('Illegal request', 403);
                }
                return $v;
            },
            $inputData->consumptionItems);

            $cardIds = [];
            foreach ($inputData->consumptionItems as $i) {
                if ($i->cardId) {
                    $cardIds[] = new CardId($i->cardId);
                }
            }

            $consumptionItems = $this->repositoryProvider
                ->getConsumptionRepository()
                ->findByInHospitalItem(
                    $hospitalId,
                    $inputData->consumptionItems
                );

            $ids = [];
            $result = [];

            foreach ($consumptionItems as $i) {
                $exist = false;
                foreach ($result as $key => $r) {
                    if (
                        $r
                            ->getDivision()
                            ->getDivisionId()
                            ->equal(
                                $i
                                    ->getDivision()
                                    ->getDivisionId()
                                    ->value()
                            )
                    ) {
                        $exist = true;
                        $result[$key] = $r->addConsumptionItem($i);
                    }
                }
                if ($exist) {
                    continue;
                }
                $id = ConsumptionId::generate();
                $ids[] = $id->value();
                //登録時には病院名は必要ないので、いったんhogeでいい
                $result[] = new Consumption(
                    $id,
                    new ConsumptionDate($inputData->consumeDate),
                    [$i],
                    new Hospital(
                        $hospitalId,
                        new HospitalName('hoge'),
                        '',
                        '',
                        new Pref(''),
                        ''
                    ),
                    $i->getDivision(),
                    new ConsumptionStatus(ConsumptionStatus::Consumption)
                );
            }

            $this->repositoryProvider
                ->getConsumptionRepository()
                ->saveToArray($result);

            $cardIds = $this->repositoryProvider
                ->getCardRepository()
                ->get($hospitalId, $cardIds);
            $this->repositoryProvider
                ->getCardRepository()
                ->reset($hospitalId, $cardIds);

            $inventoryCalculations = [];
            foreach ($result as $r) {
                foreach ($r->getConsumptionItems() as $item) {
                    $inventoryCalculations[] = new InventoryCalculation(
                        $item->getHospitalId(),
                        $item->getDivision()->getDivisionId(),
                        $item->getInHospitalItemId(),
                        0,
                        1,
                        $item->getLot(),
                        $item->getConsumptionQuantity() * -1
                    );
                }
            }

            $this->repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);

            $this->presenterProvider
                ->getConsumptionRegisterPresenter()
                ->output(new ConsumptionRegisterOutputData($ids));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Consumption {
    use Auth;
    use stdClass;

    /**
     * Class ConsumptionRegisterInputData
     * @package JoyPla\Application\InputPorts\Consumption\Api
     */
    class ConsumptionRegisterInputData
    {
        public Auth $user;
        public string $consumeDate;
        public array $consumptionItems;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            string $consumeDate,
            array $consumptionItems,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->consumeDate = $consumeDate;
            $this->consumptionItems = array_map(function ($v) use (
                $isOnlyMyDivision,
                $user
            ) {
                $object = new stdClass();
                $object->inHospitalItemId = $v['inHospitalItemId'];
                $object->consumeLotDate = $v['consumeLotDate'];
                $object->consumeLotNumber = $v['consumeLotNumber'];
                $object->consumeQuantity = $v['consumeQuantity'];
                $object->consumeUnitQuantity = $v['consumeUnitQuantity'];
                $object->divisionId = $v['divisionId'];
                $object->cardId = $v['cardId'];
                return $object;
            },
            $consumptionItems);

            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Consumption\Api
     */
    interface ConsumptionRegisterInputPortInterface
    {
        /**
         * @param ConsumptionRegisterInputData $inputData
         */
        function handle(ConsumptionRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Consumption {
    /**
     * Class ConsumptionRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Consumption\Api;
     */
    class ConsumptionRegisterOutputData
    {
        public array $ids;

        public function __construct(array $ids)
        {
            $this->ids = $ids;
        }
    }

    /**
     * Interface ConsumptionRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Consumption\Api;
     */
    interface ConsumptionRegisterOutputPortInterface
    {
        /**
         * @param ConsumptionRegisterOutputData $outputData
         */
        function output(ConsumptionRegisterOutputData $outputData);
    }
}
