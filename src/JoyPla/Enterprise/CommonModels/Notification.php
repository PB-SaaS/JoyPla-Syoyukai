<?php

namespace JoyPla\Enterprise\CommonModels;

use Collection;
use Exception;

class Notification
{
    public string $registrationTime;
    public string $notificationId;
    public string $title;
    public string $content;
    public string $creator;

    public function __construct(
        string $registrationTime,
        string $notificationId,
        string $title,
        string $content,
        string $creator,
        int $type
        )
    {
        $this->registrationTime = $registrationTime;
        $this->notificationId = $notificationId;
        $this->title = $title;
        $this->content = $content;
        $this->creator = $creator;
        $this->type = $type;        
    }
}
