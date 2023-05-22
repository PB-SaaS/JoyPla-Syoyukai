<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Web\Product\ItemList {
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Web\Product\ItemList\ItemListShowInputData;
    use JoyPla\Application\InputPorts\Web\Product\ItemList\ItemListShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Web\Product\ItemList\ItemListShowOutputData;
    use JoyPla\Application\OutputPorts\Web\Product\ItemList\ItemListShowOutputPortInterface;
    use JoyPla\Enterprise\Models\ItemListId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemListRepository;
    use JoyPla\Service\Presenter\Web\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ItemListShowInteractor
     * @package JoyPla\Application\Interactors\Web\Product\ItemList
     */
    class ItemListShowInteractor implements ItemListShowInputPortInterface
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
         * @param ItemListShowInputData $inputData
         */
        public function handle(ItemListShowInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $itemListId = new ItemListId($inputData->itemListId);

            $itemList = $this->repositoryProvider
                ->getItemListRepository()
                ->findByItemListId($hospitalId, $itemListId);

            if ($itemList === null) {
                throw new NotFoundException('Not Found.', 404);
            }

            if (
                $inputData->isOnlyMyDivision &&
                !$itemList
                    ->getDivisionId()
                    ->equal($inputData->user->divisionId) &&
                $itemList
                    ->getUsableStatus
                    ->equal('1')
            ) {
                throw new NotFoundException('Not Found.', 404);
            }

            $itemList = $itemList->toArray();

            $this->presenterProvider
                ->getItemListShowPresenter()
                ->output(new ItemListShowOutputData($itemList));
        }
    }
}

/***
 * INPUT
 */

namespace JoyPla\Application\InputPorts\Web\Product\ItemList {
    use Auth;

    /**
     * Class ItemListShowInputData
     * @package JoyPla\Application\InputPorts\Web\Product\ItemList
     */
    class ItemListShowInputData
    {
        public Auth $user;
        public string $itemListId;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            string $itemListId,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->itemListId = $itemListId;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface ItemListShowInputPortInterface
     * @package JoyPla\Application\InputPorts\Web\Product\ItemList
     */
    interface ItemListShowInputPortInterface
    {
        /**
         * @param ItemListShowInputData $inputData
         */
        function handle(ItemListShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Web\Product\ItemList {
    /**
     * Class ItemListShowOutputData
     * @package JoyPla\Application\OutputPorts\Web\Product\ItemList;
     */
    class ItemListShowOutputData
    {
        public array $itemList;

        public function __construct(array $itemList)
        {
            $this->itemList = $itemList;
        }
    }

    /**
     * Interface ItemListShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Web\Product\ItemList;
     */
    interface ItemListShowOutputPortInterface
    {
        /**
         * @param ItemListShowOutputData $outputData
         */
        function output(ItemListShowOutputData $outputData);
    }
}
