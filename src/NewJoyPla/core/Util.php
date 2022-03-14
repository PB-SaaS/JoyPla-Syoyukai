<?php

namespace validate
{

    use DbFieldTypeError\DbFieldError;
    use DbFieldTypeError\FormatError;
    use Exception;
    use monad\ValidateSuccess;

    function wrapperOfNullAllowField (callable $func, $value)
    {
        if (isValueEmpty($value)) {
            return new ValidateSuccess();
        }
        return $func($value);
    }

    function isValueEmpty ($value): bool
    {
        return $value == null || $value == '';
        //return $value == null || strlen(trim($value, " \t")) === 0;
    }

    function isOnlyKana ($value): bool
    {
        return preg_match("/^[ァ-ヾ]+$/u", $value);
    }

    function isOnlyKanaAndBlankSpace($value): bool
    {
        return preg_match("/^[ァ-ヾ ]+$/u", $value);
    }

    function isLimitOverMultiByteSentence ($sentence, $byte): bool
    {
        //マルチバイトを許容する場合、文字幅からバイト数換算。
        $width = mb_strwidth($sentence, "UTF-8");
        if ($width > $byte) {
            //フィールドのバイト数を超えるとエラー
            return true;
        }
        return false;
    }

    function isLimitOverSingleByteSentence ($sentence, $byte): bool
    {
        //マルチバイトを許容しないフィールド
        if (strlen($sentence) !== mb_strwidth($sentence, "UTF-8")) {
            //マルチバイトの文字幅と文字列の長さが一致しない場合はマルチバイトを含むと判定
            return true;
        } else {
            $width = mb_strwidth($sentence, "UTF-8");
            if ($width > $byte) {
                //フィールドのバイト数を超えるとエラー
                return true;
            }
        }
        return false;
    }
}

namespace Sanitize
{
    function htmlSanitize(string $v): string {
        return htmlspecialchars($v, ENT_QUOTES);
    }
}

namespace JoyPlaError
{
    /**
     * JoyPlaのエラーを表現する規定インターフェース
     */
    interface JoyPlaError
    {
        public function getMessage(): string;
    }
}

namespace DbFieldTypeError
{

    use Exception;
    use JoyPlaError\JoyPlaError;
    use monad\Failed;
    use monad\Success;
    use monad\Try_;

    /**
     * DBフィールド別エラーを表現するクラス
     */
    class DbFieldError implements JoyPlaError
    {
        protected $message;

        public function __construct($message)
        {
            $this->message = $message;
        }

        public function getMessage(): string
        {
            return $this->message;
        }
    }

    class FormatError extends DbFieldError
    {
        public function __construct($message="フォーマットに誤りがあります")
        {
            parent::__construct($message);
        }
    }

    class IllegalValueError extends DbFieldError
    {
        public function __construct($message="入力に誤りがあります")
        {
            parent::__construct($message);
        }
    }

    class InputValueLimitError extends DbFieldError
    {
        public function __construct($message="入力値が上限値を超えています")
        {
            parent::__construct($message);
        }
    }

    class Extractor
    {
        public static function extractErrorMessage(Try_ $try_): Try_ {
            if ($try_ instanceof Failed) {
                $v =$try_->getValue();
                if ($v instanceof DbFieldError) {
                    return new Failed($v->getMessage());
                }
            }
            if ($try_ instanceof Success) {
                return $try_;
            }
            throw new Exception("dont match error type object.");
        }
    }
}

/**
 * スパイラルAPIのエラーコードを表現するバリューオブジェクト
 * 参考URL: https://qiita.com/wanko5296/items/8b470934cdc14f869a91
 */
namespace ApiErrorCode
{

    use Exception;
    use JoyPlaError\JoyPlaError;

    class FactoryApiErrorCode
    {
        private function __construct()
        {
        }

