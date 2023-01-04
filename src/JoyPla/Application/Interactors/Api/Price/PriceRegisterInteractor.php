<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Price {

    use JoyPla\Application\InputPorts\Api\Price\PriceRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Price\PriceRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Price\PriceRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Price\PriceRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\PriceRepositoryInterface;

    /**
     * Class PriceRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\Price
     */
    class PriceRegisterInteractor implements PriceRegisterInputPortInterface
    {
        /** @var PriceRegisterOutputPortInterface */
        private PriceRegisterOutputPortInterface $outputPort;

        /** @var PriceRepositoryInterface */
        private PriceRepositoryInterface $PriceRepository;

        /**
         * PriceRegisterInteractor constructor.
         * @param PriceRegisterOutputPortInterface $outputPort
         */
        public function __construct(PriceRegisterOutputPortInterface $outputPort , PriceRepositoryInterface $PriceRepository)
        {
            $this->outputPort = $outputPort;
            $this->PriceRepository = $PriceRepository;
        }

        /**
         * @param PriceRegisterInputData $inputData
         */
        public function handle(PriceRegisterInputData $inputData)
        {
            $Price = $this->PriceRepository->saveToArray(
                (new HospitalId($inputData->hospitalId)) ,
                (new ItemId($inputData->itemId)) ,
                $inputData->input
            );
            $this->outputPort->output(new PriceRegisterOutputData($Price));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Price {

    use stdClass;

    /**
     * Class PriceRegisterInputData
     * @package JoyPla\Application\InputPorts\Api\Price
     */
    class PriceRegisterInputData
    {
        /**
         * PriceRegisterInputData constructor.
         */
        public function __construct(string $hospitalId, array $input)
        {
            $this->hospitalId = $hospitalId;
            $this->itemId = $input['itemId'];
            $this->input = new stdClass();
            $this->input->distributorId = $input['distributorId'];
            $this->input->distributorMCode = $input['distributorMCode'];
            $this->input->quantity = $input['quantity'];
            $this->input->quantityUnit = $input['quantityUnit'];
            $this->input->itemUnit= $input['itemUnit'];
            $this->input->price= $input['price'];
            $this->input->unitPrice= $input['unitPrice'];
            $this->input->notice= $input['notice'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Price
    */
    interface PriceRegisterInputPortInterface
    {
        /**
         * @param PriceRegisterInputData $inputData
         */
        function handle(PriceRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Price {

    use Collection;
    use JoyPla\Enterprise\Models\Price;

    /**
     * Class PriceRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\Price;
     */
    class PriceRegisterOutputData
    {
        /**
         * PriceRegisterOutputData constructor.
         */
        public function __construct(array $result)
        {
            $this->Prices = $result;
        }
    }

    /**
     * Interface PriceRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Price;
    */
    interface PriceRegisterOutputPortInterface
    {
        /**
         * @param PriceRegisterOutputData $outputData
         */
        function output(PriceRegisterOutputData $outputData);
    }
}