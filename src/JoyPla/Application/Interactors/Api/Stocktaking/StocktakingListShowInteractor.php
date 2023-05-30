<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\StocktakingList {
    use ApiResponse;
    use Collection;
    use framework\Exception\NotFoundException;
    use framework\Facades\Gate;
    use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListShowInputPortInterface;
    use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListShowInputData;
    use JoyPla\Enterprise\Models\StocktakingListId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;
    use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

    /**
     * Class StocktakingListShowInteractor
     * @package JoyPla\Application\Interactors\StocktakingList\Api
     */
    class StocktakingListShowInteractor implements StocktakingListShowInputPortInterface
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
         * @param StocktakingListShowInputData $inputData
         */
        public function handle(StocktakingListShowInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $stocktakingListId = new StocktakingListId($inputData->stocktakingListId);

            $stocktakingList = $this->repositoryProvider
                ->getStocktakingListRepository()
                ->findByStocktakingListId($hospitalId, $stocktakingListId);

            if (
                Gate::allows('is_user') &&
                $stocktakingList->getDivisionId()->value() !== $inputData->user->divisionId
            ) {
                throw new NotFoundException('not found', '404');
            }

            //単価の紐づけ。
            //病院管理DBから棚卸単価使用フラグを取得。→これは仮想DBから引っ張れるので検索不要になりました。
            //フラグによって取得する値が異なる。
            ////はいの場合は院内商品情報DBから取得した金額管理IDで紐づいているNJ_商品金額管理DBのレコードの単価フィールドの値
            ////それ以外は院内商品情報DBの価格フィールドの値 / 入数フィールドの値→これは仮想DBから引っ張れるので検索不要になりました。
            //在庫数の紐づけ
            //NJ_在庫管理DBから部署IDと院内商品IDで検索。在庫数フィールドの値を取得して紐づけ処理。
            $stocktakingListArray = $stocktakingList->toArray();
            $items = $stocktakingListArray['items'];
            $priceInstance = ModelRepository::getPriceInstance();
            $stockInstance = ModelRepository::getStockInstance()->where('hospitalId', $inputData->user->hospitalId)->where('divisionId', $stocktakingList->getDivisionId()->value());
            if(!empty($items)){
                $invUnitPrice = $items[0]["invUnitPrice"]; //'1'or'0' 病院管理DBから引っ張ってるから変わらない
                $itemCount = count($items);

                for($i = 0; $i < $itemCount; $i++){
                    if($invUnitPrice == '1'){
                        $priceInstance->orWhere('priceId', $stocktakingListArray['items'][$i]["priceId"]);
                    }else{
                        $tanka = (int)$stocktakingListArray['items'][$i]["price"] / (int)$stocktakingListArray['items'][$i]["quantity"];
                        $stocktakingListArray['items'][$i]['stocktakingUnitPrice'] = $tanka;
                    }
                    $stockInstance->orWhere('inHospitalItemId', $stocktakingListArray['items'][$i]["inHospitalItemId"]);
                }

                //棚卸単価使用フラグがはいの時の紐づけ処理
                if($invUnitPrice == '1'){
                    $prices = $priceInstance->get()->all();
                    for($i = 0; $i < $itemCount; $i++){
                        foreach($prices as $price){
                            //$stocktakingListArray['priceIds'][] = $price->priceId;//デバッグ用
                            //$stocktakingListArray['unitPrices'][] = $price->unitPrice;//デバッグ用
                            if($stocktakingListArray['items'][$i]['priceId'] == $price->priceId){
                                $stocktakingListArray['items'][$i]['stocktakingUnitPrice'] = $price->unitPrice;
                            }
                        }
                    }
                }

                //在庫数紐づけ処理
                $stocks = $stockInstance->get()->all();
                for($i = 0; $i < $itemCount; $i++){
                    foreach($stocks as $stock){
                        $stocktakingListArray['inHospitalItemIds'][] = $stock->inHospitalItemId;//デバッグ用
                        if($stocktakingListArray['items'][$i]['inHospitalItemId'] == $stock->inHospitalItemId){
                            $stocktakingListArray['items'][$i]['stockQuantity'] = $stock->stockQuantity;
                        }
                    }
                }

            }

            echo (new ApiResponse($stocktakingListArray, 1, 200, 'success', [
                'StocktakingListShowPresenter',
            ]))->toJson();
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\StocktakingList {
    use Auth;
    use stdClass;

    /**
     * Class StocktakingListShowInputData
     * @package JoyPla\Application\InputPorts\StocktakingList\Api
     */
    class StocktakingListShowInputData
    {
        public Auth $user;
        public string $stocktakingListId;

        public function __construct(Auth $user, string $stocktakingListId)
        {
            $this->user = $user;
            $this->stocktakingListId = $stocktakingListId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\StocktakingList\Api
     */
    interface StocktakingListShowInputPortInterface
    {
        /**
         * @param StocktakingListShowInputData $inputData
         */
        function handle(StocktakingListShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\StocktakingList {
    use Collection;

    /**
     * Class StocktakingListShowOutputData
     * @package JoyPla\Application\OutputPorts\StocktakingList\Api;
     */
    class StocktakingListShowOutputData
    {
        public array $data;
        public int $count;
        /**
         * StocktakingListShowOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface StocktakingListShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\StocktakingList\Api;
     */
    interface StocktakingListShowOutputPortInterface
    {
        /**
         * @param StocktakingListShowOutputData $outputData
         */
        function output(StocktakingListShowOutputData $outputData);
    }
}
