<?php

namespace JoyPla\Enterprise\Models;

class RequestItemCount
{
    public function __construct(
        string $recordId,
        HospitalId $hospitalId,
        InHospitalItemId $inHospitalItemId,
        ItemId $itemId,
        int $quantity,
        DivisionId $sourceDivisionId,
        DivisionId $targetDivisionId
    ) {
        $this->recordId = $recordId;
        $this->hospitalId = $hospitalId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->itemId = $itemId;
        $this->quantity = $quantity;
        $this->sourceDivisionId = $sourceDivisionId;
        $this->targetDivisionId = $targetDivisionId;
    }

    public function toArray()
    {
        return [
            'recordId' => $this->recordId,
            'hospitalId' => $this->hospitalId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'itemId' => $this->itemId->value(),
            'quantity' => $this->quantity,
            'sourceDivisionId' => $this->sourceDivisionId->value(),
            'targetDivisionId' => $this->targetDivisionId->value()
        ];
    }
}
