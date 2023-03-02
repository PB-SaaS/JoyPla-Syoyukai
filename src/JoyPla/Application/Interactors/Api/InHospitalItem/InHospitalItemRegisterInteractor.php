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
    use JoyPla\Enterprise\Models\ItemId;
    use JoyPla\Enterprise\Models\PriceId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class InHospitalItemRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\InHospitalItem
     */
    class InHospitalItemRegisterInteractor implements
        InHospitalItemRegisterInputPortInterface
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
         * @param InHospitalItemRegisterInputData $inputData
         */
        public function handle(InHospitalItemRegisterInputData $inputData)
        {
            $inHospitalItem = $this->repositoryProvider
                ->getInHospitalItemRepository()
                ->saveToArray(
                    new HospitalId($inputData->hospitalId),
                    new ItemId($inputData->input->itemId),
                    new PriceId($inputData->input->priceId),
                    $inputData->input
                );
            $this->presenterProvider
                ->getInHospitalItemRegisterPresenter()
                ->output(new InHospitalItemRegisterOutputData($inHospitalItem));
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
        public string $hospitalId;
        public stdClass $input;

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
            $this->input->perPage = $input['perPage'];
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
    /**
     * Class InHospitalItemRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\InHospitalItem;
     */
    class InHospitalItemRegisterOutputData
    {
        public array $InHospitalItems;
        public int $count;

        public function __construct(array $result, int $count)
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
