<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Received {
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedLabelOutputData;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedLabelOutputPortInterface;

    class ReceivedLabelPresenter implements ReceivedLabelOutputPortInterface
    {
        public function output(ReceivedLabelOutputData $outputData)
        {
            $viewModel = new ReceivedLabelViewModel($outputData);
            $body = View::forge(
                'labelPrint/Received/Label',
                compact('viewModel'),
                false
            )->render();
            echo view(
                'labelPrint/Common/Template',
                compact('body'),
                false
            )->render();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Received
     */
    class ReceivedLabelViewModel
    {
        public array $print;
        public function __construct(ReceivedLabelOutputData $source)
        {
            $this->print = $source->print;
        }
    }
}
