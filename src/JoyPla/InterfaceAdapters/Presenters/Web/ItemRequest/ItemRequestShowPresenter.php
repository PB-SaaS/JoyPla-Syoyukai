<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\ItemRequest {
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\ItemRequest\ItemRequestShowOutputData;
    use JoyPla\Application\OutputPorts\Web\ItemRequest\ItemRequestShowOutputPortInterface;

    class ItemRequestShowPresenter implements ItemRequestShowOutputPortInterface
    {
        public function output(ItemRequestShowOutputData $outputData)
        {
            $viewModel = new ItemRequestShowViewModel($outputData);
            $body = View::forge(
                'html/ItemRequest/Show',
                compact('viewModel'),
                false
            )->render();
            echo view('html/Common/Template', compact('body'), false)->render();
        }
    }

    /**
     * Class ItemRequest
     * @package JoyPla\InterfaceAdapters\Presenters\Web\ItemRequest
     */
    class ItemRequestShowViewModel
    {
        public array $itemRequest;

        /**
         * ItemRequest constructor.
         * @param ItemRequestShowOutputData $source
         */
        public function __construct(ItemRequestShowOutputData $source)
        {
            $this->itemRequest = $source->itemRequest;
        }
    }
}
