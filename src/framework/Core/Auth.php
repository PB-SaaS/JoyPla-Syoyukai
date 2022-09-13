<?php

use framework\SpiralConnecter\SpiralDB;

class Auth extends stdClass
{
    public function __construct($dbTitle , $fields)
    {
        if(class_exists('Spiral'))
        {
            global $SPIRAL;
            foreach($fields as $f)
            {
                $this->{$f} = $SPIRAL->getContextByFieldTitle($f);
            }
        }
        else 
        {
            //SpiralDB::title(self::$dbTitle);
        }
    }

    public function collectMerge(Collection $collection , $primaryKey)
    {
        if($this->{$primaryKey} === $collection->{$primaryKey})
        {
            foreach($collection->all() as $key => $val)
            {
                if(! isset($this->{$key})) 
                { 
                    $this->{$key} = $val;
                }
            }
        }
        return $this;
    }
} 