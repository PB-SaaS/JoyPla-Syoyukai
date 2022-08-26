<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Division {

    use ApiResponse; 
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Division\DivisionShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Division\DivisionShowOutputPortInterface;

    class DivisionShowPresenter implements DivisionShowOutputPortInterface
    {
        public function output(DivisionShowOutputData $outputData)
        {
            $viewModel = new DivisionShowViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message, ['DivisionShowPresenter']))->toJson();
        }
    }
        
    /**
     * Class DivisionShowViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Division
     */
    class DivisionShowViewModel
    {
        /**
         * DivisionShowViewModel constructor.
         * @param DivisionShowOutputData $source
         */
        public function __construct(DivisionShowOutputData $source)
        {
            $this->data = $source->divisions;
            $this->count = count($source->divisions);
            $this->code = 200;
            $this->message = "success";
        }
    }
}
