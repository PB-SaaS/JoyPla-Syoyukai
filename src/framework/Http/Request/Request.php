<?php

namespace framework\Http;

use Auth;

/**
 * Class Request
 *
 * @package App\Http\Message
 */
class Request
{
    private array $post = [] ;
    private array $server = [] ;
    private $user;


    public function __construct()
    {
        $this->post = $_POST;
        $this->server = $_SERVER;
    }

    public function isSsl(): bool
    {
        return isset($this->server['HTTPS']) && $this->server['HTTPS'] === 'on';
    }

    public function getHost(): string
    {
        return $this->server['SERVER_NAME'];
    }

    public function setUserModel($model)
    {
        $this->user = new Auth($model);
    }

    public function user()
    {
        return $this->user;
    }
    
    public function getRequestUri(): string
    { 
        //SPIRALはREQUEST_URIを任意設定ができないので POST値でとる
        //return $_SERVER['REQUEST_URI'];
        if(!isset($this->post['path']))
        {
            return ""; 
        }
        return $this->post['path'];
    }

    public function setRequestUri(string $path)
    { 
        $this->set('path' , $path);
    }

    public function getQueryParams(): array
    {
        return $_GET;
    }

    public function getRequestBody(): array
    {
        return self::requestUrldecode($this->post);
    }

    private static function requestUrldecode($v){
        if(is_array($v))
        {
            $result = array();
            foreach($v as $key => $value){
                $result[$key] = self::requestUrldecode($value);
            }
            return $result;
        }
        return urldecode($v);
    }

    public function getMethod(): string
    {
        if(empty($this->get('_method')))
        {
            return $this->server['REQUEST_METHOD'];
        }
        return $this->get('_method');
    }

    public function get($key)
    {
        global $SPIRAL;
        if($SPIRAL === null || ( $SPIRAL->getParam($key) == null )){ return self::requestUrldecode($this->post[$key]); }
        return self::requestUrldecode($SPIRAL->getParam($key));
    }

    public function set($key , $val)
    {
        $this->post[$key] = $val;
    }
}