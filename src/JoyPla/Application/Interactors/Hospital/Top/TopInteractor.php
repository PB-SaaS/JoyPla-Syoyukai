<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Hospital\Top {
    use JoyPla\Application\InputPorts\Hospital\Top\TopIndexInputPortInterface;
    use JoyPla\Application\InputPorts\Hospital\Top\TopIndexInputData;
    use JoyPla\Application\OutputPorts\Hospital\Top\TopIndexOutputData;
    use JoyPla\Application\OutputPorts\Hospital\Top\TopIndexOutputPortInterface;

    /**
     * Class TopIndexInteractor
     * @package JoyPla\Application\Interactors\Hospital\Top
     */
    class TopIndexInteractor implements TopIndexInputPortInterface
    {
        /** @var TopIndexOutputPortInterface */
        private $outputPort;

        /**
         * TopIndexInteractor constructor.
         * @param TopIndexOutputPortInterface $outputPort
         */
        public function __construct(TopIndexOutputPortInterface $outputPort)
        {
            $this->outputPort = $outputPort;
        }

        /**
         * @param TopIndexInputData $inputData
         */
        public function handle(TopIndexInputData $inputData)
        {
            $this->outputPort->output(new TopIndexOutputData());
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Hospital\Top {
    /**
     * Class TopIndexInputData
     * @package JoyPla\Application\InputPorts\Hospital\Top
     */
    class TopIndexInputData
    {
        /**
         * TopIndexInputData constructor.
         */
        public function __construct()
        {
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Hospital\Top
    */
    interface TopIndexInputPortInterface
    {
        /**
         * @param TopIndexInputData $inputData
         */
        function handle(TopIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Hospital\Top {
    /**
     * Class TopIndexOutputData
     * @package JoyPla\Application\OutputPorts\Hospital\Top;
     */
    class TopIndexOutputData
    {
        /** @var string */
        private $createdId;

        /**
         * TopIndexOutputData constructor.
         */
        public function __construct()
        {
        }
    }

    /**
     * Interface TopIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Hospital\Top;
    */
    interface TopIndexOutputPortInterface
    {
        /**
         * @param TopIndexOutputData $outputData
         */
        function output(TopIndexOutputData $outputData);
    }
}