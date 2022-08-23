<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Notification as SpiralDbNotification;
use Collection;
use JoyPla\Enterprise\CommonModels\Notification;
use stdClass;

class NotificationRepository implements NotificationRepositoryInterface{

    public function search(stdClass $search)
    {
        $notification = ( SpiralDbNotification::sort('registrationTime','desc')->page($search->page)->paginate($search->limit) )->data->all();
        return [ array_map(function(Collection $n){
            return $n;
        },$notification) , count($notification) ];
    }
}

interface NotificationRepositoryInterface 
{
    public function search(stdClass $search);
}