<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;

class DivisionRepository implements DivisionRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId)
    {
        $division = SpiralDB::title('NJ_divisionDB')
            ->where('hospitalId', $hospitalId->value())
            ->get([
                'registrationTime',
                'divisionId',
                'hospitalId',
                'divisionName',
                'divisionType',
                'deleteFlag',
                'authkey',
                'deliveryDestCode',
            ]);

        //$division = (SpiralDbDivision::where('hospitalId',$hospitalId->value())->get())->data->all();

        $result = [];
        foreach ($division as $d) {
            $result[] = Division::create($d);
        }

        return $result;
    }

    public function find(HospitalId $hospitalId, DivisionId $divisionId)
    {
        //$division = (SpiralDbDivision::where('hospitalId',$hospitalId->value())->where('divisionId',$divisionId->value())->get())->data->get(0);

        $division = SpiralDB::title('NJ_divisionDB')
            ->where('hospitalId', $hospitalId->value())
            ->where('divisionId', $divisionId->value())
            ->get([
                'registrationTime',
                'divisionId',
                'hospitalId',
                'divisionName',
                'divisionType',
                'deleteFlag',
                'authkey',
                'deliveryDestCode',
            ]);

        if ($division->count() === 0) {
            return null;
        }

        return Division::create($division->first());
    }

    public function getStorehouse(HospitalId $hospitalId)
    {
        $division = SpiralDB::title('NJ_divisionDB')
            ->where('hospitalId', $hospitalId->value())
            ->where('divisionType', '1')
            ->get([
                'registrationTime',
                'divisionId',
                'hospitalId',
                'divisionName',
                'divisionType',
                'deleteFlag',
                'authkey',
                'deliveryDestCode',
            ]);
        //$division = (SpiralDbDivision::where('hospitalId',$hospitalId->value())->where('divisionType' , '1')->get())->data->get(0);
        return Division::create($division->first());
    }
}

interface DivisionRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId);
    public function find(HospitalId $hospitalId, DivisionId $divisionId);
    public function getStorehouse(HospitalId $hospitalId);
}
