<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class AccountantItemRepository implements AccountantItemRepositoryInterface
{
    public function search(HospitalId $hospitalId, object $search)
    {
        $accountantInstance = ModelRepository::getAccountantItemViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->orderBy('id', 'desc');

        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $accountantInstance->orWhere('divisionId', $divisionId);
            }
        }

        if ($search->yearMonth !== '') {
            $yearMonth = new DateYearMonth($search->yearMonth);
            $nextMonth = $yearMonth->nextMonth();
            $accountantInstance->where(
                'accountantDate',
                $yearMonth->format('Y-m-01'),
                '>='
            );
            $accountantInstance->where(
                'accountantDate',
                $nextMonth->format('Y-m-01'),
                '<'
            );
        }

        if ($search->itemName !== '') {
            $accountantInstance->orWhere(
                'itemName',
                '%' . $search->itemName . '%',
                'LIKE'
            );
        }
        if ($search->makerName !== '') {
            $accountantInstance->orWhere(
                'makerName',
                '%' . $search->makerName . '%',
                'LIKE'
            );
        }
        if ($search->itemCode !== '') {
            $accountantInstance->orWhere(
                'itemCode',
                '%' . $search->itemCode . '%',
                'LIKE'
            );
        }
        if ($search->itemStandard !== '') {
            $accountantInstance->orWhere(
                'itemStandard',
                '%' . $search->itemStandard . '%',
                'LIKE'
            );
        }
        if ($search->itemJANCode !== '') {
            $accountantInstance->orWhere(
                'itemJANCode',
                '%' . $search->itemJANCode . '%',
                'LIKE'
            );
        }

        if (
            is_array($search->distributorIds) &&
            count($search->distributorIds) > 0
        ) {
            foreach ($search->distributorIds as $distributorId) {
                $accountantInstance->orWhere('distributorId', $distributorId);
            }
        }

        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $accountantInstance->orWhere('divisionId', $divisionId);
            }
        }

        $historys = $accountantInstance
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if ($historys->getData()->count() == 0) {
            return [[], 0];
        }

        $count =
            $search->currentPage - 1 > 0
                ? $search->perPage * ($search->currentPage - 1) + 1
                : 1;

        if ($historys->getData()->count() > 0) {
            $division = ModelRepository::getDivisionInstance()->where(
                'hospitalId',
                $hospitalId->value()
            );
            foreach ($historys->getData()->all() as $history) {
                $division->orWhere('divisionId', $history->divisionId);
            }
            $divisions = $division->get();

            $distributor = ModelRepository::getDistributorInstance()->where(
                'hospitalId',
                $hospitalId->value()
            );
            foreach ($historys->getData()->all() as $history) {
                $distributor->orWhere('distributorId', $history->distributorId);
            }
            $distributors = $distributor->get();
        }

        foreach ($historys->getData()->all() as $history) {
            $history->_division = array_find($divisions, function (
                $division
            ) use ($history) {
                return $division->divisionId == $history->divisionId;
            });

            $history->_distributor = array_find($distributors, function (
                $distributor
            ) use ($history) {
                return $distributor->distributorId == $history->distributorId;
            });

            $history->_id = $count;
            $count++;
        }

        return [$historys->getData(), $historys->getTotal()];
    }
}

interface AccountantItemRepositoryInterface
{
}
