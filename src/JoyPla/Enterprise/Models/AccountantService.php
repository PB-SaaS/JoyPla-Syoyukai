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
