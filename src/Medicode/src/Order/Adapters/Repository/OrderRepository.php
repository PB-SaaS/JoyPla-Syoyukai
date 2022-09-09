<?php

declare(strict_types=1);

namespace Medicode\Order\Adapters\Repository;

use SpiralApiRequest;
use Medicode\Shared\Exceptions\ApiException;
use Medicode\Authentication\Domain\Authentication;
use Medicode\Authentication\Domain\ValueObjects\AccessToken;
use Medicode\Order\Domain\Factory\OrderFactory;
use Medicode\Order\Domain\Order;
use Medicode\Order\Domain\OrderList;
use Medicode\Order\Adapters\Repository\MedicodeSendOrderFormatter;
use Medicode\Order\UseCases\Contracts\IOrderRepository;

class OrderRepository implements IOrderRepository
{
    /**
     * @return array
     */
    public function get(): array
    {
        global $SPIRAL;
        $orders = [];
        $records = [];
        $count = 1;

        $param = [
            'db_title' => ORDER_ITEM_DB,
            'lines_per_page' => LINES_PER_PAGE,
            'select_columns' => ['recordId', 'orderCNumber', 'orderNumber', 'hospitalCode', 'distributorCode', 'itemJANCode', 'orderQuantity', 'deliveryDestCode'],
            'sort' => [['name' => 'registrationTime', 'order' => 'asc']],
            'search_condition' => [
                ['name' => 'useMedicode', 'value' => 1],
                ['name' => 'medicodeStatus', 'value' => 1],
                ['name' => 'orderStatus', 'value' => 2],
                ['name' => 'orderQuantity', 'value' => 0, 'operator' => '>'],
                ['name' => 'orderQuantity', 'value' => 100000, 'operator' => '<'],
                ['name' => 'orderNumber', 'value' => '', 'operator' => 'ISNOTNULL'],
                ['name' => 'hospitalCode', 'value' => '', 'operator' => 'ISNOTNULL'],
                ['name' => 'distributorCode', 'value' => '', 'operator' => 'ISNOTNULL'],
                ['name' => 'recordId', 'value' => '', 'operator' => 'ISNOTNULL']
            ]
        ];

        for ($page = 1; $page <= ceil($count / LINES_PER_PAGE); $page++) {
            $param['page'] = $page;
            $result = $this->api('database', 'select', $param);
            $code = (int)$result->get('code');

            if ($code !== 0) {
                throw new ApiException($result->get('message'), $code);
            }

            if ($code === 0) {
                $count = (int)$result->get('count');
                if ($count === 0) {
                    return $orders;
                }
                $data = $result->get('data');
                $records = array_merge($records, $data);
            }
        }

        $tmp = [];
        $columns = count($param['select_columns']);
        foreach ($records as $record) {
            for ($i = 0; $i < $columns; $i++) {
                $tmp[$record[1]][$param['select_columns'][$i]] = $record[$i];
            }
        }

        foreach ($tmp as $row) {
            $orders[] = OrderFactory::create($row);
        }

        return $orders;
    }


    /**
     * @param OrderList $orderList
     */
    private function bulkUpdate(OrderList $orderList): void
    {
        global $SPIRAL;
        $updateData = [];
        $param = [
            'db_title' => ORDER_ITEM_DB,
            'key' => 'orderCNumber',
            'columns' => ['orderCNumber', 'updateTime', 'medicodeStatus', 'medicodeSentDate']
        ];

        foreach ($orderList->getOrders() as $order) {
            $status = ($order->getIsValid()) ? 2 : 3;
            $date = ($order->getIsValid()) ? 'now' : '';
            $updateData[] = [
                $order->getOrderCNumber(),
                'now',
                $status,
                $date
            ];
        }

        foreach (array_chunk($updateData, 1000) as $tmp) {
            $param['data'] = $tmp;
            $result = $this->api('database', 'bulk_update', $param);
            $code = (int)$result->get('code');
            if ($code !== 0) {
                throw new ApiException($result->get('message'), $code);
            }
        }
    }


    private function api($_app, $_method, $_params)
    {
        global $SPIRAL;
        $communicator = $SPIRAL->getSpiralApiCommunicator();
        $request = new SpiralApiRequest();
        $request->putAll($_params);
        return $communicator->request($_app, $_method, $request);
    }


    /**
     * @param AccessToken $accessToken
     * @param OrderList $orderList
     * @return array
     */
    public function send(AccessToken $accessToken, OrderList $orderList): array
    {
        $token = $accessToken->getValue();

        $postFields = MedicodeSendOrderFormatter::getPostFields($orderList->getOrders());

        if (!$postFields) {
            $this->bulkUpdate($orderList);
            throw new ApiException('対象の全発注データにフォーマットの誤りがある為、処理を中止しました。', 999);
        }

        $responseHeader = [];
        $response = '';
        $status = 0;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, SENDAPI_URL);
        curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST, SSL_CIPHER_LIST);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data; boundary=' . MEDICODE_BOUNDARY, 'X-API-AccessToken:'. $token]);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        if (!$response) {
            throw new ApiException('メディコード送信APIの実行に失敗しました。', 806);
        }

        if ($httpCode !== 200) {
            throw new ApiException($errno.' : '.$error, $httpCode);
        }

        $header = substr($response, 0, $info['header_size']);
        $responseHeader = $this->getHeaderArray($header);
        $status = (int)$responseHeader['X-API-Status'];

        if ($status === 991) {
            return ['code' => $status, 'message' => 'Access token is expired.'];
        }

        if ($status !== 200) {
            throw new ApiException('メディコード送信APIの実行に失敗しました。', $status);
        }

        $this->bulkUpdate($orderList);

        return ['code' => $status, 'message' => 'communicateKey = '.$responseHeader['X-API-CommunicateKey']];
    }


    /**
     * @param string $header
     * @return array
     */
    private function getHeaderArray(string $header): array
    {
        $headerArray = [];
        $_header = str_replace("\r", '', $header);
        $tmp_header = explode("\n", $_header);

        foreach ($tmp_header as $row) {
            $tmp = explode(': ', $row);
            $key = trim($tmp[0]);
            if ($key === '') {
                continue;
            }
            $value = str_replace($key.': ', '', $row);
            $headerArray[$key] = trim($value);
        }
        return $headerArray;
    }
}
