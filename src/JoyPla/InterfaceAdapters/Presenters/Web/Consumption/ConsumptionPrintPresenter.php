<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Consumption {
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Consumption\ConsumptionShowOutputData;
    use JoyPla\Application\OutputPorts\Web\Consumption\ConsumptionShowOutputPortInterface;

    class ConsumptionPrintPresenter implements
        ConsumptionShowOutputPortInterface
    {
        public function output(ConsumptionShowOutputData $outputData)
        {
            $viewModel = new ConsumptionPrintViewModel($outputData);
            $consumption = $viewModel->consumption;
            $consumptionItems = $viewModel->consumptionItems;

            $body = View::forge(
                'printLayout/Consumption/ConsumptionSlip',
                compact('consumption', 'consumptionItems'),
                false
            )->render();

            echo view(
                'printLayout/Common/Template',
                compact('body'),
                false
            )->render();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Consumption
     */
    class ConsumptionPrintViewModel
    {
        public array $consumption;
        public array $consumptionItems;
        /**
         * Distributor constructor.
         * @param ConsumptionShowOutputData $source
         */
        public function __construct(ConsumptionShowOutputData $source)
        {
            $this->consumption = $source->consumption;
            $this->consumptionItems = [[]];
            $x = 0;
            $count = 0;
            foreach (
                $source->consumption['consumptionItems']
                as $key => $item
            ) {
                $count++;
                $item['id'] = $key + 1;
                $this->consumptionItems[$x][] = $item;
                if (
                    ($count % 25 === 0 && $x === 0) ||
                    ($count % 40 === 0 && $x > 0)
                ) {
                    $count = 0;
                    $x++;
                }
            }
        }
    }
}
