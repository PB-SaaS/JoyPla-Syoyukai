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
    private bool $isValid = true;
    
    /**
     * OrderRecord constructor.
     * @param string $id
     * @param string $orderCNumber
     * @param string $hospitalCode
     * @param string $distributorCode
     * @param string $orderNumber
     * @param string $JANCode
     * @param int $quantity
     */
    public function __construct(
        string $id,
        string $orderCNumber,
        string $orderNumber,
        string $hospitalCode,
        string $distributorCode,
        string $JANCode,
        int $quantity
    ) {
        $this->id = $id;
        $this->orderCNumber = $orderCNumber;
        $this->orderNumber = $orderNumber;
        $this->hospitalCode = $hospitalCode;
        $this->distributorCode = $distributorCode;
        $this->JANCode = $JANCode;
        $this->quantity = $quantity;
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
     * @return string
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
    
    
    /**
     * @return bool
     */
    public function getIsValid(): bool
    {
        return $this->isValid;
    }
}
