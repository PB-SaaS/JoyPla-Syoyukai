<?php

declare(strict_types=1);

namespace Medicode\Authentication\UseCases\Contracts;

use Medicode\Authentication\Domain\Authentication;

interface ISPIRALAuthenticationRepository
{
    /**
     * @return Authentication
     */
    public function get(): Authentication;
    
    
    /**
     * @param Authentication $authentication
     */
    public function update(Authentication $authentication): void;
}
