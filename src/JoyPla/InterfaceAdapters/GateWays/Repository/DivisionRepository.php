<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Division as SpiralDbDivision;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\HospitalId;

class DivisionRepository implements DivisionRepositoryInterface{

    public function findByHospitalId( HospitalId $hospitalId )
    {
        $division = (SpiralDbDivision::where('hospitalId',$hospitalId->value())->get())->data->all();

        $result = [];
        foreach($division as $d)
        {
            $result[] = Division::create($d);
        }

        return $result;
    }
}

interface DivisionRepositoryInterface 
{
    public function findByHospitalId( HospitalId $hospitalId );
}