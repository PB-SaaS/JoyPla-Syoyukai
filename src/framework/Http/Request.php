<?php

namespace framework\Http;

/**
 * Class Request
 *
 * @package App\Http\Message
 */
class Request
{
    public function isSsl(): bool
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }

    public function getHost(): string
    {
        return $_SERVER['SERVER_NAME'];
    }
    
    public function getRequestUri(): string
    {
        //SPIRALはREQUEST_URLを任意設定ができないので POST値でとる
        //return $_SERVER['REQUEST_URI'];
        if(!isset($_POST['path']))
        {
            return "";
        }
        return $_POST['path'];
    }

    public function getQueryParams(): array
    {
        return $_GET;
    }

    public function getRequestBody(): array
    {
        return $_POST;
    }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}