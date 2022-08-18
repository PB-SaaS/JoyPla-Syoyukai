<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Received {

    use ApiResponse;
    use App\SpiralDb\HospitalUser;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterOutputPortInterface;

    class ReceivedRegisterPresenter implements ReceivedRegisterOutputPortInterface
    {
        public function output(ReceivedRegisterOutputData $outputData)
        {
            
            $viewModel = new ReceivedRegisterViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message))->toJson();
        }
    }
    
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Received
     */
    class ReceivedRegisterViewModel
    {
        /**
         * Distributor constructor.
         * @param ReceivedRegisterOutputData $source
         */
        public function __construct(ReceivedRegisterOutputData $source)
        {
            $this->data = $source->receiveds;
            $this->count = count($source->receiveds);
            $this->code = 200; 
            $this->message = "success";
        }
    }
}