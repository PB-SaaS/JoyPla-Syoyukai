<?php

namespace JoyPla\Enterprise\Models {
    use DateTime;
    use JoyPla\Enterprise\Traits\ValueObjectTrait;
    use Exception;
    use framework\Library\SiDateTime;

    class MakerName
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class LotNumber
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid(string $value)
        {
            $value = htmlspecialchars_decode($value, ENT_QUOTES);
            if ($value === '' || $value === null) {
                return true;
            }
            if (
                preg_match('/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+/', $value) &&
                mb_strlen($value) <= 20
            ) {
                return true;
            }

            return false;
        }
    }

    class LotDate
    {
        use ValueObjectTrait;

        public const FORMAT_DELIMITER_SLASH = '/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\/([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        public const FORMAT_DELIMITER_HYPHEN = '/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        public const FORMAT_DELIMITER_JAPANESE_CHARACTER = '/^([0-9]{4}|[0-9]{3}|[0-9]{2}|[0-9]{1})年([1-9]|0[1-9]|1[0-2])月([1-9]|0[1-9]|[12][0-9]|3[01])日$/';

        private $date;
        public function __construct(string $value = '')
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }

            $date = SiDateTime::parse($value);

            $this->date = $date;

            $this->value = $value;
        }

        public static function isValid(string $value)
        {
            if ($value === '' || $value === null) {
                return true;
            }
            if (
                preg_match(self::FORMAT_DELIMITER_SLASH, $value) ||
                preg_match(self::FORMAT_DELIMITER_HYPHEN, $value) ||
                preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value)
            ) {
                return true;
            }
            return false;
        }

        public function format($format)
        {
            return $this->date->format($format);
        }
    }

    class Jancode
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            if ($value === '') {
                throw new Exception(
                    self::class . ': Null is not allowed.',
                    422
                );
            }
            $this->value = $value;
        }
    }

    class ItemStandard
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class ItemName
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class ItemId
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            if ($value === '') {
                throw new Exception(
                    self::class . ': Null is not allowed.',
                    422
                );
            }
            $this->value = $value;
        }
    }

    class ItemCode
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class UnitPrice
    {
        use ValueObjectTrait;

        public function __construct(float $value)
        {
            if ($value < 0) {
                $value = 0;
                //throw new Exception(self::class . ": Must be a number greater than or equal to zero", 422);
            }
            $this->value = $value;
        }
    }

    class TenantId
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            if ($value === '') {
                throw new Exception(
                    self::class . ': Null is not allowed.',
                    422
                );
            }
            $this->value = $value;
        }
    }

    class SerialNo
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class RackName
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class PriceId
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class Price
    {
        use ValueObjectTrait;

        public function __construct($value)
        {
            $this->value = (float) $value;
        }
    }

    class OrderQuantity
    {
        use ValueObjectTrait;

        public function __construct(int $value)
        {
            $this->value = $value;
        }

        public function add(int $value)
        {
            return new OrderQuantity($this->value + $value);
        }

        public static function isValid(int $value)
        {
        }
    }

    class ReceivedQuantity
    {
        use ValueObjectTrait;

        public function __construct(int $value)
        {
            $this->value = $value;
        }

        public function add(ReceivedQuantity $value)
        {
            return new ReceivedQuantity($this->value + $value->value());
        }

        public static function isValid(int $value)
        {
        }
    }

    class ReturnQuantity
    {
        use ValueObjectTrait;

        public function __construct(int $value)
        {
            $this->value = $value;
        }

        public function add(ReturnQuantity $value)
        {
            return new ReturnQuantity($this->value + $value->value());
        }

        public static function isValid(int $value)
        {
        }
    }

    class OrderHistoryStatus
    {
        use ValueObjectTrait;

        public const Unordered = 1; //未発注
        public const Ordered = 2; //発注
        public const ReceivedOrder = 3; //受注
        public const DeliveryDateReported = 4; //受注
        public const PartialReceivingComplete = 5; //受注
        public const ReceivingComplete = 6; //受注
        public const DeliveryReset = 7; //受注
        public const Rental = 8; //受注

        public function __construct(string $value = '')
        {
            switch ($value) {
                case self::Unordered:
                    $this->value = self::Unordered;
                    break;

                case self::Ordered:
                    $this->value = self::Ordered;
                    break;

                case self::ReceivedOrder:
                    $this->value = self::ReceivedOrder;
                    break;

                case self::DeliveryDateReported:
                    $this->value = self::DeliveryDateReported;
                    break;

                case self::PartialReceivingComplete:
                    $this->value = self::PartialReceivingComplete;
                    break;

                case self::ReceivingComplete:
                    $this->value = self::ReceivingComplete;
                    break;

                case self::DeliveryReset:
                    $this->value = self::DeliveryReset;
                    break;

                case self::Rental:
                    $this->value = self::Rental;
                    break;
                default:
                    throw new Exception(self::class . ' Is Not Value', 422);
            }
        }
    }
    /*
        class OrderHistoryId {
            use ValueObjectTrait;

            private string $value = "";
            public function __construct(string $value = '')
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

        public function __construct(string $value = '')
        {
            if ($value === '') {
                throw new Exception(
                    self::class . ': Null is not allowed.',
                    422
                );
            }
            $this->value = $value;
        }
    }

    class HospitalName
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            if ($value === '') {
                throw new Exception(
                    self::class . ': Null is not allowed.',
                    422
                );
            }

            $this->value = $value;
        }
    }

    class HospitalId
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            if ($value === '') {
                throw new Exception(
                    self::class . ': Null is not allowed.',
                    422
                );
            }
            $this->value = $value;
        }
    }

    class DivisionName
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class DivisionId
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            if ($value === '') {
                throw new Exception(
                    self::class . ': Null is not allowed.',
                    422
                );
            }
            $this->value = $value;
        }
    }

    class DistributorId
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            if ($value === '') {
                throw new Exception(
                    self::class . ': Null is not allowed.',
                    422
                );
            }
            $this->value = $value;
        }
    }

    class CatalogNo
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class ConsumptionId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('02');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new ConsumptionId($id);
        }
    }

    class ConsumptionDate
    {
        use ValueObjectTrait;

        private SiDateTime $siDateTime;

        public function __construct(string $value = '')
        {
            $this->siDateTime = new SiDateTime($value);
        }

        public function isToday()
        {
            return $this->siDateTime->isToday();
        }

        public function value()
        {
            return $this->siDateTime->toJapanDateString();
        }
    }

    class AccountantId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('80');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class AccountantItemId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('81');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class CardId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('90');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class OrderId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0");

            $id = uniqid('03');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class OrderItemId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0");

            $id = uniqid('BO');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class ReceivedId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0");

            $id = uniqid('04');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class ReturnId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0");

            $id = uniqid('06');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class ReturnItemId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0");

            $id = uniqid('ret_');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class ReceivedItemId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            //$id = "03";
            //$id .= date("ymdHis");
            //$id .= str_pad(substr(explode(".", (microtime(true) . ""))[1], 0, 4) , 4, "0");

            $id = uniqid('rec_');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class OrderAdjustment
    {
        use ValueObjectTrait;

        public const FixedQuantityOrder = 1;
        public const IndividualOrder = 2; //発注

        public function __construct(string $value = '')
        {
            switch ($value) {
                case self::FixedQuantityOrder:
                    $this->value = self::FixedQuantityOrder;
                    break;

                case self::IndividualOrder:
                    $this->value = self::IndividualOrder;
                    break;
                default:
                    $this->value = self::IndividualOrder;
                    break;
            }
        }

        public function toString()
        {
            switch ($this->value) {
                case self::FixedQuantityOrder:
                    return '定数発注';
                    break;

                case self::IndividualOrder:
                    return '個別発注';
                    break;
                default:
                    return '';
                    break;
            }
        }
    }

    class DateYearMonthDay
    {
        use ValueObjectTrait;

        public const FORMAT_DELIMITER_SLASH = '/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\/([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        public const FORMAT_DELIMITER_HYPHEN = '/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        public const FORMAT_DELIMITER_JAPANESE_CHARACTER = '/^([0-9]{4}|[0-9]{3}|[0-9]{2}|[0-9]{1})年([1-9]|0[1-9]|1[0-2])月([1-9]|0[1-9]|[12][0-9]|3[01])日$/';

        public ?DateTime $date;

        public function __construct(string $value = '')
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            if (preg_match(self::FORMAT_DELIMITER_SLASH, $value)) {
                $value = $value;
                $date = DateTime::createFromFormat('Y/m/d', $value);
            }
            if (preg_match(self::FORMAT_DELIMITER_HYPHEN, $value)) {
                $value = $value;
                $date = DateTime::createFromFormat('Y-m-d', $value);
            }
            if (preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value)) {
                $value = $value;
                $date = DateTime::createFromFormat('Y年m月d日', $value);
            }

            $this->date = $date;
            $this->value = $value;
        }

        public function format($format)
        {
            return $this->date->format($format);
        }

        public static function isValid(string $value)
        {
            if ($value === '' || $value === null) {
                return true;
            }
            if ($value === 'now') {
                return true;
            }
            if (
                preg_match(self::FORMAT_DELIMITER_SLASH, $value) ||
                preg_match(self::FORMAT_DELIMITER_HYPHEN, $value) ||
                preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value)
            ) {
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

        public const FORMAT = '/[ぁ-ん]+|[ァ-ヴー]+|[一-龠]/u';
        public const LIMIT_BYTES_NUMBER = 32;

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if ($value === '' || $value === null) {
                return true;
            }

            if (preg_match(NumberSymbolAlphabet32Bytes::FORMAT, $value)) {
                return false;
            }

            if (
                self::isLimitOverSingleByteSentence(
                    $value,
                    self::LIMIT_BYTES_NUMBER
                )
            ) {
                return false;
            }

            return true;
        }
    }

    class DateYearMonthDayHourMinutesSecond
    {
        use ValueObjectTrait;

        public const FORMAT_DELIMITER_SLASH = '/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01]) ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';
        public const FORMAT_DELIMITER_HYPHEN = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';
        public const FORMAT_DELIMITER_JAPANESE_CHARACTER = '/^([0-9]{4}|[0-9]{3}|[0-9]{2}|[0-9]{1})年([1-9]|0[1-9]|1[0-2])月([1-9]|[0-2][0-9]|3[01])日 ([0-9]|[0-1][0-9]|2[0-3])時([0-9]|[0-5][0-9])分([0-9]|[0-5][0-9])秒$/';

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if ($value === '' || $value === null) {
                return true;
            }
            if ($value === 'now') {
                return true;
            }
            if (
                preg_match(self::FORMAT_DELIMITER_SLASH, $value) ||
                preg_match(self::FORMAT_DELIMITER_HYPHEN, $value) ||
                preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value)
            ) {
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

        public const LIMIT_BYTES_NUMBER = 32;

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value)) {
                return true;
            }

            if (
                self::isLimitOverMultiByteSentence(
                    $value,
                    self::LIMIT_BYTES_NUMBER
                )
            ) {
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

        public const LIMIT_BYTES_NUMBER = 64;

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value)) {
                return true;
            }

            if (
                self::isLimitOverMultiByteSentence(
                    $value,
                    self::LIMIT_BYTES_NUMBER
                )
            ) {
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

        public const LIMIT_BYTES_NUMBER = 128;

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value)) {
                return true;
            }

            if (
                self::isLimitOverMultiByteSentence(
                    $value,
                    self::LIMIT_BYTES_NUMBER
                )
            ) {
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

        public const LIMIT_BYTES_NUMBER = 256;

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value)) {
                return true;
            }

            if (
                self::isLimitOverMultiByteSentence(
                    $value,
                    self::LIMIT_BYTES_NUMBER
                )
            ) {
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

        public const LIMIT_BYTES_NUMBER = 512;

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value)) {
                return true;
            }

            if (
                self::isLimitOverMultiByteSentence(
                    $value,
                    self::LIMIT_BYTES_NUMBER
                )
            ) {
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

        public const LIMIT_BYTES_NUMBER = 1024;

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value)) {
                return true;
            }

            if (
                self::isLimitOverMultiByteSentence(
                    $value,
                    self::LIMIT_BYTES_NUMBER
                )
            ) {
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

        public const LIMIT_BYTES_NUMBER = 4096;

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value)) {
                return true;
            }

            if (
                self::isLimitOverMultiByteSentence(
                    $value,
                    self::LIMIT_BYTES_NUMBER
                )
            ) {
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

        public const LIMIT_BYTES_NUMBER = 8192;

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (self::isValueEmpty($value)) {
                return true;
            }

            if (
                self::isLimitOverMultiByteSentence(
                    $value,
                    self::LIMIT_BYTES_NUMBER
                )
            ) {
                return false;
            }

            return true;
        }
    }

    class DateYearMonth
    {
        use ValueObjectTrait;

        private DateTime $date;

        public const FORMAT_DELIMITER_SLASH = "/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\$/";
        public const FORMAT_DELIMITER_HYPHEN = '/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])$/';
        public const FORMAT_DELIMITER_JAPANESE_CHARACTER = '/^[0-9]{4}年([1-9]|0[1-9]|1[0-2])月$/';

        public function __construct($value)
        {
            if (!self::isValid($value)) {
                throw new Exception(self::class . ' is valid error.', 422);
            }

            if (preg_match(self::FORMAT_DELIMITER_SLASH, $value)) {
                $value = $value . '/01';
                $date = DateTime::createFromFormat('Y/m/d', $value);
            }
            if (preg_match(self::FORMAT_DELIMITER_HYPHEN, $value)) {
                $value = $value . '-01';
                $date = DateTime::createFromFormat('Y-m-d', $value);
            }
            if (preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value)) {
                $value = $value . '01日';
                $date = DateTime::createFromFormat('Y年m月d日', $value);
            }
            $this->date = $date;
            $this->value = $date->format('Y-m-d');
        }

        public static function isValid($value)
        {
            if ($value === '' || $value === null) {
                return true;
            }
            if ($value === 'now') {
                return true;
            }
            if (
                preg_match(self::FORMAT_DELIMITER_SLASH, $value) ||
                preg_match(self::FORMAT_DELIMITER_HYPHEN, $value) ||
                preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value)
            ) {
                return true;
            }
            return false;
        }

        public function format($format)
        {
            return $this->date->format($format);
        }

        public function nextMonth()
        {
            $date = $this->format('Y-m-d');
            $val = DateTime::createFromFormat('Y-m-d', $date);
            return new DateYearMonth($val->modify('+1 month')->format('Y-m'));
        }
    }

    class ConsumptionStatus
    {
        use ValueObjectTrait;

        public const Consumption = 1; //通常消費
        public const Borrowing = 2; //貸出品
        public const DirectDelivery = 3; //直納処理

        public function __construct(string $value = self::Consumption)
        {
            switch ($value) {
                case self::Consumption:
                    $this->value = self::Consumption;
                    break;

                case self::Borrowing:
                    $this->value = self::Borrowing;
                    break;
                    
                case self::DirectDelivery:
                    $this->value = self::DirectDelivery;
                    break;

                default:
                    $this->value = self::Consumption;
                    break;
            }
        }

        public function toString()
        {
            switch ($this->value) {
                case self::Consumption:
                    return '通常消費';

                case self::Borrowing:
                    return '貸出品';

                case self::DirectDelivery:
                    return '直納処理';

                default:
                    return '';
            }
        }
    }

    class ReceivedStatus
    {
        use ValueObjectTrait;

        public const Received = 1; //通常消費
        public const Borrowing = 2; //貸出品

        public function __construct(string $value = self::Received)
        {
            switch ($value) {
                case self::Received:
                    $this->value = self::Received;
                    break;

                case self::Borrowing:
                    $this->value = self::Borrowing;
                    break;
                default:
                    $this->value = self::Received;
                    break;
            }
        }

        public function toString()
        {
            switch ($this->value) {
                case self::Received:
                    return '通常入庫';
                    break;

                case self::Borrowing:
                    return '貸出品';
                    break;
                default:
                    return '';
                    break;
            }
        }
    }

    class OrderStatus
    {
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

        public const UnOrdered = 1; //未発注
        public const OrderCompletion = 2; //発注完了
        public const OrderFinished = 3; //受注完了
        public const DeliveryDateReported = 4; //納期報告済
        public const PartOfTheCollectionIsIn = 5; //一部入庫完了
        public const ReceivingIsComplete = 6; //入庫完了
        public const DeliveryIsCanceled = 7; //納品取消
        public const Borrowing = 8; //貸出品

        public function __construct($value = self::UnOrdered)
        {
            switch ($value) {
                case self::UnOrdered:
                    $this->value = self::UnOrdered;
                    break;

                case self::OrderCompletion:
                    $this->value = self::OrderCompletion;
                    break;
                case self::OrderFinished:
                    $this->value = self::OrderFinished;
                    break;
                case self::DeliveryDateReported:
                    $this->value = self::DeliveryDateReported;
                    break;
                case self::PartOfTheCollectionIsIn:
                    $this->value = self::PartOfTheCollectionIsIn;
                    break;
                case self::ReceivingIsComplete:
                    $this->value = self::ReceivingIsComplete;
                    break;
                case self::DeliveryIsCanceled:
                    $this->value = self::DeliveryIsCanceled;
                    break;
                case self::Borrowing:
                    $this->value = self::Borrowing;
                    break;
                default:
                    $this->value = self::UnOrdered;
                    break;
            }
        }

        public function toString()
        {
            switch ($this->value) {
                case self::UnOrdered:
                    return '未発注';
                    break;

                case self::OrderCompletion:
                    return '発注完了';
                    break;
                case self::OrderFinished:
                    return '受注完了';
                    break;
                case self::DeliveryDateReported:
                    return '納期報告済';
                    break;
                case self::PartOfTheCollectionIsIn:
                    return '一部入庫完了';
                    break;
                case self::ReceivingIsComplete:
                    return '入庫完了';
                    break;
                case self::DeliveryIsCanceled:
                    return '納品取消';
                    break;
                case self::Borrowing:
                    return '貸出品';
                    break;
                default:
                    return '';
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
                self::Borrowing,
            ];
        }
    }

    class OrderItemReceivedStatus
    {
        use ValueObjectTrait;

        public const NotInStock = 1; //未入庫
        public const PartOfTheCollectionIsIn = 2; //一部入庫完了
        public const ReceivingIsComplete = 3; //入庫完了

        public function __construct($value = self::NotInStock)
        {
            switch ($value) {
                case self::NotInStock:
                    $this->value = self::NotInStock;
                    break;

                case self::PartOfTheCollectionIsIn:
                    $this->value = self::PartOfTheCollectionIsIn;
                    break;
                case self::ReceivingIsComplete:
                    $this->value = self::ReceivingIsComplete;
                    break;
                default:
                    $this->value = self::NotInStock;
                    break;
            }
        }

        public function toString()
        {
            switch ($this->value) {
                case self::NotInStock:
                    return '未入庫';
                    break;

                case self::PartOfTheCollectionIsIn:
                    return '一部入庫完了';
                    break;
                case self::ReceivingIsComplete:
                    return '入庫完了';
                    break;
                default:
                    return '';
                    break;
            }
        }
    }

    class SelectName
    {
        use ValueObjectTrait;

        private static array $values = [];
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate($prefix)
        {
            $id = uniqid($prefix);
            if (in_array($id, self::$values, true)) {
                return self::generate($prefix);
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class RequestHId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('13');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new RequestHId($id);
        }
    }
    class RequestId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('14');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new RequestId($id);
        }
    }

    class RequestType
    {
        use ValueObjectTrait;

        public const OrdinaryRequest = 1;
        public const ConsumptionRequest = 2;

        public function __construct(int $value)
        {
            switch ($value) {
                case self::OrdinaryRequest:
                    $this->value = self::OrdinaryRequest;
                    break;
                case self::ConsumptionRequest:
                    $this->value = self::ConsumptionRequest;
                    break;
                default:
                    return '';
                    break;
            }
        }

        public function toString()
        {
            switch ($this->value) {
                case self::OrdinaryRequest:
                    return '個別請求';
                    break;

                case self::ConsumptionRequest:
                    return '消費請求';
                    break;
                default:
                    return '';
                    break;
            }
        }
    }

    class RequestQuantity
    {
        use ValueObjectTrait;

        public function __construct(int $value)
        {
            $this->value = $value;
        }

        public function add(RequestQuantity $value)
        {
            return new RequestQuantity($this->value + $value->value());
        }

        public static function isValid(int $value)
        {
        }
    }

    class PayoutHistoryId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('05');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }
    
    class PayoutItemId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('payout_');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class PayoutQuantity
    {
        use ValueObjectTrait;

        public function __construct(int $value)
        {
            $this->value = $value;
        }

        public function add(PayoutQuantity $value)
        {
            return new PayoutQuantity($this->value + $value->value());
        }

        public static function isValid(int $value)
        {
        }
    }

    /**
     * 都道府県
     */
    class Pref
    {
        use ValueObjectTrait;
        public const ALLOW_LIST = [
            '北海道',
            '青森県',
            '岩手県',
            '宮城県',
            '秋田県',
            '山形県',
            '福島県',
            '茨城県',
            '栃木県',
            '群馬県',
            '埼玉県',
            '千葉県',
            '東京都',
            '神奈川県',
            '新潟県',
            '富山県',
            '石川県',
            '福井県',
            '山梨県',
            '長野県',
            '岐阜県',
            '静岡県',
            '愛知県',
            '三重県',
            '滋賀県',
            '京都府',
            '大阪府',
            '兵庫県',
            '奈良県',
            '和歌山県',
            '鳥取県',
            '島根県',
            '岡山県',
            '広島県',
            '山口県',
            '徳島県',
            '香川県',
            '愛媛県',
            '高知県',
            '福岡県',
            '佐賀県',
            '長崎県',
            '熊本県',
            '大分県',
            '宮崎県',
            '鹿児島県',
            '沖縄県',
            'その他',
        ];

        public function __construct($value)
        {
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (in_array($value, Pref::ALLOW_LIST, true)) {
                return true;
            }

            return false;
        }
    }

    class AccountantMethod
    {
        use ValueObjectTrait;
        public const ALLOW_LIST = ['手動', '自動'];

        public function __construct($value)
        {
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (in_array($value, self::ALLOW_LIST, true)) {
                return true;
            }

            return false;
        }
    }

    class AccountantAction
    {
        use ValueObjectTrait;
        public const ALLOW_LIST = ['消費', '入荷', '払出', 'その他'];

        public function __construct($value)
        {
            $this->value = $value;
        }

        public static function isValid($value)
        {
            if (in_array($value, self::ALLOW_LIST, true)) {
                return true;
            }

            return false;
        }
    }

    class ItemListId //商品一覧表ID
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class ItemListRowId //商品一覧表項目ID
    {
        use ValueObjectTrait;

        public function __construct(?string $value = '')
        {
            $this->value = $value;
        }
    }

    class AcceptanceId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('40');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    
    class AcceptanceItemId
    {
        use ValueObjectTrait;

        private static array $values = [];
        private static int $count = 0;
        public function __construct(string $value = '')
        {
            $this->value = $value;
        }

        public static function generate()
        {
            $id = uniqid('41');
            if (in_array($id, self::$values, true)) {
                return self::generate();
            }
            self::$values[] = $id;

            usleep(1000);
            return new self($id);
        }
    }

    class StocktakingListId //在庫管理表ID
    {
        use ValueObjectTrait;

        public function __construct(string $value = '')
        {
            $this->value = $value;
        }
    }

    class StocktakingListRowId //在庫管理表項目ID
    {
        use ValueObjectTrait;

        public function __construct(?string $value = '')
        {
            $this->value = $value;
        }
    }

}
