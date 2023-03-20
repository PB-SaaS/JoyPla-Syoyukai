<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\Barcode\BarcodeOrderSearchInputData;
use JoyPla\Application\InputPorts\Api\Barcode\BarcodeOrderSearchInputPortInterface;
use JoyPla\Application\InputPorts\Api\Barcode\BarcodeSearchInputData;
use JoyPla\Application\InputPorts\Api\Barcode\BarcodeSearchInputPortInterface;

class BarcodeController extends Controller
{
    public function search($vars, BarcodeSearchInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $barcode = $this->request->get('barcode');
        //$barcode = '0195012345678903422616 3103000123';
        //$barcode = '019497103274311310TEst12314 17220199';
        $inputData = new BarcodeSearchInputData(
            $this->request->user(),
            $barcode
        );
        $inputPort->handle($inputData);
    }

    public function orderSearch(
        $vars,
        BarcodeOrderSearchInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $barcode = $this->request->get('barcode');

        if (Gate::denies('receipt')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('receipt');

        $inputData = new BarcodeOrderSearchInputData(
            $this->request->user(),
            $barcode,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }
}
