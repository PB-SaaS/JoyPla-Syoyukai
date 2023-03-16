<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Received {
    use App\Model\Division;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedShowInputData;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedShowOutputPortInterface;
    use JoyPla\Enterprise\Models\Received;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ReceivedRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ReceivedShowInteractor
     * @package JoyPla\Application\Interactors\Api\Received
     */
    class ReceivedShowInteractor implements ReceivedShowInputPortInterface
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
         * @param ReceivedShowInputData $inputData
         */
        public function handle(ReceivedShowInputData $inputData)
        {
            [
                $receiveds,
                $count,
            ] = $this->repositoryProvider
                ->getReceivedRepository()
                ->search(
                    new HospitalId($inputData->user->hospitalId),
                    $inputData->search
                );
            $this->presenterProvider
                ->getReceivedShowPresenter()
                ->output(new ReceivedShowOutputData($receiveds, $count));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Received {
    use Auth;
    use stdClass;

    /**
     * Class ReceivedShowInputData
     * @package JoyPla\Application\InputPorts\Api\Received
     */
    class ReceivedShowInputData
    {
        public Auth $user;
        public stdClass $search;

        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->itemName = $search['itemName'];
            $this->search->makerName = $search['makerName'];
            $this->search->itemCode = $search['itemCode'];
            $this->search->itemStandard = $search['itemStandard'];
            $this->search->itemJANCode = $search['itemJANCode'];
            $this->search->registerDate = $search['registerDate'];
            $this->search->receivedDate = $search['receivedDate'];
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->receivedStatus = $search['receivedStatus'];
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Received
     */
    interface ReceivedShowInputPortInterface
    {
        /**
         * @param ReceivedShowInputData $inputData
         */
        function handle(ReceivedShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Received {
    use JoyPla\Enterprise\Models\Received;

    /**
     * Class ReceivedShowOutputData
     * @package JoyPla\Application\OutputPorts\Api\Received;
     */
    class ReceivedShowOutputData
    {
        public int $count;
        public array $receiveds;

        public function __construct(array $receiveds, int $count)
        {
            $this->count = $count;
            $this->receiveds = array_map(function (Received $received) {
                return $received->toArray();
            }, $receiveds);
        }
    }

    /**
     * Interface ReceivedShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Received;
     */
    interface ReceivedShowOutputPortInterface
    {
        /**
         * @param ReceivedShowOutputData $outputData
         */
        function output(ReceivedShowOutputData $outputData);
    }
}
