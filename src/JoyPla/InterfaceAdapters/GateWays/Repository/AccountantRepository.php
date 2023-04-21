<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\Accountant;
use JoyPla\Enterprise\Models\AccountantId;
use JoyPla\Enterprise\Models\AccountantItem;
use JoyPla\Enterprise\Models\AccountantItemChageLog;
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
            'totalAmount' => $accountant['totalAmount'],
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
            $accountant->totalAmount = $history->totalAmount;

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

    public function findByAccountantId(
        HospitalId $hospitalId,
        AccountantId $accountantId
    ) {
        $accountant = ModelRepository::getAccountantInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('accountantId', $accountantId->value())
            ->get();

        $accountant = $accountant->first();

        $division = ModelRepository::getDivisionInstance()
            ->where('divisionId', $accountant->divisionId)
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();
        $distributor = ModelRepository::getDistributorInstance()
            ->where('distributorId', $accountant->distributorId)
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();

        $accountant = new Accountant(
            new AccountantId($accountant->accountantId),
            $accountant->accountantDate
                ? new DateYearMonthDay($accountant->accountantDate)
                : '',
            new HospitalId($accountant->hospitalId),
            $accountant->divisionId
                ? new DivisionId($accountant->divisionId)
                : null,
            $accountant->distributorId
                ? new DistributorId($accountant->distributorId)
                : null,
            $accountant->orderNumber ?? null,
            $accountant->receivingNumber ?? null
        );

        $accountant->_division = $division;
        $accountant->_distributor = $distributor;

        $items = ModelRepository::getAccountantItemInstance()
            ->where('accountantId', $accountantId->value())
            ->get();

        $additems = [];
        foreach ($items->all() as $item) {
            $additems[] = AccountantItem::init(
                $item->index,
                $item->accountantId,
                $item->method,
                $item->action,
                $item->accountantItemId,
                $item->itemId,
                $item->makerName,
                $item->itemName,
                $item->itemCode,
                $item->itemStandard,
                $item->itemJANCode,
                $item->count,
                $item->unit,
                $item->price,
                $item->taxrate
            );
        }
        $accountant->setItems($additems);
        return $accountant;
    }

    public function save(Accountant $accountant)
    {
        $itemUpsert = [];
        $itemInstance = ModelRepository::getAccountantItemInstance()->where(
            'accountantId',
            $accountant->getAccountantId()->value()
        );
        foreach ($accountant->getItems() as $item) {
            $item = $item->toArray();
            $itemUpsert[] = [
                'updateTime' => 'now',
                'accountantId' => $accountant->getAccountantId()->value(),
                'itemId' => $item['itemId'],
                'itemName' => $item['itemName'],
                'makerName' => $item['makerName'],
                'itemCode' => $item['itemCode'],
                'itemStandard' => $item['itemStandard'],
                'itemJANCode' => $item['itemJANCode'],
                'count' => $item['count'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'taxrate' => $item['taxrate'],
                'accountantItemId' => $item['accountantItemId'],
                'action' => $item['action'],
                'method' => $item['method'],
                'index' => $item['index'],
            ];

            $itemInstance->where(
                'accountantItemId',
                $item['accountantItemId'],
                '!='
            );
        }

        ModelRepository::getAccountantInstance()
            ->where('accountantId', $accountant->getAccountantId()->value())
            ->update([
                'updateTime' => 'now',
                'totalAmount' => $accountant->totalAmount(),
            ]);

        $itemInstance->delete();
        if (!empty($itemUpsert)) {
            ModelRepository::getAccountantItemInstance()->upsertBulk(
                'accountantItemId',
                $itemUpsert
            );
        }
    }

    public function saveItemLog(array $logs)
    {
        $logs = array_map(function (AccountantItemChageLog $log) {
            return $log;
        }, $logs);

        $insert = [];
        foreach ($logs as $log) {
            $log = $log->toArray();
            $insert[] = [
                'accountantId' => $log['accountantItem']['accountantId'],
                'itemId' => $log['accountantItem']['itemId'],
                'itemName' => $log['accountantItem']['itemName'],
                'makerName' => $log['accountantItem']['makerName'],
                'itemCode' => $log['accountantItem']['itemCode'],
                'itemStandard' => $log['accountantItem']['itemStandard'],
                'itemJANCode' => $log['accountantItem']['itemJANCode'],
                'count' => $log['accountantItem']['count'],
                'unit' => $log['accountantItem']['unit'],
                'price' => $log['accountantItem']['price'],
                'taxrate' => $log['accountantItem']['taxrate'],
                'accountantItemId' =>
                    $log['accountantItem']['accountantItemId'],
                'action' => $log['accountantItem']['action'],
                'method' => $log['accountantItem']['method'],
                'index' => $log['accountantItem']['index'],
                'kinds' => $log['kinds'],
                'userId' => $log['userId'],
            ];
        }
        if (!empty($insert)) {
            ModelRepository::getAccountantItemLogInstance()->insert($insert);
        }
    }
}

interface AccountantRepositoryInterface
{
}
