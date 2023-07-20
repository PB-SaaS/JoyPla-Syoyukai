<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use JoyPla\Application\InputPorts\Web\Stocktaking\StocktakingList\StocktakingListShowInputData;
use JoyPla\Application\InputPorts\Web\Stocktaking\StocktakingList\StocktakingListShowInputPortInterface;
use JoyPla\Service\Repository\RepositoryProvider;

class StocktakingListController extends Controller
{
    public function index($vars)
    {
        $body = View::forge('html/Stocktaking/StocktakingList/Index', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function show($vars)
    {
        if (Gate::denies('register_of_stocktakingList')) {
            Router::abort(403);
        }
        $stocktakingList = ModelRepository::getStocktakingListTableInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->where('stockListId', $vars['stockListId'])
            ->get()
            ->first();

        if (!$stocktakingList) {
            Router::abort(404);
        }

        if (
            Gate::allows('is_user') &&
            $this->request->user()->divisionId !== $stocktakingList->divisionId
        ) {
            Router::abort(403);
        }

        $body = View::forge(
            'html/Stocktaking/StocktakingList/Show',
            [
                'stockListId' => $vars['stockListId'],
            ],
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function print($vars/* , StocktakingListShowInputPortInterface $inputPort */)
    {
        if (Gate::denies('register_of_stocktakingList')) {
            Router::abort(403);
        }
/* 
        $gate = Gate::getGateInstance('register_of_stocktakingList');

        $inputData = new StocktakingListShowInputData(
            $this->request->user(),
            $vars['stocktakingListId'],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
 */
        $stocktakingList = ModelRepository::getStocktakingListTableViewInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->where('stockListId', $vars['stockListId'])
            ->get()
            ->first();

        if (!$stocktakingList) {
            Router::abort(404);
        }

        if (
            Gate::allows('is_user') &&
            $this->request->user()->divisionId !== $stocktakingList->divisionId
        ) {
            Router::abort(403);
        }

        $stocktakingListRows = ModelRepository::getStocktakingListRowsViewInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->where('stockListId', $vars['stockListId'])
            ->get()
            ->all();

        $itemCount = count($stocktakingListRows);
        if($itemCount > 0){
            $invUnitPrice = $stocktakingListRows[0]->invUnitPrice;
            $priceInstance = ModelRepository::getPriceInstance()
                ->where('hospitalId', $this->request->user()->hospitalId);
            $stockInstance = ModelRepository::getStockInstance()
                ->where('hospitalId', $this->request->user()->hospitalId)
                ->where('divisionId', $stocktakingList->divisionId);

            for($i = 0; $i < $itemCount; $i++){
                if($invUnitPrice == '1'){
                    $priceInstance->orWhere('priceId', $stocktakingListRows[$i]->priceId);
                }else{
                    $tanka = (int)$stocktakingListRows[$i]->price / (int)$stocktakingListRows[$i]->quantity;
                    $stocktakingListRows[$i]->stocktakingUnitPrice = $tanka;
                }
                $stockInstance->orWhere('inHospitalItemId', $stocktakingListRows[$i]->inHospitalItemId);
            }

            //棚卸単価使用フラグがはいの時の紐づけ処理
            if($invUnitPrice == '1'){
                $prices = $priceInstance->get()->all();
                for($i = 0; $i < $itemCount; $i++){
                    foreach($prices as $price){
                        if($stocktakingListRows[$i]->priceId == $price->priceId){
                            $stocktakingListRows[$i]->stocktakingUnitPrice = $price->unitPrice;
                        }
                    }
                }
            }

            //在庫数紐づけ処理
            $stocks = $stockInstance->get()->all();
            for($i = 0; $i < $itemCount; $i++){
                foreach($stocks as $stock){
                    if($stocktakingListRows[$i]->inHospitalItemId == $stock->inHospitalItemId){
                        $stocktakingListRows[$i]->stockQuantity = $stock->stockQuantity;
                    }
                }
            }
            
        }


        $body = View::forge(
            'printLayout/Stocktaking/StocktakingList',
            [
                'stocktakingListId' => $vars['stockListId'],
                'stocktakingList' => $stocktakingList,
                'stocktakingListRows' => $stocktakingListRows,
                'stocks' => $stocks,
            ],
            false
        )->render();
        echo view('printLayout/Common/Template', compact('body'), false)->render();
    }
}
