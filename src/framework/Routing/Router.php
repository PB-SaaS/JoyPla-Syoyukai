<?php

namespace framework\Routing;

use Closure;
use framework\Exception\NotFoundException;
use framework\Http\Request;
use framework\Service\ServiceProvider;
use Response;

class Router
{
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

        return $route;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    final public function dispatch(Request $request)
    {
        foreach (self::$routes as $route) {
            if ($route->processable($request)) {
                // Merge root middleware and global middleware.
                // $route->middleware($this->middlewares);

                return $route->process($request , $route->service);
            }
        }

        throw new NotFoundException('404 Not Found.');
    }
}