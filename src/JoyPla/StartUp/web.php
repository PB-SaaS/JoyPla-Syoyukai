<?php

require_once 'framework/Bootstrap/autoload.php';
require_once 'JoyPla/require.php';
/** */

/** components */

use framework\Http\Request;
use framework\Routing\Router;
use JoyPla\InterfaceAdapters\Controllers\Web\AcceptanceController;
use JoyPla\InterfaceAdapters\Controllers\Web\PayoutController;
use JoyPla\InterfaceAdapters\Controllers\Web\AccountantController;
use JoyPla\InterfaceAdapters\Controllers\Web\AgreeFormController;
use JoyPla\InterfaceAdapters\Controllers\Web\ConsumptionController;
use JoyPla\InterfaceAdapters\Controllers\Web\ItemAndPriceAndInHospitalItemRegisterController;
use JoyPla\InterfaceAdapters\Controllers\Web\NotificationController;
use JoyPla\InterfaceAdapters\Controllers\Web\OptionController;
use JoyPla\InterfaceAdapters\Controllers\Web\OrderController;
use JoyPla\InterfaceAdapters\Controllers\Web\PriceAndInHospitalItemRegisterController;
use JoyPla\InterfaceAdapters\Controllers\Web\ReceivedController;
use JoyPla\InterfaceAdapters\Controllers\Web\ReturnController;
use JoyPla\InterfaceAdapters\Controllers\Web\StocktakingController;
use JoyPla\InterfaceAdapters\Controllers\Web\TopController;
use JoyPla\InterfaceAdapters\Controllers\Web\ItemRequestController;
use JoyPla\InterfaceAdapters\Controllers\Web\ItemListController;
use JoyPla\InterfaceAdapters\Controllers\Web\LabelController;
use JoyPla\InterfaceAdapters\GateWays\Middleware\PersonalInformationConsentMiddleware;
use JoyPla\InterfaceAdapters\GateWays\Middleware\UnorderDataExistMiddleware;
use JoyPla\JoyPlaApplication;
use JoyPla\Service\Presenter\Web\PresenterProvider;
use JoyPla\Service\Repository\QueryProvider;
use JoyPla\Service\Repository\RepositoryProvider;
use JoyPla\Service\UseCase\Web\UseCaseProvider;
use Test\Exceptions\WebExceptionHandler;

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

Router::map('GET', '/agree', [AgreeFormController::class, 'index']);

Router::map('POST', '/agree', [AgreeFormController::class, 'send']);

