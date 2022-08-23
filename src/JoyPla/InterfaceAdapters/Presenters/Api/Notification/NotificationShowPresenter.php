<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Notification {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Notification\NotificationShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Notification\NotificationShowOutputPortInterface;

    class NotificationShowPresenter implements NotificationShowOutputPortInterface
    {
        public function output(NotificationShowOutputData $outputData)
        {
            $viewModel = new NotificationShowViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message))->toJson();
        }
    }
        
    /**
     * Class NotificationShowViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Notification
     */
    class NotificationShowViewModel
    {
        /**
         * NotificationShowViewModel constructor.
         * @param NotificationShowOutputData $source
         */
        public function __construct(NotificationShowOutputData $source)
        {
            $this->data = $source->notifications;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = "success";
        }
    }
}
