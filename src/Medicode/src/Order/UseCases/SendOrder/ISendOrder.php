<?php

declare(strict_types=1);

namespace Medicode\Order\UseCases\SendOrder;

use Medicode\Authentication\Domain\ValueObjects\AccessToken;

interface ISendOrder
{
    /**
     * @param AccessToken $accessToken
     * @return array
     */
    public function handle(AccessToken $accessToken): array;
}
