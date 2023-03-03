<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Received {
    use App\SpiralDb\HospitalUser;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedIndexOutputPortInterface;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedLabelSettingOutputData;

    class ReceivedLabelSettingPresenter implements
        ReceivedIndexOutputPortInterface
    {
        public function output(ReceivedIndexOutputData $outputData)
        {
            $viewModel = new ReceivedLabelSettingViewModel($outputData);
            $body = View::forge(
                'html/Received/LabelSetting',
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
    class ReceivedLabelSettingViewModel
    {
        public array $received;
        public function __construct(ReceivedIndexOutputData $source)
        {
            $this->received = $source->received;
        }
    }
}
