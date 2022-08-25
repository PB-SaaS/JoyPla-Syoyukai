<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {

    use ApiResponse;
    use App\SpiralDb\HospitalUser;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\FixedQuantityOrderOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\FixedQuantityOrderOutputPortInterface;

    class FixedQuantityOrderPresenter implements FixedQuantityOrderOutputPortInterface
    {
        public function output(FixedQuantityOrderOutputData $outputData)
        {
            
            $viewModel = new FixedQuantityOrderViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message, ['FixedQuantityOrderPresenter']))->toJson();
        }
    }
    
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class FixedQuantityOrderViewModel
    {
        /**
         * Distributor constructor.
         * @param FixedQuantityOrderOutputData $source
         */
        public function __construct(FixedQuantityOrderOutputData $source)
        {
            $this->data = $source->stocks;
            $this->count = $source->count;
            $this->code = 200; 
            $this->message = "success";
        }
    }
}