        public static function factory(int $code): ApiErrorCode
        {
            switch ($code) {
                case 10:
                    return new RequestProcessingIsTimeout();
                case 100:
                    return new AuthentificationFailed();
                case 101:
                    return new InvalidAPIToken();
                case 102:
                    return new InvalidSignature();
                case 103:
                    return new LoginLocked();
                case 111:
                    return new InvalidSession();
                case 112:
                    return new SessionExpired();
                case 121:
                    return new InvalidIdentificationInformation();
                case 122:
                    return new OutOfLoginTerm();
                case 123:
                    return new TerminalIPAddressNotPermitted();
                case 124:
                    return new InvalidClientCertification();
                case 126:
                    return new TwoFactorAuthenticationIsRequired();
                case 191:
                    return new APIAccessNotPermittedForThisAccount();
                case 192:
                    return new ThisTokenHasStoppedByTheAdministrator();
                case 193:
                    return new ThisTokenHasStoppedByTheUser();
                case 194:
                    return new InvalidAccessForIPAddressRestrictions();
                case 200:
                    return new InvalidParameterValues();
                case 201:
                    return new ParameterValueIsOutOfRange();
                case 202:
                    return new SpecifiedElementsNotExist();
                case 203:
                    return new RequestedDataNotFound();
                case 204:
                    return new ThisDatabaseCanNotBeUpdatedOrDeleted();
                case 205:
                    return new MasterDatabasesFieldCanNotBeInsertedOrUpdated();
                case 206:
                    return new CanNotInsertOrUpdateOnTableViolatesForeignKeyConstraint();
                case 207:
                    return new duplicateKeyViolatesUniqueConstraint();
                case 208:
                    return new violatesNotNullConstraint();
                case 209:
                    return new InvalidSearchConditionOperator();
                case 210:
                    return new IncludingNotAvailableField();
                case 211:
                    return new ErrorDuringParsingParameterValues();
                case 220:
                    return new IsInvalidFormat();
                case 221:
                    return new IsTooLong();
                case 222:
                    return new IsIncludingNotNumberAlphabetAndMark();
                case 230:
                    return new My_area_titleIsNotFoundInParameters();
                case 231:
                    return new My_page_idIsNotFoundInParameters();
                case 232:
                    return new Search_titleIsNotFoundInParameters();
                case 233:
                    return new InvalidFieldTypeForSearch();
                case 234:
                    return new Use_ctrIsNotFoundInParameters();
                case 235:
                    return new Totalizer_titleIsNotFoundInParameters();
                case 236:
                    return new ThisDatabaseCantEditOnlySearch();
                case 237:
                    return new unavailableOperatorSpecifiedBy();
                case 238:
                    return new UnavailableAsGROUPBY();
                case 239:
                    return new IsTooShort();
                case 240:
                    return new InvalidDeliverSchedule();
                case 241:
                    return new InvalidSubject();
                case 242:
                    return new InvalidBody_text();
                case 243:
                    return new InvalidFrom_address();
                case 244:
                    return new InvalidMail_field_title();
                case 245:
                    return new TheMailScheduleIsWithin10Minute();
                case 246:
                    return new TheMailScheduleIs00minOr30min();
                case 247:
                    return new invalidDkimSelector();
                case 248:
                    return new Rule_idIsNotFoundInParameters();
                case 249:
                    return new Db_titleIsNotFoundInParameters();
                case 250:
                    return new CanNotSendTheMail();
                case 251:
                    return new OverIdCount();
                case 252:
                    return new InvalidSelect_name();
                case 253:
                    return new ThisDatabaseIsNotDeliverable();
                case 254:
                    return new ThisRule_idIsNotReady();
                case 255:
                    return new InvalidDeliverStatus();
                case 256:
                    return new InvalidError_field_title();
                case 257:
                    return new InvalidOptout_field_title();
                case 258:
                    return new CanNotReserveBecauseDkimAdsp();
                case 259:
                    return new TitleIsNotFoundInParameters();
                case 260:
                    return new TheVirusWasDetectedIn();
                case 261:
                    return new DataFormatIsInconsisitent();
                case 262:
                    return new IsNotFoundInParameters();
                case 263:
                    return new InvalidMail_type();
                case 264:
                    return new CanNotUpdateTheMail();
                case 265:
                    return new CanNotReserveBecauseDMARCMechanism();
                case 266:
                    return new NoSenderSettingForSmimeSignature();
                case 267:
                    return new SenderSettingCantUseSmimeSignature();
                case 268:
                    return new ExpirationDateOfSmimeSignature();
                case 269:
                    return new ParameterNameInHeaderIsOnlySpecifiedBySrc();
                case 270:
                    return new TheMaximumNumberOfFieldsIsExceeded();
                case 271:
                    return new InvalidDatabaseType();
                case 280:
                    return new FileNotFound();
                case 281:
                    return new TheKeyFieldMustHaveAnSpecifiedConstraintSelected();
                case 299:
                    return new InvalidParameterValuesCode299();
                case 300:
                    return new InvalidParameters();
                case 311:
                    return new RequestDataNotFound();
                case 312:
                    return new RequestDataFormatNotFound();
                case 313:
                    return new RequestDataCanNotParsed();
                case 320:
                    return new RequestDataIsNotMultipart();
                case 321:
                    return new InvalidMultipartRequest();
                case 322:
                    return new MultipartRequestHasNoData();
                case 323:
                    return new MultipartRequestHasNoJson();
                case 324:
                    return new MultipartContentLengthIsOver();
                case 325:
                    return new FileSizeOfIsOver();
                case 326:
                    return new FileExtensionOfNotPermitted();
                case 327:
                    return new From_nameIsRequiredForSmimeSignature();
                case 399:
                    return new InvalidParametersCode399();
                case 400:
                    return new InvalidMethod();
                case 404:
                    return new NotFound();
                case 500:
                    return new InvalidHeaders();
                case 501:
                    return new XSPIRALAPIHeaderNotFound();
                case 502:
                    return new ContentTypeHeaderNotFound();
                case 503:
                    return new ContentLengthHeaderNotFound();
                case 511:
                    return new CharsetNotSpecifiedInContentTypeHeader();
                case 512:
                    return new SpecifiedAPIVersionNotSupported();
                case 600:
                    return new InvalidData();
                case 601:
                    return new DataCantBeProcessed();
                case 602:
                    return new UnsupportedData();
                case 699:
                    return new InvalidDataCode699();
                case 800:
                    return new TemporalSystemError();
                case 801:
                    return new AccessFrequencyLimitExceededScopeIs();
                case 802:
                    return new ImageMasterRecordsIsOver();
                case 803:
                    return new TooFrequentMethodCall();
                case 804:
                    return new RequestedProcessIsInProgress();
                case 805:
                    return new MaintenanceOfHistoryDBOptionIsInProgress();
                case 900:
                    return new SystemError();
                case 901:
                    return new SPIRALDBConnectionCantBeEstablished();
                case 902:
                    return new UserDBConnectionCantBeEstablished();
                case 903:
                    return new PostgresError();
                case 910:
                    return new SpecifiedSystemIsUnknown();
                case 911:
                    return new SpiralApiContextNotFound();
                case 921:
                    return new ApplicationNotFound();
                case 931:
                    return new ResponseContextCantBeCreated();
                case 932:
                    return new FoundMultipleRecords();
                case 941:
                    return new NoFrom_domainSetting();
                case 942:
                    return new CanNotCreateATempfile();
                case 943:
                    return new CanNotSaveAFile();
                default:
                    throw new Exception("Error codes do not match.");
            }
        }
    }

