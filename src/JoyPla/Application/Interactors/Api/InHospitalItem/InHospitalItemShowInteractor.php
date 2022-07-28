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
    use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepositoryInterface;

    /**
     * Class InHospitalItemShowInteractor
     * @package JoyPla\Application\Interactors\Api\InHospitalItem
     */
    class InHospitalItemShowInteractor implements InHospitalItemShowInputPortInterface
    {
        /** @var InHospitalItemShowOutputPortInterface */
        private InHospitalItemShowOutputPortInterface $outputPort;

        /** @var InHospitalItemRepositoryInterface */
        private InHospitalItemRepositoryInterface $InHospitalItemRepository;

        /**
         * InHospitalItemShowInteractor constructor.
         * @param InHospitalItemShowOutputPortInterface $outputPort
         */
        public function __construct(InHospitalItemShowOutputPortInterface $outputPort , InHospitalItemRepositoryInterface $InHospitalItemRepository)
        {
            $this->outputPort = $outputPort;
            $this->InHospitalItemRepository = $InHospitalItemRepository;
        }

        /**
         * @param InHospitalItemShowInputData $inputData
         */
        public function handle(InHospitalItemShowInputData $inputData)
        {
            [ $InHospitalItem , $count ] = $this->InHospitalItemRepository->search(
                (new HospitalId($inputData->hospitalId)) ,
                $inputData->search
            );
            $this->outputPort->output(new InHospitalItemShowOutputData($InHospitalItem, $count));
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
        /**
         * InHospitalItemShowInputData constructor.
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
            $this->search->distributorIds= $search['distributorIds'];
            $this->search->perPage= $search['perPage'];
            $this->search->currentPage= $search['currentPage'];
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
        /**
         * InHospitalItemShowOutputData constructor.
         */
        public function __construct(array $result , int $count)
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