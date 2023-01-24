<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\Barcode {
    use App\SpiralDb\CardView;
    use App\SpiralDb\Hospital;
    use App\SpiralDb\HospitalUser;
    use App\SpiralDb\InHospitalItemView;
    use App\SpiralDb\PayoutItem;
    use App\SpiralDb\ReceivedItemView;
    use Exception;
    use framework\Facades\Gate;
    use framework\SpiralConnecter\SpiralDB;
    use JoyPla\Application\InputPorts\Api\Barcode\BarcodeSearchInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Barcode\BarcodeSearchInputData;
    use JoyPla\Application\OutputPorts\Api\Barcode\BarcodeSearchOutputData;
    use JoyPla\Application\OutputPorts\Api\Barcode\BarcodeSearchOutputPortInterface;
    use JoyPla\Enterprise\Models\DateYearMonthDay;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\BarcodeRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepositoryInterface;
    use NGT\Barcode\GS1Decoder\Decoder;
    use Collection;

    /**
     * Class BarcodeSearchInteractor
     * @package JoyPla\Application\Interactors\Barcode\Api
     */
    class BarcodeSearchInteractor implements BarcodeSearchInputPortInterface
    {
        /**
         * BarcodeSearchInteractor constructor.
         * @param BarcodeSearchOutputPortInterface $outputPort
         */
        public function __construct(
            BarcodeSearchOutputPortInterface $outputPort,
            BarcodeRepositoryInterface $barcodeRepository
        ) {
            $this->outputPort = $outputPort;
            $this->barcodeRepository = $barcodeRepository;
        }

        /**
         * @param BarcodeSearchInputData $inputData
         */
        public function handle(BarcodeSearchInputData $inputData)
        {
            $type = '';
            $inHospitalItems = [];
            $count = 0;
            $isPickingList = false;
            if ($inputData->barcode === '') {
                throw new Exception('Barcode is Null', 200);
            }
            if (preg_match('/^STK/', $inputData->barcode)) {
                //pickingList
                $isPickingList = true;

                $type = 'pickingList';

                $array = explode(' ', $inputData->barcode);
                if (count($array) !== 2) {
                    throw new Exception('Not PickingList Barcode');
                }
                $tmp = new Collection();
                $tmp->set('recordId', str_replace('STK', '', $array[0]));
                $tmp->set('sourceDivisionId', $array[1]);
                $inHospitalItems[] = $tmp;
                $count = 1;
            } elseif (
                preg_match('/^20/', $inputData->barcode) &&
                (strlen($inputData->barcode) == 12 ||
                    strlen($inputData->barcode) == 15)
            ) {
                //received

                $type = 'received';
                //TODO Repository化は後でやる
                //検収書から発行されたラベル
                $receivedItemId = substr($inputData->barcode, 2);
                if (Gate::allows('is_admin')) {
                    $result = ReceivedItemView::where(
                        'receivingNumber',
                        'rec_' . $receivedItemId
                    )
                        ->where('hospitalId', $inputData->user->hospitalId)
                        ->get();
                } else {
                    $result = ReceivedItemView::where(
                        'receivingNumber',
                        'rec_' . $receivedItemId
                    )
                        ->where('hospitalId', $inputData->user->hospitalId)
                        ->where('divisionId', $inputData->user->divisionId)
                        ->get();
                }
                if ($result->count == 0) {
                    throw new Exception('Not Received Label');
                }
                $record = $result->data->get(0);

                $hospital = Hospital::where(
                    'hospitalId',
                    $inputData->user->hospitalId
                )->get();
                $hospital = $hospital->data->get(0);

                $divisionId = $record->divisionId;

                if ($hospital->receivingTarget == '1') {
                    $division = SpiralDB::title('NJ_divisionDB')
                        ->where('hospitalId', $inputData->user->hospitalId)
                        ->where('divisionType', '1')
                        ->get(['divisionId']);

                    $division = $division->first();
                    $divisionId = $division->divisionId;
                }

                $inHospitalItems = InHospitalItemView::where(
                    'notUsedFlag',
                    '1',
                    '!='
                )
                    ->where('inHospitalItemId', $record->inHospitalItemId)
                    ->where('hospitalId', $inputData->user->hospitalId)
                    ->get();
                $count = $inHospitalItems->count;
                $inHospitalItems = $inHospitalItems->data->all();
                foreach ($inHospitalItems as $key => $v) {
                    if ($record->lotDate != '') {
                        $record->lotDate = (new DateYearMonthDay(
                            $record->lotDate
                        ))->format('Y-m-d');
                    }
                    $inHospitalItems[$key]->set(
                        'lotNumber',
                        $record->lotNumber
                    );
                    $inHospitalItems[$key]->set('lotDate', $record->lotDate);
                    $inHospitalItems[$key]->set('divisionId', $divisionId);
                }
            } elseif (
                preg_match('/^30/', $inputData->barcode) &&
                strlen($inputData->barcode) == 12
            ) {
                //payout

                //TODO Repository化は後でやる
                $type = 'payout';
                //払出から発行されたラベル
                $payout_num = substr($inputData->barcode, 2);
                if (Gate::allows('is_admin')) {
                    $result = PayoutItem::where(
                        'payoutId',
                        'payout_' . $payout_num
                    )
                        ->where('hospitalId', $inputData->user->hospitalId)
                        ->get();
                } else {
                    $result = PayoutItem::where(
                        'payoutId',
                        'payout_' . $payout_num
                    )
                        ->where('hospitalId', $inputData->user->hospitalId)
                        ->where(
                            'sourceDivisionId',
                            $inputData->user->divisionId
                        )
                        ->get();
                }

                if ($result->count == 0) {
                    throw new Exception('Not Payout Label');
                }

                $record = $result->data->get(0);
                $inHospitalItems = InHospitalItemView::where(
                    'notUsedFlag',
                    '1',
                    '!='
                )
                    ->where('inHospitalItemId', $record->inHospitalItemId)
                    ->where('hospitalId', $inputData->user->hospitalId)
                    ->get();

                if ($inHospitalItems->count === 0) {
                    throw new Exception('Not Payout Label');
                }

                $inHospitalItems = $inHospitalItems->data->all();

                foreach ($inHospitalItems as $key => $v) {
                    if ($record->lotDate != '') {
                        $record->lotDate = (new DateYearMonthDay(
                            $record->lotDate
                        ))->format('Y-m-d');
                    }
                    $inHospitalItems[$key]->set(
                        'lotNumber',
                        $record->lotNumber
                    );
                    $inHospitalItems[$key]->set('lotDate', $record->lotDate);
                    $inHospitalItems[$key]->set(
                        'payoutQuantity',
                        $record->payoutQuantity
                    );
                    $inHospitalItems[$key]->set(
                        'divisionId',
                        $record->targetDivisionId
                    );
                }
            } elseif (
                preg_match('/^90/', $inputData->barcode) &&
                strlen($inputData->barcode) == 18
            ) {
                //card

                $type = 'card';

                if (Gate::allows('is_admin')) {
                    $result = CardView::where('cardId', $inputData->barcode)
                        ->where('hospitalId', $inputData->user->hospitalId)
                        ->get();
                    $record = $result->data->get(0);
                } else {
                    $result = CardView::where('cardId', $inputData->barcode)
                        ->where('hospitalId', $inputData->user->hospitalId)
                        ->where('divisionId', $inputData->user->divisionId)
                        ->get();
                    $record = $result->data->get(0);
                }

                if ($result->count == '0') {
                    throw new Exception('Card Label');
                }
                $inHospitalItems = InHospitalItemView::where(
                    'hospitalId',
                    $inputData->user->hospitalId
                )
                    ->where('inHospitalItemId', $record->inHospitalItemId)
                    ->get();

                if ($inHospitalItems->count === 0) {
                    throw new Exception('Not Payout Label');
                }

                $inHospitalItems = $inHospitalItems->data->all();

                foreach ($inHospitalItems as $key => $v) {
                    if ($record->lotDate != '') {
                        $record->lotDate = (new DateYearMonthDay(
                            $record->lotDate
                        ))->format('Y-m-d');
                    }
                    $inHospitalItems[$key]->set(
                        'lotNumber',
                        $record->lotNumber
                    );
                    $inHospitalItems[$key]->set('lotDate', $record->lotDate);
                    $inHospitalItems[$key]->set(
                        'cardQuantity',
                        $record->quantity
                    );
                    $inHospitalItems[$key]->set(
                        'divisionId',
                        $record->divisionId
                    );
                }
            } elseif (strlen($inputData->barcode) == 13) {
                //JanCode

                $type = 'jancode';

                [
                    $inHospitalItems,
                    $count,
                ] = $this->barcodeRepository->searchByJanCode(
                    new HospitalId($inputData->user->hospitalId),
                    (string) $inputData->barcode
                );
            } elseif (
                (preg_match('/^1/', $inputData->barcode) &&
                    strlen($inputData->barcode) == 14) ||
                (preg_match('/^01/', $inputData->barcode) &&
                    strlen($inputData->barcode) == 14)
            ) {
                //院内商品マスタ

                $type = 'customlabel';

                //在庫表等で発行されたラベル
                if (
                    preg_match('/^1/', $inputData->barcode) &&
                    strlen($inputData->barcode) == 14
                ) {
                    $label_id = substr($inputData->barcode, 1, 5);
                    $label_id = str_pad($label_id, 8, 0, STR_PAD_LEFT);
                    $custom_quantity = substr($inputData->barcode, 10, 4);
                } elseif (
                    preg_match('/^01/', $inputData->barcode) &&
                    strlen($inputData->barcode) == 14
                ) {
                    $label_id = substr($inputData->barcode, 2, 8);
                    $custom_quantity = substr($inputData->barcode, 10, 4);
                }
                $InHospitalItemView = InHospitalItemView::where(
                    'notUsedFlag',
                    '1',
                    '!='
                )
                    ->where('labelId', $label_id)
                    ->where('hospitalId', $inputData->user->hospitalId);

                $result = $InHospitalItemView->get();
                $count = $result->count;
                if ($result->count == '0') {
                    throw new Exception('Not Received Label');
                }

                $inHospitalItems = $result->data->all();
                foreach ($inHospitalItems as $key => $v) {
                    $inHospitalItems[$key]->set('lotNumber', '');
                    $inHospitalItems[$key]->set('lotDate', '');
                    $inHospitalItems[$key]->set(
                        'customQuantity',
                        (int) $custom_quantity
                    );
                }
            } else {
                try {
                    $type = 'gs1-128';

                    $decoder = new Decoder(($delimiter = ' '));
                    $barcode = $decoder->decode($inputData->barcode);
                    $gs1128Data = $barcode->toArray();

                    $gtin13 = self::gtin14ToGtin13Convert(
                        $gs1128Data['identifiers']['01']['content']
                    );

                    [
                        $inHospitalItems,
                        $count,
                    ] = $this->barcodeRepository->searchByJanCode(
                        new HospitalId($inputData->user->hospitalId),
                        (string) $gtin13
                    );

                    $lotNumber = $gs1128Data['identifiers']['10']['content'];
                    if ($gs1128Data['identifiers']['17']['content']) {
                        $lotDate = $gs1128Data['identifiers']['17'][
                            'content'
                        ]->format('Y-m-d');
                    } elseif ($gs1128Data['identifiers']['7003']['content']) {
                        $lotDate = $gs1128Data['identifiers']['7003'][
                            'content'
                        ]->format('Y-m-d');
                    } else {
                        $lotDate = '';
                    }
                    //$lotDate = ( $gs1128Data['identifiers']['17']['content'] )? $gs1128Data['identifiers']['17']['content']->format('Y-m-d') : "";

                    foreach ($inHospitalItems as $key => $val) {
                        $inHospitalItems[$key]->set('lotNumber', $lotNumber);
                        $inHospitalItems[$key]->set('lotDate', $lotDate);
                    }
                } catch (Exception $e) {
                    throw new Exception('Barcode is Null', 200);
                }
            }

            foreach ($inHospitalItems as $key => $val) {
                $inHospitalItems[$key]->set('barcode', $inputData->barcode);
                $inHospitalItems[$key]->set('priceNotice', '');
            }

            if (count($inHospitalItems) > 0 && !$isPickingList) {
                $price = SpiralDB::title('NJ_PriceDB');
                foreach ($inHospitalItems as $key => $val) {
                    $price->orWhere('priceId', $val->priceId);
                }

                $prices = $price->get(['priceId', 'notice']);

                foreach ($inHospitalItems as $key => $val) {
                    foreach ($prices as $price) {
                        if ($val->priceId === $price->priceId) {
                            $inHospitalItems[$key]->set(
                                'priceNotice',
                                $price->notice
                            );
                        }
                    }
                }
            }

            $this->outputPort->output(
                new BarcodeSearchOutputData($inHospitalItems, $count, $type)
            );
        }

        private static function gtin14ToGtin13Convert($code)
        {
            $cut = 1; //カットしたい文字数
            $code = substr($code, 0, strlen($code) - $cut);

            $cut = 1; //カットしたい文字数
            $code = substr($code, $cut, strlen($code) - $cut);
            return $code . self::calcJanCodeDigit($code);
        }

        private static function calcJanCodeDigit($num)
        {
            $arr = str_split($num);
            $odd = 0;
            $mod = 0;
            for ($i = 0; $i < count($arr); $i++) {
                if (($i + 1) % 2 == 0) {
                    //偶数の総和
                    $mod += intval($arr[$i]);
                } else {
                    //奇数の総和
                    $odd += intval($arr[$i]);
                }
            }
            //偶数の和を3倍+奇数の総和を加算して、下1桁の数字を10から引く
            $cd = 10 - intval(substr((string) ($mod * 3) + $odd, -1));
            //10なら1の位は0なので、0を返す。
            return $cd === 10 ? 0 : $cd;
        }
    }
}

