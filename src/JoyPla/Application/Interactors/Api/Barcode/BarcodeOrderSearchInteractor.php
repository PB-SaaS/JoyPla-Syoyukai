<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Barcode {
    use Collection;
    use Exception;
    use JoyPla\Application\InputPorts\Api\Barcode\BarcodeOrderSearchInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Barcode\BarcodeOrderSearchInputData;
    use JoyPla\Application\OutputPorts\Api\Barcode\BarcodeOrderSearchOutputData;
    use JoyPla\Enterprise\Models\DivisionId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InHospitalItemId;
    use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;
    use NGT\Barcode\GS1Decoder\Decoder;

    /**
     * Class BarcodeOrderSearchInteractor
     * @package JoyPla\Application\Interactors\Barcode\Api
     */
    class BarcodeOrderSearchInteractor implements
        BarcodeOrderSearchInputPortInterface
    {
        private PresenterProvider $presenterProvider;
        private RepositoryProvider $repositoryProvider;

        public function __construct(
            PresenterProvider $presenterProvider,
            RepositoryProvider $repositoryProvider
        ) {
            $this->presenterProvider = $presenterProvider;
            $this->repositoryProvider = $repositoryProvider;
        }

        /**
         * @param BarcodeOrderSearchInputData $inputData
         */
        public function handle(BarcodeOrderSearchInputData $inputData)
        {
            if ($inputData->barcode === '') {
                throw new Exception('Barcode is Null', 422);
            }
            $type = 'other';
            $orders = [];

            $divisionId = '';
            if ($inputData->isOnlyMyDivision) {
                $divisionId = new DivisionId($inputData->user->divisionId);
            }

            if (
                (preg_match('/^1/', $inputData->barcode) &&
                    strlen($inputData->barcode) == 14) ||
                (preg_match('/^01/', $inputData->barcode) &&
                    strlen($inputData->barcode) == 14)
            ) {
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

                $InHospitalItemView = ModelRepository::getInHospitalItemViewInstance()
                    ->where('notUsedFlag', '1', '!=')
                    ->where('labelId', $label_id)
                    ->where('hospitalId', $inputData->user->hospitalId);

                $result = $InHospitalItemView->get();
                $count = $result->count();
                if ($result->count() == '0') {
                    throw new Exception('Not Received Label');
                }

                $inHospitalItems = $result->first();

                [
                    $orders,
                    $count,
                ] = $this->repositoryProvider
                    ->getBarcodeRepository()
                    ->orderSearchByInHospitalItemId(
                        new HospitalId($inputData->user->hospitalId),
                        new InHospitalItemId(
                            $inHospitalItems->inHospitalItemId
                        ),
                        $divisionId
                    );

                $lotNumber = '';
                $lotDate = '';

                $result = [];
                foreach ($orders as $key => $order) {
                    $tmp = $order->toArray();
                    foreach ($tmp['orderItems'] as $key => $orderItems) {
                        $tmp['orderItems'][$key]['lotNumber'] = $lotNumber;
                        $tmp['orderItems'][$key]['lotDate'] = $lotDate;
                    }
                    $result[] = $tmp;
                }
            } elseif (strlen($inputData->barcode) == 13) {
                //JanCode

                $type = 'jancode';

                [
                    $orders,
                    $count,
                ] = $this->repositoryProvider
                    ->getBarcodeRepository()
                    ->orderSearchByJanCode(
                        new HospitalId($inputData->user->hospitalId),
                        (string) $inputData->barcode,
                        $divisionId
                    );

                $lotNumber = '';
                $lotDate = '';

                $result = [];
                foreach ($orders as $key => $order) {
                    $tmp = $order->toArray();
                    foreach ($tmp['orderItems'] as $key => $orderItems) {
                        $tmp['orderItems'][$key]['lotNumber'] = $lotNumber;
                        $tmp['orderItems'][$key]['lotDate'] = $lotDate;
                    }
                    $result[] = $tmp;
                }
            } else {
                try {
                    $decoder = new Decoder(($delimiter = ' '));
                    $barcode = $decoder->decode($inputData->barcode);
                    $type = 'gs1-128';
                    $gs1128Data = $barcode->toArray();

                    $gtin13 = self::gtin14ToGtin13Convert(
                        $gs1128Data['identifiers']['01']['content']
                    );

                    [
                        $orders,
                        $count,
                    ] = $this->repositoryProvider
                        ->getBarcodeRepository()
                        ->orderSearchByJanCode(
                            new HospitalId($inputData->user->hospitalId),
                            (string) $gtin13,
                            $divisionId
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

                    $result = [];
                    foreach ($orders as $key => $order) {
                        $tmp = $order->toArray();
                        foreach ($tmp['orderItems'] as $key => $orderItems) {
                            $tmp['orderItems'][$key]['lotNumber'] = $lotNumber;
                            $tmp['orderItems'][$key]['lotDate'] = $lotDate;
                        }
                        $result[] = $tmp;
                    }
                } catch (Exception $e) {
                    throw new Exception('Barcode is Null', 200);
                }
            }
            $this->presenterProvider
                ->getBarcodeOrderSearchPresenter()
                ->output(
                    new BarcodeOrderSearchOutputData($result, $count, $type)
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
     * Class BarcodeOrderSearchInputData
     * @package JoyPla\Application\InputPorts\Barcode\Api
     */
    class BarcodeOrderSearchInputData
    {
        public Auth $user;
        public string $barcode;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            string $barcode,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->barcode = $barcode;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Barcode\Api
     */
    interface BarcodeOrderSearchInputPortInterface
    {
        /**
         * @param BarcodeOrderSearchInputData $inputData
         */
        function handle(BarcodeOrderSearchInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Barcode {
    use Collection;

    /**
     * Class BarcodeOrderSearchOutputData
     * @package JoyPla\Application\OutputPorts\Barcode\Api;
     */
    class BarcodeOrderSearchOutputData
    {
        public array $orders;
        public int $count;
        public $type;
        /**
         * BarcodeOrderSearchOutputData constructor.
         */
        public function __construct(array $orders, int $count, $type)
        {
            $this->orders = $orders;
            $this->type = $type;
            $this->count = $count;
        }
    }

    /**
     * Interface BarcodeOrderSearchOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Barcode\Api;
     */
    interface BarcodeOrderSearchOutputPortInterface
    {
        /**
         * @param BarcodeOrderSearchOutputData $outputData
         */
        function output(BarcodeOrderSearchOutputData $outputData);
    }
}