Router::group(PersonalInformationConsentMiddleware::class, function () use (
    $useCaseProvider
) {
    Router::map('GET', '/', [TopController::class, 'index']);

    Router::map('GET', '/order', [TopController::class, 'orderpage']);

    Router::map('GET', '/consumption', [
        TopController::class,
        'consumptionpage',
    ]);

    Router::map('GET', '/stocktaking', [
        TopController::class,
        'stocktakingpage',
    ]);

    Router::map('GET', '/payout', [TopController::class, 'payoutpage']);

    Router::map('GET', '/stock', [TopController::class, 'stockpage']);

    Router::map('GET', '/card', [TopController::class, 'cardpage']);

    Router::map('GET', '/trackrecord', [
        TopController::class,
        'trackrecordpage',
    ]);

    Router::map('GET', '/monthlyreport', [
        TopController::class,
        'monthlyreportpage',
    ]);

    Router::map('GET', '/estimate', [TopController::class, 'estimatepage']);

    Router::map('GET', '/lending', [TopController::class, 'lendingpage']);

    Router::map('GET', '/product', [TopController::class, 'productpage']);

    Router::map('GET', '/user', [TopController::class, 'userpage']);

    Router::map('GET', '/itemrequest', [
        TopController::class,
        'itemrequestpage',
    ]);

    Router::map('GET', '/option', [OptionController::class, 'index']);

    Router::map('GET', '/consumption/register', [
        ConsumptionController::class,
        'register',
    ]);

    Router::map('GET', '/consumption/bulkRegister', [
        ConsumptionController::class,
        'bulkRegister',
    ]);

    Router::map('GET', '/consumption/index', [
        ConsumptionController::class,
        'index',
    ]);

    Router::map('GET', '/consumption/:consumptionId', [
        ConsumptionController::class,
        'show',
    ])->service($useCaseProvider->getConsumptionShowInteractor());

    Router::map('GET', '/consumption/:consumptionId/print', [
        ConsumptionController::class,
        'print',
    ])->service($useCaseProvider->getConsumptionPrintInteractor());

    Router::group(UnorderDataExistMiddleware::class, function () {
        Router::map('GET', '/order/fixedQuantityOrder', [
            OrderController::class,
            'fixedQuantityOrder',
        ]);
    });

    Router::map('GET', '/order/bulk/edit', [
        OrderController::class,
        'bulkEdit',
    ]);

    Router::map('GET', '/order/register', [OrderController::class, 'register']);

    Router::map('GET', '/order/unapproved/show', [
        OrderController::class,
        'unapprovedShow',
    ]);

    Router::map('GET', '/order/unapproved/:orderId', [
        OrderController::class,
        'unapprovedIndex',
    ])->service($useCaseProvider->getUnapprovedOrderIndexInteractor());

    Router::map('GET', '/order/show', [OrderController::class, 'show']);

    Router::map('GET', '/order/:orderId', [
        OrderController::class,
        'index',
    ])->service($useCaseProvider->getOrderIndexInteractor());

    Router::map('GET', '/order/unapproved/:orderId/print', [
        OrderController::class,
        'printOfUnapproved',
    ])->service($useCaseProvider->getOrderPrintInteractor());

    Router::map('GET', '/order/:orderId/print', [
        OrderController::class,
        'print',
    ])->service($useCaseProvider->getOrderPrintInteractor());

    Router::map('GET', '/received/show', [ReceivedController::class, 'show']);

    Router::map('GET', '/received/register', [
        ReceivedController::class,
        'register',
    ]);

    Router::map('GET', '/received/:receivedId', [
        ReceivedController::class,
        'index',
    ])->service($useCaseProvider->getReceivedIndexInteractor());

    //TODO
    //Router::map('GET', '/received/:receivedId/labelsetting',[ReceivedController::class,'labelsetting'])->service(new ReceivedIndexInteractor(new ReceivedLabelSettingPresenter() , new ReceivedRepository(), new ReceivedRepository()) );
    //TODO
    //Router::map('GET', '/received/:receivedId/label',[ReceivedController::class,'label'])->service(new ReceivedLabelInteractor(new ReceivedLabelPresenter() , new ReceivedRepository(), new HospitalRepository()) );

    Router::map('GET', '/received/order/list', [
        ReceivedController::class,
        'orderList',
    ]);

    Router::map('GET', '/received/order/:orderId', [
        ReceivedController::class,
        'orderReceivedSlipIndex',
    ])->service($useCaseProvider->getOrderReceivedSlipIndexInteractor());

    Router::map('GET', '/return/show', [ReturnController::class, 'show']);

    Router::map('GET', '/stocktakingimport', [
        StocktakingController::class,
        'import',
    ]);

    Router::map('GET', '/notification', [
        NotificationController::class,
        'show',
    ]);

    Router::map('GET', '/notification/:notificationId', [
        NotificationController::class,
        'index',
    ]);

    Router::map('GET', '/product/ItemAndPriceAndInHospitalRegist/input', [
        ItemAndPriceAndInHospitalItemRegisterController::class,
        'register',
    ]);
    Router::map('POST', '/product/ItemAndPriceAndInHospitalRegist/confirm', [
        ItemAndPriceAndInHospitalItemRegisterController::class,
        'confirm',
    ]);
    Router::map('POST', '/product/ItemAndPriceAndInHospitalRegist/thanks', [
        ItemAndPriceAndInHospitalItemRegisterController::class,
        'thanks',
    ]);

    Router::map('POST', '/product/PriceAndInHospitalRegist/input', [
        PriceAndInHospitalItemRegisterController::class,
        'register',
    ]);
    Router::map('POST', '/product/PriceAndInHospitalRegist/confirm', [
        PriceAndInHospitalItemRegisterController::class,
        'confirm',
    ]);
    Router::map('POST', '/product/PriceAndInHospitalRegist/thanks', [
        PriceAndInHospitalItemRegisterController::class,
        'thanks',
    ]);

    Router::map('GET', '/itemrequest/register', [
        ItemRequestController::class,
        'register',
    ]);

    Router::map('GET', '/itemrequest/history', [
        ItemRequestController::class,
        'history',
    ]);

    Router::map('GET', '/itemrequest/totalization', [
        ItemRequestController::class,
        'totalization',
    ]);

    Router::map('POST', '/itemrequest/pickingList', [
        ItemRequestController::class,
        'pickingList',
    ])->service($useCaseProvider->getPickingListInteractor());

    Router::map('GET', '/itemrequest/bulk', [
        ItemRequestController::class,
        'list',
    ])->service($useCaseProvider->getPickingListInteractor());

    Router::map('GET', '/itemrequest/:requestHId', [
        ItemRequestController::class,
        'show',
    ])->service($useCaseProvider->getItemRequestShowInteractor());

    Router::map('GET', '/accountant', [TopController::class, 'accountant']);

    Router::map('GET', '/accountant/index', [
        AccountantController::class,
        'index',
    ]);

    Router::map('GET', '/accountant/items', [
        AccountantController::class,
        'items',
    ]);

    Router::map('GET', '/accountant/logs', [
        AccountantController::class,
        'logs',
    ]);

    Router::map('GET', '/accountant/:accountantId', [
        AccountantController::class,
        'show',
    ]);

    Router::map('GET', '/accountant/:accountantId/print', [
        AccountantController::class,
        'print',
    ]);

    Router::map('GET', '/payout/register', [
        PayoutController::class,
        'register',
    ]);

    Router::map('GET', '/product/itemList/index', [
        ItemListController::class,
        'index',
    ]);

    Router::map('GET', '/product/itemList/:itemListId', [
        ItemListController::class,
        'show',
    ]);

    Router::map('GET', '/product/itemList/:itemListId/print', [
        ItemListController::class,
        'print',
    ])/* ->service($useCaseProvider->getItemListPrintInteractor()) */;

    
    Router::map('GET', 'acceptance', [
        AcceptanceController::class,
        'index',
    ]);

    Router::map('GET', 'acceptance/:acceptanceId', [
        AcceptanceController::class,
        'show',
    ]);

    Router::map('GET', '/label/payout/:payoutId', [
        LabelController::class,
        'payoutLabelPrint',
    ]);
    
    Router::map('GET', '/label/acceptance/:acceptanceId', [
        LabelController::class,
        'payoutLabelPrintForAcceptance',
    ]);
    
    
});

$router = new Router();
//$router->middleware();毎回必ずチェックする場合はこっち
$app = new JoyPlaApplication();
$exceptionHandler = new WebExceptionHandler();
$kernel = new \framework\Http\Kernel($app, $router, $exceptionHandler);
$request = new Request();

$auth = $app->getAuth();

$request->setUser($auth);
$kernel->handle($request);
