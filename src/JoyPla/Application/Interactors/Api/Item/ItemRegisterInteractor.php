<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Item {

    use JoyPla\Application\InputPorts\Api\Item\ItemRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Item\ItemRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Item\ItemRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Item\ItemRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRepositoryInterface;

    /**
     * Class ItemRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\Item
     */
    class ItemRegisterInteractor implements ItemRegisterInputPortInterface
    {
        /** @var ItemRegisterOutputPortInterface */
        private ItemRegisterOutputPortInterface $outputPort;

        /** @var ItemRepositoryInterface */
        private ItemRepositoryInterface $ItemRepository;

        /**
         * ItemRegisterInteractor constructor.
         * @param ItemRegisterOutputPortInterface $outputPort
         */
        public function __construct(ItemRegisterOutputPortInterface $outputPort , ItemRepositoryInterface $ItemRepository)
        {
            $this->outputPort = $outputPort;
            $this->ItemRepository = $ItemRepository;
        }

        /**
         * @param ItemRegisterInputData $inputData
         */
        public function handle(ItemRegisterInputData $inputData)
        {
            $Item = $this->ItemRepository->saveToArray(
                (new TenantId($inputData->tenantId)) ,
                (new HospitalId($inputData->hospitalId)) ,
                (array)$inputData->input
            );
            $this->outputPort->output(new ItemRegisterOutputData($Item));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Item {

    use stdClass;

    /**
     * Class ItemRegisterInputData
     * @package JoyPla\Application\InputPorts\Api\Item
     */
    class ItemRegisterInputData
    {
        /**
         * ItemRegisterInputData constructor.
         */
        public function __construct(string $tenantId, string $hospitalId, array $input)
        {
            $this->tenantId = $tenantId;
            $this->hospitalId = $hospitalId;
            $this->input = new stdClass();
            $this->input->itemName = $input['itemName'];
            $this->input->makerName = $input['makerName'];
            $this->input->itemCode = $input['itemCode'];
            $this->input->itemStandard = $input['itemStandard'];
            $this->input->itemJANCode = $input['itemJANCode'];
            $this->input->category = $input["category"];
            $this->input->smallCategory = $input["smallCategory"];
            $this->input->makerName = $input["makerName"];
            $this->input->catalogNo = $input["catalogNo"];
            $this->input->serialNo = $input["serialNo"];
            $this->input->lotManagement = $input["lotManagement"];
            $this->input->officialFlag = $input["officialFlag"];
            $this->input->officialprice = $input["officialprice"];
            $this->input->officialpriceOld = $input["officialpriceOld"];
            $this->input->quantity = $input["quantity"];
            $this->input->quantityUnit = $input["quantityUnit"];
            $this->input->itemUnit = $input["itemUnit"];
            $this->input->minPrice = $input["minPrice"];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Item
    */
    interface ItemRegisterInputPortInterface
    {
        /**
         * @param ItemRegisterInputData $inputData
         */
        function handle(ItemRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Item {

    use Collection;
    use JoyPla\Enterprise\Models\Item;

    /**
     * Class ItemRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\Item;
     */
    class ItemRegisterOutputData
    {
        /**
         * ItemRegisterOutputData constructor.
         */
        public function __construct(array $result)
        {
            $this->Items = $result;
        }
    }

    /**
     * Interface ItemRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Item;
    */
    interface ItemRegisterOutputPortInterface
    {
        /**
         * @param ItemRegisterOutputData $outputData
         */
        function output(ItemRegisterOutputData $outputData);
    }
}