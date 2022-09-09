<?php

declare(strict_types=1);

namespace Medicode\Order\Adapters\Repository;

class MedicodeSendOrderFormatter
{
    public static function getPostFields(array $orderList = []): string
    {
        $sendData = [];
        $stringData = '';

        $SEQNO = 1;
        foreach ($orderList as $order) {
            if ($order->getIsValid()) {
                $orderNumber = $order->getOrderNumber();

                if (!array_key_exists($orderNumber, $sendData)) {
                    $sendData[$orderNumber]['Srecord'] = [
                        'S',
                        '15',
                        self::formatHospitalCode($order->getHospitalCode()),
                        self::formatDistributorCode($order->getDistributorCode()),
                        str_pad(strval($SEQNO), 4, '0', STR_PAD_LEFT),
                        date("ymd"),
                        date("His"),
                        128,
                        128,
                        self::formatRecordId($order->getId()),
                        self::formatHospitalCode($order->getHospitalCode()),
                        str_pad('', 63)
                    ];
                    $SEQNO++;
                }

                $sendData[$orderNumber]['Drecord'][] = [
                    'D',
                    '15',
                    self::formatHospitalCode($order->getHospitalCode()),
                    date("ymd"),
                    1,
                    self::formatJANCode($order->getJANCode()),
                    str_pad('', 40),
                    self::formatQuantity((string)$order->getQuantity()),
                    str_pad('', 6, '0'),
                    self::formatDeliveryDestCode((string)$order->getDeliveryDestCode()),
                    self::formatRecordId($order->getId()),
                    str_pad('', 31)
                ];
            }
        }

        if (count($sendData) === 0) {
            return $stringData;
        }

        foreach ($sendData as $key => $value) {
            $count = count($value['Drecord']);
            $sendData[$key]['Erecord'] = [
                'E',
                '15',
                str_pad(strval($count + 2), 6, '0', STR_PAD_LEFT),
                str_pad((string)$count, 6, '0', STR_PAD_LEFT),
                str_pad('', 113)
            ];
        }

        $stringData = self::sendDataToString($sendData);

        return self::makePostFields($stringData);
    }


    private function sendDataToString(array $sendData): string
    {
        $stringData = '';

        foreach ($sendData as $orderNumber => $order) {
            foreach ($order as $type => $row) {
                if ($type === 'Srecord') {
                    foreach ($row as $column) {
                        $stringData .= $column;
                    }
                    $stringData .= NEWLINE;
                }

                if ($type === 'Drecord') {
                    foreach ($row as $array) {
                        foreach ($array as $column) {
                            $stringData .= $column;
                        }
                        $stringData .= NEWLINE;
                    }
                }

                if ($type === 'Erecord') {
                    foreach ($row as $column) {
                        $stringData .= $column;
                    }
                    $stringData .= NEWLINE;
                }
            }
        }

        $stringData = mb_convert_encoding($stringData, 'sjis', 'auto');

        return $stringData;
    }


    /**
     * @return string
     */
    private static function makePostFields(string $stringData): string
    {
        $name = SENDFILE.date("YmdHi");

        $postFields = '';
        $postFields .= "--" . MEDICODE_BOUNDARY . "\r\n";
        $postFields .= 'Content-Disposition: form-data; name="' . $name . '";' .
            ' filename="' . $name . '"' . "\r\n";
        $postFields .= 'Content-Type: application/octet-stream' . "\r\n";
        $postFields .= "\r\n";
        $postFields .= $stringData . "\r\n";
        $postFields .= "--" . MEDICODE_BOUNDARY . "--\r\n";

        return $postFields;
    }


    /**
     * @param string $id
     * @return string
     */
    private static function formatRecordId(string $id): string
    {
        return str_pad($id, 10);
    }


    /**
     * @param string $code
     * @return string
     */
    private static function formatHospitalCode(string $code): string
    {
        return str_pad($code, 10, '0', STR_PAD_LEFT);
    }


    /**
     * @param string $code
     * @return string
     */
    private static function formatDistributorCode(string $code): string
    {
        return str_pad($code, 10);
    }


    /**
     * @param string $code
     * @return string
     */
    private static function formatJANCode(string $code): string
    {
        return str_pad($code, 14);
    }


    /**
     * @param string $quantity
     * @return string
     */
    private static function formatQuantity(string $quantity): string
    {
        return str_pad($quantity, 5, '0', STR_PAD_LEFT);
    }


    /**
     * @param string $deliveryDestCode
     * @return string
     */
    private static function formatDeliveryDestCode(?string $deliveryDestCode): string
    {
        if (empty($deliveryDestCode)) {
            return '00';
        }
        return str_pad($deliveryDestCode, 2, '0', STR_PAD_LEFT);
    }
}
