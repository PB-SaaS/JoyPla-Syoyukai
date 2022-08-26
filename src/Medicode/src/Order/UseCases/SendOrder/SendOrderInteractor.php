<?php

declare(strict_types=1);

namespace Medicode\Order\UseCases\SendOrder;

use Medicode\Authentication\Domain\ValueObjects\AccessToken;
use Medicode\Order\Domain\OrderList;
use Medicode\Order\UseCases\Contracts\IOrderRepository;
use Medicode\Order\UseCases\SendOrder\ISendOrder;

class SendOrderInteractor implements ISendOrder
{
    
    private IOrderRepository $orderRepository;
    
    /**
     * AuthenticateInteractor constructor.
     * @param IOrderRepository $orderRepository
     */
    public function __construct(
        IOrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }
    
    
    /**
     * @param AccessToken $accessToken
     * @return array
     */
    public function handle(AccessToken $accessToken): array
    {
        $orderList = new OrderList();
        
        $orders = $this->orderRepository->get();
        
        if (count($orders) === 0)
        {
            return ['code' => 0, 'message' => '対象の発注データが存在しません。'];
        }
        
        foreach ($orders as $order)
        {
            $orderList->add($order);
        }
        
        $result = $this->orderRepository->send($accessToken, $orderList);
        $result['data'] = $orderList;
        
        return $result;
    }
}