    abstract class ApiErrorCode implements JoyPlaError
    {

        private $code;
        private $message;
        private $messageToJapanese;

        public function __construct(int $code, string $message, string $messageToJapanese)
        {
            $this->code = $code;
            $this->message = $message;
            $this->messageToJapanese = $messageToJapanese;
        }

        function getCode(): int
        {
            return $this->code;
        }

        function getMessage(): string
        {
            return $this->message;
        }

        function getMessageToJapanese(): string
        {
            return $this->messageToJapanese;
        }
    }

    class RequestProcessingIsTimeout extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 10;
            $message = "Request processing is timeout";
            $messageToJapanese = "処理時間が一定時間を超えました";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class AuthentificationFailed extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 100;
            $message = "Authentification failed";
            $messageToJapanese = "API認証処理が失敗しました";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidAPIToken extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 101;
            $message = "Invalid API token";
            $messageToJapanese = "不正なAPIトークンを使用しようとしています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidSignature extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 102;
            $message = "Invalid signature";
            $messageToJapanese = "署名が正しくありません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class LoginLocked extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 103;
            $message = "Login locked";
            $messageToJapanese = "ログインロックされています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidSession extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 111;
            $message = "Invalid session";
            $messageToJapanese = "不正なセッションを確認しました";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class SessionExpired extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 112;
            $message = "Session expired";
            $messageToJapanese = "セッションタイムアウトです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidIdentificationInformation extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 121;
            $message = "Invalid identification information";
            $messageToJapanese = "ID、パスワード等の識別情報が不正";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class OutOfLoginTerm extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 122;
            $message = "Out of login term";
            $messageToJapanese = "ログイン可能な期間外です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class TerminalIPAddressNotPermitted extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 123;
            $message = "Terminal IP address not permitted";
            $messageToJapanese = "不正な接続元IPアドレスです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidClientCertification extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 124;
            $message = "Invalid client certification";
            $messageToJapanese = "不正なクライアント証明書です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class TwoFactorAuthenticationIsRequired extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 126;
            $message = "Two factor authentication is required";
            $messageToJapanese = "二段階認証が必要です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class APIAccessNotPermittedForThisAccount extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 191;
            $message = "API access not permitted for this account";
            $messageToJapanese = "このアカウントにはAPIの利用が許可されていない";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ThisTokenHasStoppedByTheAdministrator extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 192;
            $message = "This token has stopped by the administrator";
            $messageToJapanese = "このトークンは、パイプドビッツによりアクセスを遮断されている";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ThisTokenHasStoppedByTheUser extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 193;
            $message = "This token has stopped by the user";
            $messageToJapanese = "このトークンは、ユーザにより無効化されている";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidAccessForIPAddressRestrictions extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 194;
            $message = "Invalid access for IP address restrictions";
            $messageToJapanese = "IPアドレス制限により接続できない";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidParameterValues extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 200;
            $message = "Invalid parameter value(s)";
            $messageToJapanese = "不正なパラメータ値(汎用)";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ParameterValueIsOutOfRange extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 201;
            $message = "Parameter value is out of range";
            $messageToJapanese = "パラメータ値が範囲外です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class SpecifiedElementsNotExist extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 202;
            $message = "Specified element(s) not exist";
            $messageToJapanese = "指定された要素は存在しません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class RequestedDataNotFound extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 203;
            $message = "Requested data not found";
            $messageToJapanese = "要求されたデータは存在しません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ThisDatabaseCanNotBeUpdatedOrDeleted extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 204;
            $message = "This database can not be updated or deleted";
            $messageToJapanese = "更新や削除ができないデータベースを対象にしています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class MasterDatabasesFieldCanNotBeInsertedOrUpdated extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 205;
            $message = "Master databases field can not be inserted or updated";
            $messageToJapanese = "マスタDBのフィールドは登録、削除できません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class CanNotInsertOrUpdateOnTableViolatesForeignKeyConstraint extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 206;
            $message = "Can not insert or update on table violates foreign key constraint";
            $messageToJapanese = "DB連携による外部キー制約エラーです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class duplicateKeyViolatesUniqueConstraint extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 207;
            $message = "duplicate key violates unique constraint";
            $messageToJapanese = "データが重複しています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class violatesNotNullConstraint extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 208;
            $message = "violates not-null constraint";
            $messageToJapanese = "NOT NULLとなります";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidSearchConditionOperator extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 209;
            $message = "Invalid search condition operator";
            $messageToJapanese = "サポートしていない比較演算子が含まれている";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class IncludingNotAvailableField extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 210;
            $message = "Including not available field";
            $messageToJapanese = "DBに存在しないフィールドを指定しています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ErrorDuringParsingParameterValues extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 211;
            $message = "Error during parsing parameter value(s)";
            $messageToJapanese = "パラメータ値のパースエラー";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class IsInvalidFormat extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 220;
            $message = "? is invalid format";
            $messageToJapanese = "[フィールド名]のデータフォーマットエラーです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }


