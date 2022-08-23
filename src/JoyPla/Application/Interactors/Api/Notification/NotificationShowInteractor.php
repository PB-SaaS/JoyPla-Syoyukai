<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Notification {

    use JoyPla\Application\InputPorts\Api\Notification\NotificationShowInputData;
    use JoyPla\Application\InputPorts\Api\Notification\NotificationShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Notification\NotificationShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Notification\NotificationShowOutputPortInterface;
    use JoyPla\Enterprise\CommonModels\Notification;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\NotificationRepositoryInterface;

    /**
     * Class NotificationShowInteractor
     * @package JoyPla\Application\Interactors\Api\Notification
     */
    class NotificationShowInteractor implements NotificationShowInputPortInterface
    {
        /** @var NotificationShowOutputPortInterface */
        private NotificationShowOutputPortInterface $outputPort;

        /** @var NotificationRepositoryInterface */
        private NotificationRepositoryInterface $NotificationRepository;

        /**
         * NotificationShowInteractor constructor.
         * @param NotificationShowOutputPortInterface $outputPort
         */
        public function __construct(NotificationShowOutputPortInterface $outputPort , NotificationRepositoryInterface $NotificationRepository)
        {
            $this->outputPort = $outputPort;
            $this->NotificationRepository = $NotificationRepository;
        }

        /**
         * @param NotificationShowInputData $inputData
         */
        public function handle(NotificationShowInputData $inputData)
        {
            [ $notifications , $count ] = $this->NotificationRepository->search($inputData->search);

            $notifications = array_map(function($notification){
                return new Notification(
                    $notification->registrationTime,
                    $notification->noticeId,
                    $notification->title,
                    $notification->content,
                    $notification->creator,
                    (int)$notification->type
                );
            },$notifications);
            $this->outputPort->output(new NotificationShowOutputData($notifications, $count));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Notification {

    use Auth;
    use stdClass;

    /**
     * Class NotificationShowInputData
     * @package JoyPla\Application\InputPorts\Api\Notification
     */
    class NotificationShowInputData
    {
        /**
         * NotificationShowInputData constructor.
         */
        public function __construct($search)
        {
            $this->search = new stdClass();
            $this->search->page = (int)$search['currentPage'];
            $this->search->limit = (int)$search['perPage'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Notification
    */
    interface NotificationShowInputPortInterface
    {
        /**
         * @param NotificationShowInputData $inputData
         */
        function handle(NotificationShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Notification {

    use Collection;
    use JoyPla\Enterprise\CommonModels\Notification;

    /**
     * Class NotificationShowOutputData
     * @package JoyPla\Application\OutputPorts\Api\Notification;
     */
    class NotificationShowOutputData
    {
        /**
         * NotificationShowOutputData constructor.
         */
        public function __construct(array $result , int $count)
        {
            $this->notifications = array_map(function(Notification $n){
                return (array)$n;
            },$result);
            $this->count = $count;
        }
    }

    /**
     * Interface NotificationShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Notification;
    */
    interface NotificationShowOutputPortInterface
    {
        /**
         * @param NotificationShowOutputData $outputData
         */
        function output(NotificationShowOutputData $outputData);
    }
}