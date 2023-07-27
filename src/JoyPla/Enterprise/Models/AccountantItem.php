<?php

namespace JoyPla\Enterprise\Models;

class AccountantItem
{
    private int $index = 0;
    private AccountantId $accountantId;
    private AccountantMethod $accountantMethod;
    private AccountantAction $accountantAction;
    private AccountantItemId $accountantItemId;
    private ?ItemId $itemId;
    private ?MakerName $makerName = null;
    private ItemName $itemName;
    private ?ItemCode $itemCode = null;
    private ?ItemStandard $itemStandard = null;
    private ?Jancode $itemJANCode = null;
    private int $count = 0;
    private string $unit = '';
    private float $price = 0;
    private int $taxrate = 0;

    private array $option = [];

    public function __construct(
        int $index,
        AccountantId $accountantId,
        AccountantMethod $accountantMethod,
        AccountantAction $accountantAction,
        AccountantItemId $accountantItemId,
        ?ItemId $itemId = null,
        ?MakerName $makerName = null,
        ItemName $itemName,
        ?ItemCode $itemCode = null,
        ?ItemStandard $itemStandard = null,
        ?Jancode $itemJANCode = null,
        int $count = 0,
        string $unit = '',
        float $price = 0,
        int $taxrate = 0
    ) {
        $this->index = $index;
        $this->accountantId = $accountantId;
        $this->accountantMethod = $accountantMethod;
        $this->accountantAction = $accountantAction;
        $this->accountantItemId = $accountantItemId;
        $this->itemId = $itemId;
        $this->makerName = $makerName;
        $this->itemName = $itemName;
        $this->itemCode = $itemCode;
        $this->itemStandard = $itemStandard;
        $this->itemJANCode = $itemJANCode;
        $this->count = $count;
        $this->unit = $unit;
        $this->price = $price;
        $this->taxrate = $taxrate;
    }

    public static function init(
        int $index,
        string $accountantId,
        string $method,
        string $action,
        string $accountantItemId = null,
        string $itemId = null,
        ?string $makerName = null,
        string $itemName = null,
        ?string $itemCode = null,
        ?string $itemStandard = null,
        ?string $itemJANCode = null,
        int $count = 0,
        string $unit = '',
        float $price = 0,
        int $taxrate = 0
    ) {
        return new self(
            $index,
            new AccountantId($accountantId),
            new AccountantMethod($method),
            new AccountantAction($action),
            $accountantItemId
                ? new AccountantItemId($accountantItemId)
                : AccountantItemId::generate(),
            $itemId ? new ItemId($itemId) : null,
            $makerName ? new MakerName($makerName) : null,
            new ItemName($itemName),
            $itemCode ? new ItemCode($itemCode) : null,
            $itemStandard ? new ItemStandard($itemStandard) : null,
            $itemJANCode ? new Jancode($itemJANCode) : null,
            $count,
            $unit,
            $price,
            $taxrate
        );
    }

    public function getAccountantItemId()
    {
        return $this->accountantItemId;
    }

    public function __get($field)
    {
        return $this->option[$field];
    }

    public function __set($field, $value)
    {
        return $this->option[$field] = $value;
    }

    public function subTotal()
    {
        $priceInt = round($this->price * 100);
        $countInt = round($this->count * 100);
        $taxRateInt = round($this->taxrate);

        // 小計と税額を計算
        $itemTotalInt = ($priceInt * $countInt) / 100;
        $taxAmountInt = ($itemTotalInt * $taxRateInt) / 100;

        // 小計と税額を加算して、結果を小数に戻して返す
        return ($itemTotalInt + $taxAmountInt) / 100 ?: 0;
    }

    public function toArray()
    {
        $result = [];
        $result['index'] = $this->index;
        $result['accountantId'] = $this->accountantId->value();
        $result['method'] = $this->accountantMethod->value();
        $result['action'] = $this->accountantAction->value();
        $result['accountantItemId'] = $this->accountantItemId->value();
        $result['itemId'] = $this->itemId ? $this->itemId->value() : null;
        $result['makerName'] = $this->makerName
            ? $this->makerName->value()
            : null;
        $result['itemName'] = $this->itemName ? $this->itemName->value() : null;
        $result['itemCode'] = $this->itemCode ? $this->itemCode->value() : null;
        $result['itemStandard'] = $this->itemStandard
            ? $this->itemStandard->value()
            : null;
        $result['itemJANCode'] = $this->itemJANCode
            ? $this->itemJANCode->value()
            : null;
        $result['count'] = (string)$this->count;
        $result['unit'] = $this->unit;
        $result['price'] = (string)$this->price;
        $result['taxrate'] = (string)$this->taxrate;

        foreach ($this->option as $field => $value) {
            if (is_object($value)) {
                $result[$field] = (array) $value;
            } else {
                $result[$field] = $value;
            }
        }

        return $result;
    }
}
