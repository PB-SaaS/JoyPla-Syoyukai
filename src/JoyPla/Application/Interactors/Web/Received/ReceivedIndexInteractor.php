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
    use JoyPla\Service\Presenter\Web\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ReceivedIndexInteractor
     * @package JoyPla\Application\Interactors\Web\Received
     */
    class ReceivedIndexInteractor implements ReceivedIndexInputPortInterface
    {
        private PresenterProvider $presenterProvider;
        private RepositoryProvider $repositoryProvider;

        public function __construct(
            PresenterProvider $presenterProvider,
            RepositoryProvider $repositoryProvider
        ) {
            $this->presenterProvider = $presenterProvider;
            $this->repositoryProvider = $repositoryProvider;
        }

        /**
         * @param ReceivedIndexInputData $inputData
         */
        public function handle(ReceivedIndexInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $receivedId = new ReceivedId($inputData->receivedId);

            $received = $this->repositoryProvider
                ->getReceivedRepository()
                ->index($hospitalId, $receivedId);

            if ($received === null) {
                throw new NotFoundException('Not Found.', 404);
            }

            if (
                $inputData->isOnlyMyDivision &&
                !$received
                    ->getDivision()
                    ->getDivisionId()
                    ->equal($inputData->user->divisionId)
            ) {
                throw new NotFoundException('Not Found.', 404);
            }

            $this->presenterProvider
                ->getReceivedIndexPresenter()
                ->output(new ReceivedIndexOutputData($received));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Web\Received {
    use Auth;

    class ReceivedIndexInputData
    {
        public Auth $user;
        public string $receivedId;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            string $receivedId,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->receivedId = $receivedId;
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
        public array $received;

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
