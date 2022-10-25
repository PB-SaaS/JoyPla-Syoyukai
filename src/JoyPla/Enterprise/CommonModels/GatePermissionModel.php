<?php

namespace JoyPla\Enterprise\CommonModels;

use framework\Enterprise\CommonModels\GateInterface;

class GatePermissionModel implements GateInterface

{
    private bool $gate;
    private bool $onlyMyDivision;
    
    public function __construct(
        bool $gate,
        bool $onlyMyDivision
    )
    {
        $this->gate = $gate;
        $this->onlyMyDivision = $onlyMyDivision;
    }

    public function isOnlyMyDivision()
    {
        return $this->onlyMyDivision;
    }

    public function can()
    {
        return $this->gate;
    }
}