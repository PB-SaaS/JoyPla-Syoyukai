<?php

namespace JoyPla\Enterprise\Models;

use Exception;

class AccountantItemChageLog
{
    private string $kinds;
    private string $userId;
    private AccountantItem $accountantItem;

    public function __construct(
        string $kinds,
        string $userId,
        AccountantItem $accountantItem
    ) {
        if (!in_array($kinds, ['登録', '更新', '削除'], true)) {
            throw new Exception('in valid action');
        }
        $this->kinds = $kinds;
        $this->userId = $userId;
        $this->accountantItem = $accountantItem;
    }

    public function toArray()
    {
        $result = [];
        $result['kinds'] = $this->kinds;
        $result['userId'] = $this->userId;
        $result['accountantItem'] = $this->accountantItem->toArray();

        return $result;
    }
}
