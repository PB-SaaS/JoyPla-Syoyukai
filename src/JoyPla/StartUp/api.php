<?php

require_once 'framework/Bootstrap/autoload.php';
require_once 'JoyPla/require.php';
/** */

/** components */

use App\Http\Middleware\VerifyCsrfTokenMiddleware;
use framework\Http\Request;
use framework\Routing\Router;
use framework\SpiralConnecter\SpiralDB;
use JoyPla\InterfaceAdapters\Controllers\Api\AcceptanceController;
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
use JoyPla\InterfaceAdapters\Controllers\Api\ReferenceController;
use JoyPla\InterfaceAdapters\Controllers\Api\ItemRequestController;
use JoyPla\InterfaceAdapters\Controllers\Api\PayoutController;
use JoyPla\InterfaceAdapters\Controllers\Api\AccountantController;
use JoyPla\InterfaceAdapters\Controllers\Api\AccountantLogController;
use JoyPla\InterfaceAdapters\Controllers\Api\ItemListController; //商品一覧表用
use JoyPla\InterfaceAdapters\Controllers\Api\ProductController;
use JoyPla\InterfaceAdapters\Controllers\Api\StockController;
use JoyPla\InterfaceAdapters\Controllers\Api\StocktakingListController; //棚卸商品管理表用
use JoyPla\JoyPlaApplication;
use JoyPla\Service\Presenter\Api\PresenterProvider;
use JoyPla\Service\Repository\QueryProvider;
use JoyPla\Service\Repository\RepositoryProvider;
use JoyPla\Service\UseCase\Api\UseCaseProvider;
use Test\Exceptions\ApiExceptionHandler;

//param _method="" を指定すると GET PUT DELETE GET PATCH を区別できる
const VIEW_FILE_ROOT = 'JoyPla/resources';
$repositoryProvider = new RepositoryProvider();
$queryProvider = new QueryProvider();
$presenterProvider = new PresenterProvider();
$useCaseProvider = new UseCaseProvider(
    $repositoryProvider,
    $queryProvider,
    $presenterProvider
);
$request = new Request();
//Router::map('Get','/api/order/maintenance',[OrderController::class,'maintenance']);

