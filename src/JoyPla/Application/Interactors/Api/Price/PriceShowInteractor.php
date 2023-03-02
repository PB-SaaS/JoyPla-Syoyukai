<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Price {
    use JoyPla\Application\InputPorts\Api\Price\PriceShowInputData;
    use JoyPla\Application\InputPorts\Api\Price\PriceShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Price\PriceShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Price\PriceShowOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\PriceRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class PriceShowInteractor
     * @package JoyPla\Application\Interactors\Api\Price
     */
    class PriceShowInteractor implements PriceShowInputPortInterface
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
         * @param PriceShowInputData $inputData
         */
        public function handle(PriceShowInputData $inputData)
        {
            [
                $Price,
                $count,
            ] = $this->repositoryProvider
                ->getPriceRepository()
                ->search(
                    new HospitalId($inputData->hospitalId),
                    $inputData->search
                );
            $this->presenterProvider
                ->getPriceShowPresenter()
                ->output(new PriceShowOutputData($Price, $count));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Price {
    use stdClass;

    /**
     * Class PriceShowInputData
     * @package JoyPla\Application\InputPorts\Api\Price
     */
    class PriceShowInputData
    {
        public string $hospitalId;
        public stdClass $search;
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
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
            $this->search->isNotUse = '0';
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Price
     */
    interface PriceShowInputPortInterface
    {
        /**
         * @param PriceShowInputData $inputData
         */
        function handle(PriceShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Price {
    use Collection;
    use JoyPla\Enterprise\Models\Price;

    /**
     * Class PriceShowOutputData
     * @package JoyPla\Application\OutputPorts\Api\Price;
     */
    class PriceShowOutputData
    {
        public array $Price;
        public int $count;
        public function __construct(array $result, int $count)
        {
            $this->Prices = $result;
            $this->count = $count;
        }
    }

    /**
     * Interface PriceShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Price;
     */
    interface PriceShowOutputPortInterface
    {
        /**
         * @param PriceShowOutputData $outputData
         */
        function output(PriceShowOutputData $outputData);
    }
}
