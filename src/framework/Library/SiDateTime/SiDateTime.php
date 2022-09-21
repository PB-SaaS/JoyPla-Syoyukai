<?php

namespace framework\Library;

use DateTime;
use DateTimeZone;

class SiDateTime extends DateTime
{
    private ?DateTime $datetime = null;

    public function __construct($date, $timezone = '')
    {
        $zone = null;
        if (!empty($timezone)) {
            $zone = new DateTimeZone($timezone);
        }
        $this->datetime = new DateTime($date, $zone);
    }

    public static function hasFormat($date, $format)
    {
        return (DateTime::createFromFormat($format, $date));
    }

    public static function now($timezone = '')
    {
        return new self('now', $timezone);
    }

    public function toDateString()
    {
        return $this->datetime->format('Y-m-d');
    }

    public function toDateTimeString()
    {
        return $this->datetime->format('Y-m-d H:i:s');
    }

    public function toJapanDateString()
    {
        return $this->datetime->format('Y年m月d秒');
    }

    public function toJapanDateTimeString()
    {
        return $this->datetime->format('Y年m月d秒 H時i分s秒');
    }

    public function __set($name, $value)
    {
        $datetime = clone $this->datetime;
        $setting = [
            'year' => $datetime->setDate($value, $this->month, $this->day),
            'month' => $datetime->setDate($this->year, $value, $this->day),
            'day' => $datetime->setDate($this->year, $this->month, $value),
            'hour' => $datetime->setDate($value, $this->minute, $this->second, $this->micro),
            'minute' => $datetime->setDate($this->hour,  $value, $this->second, $this->micro),
            'second' => $datetime->setDate($this->hour, $this->minute, $value, $this->micro),
            'micro' => $datetime->setDate($this->hour, $this->minute, $this->second, $value),
            'timestamp' => $datetime->setTimestamp($value),
            'timezone' => $datetime->setTimezone($value),
        ];

        if (array_key_exists($name, $setting)) {
            $this->datetime = $setting[$name];
        } else {
            echo "Error!!! Hint" . PHP_EOL;
            echo "Use Key" . PHP_EOL;
            foreach ($setting as $k => $v) {
                echo $k . PHP_EOL;
            }
        }
    }

    public function __get($name)
    {
        $setting = [
            'year' => $this->datetime->format('Y'),
            'month' => $this->datetime->format('m'),
            'day' => $this->datetime->format('d'),
            'hour' => $this->datetime->format('H'),
            'minute' => $this->datetime->format('i'),
            'second' => $this->datetime->format('s'),
            'micro' => $this->datetime->format('u'),
            'dayOfWeek' => $this->datetime->format('w'),
            'dayOfWeekIso' => $this->datetime->format('N'),
            'dayOfYear' => $this->datetime->format('z'),
            'weekNumberInMonth' => $this->datetime->format('z'),
            'daysInMonth' => $this->datetime->format('t'),
            'timestamp' => $this->datetime->getTimestamp(),
            'quarter' => ceil($this->month / 3),
            'timezone' => $this->datetime->getTimezone(),
        ];

        if (array_key_exists($name, $setting)) {
            return $setting[$name];
        }

        echo "Error!!! Hint" . PHP_EOL;
        echo "Use Key" . PHP_EOL;
        foreach ($setting as $k => $v) {
            echo $k . PHP_EOL;
        }
    }
}
