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
    use JoyPla\Application\InputPorts\Api\Item\PriceAndInHospitalItemRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Item\PriceAndInHospitalItemRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Item\PriceAndInHospitalItemRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Item\PriceAndInHospitalItemRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\Item;
    use JoyPla\Enterprise\Models\ItemPrice;
    use JoyPla\Enterprise\Models\InHospitalItem;
    use JoyPla\InterfaceAdapters\GateWays\Repository\HospitalRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DistributorRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\PriceRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\PriceAndInHospitalItemRepositoryInterface;

    /**
     * Class PriceAndInHospitalItemRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\PriceAndInHospitalItem
     */
    class PriceAndInHospitalItemRegisterInteractor implements PriceAndInHospitalItemRegisterInputPortInterface
    {
        /** @var PriceAndInHospitalItemRegisterOutputPortInterface */
        private PriceAndInHospitalItemRegisterOutputPortInterface $outputPort;

        /** @var PriceAndInHospitalItemRepositoryInterface */
        private PriceAndInHospitalItemRepositoryInterface $PriceAndInHospitalItemRepository;

        /**
         * PriceAndInHospitalItemRegisterInteractor constructor.
         * @param PriceAndInHospitalItemRegisterOutputPortInterface $outputPort
         */
        public function __construct(
            PriceAndInHospitalItemRegisterOutputPortInterface $outputPort ,
            PriceAndInHospitalItemRepositoryInterface $PriceAndInHospitalItemRepository
        )
        {
            $this->outputPort = $outputPort;
            $this->PriceAndInHospitalItemRepository = $PriceAndInHospitalItemRepository;
        }

        /**
         * @param PriceAndInHospitalItemRegisterInputData $inputData
         */
        public function handle(PriceAndInHospitalItemRegisterInputData $inputData)
        {
            $PriceAndInHospitalItem = $this->PriceAndInHospitalItemRepository->saveToArray(
                (new HospitalId($inputData->hospitalId)) ,
                (new ItemId($inputData->itemId)) ,
                (array)$inputData->input
            );
            $this->outputPort->output(new PriceAndInHospitalItemRegisterOutputData($PriceAndInHospitalItem));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Item {

    use stdClass;

    /**
     * Class PriceAndInHospitalItemRegisterInputData
     * @package JoyPla\Application\InputPorts\Api\Item
     */
    class PriceAndInHospitalItemRegisterInputData
    {
        /**
         * PriceAndInHospitalItemRegisterInputData constructor.
         */
        public function __construct(string $hospitalId, string $itemId, array $input)
        {
            $this->itemId = $itemId;
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
    interface PriceAndInHospitalItemRegisterInputPortInterface
    {
        /**
         * @param PriceAndInHospitalItemRegisterInputData $inputData
         */
        function handle(PriceAndInHospitalItemRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Item {

    use Collection;
    use JoyPla\Enterprise\Models\Item;

    /**
     * Class PriceAndInHospitalItemRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\PriceAndInHospitalItem;
     */
    class PriceAndInHospitalItemRegisterOutputData
    {
        /**
         * PriceAndInHospitalItemRegisterOutputData constructor.
         */
        public function __construct(object $result)
        {
            $this->PriceAndInHospitalItems = $result;
        }
    }

    /**
     * Interface PriceAndInHospitalItemRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\PriceAndInHospitalItem;
    */
    interface PriceAndInHospitalItemRegisterOutputPortInterface
    {
        /**
         * @param PriceAndInHospitalItemRegisterOutputData $outputData
         */
        function output(PriceAndInHospitalItemRegisterOutputData $outputData);
    }
}