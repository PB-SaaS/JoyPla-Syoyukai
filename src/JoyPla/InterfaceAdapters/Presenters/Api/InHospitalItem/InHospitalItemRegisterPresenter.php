<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\InHospitalItem {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemRegisterOutputPortInterface;

    class InHospitalItemRegisterPresenter implements InHospitalItemRegisterOutputPortInterface
    {
        public function output(InHospitalItemRegisterOutputData $outputData)
        {
            $viewModel = new InHospitalItemRegisterViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message, ['InHospitalItemRegisterPresenter']))->toJson();
        }
    }
        
    /**
     * Class InHospitalItemRegisterViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\InHospitalItem
     */
    class InHospitalItemRegisterViewModel
    {
        /**
         * InHospitalItemRegisterViewModel constructor.
         * @param InHospitalItemRegisterOutputData $source
         */
        public function __construct(InHospitalItemRegisterOutputData $source)
        {
            $this->data = $source->InHospitalItems;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = "success";
        }
    }
}
