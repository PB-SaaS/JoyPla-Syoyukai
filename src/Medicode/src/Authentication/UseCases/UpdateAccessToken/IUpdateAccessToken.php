<?php

declare(strict_types=1);

namespace Medicode\Authentication\UseCases\UpdateAccessToken;

use Medicode\Authentication\Domain\Authentication;
use Medicode\Authentication\Domain\ValueObjects\MedicodeApiId;
use Medicode\Authentication\Domain\ValueObjects\Password;

interface IUpdateAccessToken
{
    /**
     * @param MedicodeApiId $medicodeApiId
     * @param Password $password
     * @return Authentication
     */
    public function handle(MedicodeApiId $medicodeApiId, Password $password): Authentication;
}
