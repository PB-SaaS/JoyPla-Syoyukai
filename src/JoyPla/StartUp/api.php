<?php
require_once "framework/Bootstrap/autoload.php";
require_once "JoyPla/require.php";
/** */

/** components */

use App\Http\Middleware\VerifyCsrfTokenMiddleware;
use App\SpiralDb\HospitalUser;
use framework\Application;
use framework\Http\Request;
use framework\Routing\Router;
use JoyPla\Application\Interactors\Api\Barcode\BarcodeOrderSearchInteractor;
use JoyPla\Application\Interactors\Api\Barcode\BarcodeSearchInteractor;
use JoyPla\Application\Interactors\Api\Consumption\ConsumptionDeleteInteractor;
use JoyPla\Application\Interactors\Api\Consumption\ConsumptionRegisterInteractor;
use JoyPla\Application\Interactors\Api\Consumption\ConsumptionShowInteractor;
use JoyPla\Application\Interactors\Api\Distributor\DistributorShowInteractor;
use JoyPla\Application\Interactors\Api\Division\DivisionShowInteractor;
use JoyPla\Application\Interactors\Api\InHospitalItem\InHospitalItemShowInteractor;
use JoyPla\Application\Interactors\Api\Notification\NotificationShowInteractor;
use JoyPla\Application\Interactors\Api\ReceivedReturn\ReturnShowInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderRegisterInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderShowInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnapprovedApprovalInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnapprovedDeleteInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnapprovedItemDeleteInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnapprovedUpdateInteractor;
use JoyPla\Application\Interactors\Api\Order\FixedQuantityOrderInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderRevisedInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnReceivedShowInteractor;
use JoyPla\Application\Interactors\Api\Received\ReceivedRegisterByOrderSlipInteractor;
use JoyPla\Application\Interactors\Api\Received\ReceivedRegisterInteractor;
use JoyPla\Application\Interactors\Api\Received\ReceivedReturnRegisterInteractor;
use JoyPla\Application\Interactors\Api\Received\ReceivedShowInteractor;
use JoyPla\InterfaceAdapters\Controllers\Api\BarcodeController;
use JoyPla\InterfaceAdapters\Controllers\Api\ConsumptionController;
use JoyPla\InterfaceAdapters\Controllers\Api\DistributorController;
use JoyPla\InterfaceAdapters\Controllers\Api\DivisionController;
use JoyPla\InterfaceAdapters\Controllers\Api\InHospitalItemController;
use JoyPla\InterfaceAdapters\Controllers\Api\NotificationController;
use JoyPla\InterfaceAdapters\Controllers\Api\OrderController;
use JoyPla\InterfaceAdapters\Controllers\Api\ReceivedController;
use JoyPla\InterfaceAdapters\Controllers\Api\ReturnController;
use JoyPla\InterfaceAdapters\Controllers\Api\StocktakingController;
use JoyPla\InterfaceAdapters\GateWays\Repository\BarcodeRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\CardRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ConsumptionRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\DistributorRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\HospitalRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\InventoryCalculationRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\NotificationRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ReceivedRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ReturnRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\StockRepository;
use JoyPla\InterfaceAdapters\Presenters\Api\Barcode\BarcodeOrderSearchPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Barcode\BarcodeSearchPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Consumption\ConsumptionDeletePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Consumption\ConsumptionRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Consumption\ConsumptionShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Distributor\DistributorShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Division\DivisionShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\InHospitalItem\InHospitalItemShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Notification\NotificationShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\ReceivedReturn\ReturnShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnapprovedApprovalPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnapprovedDeletePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnapprovedItemDeletePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnapprovedShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnapprovedUpdatePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\FixedQuantityOrderPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderRevisedPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnReceivedShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Received\ReceivedRegisterByOrderSlipPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Received\ReceivedRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Received\ReceivedReturnRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Received\ReceivedShowPresenter;
use JoyPla\JoyPlaApplication;

//param _method="" を指定すると GET PUT DELETE GET PATCH を区別できる
const VIEW_FILE_ROOT = "JoyPla/resources";

//Router::map('Get','/api/order/maintenance',[OrderController::class,'maintenance']);

