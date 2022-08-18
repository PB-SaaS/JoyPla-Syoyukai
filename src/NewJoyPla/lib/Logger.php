<?php


class Logger 
{
    // ログレベル
    const LOG_LEVEL_ERROR = 0;
    const LOG_LEVEL_WARN = 1;
    const LOG_LEVEL_INFO = 2;
    const LOG_LEVEL_DEBUG = 3;


    private static $singleton;
    private $object;

    /**
     * コンストラクタ
     */
    public function __construct(LoggingObject $object) {
        $this->object = $object;
    }

    /**
     * ログ出力する
     * @param string $level ログレベル
     * @param string $msg メッセージ
     */
    public function out( $data) {
        $this->object->insert($data);
    }

    /**
     * 現在時刻を取得する
     * @return string 現在時刻
     */
    public static function getTime() {
        $miTime = explode('.',microtime(true));
        $msec = str_pad(substr($miTime[1], 0, 3) , 3, "0");
        $time = date('Y-m-d H:i:s', $miTime[0]) . '.' .$msec;
        return $time;
    }
}

interface LoggingObject 
{
    public function insert($data);
}