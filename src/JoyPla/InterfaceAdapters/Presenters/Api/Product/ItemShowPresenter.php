<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Item {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Item\ItemShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Item\ItemShowOutputPortInterface;

    class ItemShowPresenter implements ItemShowOutputPortInterface
    {
        public function output(ItemShowOutputData $outputData)
        {
            $viewModel = new ItemShowViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message, ['ItemShowPresenter']))->toJson();
        }
    }
        
    /**
     * Class ItemShowViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Item
     */
    class ItemShowViewModel
    {
        /**
         * ItemShowViewModel constructor.
         * @param ItemShowOutputData $source
         */
        public function __construct(ItemShowOutputData $source)
        {
            $this->data = $source->Items;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = "success";
        }
    }
}
