<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestHistoryOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestHistoryOutputPortInterface;

    class ItemRequestHistoryPresenter implements ItemRequestHistoryOutputPortInterface
    {
        public function output(ItemRequestHistoryOutputData $outputData)
        {
            $viewModel = new ItemRequestHistoryViewModel($outputData);
            echo (new ApiResponse($viewModel->data, $viewModel->count, $viewModel->code, $viewModel->message, ['ItemRequestHistoryPresenter']))->toJson();
        }
    }

    /**
     * Class ItemRequest
     * @package JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest
     */
    class ItemRequestHistoryViewModel
    {
        /**
         * ItemRequest constructor.
         * @param ItemRequestHistoryOutputData $source
         */
        public function __construct(ItemRequestHistoryOutputData $source)
        {
            $this->data = $source->ItemRequests;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = "success";
        }
    }
}
