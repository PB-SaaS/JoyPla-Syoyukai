<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Received {

    use App\Model\Division;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedReturnRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedReturnRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedReturnRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedReturnRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Lot;
    use JoyPla\Enterprise\Models\LotDate;
    use JoyPla\Enterprise\Models\LotNumber;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\OrderItemId;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\Enterprise\Models\Received;
    use JoyPla\Enterprise\Models\ReceivedId;
    use JoyPla\Enterprise\Models\ReceivedItem;
    use JoyPla\Enterprise\Models\ReceivedItemId;
    use JoyPla\Enterprise\Models\ReceivedQuantity;
    use JoyPla\Enterprise\Models\ReceivedStatus;
    use JoyPla\Enterprise\Models\ReturnData;
    use JoyPla\Enterprise\Models\ReturnId;
    use JoyPla\Enterprise\Models\ReturnItem;
    use JoyPla\Enterprise\Models\ReturnItemId;
    use JoyPla\Enterprise\Models\ReturnQuantity;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\HospitalRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InventoryCalculationRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ReceivedRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ReturnRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\stockRepositoryInterface;

    /**
     * Class ReceivedReturnRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\Received
     */
    class ReceivedReturnRegisterInteractor implements ReceivedReturnRegisterInputPortInterface
    {
        /** @var ReceivedReturnRegisterOutputPortInterface */
        private ReceivedReturnRegisterOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;
        
        /** @var ReceivedRepositoryInterface */
        private ReceivedRepositoryInterface $receivedRepository;

        /**
         * ReceivedReturnRegisterInteractor constructor.
         * @param ReceivedReturnRegisterOutputPortInterface $outputPort
         */
        public function __construct(
            ReceivedReturnRegisterOutputPortInterface $outputPort ,
            ReceivedRepositoryInterface $receivedRepository , 
            ReturnRepositoryInterface $returnRepository,
            HospitalRepositoryInterface $hospitalRepository,
            DivisionRepositoryInterface $divisionRepository,
            InventoryCalculationRepositoryInterface $inventoryCalculationRepository)
        { 
            $this->outputPort = $outputPort;
            $this->receivedRepository = $receivedRepository;
            $this->returnRepository = $returnRepository;
            $this->hospitalRepository = $hospitalRepository;
            $this->divisionRepository = $divisionRepository;
            $this->inventoryCalculationRepository = $inventoryCalculationRepository;
        }

        /**
         * @param ReceivedReturnRegisterInputData $inputData
         */
        public function handle(ReceivedReturnRegisterInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            $received = $this->receivedRepository->index($hospitalId , new ReceivedId($inputData->receivedId) );

            if( $received === null ){
                throw new NotFoundException("Not Found.",404);
            }

            if($inputData->isOnlyMyDivision && ! $received->getDivision()->getDivisionId()->equal($inputData->user->divisionId))
            {
                throw new NotFoundException("Not Found.",404);
            }

            $hospital = $this->hospitalRepository->find($hospitalId); 

            $storehouse = $received->getDivision();

            if($hospital->receivingTarget === "1")
            {
                $storehouse = $this->divisionRepository->getStorehouse($hospitalId);
            }

            $items = $received->getReceivedItems();

            $return = new ReturnData(
                $received->getOrderId(),
                $received->getReceivedId(),
                (ReturnId::generate()),
                (new DateYearMonthDayHourMinutesSecond('now')),
                [],
                $received->getHospital(),
                $received->getDivision(),
                $received->getDistributor(),
            );
            $returnItems = [];
            $inventoryCalculations = [];
            foreach($items as $key => $item){
                foreach($inputData->returnItems as $returnItem)
                {
                    if($item->getReceivedItemId()->equal($returnItem->receivedItemId))
                    {
                        $items[$key] = $item->addReturnQuantity( new ReturnQuantity($returnItem->returnQuantity) );
                        $returnItem = new ReturnItem(
                            $return->getReturnId(),
                            $item->getOrderItemId(),
                            $item->getReceivedItemId(),
                            (ReturnItemId::generate()),
                            $item->getInHospitalItemId(),
                            $item->getItem(),
                            $hospitalId,
                            $item->getDivision(),
                            $item->getDistributor(),
                            $item->getQuantity(),
                            $item->getPrice(),
                            (new ReturnQuantity($returnItem->returnQuantity)),
                            $item->getLot(),
                            $item->getItemImage(),
                        );

                        $inventoryCalculations[] = new InventoryCalculation(
                            $returnItem->getHospitalId(),
                            $storehouse->getDivisionId(),
                            $returnItem->getInHospitalItemId(),
                            0,
                            6,
                            $returnItem->getLot(),
                            $returnItem->getReturnQuantity()->value() * $returnItem->getQuantity()->getQuantityNum() * -1,
                        );

                        $returnItems[] = $returnItem;
                    }
                }
            }

            $received = $received->setReceivedItems($items);
            $return = $return->setReturnItems($returnItems);

            $this->returnRepository->saveToArray($hospitalId , [ $return ] );
            $receiveds = $this->receivedRepository->saveToArray($hospitalId , [ $received ] );

            $this->inventoryCalculationRepository->saveToArray($inventoryCalculations);
            $this->outputPort->output(new ReceivedReturnRegisterOutputData($receiveds));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Received {

    use Auth;
    use stdClass;

    /**
     * Class ReceivedReturnRegisterInputData
     * @package JoyPla\Application\InputPorts\Api\Received
     */
    class ReceivedReturnRegisterInputData
    {
        /**
         * ReceivedReturnRegisterInputData constructor.
         */
        public function __construct(Auth $user ,string $receivedId , array $returnItems , bool $isOnlyMyDivision )
        {
            $this->user = $user;
            $this->receivedId = $receivedId;
            $this->returnItems = array_map(function($item){
                $x = new stdClass();
                $x->receivedItemId = $item['receivedItemId'];
                $x->returnQuantity = $item['returnQuantity'];
                return $x;
            },$returnItems);

            $this->isOnlyMyDivision = $isOnlyMyDivision ;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Received
    */
    interface ReceivedReturnRegisterInputPortInterface
    {
        /**
         * @param ReceivedReturnRegisterInputData $inputData
         */
        function handle(ReceivedReturnRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Received {

    use JoyPla\Enterprise\Models\Received;
    use JoyPla\Enterprise\Models\Stock;

    /**
     * Class ReceivedReturnRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\Received;
     */
    class ReceivedReturnRegisterOutputData
    {
        /** @var string */

        /**
         * ReceivedReturnRegisterOutputData constructor.
         */
        public function __construct(array $returns)
        {
            $this->returns = array_map(function($return){
                return $return->toArray();
            },$returns);
        }
    }

    /**
     * Interface ReceivedReturnRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Received;
    */
    interface ReceivedReturnRegisterOutputPortInterface
    {
        /**
         * @param ReceivedReturnRegisterOutputData $outputData
         */
        function output(ReceivedReturnRegisterOutputData $outputData);
    }
} 