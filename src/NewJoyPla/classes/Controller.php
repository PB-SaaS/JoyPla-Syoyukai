<?php

class Controller
{
    private $name   = 'controller';
    protected function __construct()
    {
    }
    // ログの出力
    public function logging($message, string $file_name = 'app.log')
    {
        //$Logger = new Logger('logger');
        //$Logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/' . $file_name, Logger::INFO));
        //$Logger->addInfo($message);
    }

    // ビューの生成
    public function view(string $template, array $param = array() , bool $filter = true): View
    {
        return new \View($template , $param , $filter);
    }
}