Router::group(VerifyCsrfTokenMiddleware::class, function(){
    Router::map('GET','/api/division/show',[DivisionController::class , 'show'])->service(new DivisionShowInteractor(new DivisionShowPresenter() , new DivisionRepository()) );

    Router::map('GET','/api/distributor/show',[DistributorController::class , 'show'])->service(new DistributorShowInteractor(new DistributorShowPresenter() , new DistributorRepository()) );

    Router::map('GET','/api/inHospitalItem/show',[InHospitalItemController::class,'show'])->service(new InHospitalItemShowInteractor(new InHospitalItemShowPresenter() , new InHospitalItemRepository()) );

    Router::map('POST','/api/consumption/register',[ConsumptionController::class,'register'])->service(new ConsumptionRegisterInteractor(new ConsumptionRegisterPresenter() , new ConsumptionRepository() , new InventoryCalculationRepository() , new CardRepository()) );

    Router::map('GET','/api/consumption/show',[ConsumptionController::class,'show'])->service(new ConsumptionShowInteractor(new ConsumptionShowPresenter() , new ConsumptionRepository()) );

    Router::map('DELETE','/api/consumption/:consumptionId/delete',[ConsumptionController::class,'delete'])->service(new ConsumptionDeleteInteractor(new ConsumptionDeletePresenter() , new ConsumptionRepository(), new InventoryCalculationRepository()));

    Router::map('POST','/api/order/register',[OrderController::class,'register'])->service(new OrderRegisterInteractor(new OrderRegisterPresenter() , new OrderRepository(), new HospitalRepository()) );

    Router::map('POST','/api/fixedQuantityOrder/register',[OrderController::class,'fixedQuantityOrderRegister'])->service(new OrderRegisterInteractor(new OrderRegisterPresenter() , new OrderRepository() , new HospitalRepository()) );

    Router::map('GET','/api/order/unapproved/show',[OrderController::class,'unapprovedShow'])->service(new OrderShowInteractor(new OrderUnapprovedShowPresenter() , new OrderRepository()) );

    Router::map('PATCH','/api/order/unapproved/:orderId/update',[OrderController::class,'unapprovedUpdate'])->service(new OrderUnapprovedUpdateInteractor(new OrderUnapprovedUpdatePresenter() , new OrderRepository()) );
    
    Router::map('GET', '/api/order/fixedQuantityOrder', [OrderController::class,'fixedQuantityOrder'])->service(new FixedQuantityOrderInteractor(new FixedQuantityOrderPresenter() , new StockRepository()) );

    Router::map('GET','/api/order/unreceivedShow',[OrderController::class,'unreceivedShow'])->service(new OrderUnReceivedShowInteractor(new OrderUnReceivedShowPresenter() , new OrderRepository()) );

    Router::map('DELETE','/api/order/unapproved/:orderId/delete',[OrderController::class,'unapprovedDelete'])->service(new OrderUnapprovedDeleteInteractor(new OrderUnapprovedDeletePresenter() , new OrderRepository()) );

    Router::map('PATCH','/api/order/unapproved/:orderId/approval',[OrderController::class,'approval'])->service(new OrderUnapprovedApprovalInteractor(new OrderUnapprovedApprovalPresenter() , new OrderRepository() , new DivisionRepository() , new InventoryCalculationRepository()) );

    Router::map('DELETE','/api/order/unapproved/:orderId/:orderItemId/delete',[OrderController::class,'unapprovedItemDelete'])->service(new OrderUnapprovedItemDeleteInteractor(new OrderUnapprovedItemDeletePresenter() , new OrderRepository()) );

    Router::map('GET','/api/order/show',[OrderController::class,'show'])->service(new OrderShowInteractor(new OrderShowPresenter() , new OrderRepository()) );

    Router::map('PATCH','/api/order/:orderId/revised',[OrderController::class,'revised'])->service(new OrderRevisedInteractor(new OrderRevisedPresenter() , new OrderRepository() , new InventoryCalculationRepository()) );

    Router::map('GET','/api/received/order/list',[ReceivedController::class,'orderList'])->service(new OrderShowInteractor(new OrderShowPresenter() , new OrderRepository()) );

    Router::map('POST','/api/:orderId/received/register',[ReceivedController::class,'orderRegister'])->service(new ReceivedRegisterByOrderSlipInteractor(new ReceivedRegisterByOrderSlipPresenter() , new OrderRepository() , new ReceivedRepository() , new DivisionRepository() , new InventoryCalculationRepository()) );

    Router::map('POST','/api/:receivedId/return/register',[ReceivedController::class,'returnRegister'])->service(new ReceivedReturnRegisterInteractor( new ReceivedReturnRegisterPresenter() , new ReceivedRepository() , new ReturnRepository() , new HospitalRepository() , new DivisionRepository , new InventoryCalculationRepository() ));

    Router::map('GET','/api/received/show',[ReceivedController::class,'show'])->service(new ReceivedShowInteractor(new ReceivedShowPresenter() , new ReceivedRepository()) );

    Router::map('POST','/api/received/register',[ReceivedController::class,'register'])->service(new ReceivedRegisterInteractor(new ReceivedRegisterPresenter() , new OrderRepository() , new ReceivedRepository() , new DivisionRepository() , new InventoryCalculationRepository()) );

    Router::map('GET','/api/return/show',[ReturnController::class,'show'])->service(new ReturnShowInteractor(new ReturnShowPresenter() , new ReturnRepository()) );

    Router::map('GET','/api/barcode/search',[BarcodeController::class,'search'])->service(new BarcodeSearchInteractor( new BarcodeSearchPresenter() , new BarcodeRepository() ));

    Router::map('GET','/api/barcode/order/search',[BarcodeController::class,'orderSearch'])->service(new BarcodeOrderSearchInteractor( new BarcodeOrderSearchPresenter() , new BarcodeRepository() ));

    Router::map('GET','/api/stocktaking/inHospitalItem',[StocktakingController::class,'inHospitalItem']);
    
    Router::map('GET','/api/notification/show',[NotificationController::class,'show'])->service(new NotificationShowInteractor( new NotificationShowPresenter() , new NotificationRepository() ));
    
});

try{ 
    $router = new Router();
    $app = new JoyPlaApplication();
    $kernel = new \framework\Http\Kernel($app, $router);
    $request = new Request();
    $auth = new Auth('NJ_HUserDB',[
        "registrationTime",
        "updateTime",
        "authKey",
        "hospitalId",
        "divisionId",
        "userPermission",
        "loginId",
        "loginPassword",
        "name",
        "nameKana",
        "mailAddress",
        "remarks",
        "termsAgreement",
        "tenantId",
        "agreementDate",
        "hospitalAuthKey",
        "userCheck" 
    ]);

    $request->setUser($auth);
    $kernel->handle($request);
} catch(Exception $e) { 
    echo (new ApiResponse( [], 0 , $e->getCode(), $e->getMessage() , [ "path" , $request->getRequestUri() ]))->toJson();
}