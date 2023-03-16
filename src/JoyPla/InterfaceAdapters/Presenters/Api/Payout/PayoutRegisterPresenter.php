<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Payout {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Payout\PayoutRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Payout\PayoutRegisterOutputPortInterface;

    class PayoutRegisterPresenter implements PayoutRegisterOutputPortInterface
    {
        public function output(PayoutRegisterOutputData $outputData)
        {
            $viewModel = new PayoutRegisterViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['PayoutRegisterPresenter']
            ))->toJson();
        }
    }

    /**
     * Class PayoutRegisterViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Payout\Api
     */
    class PayoutRegisterViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(PayoutRegisterOutputData $source)
        {
            $this->data = $source->ids;
            $this->count = count($source->ids);
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
