<?php

namespace JoyPla\Enterprise\Models {

    use DateTime;
    use JoyPla\Enterprise\Traits\ValueObjectTrait;
    use Exception;

    class MakerName {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(string $value)
        {
            $this->value = $value;
        }
    }

    class LotNumber {

        use ValueObjectTrait;

        private string $value = "";
        public function __construct(string $value)
        {
            if(!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }
            $this->value = $value;
        }

        public static function isValid(string $value)
        {
            $value = htmlspecialchars_decode($value,ENT_QUOTES);
            if($value === '' || $value === null)
            {
                return true;
            }
            if( 
                preg_match('/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+/', $value) &&
                mb_strlen($value) <= 20 ) 
            {
                return true;
            }

            return false;
        }
    }

    class LotDate { 
 
        use ValueObjectTrait;

        const FORMAT_DELIMITER_SLASH = "/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\/([1-9]|0[1-9]|[12][0-9]|3[01])$/";
        const FORMAT_DELIMITER_HYPHEN = "/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[12][0-9]|3[01])$/";
        const FORMAT_DELIMITER_JAPANESE_CHARACTER = "/^([0-9]{4}|[0-9]{3}|[0-9]{2}|[0-9]{1})年([1-9]|0[1-9]|1[0-2])月([1-9]|0[1-9]|[12][0-9]|3[01])日$/";


        private string $value = "";
        public function __construct(string $value)
        {
            if(!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }   
            $this->value = $value;
        } 

        
        public static function isValid(string $value)
        {
            if( $value === "" || $value === null )
            {
                return true;
            }
            if (preg_match(self::FORMAT_DELIMITER_SLASH, $value) ||
                preg_match(self::FORMAT_DELIMITER_HYPHEN, $value) ||
                preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value))
            {
                return true;
            }
            return false;
        }
    }


    class Jancode 
    {
        use ValueObjectTrait; 

        private string $value = "";

        public function __construct(string $value)
        {
            if($value === "")
            {
                throw new Exception(self::class . ": Null is not allowed.", 422);
            }
            $this->value = $value;
        }
    }

    class ItemStandard {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(string $value)
        {
            $this->value = $value;
        }
    }

    class ItemName {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(string $value)
        {
            $this->value = $value;
        }
    }


    class ItemId 
    {
        use ValueObjectTrait; 

        private string $value = "";

        public function __construct(string $value)
        {
            if($value === "")
            {
                throw new Exception(self::class . ": Null is not allowed.", 422);
            }
            $this->value = $value;
        }
    }

    class ItemCode {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(string $value)
        {
            $this->value = $value;
        }
    }

    class UnitPrice {
        use ValueObjectTrait;

        private string $value = "";
        
        public function __construct(float $value)
        {
            if($value < 0 )
            {
                $value = 0;
                //throw new Exception(self::class . ": Must be a number greater than or equal to zero", 422);
            }
            $this->value = $value;
        }
    }

    class TenantId 
    {
        use ValueObjectTrait; 

        private string $value = "";

        public function __construct(string $value)
        {
            if($value === "")
            {
                throw new Exception(self::class . ": Null is not allowed.", 422);
            }
            $this->value = $value;
        }
    }

    class SerialNo {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(string $value)
        {
            $this->value = $value;
        }
    }

    class RackName {

        use ValueObjectTrait;

        private string $value = "";
        public function __construct(string $value)
        {
            $this->value = $value;
        }
    }

    class PriceId {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(string $value)
        {
            $this->value = $value;
        }
    }

    class Price {
        use ValueObjectTrait;

        private string $value = "";
        
        public function __construct(float $value)
        {
            $this->value = $value;
        }
    }

    class OrderQuantity {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(int $value)
        {
            $this->value = $value;
        }

        public function add(int $value)
        {
            return new OrderQuantity( ($this->value + $value) );
        }

        public static function isValid(int $value)
        {
        }
    }

    class ReceivedQuantity {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(int $value)
        {
            $this->value = $value;
        }

        public function add(ReceivedQuantity $value)
        {
            return new ReceivedQuantity( ($this->value + $value->value()) );
        }

        public static function isValid(int $value)
        {
        }
    }

    class ReturnQuantity {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(int $value)
        {
            $this->value = $value;
        }

        public function add(ReturnQuantity $value)
        {
            return new ReturnQuantity( ($this->value + $value->value()) );
        }

        public static function isValid(int $value)
        {
        }
    }

    class OrderHistoryStatus {

        use ValueObjectTrait;

        const Unordered = 1;//未発注
        const Ordered = 2;//発注
        const ReceivedOrder = 3;//受注
        const DeliveryDateReported = 4;//受注
        const PartialReceivingComplete = 5;//受注
        const ReceivingComplete = 6;//受注
        const DeliveryReset = 7;//受注
        const Rental = 8;//受注

        public function __construct(string $value)
        {
            switch($value){
                case self::Unordered :
                    $this->value = self::Unordered;
                    break;
                    
                case self::Ordered :
                    $this->value = self::Ordered;
                    break;
                    
                case self::ReceivedOrder :
                    $this->value = self::ReceivedOrder;
                    break;
                
                case self::DeliveryDateReported :
                    $this->value = self::DeliveryDateReported;
                    break;
            
                case self::PartialReceivingComplete :
                    $this->value = self::PartialReceivingComplete;
                    break;
        
                case self::ReceivingComplete :
                    $this->value = self::ReceivingComplete;
                    break;

                case self::DeliveryReset :
                    $this->value = self::DeliveryReset;
                    break;

                case self::Rental :
                    $this->value = self::Rental;
                    break;
                default :
                    throw new Exception(self::class." Is Not Value", 422);
            }
        }
    }
