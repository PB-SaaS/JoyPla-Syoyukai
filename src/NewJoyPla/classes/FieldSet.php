<?php

namespace validate;

use DbFieldTypeError\FormatError;
use field\DbField;
use monad\Failed;
use monad\Try_;
use monad\TryList;

use Exception;
use function Sanitize\htmlSanitize;
use function validate\isValueEmpty;

class FieldSet {

    public $name;
    public $value;

    public static function validate($fieldType , $value , $info = ['key' => 'sample','replaceKey' => 'sample'])
    {
        $signingColumn = (isValueEmpty($value))? "": htmlSanitize($value);
        $signingColumn = urldecode($signingColumn);
        $signingColumn = (is_string($signingColumn))? preg_replace('/\A[\p{Cc}\p{Cf}\p{Z}]++|[\p{Cc}\p{Cf}\p{Z}]++\z/u', '', $signingColumn): $signingColumn;

        $info['key'] = (is_null($info['key']))? 'sample' : $info['key'];
        $info['replaceKey'] = (is_null($info['replaceKey']))? 'sample' : $info['replaceKey'];

        $dbField = DbField::of($info["key"], $fieldType, $info["replaceKey"], $signingColumn, $info);
        return $dbField;
    }
}