<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class Payout
{
    private PayoutHistoryId $payoutHistoryId;
    private DateYearMonthDay $payoutDate;
    private array $items = [];
    private HospitalId $hospitalId;
    private DivisionId $sourceDivisionId;
    private DivisionId $targetDivisionId;
    private string $sourceDivisionName;
    private string $targetDivisionName;

    public function __construct(
        DateYearMonthDay $payoutDate,
        PayoutHistoryId $payoutHistoryId,
        HospitalId $hospitalId,
        DivisionId $sourceDivisionId,
        string $sourceDivisionName,
        DivisionId $targetDivisionId,
        string $targetDivisionName
    ) {
        $this->payoutDate = $payoutDate;
        $this->payoutHistoryId = $payoutHistoryId;
        $this->hospitalId = $hospitalId;
        $this->sourceDivisionId = $sourceDivisionId;
        $this->targetDivisionId = $targetDivisionId;
        $this->sourceDivisionName = $sourceDivisionName;
        $this->targetDivisionName = $targetDivisionName;
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function isExistPayoutItemId(PayoutItemId $payoutItemId)
    {
        foreach ($this->items as $item) {
            if ($item->getPayoutItemId()->equal($payoutItemId->value())) {
                return true;
            }
        }

        return false;
    }

    public function getPayoutHistoryId()
    {
        return $this->payoutHistoryId;
    }

    public function searchPayoutItem(PayoutItemId $payoutItemId)
    {
        foreach ($this->items as $item) {
            if ($item->getPayoutItemId()->equal($payoutItemId->value())) {
                return $item;
            }
        }

        return null;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getSourceDivisionId()
    {
        return $this->sourceDivisionId;
    }

    public function getTargetDivisionId()
    {
        return $this->targetDivisionId;
    }
    public function equalDivisions(
        DivisionId $sourceDivisionId,
        DivisionId $targetDivisionId
    ) {
        return $this->sourceDivisionId->value() ===
            $sourceDivisionId->value() &&
            $this->targetDivisionId->value() ===
                $targetDivisionId->value();
    }

    public function totalAmount()
    {
        $num = 0;
        foreach ($this->items as $item) {
            $num += $item->price();
        }
        return $num;
    }

    public function deleteItem(PayoutItemId $payoutItemId)
    {
        $tmp = $this->items;
        foreach ($tmp as $key => $payoutItem) {
            if ($payoutItem->getPayoutId()->equal($payoutItemId->value())) {
                unset($tmp[$key]);
                break;
            }
        }
        return $this->setPayoutItems(array_values($tmp));
    }

    public function itemCount()
    {
        $array = [];
        foreach ($this->items as $item) {
            $array[] = $item->getInHospitalItemId()->value();
        }
        return count(array_unique($array));
    }

    public function addPayoutItem(PayoutItem $item , $integrated = true)
    {
        if($integrated){
            $itemLotNumber = $item->getLotNumber()->value();
            $itemLotDate = $item->getLotDate()->value();
            $items = $this->items;
            $flag = false;

            foreach ($items as $key => $payoutItem) {
                $lotNumber = $payoutItem->getLotNumber()->value();
                $lotDate = $payoutItem->getLotDate()->value();
                $card = $payoutItem->getCard()->value();
                if (!$card) {
                    if (($payoutItem->getInHospitalItemId()->equal($item->getInHospitalItemId()->value())) &&
                        ($itemLotNumber === $lotNumber) && ($itemLotDate === $lotDate)
                    ) {
                        $flag = true;
                        $items[$key] = $payoutItem->addPayoutQuantity($item->getPayoutQuantity());
                        break;
                    }
                }
            }

            if (!$flag) {
                $items[] = $item;
            }
        } else {
            $items = $this->items;
            $items[] = $item;
        }
        return $this->setPayoutItems($items);
    }

    public function addPayoutItemQuantity(PayoutItemId $payoutItemId, PayoutQuantity $payoutQuantity)
    {
        $tmp = $this->items;
        foreach ($tmp as $key => $val) {
            if ($val->getPayoutItemId()->equal($payoutItemId->value())) {
                $tmp[$key] = $val->addPayoutQuantity($payoutQuantity);
                break;
            }
        }

        return $this->setPayoutItems($tmp);
    }

    public function setPayoutItems(array $payoutItems)
    {
        $payoutItems = array_map(function (PayoutItem $v) {
            return $v;
        }, $payoutItems);
        $this->items = $payoutItems;
        return $this;
    }

    public function toArray()
    {
        return [
            'payoutHistoryId' => $this->payoutHistoryId->value(),
            'payoutDate' => $this->payoutDate->value(),
            'payoutItems' => array_map(function (PayoutItem $v) {
                return $v->toArray();
            }, $this->items),
            'hospitalId' => $this->hospitalId->value(),
            'sourceDivisionName' => $this->sourceDivisionName,
            'targetDivisionName' => $this->targetDivisionName,
            'sourceDivisionId' => $this->sourceDivisionId->value(),
            'targetDivisionId' => $this->targetDivisionId->value(),
            'totalAmount' => $this->totalAmount(),
            'itemCount' => $this->itemCount()
        ];
    }
}
