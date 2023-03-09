<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Received {
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterByOrderSlipInputData;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterByOrderSlipInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterByOrderSlipOutputData;
    use JoyPla\Enterprise\Models\Card;
    use JoyPla\Enterprise\Models\CardId;
    use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Lot;
    use JoyPla\Enterprise\Models\LotDate;
    use JoyPla\Enterprise\Models\LotNumber;
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
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ReceivedRegisterByOrderSlipInteractor
     * @package JoyPla\Application\Interactors\Api\Received
     */
    class ReceivedRegisterByOrderSlipInteractor implements
        ReceivedRegisterByOrderSlipInputPortInterface
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
         * @param ReceivedRegisterByOrderSlipInputData $inputData
         */
        public function handle(ReceivedRegisterByOrderSlipInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $orderId = new OrderId($inputData->orderId);
            $orderstatus = array_values(
                array_filter(OrderStatus::list(), function ($var) {
                    return OrderStatus::UnOrdered !== $var;
                })
            ); //Unordered以外を取得
            $order = $this->repositoryProvider
                ->getOrderRepository()
                ->index($hospitalId, $orderId, $orderstatus);

            if ($order === null) {
                throw new Exception('Invalid value.', 422);
            }

            if (
                $inputData->isOnlyMyDivision &&
                !$order
                    ->getDivision()
                    ->getDivisionId()
                    ->equal($inputData->user->divisionId)
            ) {
                throw new NotFoundException('Not Found.', 404);
            }

            $items = $order->getOrderItems();

            $received = new Received(
                $order->getOrderId(),
                ReceivedId::generate(),
                new DateYearMonthDayHourMinutesSecond('now'),
                [],
                $order->getHospital(),
                $order->getDivision(),
                $order->getDistributor(),
                new ReceivedStatus(ReceivedStatus::Received)
            );

            $inventoryCalculations = [];
            $receivedItems = [];

            $cardIds = [];
            foreach ($inputData->receivedItems as $receivedItem) {
                foreach ($receivedItem['receiveds'] as $receivedItem) {
                    foreach ($receivedItem['cards'] as $card) {
                        $cardIds[] = new CardId($card['cardId']);
                    }
                }
            }

            $cards = $this->repositoryProvider
                ->getCardRepository()
                ->getCards($hospitalId, $cardIds);

            $updateCards = [];
            foreach ($items as $key => $item) {
                $fkey = array_search(
                    $item->getOrderItemId()->value(),
                    array_column($inputData->receivedItems, 'orderItemId'),
                    true
                );
                if ($fkey === false) {
                    throw new Exception(
                        'The item with this OrderItemId does not exist.',
                        422
                    );
                }

                if (!isset($inputData->receivedItems[$fkey]['receiveds'])) {
                    continue;
                }

                foreach (
                    $inputData->receivedItems[$fkey]['receiveds']
                    as $receivedItem
                ) {
                    $receivedCards = [];
                    foreach ($receivedItem['cards'] as $c) {
                        $cardId = $c['cardId'];
                        $receivedCards[] = array_find($cards, function (
                            Card $card
                        ) use ($cardId) {
                            return $card->getCardId()->value() === $cardId;
                        });
                    }

                    if (
                        count($receivedCards) !== count($receivedItem['cards'])
                    ) {
                        throw new Exception(
                            'Card information did not exist.',
                            422
                        );
                    }

                    $receivedQuantity = new ReceivedQuantity(
                        (int) $receivedItem['receivedQuantity']
                    );
                    $items[$key] = $items[$key]->addReceivedQuantity(
                        $receivedQuantity
                    ); // 入庫数を更新
                    $receivedItem = new ReceivedItem(
                        $item->getOrderItemId(),
                        $received->getReceivedId(),
                        ReceivedItemId::generate(),
                        $item->getInHospitalItemId(),
                        $item->getItem(),
                        $order->getHospital()->getHospitalId(),
                        $order->getDivision(),
                        $order->getDistributor(),
                        $item->getQuantity(),
                        $item->getPrice(),
                        0,
                        $receivedQuantity,
                        new ReturnQuantity(0),
                        new Lot(
                            new LotNumber($receivedItem['lotNumber']),
                            new LotDate($receivedItem['lotDate'])
                        ),
                        new Redemption(false, new Price(0)),
                        $item->getItemImage()
                    );

                    if ($receivedItem->checkCards($receivedCards)) {
                        throw new Exception(
                            'The number of cards tied together exceeds the number of.',
                            422
                        );
                    }

                    foreach ($receivedCards as $receivedCard) {
                        $updateCards[] = $receivedCard->setLot(
                            $receivedItem->getLot()
                        );
                    }

                    if ($order->getReceivedTarget() === 1) {
                        //大倉庫

                        $storehouse = $this->repositoryProvider
                            ->getDivisionRepository()
                            ->getStorehouse($hospitalId);
                            
                        $inventoryCalculations[] = new InventoryCalculation(
                            $receivedItem->getHospitalId(),
                            $storehouse->getDivisionId(),
                            $receivedItem->getInHospitalItemId(),
                            $receivedItem->getReceivedQuantity()->value() *
                                $receivedItem->getQuantity()->getQuantityNum() *
                                -1,
                            3,
                            $receivedItem->getLot(),
                            $receivedItem->getReceivedQuantity()->value() *
                                $receivedItem->getQuantity()->getQuantityNum()
                        );
                        /*
                        $inventoryCalculations[] = new InventoryCalculation(
                            $receivedItem->getHospitalId(),
                            $receivedItem->getDivision()->getDivisionId(),
                            $receivedItem->getInHospitalItemId(),
                            $receivedItem->getReceivedQuantity()->value() * $receivedItem->getQuantity()->getQuantityNum() * -1,
                            3,
                            $receivedItem->getLot(),
                            0,
                        );
                        */
                    } else {
                        $inventoryCalculations[] = new InventoryCalculation(
                            $receivedItem->getHospitalId(),
                            $receivedItem->getDivision()->getDivisionId(),
                            $receivedItem->getInHospitalItemId(),
                            $receivedItem->getReceivedQuantity()->value() *
                                $receivedItem->getQuantity()->getQuantityNum() *
                                -1,
                            3,
                            $receivedItem->getLot(),
                            $receivedItem->getReceivedQuantity()->value() *
                                $receivedItem->getQuantity()->getQuantityNum()
                        );
                    }

                    $receivedItems[] = $receivedItem;
                }
            }

            $order = $order->setOrderItems($items); // オーダーデータを更新
            $order = $order->updateOrderStatus();
            $received = $received->setReceivedItems($receivedItems);

            $this->repositoryProvider
                ->getOrderRepository()
                ->saveToArray($hospitalId, [$order], ['isReceived' => true]);
            $this->repositoryProvider
                ->getReceivedRepository()
                ->saveToArray($hospitalId, [$received]);

            $this->repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);

            $this->repositoryProvider
                ->getCardRepository()
                ->update($hospitalId, $updateCards);

            $this->presenterProvider
                ->getReceivedRegisterByOrderSlipPresenter()
                ->output(new ReceivedRegisterByOrderSlipOutputData($received));
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

        public Auth $user;
        public string $orderId;
        public array $receivedItems;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            $orderId,
            array $receivedItems,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->orderId = $orderId;
            $this->receivedItems = array_map(function ($item) {
                $d['orderItemId'] = $item['orderItemId'];
                $d['receiveds'] = [];
                if (isset($item['receiveds'])) {
                    $d['receiveds'] = array_map(function ($item) {
                        return [
                            'receivedQuantity' => $item['receivedQuantity'],
                            'lotNumber' => $item['lotNumber'],
                            'lotDate' => $item['lotDate'],
                            'cards' => isset($item['cards'])
                                ? array_map(function ($card) {
                                    return [
                                        'cardId' => $card['cardId'],
                                        'cardQuantity' => $card['cardQuantity'],
                                    ];
                                }, $item['cards'])
                                : [],
                        ];
                    }, $item['receiveds']);
                }
                return $d;
            }, $receivedItems);
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
        public array $received;

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
