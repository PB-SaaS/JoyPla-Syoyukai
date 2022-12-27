<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestTotalizationOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestTotalizationOutputPortInterface;

    class ItemRequestTotalizationPresenter implements ItemRequestTotalizationOutputPortInterface
    {
        public function output(ItemRequestTotalizationOutputData $outputData)
        {
            $viewModel = new ItemRequestTotalizationViewModel($outputData);
            echo (new ApiResponse($viewModel->data, $viewModel->count, $viewModel->code, $viewModel->message, ['ItemRequestTotalizationPresenter']))->toJson();
        }
    }

    /**
     * Class ItemRequestTotalizationViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest
     */
    class ItemRequestTotalizationViewModel
    {
        /**
         * ItemRequestTotalizationViewModel constructor.
         * @param ItemRequestTotalizationOutputData $source
         */
        public function __construct(ItemRequestTotalizationOutputData $source)
        {
            $this->data = $source->itemRequests;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = "success";
        }
    }
}
