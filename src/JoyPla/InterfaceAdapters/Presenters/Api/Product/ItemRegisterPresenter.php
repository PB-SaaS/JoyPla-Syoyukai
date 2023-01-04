<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Item {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Item\ItemRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Item\ItemRegisterOutputPortInterface;

    class ItemRegisterPresenter implements ItemRegisterOutputPortInterface
    {
        public function output(ItemRegisterOutputData $outputData)
        {
            $viewModel = new ItemRegisterViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message, ['ItemRegisterPresenter']))->toJson();
        }
    }
        
    /**
     * Class ItemRegisterViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Item
     */
    class ItemRegisterViewModel
    {
        /**
         * ItemRegisterViewModel constructor.
         * @param ItemRegisterOutputData $source
         */
        public function __construct(ItemRegisterOutputData $source)
        {
            $this->data = $source->Items;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = "success";
        }
    }
}
