<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Item\ItemAndPriceAndInHospitalItemRegisterInputData;
use JoyPla\Application\InputPorts\Api\Item\ItemAndPriceAndInHospitalItemRegisterInputPortInterface;

class ItemAndPriceAndInHospitalItemController extends Controller
{
    public function register($vars , ItemAndPriceAndInHospitalItemRegisterInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        if(Gate::denies('register_of_item_and_price_and_inHospitalItem'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_item_and_price_and_inHospitalItem');

        $item_and_price_and_inHospitalItemItem = (array)$this->request;

        $user = $this->request->user();

        $inputData = new ItemAndPriceAndInHospitalItemRegisterInputData(
            $user->tenantId,
            $user->hospitalId,
            $item_and_price_and_inHospitalItemItem, 
        );
        $inputPort->handle($inputData);
    }
}