    class IsTooLong extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 221;
            $message = "? is too long";
            $messageToJapanese = "[フィールド名]のデータ長エラーです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }


    class IsIncludingNotNumberAlphabetAndMark extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 222;
            $message = "? is including not number, alphabet and mark";
            $messageToJapanese = "[フィールド名]は、英数記号でない文字を含んでいる";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class My_area_titleIsNotFoundInParameters extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 230;
            $message = "my_area_title is not found in parameters";
            $messageToJapanese = "my_area_titleが存在しません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class My_page_idIsNotFoundInParameters extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 231;
            $message = "my_page_id is not found in parameters";
            $messageToJapanese = "my_page_idが存在しません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class Search_titleIsNotFoundInParameters extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 232;
            $message = "search_title is not found in parameters";
            $messageToJapanese = "search_titleが存在しない";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidFieldTypeForSearch extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 233;
            $message = "invalid field type for search";
            $messageToJapanese = "検索で使用できないフィールドタイプです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class Use_ctrIsNotFoundInParameters extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 234;
            $message = "use_ctr is not found in parameters";
            $messageToJapanese = "パラメータにuse_ctrが含まれていない";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class Totalizer_titleIsNotFoundInParameters extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 235;
            $message = "totalizer_title is not found in parameters";
            $messageToJapanese = "パラメータにtotalizer_titleが含いません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ThisDatabaseCantEditOnlySearch extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 236;
            $message = "This database cant edit. only search.";
            $messageToJapanese = "編集（INSERT、UPDATE、DELETE）不可のデータベースです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class unavailableOperatorSpecifiedBy extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 237;
            $message = "unavailable operator specified by ?";
            $messageToJapanese = "指定したフィールドに利用できない演算子です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class UnavailableAsGROUPBY extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 238;
            $message = "unavailable ? as GROUP BY";
            $messageToJapanese = "指定したフィールドはGROUP BYに指定できません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class IsTooShort extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 239;
            $message = "? is too short";
            $messageToJapanese = "[フィールド名]の文字列が短いです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidDeliverSchedule extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 240;
            $message = "invalid deliver schedule";
            $messageToJapanese = "配信予約時刻が無効です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidSubject extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 241;
            $message = "invalid subject";
            $messageToJapanese = "配信予約のサブジェクトが無効です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidBody_text extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 242;
            $message = "invalid body_text";
            $messageToJapanese = "配信予約の本文が無効です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidFrom_address extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 243;
            $message = "invalid from_address";
            $messageToJapanese = "配信予約の差出人メールアドレスが無効です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidMail_field_title extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 244;
            $message = "invalid mail_field_title";
            $messageToJapanese = "配信予約のメールアドレスフィールドが無効です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class TheMailScheduleIsWithin10Minute extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 245;
            $message = "The mail schedule is within 10 minute";
            $messageToJapanese = "配信予約時刻まで１０分を切っています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class TheMailScheduleIs00minOr30min extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 246;
            $message = "The mail schedule is 00min or 30min";
            $messageToJapanese = "配信予約時刻は00分もしくは30分のみ指定可能";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class invalidDkimSelector extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 247;
            $message = "invalid dkim selector";
            $messageToJapanese = "dkim selectorが無効です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class Rule_idIsNotFoundInParameters extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 248;
            $message = "rule_id is not found in parameters";
            $messageToJapanese = "パラメータにrule_idが含まれていません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class Db_titleIsNotFoundInParameters extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 249;
            $message = "db_title is not found in parameters";
            $messageToJapanese = "パラメータにdb_titleが含まれていません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class CanNotSendTheMail extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 250;
            $message = "can not send the mail";
            $messageToJapanese = "サンプリング配信に失敗しました";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class OverIdCount extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 251;
            $message = "over id count";
            $messageToJapanese = "サンプリング配信で指定できるid数は100までです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidSelect_name extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 252;
            $message = "invalid select_name";
            $messageToJapanese = "配信予約のＤＢ抽出ルール名が無効";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ThisDatabaseIsNotDeliverable extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 253;
            $message = "This database is not deliverable";
            $messageToJapanese = "配信予約で指定したDBは、配信で利用できません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ThisRule_idIsNotReady extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 254;
            $message = "This rule_id is not ready";
            $messageToJapanese = "配信予約で指定したrule_idは、スタンバイOFFとなっています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidDeliverStatus extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 255;
            $message = "invalid deliver status";
            $messageToJapanese = "配信予約で指定したrule_idは、配信待機中ではありません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidError_field_title extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 256;
            $message = "invalid error_field_title";
            $messageToJapanese = "配信予約で指定した配信エラーフィールドは無効となっています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidOptout_field_title extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 257;
            $message = "invalid optout_field_title";
            $messageToJapanese = "配信予約で指定したオプトアウトフィールドは無効です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class CanNotReserveBecauseDkimAdsp extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 258;
            $message = "can not reserve because dkim adsp";
            $messageToJapanese = "差出人フィールドで指定したドメインのDKIM ADSP制限により予約できません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class TitleIsNotFoundInParameters extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 259;
            $message = "title is not found in parameters";
            $messageToJapanese = "カスタムプログラムのタイトルが指定されていない";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class TheVirusWasDetectedIn extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 260;
            $message = "The virus was detected in ?";
            $messageToJapanese = "ファイルにウィルスが含まれています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class DataFormatIsInconsisitent extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 261;
            $message = "data format is inconsisitent";
            $messageToJapanese = "データ形式が不整合です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class IsNotFoundInParameters extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 262;
            $message = "? is not found in parameters";
            $messageToJapanese = "パラメーターに[パラメーター名]が含まれていません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidMail_type extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 263;
            $message = "invalid mail_type";
            $messageToJapanese = "mail_typeの指定が不正です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class CanNotUpdateTheMail extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 264;
            $message = "can not update the mail.";
            $messageToJapanese = "指定の配信設定がAPIで更新可能なメール形式ではありません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class CanNotReserveBecauseDMARCMechanism extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 265;
            $message = "can not reserve because DMARC mechanism";
            $messageToJapanese = "差出人フィールドで指定したドメインのDMARC宣言により予約できません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class NoSenderSettingForSmimeSignature extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 266;
            $message = "no sender-setting for smime signature";
            $messageToJapanese = "S/MIME署名で使用する差出人設定が存在しません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class SenderSettingCantUseSmimeSignature extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 267;
            $message = "sender-setting cant use smime signature";
            $messageToJapanese = "差出人設定でS/MIME署名が設定されていません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ExpirationDateOfSmimeSignature extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 268;
            $message = "Expiration date of smime signature";
            $messageToJapanese = "S/MIME署名の有効期限が切れています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ParameterNameInHeaderIsOnlySpecifiedBySrc extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 269;
            $message = "Parameter \"name=\" in header is only specified by \"src\"";
            $messageToJapanese = "ヘッダのパラメータ\"name=\"は、\"src\"のみを指定することができます";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class TheMaximumNumberOfFieldsIsExceeded extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 270;
            $message = "The maximum number of fields is exceeded.";
            $messageToJapanese = "フィールド数の最大値を超えています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidDatabaseType extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 271;
            $message = "Invalid database type";
            $messageToJapanese = "指定したDBタイプは存在しません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class FileNotFound extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 280;
            $message = "file not found";
            $messageToJapanese = "ファイルが存在しません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class TheKeyFieldMustHaveAnSpecifiedConstraintSelected extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 281;
            $message = "The key field must have an specified constraint selected :";
            $messageToJapanese = "対象レコード特定用フィールドは入力必須かつ重複不可である必要があります";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidParameterValuesCode299 extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 299;
            $message = "Invalid parameter value(s) : ?";
            $messageToJapanese = "不正なパラメータ値（汎用）。プレースホルダにパラメータ名等差し替えられる";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidParameters extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 300;
            $message = "Invalid parameter(s)";
            $messageToJapanese = "不正なパラメータ（汎用）です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }


