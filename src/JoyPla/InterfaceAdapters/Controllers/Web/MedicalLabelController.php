<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use Exception;
use framework\Http\Request;
use framework\Http\Controller;
use framework\Http\View;

class MedicalLabelController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function MedicalOrderLabelPrint(array $vars)
    {

    }
    public function MedicalReceivedLabelPrint(array $vars)
    {

    }
    public function MedicalPayoutLabelPrint(array $vars)
    {

    }
}