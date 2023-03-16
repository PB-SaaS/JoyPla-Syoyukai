<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestUpdateOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestUpdateOutputPortInterface;

    class ItemRequestUpdatePresenter implements
        ItemRequestUpdateOutputPortInterface
    {
        public function output(ItemRequestUpdateOutputData $outputData)
        {
            $viewModel = new ItemRequestUpdateViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ItemRequestUpdatePresenter']
            ))->toJson();
        }
    }

    /**
     * Class ItemRequestUpdateViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest
     */
    class ItemRequestUpdateViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(ItemRequestUpdateOutputData $source)
        {
            $this->data = $source->data;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
