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

//param _method="" を指定すると GET PUT DELETE GET PATCH を区別できる

const VIEW_FILE_ROOT = "JoyPla/resources";

Router::map('GET', '/agree', [AgreeFormController:: class , 'index']);

Router::map('POST', '/agree/send', [AgreeFormController:: class , 'send']);

Router::group(PersonalInformationConsentMiddleware::class, function(){

    Router::map('GET', '/', [TopController:: class , 'index']);

    Router::map('GET', '/order', [TopController:: class , 'orderpage']);

    Router::map('GET', '/consumption', [TopController:: class , 'consumptionpage']);

    Router::map('GET', '/stocktaking', [TopController:: class , 'stocktakingpage']);
    
    Router::map('GET', '/payout', [TopController:: class , 'payoutpage']);

    Router::map('GET', '/stock', [TopController:: class , 'stockpage']);
    
    Router::map('GET', '/card', [TopController:: class , 'cardpage']);

    Router::map('GET', '/trackrecord', [TopController:: class , 'trackrecordpage']);
    
    Router::map('GET', '/monthlyreport', [TopController:: class , 'monthlyreportpage']);

    Router::map('GET', '/estimate', [TopController:: class , 'estimatepage']);

    Router::map('GET', '/lending', [TopController:: class , 'lendingpage']);

    Router::map('GET', '/product', [TopController:: class , 'productpage']);

    Router::map('GET', '/user', [TopController:: class , 'userpage']);

    Router::map('GET', '/option', [OptionController:: class , 'index']);

    Router::map('GET', '/consumption/register', [ConsumptionController::class,'register']);

    Router::map('GET', '/consumption/show', [ConsumptionController::class,'show']);

    Router::map('GET', '/consumption/:consumptionId', [ConsumptionController::class,'index'])->service(new ConsumptionIndexInteractor(new ConsumptionIndexPresenter() , new ConsumptionRepository()) );

    Router::map('GET', '/consumption/:consumptionId/print', [ConsumptionController::class,'print'])->service(new ConsumptionIndexInteractor(new ConsumptionPrintPresenter() , new ConsumptionRepository()) );

    Router::group(UnorderDataExistMiddleware::class , function(){
        Router::map('GET', '/order/fixedQuantityOrder', [OrderController::class,'fixedQuantityOrder']);
    });

    Router::map('GET', '/order/register', [OrderController::class,'register']);

    Router::map('GET', '/order/unapproved/show', [OrderController::class,'unapprovedShow']);

    Router::map('GET', '/order/unapproved/:orderId', [OrderController::class,'unapprovedIndex'])->service(new OrderIndexInteractor(new UnapprovedOrderIndexPresenter() , new OrderRepository() , new DivisionRepository()) );

    Router::map('GET', '/order/show', [OrderController::class,'show']);

    Router::map('GET', '/order/:orderId', [OrderController::class,'index'])->service(new OrderIndexInteractor(new OrderIndexPresenter() , new OrderRepository() ,  new DivisionRepository()) );

    Router::map('GET', '/order/:orderId/print', [OrderController::class,'print'])->service(new OrderIndexInteractor(new OrderPrintPresenter() , new OrderRepository() , new DivisionRepository()) );

    Router::map('GET', '/received/show',[ReceivedController::class,'show']); 

    Router::map('GET', '/received/register', [ReceivedController::class,'register']);

    Router::map('GET', '/received/:receivedId',[ReceivedController::class,'index'])->service(new ReceivedIndexInteractor(new ReceivedIndexPresenter() , new ReceivedRepository()) );

    //TODO
    //Router::map('GET', '/received/:receivedId/labelsetting',[ReceivedController::class,'labelsetting'])->service(new ReceivedIndexInteractor(new ReceivedLabelSettingPresenter() , new ReceivedRepository(), new ReceivedRepository()) );
    //TODO
    //Router::map('GET', '/received/:receivedId/label',[ReceivedController::class,'label'])->service(new ReceivedLabelInteractor(new ReceivedLabelPresenter() , new ReceivedRepository(), new HospitalRepository()) );

    Router::map('GET', '/received/order/list', [ReceivedController::class,'orderList']);

    Router::map('GET', '/received/order/:orderId', [ReceivedController::class,'orderReceivedSlipIndex'])->service(new OrderReceivedSlipIndexInteractor(new OrderReceivedSlipIndexPresenter() , new OrderRepository()) );

    Router::map('GET', '/return/show', [ReturnController::class,'show']);

    Router::map('GET', '/stocktakingimport', [StocktakingController::class,'import']);
    
    Router::map('GET', '/notification', [NotificationController::class,'show']);
    
    Router::map('GET', '/notification/:notificationId', [NotificationController::class,'index']);
    
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