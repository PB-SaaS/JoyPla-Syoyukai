<?php

class Auth 
{
    public function __construct(String $model)
    {
        global $SPIRAL;
        if(class_exists($model))
        {
            $column = array_merge($model::$fillable,$model::$guarded);
            foreach($column as $f)
            {
                $this->{$f} = $SPIRAL->getContextByFieldTitle($f);
            }
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