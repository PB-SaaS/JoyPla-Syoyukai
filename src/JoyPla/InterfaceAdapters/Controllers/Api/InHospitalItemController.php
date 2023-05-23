<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemIndexInputData;
use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemIndexInputPortInterface;
use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemShowInputData;
use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemShowInputPortInterface;

class InHospitalItemController extends Controller
{
    public function index(
        $vars,
        InHospitalItemIndexInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $inputData = new InHospitalItemIndexInputData(
            $this->request->user()->hospitalId,
            $this->request->get('search'),
            $this->request->get('divisionId')
        );
        $inputPort->handle($inputData);
    }

    public function show(
        $vars,
        InHospitalItemShowInputPortInterface $inputPort
    ) {
        global $SPIRAL;
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $inputData = new InHospitalItemShowInputData(
            $this->request->user()->hospitalId,
            $this->request->get('search')
        );
        $inputPort->handle($inputData);
    }

}
