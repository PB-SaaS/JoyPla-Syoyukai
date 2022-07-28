<?php

class Auth 
{
    public function __construct(String $model)
    {
        global $SPIRAL;
        $column = array_merge($model::$fillable,$model::$guarded);
        foreach($column as $f)
        {
            $this->{$f} = $SPIRAL->getContextByFieldTitle($f);
        }
    }
} 