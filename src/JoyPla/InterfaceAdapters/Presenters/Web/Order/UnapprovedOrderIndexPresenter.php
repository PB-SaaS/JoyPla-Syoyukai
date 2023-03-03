<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Order {
    use App\SpiralDb\HospitalUser;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Order\OrderIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Order\OrderIndexOutputPortInterface;

    class UnapprovedOrderIndexPresenter implements OrderIndexOutputPortInterface
    {
        public function output(OrderIndexOutputData $outputData)
        {
            $viewModel = new UnapprovedOrderIndexViewModel($outputData);
            $body = View::forge(
                'html/Order/UnapprovedIndex',
                compact('viewModel'),
                false
            )->render();
            echo view('html/Common/Template', compact('body'), false)->render();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Order
     */
    class UnapprovedOrderIndexViewModel
    {
        public array $order;
        public function __construct(OrderIndexOutputData $source)
        {
            $this->order = $source->order;
        }
    }
}
