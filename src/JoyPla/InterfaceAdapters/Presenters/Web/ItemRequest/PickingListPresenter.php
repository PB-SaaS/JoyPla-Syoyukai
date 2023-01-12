<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\ItemRequest {

    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\ItemRequest\PickingListOutputData;
    use JoyPla\Application\OutputPorts\Web\ItemRequest\PickingListOutputPortInterface;

    class PickingListPresenter implements PickingListOutputPortInterface
    {
        public function output(PickingListOutputData $outputData)
        {

            $viewModel = new PickingListViewModel($outputData);
            $totalization = $viewModel->totalization;
            $count = $viewModel->count;

            $body = View::forge('printLayout/ItemRequest/PickingList', compact('totalization', 'count'), false)->render();
            echo view('printLayout/Common/Template', compact('body'), false)->render();
        }
    }

    /**
     * Class PickingListViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Web\ItemRequest
     */
    class PickingListViewModel
    {
        /**
         * PickingListOutputData constructor.
         * @param PickingListOutputData $source
         */
        public function __construct(PickingListOutputData $source)
        {
            $this->totalization = $source->totalRequestItems;
            $this->count = $source->count;
        }
    }
}
