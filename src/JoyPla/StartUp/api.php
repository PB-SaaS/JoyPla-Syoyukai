<?php

require_once 'framework/Bootstrap/autoload.php';
require_once 'JoyPla/require.php';
/** */

/** components */

use App\Http\Middleware\VerifyCsrfTokenMiddleware;
use framework\Http\Request;
use framework\Routing\Router;
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
//Router::map('Get','/api/order/maintenance',[OrderController::class,'maintenance']);

Router::group(VerifyCsrfTokenMiddleware::class, function () use (
    $useCaseProvider
) {
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

    Router::map('POST', '/api/consumption/register', [
        ConsumptionController::class,
        'register',
    ])->service($useCaseProvider->getConsumptionRegisterInteractor());

    Router::map('GET', '/api/consumption/index', [
        ConsumptionController::class,
        'index',
    ])->service($useCaseProvider->getConsumptionIndexInteractor());

    Router::map('DELETE', '/api/consumption/:consumptionId/delete', [
        ConsumptionController::class,
        'delete',
    ])->service($useCaseProvider->getConsumptionDeleteInteractor());

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

    Router::map('POST', '/api/payout/register', [
        PayoutController::class,
        'register',
    ])->service($useCaseProvider->getPayoutRegisterInteractor());
});

$router = new Router();
$app = new JoyPlaApplication();
$exceptionHandler = new ApiExceptionHandler();
$kernel = new \framework\Http\Kernel($app, $router, $exceptionHandler);
$request = new Request();
$auth = new Auth('NJ_HUserDB', [
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
]);

$request->setUser($auth);
$kernel->handle($request);
