<?php

class Response {
    public $body = null;
    public $status = 200;
    public $header = array();
    
    public function __construct(mixed $body = null , int $status = 200 , array $header = array())
    {
        $this->body = $body;
        $this->status = $status;
        $this->header = $header;
    }
}