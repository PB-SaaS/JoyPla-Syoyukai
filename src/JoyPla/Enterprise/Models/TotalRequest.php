<?php

namespace JoyPla\Enterprise\Models;

use Collection;

class TotalRequest
{
    private string $recordId;
    private InHospitalItemId $inHospitalItemId;
    private HospitalId $hospitalId;
    private Division $sourceDivision;
    private Division $targetDivision;
    private RequestQuantity $requestQuantity;

    public function __construct(
        string $recordId,
        InHospitalItemId $inHospitalItemId,
        HospitalId $hospitalId,
        Division $sourceDivision,
        Division $targetDivision,
        RequestQuantity $requestQuantity
    ) {
        $this->recordId = $recordId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->hospitalId = $hospitalId;
        $this->sourceDivision = $sourceDivision;
        $this->targetDivision = $targetDivision;
        $this->requestQuantity = $requestQuantity;
    }

    public static function create(Collection $input)
    {
        return new TotalRequest(
            ((string) $input->recordId),
            (new InHospitalItemId($input->inHospitalItemId)),
            (new HospitalId($input->hospitalId)),
            (Division::create($input->sourceDivision)),
            (Division::create($input->targetDivision)),
            (new RequestQuantity($input->requestQuantity))
        );
    }

    public function getRecordId()
    {
        return $this->recordId;
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function getSourceDivision()
    {
        return $this->sourceDivision;
    }

    public function getTargetDivision()
    {
        return $this->targetDivision;
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getRequestQuantity()
    {
        return $this->requestQuantity;
    }

    public function equalDivisions(Division $sourceDivision, Division $targetDivision)
    {
        return (($this->sourceDivision->getDivisionId()->value() === $sourceDivision->getDivisionId()->value()) &&
            ($this->targetDivision->getDivisionId()->value() === $targetDivision->getDivisionId()->value()));
    }

    public function toArray()
    {
        return [
            'recordId' => $this->recordId,
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'hospitalId' => $this->hospitalId->value(),
            'sourceDivision' => $this->sourceDivision->toArray(),
            'targetDivision' => $this->targetDivision->toArray(),
            'requestQuantity' => $this->requestQuantity->value()
        ];
    }
}
