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
    use JoyPla\Service\Presenter\Web\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ReceivedLabelInteractor
     * @package JoyPla\Application\Interactors\Web\Received
     */
    class ReceivedLabelInteractor implements ReceivedLabelInputPortInterface
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
         * @param ReceivedLabelInputData $inputData
         */
        public function handle(ReceivedLabelInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->hospitalId);
            $receivedId = new ReceivedId($inputData->receivedId);

            $hospital = $this->repositoryProvider
                ->getHospitalRepository()
                ->index($hospitalId);

            $received = $this->repositoryProvider
                ->getReceivedRepository()
                ->index($hospitalId, $receivedId);

            if ($received === null) {
                throw new NotFoundException('Not Found.', 404);
            }

            $print = [];
            foreach ($received->getReceivedItems() as $receivedItem) {
                $print[] = new ReceivedLabelModel(
                    $receivedItem,
                    1,
                    $hospital->labelDesign1
                );
            }

            $this->presenterProvider
                ->getReceivedLabelPresenter()
                ->output(new ReceivedLabelOutputData($print));
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
        public string $hospitalId;
        public string $receivedId;

        public function __construct(string $hospitalId, string $receivedId)
        {
            $this->hospitalId = $hospitalId;
            $this->receivedId = $receivedId;
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
    /**
     * Class ReceivedLabelOutputData
     * @package JoyPla\Application\OutputPorts\Web\Received;
     */
    class ReceivedLabelOutputData
    {
        public array $print;

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
