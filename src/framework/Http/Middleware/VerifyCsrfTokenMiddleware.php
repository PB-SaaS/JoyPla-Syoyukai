<?php
namespace App\Http\Middleware;

use Csrf;

class VerifyCsrfTokenMiddleware extends Middleware implements MiddlewareInterface {

    public function process(array $vars) : void
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);
    }
}