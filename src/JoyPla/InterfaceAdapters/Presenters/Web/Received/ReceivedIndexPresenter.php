<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Received {
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedIndexOutputPortInterface;

    class ReceivedIndexPresenter implements ReceivedIndexOutputPortInterface
    {
        public function output(ReceivedIndexOutputData $outputData)
        {
            $viewModel = new ReceivedIndexViewModel($outputData);
            $body = View::forge(
                'html/Received/Index',
                compact('viewModel'),
                false
            )->render();
            echo view('html/Common/Template', compact('body'), false)->render();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Received
     */
    class ReceivedIndexViewModel
    {
        public array $received;
        public function __construct(ReceivedIndexOutputData $source)
        {
            $this->received = $source->received;
        }
    }
}
