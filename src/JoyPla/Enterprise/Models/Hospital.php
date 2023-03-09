<?php

namespace JoyPla\Enterprise\Models;

use Collection;
class Hospital
{
    private HospitalId $hospitalId;
    private HospitalName $hospitalName;

    public function __construct(
        HospitalId $hospitalId,
        HospitalName $hospitalName,
        string $postalCode,
        string $phoneNumber,
        Pref $prefectures,
        string $address
    ) {
        $this->hospitalId = $hospitalId;
        $this->hospitalName = $hospitalName;
        $this->postalCode = $postalCode;
        $this->phoneNumber = $phoneNumber;
        $this->prefectures = $prefectures;
        $this->address = $address;
    }

    public static function create(Collection $i)
    {
        return new Hospital(
            new HospitalId($i->hospitalId),
            new HospitalName($i->hospitalName),
            $i->postalCode,
            $i->phoneNumber,
            new Pref($i->prefectures),
            $i->address
        );
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getHospitalName()
    {
        return $this->hospitalName;
    }

    public function toArray()
    {
        return [
            'hospitalId' => $this->hospitalId->value(),
            'hospitalName' => $this->hospitalName->value(),
            'postalCode' => $this->postalCode,
            'phoneNumber' => $this->phoneNumber,
            'prefectures' => $this->prefectures->value(),
            'address' => $this->address,
        ];
    }
}
