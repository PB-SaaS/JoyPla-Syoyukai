<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Web\Received {

    use App\Model\Division;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Web\Received\ReceivedIndexInputData;
    use JoyPla\Application\InputPorts\Web\Received\ReceivedIndexInputPortInterface;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Received\ReceivedIndexOutputPortInterface;
    use JoyPla\Enterprise\Models\ReceivedId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\Received;
    use JoyPla\Enterprise\Models\ReceivedStatus;
    use JoyPla\InterfaceAdapters\GateWays\Repository\receivedRepositoryInterface;

    /**
     * Class ReceivedIndexInteractor
     * @package JoyPla\Application\Interactors\Web\Received
     */
    class ReceivedIndexInteractor implements ReceivedIndexInputPortInterface
    {
        /** @var ReceivedIndexOutputPortInterface */
        private ReceivedIndexOutputPortInterface $outputPort;

        /** @var ReceivedRepositoryInterface */
        private ReceivedRepositoryInterface $receivedRepository;

        /**
         * ReceivedIndexInteractor constructor.
         * @param ReceivedIndexOutputPortInterface $outputPort
         */
        public function __construct(ReceivedIndexOutputPortInterface $outputPort , ReceivedRepositoryInterface $receivedRepository)
        {
            $this->outputPort = $outputPort;
            $this->receivedRepository = $receivedRepository;
        }

        /**
         * @param ReceivedIndexInputData $inputData
         */
        public function handle(ReceivedIndexInputData $inputData)
        {

            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $receivedId = new ReceivedId($inputData->receivedId);

            $received = $this->receivedRepository->index($hospitalId,$receivedId);

            if( $received === null )
            {
                throw new NotFoundException("Not Found.",404);
            }

            if($inputData->isOnlyMyDivision && ! $received->getDivision()->getDivisionId()->equal($inputData->user->divisionId))
            {
                throw new NotFoundException("Not Found.",404);
            }

            $this->outputPort->output(new ReceivedIndexOutputData($received));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Web\Received {

    use Auth;
    use stdClass;

    /**
     * Class ReceivedIndexInputData
     * @package JoyPla\Application\InputPorts\Web\Received
     */
    class ReceivedIndexInputData
    {
        /**
         * ReceivedIndexInputData constructor.
         */
        public function __construct(Auth $user , string $receivedId , bool $isOnlyMyDivision)
        {
            $this->user = $user;
            $this->receivedId= $receivedId;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Web\Received
    */
    interface ReceivedIndexInputPortInterface
    {
        /**
         * @param ReceivedIndexInputData $inputData
         */
        function handle(ReceivedIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Web\Received {

    use JoyPla\Enterprise\Models\Received;

    /**
     * Class ReceivedIndexOutputData
     * @package JoyPla\Application\OutputPorts\Web\Received;
     */
    class ReceivedIndexOutputData
    {
        /** @var string */

        /**
         * ReceivedIndexOutputData constructor.
         */
        public function __construct(Received $received)
        {
            $this->received = $received->toArray();
        }
    }

    /**
     * Interface ReceivedIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Web\Received;
    */
    interface ReceivedIndexOutputPortInterface
    {
        /**
         * @param ReceivedIndexOutputData $outputData
         */
        function output(ReceivedIndexOutputData $outputData);
    }
} 