<?php

/**
 * DBフィールドを表現する名前空間
 */

namespace field {
    use DateTime;
    use DbFieldTypeError\DbFieldError;
    use DbFieldTypeError\FormatError;
    use DbFieldTypeError\InputValueLimitError;
    use Exception;
    use monad\Success;
    use monad\Failed;
    use monad\Try_;
    use function validate\isLimitOverMultiByteSentence;
    use function validate\isLimitOverSingleByteSentence;
    use function validate\isValueEmpty;

    class DbField
    {
        private $key;
        private $fieldType;
        private $replaceKey;
        private $value;
        private $notNullFlg;

        private function __construct(
            string $key,
            string $fieldType,
            string $replaceKey,
            string $value,
            bool $notNullFlg
        ) {
            $this->key = $key;
            $this->fieldType = $fieldType;
            $this->replaceKey = $replaceKey;
            $this->value = $value;
            $this->notNullFlg = $notNullFlg;
        }

        public function getKey(): string
        {
            return $this->key;
        }

        public function getFieldType(): string
        {
            return $this->fieldType;
        }

        public function getReplaceKey(): string
        {
            return $this->replaceKey;
        }

        public function getValue(): string
        {
            return $this->value;
        }

        public function isNotNullFlg(): bool
        {
            return $this->notNullFlg;
        }

        public static function of(
            string $key,
            string $fieldType,
            string $replaceKey,
            string $value,
            array $option
        ): Try_ {
            if ($key === '' || $key === null) {
                throw new Exception("not empty variable name \$key: " . $key);
            }

            if ($fieldType === '' || $fieldType === null) {
                throw new Exception(
                    "not empty variable name \$fieldType: " . $fieldType
                );
            }

            //if ($replaceKey === "" || $replaceKey === null) {
            if ($replaceKey === null) {
                throw new Exception(
                    "not empty variable name \$replaceKey: " . $replaceKey
                );
            }

            if (count($option) === 0 || $option === null) {
                throw new Exception(
                    "not empty variable name \$option: " . $option
                );
            }

            $notNullFlg = DbField::getNotNullFlg($option);
            $val = self::getFieldTypeInValue($fieldType, $value);
            if ($val->isFailed()) {
                return $val;
            }

            return new Success(
                new DbField(
                    $key,
                    $fieldType,
                    $replaceKey,
                    $val->getValue()->getValue(),
                    $notNullFlg
                )
            );
        }

        private static function getNotNullFlg(array $option): bool
        {
            return $option['notNullFlg'] === 't';
        }

        private static function getFieldTypeInValue($fieldType, $value): Try_
        {
            switch ($fieldType) {
                case MailAddressCharIgnore::FIELD_NAME:
                    return MailAddressCharIgnore::of($value);
                case Sex::FIELD_NAME:
                    return Sex::of($value);
                case Pref::FIELD_NAME:
                    return Pref::of($value);
                case ZipNumber::FIELD_NAME:
                    return ZipNumber::of($value);
                case Boolean_::FIELD_NAME:
                    return Boolean_::of($value);
                case PhoneNumber::FIELD_NAME:
                    return PhoneNumber::of($value);
                case NumberSymbolAlphabet32Bytes::FIELD_NAME:
                    return NumberSymbolAlphabet32Bytes::of($value);
                case TextFieldType32Bytes::FIELD_NAME:
                    return TextFieldType32Bytes::of($value);
                case TextFieldType64Bytes::FIELD_NAME:
                    return TextFieldType64Bytes::of($value);
                case TextFieldType128Bytes::FIELD_NAME:
                    return TextFieldType128Bytes::of($value);
                case TextArea256Bytes::FIELD_NAME:
                    return TextArea256Bytes::of($value);
                case TextArea512Bytes::FIELD_NAME:
                    return TextArea512Bytes::of($value);
                case TextArea1024Bytes::FIELD_NAME:
                    return TextArea1024Bytes::of($value);
                case TextArea4096Bytes::FIELD_NAME:
                    return TextArea4096Bytes::of($value);
                case Select::FIELD_NAME:
                    return Select::of($value);
                case Currency::FIELD_NAME:
                    return Currency::of($value);
                case Integer_::FIELD_NAME:
                    return Integer_::of($value);
                case RealNumber::FIELD_NAME:
                    return RealNumber::of($value);
                case RegistrationDate::FIELD_NAME:
                    return RegistrationDate::of($value);
                case DateYearMonthDayHour::FIELD_NAME:
                    return DateYearMonthDayHour::of($value);
                case DateYearMonthDayHourMinutesSecond::FIELD_NAME:
                    return DateYearMonthDayHourMinutesSecond::of($value);
                case DateYearMonthDay::FIELD_NAME:
                    return DateYearMonthDay::of($value);
                case DateMonthDay::FIELD_NAME:
                    return DateMonthDay::of($value);
                case MessageDigestSHA256::FIELD_NAME:
                    return MessageDigestSHA256::of($value);
                case SimplePassword::FIELD_NAME:
                    return SimplePassword::of($value);
                case JanCode::FIELD_NAME:
                    return JanCode::of($value);
                default:
                    throw new Exception(
                        "dont match variable name \$fieldType: " . $fieldType
                    );
            }
        }
    }

