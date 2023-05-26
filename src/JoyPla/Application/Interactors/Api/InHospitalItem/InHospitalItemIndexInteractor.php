<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\InHospitalItem {

    use ApiErrorCode\LoginLocked;
    use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemIndexInputData;
    use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemIndexInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemIndexOutputData;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemIndexOutputPortInterface;
    use JoyPla\Enterprise\Models\DivisionId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InHospitalItemId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class InHospitalItemIndexInteractor
     * @package JoyPla\Application\Interactors\Api\InHospitalItem
     */
    class InHospitalItemIndexInteractor implements
        InHospitalItemIndexInputPortInterface
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
         * @param InHospitalItemIndexInputData $inputData
         */
        public function handle(InHospitalItemIndexInputData $inputData)
        {
            [
                $inHospitalItem,
                $count,
            ] = $this->repositoryProvider
                ->getInHospitalItemRepository()
                ->search(
                    new HospitalId($inputData->hospitalId),
                    $inputData->search
                );

                /*
                $stocks = [];
            
            if($inputData->divisionId){
                $inHospitalItemIds = array_map(function($inHospitalItem){
                    return new InHospitalItemId($inHospitalItem->inHospitalItemId);
                },$inHospitalItem);

                $stocks = $this->repositoryProvider
                ->getStockRepository()->getInHospitalItemIdsAndDivisionId(
                    new HospitalId($inputData->hospitalId),
                    new DivisionId($inputData->divisionId),
                    $inHospitalItemIds
                );
            }
                
            foreach($inHospitalItem as $key => $item)
            {
                $stock = array_find($stocks, function($stock) use ($item)
                {
                    return $stock->inHospitalItemId == $item->inHospitalItemId;
                });


                $inHospitalItem[$key]->_stock = $stock;
            }
            */

            $this->presenterProvider
                ->getInHospitalItemIndexPresenter()
                ->output(
                    new InHospitalItemIndexOutputData($inHospitalItem, $count)
                );
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\InHospitalItem {
    use stdClass;

    /**
     * Class InHospitalItemIndexInputData
     * @package JoyPla\Application\InputPorts\Api\InHospitalItem
     */
    class InHospitalItemIndexInputData
    {
        public stdClass $search;
        public string $hospitalId;
        public string $divisionId;
        /**
         * InHospitalItemIndexInputData constructor.
         */
        public function __construct(string $hospitalId, array $search , string $divisionId = '')
        {
            $this->hospitalId = $hospitalId;
            $this->divisionId = $divisionId;
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
     * @package JoyPla\Application\InputPorts\Api\InHospitalItem
     */
    interface InHospitalItemIndexInputPortInterface
    {
        /**
         * @param InHospitalItemIndexInputData $inputData
         */
        function handle(InHospitalItemIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\InHospitalItem {
    use Collection;
    use JoyPla\Enterprise\Models\InHospitalItem;

    /**
     * Class InHospitalItemIndexOutputData
     * @package JoyPla\Application\OutputPorts\Api\InHospitalItem;
     */
    class InHospitalItemIndexOutputData
    {
        public array $inHospitalItems = [];
        public int $count = 0;
        /**
         * InHospitalItemIndexOutputData constructor.
         */
        public function __construct(array $result, int $count)
        {
            $this->InHospitalItems = $result;
            $this->count = $count;
        }
    }

    /**
     * Interface InHospitalItemIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\InHospitalItem;
     */
    interface InHospitalItemIndexOutputPortInterface
    {
        /**
         * @param InHospitalItemIndexOutputData $outputData
         */
        function output(InHospitalItemIndexOutputData $outputData);
    }
}
