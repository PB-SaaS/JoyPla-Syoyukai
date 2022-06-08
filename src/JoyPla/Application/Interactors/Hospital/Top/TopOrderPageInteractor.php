<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Hospital\Top {
    use JoyPla\Application\InputPorts\Hospital\Top\TopOrderPageInputPortInterface;
    use JoyPla\Application\InputPorts\Hospital\Top\TopOrderPageInputData;
    use JoyPla\Application\OutputPorts\Hospital\Top\TopOrderPageOutputData;
    use JoyPla\Application\OutputPorts\Hospital\Top\TopOrderPageOutputPortInterface;

    /**
     * Class TopOrderPageInteractor
     * @package JoyPla\Application\Interactors\Hospital\Top
     */
    class TopOrderPageInteractor implements TopOrderPageInputPortInterface
    {
        /** @var TopOrderPageOutputPortInterface */
        private $outputPort;

        /**
         * TopOrderPageInteractor constructor.
         * @param TopOrderPageOutputPortInterface $outputPort
         */
        public function __construct(TopOrderPageOutputPortInterface $outputPort)
        {
            $this->outputPort = $outputPort;
        }

        /**
         * @param TopOrderPageInputData $inputData
         */
        public function handle(TopOrderPageInputData $inputData)
        {
            $this->outputPort->output(new TopOrderPageOutputData());
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Hospital\Top {
    /**
     * Class TopOrderPageInputData
     * @package JoyPla\Application\InputPorts\Hospital\Top
     */
    class TopOrderPageInputData
    {
        /**
         * TopOrderPageInputData constructor.
         */
        public function __construct()
        {
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Hospital\Top
    */
    interface TopOrderPageInputPortInterface
    {
        /**
         * @param TopOrderPageInputData $inputData
         */
        function handle(TopOrderPageInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Hospital\Top {
    /**
     * Class TopOrderPageOutputData
     * @package JoyPla\Application\OutputPorts\Hospital\Top;
     */
    class TopOrderPageOutputData
    {
        /** @var string */
        private $createdId;

        /**
         * TopOrderPageOutputData constructor.
         */
        public function __construct()
        {
        }
    } 

    /**
     * Interface TopOrderPageOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Hospital\Top;
    */
    interface TopOrderPageOutputPortInterface
    {
        /**
         * @param TopOrderPageOutputData $outputData
         */
        function output(TopOrderPageOutputData $outputData);
    }
}