/*
    class OrderHistoryId {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(string $value)
        {
            $this->value = $value;
        }
        
        public static function generateId() :OrderHistoryId
        {
            $id = "03";
            $id .= date("ymdHis");
            $id .= str_pad(substr(rand(),0,3) , 4, "0"); 
            return new OrderHistoryId($id);
        }
    }
*/
    class InHospitalItemId 
    {
        use ValueObjectTrait; 

        private string $value = "";

        public function __construct(string $value)
        {
            if($value === "")
            {
                throw new Exception(self::class . ": Null is not allowed.", 422);
            }
            $this->value = $value;
        }
    }

    class HospitalName 
    {
        use ValueObjectTrait; 

        private string $value = "";

        public function __construct(string $value)
        {
            if($value === "")
            {
                throw new Exception(self::class . ": Null is not allowed.", 422);
            }
            $this->value = $value;
        }
    }

    class HospitalId 
    {
        use ValueObjectTrait; 

        private string $value = "";

        public function __construct(string $value)
        {
            if($value === "")
            {
                throw new Exception(self::class . ": Null is not allowed.", 422);
            }
            $this->value = $value;
        }
    }

    class DivisionName 
    {
        use ValueObjectTrait; 

        private string $value = "";

        public function __construct(string $value)
        {
            $this->value = $value;
        }
    }


    class DivisionId 
    {
        use ValueObjectTrait; 

        private string $value = "";

        public function __construct(string $value)
        {
            if($value === "")
            {
                throw new Exception(self::class . ": Null is not allowed.", 422);
            }
            $this->value = $value;
        }
    }

    class DistributorId 
    {
        use ValueObjectTrait; 

        private string $value = "";

        public function __construct(string $value)
        {
            if($value === "")
            {
                throw new Exception(self::class . ": Null is not allowed.", 422);
            }
            $this->value = $value;
        }
    }


    class CatalogNo {
        use ValueObjectTrait;

        private string $value = "";
        public function __construct(string $value)
        {
            $this->value = $value;
        }
    }
    
    class ConsumptionId {
        
        use ValueObjectTrait;

        private string $value = "";
        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value)
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('02');
            if( in_array($id, self::$values, true) ){
                return self::generate(); 
            }
            self::$values[] = $id;

            usleep(1000);
            return new ConsumptionId($id);
        }
    }

    class CardId {
        
        use ValueObjectTrait;

        private string $value = "";
        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value)
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('90');
            if( in_array($id, self::$values, true) ){
                return self::generate(); 
            }
            self::$values[] = $id;

            usleep(1000);
            return new ConsumptionId($id);
        }
    }

    
    class OrderId {
        
        use ValueObjectTrait;

        private string $value = "";
        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value)
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0"); 
            
            $id = uniqid('03');
            if( in_array($id, self::$values, true) ){
                return self::generate(); 
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }


    class OrderItemId {
        
        use ValueObjectTrait;

        private string $value = "";
        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value)
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0"); 
            
            $id = uniqid('BO');
            if( in_array($id, self::$values, true) ){
                return self::generate(); 
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    
    class ReceivedId {
        
        use ValueObjectTrait;

        private string $value = "";
        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value)
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0"); 
            
            $id = uniqid('04');
            if( in_array($id, self::$values, true) ){
                return self::generate(); 
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class ReturnId {
        
        use ValueObjectTrait;

        private string $value = "";
        private static array $values = [];
        private static int $count = 0;

        public function __construct(string $value)
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0"); 
            
            $id = uniqid('06');
            if( in_array($id, self::$values, true) ){
                return self::generate(); 
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class ReturnItemId {
        
        use ValueObjectTrait;

        private string $value = "";
        private static array $values = [];
        private static int $count = 0;

        public function __construct(string $value)
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0"); 
            
            $id = uniqid('ret_');
            if( in_array($id, self::$values, true) ){
                return self::generate(); 
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }
    

    class ReceivedItemId {
        
        use ValueObjectTrait;

        private string $value = "";
        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value)
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0"); 
            
            $id = uniqid('rec_');
            if( in_array($id, self::$values, true) ){
                return self::generate(); 
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class OrderAdjustment {
 
        use ValueObjectTrait;
        
        const FixedQuantityOrder = 1;
        const IndividualOrder = 2;//発注

        public function __construct(string $value)
        {
            switch($value){
                case self::FixedQuantityOrder :
                    $this->value = self::FixedQuantityOrder;
                    break;
                    
                case self::IndividualOrder :
                    $this->value = self::IndividualOrder;
                    break;
                default :
                    $this->value = self::IndividualOrder;
                    break;
            }
        }

        public function toString(){
            switch($this->value){
                case self::FixedQuantityOrder :
                    return "定数発注";
                    break;
                    
                case self::IndividualOrder :
                    return "個別発注";
                    break;
                default :
                    return "";
                    break;
            }
        }
    }


    class DateYearMonthDay { 
 
        use ValueObjectTrait;

        const FORMAT_DELIMITER_SLASH = "/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\/([1-9]|0[1-9]|[12][0-9]|3[01])$/";
        const FORMAT_DELIMITER_HYPHEN = "/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[12][0-9]|3[01])$/";
        const FORMAT_DELIMITER_JAPANESE_CHARACTER = "/^([0-9]{4}|[0-9]{3}|[0-9]{2}|[0-9]{1})年([1-9]|0[1-9]|1[0-2])月([1-9]|0[1-9]|[12][0-9]|3[01])日$/";


        private string $value = "";
        public function __construct(string $value)
        {
            if(!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }   
            if( preg_match(self::FORMAT_DELIMITER_SLASH, $value) )
            {
                $value = $value;
                $date = DateTime::createFromFormat("Y/m/d", $value);
            }
            if( preg_match(self::FORMAT_DELIMITER_HYPHEN, $value) )
            {
                $value = $value;
                $date = DateTime::createFromFormat("Y-m-d", $value);
            }
            if( preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value) )
            {
                $value = $value;
                $date = DateTime::createFromFormat("Y年m月d日", $value);
            }

            $this->date = $date;
            $this->value = $value;
        }

        public function format($format){
            return $this->date->format($format);
        }
        
        public static function isValid(string $value)
        {
            if( $value === "" || $value === null )
            {
                return true;
            }
            if( $value === "now")
            {
                return true;
            }
            if (preg_match(self::FORMAT_DELIMITER_SLASH, $value) ||
                preg_match(self::FORMAT_DELIMITER_HYPHEN, $value) ||
                preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value))
            {
                return true;
            }
            return false;
        }
    }

    /**
     * 数字・記号・アルファベット(32 bytes)
     */
    class NumberSymbolAlphabet32Bytes
    {
        use ValueObjectTrait;
        
        const FORMAT = "/[ぁ-ん]+|[ァ-ヴー]+|[一-龠]/u";
        const LIMIT_BYTES_NUMBER = 32;

        public function __construct($value)
        {
            if(!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if( $value === "" || $value === null )
            {
                return true;
            }

            if (preg_match(NumberSymbolAlphabet32Bytes::FORMAT, $value)) {
                return false;
            }

            if (self::isLimitOverSingleByteSentence($value, self::LIMIT_BYTES_NUMBER)) {
                return false;
            }

            return true;
        }
    }

    class DateYearMonthDayHourMinutesSecond
    {
        use ValueObjectTrait;

        const FORMAT_DELIMITER_SLASH = "/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01]) ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/";
        const FORMAT_DELIMITER_HYPHEN = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/";
        const FORMAT_DELIMITER_JAPANESE_CHARACTER = "/^([0-9]{4}|[0-9]{3}|[0-9]{2}|[0-9]{1})年([1-9]|0[1-9]|1[0-2])月([1-9]|[0-2][0-9]|3[01])日 ([0-9]|[0-1][0-9]|2[0-3])時([0-9]|[0-5][0-9])分([0-9]|[0-5][0-9])秒$/";

        public function __construct($value)
        {
            if(!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }   
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if( $value === "" || $value === null )
            {
                return true;
            }
            if( $value === "now")
            {
                return true;
            }
            if (preg_match(self::FORMAT_DELIMITER_SLASH, $value) ||
                preg_match(self::FORMAT_DELIMITER_HYPHEN, $value) ||
                preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value))
            {
                return true;
            }
            return false;
        }

    }

    /**
     * テキストフィールド(32 bytes)
     */
    class TextFieldType32Bytes
    {
        use ValueObjectTrait;
        
        const LIMIT_BYTES_NUMBER = 32;

        public function __construct($value)
        {
            if (!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value))
            {
                return true;
            }

            if (self::isLimitOverMultiByteSentence($value, self::LIMIT_BYTES_NUMBER)) {
                return false;
            }

            return true;
        }
    }

    /**
     * テキストフィールド(64 bytes)
     */
    class TextFieldType64Bytes
    {
        use ValueObjectTrait;
        
        const LIMIT_BYTES_NUMBER = 64;

        public function __construct($value)
        {
            if (!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value))
            {
                return true;
            }

            if (self::isLimitOverMultiByteSentence($value, self::LIMIT_BYTES_NUMBER)) {
                return false;
            }

            return true;
        }
    }


    /**
     * テキストフィールド(128 bytes)
     */
    class TextFieldType128Bytes
    {
        use ValueObjectTrait;
        
        const LIMIT_BYTES_NUMBER = 128;

        public function __construct($value)
        {
            if (!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value))
            {
                return true;
            }

            if (self::isLimitOverMultiByteSentence($value, self::LIMIT_BYTES_NUMBER)) {
                return false;
            }

            return true;
        }
    }

    /**
     * テキストエリア(256 bytes)
     */
    class TextArea256Bytes
    {
        use ValueObjectTrait;
        
        const LIMIT_BYTES_NUMBER = 256;

        public function __construct($value)
        {
            if (!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value))
            {
                return true;
            }

            if (self::isLimitOverMultiByteSentence($value, self::LIMIT_BYTES_NUMBER)) {
                return false;
            }

            return true;
        }
    }
    
    
    /**
     * テキストエリア(512 bytes)
     */
    class TextArea512Bytes
    {
        use ValueObjectTrait;
        
        const LIMIT_BYTES_NUMBER = 512;

        public function __construct($value)
        {
            if (!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value))
            {
                return true;
            }

            if (self::isLimitOverMultiByteSentence($value, self::LIMIT_BYTES_NUMBER)) {
                return false;
            }

            return true;
        }
    }

    /**
     * テキストエリア(1024 bytes)
     */
    class TextArea1024Bytes
    {
        use ValueObjectTrait;
        
        const LIMIT_BYTES_NUMBER = 1024;

        public function __construct($value)
        {
            if (!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value))
            {
                return true;
            }

            if (self::isLimitOverMultiByteSentence($value, self::LIMIT_BYTES_NUMBER)) {
                return false;
            }

            return true;
        }
    }

    /**
     * テキストエリア(4096 bytes)
     */
    class TextArea4096Bytes
    {
        use ValueObjectTrait;

        const LIMIT_BYTES_NUMBER = 4096;

        public function __construct($value)
        {
            if (!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value))
            {
                return true;
            }

            if (self::isLimitOverMultiByteSentence($value, self::LIMIT_BYTES_NUMBER)) {
                return false;
            }

            return true;
        }
    }

    /**
     * テキストエリア(8192 bytes)
     */
    class TextArea8192Bytes
    {
        use ValueObjectTrait;

        const LIMIT_BYTES_NUMBER = 8192;

        public function __construct($value)
        {
            if (!self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value))
            {
                return true;
            }

            if (self::isLimitOverMultiByteSentence($value, self::LIMIT_BYTES_NUMBER)) {
                return false;
            }

            return true;
        }
    }

    class DateYearMonth
    {
        use ValueObjectTrait;

        private DateTime $date;

        const FORMAT_DELIMITER_SLASH = "/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\$/";
        const FORMAT_DELIMITER_HYPHEN = "/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])$/";
        const FORMAT_DELIMITER_JAPANESE_CHARACTER = "/^[0-9]{4}年([1-9]|0[1-9]|1[0-2])月$/";

        public function __construct($value)
        {
            if(! self::isValid($value))
            {
                throw new Exception(self::class . " is valid error.", 422);
            }

            if( preg_match(self::FORMAT_DELIMITER_SLASH, $value) )
            {
                $value = $value . "/01";
                $date = DateTime::createFromFormat("Y/m/d", $value);
            }
            if( preg_match(self::FORMAT_DELIMITER_HYPHEN, $value) )
            {
                $value = $value . "-01";
                $date = DateTime::createFromFormat("Y-m-d", $value);
            }
            if( preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value) )
            {
                $value = $value . "01日";
                $date = DateTime::createFromFormat("Y年m月d日", $value);
            }
            $this->date = $date;
            $this->value = $date->format("Y-m-d");
        }

        public static function isValid($value)
        {
            if( $value === "" || $value === null )
            {
                return true;
            }
            if( $value === "now")
            {
                return true;
            }
            if (preg_match(self::FORMAT_DELIMITER_SLASH, $value) ||
                preg_match(self::FORMAT_DELIMITER_HYPHEN, $value) ||
                preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value))
            {
                return true;
            }
            return false;
        }

        public function format($format){
            return $this->date->format($format);
        }

        public function nextMonth()
        {
            $date = $this->format('Y-m-d');
            $val = DateTime::createFromFormat("Y-m-d", $date);
            return new DateYearMonth($val->modify('+1 month')->format("Y-m"));
        }
    }

    class ConsumptionStatus {
        
        use ValueObjectTrait;

        const Consumption = 1;//通常消費
        const Borrowing = 2;//貸出品

        public function __construct(string $value = self::Consumption )
        {
            switch($value){
                case self::Consumption :
                    $this->value = self::Consumption;
                    break;
                    
                case self::Borrowing :
                    $this->value = self::Borrowing;
                    break;
                default :
                    $this->value = self::Consumption;
                    break;
            }
        }

        public function toString(){
            switch($this->value){
                case self::Consumption :
                    return "通常消費";
                    break;
                    
                case self::Borrowing :
                    return "貸出品";
                    break;
                default :
                    return "";
                    break;
            }
        }
    }

    
    class ReceivedStatus {
        
        use ValueObjectTrait;

        const Received = 1;//通常消費
        const Borrowing = 2;//貸出品

        public function __construct(string $value = self::Received )
        {
            switch($value){
                case self::Received :
                    $this->value = self::Received;
                    break;
                    
                case self::Borrowing :
                    $this->value = self::Borrowing;
                    break;
                default :
                    $this->value = self::Received;
                    break;
            }
        }

        public function toString(){
            switch($this->value){
                case self::Received :
                    return "通常入庫";
                    break;
                    
                case self::Borrowing :
                    return "貸出品";
                    break;
                default :
                    return "";
                    break;
            }
        }
    }
    
    class OrderStatus {
        
        use ValueObjectTrait;
        /**
         * 1	未発注
         * 2	発注完了
         * 3	受注完了
         * 4	納期報告済
         * 5	一部入庫完了
         * 6	入庫完了
         * 7	納品取消
         * 8	貸出品
         */

        const UnOrdered = 1;//未発注
        const OrderCompletion = 2;//発注完了
        const OrderFinished = 3;//受注完了
        const DeliveryDateReported = 4;//納期報告済
        const PartOfTheCollectionIsIn = 5;//一部入庫完了
        const ReceivingIsComplete = 6;//入庫完了
        const DeliveryIsCanceled = 7;//納品取消
        const Borrowing = 8;//貸出品

        public function __construct($value = self::UnOrdered )
        {
            switch($value){
                case self::UnOrdered :
                    $this->value = self::UnOrdered;
                    break;
                    
                case self::OrderCompletion :
                    $this->value = self::OrderCompletion;
                    break;
                case self::OrderFinished :
                    $this->value = self::OrderFinished;
                    break;
                case self::DeliveryDateReported :
                    $this->value = self::DeliveryDateReported;
                    break;
                case self::PartOfTheCollectionIsIn :
                    $this->value = self::PartOfTheCollectionIsIn;
                    break;
                case self::ReceivingIsComplete :
                    $this->value = self::ReceivingIsComplete;
                    break;
                case self::DeliveryIsCanceled :
                    $this->value = self::DeliveryIsCanceled;
                    break;
                case self::Borrowing :
                    $this->value = self::Borrowing;
                    break;
                default :
                    $this->value = self::UnOrdered;
                    break;
            }
        }

        public function toString(){
            switch($this->value){
                case self::UnOrdered :
                    return "未発注";
                    break;
                    
                case self::OrderCompletion :
                    return "発注完了";
                    break;
                case self::OrderFinished :
                    return "受注完了";
                    break;
                case self::DeliveryDateReported :
                    return "納期報告済";
                    break;
                case self::PartOfTheCollectionIsIn :
                    return "一部入庫完了";
                    break;
                case self::ReceivingIsComplete :
                    return "入庫完了";
                    break;
                case self::DeliveryIsCanceled :
                    return "納品取消";
                    break;
                case self::Borrowing :
                    return "貸出品";
                    break;
                default :
                    return "";
                    break;
            }
        }

        public static function list()
        {
            return [
                self::UnOrdered,
                self::OrderCompletion,
                self::OrderFinished,
                self::DeliveryDateReported,
                self::PartOfTheCollectionIsIn,
                self::ReceivingIsComplete,
                self::DeliveryIsCanceled,
                self::Borrowing
            ];
        }
    }
    
    class OrderItemReceivedStatus {
        
        use ValueObjectTrait;

        const NotInStock = 1;//未入庫
        const PartOfTheCollectionIsIn = 2;//一部入庫完了
        const ReceivingIsComplete = 3;//入庫完了

        public function __construct($value = self::NotInStock )
        {
            switch($value){
                case self::NotInStock :
                    $this->value = self::NotInStock;
                    break;
                    
                case self::PartOfTheCollectionIsIn :
                    $this->value = self::PartOfTheCollectionIsIn;
                    break;
                case self::ReceivingIsComplete :
                    $this->value = self::ReceivingIsComplete;
                    break;
                default :
                    $this->value = self::NotInStock;
                    break;
            }
        }

        public function toString(){
            switch($this->value){
                case self::NotInStock :
                    return "未入庫";
                    break;
                    
                case self::PartOfTheCollectionIsIn :
                    return "一部入庫完了";
                    break;
                case self::ReceivingIsComplete :
                    return "入庫完了";
                    break;
                default :
                    return "";
                    break;
            }
        }
    }
    
    class SelectName {
        use ValueObjectTrait;

        private string $value = "";
        private static array $values = [];
        public function __construct(string $value)
        {
            $this->value = $value;
        }

        public static function generate($prefix)
        {
            $id = uniqid($prefix);
            if( in_array($id, self::$values, true) ){
                return self::generate($prefix); 
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }


    /**
     * 都道府県
     */
    class Pref
    {
        use ValueObjectTrait;
        const ALLOW_LIST = array(
            "北海道",
            "青森県",
            "岩手県",
            "宮城県",
            "秋田県",
            "山形県",
            "福島県",
            "茨城県",
            "栃木県",
            "群馬県",
            "埼玉県",
            "千葉県",
            "東京都",
            "神奈川県",
            "新潟県",
            "富山県",
            "石川県",
            "福井県",
            "山梨県",
            "長野県",
            "岐阜県",
            "静岡県",
            "愛知県",
            "三重県",
            "滋賀県",
            "京都府",
            "大阪府",
            "兵庫県",
            "奈良県",
            "和歌山県",
            "鳥取県",
            "島根県",
            "岡山県",
            "広島県",
            "山口県",
            "徳島県",
            "香川県",
            "愛媛県",
            "高知県",
            "福岡県",
            "佐賀県",
            "長崎県",
            "熊本県",
            "大分県",
            "宮崎県",
            "鹿児島県",
            "沖縄県",
            "その他"
        );

        public function __construct($value)
        {
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (in_array($value, Pref::ALLOW_LIST, true))
            {
                return true;
            }

            return false;
        }
    }
}