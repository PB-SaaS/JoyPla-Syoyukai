<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use ApiResponse;
use framework\Facades\Gate;
use framework\Http\Controller;
use JoyPla\Service\Repository\RepositoryProvider;
use stdClass;

class ProductController extends Controller
{
    public function items($vars)
    {
        $search = $this->request->get('search',[]);
        $user = $this->request->user();
        if (Gate::allows('is_user')) {
            $search['divisionIds'] = [$user->divisionId];
        }

        $searchObject = new stdClass();
        $searchObject->itemName = $search['itemName'];
        $searchObject->makerName = $search['makerName'];
        $searchObject->itemCode = $search['itemCode'];
        $searchObject->itemStandard = $search['itemStandard'];
        $searchObject->itemJANCode = $search['itemJANCode'];
        $searchObject->distributorIds = $search['distributorIds'];
        $searchObject->divisionIds = $search['divisionIds'];
        $searchObject->perPage = $search['perPage'];
        $searchObject->currentPage = $search['currentPage'];

        [ $items , $count ]  = (new RepositoryProvider())->getStockRepository()->search(
            $this->request->user(),
            $searchObject
        );

        echo (new ApiResponse($items , $count , 0 , '' ))->toJson();
    }
}
