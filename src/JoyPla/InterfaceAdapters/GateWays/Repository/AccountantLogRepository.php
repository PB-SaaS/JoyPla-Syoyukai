<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use JoyPla\Service\Functions\FunctionService;
use stdClass;

class AccountantLogRepository implements AccountantLogRepositoryInterface
{
    public function totalPrice(HospitalId $hospitalId, object $search)
    {
        $accountantInstance = ModelRepository::getAccountantLogViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->orderBy($search->sortColumn, $search->sortDirection);

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

        $items = $accountantInstance
            ->resetValue(['price', 'count', 'taxrate'])
            ->get();

        $totalCount = 0;
        foreach ($items as $item) {
            $totalCount += FunctionService::calculateTotalWithTax(
                $item->price,
                $item->count,
                $item->taxrate
            );
        }

        return $totalCount;
    }

    public function search(HospitalId $hospitalId, object $search)
    {
        $accountantInstance = ModelRepository::getAccountantLogViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->orderBy($search->sortColumn, $search->sortDirection);

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

            $user = ModelRepository::getHospitalUserInstance()->where(
                'hospitalId',
                $hospitalId->value()
            );
            foreach ($historys->getData()->all() as $history) {
                $user->orWhere('id', $history->userId);
            }
            $users = $user->get();
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

            $_user = array_find($users, function ($user) use ($history) {
                return $user->id == $history->userId;
            });

            $history->userName = $_user->name ?? '';

            $history->_id = $count;
            $count++;
        }

        return [$historys->getData(), $historys->getTotal()];
    }

    public function fetchPaginatedDataWithLimit(
        HospitalId $hospitalId,
        object $search,
        object $downloadSetting
    ) {
        $accountantInstance = ModelRepository::getAccountantLogViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->orderBy($search->sortColumn, $search->sortDirection);

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

        $items = collect([]);
        if ($downloadSetting->{'download-range-type'} == '0') {
            $items = $accountantInstance->fetchPaginatedDataWithLimit(
                (int) $downloadSetting->{'download-start-record'},
                (int) $search->perPage,
                (int) $downloadSetting->{'download-max-record'}
            );
        } else {
            $items = $accountantInstance->fetchPaginatedPagesWithLimit(
                (int) $downloadSetting->{'download-start-page'},
                (int) $search->perPage,
                (int) $downloadSetting->{'download-max-page'}
            );
        }

        if ($items->count() == 0) {
            return [];
        }

        $data = [];

        if ($items->count() > 0) {
            $division = ModelRepository::getDivisionInstance()->where(
                'hospitalId',
                $hospitalId->value()
            );
            foreach ($items->all() as $history) {
                $division->orWhere('divisionId', $history->divisionId);
            }
            $divisions = $division->get();

            $distributor = ModelRepository::getDistributorInstance()->where(
                'hospitalId',
                $hospitalId->value()
            );
            foreach ($items->all() as $history) {
                $distributor->orWhere('distributorId', $history->distributorId);
            }

            $user = ModelRepository::getHospitalUserInstance()->where(
                'hospitalId',
                $hospitalId->value()
            );
            $distributors = $distributor->get();

            foreach ($items->all() as $history) {
                $user->orWhere('id', $history->userId);
            }
            $users = $user->get();
        }
        foreach ($items->all() as $item) {
            $division = array_find($divisions, function ($division) use (
                $item
            ) {
                return $division->divisionId == $item->divisionId;
            });

            $distributor = array_find($distributors, function (
                $distributor
            ) use ($item) {
                return $distributor->distributorId == $item->distributorId;
            });

            $user = array_find($users, function ($user) use ($item) {
                return $user->id == $item->userId;
            });

            $item->divisionName = $division->divisionName;
            $item->distributorName = $distributor->distributorName;
            $item->userName = $user->name;

            $data[] = $item;
        }

        return $data;
    }
}
interface AccountantLogRepositoryInterface
{
}
