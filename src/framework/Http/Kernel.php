<?php

namespace framework\Http;

use framework\Application;
use framework\Routing\Router;
use framework\Service\ServiceProvider;

class Kernel
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;
    }

    final public function handle(Request $request)
    {
        // add global middleware.
        // $this->router->middleware($this->middlewares);

        return $this->router->dispatch($request);
    }
}