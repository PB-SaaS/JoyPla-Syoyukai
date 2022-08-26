<?php

declare(strict_types=1);

namespace Medicode\Authentication\UseCases\Contracts;

use Medicode\Authentication\Domain\ValueObjects\MedicodeApiId;
use Medicode\Authentication\Domain\ValueObjects\Password;

interface IMedicodeAuthenticationRepository
{
    /**
     * @param MedicodeApiId $medicodeApiId
     * @param Password $password
     * @return array
     */
    public function get(MedicodeApiId $medicodeApiId, Password $password): array;
}
