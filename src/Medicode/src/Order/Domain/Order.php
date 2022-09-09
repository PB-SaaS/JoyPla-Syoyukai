<?php

declare(strict_types=1);

namespace Medicode\Order\Domain;

class Order
{
    private string $id;
    private string $orderCNumber;
    private string $hospitalCode;
    private string $distributorCode;
    private string $orderNumber;
    private string $JANCode;
    private int $quantity;
    private string $deliveryDestCode;
    private bool $isValid = true;

    /**
     * OrderRecord constructor.
     * @param string $id
     * @param string $orderCNumber
     * @param string $hospitalCode
     * @param string $distributorCode
     * @param string $orderNumber
     * @param string $JANCode
     * @param string $deliveryDestCode
     * @param int $quantity
     */
    public function __construct(
        string $id,
        string $orderCNumber,
        string $orderNumber,
        string $hospitalCode,
        string $distributorCode,
        string $JANCode,
        int $quantity,
        string $deliveryDestCode
    ) {
        $this->id = $id;
        $this->orderCNumber = $orderCNumber;
        $this->orderNumber = $orderNumber;
        $this->hospitalCode = $hospitalCode;
        $this->distributorCode = $distributorCode;
        $this->JANCode = $JANCode;
        $this->quantity = $quantity;
        $this->deliveryDestCode = $deliveryDestCode;
        $this->isValid = $this->checkIsValid();
    }


    private function checkIsValid(): bool
    {
        if (!$this->id || strlen($this->id) > 11) {
            return false;
        }

        if (!$this->hospitalCode || !$this->checkCode($this->hospitalCode, 10)) {
            return false;
        }

        if (!$this->distributorCode || !$this->checkCode($this->distributorCode, 9)) {
            return false;
        }

        if (!$this->JANCode || !$this->checkCode($this->JANCode, 13)) {
            return false;
        }

        if (!$this->quantity || !$this->checkQuntity($this->quantity)) {
            return false;
        }

        if (!$this->checkDeliveryDestCode($this->deliveryDestCode)) {
            return false;
        }

        return true;
    }


    /**
     * @param string $value
     * @param int $digit
     * @return bool
     * 指定桁数の数値かをチェック
     */
    private function checkCode(string $value, int $digit): bool
    {
        return preg_match('/^[0-9]{'.$digit.'}$/', $value) > 0;
    }


    /**
     * @param int $value
     * @return bool
     * 発注数チェック
     */
    private function checkQuntity(int $value): bool
    {
        return $value > 0 && $value < 100000;
    }


    /**
     * @param string $value
     * @return bool
     * 納品場所番号チェック
     */
    private function checkDeliveryDestCode(string $value): bool
    {
        if (empty($value)) {
            return true;
        }

        return $this->checkCode($value, 2);
    }

    /**
     * @return int
     */
    public function getId(): string
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getHospitalCode(): string
    {
        return $this->hospitalCode;
    }


    /**
     * @return string
     */
    public function getDistributorCode(): string
    {
        return $this->distributorCode;
    }


    /**
     * @return string
     */
    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }


    /**
     * @return string
     */
    public function getOrderCNumber(): string
    {
        return $this->orderCNumber;
    }


    /**
     * @return string
     */
    public function getJANCode(): string
    {
        return $this->JANCode;
    }


    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getDeliveryDestCode(): string
    {
        return $this->deliveryDestCode;
    }


    /**
     * @return bool
     */
    public function getIsValid(): bool
    {
        return $this->isValid;
    }
}
