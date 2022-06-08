<?php

namespace JoyPla\InterfaceAdapters\Presenters\Hospital\Top {

    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Hospital\Top\TopOrderPageOutputData;
    use JoyPla\Application\OutputPorts\Hospital\Top\TopOrderPageOutputPortInterface;
    
    class TopOrderPagePresenter implements TopOrderPageOutputPortInterface
    {
        public function output(TopOrderPageOutputData $outputData)
        {
            $viewModel = new TopOrderPageViewModel($outputData);
    
            //header.phpをテンプレートの$headerとbindさせる。
            $header = View::forge('parts/header-navi')->render();
            //footer.phpをテンプレートの$footerとbindさせる。
            //$footer = View::forge('parts/footer')->render();
            $footer = "";
    
            $content = View::forge('Hospital/OrderPage')->render();
            echo view('Template', compact('content','header','footer'),false)->render();
        }
    }
        
    /**
     * Class TopOrderPageViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Hospital\Top
     */
    class TopOrderPageViewModel
    {
        /**
         * TopOrderPageViewModel constructor.
         * @param TopOrderPageOutputData $source
         */
        public function __construct(TopOrderPageOutputData $source)
        {
        }
    }
}
