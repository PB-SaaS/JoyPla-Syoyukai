<?php

namespace JoyPla\InterfaceAdapters\Presenters\Hospital\Top {

    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Hospital\Top\TopIndexOutputData;
    use JoyPla\Application\OutputPorts\Hospital\Top\TopIndexOutputPortInterface;
    
    class TopIndexPresenter implements TopIndexOutputPortInterface
    {
        public function output(TopIndexOutputData $outputData)
        {
            $viewModel = new TopIndexViewModel($outputData);
    
            //header.phpをテンプレートの$headerとbindさせる。
            $header = View::forge('parts/header-navi')->render();
            //footer.phpをテンプレートの$footerとbindさせる。
            //$footer = View::forge('parts/footer')->render();
            $footer = "";
    
            $content = View::forge('Hospital/Top')->render();
            echo view('Template', compact('content','header','footer'),false)->render();
        }
    }
        
    /**
     * Class TopIndexViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Hospital\Top
     */
    class TopIndexViewModel
    {
        /**
         * TopIndexViewModel constructor.
         * @param TopIndexOutputData $source
         */
        public function __construct(TopIndexOutputData $source)
        {
        }
    }
}
