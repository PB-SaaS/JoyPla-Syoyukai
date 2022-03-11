<?php

namespace Validate;

use DbFieldTypeError\FormatError;
use field\DbField;
use monad\Failed;
use monad\Try_;
use monad\TryList;

use Exception;
use function Sanitize\htmlSanitize;
use function validate\isValueEmpty;

abstract class FieldTypeValidate
{
    protected $tryDbFieldList;
    
    public $dbFieldInfo = [];
    public $startRowNumber = 0;
    public $rowData = [];

    public function __construct() {
        $this->tryDbFieldList = new TryList();
    }

    public function validate(): void
    {
        //if (!is_array($_POST["dbFieldInfo"])) {
        if (!is_array($this->dbFieldInfo)) {
            throw new Exception("post parameter '\$dbFieldInfo' is not array.");
        }

        //$startRowNumber = intval(htmlSanitize($_POST["rowStartNumber"]));
        $startRowNumber = intval(htmlSanitize($this->startRowNumber));
        if (!is_int($startRowNumber)) {
            throw new Exception("post parameter '\$rowStartNumber' is not int :" . gettype($startRowNumber));
        }
        
        
        $errorLimitCount = 1000;

        //$fieldInfoList = $_POST["dbFieldInfo"];
        $fieldInfoList = $this->dbFieldInfo;
        $rowData = $this->rowData;
        for ($i = 0; true; $i++) {
            $rowNumber = $i;
            if ($rowData[$rowNumber]['data'] === "" || is_null($rowData[$rowNumber]['data'])) {
                break;
            }
            if(count($fieldInfoList) !== count($rowData[$rowNumber]['data']))
            {
                throw new Exception("header length(".count($fieldInfoList)."), body length(".count($rowData[$rowNumber]['data']).") is not equal ");
            }
            //$row = explode("\t", $_POST["rowData[" . $rowNumber . "]"]);
            $row = $rowData[$rowNumber]['data'];
            $index = $rowData[$rowNumber]['index'];
            foreach (array_map(NULL, $fieldInfoList, $row) as [ $info, $column ]) {
                $signingColumn = (isValueEmpty($column))? "": htmlSanitize($column);
                /* 2021/11追加ここから */
                /* 
                文字列型の場合は文頭・文末の空白文字を削除 
                参考：【PHP】マルチバイト(全角スペース等)対応のtrim処理 - Qiita
                https://qiita.com/fallout/items/a13cebb07015d421fde3
                */
                $signingColumn = urldecode($signingColumn);
                $signingColumn = (is_string($signingColumn))? preg_replace('/\A[\p{Cc}\p{Cf}\p{Z}]++|[\p{Cc}\p{Cf}\p{Z}]++\z/u', '', $signingColumn): $signingColumn;
                /* 2021/11追加ここまで */
                $dbField = DbField::of($info["key"], $info["fieldType"], $info["replaceKey"], $signingColumn, $info);
                $validatedResult = $this->personalValidate($dbField);

                if ($validatedResult->isFailed()) {
                    $errorSentence = $this->makeErrorSentence($info["key"], $index, $validatedResult->getValue()->getMessage());
                    $this->tryDbFieldList->add(new Failed($errorSentence));
                }

                // エラー件数が1000件を超えたら強制的に処理を終了する
                if ($errorLimitCount <= $this->tryDbFieldList->countFailedObject()) {
                    return;
                }
            }
        }
    }

    abstract function personalValidate(Try_ $field): Try_;

    public function getTryDbFieldList(): TryList
    {
        return $this->tryDbFieldList;
    }

    private function makeErrorSentence(string $key, int $rowNumber, string $detailSentence): string
    {
        return ($rowNumber + 1 ) . "行目の" . $key . "：" . $detailSentence;
    }
}

class itemDB extends FieldTypeValidate
{
    const TARGET_NAME = "items";
    
    const SENTENCE_INPUT_REQUIRED = "値は入力必須です";
    
