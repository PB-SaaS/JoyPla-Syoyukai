<?php
require_once "framework/Bootstrap/autoload.php";
require_once "JoyPla/require.php";
/** */

/** components */

use framework\Application;
use framework\Http\Request;
use framework\Routing\Router;
use JoyPla\Application\Interactors\Hospital\Top\TopIndexInteractor;
use JoyPla\Application\Interactors\Hospital\Top\TopOrderPageInteractor;
use JoyPla\InterfaceAdapters\Controllers\Hospital\Top\TopController;
use JoyPla\InterfaceAdapters\Presenters\Hospital\Top\TopIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Hospital\Top\TopOrderPagePresenter;

//SPIRALはすべてPOSTになる

const VIEW_FILE_ROOT = "JoyPla/resources";

Router::map('POST', '/top', function(Request $req){
echo"test";
})->service(new Request());

Router::map('POST', '/', [TopController::class , 'index'])->service(new TopIndexInteractor(new TopIndexPresenter()));

Router::map('POST', '/orderpage', [TopController::class , 'orderpage'])->service(new TopOrderPageInteractor(new TopOrderPagePresenter()));

$router = new Router();

$app = new Application();
$kernel = new \framework\Http\Kernel($app, $router);
$request = new Request();
$kernel->handle($request);