<?php

namespace JoyPla\Enterprise\Traits;

trait ValueObjectTrait
{
    public $value;

    public function value()
    {
        return $this->value;
    }

    public function equal($value)
    {
        return $this->value === $value;
    }

    public function isEmpty()
    {
        return $this->value === null || $this->value === '';
    }

    public static function wrapperOfNullAllowField(callable $func, $value)
    {
        if (self::isValueEmpty($value)) {
            return true;
        }
        return $func($value);
    }

    public static function isValueEmpty($value): bool
    {
        return $value == null || $value == '';
    }

    public static function isOnlyKana($value): bool
    {
        return preg_match('/^[ァ-ヾ]+$/u', $value);
    }

    public static function isOnlyKanaAndBlankSpace($value): bool
    {
        return preg_match('/^[ァ-ヾ ]+$/u', $value);
    }

    public static function isLimitOverMultiByteSentence($sentence, $byte): bool
    {
        //マルチバイトを許容する場合、文字幅からバイト数換算。
        $width = mb_strwidth($sentence, 'UTF-8');
        if ($width > $byte) {
            //フィールドのバイト数を超えるとエラー
            return true;
        }
        return false;
    }

    public static function isLimitOverSingleByteSentence($sentence, $byte): bool
    {
        //マルチバイトを許容しないフィールド
        if (strlen($sentence) !== mb_strwidth($sentence, 'UTF-8')) {
            //マルチバイトの文字幅と文字列の長さが一致しない場合はマルチバイトを含むと判定
            return true;
        } else {
            $width = mb_strwidth($sentence, 'UTF-8');
            if ($width > $byte) {
                //フィールドのバイト数を超えるとエラー
                return true;
            }
        }
        return false;
    }
}
