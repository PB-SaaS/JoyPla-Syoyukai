<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Web\Received {

    use App\Model\Division;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Web\Received\ReceivedLabelInputData;
    use JoyPla\Application\InputPorts\Web\Received\ReceivedLabelInputPortInterface;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedLabelOutputData;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedLabelOutputPortInterface;
    use JoyPla\Enterprise\CommonModels\ReceivedLabelModel;
    use JoyPla\Enterprise\Models\ReceivedId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\Received;
    use JoyPla\Enterprise\Models\ReceivedStatus;
    use JoyPla\InterfaceAdapters\GateWays\Repository\HospitalRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\receivedRepositoryInterface;

    /**
     * Class ReceivedLabelInteractor
     * @package JoyPla\Application\Interactors\Web\Received
     */
    class ReceivedLabelInteractor implements ReceivedLabelInputPortInterface
    {
        /** @var ReceivedLabelOutputPortInterface */
        private ReceivedLabelOutputPortInterface $outputPort;

        /** @var ReceivedRepositoryInterface */
        private ReceivedRepositoryInterface $receivedRepository;

        /**
         * ReceivedLabelInteractor constructor.
         * @param ReceivedLabelOutputPortInterface $outputPort
         */
        public function __construct(
            ReceivedLabelOutputPortInterface $outputPort , 
            ReceivedRepositoryInterface $receivedRepository,
            HospitalRepositoryInterface $hospitalRepository
            )
        {
            $this->outputPort = $outputPort;
            $this->receivedRepository = $receivedRepository;
            $this->hospitalRepository = $hospitalRepository;
        }

        /**
         * @param ReceivedLabelInputData $inputData
         */
        public function handle(ReceivedLabelInputData $inputData)
        {

            $hospitalId = new HospitalId($inputData->hospitalId);
            $receivedId = new ReceivedId($inputData->receivedId);

            $hospital = $this->hospitalRepository->index($hospitalId);

            $received = $this->receivedRepository->index($hospitalId,$receivedId);

            if( $received === null )
            {
                throw new NotFoundException("Not Found.",404);
            }

            $print = [];
            foreach($received->getReceivedItems() as $receivedItem)
            {
                $print[] = (new ReceivedLabelModel($receivedItem , 1 , $hospital->labelDesign1));
            }

            $this->outputPort->output(new ReceivedLabelOutputData($print));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Web\Received {

    use stdClass;

    /**
     * Class ReceivedLabelInputData
     * @package JoyPla\Application\InputPorts\Web\Received
     */
    class ReceivedLabelInputData
    {
        /**
         * ReceivedLabelInputData constructor.
         */
        public function __construct(string $hospitalId , string $receivedId)
        {
            $this->hospitalId = $hospitalId;
            $this->receivedId= $receivedId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Web\Received
    */
    interface ReceivedLabelInputPortInterface
    {
        /**
         * @param ReceivedLabelInputData $inputData
         */
        function handle(ReceivedLabelInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Web\Received {

    use JoyPla\Enterprise\Models\Received;

    /**
     * Class ReceivedLabelOutputData
     * @package JoyPla\Application\OutputPorts\Web\Received;
     */
    class ReceivedLabelOutputData
    {
        /** @var string */

        /**
         * ReceivedLabelOutputData constructor.
         */
        public function __construct(array $print)
        {
            $this->print = $print;
        }
    }

    /**
     * Interface ReceivedLabelOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Web\Received;
    */
    interface ReceivedLabelOutputPortInterface
    {
        /**
         * @param ReceivedLabelOutputData $outputData
         */
        function output(ReceivedLabelOutputData $outputData);
    }
} 