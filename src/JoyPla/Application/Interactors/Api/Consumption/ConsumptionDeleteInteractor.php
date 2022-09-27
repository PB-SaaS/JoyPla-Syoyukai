<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Consumption {

    use App\Model\Division;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionDeleteInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionDeleteInputData;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionDeleteOutputPortInterface;
    use JoyPla\Enterprise\Models\Consumption;
    use JoyPla\Enterprise\Models\DateYearMonthDay;
    use JoyPla\Enterprise\Models\ConsumptionId;
    use JoyPla\Enterprise\Models\ConsumptionStatus;
    use JoyPla\Enterprise\Models\DivisionId;
    use JoyPla\Enterprise\Models\Hospital;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\HospitalName;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Pref;
    use JoyPla\InterfaceAdapters\GateWays\Repository\consumptionRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InventoryCalculationRepositoryInterface;

    /**
     * Class ConsumptionDeleteInteractor
     * @package JoyPla\Application\Interactors\Consumption\Api
     */
    class ConsumptionDeleteInteractor implements ConsumptionDeleteInputPortInterface
    {
        /** @var ConsumptionDeleteOutputPortInterface */
        private ConsumptionDeleteOutputPortInterface $outputPort;

        /** @var ConsumptionRepositoryInterface */
        private ConsumptionRepositoryInterface $consumptionRepository;

        /**
         * ConsumptionDeleteInteractor constructor.
         * @param ConsumptionDeleteOutputPortInterface $outputPort
         */
        public function __construct(ConsumptionDeleteOutputPortInterface $outputPort , ConsumptionRepositoryInterface $consumptionRepository , InventoryCalculationRepositoryInterface $inventoryCalculationRepository)
        {
            $this->outputPort = $outputPort;
            $this->consumptionRepository = $consumptionRepository;
            $this->inventoryCalculationRepository = $inventoryCalculationRepository;
        }

        /**
         * @param ConsumptionDeleteInputData $inputData
         */
        public function handle(ConsumptionDeleteInputData $inputData)
        {

            $hospitalId = new HospitalId($inputData->user->hospitalId);

            $consumption = $this->consumptionRepository->index($hospitalId , new ConsumptionId($inputData->consumptionId));

            if(empty($consumption)){
                throw new NotFoundException('not found.',404);
            }

            if($inputData->isOnlyMyDivision && ! $consumption->getDivision()->getDivisionId()->equal($inputData->user->divisionId))
            {
                throw new Exception('Illegal request',403);
            }

            $inventoryCalculations = [];
            foreach($consumption->getConsumptionItems() as $item)
            {
                $inventoryCalculations[] = new InventoryCalculation(
                    $item->getHospitalId(),
                    $item->getDivision()->getDivisionId(),
                    $item->getInHospitalItemId(),
                    0,
                    1,
                    $item->getLot(),
                    $item->getConsumptionQuantity(), //消費の取り消しなので増やす
                );
            }

            $this->consumptionRepository->delete($hospitalId , $consumption->getConsumptionId());

            $this->inventoryCalculationRepository->saveToArray($inventoryCalculations);

            $this->outputPort->output(new ConsumptionDeleteOutputData());
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
     * Class ConsumptionDeleteInputData
     * @package JoyPla\Application\InputPorts\Consumption\Api
     */
    class ConsumptionDeleteInputData
    {
        /**
         * ConsumptionDeleteInputData constructor.
         */
        public function __construct(Auth $user , string $consumptionId, bool $isOnlyMyDivision)
        {
            $this->user = $user;
            $this->consumptionId = $consumptionId;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Consumption\Api
    */
    interface ConsumptionDeleteInputPortInterface
    {
        /**
         * @param ConsumptionDeleteInputData $inputData
         */
        function handle(ConsumptionDeleteInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Consumption {

    /**
     * Class ConsumptionDeleteOutputData
     * @package JoyPla\Application\OutputPorts\Consumption\Api;
     */
    class ConsumptionDeleteOutputData
    {
        /** @var string */

        /**
         * ConsumptionDeleteOutputData constructor.
         */
        public function __construct()
        {
        }
    }

    /**
     * Interface ConsumptionDeleteOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Consumption\Api;
    */
    interface ConsumptionDeleteOutputPortInterface
    {
        /**
         * @param ConsumptionDeleteOutputData $outputData
         */
        function output(ConsumptionDeleteOutputData $outputData);
    }
} 