    class RequestDataNotFound extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 311;
            $message = "Request data not found";
            $messageToJapanese = "リクエストのデータが指定されていません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class RequestDataFormatNotFound extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 312;
            $message = "Request data format not found";
            $messageToJapanese = "リクエストのデータフォーマットが指定されていません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class RequestDataCanNotParsed extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 313;
            $message = "Request data can not parsed";
            $messageToJapanese = "リクエストを解析できません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class RequestDataIsNotMultipart extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 320;
            $message = "Request data is not multipart";
            $messageToJapanese = "マルチパートリクエストではありません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidMultipartRequest extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 321;
            $message = "invalid multipart request";
            $messageToJapanese = "不正なマルチパートリクエストです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class MultipartRequestHasNoData extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 322;
            $message = "Multipart request has no data";
            $messageToJapanese = "空のマルチパートリクエストです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class MultipartRequestHasNoJson extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 323;
            $message = "Multipart request has no json";
            $messageToJapanese = "マルチパートリクエストにJSONパートが含まれていません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class MultipartContentLengthIsOver extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 324;
            $message = "Multipart content length is over";
            $messageToJapanese = "マルチパートリクエストのトータルサイズが大きすぎます";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class FileSizeOfIsOver extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 325;
            $message = "file size of ? is over";
            $messageToJapanese = "指定したフィールドのファイルサイズが大きすぎます";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class FileExtensionOfNotPermitted extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 326;
            $message = "file extension of ? not permitted";
            $messageToJapanese = "指定したフィールドのファイル拡張子は許可されていません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class From_nameIsRequiredForSmimeSignature extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 327;
            $message = "from_name is required for smime signature";
            $messageToJapanese = "S/MIME署名で使用するfrom_nameが指定されていません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidParametersCode399 extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 399;
            $message = "Invalid parameter(s) : ?";
            $messageToJapanese = "不正なパラメータ（汎用）。プレースホルダにパラメータ名等差し替えられる";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidMethod extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 400;
            $message = "Invalid method";
            $messageToJapanese = "不正なメソッド（汎用）です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }
    
