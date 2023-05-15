<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use ApiResponse;
use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantLogsIndexInputData;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantLogsIndexInputPortInterface;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Service\Functions\FunctionService;
use JoyPla\Service\Repository\RepositoryProvider;
use stdClass;

class AccountantLogController extends Controller
{
    public function logs(
        $vars,
        AccountantLogsIndexInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $gate = Gate::getGateInstance('list_of_accountant_slips');

        $search = $this->request->get('search', []);

        if ($gate->isOnlyMyDivision()) {
            $search['divisionIds'] = [$this->request->user()->divisionId];
        }

        $inputData = new AccountantLogsIndexInputData(
            $this->request->user(),
            $search
        );

        $inputPort->handle($inputData);
    }

    public function totalPrice($vars)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $searchRequest = $this->request->get('search');
        $search = new stdClass();
        $search->sortColumn = $searchRequest['sortColumn'] ?? 'id';
        $search->sortDirection = $searchRequest['sortDirection'] ?? 'desc';
        $search->itemName = $searchRequest['itemName'] ?? '';
        $search->makerName = $searchRequest['makerName'] ?? '';
        $search->itemCode = $searchRequest['itemCode'] ?? '';
        $search->itemStandard = $searchRequest['itemStandard'] ?? '';
        $search->itemJANCode = $searchRequest['itemJANCode'] ?? '';
        $search->yearMonth = $searchRequest['yearMonth'] ?? '';
        $search->divisionIds = $searchRequest['divisionIds'] ?? '';
        $search->perPage = $searchRequest['perPage'] ?? 1;
        $search->currentPage = $searchRequest['currentPage'] ?? 1;
        $search->distributorIds = $searchRequest['distributorIds'] ?? '';

        $repositoryProvider = new RepositoryProvider();
        $totalPrice = $repositoryProvider
            ->getAccountantLogRepository()
            ->totalPrice(
                new HospitalId($this->request->user()->hospitalId),
                $search
            );

        echo (new ApiResponse($totalPrice, 1, 200, 'totalPrice', []))->toJson();
    }

    public function itemsDownload($vars)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $searchRequest = $this->request->get('search');
        $search = new stdClass();
        $search->sortColumn = $searchRequest['sortColumn'] ?? 'id';
        $search->sortDirection = $searchRequest['sortDirection'] ?? 'desc';
        $search->itemName = $searchRequest['itemName'] ?? '';
        $search->makerName = $searchRequest['makerName'] ?? '';
        $search->itemCode = $searchRequest['itemCode'] ?? '';
        $search->itemStandard = $searchRequest['itemStandard'] ?? '';
        $search->itemJANCode = $searchRequest['itemJANCode'] ?? '';
        $search->yearMonth = $searchRequest['yearMonth'] ?? '';
        $search->divisionIds = $searchRequest['divisionIds'] ?? '';
        $search->perPage = $searchRequest['perPage'] ?? 1;
        $search->currentPage = $searchRequest['currentPage'] ?? 1;
        $search->distributorIds = $searchRequest['distributorIds'] ?? '';

        $downloadSettingRequest = $this->request->get('download_setting');

        $downloadSetting = new stdClass();
        $downloadSetting->{'download-range-type'} =
            $downloadSettingRequest['download-range-type'] ?? '';
        $downloadSetting->{'download-start-record'} =
            $downloadSettingRequest['download-start-record'] ?? '';
        $downloadSetting->{'download-max-record'} =
            $downloadSettingRequest['download-max-record'] ?? '';
        $downloadSetting->{'download-start-page'} =
            $downloadSettingRequest['download-start-page'] ?? '';
        $downloadSetting->{'download-max-page'} =
            $downloadSettingRequest['download-max-page'] ?? '';

        $repositoryProvider = new RepositoryProvider();
        $items = $repositoryProvider
            ->getAccountantLogRepository()
            ->fetchPaginatedDataWithLimit(
                new HospitalId($this->request->user()->hospitalId),
                $search,
                $downloadSetting
            );
        $data = [];
        $data[] = [
            'id',
            '日時',
            '種別',
            '担当者',
            '会計日',
            '会計番号',
            '発注番号',
            '検収番号',
            '部署',
            '卸業者',
            '登録元',
            'アクション',
            '商品ID',
            '商品名',
            'メーカー名',
            '製品コード',
            '規格',
            'JANコード',
            '個数',
            '単位',
            '価格',
            '税率',
            '小計',
        ];

        foreach ($items as $item) {
            $data[] = [
                $item->id,
                $item->registTime,
                $item->kinds,
                $item->userName,
                $item->accountantDate,
                $item->accountantId,
                $item->orderNumber,
                $item->receivingNumber,
                $item->divisionName,
                $item->distributorName,
                $item->method,
                $item->action,
                $item->itemId,
                $item->itemName,
                $item->makerName,
                $item->itemCode,
                $item->itemStandard,
                $item->itemJANCode,
                $item->count,
                $item->unit,
                $item->price,
                $item->taxrate,
                FunctionService::calculateTotalWithTax(
                    $item->price,
                    $item->count,
                    $item->taxrate
                ),
            ];
        }

        $base64 = '';
        if (
            $downloadSettingRequest['download-file-extensions'] === 'tab-txt' ||
            $downloadSettingRequest['download-file-extensions'] === 'tsv'
        ) {
            $base64 = FunctionService::arrayToTsvBase64($data);
        } else {
            $base64 = FunctionService::arrayToCsvBase64($data);
        }

        echo (new ApiResponse($base64, 1, 200, 'fileDownload', []))->toJson();
    }
}
