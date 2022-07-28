<?php

namespace JoyPla\Enterprise\Traits;

trait ValueObjectTrait {
    
    public function value()
    {
        return $this->value;
    }

    public function equal($value)
    {
        return $this->value === $value;
    }
    
    public function isEmpty()
    {
        return ( $this->value === null || $this->value === "" );
    }
} 