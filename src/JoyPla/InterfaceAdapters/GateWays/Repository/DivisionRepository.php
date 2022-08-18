<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Division as SpiralDbDivision;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;

class DivisionRepository implements DivisionRepositoryInterface{

    public function findByHospitalId( HospitalId $hospitalId)
    {
        $division = (SpiralDbDivision::where('hospitalId',$hospitalId->value())->get())->data->all();

        $result = [];
        foreach($division as $d)
        {
            $result[] = Division::create($d);
        }

        return $result;
    }

    public function find( HospitalId $hospitalId , DivisionId $divisionId)
    {
        $division = (SpiralDbDivision::where('hospitalId',$hospitalId->value())->where('divisionId',$divisionId->value())->get())->data->get(0);

        return Division::create($division);
    } 

    public function getStorehouse( HospitalId $hospitalId )
    {
        $division = (SpiralDbDivision::where('hospitalId',$hospitalId->value())->where('divisionType' , '1')->get())->data->get(0);
        return Division::create($division);
    }
}

interface DivisionRepositoryInterface 
{
    public function findByHospitalId( HospitalId $hospitalId );
    public function find( HospitalId $hospitalId , DivisionId $divisionId);
    public function getStorehouse( HospitalId $hospitalId );
}