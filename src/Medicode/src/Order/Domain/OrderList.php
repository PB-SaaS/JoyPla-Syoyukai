<?php

declare(strict_types=1);

namespace Medicode\Order\Domain;

use IteratorAggregate;
use Countable;
use ArrayIterator;
use Traversable;
use Medicode\Order\Domain\Order;

class OrderList implements IteratorAggregate, Countable
{
    /**
     * @var array
     * ファーストクラスコレクション
     */
    private array $orders = [];
    
    public function add(Order $order): void
    {
        $this->orders[] = $order;
    }
    
    
    /**
     * @return Order[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }
    
    
    public function count(): int
    {
        return count($this->orders);
    }
    
    
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->orders);
    }
}
