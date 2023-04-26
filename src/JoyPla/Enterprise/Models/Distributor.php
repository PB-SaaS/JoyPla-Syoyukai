<?php

namespace JoyPla\Enterprise\Models;

use Collection;
class Distributor
{
    private HospitalId $hospitalId;
    private DistributorId $distributorId;
    private string $distributorName;
    private ?string $orderMethod = null;

    public function __construct(
        HospitalId $hospitalId,
        DistributorId $distributorId,
        string $distributorName,
        ?string $orderMethod = null
    ) {
        $this->hospitalId = $hospitalId;
        $this->distributorId = $distributorId;
        $this->distributorName = $distributorName;
        $this->orderMethod = $orderMethod;
    }

    public static function create(Collection $i)
    {
        return new Distributor(
            new HospitalId($i->hospitalId),
            new DistributorId($i->distributorId),
            $i->distributorName,
            $i->orderMethod ?? null
        );
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getDistributorId()
    {
        return $this->distributorId;
    }

    public function getDistributorName()
    {
        return $this->distributorName;
    }

    public function getOrderMethod()
    {
        return $this->orderMethod;
    }

    public function getOrderMethodName()
    {
        if ($this->orderMethod == '1') {
            return 'JoyPla';
        }
        if ($this->orderMethod == '2') {
            return 'メール';
        }
        if ($this->orderMethod == '3') {
            return 'FAX';
        }
        if ($this->orderMethod == '4') {
            return '電話';
        }
        if ($this->orderMethod == '5') {
            return '業者システム';
        }
        return 'その他';
    }

    public function toArray()
    {
        return [
            'hospitalId' => $this->hospitalId->value(),
            'distributorId' => $this->distributorId->value(),
            'distributorName' => $this->distributorName,
            'orderMethod' => $this->getOrderMethodName(),
        ];
    }
}
