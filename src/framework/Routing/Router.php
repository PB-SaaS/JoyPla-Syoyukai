<?php

namespace framework\Routing;

use App\Http\Middleware\MiddlewareTrait;
use Closure;
use Exception;
use framework\Exception\NotFoundException;
use framework\Http\Request;
use framework\Service\ServiceProvider;
use Response;

class Router
{
    use MiddlewareTrait;

    public static array $routes = [];

    /**
     * Router constructor.
     *
     * @param ServiceProvider $container
     */
    public function __construct() {
    }

    /**
     * @param string         $method
     * @param string         $pass
     * @param array|Closure $handler
     *
     * @return Route
     */
    final public static function map(string $method, string $pass, $handler): Route
    {
        $route = new Route($method, $pass, $handler);

        self::$routes[] = $route;

        return $route->middleware(self::$groupMiddlewares);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    final public function dispatch(Request $request, $isMethodCheck = true)
    {
        foreach (self::$routes as $route) {
            if ($route->processable($request , $isMethodCheck)) {
                $route->middleware($this->middlewares);
                return $route->process($request , $route->service);
            }
        }

        throw new NotFoundException('Not Found.',404);
    }

    public static function redirect($uri , Request $request){
        $request->setRequestUri($uri);
        $router = new Router();
        return $router->dispatch($request , false);
    }

    public static function abort(int $code , string $message = "")
    {
        if($message == ""){
            switch ($code) {
                case 404:
                    $message = "Not Found";
                    break;
                case 403:
                    $message = "Forbidden";
                    break;
            }
        }

        throw new Exception($message , $code);
    }
    
}