Router::group(VerifyCsrfTokenMiddleware::class, function () use (
    $useCaseProvider,$request
) {
    Router::map('GET', '/api/user/affiliation', function($vars) {
        $auth =  new Auth('NJ_HUserDB', [
            'id',
            'registrationTime',
            'updateTime',
            'authKey',
            'hospitalId',
            'divisionId',
            'userPermission',
            'loginId',
            'loginPassword',
            'name',
            'nameKana',
            'mailAddress',
            'remarks',
            'termsAgreement',
            'tenantId',
            'agreementDate',
            'hospitalAuthKey',
            'userCheck',
            'affiliationHId'
        ]);
        $data = SpiralDB::title('HAffiliationView')->value(['loginId','hospitalName','affiliationHId'])->where('loginId' , $auth->loginId)->get();
        $res =[['value'=> '', 'label' => '--- 選択してください ---']];
        foreach($data as $d){
            $res[] = ['value'=> $d->affiliationHId , 'label' => $d->hospitalName];
        }
        echo (new ApiResponse($res , 1 , 200 , 'success', []))->toJson();
    });
    Router::map('POST', '/api/user/change/affiliation', function($vars) use ($request){
        $auth =  new Auth('NJ_HUserDB', [
            'id',
            'registrationTime',
            'updateTime',
            'authKey',
            'hospitalId',
            'divisionId',
            'userPermission',
            'loginId',
            'loginPassword',
            'name',
            'nameKana',
            'mailAddress',
            'remarks',
            'termsAgreement',
            'tenantId',
            'agreementDate',
            'hospitalAuthKey',
            'userCheck',
            'affiliationHId'
        ]);

        $data = SpiralDB::title('NJ_HUserDB')->where('loginId' , $auth->loginId)->update([
            'affiliationHId' => $request->affiliationId   
        ]);
        echo (new ApiResponse([], $data  , 200 , 'success', []))->toJson();
    });

    Router::map('GET', '/api/division/index', [
        DivisionController::class,
        'index',
    ])->service($useCaseProvider->getDivisionIndexInteractor());

    Router::map('GET', '/api/distributor/index', [
        DistributorController::class,
        'index',
    ])->service($useCaseProvider->getDistributorIndexInteractor());

    Router::map('GET', '/api/inHospitalItem/index', [
        InHospitalItemController::class,
        'index',
    ])->service($useCaseProvider->getInHospitalItemIndexInteractor());

    Router::map('GET', '/api/inHospitalItem/show', [
        InHospitalItemController::class,
        'show',
    ])->service($useCaseProvider->getInHospitalItemShowInteractor());

    Router::map('POST', '/api/consumption/register', [
        ConsumptionController::class,
        'register',
    ])->service($useCaseProvider->getConsumptionRegisterInteractor());

    Router::map('GET', '/api/consumption/index', [
        ConsumptionController::class,
        'index',
    ])->service($useCaseProvider->getConsumptionIndexInteractor());

    Router::map('PATCH', '/api/consumption/:consumptionId', [
        ConsumptionController::class,
        'update',
    ]);

    Router::map('DELETE', '/api/consumption/:consumptionId/delete', [
        ConsumptionController::class,
        'delete',
    ])->service($useCaseProvider->getConsumptionDeleteInteractor());
    
    Router::map('DELETE', '/api/consumption/:consumptionId/item/delete', [
        ConsumptionController::class,
        'deleteItem',
    ]);
    Router::map('POST', '/api/order/register', [
        OrderController::class,
        'register',
    ])->service($useCaseProvider->getOrderRegisterInteractor());

    Router::map('POST', '/api/fixedQuantityOrder/register', [
        OrderController::class,
        'fixedQuantityOrderRegister',
    ])->service($useCaseProvider->getOrderRegisterInteractor());

    Router::map('GET', '/api/order/unapproved/show', [
        OrderController::class,
        'unapprovedShow',
    ])->service($useCaseProvider->getOrderShowInteractor());

    Router::map('PATCH', '/api/order/unapproved/:orderId/update', [
        OrderController::class,
        'unapprovedUpdate',
    ])->service($useCaseProvider->getOrderUnapprovedUpdateInteractor());

    Router::map('GET', '/api/order/fixedQuantityOrder', [
        OrderController::class,
        'fixedQuantityOrder',
    ])->service($useCaseProvider->getFixedQuantityOrderInteractor());

    Router::map('GET', '/api/order/unreceivedShow', [
        OrderController::class,
        'unreceivedShow',
    ])->service($useCaseProvider->getOrderUnReceivedShowInteractor());

    Router::map('DELETE', '/api/order/unapproved/:orderId/delete', [
        OrderController::class,
        'unapprovedDelete',
    ])->service($useCaseProvider->getOrderUnapprovedDeleteInteractor());

    Router::map('PATCH', '/api/order/unapproved/:orderId/approval', [
        OrderController::class,
        'approval',
    ])->service($useCaseProvider->getOrderUnapprovedApprovalInteractor());

    Router::map('POST', '/api/order/unapproved/approval/all', [
        OrderController::class,
        'approvalAll',
    ])->service($useCaseProvider->getOrderUnapprovedApprovalAllInteractor());

    Router::map(
        'DELETE',
        '/api/order/unapproved/:orderId/:orderItemId/delete',
        [OrderController::class, 'unapprovedItemDelete']
    )->service($useCaseProvider->getOrderUnapprovedItemDeleteInteractor());

    Router::map('GET', '/api/order/show', [
        OrderController::class,
        'show',
    ])->service($useCaseProvider->getOrderShowInteractor());

    Router::map('PATCH', '/api/order/item/bulkUpdate', [
        OrderController::class,
        'itemBulkUpdate',
    ])->service($useCaseProvider->getOrderItemBulkUpdateInteractor());

    Router::map('PATCH', '/api/order/:orderId/revised', [
        OrderController::class,
        'revised',
    ])->service($useCaseProvider->getOrderRevisedInteractor());

    Router::map('DELETE', '/api/order/:orderId/delete', [
        OrderController::class,
        'delete',
    ])->service($useCaseProvider->getOrderDeleteInteractor());

    Router::map('POST', '/api/order/:orderId/sent', [
        OrderController::class,
        'sent',
    ]);

    Router::map('GET', '/api/received/order/list', [
        ReceivedController::class,
        'orderList',
    ])->service($useCaseProvider->getOrderShowInteractor());

    Router::map('POST', '/api/:orderId/received/register', [
        ReceivedController::class,
        'orderRegister',
    ])->service($useCaseProvider->getReceivedRegisterByOrderSlipInteractor());

    Router::map('POST', '/api/:receivedId/return/register', [
        ReceivedController::class,
        'returnRegister',
    ])->service($useCaseProvider->getReceivedReturnRegisterInteractor());

    Router::map('GET', '/api/received/show', [
        ReceivedController::class,
        'show',
    ])->service($useCaseProvider->getReceivedShowInteractor());

    Router::map('POST', '/api/received/register', [
        ReceivedController::class,
        'register',
    ])->service($useCaseProvider->getReceivedRegisterInteractor());

    Router::map('POST', '/api/received/lateRegister', [
        ReceivedController::class,
        'lateRegister',
    ])->service($useCaseProvider->getReceivedLateRegisterInteractor());

    Router::map('GET', '/api/return/show', [
        ReturnController::class,
        'show',
    ])->service($useCaseProvider->getReturnShowInteractor());

    Router::map('GET', '/api/barcode/search', [
        BarcodeController::class,
        'search',
    ])->service($useCaseProvider->getBarcodeSearchInteractor());

    Router::map('GET', '/api/barcode/order/search', [
        BarcodeController::class,
        'orderSearch',
    ])->service($useCaseProvider->getBarcodeOrderSearchInteractor());

    Router::map('GET', '/api/stocktaking/inHospitalItem', [
        StocktakingController::class,
        'inHospitalItem',
    ]);

    Router::map('GET', '/api/notification/show', [
        NotificationController::class,
        'show',
    ])->service($useCaseProvider->getNotificationShowInteractor());

    Router::map('GET', '/api/reference/consumption', [
        ReferenceController::class,
        'consumption',
    ])->service($useCaseProvider->getConsumptionHistoryShowInteractor());

    Router::map('POST', '/api/itemrequest/register', [
        ItemRequestController::class,
        'register',
    ])->service($useCaseProvider->getItemRequestRegisterInteractor());

    Router::map('GET', '/api/itemrequest/history', [
        ItemRequestController::class,
        'history',
    ])->service($useCaseProvider->getItemRequestHistoryInteractor());

    Router::map('DELETE', '/api/itemrequest/:requestHId/delete', [
        ItemRequestController::class,
        'delete',
    ])->service($useCaseProvider->getItemRequestDeleteInteractor());

    Router::map('DELETE', '/api/itemrequest/:requestHId/:requestId/delete', [
        ItemRequestController::class,
        'itemDelete',
    ])->service($useCaseProvider->getRequestItemDeleteInteractor());

    Router::map('PATCH', '/api/itemrequest/:requestHId/update', [
        ItemRequestController::class,
        'update',
    ])->service($useCaseProvider->getItemRequestUpdateInteractor());

    Router::map('GET', '/api/itemrequest/totalization', [
        ItemRequestController::class,
        'totalization',
    ])->service($useCaseProvider->getTotalizationInteractor());

    Router::map('PATCH', '/api/itemrequest/item/bulk', [
        ItemRequestController::class,
        'itemBulk',
    ])->service($useCaseProvider->getItemRequestBulkUpdateInteractor());

    Router::map('POST', '/api/payout/register', [
        PayoutController::class,
        'register',
    ])->service($useCaseProvider->getPayoutRegisterInteractor());

    Router::map('GET', '/api/payout/index', [
        PayoutController::class,
        'index',
    ]);
    
    Router::map('GET', '/api/payout/:payoutHistoryId', [
        PayoutController::class,
        'show',
    ]);

    Router::map('PATCH', '/api/payout/:payoutHistoryId', [
        PayoutController::class,
        'update',
    ]);
    
    Router::map('DELETE', '/api/payout/:payoutHistoryId', [
        PayoutController::class,
        'delete',
    ]);

    Router::map('POST', '/api/acceptance/register', [
        AcceptanceController::class,
        'register',
    ])->service($useCaseProvider->getAcceptanceRegisterInteractor());

    
    Router::map('GET', '/api/acceptance/index', [
        AcceptanceController::class,
        'index',
    ]);
    
    Router::map('GET', '/api/acceptance/:acceptanceId', [
        AcceptanceController::class,
        'show',
    ]);
    
    Router::map('Patch', '/api/acceptance/:acceptanceId', [
        AcceptanceController::class,
        'update',
    ]);
    
    Router::map('POST', '/api/acceptance/:acceptanceId/payout', [
        AcceptanceController::class,
        'payoutRegister',
    ]);

    Router::map('Delete', '/api/acceptance/:acceptanceId', [
        AcceptanceController::class,
        'delete',
    ]);

    Router::map('POST', '/api/accountant/register', [
        AccountantController::class,
        'register',
    ])->service($useCaseProvider->getAccountantRegisterInteractor());

    Router::map('GET', '/api/accountant/index', [
        AccountantController::class,
        'index',
    ])->service($useCaseProvider->getAccountantIndexInteractor());

    Router::map('GET', '/api/accountant/items', [
        AccountantController::class,
        'items',
    ])->service($useCaseProvider->getAccountantItemsIndexInteractor());

    Router::map('GET', '/api/accountant/logs', [
        AccountantLogController::class,
        'logs',
    ])->service($useCaseProvider->getAccountantLogsIndexInteractor());

    Router::map('GET', '/api/accountant/:accountantId', [
        AccountantController::class,
        'show',
    ])->service($useCaseProvider->getAccountantShowInteractor());

    Router::map('patch', '/api/accountant/:accountantId/update', [
        AccountantController::class,
        'update',
    ])->service($useCaseProvider->getAccountantUpdateInteractor());

    Router::map('delete', '/api/accountant/:accountantId/delete', [
        AccountantController::class,
        'delete',
    ])->service($useCaseProvider->getAccountantUpdateInteractor());

    Router::map('get', '/api/accountant/items/download', [
        AccountantController::class,
        'itemsDownload',
    ]);

    Router::map('get', '/api/accountant/items/totalPrice', [
        AccountantController::class,
        'totalPrice',
    ]);

    Router::map('get', '/api/accountant/logs/download', [
        AccountantLogController::class,
        'itemsDownload',
    ]);

    Router::map('get', '/api/accountant/logs/totalPrice', [
        AccountantLogController::class,
        'totalPrice',
    ]);
    
    Router::map('GET', '/api/product/label/items', [
        ProductController::class,
        'items',
    ]);

    Router::map('GET', '/api/product/itemList/index', [
        ItemListController::class,
        'index',
    ])->service($useCaseProvider->getItemListIndexInteractor());

    Router::map('POST', '/api/product/itemList/register', [
        ItemListController::class,
        'register',
    ])->service($useCaseProvider->getItemListRegisterInteractor());

    Router::map('GET', '/api/product/itemList/:itemListId', [
        ItemListController::class,
        'show',
    ])->service($useCaseProvider->getItemListShowInteractor());

    Router::map('patch', '/api/product/itemList/:itemListId/update', [
        ItemListController::class,
        'update',
    ])->service($useCaseProvider->getItemListUpdateInteractor());

    Router::map('delete', '/api/product/itemList/:itemListId/delete', [
        ItemListController::class,
        'delete',
    ]);

    
    Router::map('GET', '/api/stock/:divisionId/:inHospitalItemId', [
        StockController::class,
        'stock',
    ]);

    Router::map('GET', '/api/stocktaking/stocktakingList/index', [
        StocktakingListController::class,
        'index',
    ])->service($useCaseProvider->getStocktakingListIndexInteractor());

    Router::map('POST', '/api/stocktaking/stocktakingList/register', [
        StocktakingListController::class,
        'register',
    ])->service($useCaseProvider->getStocktakingListRegisterInteractor());

    Router::map('GET', '/api/stocktaking/stocktakingList/:stockListId', [
        StocktakingListController::class,
        'show',
    ])->service($useCaseProvider->getStocktakingListShowInteractor());

    Router::map('patch', '/api/stocktaking/stocktakingList/:stockListId/update', [
        StocktakingListController::class,
        'update',
    ])->service($useCaseProvider->getStocktakingListUpdateInteractor());

    Router::map('delete', '/api/stocktaking/stocktakingList/:stockListId/delete', [
        StocktakingListController::class,
        'delete',
    ]);
});

$router = new Router();
$app = new JoyPlaApplication();
$exceptionHandler = new ApiExceptionHandler();
$kernel = new \framework\Http\Kernel($app, $router, $exceptionHandler);

$auth = $app->getAuth();

$request->setUser($auth);
$kernel->handle($request);