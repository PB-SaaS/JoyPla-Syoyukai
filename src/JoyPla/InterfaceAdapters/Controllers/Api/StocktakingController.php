<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use ApiResponse;
use App\SpiralDb\InHospitalItemView;
use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Enterprise\Models\InHospitalItem;

class StocktakingController extends Controller
{
    //後できれいにしましょう
    public function inHospitalItem($vars)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $user = $this->request->user();

        if(Gate::denies('register_of_stocktaking_slips') )
        {
            Router::abort(403);
        }

        $instance = InHospitalItemView::where('hospitalId', $user->hospitalId)->value('inHospitalItemId')->value('lotManagement')->get();

        echo ( new ApiResponse($instance->data->all() , $instance->count , 200 , "" , ['StocktakingController@inHospitalItem']) )->toJson();
    }
}
 