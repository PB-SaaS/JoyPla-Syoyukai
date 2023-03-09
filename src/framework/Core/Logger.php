<?php

use App\Lib\LoggingSpiralv2;

class Logger
{
    // ログレベル
    public const LOG_LEVEL_ERROR = 0;
    public const LOG_LEVEL_WARN = 1;
    public const LOG_LEVEL_INFO = 2;
    public const LOG_LEVEL_DEBUG = 3;

    private static $singleton;
    private $object;

    /**
     * コンストラクタ
     */
    public function __construct(LoggingObject $object)
    {
        $this->object = $object;
    }

    /**
     * ログ出力する
     * @param string $level ログレベル
     * @param string $msg メッセージ
     */
    public function out(array $data)
    {
        $this->object->insert($data);
    }

    public function outBulk(array $data)
    {
        $this->object->bulkInsert($data);
    }

    /**
     * 現在時刻を取得する
     * @return string 現在時刻
     */
    public static function getTime()
    {
        $miTime = explode('.', microtime(true));
        $msec = str_pad(substr($miTime[1], 0, 3), 3, '0');
        $time = date('Y-m-d H:i:s', $miTime[0]) . '.' . $msec;
        return $time;
    }

    public function getLevel()
    {
        return $this->object->logLevel;
    }
}

interface LoggingObject
{
    public function insert(array $data);
    public function bulkInsert(array $data);
}
