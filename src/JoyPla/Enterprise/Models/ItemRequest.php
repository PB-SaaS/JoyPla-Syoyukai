<?php

namespace JoyPla\Enterprise\Models;

use Collection;

class ItemRequest
{
    private RequestHId $requestHId;
    private DateYearMonthDayHourMinutesSecond $registrationTime;
    private DateYearMonthDayHourMinutesSecond $updateTime;
    private array $requestItems;
    private Hospital $hospital;
    private Division $sourceDivision;
    private Division $targetDivision;
    private RequestType $requestType;
    private TextFieldType64Bytes $requestUserName;

    public function __construct(
        RequestHId $requestHId,
        DateYearMonthDayHourMinutesSecond $registrationTime,
        DateYearMonthDayHourMinutesSecond $updateTime,
        array $requestItems,
        Hospital $hospital,
        Division $sourceDivision,
        Division $targetDivision,
        RequestType $requestType,
        TextFieldType64Bytes $requestUserName
    ) {
        $this->requestHId = $requestHId;
        $this->registrationTime = $registrationTime;
        $this->updateTime = $updateTime;
        $this->requestItems = array_map(function (RequestItem $v) {
            return $v;
        }, $requestItems);
        $this->hospital = $hospital;
        $this->sourceDivision = $sourceDivision;
        $this->targetDivision = $targetDivision;
        $this->requestType = $requestType;
        $this->requestUserName = $requestUserName;
    }

    public static function create(Collection $input)
    {
        return new ItemRequest(
            (new RequestHId($input->requestHId)),
            (new DateYearMonthDayHourMinutesSecond($input->registrationTime)),
            (new DateYearMonthDayHourMinutesSecond($input->updateTime)),
            [],
            (Hospital::create($input)),
            (Division::create($input->sourceDivision)),
            (Division::create($input->targetDivision)),
            (new RequestType($input->requestType)),
            (new TextFieldType64Bytes($input->requestUserName))
        );
    }

    public function getHospital()
    {
        return $this->hospital;
    }

    public function getRequestHId()
    {
        return $this->requestHId;
    }

    public function getRequestItems()
    {
        return $this->requestItems;
    }

    public function getSourceDivision()
    {
        return $this->sourceDivision;
    }

    public function getTargetDivision()
    {
        return $this->targetDivision;
    }

    public function equalDivisions(Division $sourceDivision, Division $targetDivision)
    {
        return (($this->sourceDivision->getDivisionId()->value() === $sourceDivision->getDivisionId()->value()) &&
            ($this->targetDivision->getDivisionId()->value() === $targetDivision->getDivisionId()->value()));
    }

    public function totalAmount()
    {
        $num = 0;
        foreach ($this->requestItems as $item) {
            $num += $item->price();
        }
        return $num;
    }

    public function itemCount()
    {
        $array = [];
        foreach ($this->requestItems as $item) {
            $array[] = $item->getInHospitalItemId()->value();
        }
        return count(array_unique($array));
    }

    public function addRequestItem(RequestItem $item)
    {
        $items = $this->requestItems;
        $items[] = $item;
        return $this->setRequestItem($items);
    }

    public function setRequestItem(array $requestItems)
    {
        $requestItems = array_map(function (RequestItem $v) {
            return $v;
        }, $requestItems);

        return new ItemRequest(
            $this->requestHId,
            $this->registrationTime,
            $this->updateTime,
            $requestItems,
            $this->hospital,
            $this->sourceDivision,
            $this->targetDivision,
            $this->requestType,
            $this->requestUserName
        );
    }

    public function setRequestType(RequestType $requestType)
    {
        return new ItemRequest(
            $this->requestHId,
            $this->registrationTime,
            $this->updateTime,
            $this->requestItems,
            $this->hospital,
            $this->sourceDivision,
            $this->targetDivision,
            $requestType,
            $this->requestUserName
        );
    }

    public function existRequestItem(RequestId $requestId)
    {
        foreach ($this->requestItems as $item) {
            if ($item->getRequestId()->equal($requestId->value())) {
                return true;
            }
        }

        return false;
    }

    public function deleteItem(RequestId $requestId)
    {
        $tmp = $this->requestItems;
        foreach ($tmp as $key => $requestItem) {
            if ($requestItem->getRequestId()->equal($requestId->value())) {
                unset($tmp[$key]);
                break;
            }
        }
        return $this->setRequestItem(array_values($tmp));
    }

    public function toArray()
    {
        return [
            'requestHId' => $this->requestHId->value(),
            'registrationTime' => $this->registrationTime->value(),
            'updateTime' => $this->updateTime->value(),
            'requestItems' => array_map(function (RequestItem $v) {
                return $v->toArray();
            }, $this->requestItems),
            'hospital' => $this->hospital->toArray(),
            'sourceDivision' => $this->sourceDivision->toArray(),
            'targetDivision' => $this->targetDivision->toArray(),
            'requestType' => $this->requestType->value(),
            'requestTypeToString' => $this->requestType->toString(),
            'totalAmount' => $this->totalAmount(),
            'itemCount' => $this->itemCount(),
            'requestUserName' => $this->requestUserName->value()
        ];
    }
}
