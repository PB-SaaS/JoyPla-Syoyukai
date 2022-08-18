<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Received {

    use App\Model\Division;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterByOrderSlipInputData;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterByOrderSlipInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterByOrderSlipOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterByOrderSlipOutputPortInterface;
    use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Lot;
    use JoyPla\Enterprise\Models\LotDate;
    use JoyPla\Enterprise\Models\LotNumber;
    use JoyPla\Enterprise\Models\Order;
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
     * Class ReceivedRegisterByOrderSlipInteractor
     * @package JoyPla\Application\Interactors\Api\Received
     */
    class ReceivedRegisterByOrderSlipInteractor implements ReceivedRegisterByOrderSlipInputPortInterface
    {
        /** @var ReceivedRegisterByOrderSlipOutputPortInterface */
        private ReceivedRegisterByOrderSlipOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;
        
        /** @var ReceivedRepositoryInterface */
        private ReceivedRepositoryInterface $receivedRepository;

        /**
         * ReceivedRegisterByOrderSlipInteractor constructor.
         * @param ReceivedRegisterByOrderSlipOutputPortInterface $outputPort
         */
        public function __construct(ReceivedRegisterByOrderSlipOutputPortInterface $outputPort , 
            OrderRepositoryInterface $orderRepository ,
            ReceivedRepositoryInterface $receivedRepository,
            DivisionRepositoryInterface $divisionRepository,
            InventoryCalculationRepositoryInterface $inventoryCalculationRepository
         )
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
            $this->receivedRepository = $receivedRepository;
            $this->divisionRepository = $divisionRepository;
            $this->inventoryCalculationRepository = $inventoryCalculationRepository;
        }

        /**
         * @param ReceivedRegisterByOrderSlipInputData $inputData
         */
        public function handle(ReceivedRegisterByOrderSlipInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $orderId = new OrderId($inputData->orderId);
            $orderstatus = array_values(array_filter(OrderStatus::list(),function($var){ return (OrderStatus::UnOrdered !== $var); }));//Unordered以外を取得
            $order = $this->orderRepository->index($hospitalId ,$orderId ,$orderstatus);

            if( $order === null )
            {
                throw new Exception("Invalid value.",422);
            }

            if($inputData->isOnlyMyDivision && ! $order->getDivision()->getDivisionId()->equal($inputData->user->divisionId))
            {
                throw new NotFoundException("Not Found.",404);
            }

            $items = $order->getOrderItems();

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
            
            $storehouse = $this->divisionRepository->getStorehouse($hospitalId);
            $inventoryCalculations = [];
            $receivedItems = [];

            foreach($items as $key => $item)
            {
                $fkey = array_search( $item->getOrderItemId()->value() ,array_column($inputData->receivedItems, 'orderItemId') , true);
                if($fkey === false)
                {
                    throw new Exception('The item with this OrderItemId does not exist.',422);
                }

                foreach($inputData->receivedItems[$fkey]['receiveds'] as $receivedItem)
                {
                    $receivedQuantity = new ReceivedQuantity((int)$receivedItem['receivedQuantity']);
                    $items[$key] = $item->addReceivedQuantity($receivedQuantity);// 入庫数を更新
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
                        (new Redemption(false,new Price(0) )),
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

            $order = $order->setOrderItems($items);// オーダーデータを更新
            $order = $order->updateOrderStatus();
            $received = $received->setReceivedItems($receivedItems);
            $this->orderRepository->saveToArray($hospitalId , [$order] , [ 'isReceived' => true ]);
            $this->receivedRepository->saveToArray($hospitalId , [$received]);

            $this->inventoryCalculationRepository->saveToArray($inventoryCalculations);

            $this->outputPort->output(new ReceivedRegisterByOrderSlipOutputData($received));
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
     * Class ReceivedRegisterByOrderSlipInputData
     * @package JoyPla\Application\InputPorts\Api\Received
     */
    class ReceivedRegisterByOrderSlipInputData
    {
        /**
         * ReceivedRegisterByOrderSlipInputData constructor.
         */
        public function __construct(Auth $user , $orderId ,array $receivedItems , bool $isOnlyMyDivision )
        {
            $this->user = $user;
            $this->orderId = $orderId;
            $this->receivedItems = array_map(function($item){
                $d['orderItemId'] = $item['orderItemId'];
                $d['receiveds'] = array_map(function($item){
                    $s['receivedQuantity'] = $item['receivedQuantity'];
                    $s['lotNumber'] = $item['lotNumber'];
                    $s['lotDate'] = $item['lotDate'];
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
    interface ReceivedRegisterByOrderSlipInputPortInterface
    {
        /**
         * @param ReceivedRegisterByOrderSlipInputData $inputData
         */
        function handle(ReceivedRegisterByOrderSlipInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Received {

    use JoyPla\Enterprise\Models\Received;
    use JoyPla\Enterprise\Models\Stock;

    /**
     * Class ReceivedRegisterByOrderSlipOutputData
     * @package JoyPla\Application\OutputPorts\Api\Received;
     */
    class ReceivedRegisterByOrderSlipOutputData
    {
        /** @var string */

        /**
         * ReceivedRegisterByOrderSlipOutputData constructor.
         */
        public function __construct(Received $received)
        {
            $this->received = $received->toArray();
        }
    }

    /**
     * Interface ReceivedRegisterByOrderSlipOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Received;
    */
    interface ReceivedRegisterByOrderSlipOutputPortInterface
    {
        /**
         * @param ReceivedRegisterByOrderSlipOutputData $outputData
         */
        function output(ReceivedRegisterByOrderSlipOutputData $outputData);
    }
} 