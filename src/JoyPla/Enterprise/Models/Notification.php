<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class Notification
{
    private DateYearMonthDayHourMinutesSecond $registrationTime;
    private NumberSymbolAlphabet32Bytes $notificationId;
    private TextFieldType128Bytes $title;
    private TextArea8192Bytes $content;
    private TextFieldType128Bytes $creator;

    public function __construct(
        DateYearMonthDayHourMinutesSecond $registrationTime,
        NumberSymbolAlphabet32Bytes $notificationId,
        TextFieldType128Bytes $title,
        TextArea8192Bytes $content,
        TextFieldType128Bytes $creator,
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

    public static function create( Collection $input)
    {
        return new self(
            new DateYearMonthDayHourMinutesSecond($input->registrationTime),
            new NumberSymbolAlphabet32Bytes($input->noticeId),
            new TextFieldType128Bytes($input->title),
            new TextArea8192Bytes($input->content),
            new TextFieldType128Bytes($input->creator),
            (int)$input->type
        );
    }

    public function toArray()
    {
        return [
            'registrationTime' => $this->registrationTime->value(),
            'notificationId' => $this->notificationId->value(),
            'title' => $this->title->value(),
            'content' => $this->content->value(),
            'creator' => $this->creator->value(),
            'type' => $this->type
        ];
    } 
}
