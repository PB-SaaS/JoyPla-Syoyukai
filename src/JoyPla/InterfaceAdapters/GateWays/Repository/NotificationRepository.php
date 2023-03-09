<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use Collection;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use stdClass;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function search(stdClass $search)
    {
        $notification = ModelRepository::getNotificationInstance()
            ->orderBy('registrationTime', 'desc')
            ->page($search->page)
            ->paginate($search->limit);
        return [
            array_map(function (Collection $n) {
                return $n;
            }, $notification->getData()->all()),
            count($notification->getData()->all()),
        ];
    }
}

interface NotificationRepositoryInterface
{
    public function search(stdClass $search);
}
