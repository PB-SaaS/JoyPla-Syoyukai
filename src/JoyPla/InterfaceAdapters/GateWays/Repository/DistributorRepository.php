<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class DistributorRepository implements DistributorRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId)
    {
        $distributor = ModelRepository::getDistributorInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->all();

        $result = [];
        foreach ($distributor as $d) {
            $result[] = $d;
        }

        return $result;
    }
}

interface DistributorRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId);
}
