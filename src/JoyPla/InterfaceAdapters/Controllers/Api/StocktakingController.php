<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use ApiResponse;
use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class StocktakingController extends Controller
{
    //後できれいにしましょう
    public function inHospitalItem($vars)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $user = $this->request->user();

        if (Gate::denies('register_of_stocktaking_slips')) {
            Router::abort(403);
        }

        $instance = ModelRepository::getInHospitalItemViewInstance()
            ->where('hospitalId', $user->hospitalId)
            ->value('inHospitalItemId')
            ->value('lotManagement')
            ->get();

        echo (new ApiResponse($instance->all(), $instance->count(), 200, '', [
            'StocktakingController@inHospitalItem',
        ]))->toJson();
    }
}