    class DbFieldTypeService
    {
        /**
         * フィールドバリューオブジェクトたちは空文字及びNULLを許容している。
         * バリューオブジェクトをインスタンス化する際に値が空文字またはNULLか
         * 確認を行い、該当するならば、正常値としてFieldValueNullインスタンスを返却する関数
         */
        public static function fieldValueEmpty(
            string $fieldValue,
            callable $process
        ): Try_ {
            if (isValueEmpty($fieldValue)) {
                return new Success(new FieldValueNull());
            }
            return $process($fieldValue);
        }
    }

    abstract class DbFieldType
    {
        protected $value;

        function __construct($value)
        {
            $this->value = $value;
        }

        function getValue()
        {
            return $this->value;
        }
    }

    /**
     * フィールドのバリューの値をNULLとして表現するクラス
     */
    class FieldValueNull extends DbFieldType
    {
        const VALUE_EMPTY = '';

        public function __construct()
        {
            parent::__construct(FieldValueNull::VALUE_EMPTY);
        }
    }

    /**
     * メールアドレス(大文字小文字無視)
     */
    class MailAddressCharIgnore extends DbFieldType
    {
        const FIELD_NAME = 'MailAddressCharIgnore';

        const FORMAT = '/^[a-zA-Z0-9_+-]+(.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/';
        const CHAR_LIMIT = 129;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                $errorSentenceList = [];

                if (!preg_match(MailAddressCharIgnore::FORMAT, $v)) {
                    $errorSentenceList[] =
                        '入力内容に誤りがあります ※ 入力項目は 「「64byte」以内 @ 任意のドメイン名」です';
                }

                if (
                    isLimitOverMultiByteSentence(
                        $v,
                        MailAddressCharIgnore::CHAR_LIMIT
                    )
                ) {
                    $errorSentenceList[] =
                        '入力値が上限を超えています ※ 129bytesまで許容';
                }

