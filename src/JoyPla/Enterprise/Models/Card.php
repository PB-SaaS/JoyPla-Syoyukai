<?php

namespace JoyPla\Enterprise\Models;

class Card
{
    private CardId $cardId;
    private InHospitalItemId $inHospitalItemId;
    private Lot $lot;
    private HospitalId $hospitalId;
    private DivisionId $divisionId;
    private Quantity $quantity;

    public function __construct(
        CardId $cardId,
        InHospitalItemId $inHospitalItemId,
        Lot $lot,
        HospitalId $hospitalId,
        DivisionId $divisionId,
        Quantity $quantity
    ) {
        $this->cardId = $cardId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->lot = $lot;
        $this->hospitalId = $hospitalId;
        $this->divisionId = $divisionId;
        $this->quantity = $quantity;
    }

    public function getCardId()
    {
        return $this->cardId;
    }

    public function setLot(Lot $lot)
    {
        $old = $this->lot;
        $this->lot = $lot;
        $self = clone $this;
        $this->lot = $old;

        return $self;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function toArray()
    {
        return [
            'cardId' => $this->cardId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'lot' => $this->lot->toArray(),
            'hospitalId' => $this->hospitalId->value(),
            'divisionId' => $this->divisionId->value(),
            'quantity' => $this->quantity->toArray(),
        ];
    }
}
