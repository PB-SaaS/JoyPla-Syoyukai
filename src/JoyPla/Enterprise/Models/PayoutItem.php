<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class PayoutItem
{
    private PayoutHId $payoutHId;
    private string $payoutId;
    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private HospitalId $hospitalId;
    private Division $sourceDivision;
    private Division $targetDivision;
    private Quantity $quantity;
    private Price $price;
    private UnitPrice $unitPrice;
    private PayoutQuantity $payoutQuantity;
    private Lot $lot;
    private bool $lotManagement;
    private CardId $card;

    public function __construct(
        PayoutHId $payoutHId,
        string $payoutId,
        InHospitalItemId $inHospitalItemId,
        Item $item,
        HospitalId $hospitalId,
        Division $sourceDivision,
        Division $targetDivision,
        Quantity $quantity,
        Price $price,
        UnitPrice $unitPrice,
        PayoutQuantity $payoutQuantity,
        Lot $lot,
        bool $lotManagement,
        CardId $card
    ) {
        $this->payoutHId = $payoutHId;
        $this->payoutId = $payoutId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->hospitalId = $hospitalId;
        $this->sourceDivision = $sourceDivision;
        $this->targetDivision = $targetDivision;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->unitPrice = $unitPrice;
        $this->payoutQuantity = $payoutQuantity;
        $this->lot = $lot;
        $this->lotManagement = $lotManagement;
        $this->card = $card;
    }

    public static function create(Collection $input)
    {
        return new PayoutItem(
            (new PayoutHId($input->payoutHId)),
            $input->payoutId,
            (new InHospitalItemId($input->inHospitalItemId)),
            (Item::create($input)),
            (new HospitalId($input->hospitalId)),
            (Division::create($input->sourceDivision)),
            (Division::create($input->targetDivision)),
            (Quantity::create($input)),
            (new Price($input->price)),
            (new UnitPrice($input->unitPrice)),
            (new PayoutQuantity((int)$input->payoutQuantity)),
            (Lot::create($input)),
            (int)$input->lotManagement,
            (new CardId((string)$input->card))
        );
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getPrice()
    {
        return $this->price;
    }


    public function getPayoutId()
    {
        return $this->payoutId;
    }

    public function getSourceDivision()
    {
        return $this->sourceDivision;
    }

    public function getTargetDivision()
    {
        return $this->targetDivision;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function equalDivisions(Division $sourceDivision, Division $targetDivision)
    {
        return (($this->sourceDivision->getDivisionId()->value() === $sourceDivision->getDivisionId()->value()) &&
            ($this->targetDivision->getDivisionId()->value() === $targetDivision->getDivisionId()->value()));
    }

    public function setPayoutId(string $payoutId)
    {
        return new PayoutItem(
            $this->payoutHId,
            $payoutId,
            $this->inHospitalItemId,
            $this->item,
            $this->hospitalId,
            $this->sourceDivision,
            $this->targetDivision,
            $this->quantity,
            $this->price,
            $this->unitPrice,
            $this->payoutQuantity,
            $this->lot,
            $this->lotManagement,
            $this->card
        );
    }


    public function price()
    {
        return (float)$this->unitPrice->value() * (float)$this->payoutQuantity->value();
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function getPayoutQuantity()
    {
        return $this->payoutQuantity;
    }

    public function getLot()
    {
        return $this->lot;
    }

    public function getLotManagement(): bool
    {
        return (bool)$this->lotManagement;
    }

    public function getCard()
    {
        return $this->card;
    }

    public function setPayoutQuantity(PayoutQuantity $payoutQuantity)
    {
        return new PayoutItem(
            $this->payoutHId,
            $this->payoutId,
            $this->inHospitalItemId,
            $this->item,
            $this->hospitalId,
            $this->sourceDivision,
            $this->targetDivision,
            $this->quantity,
            $this->price,
            $this->unitPrice,
            $payoutQuantity,
            $this->lot,
            $this->lotManagement,
            $this->card
        );
    }

    public function addPayoutQuantity(PayoutQuantity $quantity)
    {
        return $this->setPayoutQuantity($this->payoutQuantity->add($quantity));
    }


    public function toArray()
    {
        return [
            'payoutHId' => $this->payoutHId->value(),
            'payoutId' => $this->payoutId,
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'hospitalId' => $this->hospitalId->value(),
            'sourceDivision' => $this->sourceDivision->toArray(),
            'targetDivision' => $this->targetDivision->toArray(),
            'quantity' => $this->quantity->toArray(),
            'price' => $this->price->value(),
            'unitPrice' => $this->unitPrice->value(),
            'payoutQuantity' => $this->payoutQuantity->value(),
            'payoutAmount' => $this->price(),
            'lot' => $this->lot->toArray(),
            'lotManagement' => $this->lotManagement,
            'card' => $this->card->value()
        ];
    }
}