                if (count($errorSentenceList) === 0) {
                    return new Success(new MailAddressCharIgnore($v));
                } else {
                    return new Failed(
                        new DbFieldError(implode('、', $errorSentenceList))
                    );
                }
            });
        }
    }

    /**
     * 性別
     */
    class Sex extends DbFieldType
    {
        const FIELD_NAME = 'Sex';

        const ALLOW_LIST = ['男', '女', 'm', 'f', '雄', '雌'];

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (in_array($v, Sex::ALLOW_LIST, true)) {
                    return new Success(new Sex($v));
                }
                return new Failed(
                    new FormatError(
                        '入力内容に誤りがあります ※ 入力項目は「男」「女」「m」「f」「雄」「雌」となります'
                    )
                );
            });
        }
    }

    /**
     * 都道府県
     */
    class Pref extends DbFieldType
    {
        const FIELD_NAME = 'Pref';

        const ALLOW_LIST = [
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

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (in_array($v, Pref::ALLOW_LIST, true)) {
                    return new Success(new Pref($v));
                }
                return new Failed(
                    new FormatError(
                        '入力内容に誤りがあります ※ 入力項目各都道府県名※ 文末の都道府県は必須 またはその他となります'
                    )
                );
            });
        }
    }

    /**
     * 郵便番号
     */
    class ZipNumber extends DbFieldType
    {
        const FIELD_NAME = 'ZipNumber';

        const FORMAT = '/^[0-9]{3}-[0-9]{4}$/';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                /*
                 * ハイフンなしは許容しない
                 */
                if (preg_match(ZipNumber::FORMAT, $v)) {
                    return new Success(new ZipNumber($v));
                }
                return new Failed(
                    new FormatError(
                        '入力値に誤りが存在します ※ フォーマット: 123-1234(ハイフンは入力必須)'
                    )
                );
            });
        }
    }

    /**
     * ブーリアン
     */
    class Boolean_ extends DbFieldType
    {
        const FIELD_NAME = 'Boolean_';

        const ALLOW_KEY_ONE = '1';
        const ALLOW_KEY_ZERO = '0';
        const ALLOW_KEY_VALUE_EMPTY = '';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                /*
                 * 「1」「0」及び空文字のみの値を許容する
                 */
                if (
                    $v === Boolean_::ALLOW_KEY_ONE ||
                    $v === Boolean_::ALLOW_KEY_ZERO ||
                    $v === self::ALLOW_KEY_VALUE_EMPTY
                ) {
                    return new Success(new Boolean_($v));
                }
                return new Failed(
                    new FormatError(
                        '入力内容に誤りがあります ※ 入力項目は「1」「0」及び空文字となります'
                    )
                );
            });
        }
    }

    /**
     * 電話番号
     */
    class PhoneNumber extends DbFieldType
    {
        const FIELD_NAME = 'PhoneNumber';

        const HYPHEN_FORMAt = '/^0\d{1,4}-\d{1,4}-\d{4}$/';
        const AREA_CODE_HYPHEN_FORMAt = '/^\d{1,4}-\d{4}$/';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                /*
                 * 以下の条件に一致した場合成功
                 */
                if (
                    preg_match(PhoneNumber::HYPHEN_FORMAt, $v) ||
                    preg_match(PhoneNumber::AREA_CODE_HYPHEN_FORMAt, $v)
                ) {
                    return new Success(new PhoneNumber($v));
                }
                return new Failed(
                    new FormatError(
                        '入力値に誤りがあります ※ フォーマット: 「XXXX-XXXX-XXXX」または「XXXX-XXXX」を許容'
                    )
                );
            });
        }
    }

    /**
     * 数字・記号・アルファベット(32 bytes)
     */
    class NumberSymbolAlphabet32Bytes extends DbFieldType
    {
        const FIELD_NAME = 'NumberSymbolAlphabet32bytes';

        const FORMAT = '/[ぁ-ん]+|[ァ-ヴー]+|[一-龠]/u';

        const LIMIT_BYTES_NUMBER = 32;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                $errorSentenceList = [];

                if (preg_match(NumberSymbolAlphabet32Bytes::FORMAT, $v)) {
                    $errorSentenceList[] =
                        'フォーマットに誤りがあります ※ フォーマット: 「数字」「記号」「アルファベット」を許容';
                }

                if (
                    isLimitOverSingleByteSentence(
                        $v,
                        NumberSymbolAlphabet32Bytes::LIMIT_BYTES_NUMBER
                    )
                ) {
                    $errorSentenceList[] =
                        '入力値が上限を超えています ※ 32bytesまで許容';
                }

                if (count($errorSentenceList) === 0) {
                    return new Success(new NumberSymbolAlphabet32Bytes($v));
                } else {
                    return new Failed(
                        new DbFieldError(implode('、', $errorSentenceList))
                    );
                }
            });
        }
    }

    /**
     * テキストフィールド(32 bytes)
     */
    class TextFieldType32Bytes extends DbFieldType
    {
        const FIELD_NAME = 'TextField32bytes';

        const LIMIT_BYTES_NUMBER = 32;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (
                    isLimitOverMultiByteSentence(
                        $v,
                        TextFieldType32Bytes::LIMIT_BYTES_NUMBER
                    )
                ) {
                    return new Failed(
                        new InputValueLimitError(
                            '文字数が上限を超えています※ 32bytesまで許容'
                        )
                    );
                } else {
                    return new Success(new TextFieldType32Bytes($v));
                }
            });
        }
    }

    /**
     * テキストフィールド(64 bytes)
     */
    class TextFieldType64Bytes extends DbFieldType
    {
        const FIELD_NAME = 'TextField64bytes';

        const LIMIT_BYTES_NUMBER = 64;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (
                    isLimitOverMultiByteSentence(
                        $v,
                        TextFieldType64Bytes::LIMIT_BYTES_NUMBER
                    )
                ) {
                    return new Failed(
                        new InputValueLimitError(
                            '文字数が上限を超えています※ 64bytesまで許容'
                        )
                    );
                } else {
                    return new Success(new TextFieldType64Bytes($v));
                }
            });
        }
    }

    /**
     * テキストフィールド(128 bytes)
     */
    class TextFieldType128Bytes extends DbFieldType
    {
        const FIELD_NAME = 'TextField128bytes';

        const LIMIT_BYTES_NUMBER = 128;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (
                    isLimitOverMultiByteSentence(
                        $v,
                        TextFieldType128Bytes::LIMIT_BYTES_NUMBER
                    )
                ) {
                    return new Failed(
                        new InputValueLimitError(
                            '文字数が上限を超えています※ 128bytesまで許容'
                        )
                    );
                } else {
                    return new Success(new TextFieldType128Bytes($v));
                }
            });
        }
    }

    /**
     * テキストエリア(256 bytes)
     */
    class TextArea256Bytes extends DbFieldType
    {
        const FIELD_NAME = 'TextArea256bytes';

        const LIMIT_BYTES_NUMBER = 256;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (
                    isLimitOverMultiByteSentence(
                        $v,
                        TextArea256Bytes::LIMIT_BYTES_NUMBER
                    )
                ) {
                    return new Failed(
                        new InputValueLimitError(
                            '文字数が上限を超えています※ 256bytesまで許容'
                        )
                    );
                } else {
                    return new Success(new TextArea256Bytes($v));
                }
            });
        }
    }

    /**
     * テキストエリア(512 bytes)
     */
    class TextArea512Bytes extends DbFieldType
    {
        const FIELD_NAME = 'TextArea512Bytes';

        const LIMIT_BYTES_NUMBER = 512;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (
                    isLimitOverMultiByteSentence(
                        $v,
                        TextArea512Bytes::LIMIT_BYTES_NUMBER
                    )
                ) {
                    return new Failed(
                        new InputValueLimitError(
                            '文字数が上限を超えています※ 512ytesまで許容'
                        )
                    );
                } else {
                    return new Success(new TextArea512Bytes($v));
                }
            });
        }
    }

    /**
     * テキストエリア(1024 bytes)
     */
    class TextArea1024Bytes extends DbFieldType
    {
        const FIELD_NAME = 'TextArea1024bytes';

        const LIMIT_BYTES_NUMBER = 1024;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (
                    isLimitOverMultiByteSentence(
                        $v,
                        TextArea1024Bytes::LIMIT_BYTES_NUMBER
                    )
                ) {
                    return new Failed(
                        new InputValueLimitError(
                            '文字数が上限を超えています※ 1024bytesまで許容'
                        )
                    );
                } else {
                    return new Success(new TextArea1024Bytes($v));
                }
            });
        }
    }

    /**
     * テキストエリア(4096 bytes)
     */
    class TextArea4096Bytes extends DbFieldType
    {
        const FIELD_NAME = 'TextArea4096bytes';

        const LIMIT_BYTES_NUMBER = 4096;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (
                    isLimitOverMultiByteSentence(
                        $v,
                        TextArea4096Bytes::LIMIT_BYTES_NUMBER
                    )
                ) {
                    return new Failed(
                        new InputValueLimitError(
                            '文字数が上限を超えています※ 4096bytesまで許容'
                        )
                    );
                } else {
                    return new Success(new TextArea4096Bytes($v));
                }
            });
        }
    }

    /**
     * セレクト
     */
    class Select extends DbFieldType
    {
        const FIELD_NAME = 'Select_';

        const NOT_SELECTED_VALUE = '';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                /*
                 * $valueの値がnull又は空文字の場合、
                 * セレクトを選択していないという事でバリューオブジェクトに空文字を代入
                 */
                if ($v == null || mb_strlen($v) === 0) {
                    return new Success(new Select(Select::NOT_SELECTED_VALUE));
                }

                /*
                 * 半角数字以外が含まれている場合失敗
                 */
                if (preg_match('/[^0-9]/', $v)) {
                    return new Failed(
                        new FormatError(
                            '入力値に不正な値があります。半角数字のみ許容'
                        )
                    );
                }

                return new Success(new Select($v));
            });
        }
    }

    /**
     * 通貨
     */
    class Currency extends DbFieldType
    {
        const FIELD_NAME = 'Currency';

        const LIMIT_BYTES_NUMBER = 9;
        const CHECK_CHARACTER = ',';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                $headStr = substr($v, 0, 1);
                $tailStr = substr($v, -1, 1);

                $errorSentenceList = [];

                /*
                 * 文頭と文末にカンマとなっている場合失敗
                 */
                if (
                    strcmp($headStr, Currency::CHECK_CHARACTER) === 0 ||
                    strcmp($tailStr, Currency::CHECK_CHARACTER) === 0
                ) {
                    $errorSentenceList[] =
                        '入力値に不正な値があります。文頭または文末にカンマ文字が存在しています';
                }

                /*
                 * 半角、全角数字及びカンマ以外が含まれている場合失敗
                 */
                if (preg_match('/[^0-9０-９,]/u', $v)) {
                    $errorSentenceList[] =
                        '入力値に不正な値があります。半角、全角数字及びカンマ以外の値が入力されています';
                }

                /*
                 * カンマ抽出後の数字の桁数が9桁以上ならば失敗
                 */
                $extractedStr = str_replace(Currency::CHECK_CHARACTER, '', $v);
                if (mb_strlen($extractedStr) > Currency::LIMIT_BYTES_NUMBER) {
                    $errorSentenceList[] =
                        '入力値に不正な値があります。9桁以上の値が入力されています';
                }

                if (count($errorSentenceList) === 0) {
                    return new Success(new Currency($v));
                } else {
                    return new Failed(
                        new DbFieldError(implode('、', $errorSentenceList))
                    );
                }
            });
        }
    }

    /**
     * 整数
     */
    class Integer_ extends DbFieldType
    {
        const FIELD_NAME = 'Integer_';

        const MAX_VALUE = 2147483647;
        const MIN_VALUE = -2147483647;
        const CHECK_CHARACTER = ',';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value)
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                $errorSentenceList = [];

                $headStr = substr($v, 0, 1);
                $tailStr = substr($v, -1, 1);

                /*
                 * 文頭と文末にカンマとなっている場合失敗
                 */
                if (
                    strcmp($headStr, Integer_::CHECK_CHARACTER) === 0 ||
                    strcmp($tailStr, Integer_::CHECK_CHARACTER) === 0
                ) {
                    $errorSentenceList[] =
                        '入力値に不正な値があります。文頭または文末にカンマ文字が存在しています';
                }

                /*
                 * 半角、全角数字及びマイナス、カンマ以外の文字列が含まれている場合失敗
                 */
                if (preg_match('/[^0-9０-９,-]/', $v)) {
                    $errorSentenceList[] =
                        '入力値に不正な値があります。半角、全角数字及びカンマ以外の値が入力されています';
                }

                /*
                 * 上限または下限を突破している場合失敗
                 */
                if ($v >= Integer_::MAX_VALUE || $v <= Integer_::MIN_VALUE) {
                    $errorSentenceList[] =
                        '入力値が上限値を超えています。上下限値:「-2147483647~2147483647」の値及び文字の中に「,」を許容';
                }

                if (count($errorSentenceList) === 0) {
                    return new Success(new Integer_($v));
                } else {
                    return new Failed(
                        new DbFieldError(implode('、', $errorSentenceList))
                    );
                }
            });
        }
    }

    /**
     * 実数
     */
    class RealNumber extends DbFieldType
    {
        const FIELD_NAME = 'RealNumber';

        const FORMAT = '/^([\+\-]|\d)?(?:|\.|\d)+\d$/';
        const FORMAT_NUMBER_COUNT_LIMIT = '/^[0-9]{1,15}$/';
        const REPLACE_LIST = ['+', '-', '.'];
        const commaLimit = 1;

        const FORMAT_ERROR_MESSAGE = '入力値に誤りがあります ※ フォーマット: 「+-.」を除いた15桁の半角数字まで許容、「.」の連続使用は不可';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                $replaceWord = str_replace(RealNumber::REPLACE_LIST, '', $v);
                $commaCount = substr_count($v, '.');
                if (!preg_match(RealNumber::FORMAT, $v)) {
                    return new Failed(
                        new FormatError(RealNumber::FORMAT_ERROR_MESSAGE)
                    );
                }

                if (
                    !preg_match(
                        RealNumber::FORMAT_NUMBER_COUNT_LIMIT,
                        $replaceWord
                    )
                ) {
                    return new Failed(
                        new FormatError(RealNumber::FORMAT_ERROR_MESSAGE)
                    );
                }

                if (RealNumber::commaLimit < $commaCount) {
                    return new Failed(
                        new FormatError(RealNumber::FORMAT_ERROR_MESSAGE)
                    );
                }

                return new Success(new RealNumber($v));
            });
        }
    }

    /**
     * 登録日時
     */
    class RegistrationDate extends DbFieldType
    {
        const FIELD_NAME = 'RegistrationDate';

        const FORMAT_DELIMITER_SLASH = '/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';
        const FORMAT_DELIMITER_HYPHEN = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';
        const FORMAT_DELIMITER_JAPANESE_CHARACTER = '/^[0-9]{4}年(0[1-9]|1[0-2])月(0[1-9]|[12][0-9]|3[01])日 ([01][0-9]|2[0-3])時[0-5][0-9]分[0-5][0-9]秒$/';
        const FORMAT_MAGIC_WORD = 'now';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                // 登録日時フィールドで「now」の文字列はDB側で現在時刻で登録することになるので、
                // 正常な値として、処理を行う
                if (RegistrationDate::FORMAT_MAGIC_WORD === $v) {
                    return new Success(new RegistrationDate($v));
                }

                // 以下のフォーマット以外はエラー処理を行う
                if (
                    preg_match(RegistrationDate::FORMAT_DELIMITER_SLASH, $v) ||
                    preg_match(RegistrationDate::FORMAT_DELIMITER_HYPHEN, $v) ||
                    preg_match(
                        RegistrationDate::FORMAT_DELIMITER_JAPANESE_CHARACTER,
                        $v
                    )
                ) {
                    return new Success(new RegistrationDate($v));
                }
                return new Failed(
                    new FormatError(
                        '入力値に誤りがあります ※ フォーマット: 「YYYY/MM/DD HH:MM:SS」「YYYY-MM-DD HH:MM:SS」「YYYY年MM月DD日 HH時MM分SS秒」'
                    )
                );
            });
        }
    }

    /**
     * 日付（○年○月○日 〇時〇分〇秒）
     */
    class DateYearMonthDayHour extends DbFieldType
    {
        const FIELD_NAME = 'DateYearMonthDayHour';

        const FORMAT_DELIMITER_SLASH = '/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0-3])$/';
        const FORMAT_DELIMITER_HYPHEN = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0-3])$/';
        const FORMAT_DELIMITER_JAPANESE_CHARACTER = '/^[0-9]{4}年(0[1-9]|1[0-2])月(0[1-9]|[12][0-9]|3[01])日 ([01][0-9]|2[0-3])時$/';

        public ?DateTime $date;

        private function __construct($value)
        {
            parent::__construct($value);
            if (preg_match(self::FORMAT_DELIMITER_SLASH, $value)) {
                $value = $value;
                $date = DateTime::createFromFormat('Y/m/d H', $value);
            }
            if (preg_match(self::FORMAT_DELIMITER_HYPHEN, $value)) {
                $value = $value;
                $date = DateTime::createFromFormat('Y-m-d H', $value);
            }
            if (preg_match(self::FORMAT_DELIMITER_JAPANESE_CHARACTER, $value)) {
                $value = $value;
                $date = DateTime::createFromFormat('Y年m月d日 H時', $value);
            }

            $this->date = $date;
        }

        public function format($format)
        {
            return $this->date->format($format);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                // 以下のフォーマット以外はエラー処理を行う
                if (
                    preg_match(
                        DateYearMonthDayHour::FORMAT_DELIMITER_SLASH,
                        $v
                    ) ||
                    preg_match(
                        DateYearMonthDayHour::FORMAT_DELIMITER_HYPHEN,
                        $v
                    ) ||
                    preg_match(
                        DateYearMonthDayHour::FORMAT_DELIMITER_JAPANESE_CHARACTER,
                        $v
                    )
                ) {
                    return new Success(new DateYearMonthDayHour($v));
                }
                return new Failed(
                    new FormatError(
                        '入力値に誤りがあります ※ フォーマット: 「YYYY/MM/DD HH」「YYYY-MM-DD HH」「YYYY年MM月DD日 HH時」'
                    )
                );
            });
        }
    }
    /**
     * 日付（○年○月○日 〇時〇分〇秒）
     */
    class DateYearMonthDayHourMinutesSecond extends DbFieldType
    {
        const FIELD_NAME = 'DateYearMonthDayHourMinutesSecond';

        const FORMAT_DELIMITER_SLASH = '/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';
        const FORMAT_DELIMITER_HYPHEN = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';
        const FORMAT_DELIMITER_JAPANESE_CHARACTER = '/^[0-9]{4}年(0[1-9]|1[0-2])月(0[1-9]|[12][0-9]|3[01])日 ([01][0-9]|2[0-3])時[0-5][0-9]分[0-5][0-9]秒$/';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                // 以下のフォーマット以外はエラー処理を行う
                if (
                    preg_match(
                        DateYearMonthDayHourMinutesSecond::FORMAT_DELIMITER_SLASH,
                        $v
                    ) ||
                    preg_match(
                        DateYearMonthDayHourMinutesSecond::FORMAT_DELIMITER_HYPHEN,
                        $v
                    ) ||
                    preg_match(
                        DateYearMonthDayHourMinutesSecond::FORMAT_DELIMITER_JAPANESE_CHARACTER,
                        $v
                    )
                ) {
                    return new Success(
                        new DateYearMonthDayHourMinutesSecond($v)
                    );
                }
                return new Failed(
                    new FormatError(
                        '入力値に誤りがあります ※ フォーマット: 「YYYY/MM/DD HH:MM:SS」「YYYY-MM-DD HH:MM:SS」「YYYY年MM月DD日 HH時MM分SS秒」'
                    )
                );
            });
        }
    }

    /**
     * 日付（○年○月○日）
     */
    class DateYearMonthDay extends DbFieldType
    {
        const FIELD_NAME = 'DateYearMonthDay';

        const FORMAT_DELIMITER_SLASH = '/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\/([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        const FORMAT_DELIMITER_HYPHEN = '/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        const FORMAT_DELIMITER_JAPANESE_CHARACTER = '/^[0-9]{4}年([1-9]|0[1-9]|1[0-2])月([1-9]|0[1-9]|[12][0-9]|3[01])日$/';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                // 以下のフォーマット以外はエラー処理を行う
                if (
                    preg_match(DateYearMonthDay::FORMAT_DELIMITER_SLASH, $v) ||
                    preg_match(DateYearMonthDay::FORMAT_DELIMITER_HYPHEN, $v) ||
                    preg_match(
                        DateYearMonthDay::FORMAT_DELIMITER_JAPANESE_CHARACTER,
                        $v
                    )
                ) {
                    return new Success(new DateYearMonthDay($v));
                }
                return new Failed(
                    new FormatError(
                        '入力値に誤りがあります ※ フォーマット: 「YYYY/MM/DD」「YYYY-MM-DD」「YYYY年MM月DD日」を許容'
                    )
                );
            });
        }
    }

    /**
     * 月日（○月○日）
     */
    class DateMonthDay extends DbFieldType
    {
        const FIELD_NAME = 'DateMonthDay';

        const FORMAT_DELIMITER_SLASH = '/^([1-9]{1}|1[0-2]{1})\/([1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1})$/';
        const FORMAT_DELIMITER_JAPANESE_CHARACTER = '/^([1-9]{1}|1[0-2]{1})月([1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1})日$/';

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (
                    preg_match(DateMonthDay::FORMAT_DELIMITER_SLASH, $v) ||
                    preg_match(
                        DateMonthDay::FORMAT_DELIMITER_JAPANESE_CHARACTER,
                        $v
                    )
                ) {
                    return new Success(new DateMonthDay($v));
                }
                return new Failed(
                    new FormatError(
                        '入力値に誤りがあります ※ フォーマット: 「MM/DD」「MM月DD日」'
                    )
                );
            });
        }
    }

    /**
     * メッセージダイジェスト (SHA256)
     */
    class MessageDigestSHA256 extends DbFieldType
    {
        const FIELD_NAME = 'MessageDigestSHA256';

        const LIMIT_BYTES_NUMBER = 128;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (
                    isLimitOverMultiByteSentence(
                        $v,
                        MessageDigestSHA256::LIMIT_BYTES_NUMBER
                    )
                ) {
                    return new Failed(
                        new InputValueLimitError(
                            '文字数が上限を超えています※ 128bytesまで許容'
                        )
                    );
                }
                return new Success(new MessageDigestSHA256($v));
            });
        }
    }

    /**
     * 簡易パスワード
     */
    class SimplePassword extends DbFieldType
    {
        const FIELD_NAME = 'SimplePassword';

        const LIMIT_BYTES_NUMBER = 16;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                if (
                    isLimitOverSingleByteSentence(
                        $v,
                        SimplePassword::LIMIT_BYTES_NUMBER
                    )
                ) {
                    return new Failed(
                        new InputValueLimitError(
                            '文字数が上限を超えています※ 16bytesまで許容'
                        )
                    );
                } else {
                    return new Success(new SimplePassword($v));
                }
            });
        }
    }

    /**
     * JoyPla専用 JANコード 数字13桁のみ
     */
    class JanCode extends DbFieldType
    {
        const FIELD_NAME = 'JanCode';

        const LENGTH = 13;

        private function __construct($value)
        {
            parent::__construct($value);
        }

        public static function of($value): Try_
        {
            return DbFieldTypeService::fieldValueEmpty($value, function ($v) {
                $errorSentenceList = [];

                /*
                 * 半角数字以外の文字列が含まれている場合失敗
                 */
                if (preg_match('/[^0-9]/', $v)) {
                    $errorSentenceList[] =
                        '入力値に不正な値があります。半角数字以外の値が入力されています';
                }

                /*
                 * 桁数を超えていた場合失敗
                 */
                if (strlen($v) !== JanCode::LENGTH) {
                    $errorSentenceList[] =
                        '入力値に誤りがあります。半角数字13桁で入力してください';
                }

                if (count($errorSentenceList) === 0) {
                    return new Success(new JanCode($v));
                } else {
                    return new Failed(
                        new DbFieldError(implode('、', $errorSentenceList))
                    );
                }
            });
        }
    }
}
