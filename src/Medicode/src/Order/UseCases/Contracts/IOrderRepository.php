<?php

declare(strict_types=1);

namespace Medicode\Order\UseCases\Contracts;

use Medicode\Authentication\Domain\ValueObjects\AccessToken;
use Medicode\Order\Domain\OrderList;

interface IOrderRepository
{
    /**
     * @return array
     */
    public function get(): array;
    
    
    /**
     * @param AccessToken $accessToken
     * @param OrderList $orderList
     * @return array
     */
    public function send(AccessToken $accessToken, OrderList $orderList): array;
}