    public $dbFieldInfo =[ 
        [   
            'key' => 'メーカー',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '商品名',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '分類',
            'fieldType' => 'Select_',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '製品コード',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '商品規格',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => 'JANコード',
            'fieldType' => 'JanCode',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => 'カタログNo',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => 'シリアルNo',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '定価',
            'fieldType' => 'RealNumber',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '償還フラグ',
            'fieldType' => 'Boolean_',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '償還価格',
            'fieldType' => 'RealNumber',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '入数',
            'fieldType' => 'Integer_',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '入数単位',
            'fieldType' => 'TextField32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '個数単位',
            'fieldType' => 'TextField32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => 'ロット管理フラグ',
            'fieldType' => 'Boolean_',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
    ];
    
    public $rowData = [
        //"商品名\t商品コード（製品コード）\t商品規格\t1235\tメーカー名\tカタログNo\tあ000\t1\t100\t10\t枚\t個",
        ];

    public function __construct()
    {
        $this->rowData = $_POST['rowData'];
        
        $this->startRowNumber = $_POST['startRowNumber'];
        //$this->startRowNumber = $_POST['startRowNumber'];
        //$this->startRowNumber = 0;
        parent::__construct();
        $this->validate();
    }

    public function personalValidate(Try_ $field): Try_
    {
        if ($field->isFailed()) {
            return $field;
        }

        $columnObj = $field->getValue();
        if ($columnObj->isNotNullFlg() && isValueEmpty($columnObj->getValue())) {
            return new Failed(new FormatError(itemDB::SENTENCE_INPUT_REQUIRED));
        }

        return $field;
    }
}


class PriceTrDB extends FieldTypeValidate
{
    const TARGET_NAME = "PriceTrDB";
    
    const SENTENCE_INPUT_REQUIRED = "値は入力必須です";
    
    public $dbFieldInfo =[ 
        [   
            'key' => '金額管理ID',
            'fieldType' => 'NumberSymbolAlphabet32bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '卸業者ID',
            'fieldType' => 'NumberSymbolAlphabet32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '商品ID',
            'fieldType' => 'NumberSymbolAlphabet32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '入数',
            'fieldType' => 'Integer_',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '入数単位',
            'fieldType' => 'TextField32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '個数単位',
            'fieldType' => 'TextField32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '購買価格',
            'fieldType' => 'RealNumber',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '特記事項',
            'fieldType' => 'TextArea512Bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
    ];
    
    public $rowData = [
        //"商品名\t商品コード（製品コード）\t商品規格\t1235\tメーカー名\tカタログNo\tあ000\t1\t100\t10\t枚\t個",
        ];

    public function __construct()
    {
        $this->rowData = $_POST['rowData'];
        
        $this->startRowNumber = $_POST['startRowNumber'];
        parent::__construct();
        $this->validate();
    }

    public function personalValidate(Try_ $field): Try_
    {
        if ($field->isFailed()) {
            return $field;
        }

        $columnObj = $field->getValue();
        if ($columnObj->isNotNullFlg() && isValueEmpty($columnObj->getValue())) {
            return new Failed(new FormatError(itemDB::SENTENCE_INPUT_REQUIRED));
        }

        return $field;
    }
    
    
    
}


class InHospitalNewInsertDb extends FieldTypeValidate
{
    const TARGET_NAME = "InHospitalTrDb";
    
    const SENTENCE_INPUT_REQUIRED = "値は入力必須です";
    
