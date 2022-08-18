<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\ReceivedReturn {

    use JoyPla\Application\InputPorts\Api\ReceivedReturn\ReturnShowInputData;
    use JoyPla\Application\InputPorts\Api\ReceivedReturn\ReturnShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\ReceivedReturn\ReturnShowOutputData;
    use JoyPla\Application\OutputPorts\Api\ReceivedReturn\ReturnShowOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ReturnRepositoryInterface;

    /**
     * Class ReturnShowInteractor
     * @package JoyPla\Application\Interactors\Api\Return
     */
    class ReturnShowInteractor implements ReturnShowInputPortInterface
    {
        /** @var ReturnShowOutputPortInterface */
        private ReturnShowOutputPortInterface $outputPort;

        /** @var ReturnRepositoryInterface */
        private ReturnRepositoryInterface $returnRepository;

        /**
         * ReturnShowInteractor constructor.
         * @param ReturnShowOutputPortInterface $outputPort
         */
        public function __construct(ReturnShowOutputPortInterface $outputPort , ReturnRepositoryInterface $returnRepository)
        {
            $this->outputPort = $outputPort;
            $this->returnRepository = $returnRepository;
        }

        /**
         * @param ReturnShowInputData $inputData
         */
        public function handle(ReturnShowInputData $inputData)
        {
            [ $returns , $count ] = $this->returnRepository->search(
                (new HospitalId($inputData->user->hospitalId)),
                $inputData->search
            );
            $this->outputPort->output(new ReturnShowOutputData($returns , $count));
        }
    }
}


/*** 
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\ReceivedReturn {

    use Auth;
    use stdClass;

    /**
     * Class ReturnShowInputData
     * @package JoyPla\Application\InputPorts\Api\Return
     */
    class ReturnShowInputData
    {
        /**
         * ReturnShowInputData constructor.
         */
        public function __construct(Auth $user , array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->itemName = $search['itemName'];
            $this->search->makerName = $search['makerName'];
            $this->search->itemCode = $search['itemCode'];
            $this->search->itemStandard = $search['itemStandard'];
            $this->search->itemJANCode = $search['itemJANCode'];
            $this->search->registerDate = $search['registerDate'];
            $this->search->returnDate = $search['returnDate'];
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->perPage= $search['perPage'];
            $this->search->currentPage= $search['currentPage'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Return
    */
    interface ReturnShowInputPortInterface
    {
        /**
         * @param ReturnShowInputData $inputData
         */
        function handle(ReturnShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\ReceivedReturn {

    use JoyPla\Enterprise\Models\ReturnData;

    /**
     * Class ReturnShowOutputData
     * @package JoyPla\Application\OutputPorts\Api\Return;
     */
    class ReturnShowOutputData
    {
        /** @var string */

        /**
         * ReturnShowOutputData constructor.
         */
        
        public function __construct(array $returns , int $count)
        {
            $this->count = $count;
            $this->returns = array_map(
                function( ReturnData $return)
                {
                    return $return->toArray();
                },$returns
            );
        }
    }

    /**
     * Interface ReturnShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Return;
    */
    interface ReturnShowOutputPortInterface
    {
        /**
         * @param ReturnShowOutputData $outputData
         */
        function output(ReturnShowOutputData $outputData);
    }
} 