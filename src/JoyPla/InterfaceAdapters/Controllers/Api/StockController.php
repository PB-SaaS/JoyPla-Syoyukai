<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use ApiResponse;
use framework\Http\Controller;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Service\Repository\RepositoryProvider;

class StockController extends Controller
{
    public function stock($var)
    {
        $repository = new RepositoryProvider();

        $hospitalId = new HospitalId($this->request->user()->hospitalId);
        $divisionId = new DivisionId($var['divisionId']);

        $inHospitalItemIds = [ new InHospitalItemId($var['inHospitalItemId'])];

        $data = $repository->getStockRepository()->getInHospitalItemIdsAndDivisionId($hospitalId, $divisionId, $inHospitalItemIds);

        echo (new ApiResponse($data->first(), 1 , 200 , '' , []))->toJson();
    }
}
 