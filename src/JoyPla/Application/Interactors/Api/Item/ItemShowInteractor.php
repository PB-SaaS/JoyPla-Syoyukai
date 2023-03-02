<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Item {
    use JoyPla\Application\InputPorts\Api\Item\ItemShowInputData;
    use JoyPla\Application\InputPorts\Api\Item\ItemShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Item\ItemShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Item\ItemShowOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ItemShowInteractor
     * @package JoyPla\Application\Interactors\Api\Item
     */
    class ItemShowInteractor implements ItemShowInputPortInterface
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
         * @param ItemShowInputData $inputData
         */
        public function handle(ItemShowInputData $inputData)
        {
            [
                $Item,
                $count,
            ] = $this->repositoryProvider
                ->getItemRepository()
                ->search(
                    new HospitalId($inputData->hospitalId),
                    $inputData->search
                );
            $this->presenterProvider
                ->getItemShowPresenter()
                ->output(new ItemShowOutputData($Item, $count));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Item {
    use stdClass;

    /**
     * Class ItemShowInputData
     * @package JoyPla\Application\InputPorts\Api\Item
     */
    class ItemShowInputData
    {
        public string $hospitalId;
        public stdClass $search;
        public function __construct(string $hospitalId, array $search)
        {
            $this->hospitalId = $hospitalId;
            $this->search = new stdClass();
            $this->search->itemName = $search['itemName'];
            $this->search->makerName = $search['makerName'];
            $this->search->itemCode = $search['itemCode'];
            $this->search->itemStandard = $search['itemStandard'];
            $this->search->itemJANCode = $search['itemJANCode'];
            $this->search->distributorIds = $search['distributorIds'];
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
            $this->search->isNotUse = '0';
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Item
     */
    interface ItemShowInputPortInterface
    {
        /**
         * @param ItemShowInputData $inputData
         */
        function handle(ItemShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Item {
    use Collection;
    use JoyPla\Enterprise\Models\Item;

    /**
     * Class ItemShowOutputData
     * @package JoyPla\Application\OutputPorts\Api\Item;
     */
    class ItemShowOutputData
    {
        public array $Items;
        public int $count;

        public function __construct(array $result, int $count)
        {
            $this->Items = $result;
            $this->count = $count;
        }
    }

    /**
     * Interface ItemShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Item;
     */
    interface ItemShowOutputPortInterface
    {
        /**
         * @param ItemShowOutputData $outputData
         */
        function output(ItemShowOutputData $outputData);
    }
}
