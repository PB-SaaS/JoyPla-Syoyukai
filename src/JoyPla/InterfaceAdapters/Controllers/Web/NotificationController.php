<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Http\Controller;
use framework\Http\View;
use JoyPla\Enterprise\Models\Notification;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class NotificationController extends Controller
{
    public function show($vars)
    {
        $body = View::forge('html/Notification/Show', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function index($vars)
    {
        $notification = ModelRepository::getNotificationInstance()
            ->where('noticeId', $vars['notificationId'])
            ->get();
        $notification = $notification->first();

        $notification = Notification::create($notification);
        $viewModel = $notification->toArray();

        $body = View::forge(
            'html/Notification/Index',
            compact('viewModel'),
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}
