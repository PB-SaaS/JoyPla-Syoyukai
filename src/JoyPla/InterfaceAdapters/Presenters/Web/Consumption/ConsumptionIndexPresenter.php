<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Consumption {

    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Consumption\ConsumptionIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Consumption\ConsumptionIndexOutputPortInterface;

    class ConsumptionIndexPresenter implements ConsumptionIndexOutputPortInterface
    {
        public function output(ConsumptionIndexOutputData $outputData)
        {
            $viewModel = new ConsumptionIndexViewModel($outputData);
            $body = View::forge('html/Consumption/ConsumptionIndex', compact('viewModel'), false)->render();
            echo view('html/Common/Template', compact('body'), false)->render();
        }
    }
    
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Consumption
     */
    class ConsumptionIndexViewModel
    {
        /**
         * Distributor constructor.
         * @param ConsumptionIndexOutputData $source
         */
        public function __construct(ConsumptionIndexOutputData $source)
        {
            $this->consumption = $source->consumption;
        }
    }
}