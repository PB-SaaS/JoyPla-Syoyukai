<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\InHospitalItem {

    use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemRegisterInputData;
    use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepositoryInterface;

    /**
     * Class InHospitalItemRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\InHospitalItem
     */
    class InHospitalItemRegisterInteractor implements InHospitalItemRegisterInputPortInterface
    {
        /** @var InHospitalItemRegisterOutputPortInterface */
        private InHospitalItemRegisterOutputPortInterface $outputPort;

        /** @var InHospitalItemRepositoryInterface */
        private InHospitalItemRepositoryInterface $InHospitalItemRepository;

        /**
         * InHospitalItemRegisterInteractor constructor.
         * @param InHospitalItemRegisterOutputPortInterface $outputPort
         */
        public function __construct(InHospitalItemRegisterOutputPortInterface $outputPort , InHospitalItemRepositoryInterface $InHospitalItemRepository)
        {
            $this->outputPort = $outputPort;
            $this->InHospitalItemRepository = $InHospitalItemRepository;
        }

        /**
         * @param InHospitalItemRegisterInputData $inputData
         */
        public function handle(InHospitalItemRegisterInputData $inputData)
        {
            $InHospitalItem = $this->InHospitalItemRepository->saveToArray(
                (new HospitalId($inputData->hospitalId)) ,
                (new ItemId($inputData->itemId)) ,
                (new PriceId($inputData->priceId)) ,
                $inputData->input
            );
            $this->outputPort->output(new InHospitalItemRegisterOutputData($InHospitalItem));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\InHospitalItem {

    use stdClass;

    /**
     * Class InHospitalItemRegisterInputData
     * @package JoyPla\Application\InputPorts\Api\InHospitalItem
     */
    class InHospitalItemRegisterInputData
    {
        /**
         * InHospitalItemRegisterInputData constructor.
         */
        public function __construct(string $hospitalId, array $input)
        {
            $this->hospitalId = $hospitalId;
            $this->input = new stdClass();
            $this->input->itemName = $input['itemName'];
            $this->input->makerName = $input['makerName'];
            $this->input->itemCode = $input['itemCode'];
            $this->input->itemStandard = $input['itemStandard'];
            $this->input->itemJANCode = $input['itemJANCode'];
            $this->input->distributorId = $input['distributorId'];
            $this->input->perPage= $input['perPage'];
            $this->input->currentPage = $input['currentPage'];
            $this->input->isNotUse = '0';
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\InHospitalItem
    */
    interface InHospitalItemRegisterInputPortInterface
    {
        /**
         * @param InHospitalItemRegisterInputData $inputData
         */
        function handle(InHospitalItemRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\InHospitalItem {

    use Collection;
    use JoyPla\Enterprise\Models\InHospitalItem;

    /**
     * Class InHospitalItemRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\InHospitalItem;
     */
    class InHospitalItemRegisterOutputData
    {
        /**
         * InHospitalItemRegisterOutputData constructor.
         */
        public function __construct(array $result , int $count)
        {
            $this->InHospitalItems = $result;
            $this->count = $count;
        }
    }

    /**
     * Interface InHospitalItemRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\InHospitalItem;
    */
    interface InHospitalItemRegisterOutputPortInterface
    {
        /**
         * @param InHospitalItemRegisterOutputData $outputData
         */
        function output(InHospitalItemRegisterOutputData $outputData);
    }
}