    public $dbFieldInfo =[ 
        [   
            'key' => '商品ID',
            'fieldType' => 'NumberSymbolAlphabet32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '卸業者ID',
            'fieldType' => 'NumberSymbolAlphabet32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '不使用フラグ',
            'fieldType' => 'Boolean_',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        /*
        [   
            'key' => '定価',
            'fieldType' => 'RealNumber',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        */
        [   
            'key' => '単価',
            'fieldType' => 'RealNumber',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '測定機器名',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '入数',
            'fieldType' => 'Integer_',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '入数単位',
            'fieldType' => 'TextField32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '個数単位',
            'fieldType' => 'TextField32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '購買価格',
            'fieldType' => 'RealNumber',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '特記事項',
            'fieldType' => 'TextArea512Bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
    ];
    
    public $rowData = [
        //"商品名\t商品コード（製品コード）\t商品規格\t1235\tメーカー名\tカタログNo\tあ000\t1\t100\t10\t枚\t個",
        ];

    public function __construct()
    {
        $this->rowData = $_POST['rowData'];
        
        $this->startRowNumber = $_POST['startRowNumber'];
        parent::__construct();
        $this->validate();
    }

    public function personalValidate(Try_ $field): Try_
    {
        if ($field->isFailed()) {
            return $field;
        }

        $columnObj = $field->getValue();
        if ($columnObj->isNotNullFlg() && isValueEmpty($columnObj->getValue())) {
            return new Failed(new FormatError(InHospitalNewInsertDb::SENTENCE_INPUT_REQUIRED));
        }

        return $field;
    }
    
    
    
}


class DistributorDB extends FieldTypeValidate
{
    const TARGET_NAME = "DistributorDB";
    
    const SENTENCE_INPUT_REQUIRED = "値は入力必須です";
    
    public $dbFieldInfo =[ 
        [   
            'key' => '卸業者名',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '卸業者ID',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '共有ID',
            'fieldType' => 'NumberSymbolAlphabet32bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '郵便番号',
            'fieldType' => 'ZipNumber',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '都道府県',
            'fieldType' => 'Pref',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '住所',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '電話番号',
            'fieldType' => 'PhoneNumber',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => 'FAX番号',
            'fieldType' => 'PhoneNumber',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '見積対応可能フラグ',
            'fieldType' => 'Select_',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
    ];
    
    public $rowData = [
        //"商品名\t商品コード（製品コード）\t商品規格\t1235\tメーカー名\tカタログNo\tあ000\t1\t100\t10\t枚\t個",
        ];

    public function __construct()
    {
        $this->rowData = $_POST['rowData'];
        
        $this->startRowNumber = $_POST['startRowNumber'];
        parent::__construct();
        $this->validate();
    }

    public function personalValidate(Try_ $field): Try_
    {
        if ($field->isFailed()) {
            return $field;
        }

        $columnObj = $field->getValue();
        if ($columnObj->isNotNullFlg() && isValueEmpty($columnObj->getValue())) {
            return new Failed(new FormatError(DistributorDB::SENTENCE_INPUT_REQUIRED));
        }

        return $field;
    }
}


class AllNewItemInsertDB extends FieldTypeValidate
{
    const TARGET_NAME = "AllNewItemInsertDB";
    
    const SENTENCE_INPUT_REQUIRED = "値は入力必須です";
    
    public $dbFieldInfo =[ 
        [   
            'key' => '卸業者ID',
            'fieldType' => 'NumberSymbolAlphabet32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '商品名',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '分類',
            'fieldType' => 'Select_',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '製品コード',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '規格',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => 'JANコード',
            'fieldType' => 'JanCode',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => 'メーカー名',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => 'ロット管理フラグ',
            'fieldType' => 'Boolean_',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '償還価格フラグ',
            'fieldType' => 'Boolean_',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '償還価格',
            'fieldType' => 'RealNumber',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => 'カタログNo',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => 'シリアルNo',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '保険請求分類（医科）',
            'fieldType' => 'TextArea512Bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '保険請求分類（在宅）',
            'fieldType' => 'TextArea512Bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '入数',
            'fieldType' => 'Integer_',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '入数単位',
            'fieldType' => 'TextField32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '個数単位',
            'fieldType' => 'TextField32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '購買価格',
            'fieldType' => 'RealNumber',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '定価',
            'fieldType' => 'RealNumber',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '単価',
            'fieldType' => 'RealNumber',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '測定機器名',
            'fieldType' => 'TextField128bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
        [   
            'key' => '特記事項',
            'fieldType' => 'TextArea512Bytes',
            'replaceKey' => 't',
            'notNullFlg' => '',
        ],
    ];
    
    public $rowData = [
        //"商品名\t商品コード（製品コード）\t商品規格\t1235\tメーカー名\tカタログNo\tあ000\t1\t100\t10\t枚\t個",
        ];

    public function __construct()
    {
        $this->rowData = $_POST['rowData'];
        
        $this->startRowNumber = $_POST['startRowNumber'];
        parent::__construct();
        $this->validate();
    }

    public function personalValidate(Try_ $field): Try_
    {
        if ($field->isFailed()) {
            return $field;
        }

        $columnObj = $field->getValue();
        if ($columnObj->isNotNullFlg() && isValueEmpty($columnObj->getValue())) {
            return new Failed(new FormatError(AllNewItemInsertDB::SENTENCE_INPUT_REQUIRED));
        }

        return $field;
    }
}

class CardDB extends FieldTypeValidate
{
    const TARGET_NAME = "CardDB";
    
    const SENTENCE_INPUT_REQUIRED = "値は入力必須です";
    
    public $dbFieldInfo =[ 
        [   
            'key' => '部署ID',
            'fieldType' => 'NumberSymbolAlphabet32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '院内商品ID',
            'fieldType' => 'NumberSymbolAlphabet32bytes',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
        [   
            'key' => '入数',
            'fieldType' => 'Integer_',
            'replaceKey' => 't',
            'notNullFlg' => 't',
        ],
    ];
    
    public $rowData = [
        //"商品名\t商品コード（製品コード）\t商品規格\t1235\tメーカー名\tカタログNo\tあ000\t1\t100\t10\t枚\t個",
        ];

    public function __construct()
    {
        $this->rowData = $_POST['rowData'];
        
        $this->startRowNumber = $_POST['startRowNumber'];
        parent::__construct();
        $this->validate();
    }

    public function personalValidate(Try_ $field): Try_
    {
        if ($field->isFailed()) {
            return $field;
        }

        $columnObj = $field->getValue();
        if ($columnObj->isNotNullFlg() && isValueEmpty($columnObj->getValue())) {
            return new Failed(new FormatError(CardDB::SENTENCE_INPUT_REQUIRED));
        }

        return $field;
    }
}

