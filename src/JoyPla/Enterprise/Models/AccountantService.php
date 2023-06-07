<?php

namespace JoyPla\Enterprise\Models;

use JoyPla\Enterprise\TmpModels\TmpValueObject;

class AccountantService
{
    public function __construct()
    {
    }

    public static function checkAccountant(
        Accountant $newAccountant,
        Accountant $oldAccountant,
        string $userId
    ) {
        $newItems = $newAccountant->getItems();
        $oldItems = $oldAccountant->getItems();

        $logItems = [];
        foreach ($oldItems as $oldItem) {
            $newItem = array_find($newItems, function (
                AccountantItem $item
            ) use ($oldItem) {
                return $item->getAccountantItemId()->value() ===
                    $oldItem->getAccountantItemId()->value();
            });

            if (!$newItem) {
                $logItems[] = new AccountantItemChageLog(
                    '削除',
                    $userId,
                    $oldItem
                );
                continue;
            }

            if (self::isChangeAccountantItem($newItem, $oldItem)) {
                $logItems[] = new AccountantItemChageLog(
                    '更新',
                    $userId,
                    $newItem
                );
                continue;
            }
        }

        foreach ($newItems as $newItem) {
            $oldItem = array_find($oldItems, function (
                AccountantItem $item
            ) use ($newItem) {
                return $item->getAccountantItemId()->value() ===
                    $newItem->getAccountantItemId()->value();
            });

            if (!$oldItem) {
                $logItems[] = new AccountantItemChageLog(
                    '登録',
                    $userId,
                    $newItem
                );
                continue;
            }
        }

        return $logItems;
    }

    public static function ReceivedToAccountant(Received $received, DateYearMonthDay $accountantDate)
    {
        $accountant = Accountant::init(
            $accountantDate ? $accountantDate->value() : date('Y-m-d'),
            $received
                ->getHospital()
                ->getHospitalId()
                ->value(),
            $received
                ->getDivision()
                ->getDivisionId()
                ->value(),
            $received
                ->getDistributor()
                ->getDistributorId()
                ->value(),
            $received->getOrderId()->value(),
            $received->getReceivedId()->value()
        );

        foreach ($received->getReceivedItems() as $index => $item) {
            $accountantItem = AccountantItem::init(
                $index,
                $accountant->getAccountantId()->value(),
                '自動',
                '入荷',
                AccountantItemId::generate()->value(),
                $item
                    ->getItem()
                    ->getItemId()
                    ->value(),
                $item
                    ->getItem()
                    ->getMakerName()
                    ->value(),
                $item
                    ->getItem()
                    ->getItemName()
                    ->value(),
                $item
                    ->getItem()
                    ->getItemCode()
                    ->value(),
                $item
                    ->getItem()
                    ->getItemStandard()
                    ->value(),
                $item
                    ->getItem()
                    ->getItemJANCode()
                    ->value(),
                $item->getReceivedQuantity()->value(),
                $item->getQuantity()->getItemUnit(),
                $item->getPrice()->value(),
                0
            );

            $accountant->addItem($accountantItem);
        }

        return $accountant;
    }

    public static function LateReceivedToAccountant(Received $received, DateYearMonthDay $accountantDate)
    {
        $accountant = Accountant::init(
            $accountantDate ? $accountantDate->value() : date('Y-m-d'),
            $received
                ->getHospital()
                ->getHospitalId()
                ->value(),
            $received
                ->getDivision()
                ->getDivisionId()
                ->value(),
            $received
                ->getDistributor()
                ->getDistributorId()
                ->value(),
            $received->getOrderId()->value(), //nullかもしれない。
            $received->getReceivedId()->value()
        );

        foreach ($received->getReceivedItems() as $index => $item) {
            $accountantItem = AccountantItem::init(
                $index,
                $accountant->getAccountantId()->value(),
                '自動',
                '入荷',
                AccountantItemId::generate()->value(),
                $item
                    ->getItem()
                    ->getItemId()
                    ->value(),
                $item
                    ->getItem()
                    ->getMakerName()
                    ->value(),
                $item
                    ->getItem()
                    ->getItemName()
                    ->value(),
                $item
                    ->getItem()
                    ->getItemCode()
                    ->value(),
                $item
                    ->getItem()
                    ->getItemStandard()
                    ->value(),
                $item
                    ->getItem()
                    ->getItemJANCode()
                    ->value(),
                $item->getReceivedQuantity()->value(),
                $item->getQuantity()->getItemUnit(),
                $item->getPrice()->value(),
                0
            );

            $accountant->addItem($accountantItem);
        }

        return $accountant;
    }

    public static function isChangeAccountantItem(
        AccountantItem $newItem,
        AccountantItem $oldItem
    ) {
        $newItem = $newItem->toArray();
        $oldItem = $oldItem->toArray();

        if ($newItem['index'] !== $oldItem['index']) {
            return true;
        }
        if ($newItem['accountantMethod'] !== $oldItem['accountantMethod']) {
            return true;
        }
        if ($newItem['accountantAction'] !== $oldItem['accountantAction']) {
            return true;
        }
        /*
        if($newItem['accountantItemId'] !== $oldItem['accountantItemId']){
            return true;
        }
        */
        if ($newItem['itemId'] !== $oldItem['itemId']) {
            return true;
        }
        if ($newItem['makerName'] !== $oldItem['makerName']) {
            return true;
        }
        if ($newItem['itemName'] !== $oldItem['itemName']) {
            return true;
        }
        if ($newItem['itemCode'] !== $oldItem['itemCode']) {
            return true;
        }
        if ($newItem['itemStandard'] !== $oldItem['itemStandard']) {
            return true;
        }
        if ($newItem['itemJANCode'] !== $oldItem['itemJANCode']) {
            return true;
        }
        if ($newItem['count'] !== $oldItem['count']) {
            return true;
        }
        if ($newItem['unit'] !== $oldItem['unit']) {
            return true;
        }
        if ($newItem['price'] !== $oldItem['price']) {
            return true;
        }
        if ($newItem['taxrate'] !== $oldItem['taxrate']) {
            return true;
        }
        return false;
    }
}
