<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Received {
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedLateRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedLateRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedLateRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedLateRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\Accountant;
    use JoyPla\Enterprise\Models\AccountantService;
    use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
    use JoyPla\Enterprise\Models\DateYearMonthDay;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Lot;
    use JoyPla\Enterprise\Models\LotDate;
    use JoyPla\Enterprise\Models\LotNumber;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\OrderItem;
    use JoyPla\Enterprise\Models\OrderItemId;
    use JoyPla\Enterprise\Models\OrderQuantity;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\Enterprise\Models\OrderDate;
    use JoyPla\Enterprise\Models\OrderAdjustment;
    use JoyPla\Enterprise\Models\Price;
    use JoyPla\Enterprise\Models\Received;
    use JoyPla\Enterprise\Models\ReceivedId;
    use JoyPla\Enterprise\Models\ReceivedItem;
    use JoyPla\Enterprise\Models\ReceivedItemId;
    use JoyPla\Enterprise\Models\ReceivedQuantity;
    use JoyPla\Enterprise\Models\ReceivedStatus;
    use JoyPla\Enterprise\Models\Redemption;
    use JoyPla\Enterprise\Models\ReturnQuantity;
    use JoyPla\Enterprise\Models\Distributor;
    use JoyPla\Enterprise\Models\DistributorId;
    use JoyPla\Enterprise\Models\Division;
    use JoyPla\Enterprise\Models\DivisionId;
    use JoyPla\Enterprise\Models\InHospitalItemId;
    use JoyPla\Enterprise\Models\TextArea512Bytes;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InventoryCalculationRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ReceivedRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\stockRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ReceivedLateRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\Received
     */
    class ReceivedLateRegisterInteractor implements
        ReceivedLateRegisterInputPortInterface
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
         * @param ReceivedLateRegisterInputData $inputData
         */
        public function handle(ReceivedLateRegisterInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $accountantDate = new DateYearMonthDay(
                $inputData->accountantDate ?? 'now'
            );
            $receivedItems = $inputData->receivedItems;
            $divisionId = $inputData->divisionId;
            $distributors = $this->repositoryProvider->getDistributorRepository()->findByHospitalId($hospitalId);
            $division = $this->repositoryProvider->getDivisionRepository()->find($hospitalId, new DivisionId($divisionId));
            $hospital = $this->repositoryProvider->getHospitalRepository()->find($hospitalId);
            $inhospitalItems = $this->repositoryProvider->getInHospitalItemRepository()->findByHospitalId($hospitalId); //Itemクラス取得用

/* 
            //Itemクラス取得用
            //院内商品IDで院内商品を検索
            foreach($receivedItems as $receivedItem){
                $inHpItemIds[] = $receivedItem['inHospitalItemId'];
            }
            $inhospitalItems = $this->repositoryProvider->getInHospitalItemRepository()->getByInHospitalItemIds($hospitalId, $inHpItemIds);
 */

            foreach($distributors as $dist){
                $distributor[$dist->distributorId] = new Distributor($hospitalId, new DistributorId($dist->distributorId), $dist->distributorName, $dist->orderMethod);
            }
            foreach($inhospitalItems as $inhpItem){
                $key = $inhpItem->getInHospitalItemId()->value();
                $itemByInHospitalId[$key] = $inhpItem->getItem();
                $priceByInHospitalId[$key] = $inhpItem->getPrice();
                $quantityByInHospitalId[$key] = $inhpItem->getQuantity();
                $distributorIdsByInHospitalId[$key] = $inhpItem->getDistributorId()->value();
                $isLotManagementByInHospitalId[$key] = $inhpItem->isLotManagement();
            }

            $receivedList = [];
            $orderList = [];
            $inventoryCalculations = [];
            $receivedIds = [];
            $receivedRows = [];
            $orderRows = [];
            foreach($receivedItems as $receivedItem){
                //院内商品ID単位で必要なデータを作成
                //卸業者単位で発注履歴DB用と入庫履歴DB用のデータを作成
                //入庫商品単位で発注DB用と入庫DB用のデータを作成
                
                //事前準備
                $inHpItemId = $receivedItem['inHospitalItemId']; //院内商品ID対応

/* 
                //卸業者単位のデータが作成されていない場合は卸業者単位で入庫履歴ID作成。
                if(!$receivedIds[$distributorIdsByInHospitalId[$inHpItemId]]){
                    $receivedIds[$distributorIdsByInHospitalId[$inHpItemId]] = ReceivedId::generate();
                }
 */

                //卸業者単位のデータが作成されていない場合は卸業者単位で発注履歴と入庫履歴データ作成。
                if(!$receivedList[$distributorIdsByInHospitalId[$inHpItemId]]){
                    $receivedIds[$distributorIdsByInHospitalId[$inHpItemId]] = ReceivedId::generate();
                    $orderId = "late_" . OrderId::generate()->value(); //あとから入荷と分かるように発注番号を作る
                    $orderList[$distributorIdsByInHospitalId[$inHpItemId]] = new Order( //発注履歴
                        new OrderId($orderId),
                        new DateYearMonthDayHourMinutesSecond($inputData->orderDate ?? 'now'), //registDate
                        new DateYearMonthDayHourMinutesSecond($inputData->receivedDate ?? 'now'), //orderDate
                        [], //orderItems
                        $hospital, //hospital
                        $division, //division
                        $distributor[$distributorIdsByInHospitalId[$inHpItemId]], //distributor
                        new OrderStatus(OrderStatus::ReceivingIsComplete), //orderstatus=6（入庫完了扱い）
                        new OrderAdjustment(OrderAdjustment::IndividualOrder), //orderadjustment=2(個別発注扱い)
                        new TextArea512Bytes('あとから入荷'), //orderComment
                        new TextArea512Bytes(''), //distributorComment
                        $inputData->user->name, //orderUserName
                        1,//receivedTarget
                        $distributor[$distributorIdsByInHospitalId[$inHpItemId]]->getOrderMethod() == '1' || 
                            $distributor[$distributorIdsByInHospitalId[$inHpItemId]]->getOrderMethod() == '2' //sentFlag
                    );
                    $receivedList[$distributorIdsByInHospitalId[$inHpItemId]] = new Received( //入庫履歴
                        new OrderId($orderId), //$receivedItem->getOrderId(), //orderId
                        $receivedIds[$distributorIdsByInHospitalId[$inHpItemId]], //receievedId
                        new DateYearMonthDayHourMinutesSecond( //receivedDate
                            $inputData->receivedDate ?? 'now'
                        ),
                        [], //receivedItem
                        $hospital, //hospital
                        $division, //$receivedItem->getDivision(),
                        $distributor[$distributorIdsByInHospitalId[$inHpItemId]],//$receivedItem->getDistributor(),
                        new ReceivedStatus(ReceivedStatus::Received),
                    );
                }

                //入力ごとの発注DBと入庫DB用のデータを作成
                foreach($receivedItem['receiveds'] as $received){
                    if($received['receivedQuantity'] < 0){
                        continue; //発注が0未満の場合は通さない
                    }
                    $orderItemId = "late_" . OrderItemId::generate()->value();
                    $orderRow = new OrderItem(
                        new OrderId($orderId), //$receivedItem->getOrderId(), //orderId
                        new OrderItemId($orderItemId), //orderItemId
                        new InHospitalItemId($inHpItemId), //inHospitalItemId
                        $itemByInHospitalId[$inHpItemId], //item
                        $hospitalId, //hospitalId
                        $division, //division
                        $distributor[$distributorIdsByInHospitalId[$inHpItemId]], //distributor
                        $quantityByInHospitalId[$inHpItemId], //quantity
                        $priceByInHospitalId[$inHpItemId], //price
                        new OrderQuantity($received['receivedQuantity']), //orderQuantity
                        new ReceivedQuantity($received['receivedQuantity']), //receivedQuantity
                        new DateYearMonthDay(''), //dueDate
                        '', //distributorManagerCode
                        $isLotManagementByInHospitalId[$inHpItemId], //lotManagement
                        '', //itemImage
                        ($received['useMedicode']) ? 1 : 0, //useMedicode
                        1 //medicodeStatus
                    );
                    $receivedRow = new ReceivedItem(
                        new OrderItemId($orderItemId), //$item->getOrderItemId(), //orderItemId
                        $receivedIds[$distributorIdsByInHospitalId[$inHpItemId]], //receivedId
                        ReceivedItemId::generate(), //receivedItemId
                        new InHospitalItemId($inHpItemId), //inhospitalId
                        $itemByInHospitalId[$inHpItemId], //$item->getItem(), //item
                        $hospitalId, //hospitalId
                        $division, //division
                        $distributor[$distributorIdsByInHospitalId[$inHpItemId]], //distributor
                        $quantityByInHospitalId[$inHpItemId], //quantity
                        $priceByInHospitalId[$inHpItemId], //$receivedItem['price'], //price
                        0, //adjustmentAmount
                        new ReceivedQuantity($received['receivedQuantity']), //receivedQuantity
                        new ReturnQuantity(0), //returnQuantity
                        new Lot( //lot
                            new LotNumber($received['lotNumber']),
                            new LotDate($received['lotDate'])
                        ),
                        new Redemption(false, new Price(0)), //redemption
                        '', //$item->getItemImage() //itemimage
                    );
                    $receivedRows[$distributorIdsByInHospitalId[$inHpItemId]][] = $receivedRow; //卸業者単位の商品列
                    $orderRows[$distributorIdsByInHospitalId[$inHpItemId]][] = $orderRow; //卸業者単位の商品列

                    //NJ_在庫管理TRDBに登録するデータの作成
                    $inventoryCalculations[] = new InventoryCalculation(
                        $hospitalId,
                        $division->getDivisionId(),
                        new InHospitalItemId($inHpItemId),
                        0, //発注した個数=0 なぜなら発注作業がないから！
                        3, //入荷
                        $receivedRow->getLot(),
                        $receivedRow
                            ->getReceivedQuantity()
                            ->value() *
                            $receivedRow
                                ->getQuantity()
                                ->getQuantityNum() //入荷した個数（入数）=入荷した個数(receivedUnitQuantity)*商品に設定されている入数(quantity)
                    );

                }
/* 
                if($receivedList[$distributorIdsByInHospitalId[$inHpItemId]]){
                    //卸業者単位の入庫履歴DB用のデータ更新（入荷の入力ごとの追加）
                    foreach($receivedRows as $row){
                        $receivedList[$distributorIdsByInHospitalId[$inHpItemId]]->addReceivedItem($row);
                    }
                }else{
                    //卸業者単位の入庫履歴DB用のデータ作成
                    $orderId = "late_" . OrderId::generate()->value(); //あとから入荷と分かるように発注番号を作る
                    $receivedList[$distributorIdsByInHospitalId[$inHpItemId]] = new Received(
                        new OrderId($orderId), //$receivedItem->getOrderId(), //orderId
                        $receivedIds[$distributorIdsByInHospitalId[$inHpItemId]], //receievedId
                        new DateYearMonthDayHourMinutesSecond( //receivedDate
                            $inputData->receivedDate ?? 'now'
                        ),
                        $receivedRows, //[], //receivedItem
                        $hospital, //hospital
                        $division, //$receivedItem->getDivision(),
                        $distributor[$distributorIdsByInHospitalId[$inHpItemId]],//$receivedItem->getDistributor(),
                        new ReceivedStatus(ReceivedStatus::Received)
                    );
                }
 */
            }

            foreach($receivedRows as $distributorIdAsKey => $row){
                $receivedList[$distributorIdAsKey] = $receivedList[$distributorIdAsKey]->setReceivedItems($row);
            }
            foreach($orderRows as $distributorIdAsKey => $row){
                $orderList[$distributorIdAsKey] = $orderList[$distributorIdAsKey]->setOrderItems($row);
            }

            $accountants = [];
            $accountantLogs = [];
            foreach ($receivedList as $receivedSlip) {
                $accountant = AccountantService::LateReceivedToAccountant(
                    $receivedSlip,
                    $accountantDate
                );
                $oldaccountant = clone $accountant;
                $oldaccountant->setItems([]);

                $accountantLogs = array_merge(
                    $accountantLogs,
                    AccountantService::checkAccountant(
                        $accountant,
                        $oldaccountant,
                        $inputData->user->id
                    )
                );

                $accountants[] = $accountant;
            }
            $this->repositoryProvider
                ->getAccountantRepository()
                ->saveToArray($accountants);

            $this->repositoryProvider
                ->getAccountantRepository()
                ->saveItemLog($accountantLogs);

            $this->repositoryProvider
                ->getOrderRepository()
                ->saveToArray($hospitalId, $orderList);

            $this->repositoryProvider
                ->getReceivedRepository()
                ->saveToArray($hospitalId, $receivedList);

            $this->repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);

            $this->presenterProvider
                ->getReceivedLateRegisterPresenter()  //もしかしたらgetReceivedRegisterPresenterでいける？
                ->output(new ReceivedLateRegisterOutputData($receivedList)); //もしかしたらReceivedRegisterOutputDataにすればいけるかも？
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
     * Class ReceivedLateRegisterInputData
     * @package JoyPla\Application\InputPorts\Api\Received
     */
    class ReceivedLateRegisterInputData
    {
        public Auth $user;
        public array $receivedItems;
        public bool $isOnlyMyDivision;
        public string $receivedDate;
        public string $accountantDate;
        public string $divisionId;
        public string $orderDate;

        public function __construct(
            Auth $user,
            array $receivedItems,
            bool $isOnlyMyDivision,
            string $receivedDate,
            string $accountantDate,
            string $divisionId,
            string $orderDate
        ) {
            $this->user = $user;
            $this->receivedItems = array_map(function ($item) {
                $d['orderItemId'] = $item['orderItemId'] ?? '' ; //空文字にするかもしれない。
                $d['inHospitalItemId'] = $item['inHospitalItemId']; //院内商品ID（追加）
                $d['receiveds'] = array_map(function ($item) {
                    $s['receivedQuantity'] = $item['receivedUnitQuantity'];
                    $s['lotNumber'] = $item['lotNumber']
                        ? $item['lotNumber']
                        : '';
                    $s['lotDate'] = $item['lotDate'] ? $item['lotDate'] : '';
                    return $s;
                }, $item['receiveds']);
                return $d;
            }, $receivedItems);
            $this->isOnlyMyDivision = $isOnlyMyDivision;
            $this->receivedDate = $receivedDate
                ? $receivedDate . ' 00:00:00'
                : 'now';
            $this->accountantDate = $accountantDate ? $accountantDate : 'now';
            $this->divisionId = $divisionId;
            $this->orderDate = $orderDate
                ? $orderDate . ' 00:00:00'
                : 'now';
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Received
     */
    interface ReceivedLateRegisterInputPortInterface
    {
        /**
         * @param ReceivedLateRegisterInputData $inputData
         */
        function handle(ReceivedLateRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Received {
    use JoyPla\Enterprise\Models\Received;
    use JoyPla\Enterprise\Models\Stock;

    /**
     * Class ReceivedLateRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\Received;
     */
    class ReceivedLateRegisterOutputData //もしかしたらReceivedRegisterOutputDataでもいけるかも？
    {
        public array $receiveds;

        public function __construct(array $receiveds)
        {
            $this->receiveds = $receiveds;
        }
    }

    /**
     * Interface ReceivedLateRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Received;
     */
    interface ReceivedLateRegisterOutputPortInterface //もしかしたらReceivedRegisterOutputPortInterfaceでもいけるかも？
    {
        /**
         * @param ReceivedLateRegisterOutputData $outputData //もしかしたらReceivedRegisterOutputDataでもいけるかも？
         */
        function output(ReceivedLateRegisterOutputData $outputData); //もしかしたらReceivedRegisterOutputDataでもいけるかも？
    }
}
