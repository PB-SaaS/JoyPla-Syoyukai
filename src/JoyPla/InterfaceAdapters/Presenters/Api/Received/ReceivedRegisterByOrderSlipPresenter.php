<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Received {

    use ApiResponse;
    use App\SpiralDb\HospitalUser;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterByOrderSlipOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterByOrderSlipOutputPortInterface;

    class ReceivedRegisterByOrderSlipPresenter implements ReceivedRegisterByOrderSlipOutputPortInterface
    {
        public function output(ReceivedRegisterByOrderSlipOutputData $outputData)
        {
            
            $viewModel = new ReceivedRegisterByOrderSlipViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message, ['ReceivedRegisterByOrderSlipPresenter']))->toJson();
        }
    }
    
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Received
     */
    class ReceivedRegisterByOrderSlipViewModel
    {
        /**
         * Distributor constructor.
         * @param ReceivedRegisterByOrderSlipOutputData $source
         */
        public function __construct(ReceivedRegisterByOrderSlipOutputData $source)
        {
            $this->data = $source->received;
            $this->count = count($source->received);
            $this->code = 200; 
            $this->message = "success";
        }
    }
}