<?php

declare(strict_types=1);

namespace Medicode\Order\Domain\Factory;

use Medicode\Order\Domain\Order;

final class OrderFactory
{
    public static function create(array $values = []): Order
    {
        $id = '';
        $orderCNumber = '';
        $orderNumber = '';
        $hospitalCode = '';
        $distributorCode = '';
        $JANCode = '';
        $quantity = 0;
        $deliveryDestCode = '';

        if (array_key_exists('recordId', $values)) {
            $id = $values['recordId'];
        }

        if (array_key_exists('orderCNumber', $values)) {
            $orderCNumber = $values['orderCNumber'];
        }

        if (array_key_exists('orderNumber', $values)) {
            $orderNumber = $values['orderNumber'];
        }

        if (array_key_exists('hospitalCode', $values)) {
            $hospitalCode = trim($values['hospitalCode']);
        }

        if (array_key_exists('distributorCode', $values)) {
            $distributorCode = trim($values['distributorCode']);
        }

        if (array_key_exists('itemJANCode', $values)) {
            $JANCode = trim($values['itemJANCode']);
        }

        if (array_key_exists('orderQuantity', $values)) {
            $quantity = (int)$values['orderQuantity'];
        }


        if (array_key_exists('deliveryDestCode', $values)) {
            $deliveryDestCode = (string)$values['deliveryDestCode'];
        }

        return new Order($id, $orderCNumber, $orderNumber, $hospitalCode, $distributorCode, $JANCode, $quantity, $deliveryDestCode);
    }
}
