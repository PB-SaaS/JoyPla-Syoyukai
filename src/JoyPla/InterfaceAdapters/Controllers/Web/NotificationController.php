<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\Notification;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Enterprise\Models\Notification as ModelsNotification;

class NotificationController extends Controller
{
    public function show($vars)
    {
        $body = View::forge('html/Notification/Show', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function index($vars)
    {

        $notification = Notification::where('noticeId',$vars['notificationId'])->get();
        $notification = $notification->data->get(0);

        $notification = ModelsNotification::create($notification);
        $viewModel = $notification->toArray();

        $body = View::forge('html/Notification/Index', compact('viewModel'), false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}
 