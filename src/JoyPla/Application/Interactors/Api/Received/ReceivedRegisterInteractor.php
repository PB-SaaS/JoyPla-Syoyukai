<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Received {

    use App\Model\Division;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterOutputPortInterface;
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
    use JoyPla\Enterprise\Models\Price;
    use JoyPla\Enterprise\Models\Received;
    use JoyPla\Enterprise\Models\ReceivedId;
    use JoyPla\Enterprise\Models\ReceivedItem;
    use JoyPla\Enterprise\Models\ReceivedItemId;
    use JoyPla\Enterprise\Models\ReceivedQuantity;
    use JoyPla\Enterprise\Models\ReceivedStatus;
    use JoyPla\Enterprise\Models\Redemption;
    use JoyPla\Enterprise\Models\ReturnQuantity;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InventoryCalculationRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ReceivedRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\stockRepositoryInterface;

    /**
     * Class ReceivedRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\Received
     */
    class ReceivedRegisterInteractor implements ReceivedRegisterInputPortInterface
    {
        /** @var ReceivedRegisterOutputPortInterface */
        private ReceivedRegisterOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;
        
        /** @var ReceivedRepositoryInterface */
        private ReceivedRepositoryInterface $receivedRepository;

        /**
         * ReceivedRegisterInteractor constructor.
         * @param ReceivedRegisterOutputPortInterface $outputPort
         */
        public function __construct(
            ReceivedRegisterOutputPortInterface $outputPort , 
            OrderRepositoryInterface $orderRepository ,
            ReceivedRepositoryInterface $receivedRepository,
            DivisionRepositoryInterface $divisionRepository,
            InventoryCalculationRepositoryInterface $inventoryCalculationRepository)
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
            $this->receivedRepository = $receivedRepository;
            $this->divisionRepository = $divisionRepository;
            $this->inventoryCalculationRepository = $inventoryCalculationRepository;
        }

        /**
         * @param ReceivedRegisterInputData $inputData
         */
        public function handle(ReceivedRegisterInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $orders = $this->orderRepository->getOrderByOrderItemId($hospitalId , array_column($inputData->receivedItems, 'orderItemId'));
            $storehouse = $this->divisionRepository->getStorehouse($hospitalId);
            
            if($inputData->isOnlyMyDivision )
            {
                foreach($orders as $order)
                {
                    if(! $order->getDivision()->getDivisionId()->equal($inputData->user->divisionId))
                    {
                        throw new NotFoundException("Not Found.",404);
                    }
                }
            }

            $receiveds = [];
            $inventoryCalculations = [];
            foreach($orders as $orderKey => $order)
            {
                $received = new Received(
                    $order->getOrderId(),
                    (ReceivedId::generate()),
                    (new DateYearMonthDayHourMinutesSecond('now')),
                    [],
                    $order->getHospital(),
                    $order->getDivision(),
                    $order->getDistributor(),
                    new ReceivedStatus(ReceivedStatus::Received)
                );
                $items = $order->getOrderItems();
                foreach($inputData->receivedItems as $requestReceived)
                {
                    foreach($items as $key => &$item){
                        if($item->getOrderItemId()->equal($requestReceived['orderItemId']))
                        {
                            foreach($requestReceived['receiveds'] as $receivedItem)
                            {
                                $receivedQuantity = new ReceivedQuantity((int)$receivedItem['receivedQuantity']);
                                $item = $item->addReceivedQuantity($receivedQuantity);// 入庫数を更新
                                $receivedItem = new ReceivedItem(
                                    $item->getOrderItemId(),
                                    $received->getReceivedId(),
                                    (ReceivedItemId::generate()),
                                    $item->getInHospitalItemId(),
                                    $item->getItem(),
                                    $order->getHospital()->getHospitalId(),
                                    $order->getDivision(),
                                    $order->getDistributor(),
                                    $item->getQantity(),
                                    $item->getPrice(),
                                    0,
                                    $receivedQuantity,
                                    (new ReturnQuantity(0)),
                                    (new Lot(
                                        new LotNumber($receivedItem['lotNumber']),
                                        new LotDate($receivedItem['lotDate']),
                                    )),
                                    (new Redemption(
                                        false,
                                        new Price(0)
                                    )),
                                    $item->getItemImage()
                                );
                                
                                if($order->getReceivedTarget() === 1) //大倉庫
                                {

                                    $inventoryCalculations[] = new InventoryCalculation(
                                        $receivedItem->getHospitalId(),
                                        $storehouse->getDivisionId(),
                                        $receivedItem->getInHospitalItemId(),
                                        0,
                                        3,
                                        $receivedItem->getLot(),
                                        $receivedItem->getReceivedQuantity()->value() * $receivedItem->getQuantity()->getQuantityNum(),
                                    );
                                    $inventoryCalculations[] = new InventoryCalculation(
                                        $receivedItem->getHospitalId(),
                                        $receivedItem->getDivision()->getDivisionId(),
                                        $receivedItem->getInHospitalItemId(),
                                        $receivedItem->getReceivedQuantity()->value() * $receivedItem->getQuantity()->getQuantityNum() * -1,
                                        3,
                                        $receivedItem->getLot(),
                                        0,
                                    );
                                } else 
                                {
                                    $inventoryCalculations[] = new InventoryCalculation(
                                        $receivedItem->getHospitalId(),
                                        $receivedItem->getDivision()->getDivisionId(),
                                        $receivedItem->getInHospitalItemId(),
                                        $receivedItem->getReceivedQuantity()->value() * $receivedItem->getQuantity()->getQuantityNum() * -1,
                                        3,
                                        $receivedItem->getLot(),
                                        $receivedItem->getReceivedQuantity()->value() * $receivedItem->getQuantity()->getQuantityNum(),
                                    );
                                }

                                $receivedItems[] = $receivedItem;
                            }
                        }
                    }
                }
                $order = $order->setOrderItems($items);// オーダーデータを更新
                $orders[$orderKey] = $order->updateOrderStatus();
                $receiveds[] = $received->setReceivedItems($receivedItems);
            }

            $this->orderRepository->saveToArray($hospitalId , $orders , [ 'isReceived' => true ]);
            $this->receivedRepository->saveToArray($hospitalId , $receiveds);
            $this->outputPort->output(new ReceivedRegisterOutputData($receiveds));

            $this->inventoryCalculationRepository->saveToArray($inventoryCalculations);
            
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
     * Class ReceivedRegisterInputData
     * @package JoyPla\Application\InputPorts\Api\Received
     */
    class ReceivedRegisterInputData
    {
        /**
         * ReceivedRegisterInputData constructor.
         */
        public function __construct(Auth $user ,array $receivedItems , bool $isOnlyMyDivision)
        {
            $this->user = $user;
            $this->receivedItems = array_map(function($item){
                $d['orderItemId'] = $item['orderItemId'];
                $d['receiveds'] = array_map(function($item){
                    $s['receivedQuantity'] = $item['receivedUnitQuantity'];
                    $s['lotNumber'] = ($item['lotNumber'])? $item['lotNumber'] : "";
                    $s['lotDate'] = ($item['lotDate'])? $item['lotDate'] : "";
                    return $s;
                }, $item['receiveds']);
                return $d;
            },$receivedItems);
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Received
    */
    interface ReceivedRegisterInputPortInterface
    {
        /**
         * @param ReceivedRegisterInputData $inputData
         */
        function handle(ReceivedRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Received {

    use JoyPla\Enterprise\Models\Received;
    use JoyPla\Enterprise\Models\Stock;

    /**
     * Class ReceivedRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\Received;
     */
    class ReceivedRegisterOutputData
    {
        /** @var string */

        /**
         * ReceivedRegisterOutputData constructor.
         */
        public function __construct(array $receiveds)
        {
            $this->receiveds = $receiveds;
        }
    }

    /**
     * Interface ReceivedRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Received;
    */
    interface ReceivedRegisterOutputPortInterface
    {
        /**
         * @param ReceivedRegisterOutputData $outputData
         */
        function output(ReceivedRegisterOutputData $outputData);
    }
} 