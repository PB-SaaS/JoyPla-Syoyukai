<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\Accountant;
use JoyPla\Enterprise\Models\AccountantId;
use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\DateYearMonthDay;
use JoyPla\Enterprise\Models\DistributorId;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use stdClass;

use function App\Lib\pager;

class AccountantRepository implements AccountantRepositoryInterface
{
    public function register(Accountant $accountant)
    {
        $accountant = $accountant->toArray();
        ModelRepository::getAccountantInstance()->create([
            'registTime' => 'now',
            'hospitalId' => $accountant['hospitalId'],
            'divisionId' => $accountant['divisionId'],
            'distributorId' => $accountant['distributorId'],
            'accountantId' => $accountant['accountantId'],
            'accountantDate' => $accountant['accountantDate'],
            'orderNumber' => $accountant['orderId'],
            'receivingNumber' => $accountant['receivedId'],
        ]);
    }

    public function search(HospitalId $hospitalId, object $search)
    {
        $accountantInstance = ModelRepository::getAccountantInstance()
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
            ->orderBy('accountantDate', 'desc')
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if ($historys->getData()->count() == 0) {
            return [[], 0];
        }
        /*
        10limit 
        1page = 1 ... 2 ... 3 ....... 10

        10limit
        2page = 11 ... 12 ... 13 ... 14 ... 15
        */

        $count =
            $search->currentPage - 1 > 0
                ? $search->perPage * ($search->currentPage - 1) + 1
                : 1;
        $accountants = [];

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
            $accountant = new stdClass();
            $accountant->id = $history->id;
            $accountant->accountantId = $history->accountantId;
            $accountant->accountantDate = $history->accountantDate;
            $accountant->hospitalId = $history->hospitalId;
            $accountant->divisionId = $history->divisionId;
            $accountant->distributorId = $history->distributorId;
            $accountant->orderId = $history->orderNumber;
            $accountant->receivedId = $history->receivingNumber;

            $accountant->_division = array_find($divisions, function (
                $division
            ) use ($history) {
                return $division->divisionId == $history->divisionId;
            });

            $accountant->_distributor = array_find($distributors, function (
                $distributor
            ) use ($history) {
                return $distributor->distributorId == $history->distributorId;
            });

            $accountant->_id = $count;
            $accountants[] = $accountant;
            $count++;
        }

        return [$accountants, $historys->getTotal()];
    }
}

interface AccountantRepositoryInterface
{
}
