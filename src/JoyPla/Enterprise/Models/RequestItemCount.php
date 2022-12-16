<?php

namespace JoyPla\Enterprise\Models;

class RequestItemCount
{
    public function __construct(
        string $recordId,
        HospitalId $hospitalId,
        DivisionId $divisionId,
        InHospitalItemId $inHospitalItemId,
        ItemId $itemId,
        int $quantity = 0
    ) {
        $this->recordId = $recordId;
        $this->hospitalId = $hospitalId;
        $this->divisionId = $divisionId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->itemId = $itemId;
        $this->quantity = $quantity;
    }

    public function toArray()
    {
        return [
            'recordId' => $this->recordId,
            'hospitalId' => $this->hospitalId->value(),
            'divisionId' => $this->divisionId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'itemId' => $this->itemId->value(),
            'quantity' => $this->quantity
        ];
    }
}
