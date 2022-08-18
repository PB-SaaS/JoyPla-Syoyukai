<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Consumption {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionDeleteOutputPortInterface;

    class ConsumptionDeletePresenter implements ConsumptionDeleteOutputPortInterface
    {
        public function output(ConsumptionDeleteOutputData $outputData)
        {
            $viewModel = new ConsumptionDeleteViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message))->toJson();
        }
    }
    
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Hospital\Api
     */
    class ConsumptionDeleteViewModel
    {
        /**
         * Distributor constructor.
         * @param ConsumptionDeleteOutputData $source
         */
        public function __construct(ConsumptionDeleteOutputData $source)
        {
            $this->data = [];
            $this->count = 1;
            $this->code = 200; 
            $this->message = "success";
        }
    }
}
