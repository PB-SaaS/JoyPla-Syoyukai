<?php

namespace JoyPla\Enterprise\TmpModels;

use Exception;
use JoyPla\Enterprise\Traits\ValueObjectTrait;

class TmpValueObject 
{
    use ValueObjectTrait; 

    private string $value = "";

    public function __construct(string $value)
    {
        if($value === "")
        {
            throw new Exception(self::class . ": Null is not allowed.", 422);
        }
        $this->value = $value;
    }
}