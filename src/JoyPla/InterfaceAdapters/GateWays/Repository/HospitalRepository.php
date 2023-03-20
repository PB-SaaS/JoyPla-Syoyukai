<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\Hospital;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class HospitalRepository implements HospitalRepositoryInterface
{
    public function find(HospitalId $hospitalId)
    {
        $hospital = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();

        $hospital = Hospital::create($hospital);

        return $hospital;
    }

    public function index(HospitalId $hospitalId)
    {
        $hospital = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();

        return $hospital;
    }

    public function findRow(HospitalId $hospitalId)
    {
        $hospital = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();

        return $hospital;
    }
}

interface HospitalRepositoryInterface
{
    public function find(HospitalId $hospitalId);
    public function index(HospitalId $hospitalId);
}
