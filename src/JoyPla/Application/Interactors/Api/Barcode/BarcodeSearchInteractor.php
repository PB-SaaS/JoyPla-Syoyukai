<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Barcode {

    use App\Model\Division;
    use Exception;
    use JoyPla\Application\InputPorts\Api\Barcode\BarcodeSearchInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Barcode\BarcodeSearchInputData;
    use JoyPla\Application\OutputPorts\Api\Barcode\BarcodeSearchOutputData;
    use JoyPla\Application\OutputPorts\Api\Barcode\BarcodeSearchOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\BarcodeRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepositoryInterface;
    use NGT\Barcode\GS1Decoder\Decoder;

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
            BarcodeSearchOutputPortInterface $outputPort ,
            BarcodeRepositoryInterface $barcodeRepository
        )
        {
            $this->outputPort = $outputPort;
            $this->barcodeRepository = $barcodeRepository;
        }

        /**
         * @param BarcodeSearchInputData $inputData
         */
        public function handle(BarcodeSearchInputData $inputData)
        {
            if($inputData->barcode === "" )
            {
                throw new Exception("Barcode is Null",422);
            }

            if(preg_match('/^20/', $inputData->barcode) && strlen($inputData->barcode) == 12){
            
            } else if(preg_match('/^30/', $inputData->barcode) && strlen($inputData->barcode) == 12){
            
            } else if(preg_match('/^90/', $inputData->barcode) && strlen($inputData->barcode) == 18){
            
            } else if(strlen($inputData->barcode) == 13) {
            
            } else if((preg_match('/^1/', $inputData->barcode) && strlen($inputData->barcode) == 14 ) || (preg_match('/^01/', $inputData->barcode) && strlen($inputData->barcode) == 14)){
            
            } else {
                $decoder = new Decoder($delimiter = ' ');
                $barcode = $decoder->decode($inputData->barcode);
                $gs1128Data = $barcode->toArray();

                $gtin13 = self::gtin14ToGtin13Convert($gs1128Data['identifiers']['01']['content']);

                [ $inHospitalItems , $count ] = $this->barcodeRepository->searchByJanCode((new HospitalId($inputData->user->hospitalId)) , (string)$gtin13);
                
                $lotNumber = $gs1128Data['identifiers']['10']['content'];
                $lotDate = ( $gs1128Data['identifiers']['17']['content'] )? $gs1128Data['identifiers']['17']['content']->format('Y-m-d') : "";
                
                foreach($inHospitalItems as $key => $val)
                {
                    $inHospitalItems[$key]->set('lotNumber',$lotNumber);
                    
                    $inHospitalItems[$key]->set('lotDate',$lotDate);
                }
                
            }
            $this->outputPort->output(new BarcodeSearchOutputData($inHospitalItems , $count));
        }

        private static function gtin14ToGtin13Convert($code)
        {
            $cut = 1;//カットしたい文字数
            $code = substr( $code , 0 , strlen($code) - $cut );

            $cut = 1;//カットしたい文字数
            $code = substr( $code , $cut , strlen($code) -$cut );
            return  $code . self::calcJanCodeDigit($code);
        }

        private static function calcJanCodeDigit($num) {
            $arr = str_split($num);
            $odd = 0;
            $mod = 0;
            for($i=0;$i<count($arr);$i++){
            if(($i+1) % 2 == 0) {
                //偶数の総和
                $mod += intval($arr[$i]);
            } else {
                //奇数の総和
                $odd += intval($arr[$i]);               
            }
            }
            //偶数の和を3倍+奇数の総和を加算して、下1桁の数字を10から引く
            $cd = 10 - intval(substr((string)($mod * 3) + $odd,-1));
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
        public function __construct(Auth $user , string $barcode)
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
        public function __construct(array $inHospitalItems , int $count)
        {
            $this->inHospitalItems = $inHospitalItems;
            $this->count = $count;
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