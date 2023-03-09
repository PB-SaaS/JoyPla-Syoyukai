<?php

namespace JoyPla\InterfaceAdapters\GateWays\Middleware;

use App\Http\Middleware\Middleware;
use App\Http\Middleware\MiddlewareInterface;
use framework\Routing\Router;

class PersonalInformationConsentMiddleware extends Middleware implements
    MiddlewareInterface
{
    public function process(array $vars): void
    {
        $auth = $this->request->user();
        if ($auth->termsAgreement != '2') {
            Router::redirect('/agree', $this->request);
            exit();
        }
    }
}
