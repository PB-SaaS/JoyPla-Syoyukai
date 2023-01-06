<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Web\ItemRequest {

    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Web\ItemRequest\ItemRequestShowInputData;
    use JoyPla\Application\InputPorts\Web\ItemRequest\ItemRequestShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Web\ItemRequest\ItemRequestShowOutputData;
    use JoyPla\Application\OutputPorts\Web\ItemRequest\ItemRequestShowOutputPortInterface;
    use JoyPla\Enterprise\Models\RequestHId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRequestRepositoryInterface;

    /**
     * Class ItemRequestShowInteractor
     * @package JoyPla\Application\Interactors\Web\ItemRequest
     */
    class ItemRequestShowInteractor implements ItemRequestShowInputPortInterface
    {
        /** @var ItemRequestShowOutputPortInterface */
        private ItemRequestShowOutputPortInterface $outputPort;

        /** @var ItemRequestRepositoryInterface */
        private ItemRequestRepositoryInterface $repository;

        /**
         * ItemRequestShowInteractor constructor.
         * @param ItemRequestShowOutputPortInterface $outputPort
         */
        public function __construct(
            ItemRequestShowOutputPortInterface $outputPort,
            ItemRequestRepositoryInterface $repository
        ) {
            $this->outputPort = $outputPort;
            $this->repository = $repository;
        }

        /**
         * @param ItemRequestShowInputData $inputData
         */
        public function handle(ItemRequestShowInputData $inputData)
        {

            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $requestHId = new RequestHId($inputData->requestHId);

            $itemRequest = $this->repository->show($hospitalId, $requestHId);

            if ($itemRequest === null) {
                throw new NotFoundException("Not Found.", 404);
            }

            if ($inputData->isOnlyMyDivision && !$itemRequest->getSourceDivision()->getDivisionId()->equal($inputData->user->divisionId)) {
                throw new NotFoundException("Not Found.", 404);
            }

            $itemRequest = $itemRequest->toArray();

            $this->outputPort->output(new ItemRequestShowOutputData($itemRequest));
        }
    }
}


/***
 * INPUT
 */

namespace JoyPla\Application\InputPorts\Web\ItemRequest {

    use Auth;

    /**
     * Class ItemRequestShowInputData
     * @package JoyPla\Application\InputPorts\Web\ItemRequest
     */
    class ItemRequestShowInputData
    {
        /**
         * ItemRequestShowInputData constructor.
         */
        public function __construct(Auth $user, string $requestHId, bool $isOnlyMyDivision)
        {
            $this->user = $user;
            $this->requestHId = $requestHId;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface ItemRequestShowCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Web\ItemRequest
     */
    interface ItemRequestShowInputPortInterface
    {
        /**
         * @param ItemRequestShowInputData $inputData
         */
        function handle(ItemRequestShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Web\ItemRequest {

    /**
     * Class ItemRequestShowOutputData
     * @package JoyPla\Application\OutputPorts\Web\ItemRequest;
     */
    class ItemRequestShowOutputData
    {
        /** @var array */

        /**
         * ItemRequestShowOutputData constructor.
         */
        public function __construct(array $itemRequest)
        {
            $this->itemRequest = $itemRequest;
        }
    }

    /**
     * Interface ItemRequestShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Web\ItemRequest;
     */
    interface ItemRequestShowOutputPortInterface
    {
        /**
         * @param ItemRequestShowOutputData $outputData
         */
        function output(ItemRequestShowOutputData $outputData);
    }
}