/***
 * INPUT
 */

namespace JoyPla\Application\InputPorts\Api\Barcode {
    use Auth;
    use stdClass;

    /**
     * Class BarcodeSearchInputData
     * @package JoyPla\Application\InputPorts\Barcode\Api
     */
    class BarcodeSearchInputData
    {
        /**
         * BarcodeSearchInputData constructor.
         */
        public function __construct(Auth $user, string $barcode)
        {
            $this->user = $user;
            $this->barcode = $barcode;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Barcode\Api
     */
    interface BarcodeSearchInputPortInterface
    {
        /**
         * @param BarcodeSearchInputData $inputData
         */
        function handle(BarcodeSearchInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\Barcode {
    /**
     * Class BarcodeSearchOutputData
     * @package JoyPla\Application\OutputPorts\Barcode\Api;
     */
    class BarcodeSearchOutputData
    {
        /** @var string */

        /**
         * BarcodeSearchOutputData constructor.
         */
        public function __construct(
            array $inHospitalItems,
            int $count,
            string $type
        ) {
            $this->inHospitalItems = $inHospitalItems;
            $this->count = $count;
            $this->type = $type;
        }
    }

    /**
     * Interface BarcodeSearchOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Barcode\Api;
     */
    interface BarcodeSearchOutputPortInterface
    {
        /**
         * @param BarcodeSearchOutputData $outputData
         */
        function output(BarcodeSearchOutputData $outputData);
    }
}
