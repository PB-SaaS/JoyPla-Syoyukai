<?php
require_once "framework/Bootstrap/autoload.php";
require_once "JoyPla/require.php";
/** */

/** components */

use framework\Application;
use framework\Http\Request;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Application\Interactors\Api\Barcode\BarcodeSearchInteractor;
use JoyPla\Application\Interactors\Web\Consumption\ConsumptionIndexInteractor;
use JoyPla\Application\Interactors\Web\Order\OrderIndexInteractor;
use JoyPla\Application\Interactors\Web\Received\OrderReceivedSlipIndexInteractor;
use JoyPla\InterfaceAdapters\Controllers\Api\BarcodeController;
use JoyPla\InterfaceAdapters\Controllers\Web\ConsumptionController;
use JoyPla\InterfaceAdapters\Controllers\Web\OrderController;
use JoyPla\InterfaceAdapters\Controllers\Web\ReceivedController;
use JoyPla\InterfaceAdapters\Controllers\Web\TopController;
use JoyPla\InterfaceAdapters\GateWays\Repository\ConsumptionRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepository;
use JoyPla\InterfaceAdapters\Presenters\Api\Barcode\BarcodeSearchPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Consumption\ConsumptionIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Consumption\ConsumptionPrintPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Order\OrderIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Order\UnapprovedOrderIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Received\OrderReceivedSlipIndexPresenter;

//SPIRALはすべてPOSTになる

const VIEW_FILE_ROOT = "JoyPla/resources";

Router::map('POST', '/top', function(Request $req){
    Router::redirect('/order/regist',$req);
})->service(new Request());

Router::map('POST', '/', [TopController:: class , 'index']);

Router::map('POST', '/order', [TopController:: class , 'orderpage']);

Router::map('POST', '/consumption', [TopController:: class , 'consumptionpage']);

Router::map('POST', '/consumption/regist', [ConsumptionController::class,'register']);

Router::map('POST', '/consumption/show', [ConsumptionController::class,'show']);

Router::map('POST', '/consumption/:consumptionId', [ConsumptionController::class,'index'])->service(new ConsumptionIndexInteractor(new ConsumptionIndexPresenter() , new ConsumptionRepository()) );

Router::map('POST', '/consumption/:consumptionId/print', [ConsumptionController::class,'print'])->service(new ConsumptionIndexInteractor(new ConsumptionPrintPresenter() , new ConsumptionRepository()) );

Router::map('POST', '/order/fixedQuantityOrder', [OrderController::class,'fixedQuantityOrder']);

Router::map('POST', '/order/regist', [OrderController::class,'register']);

Router::map('POST', '/order/unapproved/show', [OrderController::class,'unapprovedShow']);

Router::map('POST', '/order/unapproved/:orderId', [OrderController::class,'unapprovedIndex'])->service(new OrderIndexInteractor(new UnapprovedOrderIndexPresenter() , new OrderRepository()) );

Router::map('POST', '/order/show', [OrderController::class,'show']);

Router::map('POST', '/order/:orderId', [OrderController::class,'index'])->service(new OrderIndexInteractor(new OrderIndexPresenter() , new OrderRepository()) );

Router::map('POST', '/received/order/list', [ReceivedController::class,'orderList']);

Router::map('POST', '/received/order/:orderId', [ReceivedController::class,'orderReceivedSlipIndex'])->service(new OrderReceivedSlipIndexInteractor(new OrderReceivedSlipIndexPresenter() , new OrderRepository()) );


try{
    $router = new Router();
    $app = new Application();
    $kernel = new \framework\Http\Kernel($app, $router);
    $request = new Request();
    $kernel->handle($request);
} catch(Exception $e) {
    $body = View::forge('html/Common/Error', [ 
        'code' => $e->getCode(),
        'message' => $e->getMessage()
    ], false)->render();
    echo view('html/Common/Template', compact('body'), false)->render();
}  