<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestDeleteOutputPortInterface;

    class ItemRequestDeletePresenter implements
        ItemRequestDeleteOutputPortInterface
    {
        public function output(ItemRequestDeleteOutputData $outputData)
        {
            $viewModel = new ItemRequestDeleteViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ItemRequestDelete']
            ))->toJson();
        }
    }

    /**
     * Class ItemRequestDelete
     * @package JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest
     */
    class ItemRequestDeleteViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(ItemRequestDeleteOutputData $source)
        {
            $this->data = [];
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
