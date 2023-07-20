<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\RequestItemDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\RequestItemDeleteOutputPortInterface;

    class RequestItemDeletePresenter implements
        RequestItemDeleteOutputPortInterface
    {
        public function output(RequestItemDeleteOutputData $outputData)
        {
            $viewModel = new RequestItemDeleteViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['RequestItemDeletePresenter']
            ))->toJson();
        }
    }

    /**
     * Class RequestItemDeleteViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest
     */
    class RequestItemDeleteViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(RequestItemDeleteOutputData $source)
        {
            $this->data = $source->data;
            $this->count = 0;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
