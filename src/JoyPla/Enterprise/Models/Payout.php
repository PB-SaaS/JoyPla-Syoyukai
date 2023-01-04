<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class Payout
{
    private PayoutHId $payoutHId;
    private DateYearMonthDayHourMinutesSecond $registrationTime;
    private array $payoutItems;
    private Hospital $hospital;
    private Division $sourceDivision;
    private Division $targetDivision;

    public function __construct(
        PayoutHId $payoutHId,
        DateYearMonthDayHourMinutesSecond $registrationTime,
        array $payoutItems,
        Hospital $hospital,
        Division $sourceDivision,
        Division $targetDivision
    ) {

        $this->payoutHId = $payoutHId;
        $this->registrationTime = $registrationTime;
        $this->payoutItems = array_map(function (PayoutItem $v) {
            return $v;
        }, $payoutItems);
        $this->hospital = $hospital;
        $this->sourceDivision = $sourceDivision;
        $this->targetDivision = $targetDivision;
    }

    public static function create(Collection $input)
    {
        return new Payout(
            (new PayoutHId($input->payoutNumber)),
            (new DateYearMonthDayHourMinutesSecond($input->registrationTime)),
            [],
            (Hospital::create($input)),
            (Division::create($input->sourceDivision)),
            (Division::create($input->targetDivision))
        );
    }

    public function getHospital()
    {
        return $this->hospital;
    }

    public function isExistPayoutItemId(string $payoutId)
    {
        foreach ($this->payoutItems as $item) {
            if ($item->getPayoutId()->equal($payoutId)) {
                return true;
            }
        }

        return false;
    }

    public function getPayoutHId()
    {
        return $this->payoutHId;
    }

    public function searchPayoutItem(string $payoutId)
    {
        foreach ($this->payoutItems as $item) {
            if ($item->getPayoutId()->equal($payoutId)) {
                return $item;
            }
        }

        return null;
    }

    public function getPayoutItems()
    {
        return $this->payoutItems;
    }

    public function getSourceDivision()
    {
        return $this->sourceDivision;
    }

    public function getTargetDivision()
    {
        return $this->targetDivision;
    }

    public function totalAmount()
    {
        $num = 0;
        foreach ($this->payoutItems as $item) {
            $num += $item->price();
        }
        return $num;
    }

    public function deleteItem(string $payoutId)
    {
        $tmp = $this->payoutItems;
        foreach ($tmp as $key => $payoutItem) {
            if ($payoutItem->getPayoutId()->equal($payoutId)) {
                unset($tmp[$key]);
                break;
            }
        }
        return $this->setPayoutItems(array_values($tmp));
    }

    public function itemCount()
    {
        $array = [];
        foreach ($this->payoutItems as $item) {
            $array[] = $item->getInHospitalItemId()->value();
        }
        return count(array_unique($array));
    }

    public function addPayoutItem(PayoutItem $item)
    {
        $items = $this->payoutItems;
        $flag = false;
        foreach ($items as $key => $payoutItem) {
            if ($payoutItem->getInHospitalItemId()->equal($item->getInHospitalItemId()->value())) {
                $flag = true;
                $items[$key] = $payoutItem->addPayoutQuantity($item->getPayoutQuantity());
                break;
            }
        }
        if (!$flag) {
            $items[] = $item;
        }
        return $this->setPayoutItems($items);
    }

    public function addPayoutItemQuantity(string $payoutId, PayoutQuantity $payoutQuantity)
    {
        $tmp = $this->payoutItems;
        foreach ($tmp as $key => $val) {
            if ($val->getPayoutItemId()->equal($payoutId)) {
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

        return new Payout(
            $this->payoutHId,
            $this->registrationTime,
            $payoutItems,
            $this->hospital,
            $this->sourceDivision,
            $this->targetDivision,
        );
    }

    public function toArray()
    {
        return [
            'payoutHId' => $this->payoutHId->value(),
            'registrationTime' => $this->registDate->value(),
            'payoutItems' => array_map(function (PayoutItem $v) {
                return $v->toArray();
            }, $this->payoutItems),
            'hospital' => $this->hospital->toArray(),
            'sourceDivision' => $this->sourceDivision->toArray(),
            'targetDivision' => $this->targetDivision->toArray(),
            'totalAmount' => $this->totalAmount(),
            'itemCount' => $this->itemCount()
        ];
    }
}
