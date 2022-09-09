<?php
require_once "framework/Bootstrap/autoload.php";
require_once "JoyPla/require.php";
/** */

/** components */

use App\SpiralDb\HospitalUser;
use App\SpiralDb\Notification;
use framework\Application;
use framework\Http\Request;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Application\Interactors\Web\Consumption\ConsumptionIndexInteractor;
use JoyPla\Application\Interactors\Web\Order\OrderIndexInteractor;
use JoyPla\Application\Interactors\Web\Received\OrderReceivedSlipIndexInteractor;
use JoyPla\Application\Interactors\Web\Received\ReceivedIndexInteractor;
use JoyPla\Application\Interactors\Web\Received\ReceivedLabelInteractor;
use JoyPla\InterfaceAdapters\Controllers\Web\AgreeFormController;
use JoyPla\InterfaceAdapters\Controllers\Web\ConsumptionController;
use JoyPla\InterfaceAdapters\Controllers\Web\NotificationController;
use JoyPla\InterfaceAdapters\Controllers\Web\OptionController;
use JoyPla\InterfaceAdapters\Controllers\Web\OrderController;
use JoyPla\InterfaceAdapters\Controllers\Web\ReceivedController;
use JoyPla\InterfaceAdapters\Controllers\Web\ReturnController;
use JoyPla\InterfaceAdapters\Controllers\Web\StocktakingController;
use JoyPla\InterfaceAdapters\Controllers\Web\TopController;
use JoyPla\InterfaceAdapters\GateWays\Middleware\PersonalInformationConsentMiddleware;
use JoyPla\InterfaceAdapters\GateWays\Middleware\UnorderDataExistMiddleware;
use JoyPla\InterfaceAdapters\GateWays\Repository\ConsumptionRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\HospitalRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ReceivedRepository;
use JoyPla\InterfaceAdapters\Presenters\Web\Consumption\ConsumptionIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Consumption\ConsumptionPrintPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Order\OrderIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Order\OrderPrintPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Order\UnapprovedOrderIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Received\OrderReceivedSlipIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Received\ReceivedIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Received\ReceivedLabelPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Received\ReceivedLabelSettingPresenter;
use JoyPla\JoyPlaApplication;

//param _method="" を指定すると POST PUT DELETE GET PATCH を区別できる

const VIEW_FILE_ROOT = "JoyPla/resources";

Router::map('POST', '/top', function($vars , Request $req){
    Router::redirect('/order/register',$req);
})->service(new Request());

Router::map('POST', '/agree', [AgreeFormController:: class , 'index']);

Router::map('POST', '/agree/send', [AgreeFormController:: class , 'send']);

Router::group(PersonalInformationConsentMiddleware::class, function(){

    Router::map('POST', '/', [TopController:: class , 'index']);

    Router::map('POST', '/order', [TopController:: class , 'orderpage']);

    Router::map('POST', '/consumption', [TopController:: class , 'consumptionpage']);

    Router::map('POST', '/stocktaking', [TopController:: class , 'stocktakingpage']);
    
    Router::map('POST', '/payout', [TopController:: class , 'payoutpage']);

    Router::map('POST', '/stock', [TopController:: class , 'stockpage']);
    
    Router::map('POST', '/card', [TopController:: class , 'cardpage']);

    Router::map('POST', '/trackrecord', [TopController:: class , 'trackrecordpage']);
    
    Router::map('POST', '/monthlyreport', [TopController:: class , 'monthlyreportpage']);

    Router::map('POST', '/estimate', [TopController:: class , 'estimatepage']);

    Router::map('POST', '/lending', [TopController:: class , 'lendingpage']);

    Router::map('POST', '/product', [TopController:: class , 'productpage']);

    Router::map('POST', '/user', [TopController:: class , 'userpage']);

    Router::map('POST', '/option', [OptionController:: class , 'index']);

    Router::map('POST', '/consumption/register', [ConsumptionController::class,'register']);

    Router::map('POST', '/consumption/show', [ConsumptionController::class,'show']);

    Router::map('POST', '/consumption/:consumptionId', [ConsumptionController::class,'index'])->service(new ConsumptionIndexInteractor(new ConsumptionIndexPresenter() , new ConsumptionRepository()) );

    Router::map('POST', '/consumption/:consumptionId/print', [ConsumptionController::class,'print'])->service(new ConsumptionIndexInteractor(new ConsumptionPrintPresenter() , new ConsumptionRepository()) );

    Router::group(UnorderDataExistMiddleware::class , function(){
        Router::map('POST', '/order/fixedQuantityOrder', [OrderController::class,'fixedQuantityOrder']);
    });

    Router::map('POST', '/order/register', [OrderController::class,'register']);

    Router::map('POST', '/order/unapproved/show', [OrderController::class,'unapprovedShow']);

    Router::map('POST', '/order/unapproved/:orderId', [OrderController::class,'unapprovedIndex'])->service(new OrderIndexInteractor(new UnapprovedOrderIndexPresenter() , new OrderRepository() , new DivisionRepository()) );

    Router::map('POST', '/order/show', [OrderController::class,'show']);

    Router::map('POST', '/order/:orderId', [OrderController::class,'index'])->service(new OrderIndexInteractor(new OrderIndexPresenter() , new OrderRepository() ,  new DivisionRepository()) );

    Router::map('POST', '/order/:orderId/print', [OrderController::class,'print'])->service(new OrderIndexInteractor(new OrderPrintPresenter() , new OrderRepository() , new DivisionRepository()) );

    Router::map('POST', '/received/show',[ReceivedController::class,'show']); 

    Router::map('POST', '/received/register', [ReceivedController::class,'register']);

    Router::map('POST', '/received/:receivedId',[ReceivedController::class,'index'])->service(new ReceivedIndexInteractor(new ReceivedIndexPresenter() , new ReceivedRepository()) );

    //TODO
    //Router::map('POST', '/received/:receivedId/labelsetting',[ReceivedController::class,'labelsetting'])->service(new ReceivedIndexInteractor(new ReceivedLabelSettingPresenter() , new ReceivedRepository(), new ReceivedRepository()) );
    //TODO
    //Router::map('POST', '/received/:receivedId/label',[ReceivedController::class,'label'])->service(new ReceivedLabelInteractor(new ReceivedLabelPresenter() , new ReceivedRepository(), new HospitalRepository()) );

    Router::map('POST', '/received/order/list', [ReceivedController::class,'orderList']);

    Router::map('POST', '/received/order/:orderId', [ReceivedController::class,'orderReceivedSlipIndex'])->service(new OrderReceivedSlipIndexInteractor(new OrderReceivedSlipIndexPresenter() , new OrderRepository()) );

    Router::map('POST', '/return/show', [ReturnController::class,'show']);

    Router::map('POST', '/stocktakingimport', [StocktakingController::class,'import']);
    
    Router::map('POST', '/notification', [NotificationController::class,'show']);
    
    Router::map('POST', '/notification/:notificationId', [NotificationController::class,'index']);
    
});
try{ 

    $router = new Router();
    //$router->middleware();毎回必ずチェックする場合はこっち
    $app = new JoyPlaApplication();
    $kernel = new \framework\Http\Kernel($app, $router);
    $request = new Request();
    $request->setUserModel(HospitalUser::class);
    $kernel->handle($request);
} catch(Exception $e) {
    $body = View::forge('html/Common/Error', [ 
        'code' => $e->getCode(), 
        'message' => $e->getMessage()
    ], false)->render();
    echo view('html/Common/Template', compact('body'), false)->render();
}  