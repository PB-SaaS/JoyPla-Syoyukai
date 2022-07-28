<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Distributor as SpiralDbDistributor;
use JoyPla\Enterprise\Models\Distributor;
use JoyPla\Enterprise\Models\HospitalId;

class DistributorRepository implements DistributorRepositoryInterface{

    public function findByHospitalId( HospitalId $hospitalId )
    {
        $Distributor = (SpiralDbDistributor::where('hospitalId',$hospitalId->value())->get())->data->all();

        $result = [];
        foreach($Distributor as $d)
        {
            $result[] = $d;
        }

        return $result;
    }
}

interface DistributorRepositoryInterface 
{
    public function findByHospitalId( HospitalId $hospitalId );
}