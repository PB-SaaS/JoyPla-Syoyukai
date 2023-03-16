<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Division {
    use ApiResponse;
    use JoyPla\Application\OutputPorts\Api\Division\DivisionIndexOutputData;
    use JoyPla\Application\OutputPorts\Api\Division\DivisionIndexOutputPortInterface;

    class DivisionIndexPresenter implements DivisionIndexOutputPortInterface
    {
        public function output(DivisionIndexOutputData $outputData)
        {
            $viewModel = new DivisionIndexViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['DivisionIndexPresenter']
            ))->toJson();
        }
    }

    /**
     * Class DivisionIndexViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Division
     */
    class DivisionIndexViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';
        /**
         * DivisionIndexViewModel constructor.
         * @param DivisionIndexOutputData $source
         */
        public function __construct(DivisionIndexOutputData $source)
        {
            $this->data = $source->divisions;
            $this->count = count($source->divisions);
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
