<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Consumption {
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Consumption\ConsumptionShowOutputData;
    use JoyPla\Application\OutputPorts\Web\Consumption\ConsumptionShowOutputPortInterface;

    class ConsumptionShowPresenter implements ConsumptionShowOutputPortInterface
    {
        public function output(ConsumptionShowOutputData $outputData)
        {
            $viewModel = new ConsumptionShowViewModel($outputData);
            $body = View::forge(
                'html/Consumption/Show',
                compact('viewModel'),
                false
            )->render();
            echo view('html/Common/Template', compact('body'), false)->render();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Consumption
     */
    class ConsumptionShowViewModel
    {
        public array $consumption;
        /**
         * Distributor constructor.
         * @param ConsumptionShowOutputData $source
         */
        public function __construct(ConsumptionShowOutputData $source)
        {
            $this->consumption = $source->consumption;
        }
    }
}
