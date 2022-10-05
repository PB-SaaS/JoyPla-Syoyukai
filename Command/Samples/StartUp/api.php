<?php
echo '
<?php
require_once "framework/Bootstrap/autoload.php";
require_once "'.$projectName.'/require.php";

use framework\Routing\Router;

/** */

/** components */

//param _method="" を指定すると GET PUT DELETE GET PATCH を区別できる

const VIEW_FILE_ROOT = "";

/** sample */

//Router::map("GET", "/users", [UserController:: class , "show"]);

//Router::map("GET", "/:userId", [UserController:: class , "index"]);

//Router::map("POST", "/user", [HogeHogeController:: class , "create"]);

//Router::map("PATCH", "/:userId", [HogeHogeController:: class , "update"]);

//Router::map("DELETE", "/", [HogeHogeController:: class , "delete"]);


try{ 
    $router = new Router();
    //$router->middleware();毎回必ずチェックする場合はこっち
    $app = new '.$projectName.'\\'.$projectName.'Application();
    $exceptionHandler = new '.$projectName.'\Exceptions\ExceptionHandler();
    $kernel = new \framework\Http\Kernel($app, $router ,$exceptionHandler);
    $request = new \framework\Http\Request();
    
    $kernel->handle($request);

} catch(Exception $e) {
    var_dump([ 
        "code" => $e->getCode(), 
        "message" => $e->getMessage()
    ]);
}  
';