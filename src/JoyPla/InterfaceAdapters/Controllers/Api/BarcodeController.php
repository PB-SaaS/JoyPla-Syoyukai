<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Barcode\BarcodeSearchInputData;
use JoyPla\Application\InputPorts\Api\Barcode\BarcodeSearchInputPortInterface;
use NGT\Barcode\GS1Decoder\Decoder;

class BarcodeController extends Controller
{
    public function search($vars , BarcodeSearchInputPortInterface $inputPort ) 
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $barcode = $this->request->get('barcode');
        //$barcode = '0195012345678903422616 3103000123';
        //$barcode = '019497103274311310TEst12314 17220199';
        $inputData = new BarcodeSearchInputData((new Auth(HospitalUser::class)),$barcode);
        $inputPort->handle($inputData);
    }
}

