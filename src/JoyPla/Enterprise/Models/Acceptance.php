<?php

namespace JoyPla\Enterprise\Models;

class Acceptance
{
    private ?DateYearMonthDay $accountantDate;
    private AccountantId $accountantId;
    private HospitalId $hospitalId;
    private ?DivisionId $divisionId;
    private ?DistributorId $distributorId;
    private ?string $orderId;
    private ?string $receivedId;
    private array $items = [];
    private array $option = [];

    public function __construct(
        AccountantId $accountantId,
        ?DateYearMonthDay $accountantDate = null,
        HospitalId $hospitalId,
        ?DivisionId $divisionId = null,
        ?DistributorId $distributorId = null,
        ?string $orderId = null,
        ?string $receivedId = null
    ) {
        $this->accountantId = $accountantId;
        $this->accountantDate = $accountantDate;
        $this->hospitalId = $hospitalId;
        $this->divisionId = $divisionId;
        $this->distributorId = $distributorId;
        $this->orderId = $orderId;
        $this->receivedId = $receivedId;
    }
}
