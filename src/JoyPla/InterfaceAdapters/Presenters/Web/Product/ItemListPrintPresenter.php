<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Product\ItemList {
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Product\ItemList\ItemListShowOutputData;
    use JoyPla\Application\OutputPorts\Web\Product\ItemList\ItemListShowOutputPortInterface;

    class ItemListPrintPresenter implements ItemListShowOutputPortInterface
    {
        public function output(ItemListShowOutputData $outputData)
        {
            $viewModel = new ItemListPrintViewModel($outputData);
            $itemList = $viewModel->itemList;
            $itemListRows = $viewModel->itemList->items;

            $body = View::forge(
                'printLayout/Product/ItemList',
                compact('itemList', 'itemListRows'),
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
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Order
     */
    class ItemListPrintViewModel
    {
        public array $itemList;
        public function __construct(ItemListShowOutputData $source)
        {
            $this->itemList = $source->itemList;
/* 
            $x = 0;
            $count = 0;
            foreach ($source->itemList->items as $key => $item) {
                $count++;
                $item['id'] = $key + 1;
                $this->itemList[$x][] = $item;
                if (
                    ($count % 9 === 0 && $x === 0) ||
                    ($count % 13 === 0 && $x > 0)
                ) {
                    $count = 0;
                    $x++;
                }
            }
 */
        }
    }
}
