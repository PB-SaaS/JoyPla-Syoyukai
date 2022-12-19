<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Item{

    use JoyPla\Application\InputPorts\Api\Item\ItemRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Item\ItemRegisterInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Price\PriceRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Price\PriceRegisterInputPortInterface;
    use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemRegisterInputData;
    use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Item\ItemRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Item\ItemRegisterOutputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Price\PriceRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Price\PriceRegisterOutputPortInterface;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\InHospitalItem\InHospitalItemRegisterOutputPortInterface;
    use JoyPla\Application\InputPorts\Api\Item\ItemAndPriceAndInHospitalItemRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Item\ItemAndPriceAndInHospitalItemRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Item\ItemAndPriceAndInHospitalItemRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Item\ItemAndPriceAndInHospitalItemRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\Item;
    use JoyPla\Enterprise\Models\ItemPrice;
    use JoyPla\Enterprise\Models\InHospitalItem;
    use JoyPla\InterfaceAdapters\GateWays\Repository\HospitalRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DistributorRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\PriceRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemAndPriceAndInHospitalItemRepositoryInterface;

    /**
     * Class ItemAndPriceAndInHospitalItemRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\ItemAndPriceAndInHospitalItem
     */
    class ItemAndPriceAndInHospitalItemRegisterInteractor implements ItemAndPriceAndInHospitalItemRegisterInputPortInterface
    {
        /** @var ItemAndPriceAndInHospitalItemRegisterOutputPortInterface */
        private ItemAndPriceAndInHospitalItemRegisterOutputPortInterface $outputPort;

        /** @var ItemAndPriceAndInHospitalItemRepositoryInterface */
        private ItemAndPriceAndInHospitalItemRepositoryInterface $ItemAndPriceAndInHospitalItemRepository;

        /**
         * ItemAndPriceAndInHospitalItemRegisterInteractor constructor.
         * @param ItemAndPriceAndInHospitalItemRegisterOutputPortInterface $outputPort
         */
        public function __construct(
            ItemAndPriceAndInHospitalItemRegisterOutputPortInterface $outputPort ,
            ItemAndPriceAndInHospitalItemRepositoryInterface $ItemAndPriceAndInHospitalItemRepository
        )
        {
            $this->outputPort = $outputPort;
            $this->ItemAndPriceAndInHospitalItemRepository = $ItemAndPriceAndInHospitalItemRepository;
        }

        /**
         * @param ItemAndPriceAndInHospitalItemRegisterInputData $inputData
         */
        public function handle(ItemAndPriceAndInHospitalItemRegisterInputData $inputData)
        {
            $ItemAndPriceAndInHospitalItem = $this->ItemAndPriceAndInHospitalItemRepository->saveToArray(
                (new TenantId($inputData->tenantId)) ,
                (new HospitalId($inputData->hospitalId)) ,
                (array)$inputData->input
            );
            $this->outputPort->output(new ItemAndPriceAndInHospitalItemRegisterOutputData($ItemAndPriceAndInHospitalItem));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Item {

    use stdClass;

    /**
     * Class ItemAndPriceAndInHospitalItemRegisterInputData
     * @package JoyPla\Application\InputPorts\Api\Item
     */
    class ItemAndPriceAndInHospitalItemRegisterInputData
    {
        /**
         * ItemAndPriceAndInHospitalItemRegisterInputData constructor.
         */
        public function __construct(string $tenantId, string $hospitalId, array $input)
        {
            $this->tenantId = $tenantId;
            $this->hospitalId = $hospitalId;
            $this->input = new stdClass();
            $this->input->itemName = $input['itemName'];
            $this->input->category = $input['category'];
            $this->input->smallCategory = $input['smallCategory'];
            $this->input->itemCode = $input['itemCode'];
            $this->input->itemStandard = $input['itemStandard'];
            $this->input->itemJANCode = $input['itemJANCode'];
            $this->input->makerName = $input['makerName'];
            $this->input->catalogNo = $input['catalogNo'];
            $this->input->serialNo = $input['serialNo'];
            $this->input->lotManagement = $input['lotManagement'];
            $this->input->officialFlag = $input['officialFlag'];
            $this->input->officialprice = $input['officialprice'];
            $this->input->officialpriceOld = $input['officialpriceOld'];
            $this->input->quantity = $input['quantity'];
            $this->input->quantityUnit = $input['quantityUnit'];
            $this->input->itemUnit = $input['itemUnit'];
            $this->input->minPrice = $input['minPrice'];
            $this->input->distributorId = $input['distributorId'];
            $this->input->distributorMCode = $input['distributorMCode'];
            $this->input->price = $input['price'];
            $this->input->unitPrice = $input['unitPrice'];
            $this->input->medicineCategory = $input['medicineCategory'];
            $this->input->homeCategory = $input['homeCategory'];
            $this->input->measuringInst = $input['measuringInst'];
            $this->input->notice = $input['notice'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Item
    */
    interface ItemAndPriceAndInHospitalItemRegisterInputPortInterface
    {
        /**
         * @param ItemAndPriceAndInHospitalItemRegisterInputData $inputData
         */
        function handle(ItemAndPriceAndInHospitalItemRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Item {

    use Collection;
    use JoyPla\Enterprise\Models\Item;

    /**
     * Class ItemAndPriceAndInHospitalItemRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\ItemAndPriceAndInHospitalItem;
     */
    class ItemAndPriceAndInHospitalItemRegisterOutputData
    {
        /**
         * ItemAndPriceAndInHospitalItemRegisterOutputData constructor.
         */
        public function __construct(object $result)
        {
            $this->ItemAndPriceAndInHospitalItems = $result;
        }
    }

    /**
     * Interface ItemAndPriceAndInHospitalItemRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\ItemAndPriceAndInHospitalItem;
    */
    interface ItemAndPriceAndInHospitalItemRegisterOutputPortInterface
    {
        /**
         * @param ItemAndPriceAndInHospitalItemRegisterOutputData $outputData
         */
        function output(ItemAndPriceAndInHospitalItemRegisterOutputData $outputData);
    }
}