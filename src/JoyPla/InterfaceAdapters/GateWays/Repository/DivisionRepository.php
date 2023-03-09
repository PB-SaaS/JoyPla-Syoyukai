<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class DivisionRepository implements DivisionRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId)
    {
        $division = ModelRepository::getDivisionInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get();

        $result = [];
        foreach ($division as $d) {
            $result[] = Division::create($d);
        }

        return $result;
    }

    public function find(HospitalId $hospitalId, DivisionId $divisionId)
    {
        $division = ModelRepository::getDivisionInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('divisionId', $divisionId->value())
            ->get();

        if ($division->count() === 0) {
            return null;
        }

        return Division::create($division->first());
    }

    public function getStorehouse(HospitalId $hospitalId)
    {
        $division = ModelRepository::getDivisionInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('divisionType', '1')
            ->get();
        return Division::create($division->first());
    }
}

interface DivisionRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId);
    public function find(HospitalId $hospitalId, DivisionId $divisionId);
    public function getStorehouse(HospitalId $hospitalId);
}
