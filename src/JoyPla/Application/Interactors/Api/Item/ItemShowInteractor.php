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

    /**
     * Class ItemShowInteractor
     * @package JoyPla\Application\Interactors\Api\Item
     */
    class ItemShowInteractor implements ItemShowInputPortInterface
    {
        /** @var ItemShowOutputPortInterface */
        private ItemShowOutputPortInterface $outputPort;

        /** @var ItemRepositoryInterface */
        private ItemRepositoryInterface $ItemRepository;

        /**
         * ItemShowInteractor constructor.
         * @param ItemShowOutputPortInterface $outputPort
         */
        public function __construct(ItemShowOutputPortInterface $outputPort , ItemRepositoryInterface $ItemRepository)
        {
            $this->outputPort = $outputPort;
            $this->ItemRepository = $ItemRepository;
        }

        /**
         * @param ItemShowInputData $inputData
         */
        public function handle(ItemShowInputData $inputData)
        {
            [ $Item , $count ] = $this->ItemRepository->search(
                (new HospitalId($inputData->hospitalId)) ,
                $inputData->search
            );
            $this->outputPort->output(new ItemShowOutputData($Item, $count));
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
        /**
         * ItemShowInputData constructor.
         */
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
            $this->search->perPage= $search['perPage'];
            $this->search->currentPage= $search['currentPage'];
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
        /**
         * ItemShowOutputData constructor.
         */
        public function __construct(array $result , int $count)
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