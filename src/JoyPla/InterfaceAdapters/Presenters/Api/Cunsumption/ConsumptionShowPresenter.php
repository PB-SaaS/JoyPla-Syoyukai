<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Consumption {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionShowOutputPortInterface;

    class ConsumptionShowPresenter implements ConsumptionShowOutputPortInterface
    {
        public function output(ConsumptionShowOutputData $outputData)
        {
            $viewModel = new ConsumptionShowViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message))->toJson();
        }
    }
        
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Consumption
     */
    class ConsumptionShowViewModel
    {
        /**
         * Distributor constructor.
         * @param ConsumptionShowOutputData $source
         */
        public function __construct(ConsumptionShowOutputData $source)
        {
            $this->data = $source->consumptions;
            $this->count = $source->count;
            $this->code = 200; 
            $this->message = "success";
        }
    }
}
