<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemShowInputData;
use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemShowInputPortInterface;

class PriceAndInHospitalItemController extends Controller
{
    public function register($vars , PriceAndInHospitalItemRegisterInputPortInterface $inputPort ) 
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        if(Gate::denies('register_of_price_and_inHospitalItem'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_price_and_inHospitalItem');

        $price_and_inHospitalItemItem = $this->request->get('price_and_inHospitalItemItem');

        $user = $this->request->user();

        $inputData = new PriceAndInHospitalItemRegisterInputData(
            $user,
            $price_and_inHospitalItemDate,
            $price_and_inHospitalItemItem, 
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }
}

