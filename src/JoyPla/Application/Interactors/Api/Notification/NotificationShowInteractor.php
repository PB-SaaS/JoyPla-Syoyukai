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
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class NotificationShowInteractor
     * @package JoyPla\Application\Interactors\Api\Notification
     */
    class NotificationShowInteractor implements
        NotificationShowInputPortInterface
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
         * @param NotificationShowInputData $inputData
         */
        public function handle(NotificationShowInputData $inputData)
        {
            [
                $notifications,
                $count,
            ] = $this->repositoryProvider
                ->getNotificationRepository()
                ->search($inputData->search);

            $notifications = array_map(function ($notification) {
                return new Notification(
                    $notification->registrationTime,
                    $notification->noticeId,
                    $notification->title,
                    $notification->content,
                    $notification->creator,
                    (int) $notification->type
                );
            }, $notifications);
            $this->presenterProvider
                ->getNotificationShowPresenter()
                ->output(
                    new NotificationShowOutputData($notifications, $count)
                );
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
        public stdClass $search;

        public function __construct($search)
        {
            $this->search = new stdClass();
            $this->search->page = (int) $search['currentPage'];
            $this->search->limit = (int) $search['perPage'];
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
        public array $notifications;
        public int $count;

        public function __construct(array $result, int $count)
        {
            $this->notifications = array_map(function (Notification $n) {
                return (array) $n;
            }, $result);
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
