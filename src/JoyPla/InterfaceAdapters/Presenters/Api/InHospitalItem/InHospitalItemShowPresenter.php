<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\InHospitalItem {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemShowOutputData;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemShowOutputPortInterface;

    class InHospitalItemShowPresenter implements InHospitalItemShowOutputPortInterface
    {
        public function output(InHospitalItemShowOutputData $outputData)
        {
            $viewModel = new InHospitalItemShowViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message, ['InHospitalItemShowPresenter']))->toJson();
        }
    }
        
    /**
     * Class InHospitalItemShowViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\InHospitalItem
     */
    class InHospitalItemShowViewModel
    {
        /**
         * InHospitalItemShowViewModel constructor.
         * @param InHospitalItemShowOutputData $source
         */
        public function __construct(InHospitalItemShowOutputData $source)
        {
            $this->data = $source->InHospitalItems;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = "success";
        }
    }
}
