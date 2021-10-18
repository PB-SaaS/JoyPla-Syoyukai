<?php

class ApiResponse {
    public $data = null;
    public $count = 0;
    public $code = 0;
    public $message = null;
    public $header = array();
    public $result = false;

    public function __construct( $data = null ,  $count = 0 ,  $code = 0 , $message = null , $header = array())
    {
        $this->data = $data;
        $this->count = $count;
        $this->code = $code;
        $this->message = $message;
        $this->header = $header;
        if($code == 0)
        {
            $this->result = true;
        }
    }

    public function toJson(): string
    {
        return json_encode(array("data"=> $this->data ,"count"=> $this->count ,"code"=> $this->code ,"message"=> $this->message ,"header"=> $this->header ,"result" => $this->result));
    }
}