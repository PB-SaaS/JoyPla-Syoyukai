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
    
    public function get( $key )
    {
        if(!isset($this->{$key}))
        {
            return null;
        }
        return $this->{$key};
    }

    public function toJson()
    {
        return json_encode((array) $this , true);
    }

    public function toArray()
    {
        return (array)$this;
    }
}