    class NotFound extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 404;
            $message = "Not Found";
            $messageToJapanese = "ページが見つかりませんでした";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidHeaders extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 500;
            $message = "Invalid header(s)";
            $messageToJapanese = "不正なヘッダ（汎用）です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class XSPIRALAPIHeaderNotFound extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 501;
            $message = "X-SPIRAL-API header not found";
            $messageToJapanese = "X-SPIRAL-APIヘッダを取得できません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ContentTypeHeaderNotFound extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 502;
            $message = "Content-Type header not found";
            $messageToJapanese = "Content-Typeヘッダを取得できない";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ContentLengthHeaderNotFound extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 503;
            $message = "Content-Length header not found";
            $messageToJapanese = "Content-Lengthヘッダを取得できません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class CharsetNotSpecifiedInContentTypeHeader extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 511;
            $message = "charset not specified in Content-Type header";
            $messageToJapanese = "Content-Typeヘッダにcharsetが指定いません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class SpecifiedAPIVersionNotSupported extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 512;
            $message = "Specified API version not supported";
            $messageToJapanese = "指定のAPIバージョンはサポートされていません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidData extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 600;
            $message = "Invalid data";
            $messageToJapanese = "不正なデータ（汎用）です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class DataCantBeProcessed extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 601;
            $message = "Data cant be processed";
            $messageToJapanese = "データ処理ができません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class UnsupportedData extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 602;
            $message = "Unsupported data";
            $messageToJapanese = "サポート対象外のデータです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class InvalidDataCode699 extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 699;
            $message = "Invalid data : ?";
            $messageToJapanese = "不正なデータ（汎用）。プレースホルダにパラメータ名等差し替えられる";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class TemporalSystemError extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 800;
            $message = "Temporal system error";
            $messageToJapanese = "一時的なシステムエラー（汎用）が発生しました";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class AccessFrequencyLimitExceededScopeIs extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 801;
            $message = "Access frequency limit exceeded. scope is ?.";
            $messageToJapanese = "呼びだし頻度の上限越えました";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ImageMasterRecordsIsOver extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 802;
            $message = "Image master records is over";
            $messageToJapanese = "画像DBはレコードリミットに達しています";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class TooFrequentMethodCall extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 803;
            $message = "Too frequent method call";
            $messageToJapanese = "単位時間あたりのメソッド呼び出しが多すぎます";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class RequestedProcessIsInProgress extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 804;
            $message = "Requested process is in progress";
            $messageToJapanese = "要求された処理は現在処理中でです";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class MaintenanceOfHistoryDBOptionIsInProgress extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 805;
            $message = "Maintenance of history DB option is in progress";
            $messageToJapanese = "履歴DBのメンテナンスが進行中です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class SystemError extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 900;
            $message = "System error";
            $messageToJapanese = "恒久的なシステムエラー（汎用）が発生しました";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class SPIRALDBConnectionCantBeEstablished extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 901;
            $message = "SPIRAL DB connection cant be established";
            $messageToJapanese = "SPIRAL DBへの接続エラーが発生しました";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class UserDBConnectionCantBeEstablished extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 902;
            $message = "User DB connection cant be established";
            $messageToJapanese = "ユーザDBへの接続エラーが発生しました";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class PostgresError extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 903;
            $message = "(Postgresエラー)";
            $messageToJapanese = "Postgresエラーが発生しました";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class SpecifiedSystemIsUnknown extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 910;
            $message = "Specified system is unknown";
            $messageToJapanese = "不明なシステム番号です";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class SpiralApiContextNotFound extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 911;
            $message = "SpiralApiContext not found";
            $messageToJapanese = "SpiralApiContextを取得できませんでした";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ApplicationNotFound extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 921;
            $message = "Application not found";
            $messageToJapanese = "モジュールが見つかりませんでした";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class ResponseContextCantBeCreated extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 931;
            $message = "Response context cant be created";
            $messageToJapanese = "レスポンスコンテキストを生成できません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class FoundMultipleRecords extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 932;
            $message = "Found multiple records";
            $messageToJapanese = "複数行のレコードが存在します";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class NoFrom_domainSetting extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 941;
            $message = "no from_domain setting";
            $messageToJapanese = "送信ドメインの設定が存在しません";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class CanNotCreateATempfile extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 942;
            $message = "can not create a tempfile";
            $messageToJapanese = "システムの障害により一時ファイルを作成できませんでした";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }

    class CanNotSaveAFile extends ApiErrorCode
    {
        public function __construct()
        {
            $code = 943;
            $message = "can not save a file";
            $messageToJapanese = "システムの障害によりファイルを保存できませんでした";
            parent::__construct($code, $message, $messageToJapanese);
        }
    }
}