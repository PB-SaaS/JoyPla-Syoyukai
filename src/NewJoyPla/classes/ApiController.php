<?php

class ApiController
{
    private $name   = 'ApiController';
    protected function __construct()
    {
        // コントローラーを直接呼ばれてもnewできないように
    }
    // ログの出力
    public function logging($message, string $file_name = 'app.log')
    {
        //$Logger = new Logger('logger');
        //$Logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/' . $file_name, Logger::INFO));
        //$Logger->addInfo($message);
    }

    // ビューの生成
    public function view(string $template, array $param , bool $filter = true): View
    {
        return new \View($template , $param , $filter);
    }
}