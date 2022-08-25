<?php

class HttpRequestParameter extends stdClass
{
    public function __construct()
    {
    }

    public function set( $key , $value )
    {
        $this->{$key} = $value;
    }

    public function toJson()
    {
        return json_encode((array) $this , true);
    }
}