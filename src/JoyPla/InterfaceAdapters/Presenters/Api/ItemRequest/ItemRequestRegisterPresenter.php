<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestRegisterOutputPortInterface;

    class ItemRequestRegisterPresenter implements ItemRequestRegisterOutputPortInterface
    {
        public function output(ItemRequestRegisterOutputData $outputData)
        {
            $viewModel = new ItemRequestRegisterViewModel($outputData);
            echo (new ApiResponse($viewModel->data, $viewModel->count, $viewModel->code, $viewModel->message, ['ItemRequestRegisterPresenter']))->toJson();
        }
    }

    /**
     * Class ItemRequest
     * @package JoyPla\InterfaceAdapters\Presenters\ItemRequest\Api
     */
    class ItemRequestRegisterViewModel
    {
        /**
         * ItemRequest constructor.
         * @param ItemRequestRegisterOutputData $source
         */
        public function __construct(ItemRequestRegisterOutputData $source)
        {
            $this->data = $source->ids;
            $this->count = count($source->ids);
            $this->code = 200;
            $this->message = "success";
        }
    }
}
