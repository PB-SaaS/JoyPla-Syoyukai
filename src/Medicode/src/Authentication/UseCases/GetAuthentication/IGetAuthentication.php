<?php

declare(strict_types=1);

namespace Medicode\Authentication\UseCases\GetAuthentication;

use Medicode\Authentication\Domain\Authentication;

interface IGetAuthentication
{
    /**
     * @return Authentication
     */
    public function handle(): Authentication;
}
