<?php

namespace JoyPla\Enterprise\Models;

class Accountant
{
    private DateYearMonthDay $accountantDate;
    private AccountantId $accountantId;
    private HospitalId $hospitalId;
    private ?DivisionId $divisionId;
    private ?DistributorId $distributorId;
    private ?string $orderId;
    private ?string $receivedId;

    public function __construct(
        DateYearMonthDay $accountantDate,
        AccountantId $accountantId,
        HospitalId $hospitalId,
        ?DivisionId $divisionId = null,
        ?DistributorId $distributorId = null,
        ?string $orderId = '',
        ?string $receivedId = ''
    ) {
        $this->accountantDate = $accountantDate;
        $this->accountantId = $accountantId;
        $this->hospitalId = $hospitalId;
        $this->divisionId = $divisionId;
        $this->distributorId = $distributorId;
        $this->orderId = $orderId;
        $this->receivedId = $receivedId;
    }

    public static function init(
        string $accountantDate,
        string $hospitalId,
        ?string $divisionId = null,
        ?string $distributorId = null,
        ?string $orderId = null,
        ?string $receivedId = null
    ) {
        return new self(
            new DateYearMonthDay($accountantDate),
            AccountantId::generate(),
            new HospitalId($hospitalId),
            $divisionId ? new DivisionId($divisionId) : null,
            $distributorId ? new DistributorId($distributorId) : null,
            $orderId,
            $receivedId
        );
    }

    public function getAccountantId()
    {
        return $this->accountantId;
    }

    public function toArray()
    {
        return [
            'accountantDate' => $this->accountantDate->value(),
            'accountantId' => $this->accountantId->value(),
            'hospitalId' => $this->hospitalId->value(),
            'divisionId' => $this->divisionId ? $this->divisionId->value() : '',
            'distributorId' => $this->distributorId
                ? $this->distributorId->value()
                : '',
            'orderId' => $this->orderId ?? '',
            'receivedId' => $this->receivedId ?? '',
        ];
    }
}
