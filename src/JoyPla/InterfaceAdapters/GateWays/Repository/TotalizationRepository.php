<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use framework\SpiralConnecter\SpiralDB;
use Collection;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\TotalRequestItem;
use JoyPla\Enterprise\Models\TotalRequest;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class TotalizationRepository implements TotalizationRepositoryInterface
{
    public function search(HospitalId $hospitalId, object $search)
    {
        $byDivisionSearchFlag = false;

        $division = SpiralDB::title('NJ_divisionDB')->value([
            'registrationTime',
            'divisionId',
            'hospitalId',
            'divisionName',
            'divisionType',
            'deleteFlag',
            'authkey',
            'deliveryDestCode',
        ]);

        $division->where('hospitalId', $hospitalId->value());

        $totalzationByInHPItemInstance = ModelRepository::getTotalRequestByInHpItemViewIntance()
            ->where('hospitalId', $hospitalId->value())
            ->where('requestQuantity', 0, '>');
        $totalzationByDivisionInstance = ModelRepository::getTotalRequestByDivisionInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('requestQuantity', 0, '>');

        if (
            is_array($search->sourceDivisionIds) &&
            count($search->sourceDivisionIds) > 0
        ) {
            foreach ($search->sourceDivisionIds as $sourceDivisionId) {
                $totalzationByDivisionInstance->orWhere(
                    'sourceDivisionId',
                    $sourceDivisionId
                );
            }
            $byDivisionSearchFlag = true;
        }

        if ($byDivisionSearchFlag) {
            $totalzationByDivisions = $totalzationByDivisionInstance
                ->orderBy('id', 'asc')
                ->get();
            if ((int) $totalzationByDivisions->count() === 0) {
                return [[], 0];
            }
            $recordIds = [];
            foreach ($totalzationByDivisions->all() as $totalzationByDivision) {
                $recordIds[] = $totalzationByDivision->recordId;
                $division->orWhere(
                    'divisionId',
                    $totalzationByDivision->sourceDivisionId
                );
                $division->orWhere(
                    'divisionId',
                    $totalzationByDivision->targetDivisionId
                );
            }
            $byDivisionRecordIds = array_unique($recordIds);
            foreach ($byDivisionRecordIds as $recordId) {
                $totalzationByInHPItemInstance->orWhere('recordId', $recordId);
            }
        }

        if ($search->itemName) {
            $totalzationByInHPItemInstance->orWhere(
                'itemName',
                '%' . $search->itemName . '%',
                'LIKE'
            );
        }
        if ($search->makerName) {
            $totalzationByInHPItemInstance->orWhere(
                'makerName',
                '%' . $search->makerName . '%',
                'LIKE'
            );
        }
        if ($search->itemCode) {
            $totalzationByInHPItemInstance->orWhere(
                'itemCode',
                '%' . $search->itemCode . '%',
                'LIKE'
            );
        }
        if ($search->itemStandard) {
            $totalzationByInHPItemInstance->orWhere(
                'itemStandard',
                '%' . $search->itemStandard . '%',
                'LIKE'
            );
        }
        if ($search->itemJANCode) {
            $totalzationByInHPItemInstance->orWhere(
                'itemJANCode',
                '%' . $search->itemJANCode . '%',
                'LIKE'
            );
        }
        if (
            is_array($search->targetDivisionIds) &&
            count($search->targetDivisionIds) > 0
        ) {
            foreach ($search->targetDivisionIds as $targetDivisionId) {
                $totalzationByInHPItemInstance->orWhere(
                    'targetDivisionId',
                    $targetDivisionId
                );
            }
        }

        $totalzationByInHPItems = $totalzationByInHPItemInstance
            ->orderBy('id', 'asc')
            ->page($search->currentPage)
            ->paginate($search->perPage);
        if ((int) count($totalzationByInHPItems->getData()->all()) === 0) {
            return [[], 0];
        }

        foreach (
            $totalzationByInHPItems->getData()->all()
            as $totalzationByInHPItem
        ) {
            $division->orWhere(
                'divisionId',
                $totalzationByInHPItem->targetDivisionId
            );
        }

        $totalRequestItems = [];

        if ($byDivisionSearchFlag) {
            $division = $division->get()->all();

            foreach (
                $totalzationByInHPItems->getData()->all()
                as $totalzationByInHPItem
            ) {
                $target_division_find_key = array_search(
                    $totalzationByInHPItem->targetDivisionId,
                    collect_column($division, 'divisionId')
                );
                $totalzationByInHPItem->set(
                    'divisionId',
                    $totalzationByInHPItem->targetDivisionId
                );
                $totalzationByInHPItem->set(
                    'divisionName',
                    htmlspecialchars_decode(
                        $division[$target_division_find_key]->divisionName,
                        ENT_QUOTES
                    )
                );

                $totalRequestItem = TotalRequestItem::create(
                    $totalzationByInHPItem
                );

                foreach (
                    $totalzationByDivisions->all()
                    as $totalzationByDivision
                ) {
                    if (
                        $totalRequestItem->getRecordId() ===
                        $totalzationByDivision->recordId
                    ) {
                        $source_division_find_key = array_search(
                            $totalzationByDivision->sourceDivisionId,
                            collect_column($division, 'divisionId')
                        );
                        $target_division_find_key = array_search(
                            $totalzationByDivision->targetDivisionId,
                            collect_column($division, 'divisionId')
                        );
                        $sourceDivision = new Collection();
                        $sourceDivision->hospitalId = $hospitalId->value();
                        $sourceDivision->divisionId =
                            $totalzationByDivision->sourceDivisionId;
                        $sourceDivision->divisionName = htmlspecialchars_decode(
                            $division[$source_division_find_key]->divisionName,
                            ENT_QUOTES
                        );
                        $targetDivision = new Collection();
                        $targetDivision->hospitalId = $hospitalId->value();
                        $targetDivision->divisionId =
                            $totalzationByDivision->targetDivisionId;
                        $targetDivision->divisionName = htmlspecialchars_decode(
                            $division[$target_division_find_key]->divisionName,
                            ENT_QUOTES
                        );
                        $totalzationByDivision->set(
                            'sourceDivision',
                            $sourceDivision
                        );
                        $totalzationByDivision->set(
                            'targetDivision',
                            $targetDivision
                        );

                        $totalRequestItem = $totalRequestItem->addTotalRequest(
                            TotalRequest::create($totalzationByDivision)
                        );
                    }
                }
                $totalRequestItems[] = $totalRequestItem;
            }

            return [
                $totalRequestItems,
                (int) $totalzationByInHPItems->getData()->count(),
            ];
        }

        /*  請求元指定なし  */
        foreach (
            $totalzationByInHPItems->getData()->all()
            as $totalzationByInHPItem
        ) {
            $totalzationByDivisionInstance->orWhere(
                'recordId',
                $totalzationByInHPItem->recordId
            );
        }
        $totalzationByDivisions = $totalzationByDivisionInstance->get();

        if ((int) $totalzationByDivisions->count() === 0) {
            return [[], 0];
        }

        foreach ($totalzationByDivisions->all() as $totalzationByDivision) {
            $division->orWhere(
                'divisionId',
                $totalzationByDivision->sourceDivisionId
            );
            $division->orWhere(
                'divisionId',
                $totalzationByDivision->targetDivisionId
            );
        }

        $division = $division->get()->all();

        foreach (
            $totalzationByInHPItems->getData()->all()
            as $totalzationByInHPItem
        ) {
            $target_division_find_key = array_search(
                $totalzationByInHPItem->targetDivisionId,
                collect_column($division, 'divisionId')
            );
            $totalzationByInHPItem->set(
                'divisionId',
                $totalzationByInHPItem->targetDivisionId
            );
            $totalzationByInHPItem->set(
                'divisionName',
                htmlspecialchars_decode(
                    $division[$target_division_find_key]->divisionName,
                    ENT_QUOTES
                )
            );

            $totalRequestItem = TotalRequestItem::create(
                $totalzationByInHPItem
            );

            foreach ($totalzationByDivisions->all() as $totalzationByDivision) {
                if (
                    $totalRequestItem->getRecordId() ===
                    $totalzationByDivision->recordId
                ) {
                    $source_division_find_key = array_search(
                        $totalzationByDivision->sourceDivisionId,
                        collect_column($division, 'divisionId')
                    );
                    $target_division_find_key = array_search(
                        $totalzationByDivision->targetDivisionId,
                        collect_column($division, 'divisionId')
                    );
                    if (
                        $source_division_find_key !== false &&
                        $target_division_find_key !== false
                    ) {
                        $sourceDivision = new Collection();
                        $sourceDivision->hospitalId = $hospitalId->value();
                        $sourceDivision->divisionId =
                            $totalzationByDivision->sourceDivisionId;
                        $sourceDivision->divisionName = htmlspecialchars_decode(
                            $division[$source_division_find_key]->divisionName,
                            ENT_QUOTES
                        );
                        $targetDivision = new Collection();
                        $targetDivision->hospitalId = $hospitalId->value();
                        $targetDivision->divisionId =
                            $totalzationByDivision->targetDivisionId;
                        $targetDivision->divisionName = htmlspecialchars_decode(
                            $division[$target_division_find_key]->divisionName,
                            ENT_QUOTES
                        );
                        $totalzationByDivision->set(
                            'sourceDivision',
                            $sourceDivision
                        );
                        $totalzationByDivision->set(
                            'targetDivision',
                            $targetDivision
                        );

                        $totalRequestItem = $totalRequestItem->addTotalRequest(
                            TotalRequest::create($totalzationByDivision)
                        );
                    }
                }
            }
            $totalRequestItems[] = $totalRequestItem;
        }

        return [
            $totalRequestItems,
            (int) $totalzationByInHPItems->getData()->count(),
        ];
    }
}

interface TotalizationRepositoryInterface
{
    public function search(HospitalId $hospitalId, object $search);
}
