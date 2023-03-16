<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\InHospitalItem {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemIndexOutputData;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemIndexOutputPortInterface;

    class InHospitalItemIndexPresenter implements
        InHospitalItemIndexOutputPortInterface
    {
        public function output(InHospitalItemIndexOutputData $outputData)
        {
            $viewModel = new InHospitalItemIndexViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['InHospitalItemIndexPresenter']
            ))->toJson();
        }
    }

    /**
     * Class InHospitalItemIndexViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\InHospitalItem
     */
    class InHospitalItemIndexViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';
        /**
         * InHospitalItemIndexViewModel constructor.
         * @param InHospitalItemIndexOutputData $source
         */
        public function __construct(InHospitalItemIndexOutputData $source)
        {
            $this->data = $source->InHospitalItems;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
