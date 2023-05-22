<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\InHospitalItem {
    use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemShowInputData;
    use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemShowOutputData;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemShowOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InHospitalItemId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class InHospitalItemShowInteractor
     * @package JoyPla\Application\Interactors\Api\InHospitalItem
     */
    class InHospitalItemShowInteractor implements
        InHospitalItemShowInputPortInterface
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
         * @param InHospitalItemShowInputData $inputData
         */
        public function handle(InHospitalItemShowInputData $inputData)
        {
            $inHospitalItemIds = $inputData->search->inHospitalItemIds;
            $inHospitalItemIds = array_map(function($inHospitalItemId){
                return new InHospitalItemId($inHospitalItemId);
            }, $inHospitalItemIds);
            $InHospitalItems = $this->repositoryProvider
                ->getInHospitalItemRepository()
                ->getInHospitalItemViewByInHospitalItemIds(
                    new HospitalId($inputData->hospitalId),
                    $inHospitalItemIds
                );
            $this->presenterProvider
                ->getInHospitalItemShowPresenter()
                ->output(
                    new InHospitalItemShowOutputData($InHospitalItems, count($InHospitalItems))
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
     * Class InHospitalItemShowInputData
     * @package JoyPla\Application\InputPorts\Api\InHospitalItem
     */
    class InHospitalItemShowInputData
    {
        public string $hospitalId;
        public stdClass $search;
        /**
         * InHospitalItemShowInputData constructor.
         */
        public function __construct(string $hospitalId, array $search)
        {
            $this->hospitalId = $hospitalId;
            $this->search = new stdClass();
            $this->search->inHospitalItemIds = $search['inHospitalItemIds'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\InHospitalItem
     */
    interface InHospitalItemShowInputPortInterface
    {
        /**
         * @param InHospitalItemShowInputData $inputData
         */
        function handle(InHospitalItemShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\InHospitalItem {
    use Collection;
    use JoyPla\Enterprise\Models\InHospitalItem;

    /**
     * Class InHospitalItemShowOutputData
     * @package JoyPla\Application\OutputPorts\Api\InHospitalItem;
     */
    class InHospitalItemShowOutputData
    {
        public array $InHospitalItems = [];
        public int $count = 0;
        /**
         * InHospitalItemShowOutputData constructor.
         */
        public function __construct(array $result, int $count)
        {
            $this->InHospitalItems = $result;
            $this->count = $count;
        }
    }

    /**
     * Interface InHospitalItemShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\InHospitalItem;
     */
    interface InHospitalItemShowOutputPortInterface
    {
        /**
         * @param InHospitalItemShowOutputData $outputData
         */
        function output(InHospitalItemShowOutputData $outputData);
    }
}
