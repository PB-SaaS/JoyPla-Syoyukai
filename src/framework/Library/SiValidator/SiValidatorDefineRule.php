<?php

use framework\Library\SiValidator;
use framework\SpiralConnecter\SpiralDB;

SiValidator::defineRule('accepted', function ($value) {
    $validate = ['yes', 'on', '1', 1, true, 'true', 'はい'];

    if (in_array($value, $validate, true)) {
        return true;
    }
    return false;
});

SiValidator::defineRule('accepted_if:other,operator,if_value', function (
    $value,
    $param,
    $values
) {
    $validate = ['yes', 'on', '1', 1, true, 'true', 'はい'];

    if ($values[$param['other']] != '' && !is_null($values[$param['other']])) {
        if ($param['operator'] === '==') {
            if (!($values[$param['other']] == $param['if_value'])) {
                return true;
            }
            if (
                $values[$param['other']] == $param['if_value'] &&
                in_array($value, $validate, true)
            ) {
                return true;
            }
        }
        if ($param['operator'] === '!=') {
            if (!($values[$param['other']] != $param['if_value'])) {
                return true;
            }
            if (
                $values[$param['other']] != $param['if_value'] &&
                in_array($value, $validate, true)
            ) {
                return true;
            }
        }
    }
    return false;
});

SiValidator::defineRule('declined', function ($value) {
    $validate = ['no', 'off', '0', 0, false, 'false', 'いいえ'];

    if (in_array($value, $validate, true)) {
        return true;
    }
    return false;
});

SiValidator::defineRule('declined_if:other,operator,if_value', function (
    $value,
    $param,
    $values
) {
    $validate = ['no', 'off', '0', 0, false, 'false', 'いいえ'];
    if ($values[$param['other']] != '' && !is_null($values[$param['other']])) {
        if ($param['operator'] === '==') {
            if (!($values[$param['other']] == $param['if_value'])) {
                return true;
            }
            if (
                $values[$param['other']] == $param['if_value'] &&
                in_array($value, $validate, true)
            ) {
                return true;
            }
        }
        if ($param['operator'] === '!=') {
            if (!($values[$param['other']] != $param['if_value'])) {
                return true;
            }
            if (
                $values[$param['other']] != $param['if_value'] &&
                in_array($value, $validate, true)
            ) {
                return true;
            }
        }
    }
    return false;
});
SiValidator::defineRule('after:other', function (
    $value,
    array $param,
    $values
) {
    $changeDate = function ($v) {
        $FORMAT_DELIMITER_SLASH =
            '/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\/([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        $FORMAT_DELIMITER_HYPHEN =
            '/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        $FORMAT_DELIMITER_JAPANESE_CHARACTER =
            '/^([0-9]{4}|[0-9]{3}|[0-9]{2}|[0-9]{1})年([1-9]|0[1-9]|1[0-2])月([1-9]|0[1-9]|[12][0-9]|3[01])日$/';
        $other = null;
        if (preg_match($FORMAT_DELIMITER_SLASH, $v)) {
            $other = DateTime::createFromFormat('Y/m/d', $v);
        }
        if (preg_match($FORMAT_DELIMITER_HYPHEN, $v)) {
            $other = DateTime::createFromFormat('Y-m-d', $v);
        }
        if (preg_match($FORMAT_DELIMITER_JAPANESE_CHARACTER, $v)) {
            $other = DateTime::createFromFormat('Y年m月d日', $v);
        }
        return $other;
    };

    if (
        $changeDate($values[$param['other']]) === null ||
        $changeDate($value) === null
    ) {
        return false;
    }

    if (
        strtotime(
            $changeDate($values[$param['other']])->format('Y-m-d H:i:s')
        ) <= strtotime($changeDate($value)->format('Y-m-d H:i:s'))
    ) {
        return true;
    }
    return false;
});

SiValidator::defineRule('date_equals:date', function ($value, array $param) {
    $changeDate = function ($v) {
        $FORMAT_DELIMITER_SLASH =
            '/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\/([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        $FORMAT_DELIMITER_HYPHEN =
            '/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        $FORMAT_DELIMITER_JAPANESE_CHARACTER =
            '/^([0-9]{4}|[0-9]{3}|[0-9]{2}|[0-9]{1})年([1-9]|0[1-9]|1[0-2])月([1-9]|0[1-9]|[12][0-9]|3[01])日$/';
        $other = null;
        if (preg_match($FORMAT_DELIMITER_SLASH, $v)) {
            $other = DateTime::createFromFormat('Y/m/d', $v);
        }
        if (preg_match($FORMAT_DELIMITER_HYPHEN, $v)) {
            $other = DateTime::createFromFormat('Y-m-d', $v);
        }
        if (preg_match($FORMAT_DELIMITER_JAPANESE_CHARACTER, $v)) {
            $other = DateTime::createFromFormat('Y年m月d日', $v);
        }
        return $other;
    };
    if ($changeDate($param['date']) === null || $changeDate($value) === null) {
        return false;
    }

    if (
        strtotime($changeDate($param['date'])->format('Y-m-d H:i:s')) ==
        strtotime($changeDate($value)->format('Y-m-d H:i:s'))
    ) {
        return true;
    }
    return false;
});

SiValidator::defineRule('before:other', function (
    $value,
    array $param,
    $values
) {
    $changeDate = function ($v) {
        $FORMAT_DELIMITER_SLASH =
            '/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\/([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        $FORMAT_DELIMITER_HYPHEN =
            '/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        $FORMAT_DELIMITER_JAPANESE_CHARACTER =
            '/^([0-9]{4}|[0-9]{3}|[0-9]{2}|[0-9]{1})年([1-9]|0[1-9]|1[0-2])月([1-9]|0[1-9]|[12][0-9]|3[01])日$/';
        $other = null;
        if (preg_match($FORMAT_DELIMITER_SLASH, $v)) {
            $other = DateTime::createFromFormat('Y/m/d', $v);
        }
        if (preg_match($FORMAT_DELIMITER_HYPHEN, $v)) {
            $other = DateTime::createFromFormat('Y-m-d', $v);
        }
        if (preg_match($FORMAT_DELIMITER_JAPANESE_CHARACTER, $v)) {
            $other = DateTime::createFromFormat('Y年m月d日', $v);
        }
        return $other;
    };

    if (
        $changeDate($values[$param['other']]) === null ||
        $changeDate($value) === null
    ) {
        return false;
    }

    if (
        strtotime(
            $changeDate($values[$param['other']])->format('Y-m-d H:i:s')
        ) >= strtotime($changeDate($value)->format('Y-m-d H:i:s'))
    ) {
        return true;
    }
    return false;
});

SiValidator::defineRule('date', function ($value) {
    $changeDate = function ($v) {
        $FORMAT_DELIMITER_SLASH =
            '/^[0-9]{4}\/([1-9]|0[1-9]|1[0-2])\/([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        $FORMAT_DELIMITER_HYPHEN =
            '/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[12][0-9]|3[01])$/';
        $FORMAT_DELIMITER_JAPANESE_CHARACTER =
            '/^([0-9]{4}|[0-9]{3}|[0-9]{2}|[0-9]{1})年([1-9]|0[1-9]|1[0-2])月([1-9]|0[1-9]|[12][0-9]|3[01])日$/';
        $other = null;
        if (preg_match($FORMAT_DELIMITER_SLASH, $v)) {
            $other = DateTime::createFromFormat('Y/m/d', $v);
        }
        if (preg_match($FORMAT_DELIMITER_HYPHEN, $v)) {
            $other = DateTime::createFromFormat('Y-m-d', $v);
        }
        if (preg_match($FORMAT_DELIMITER_JAPANESE_CHARACTER, $v)) {
            $other = DateTime::createFromFormat('Y年m月d日', $v);
        }
        return $other;
    };

    if ($changeDate($value) !== null) {
        return true;
    }

    return false;
});

SiValidator::defineRule('date_format:format', function ($value, array $param) {
    $date = DateTime::createFromFormat($param['format'], $value)
        ? DateTime::createFromFormat($param['format'], $value)
        : null;

    if ($date !== null) {
        return true;
    }

    return false;
});
SiValidator::defineRule('required', function ($value) {
    if ($value != '' && !is_null($value)) {
        return true;
    }
    return false;
});

SiValidator::defineRule('min:min', function ($value, array $param) {
    if ((int) $value >= (int) $param['min']) {
        return true;
    }
    return false;
});
SiValidator::defineRule('between:min,max', function ($value, array $param) {
    $num = 0;
    if (is_int($value)) {
        $num = (int) $value;
    }
    if (is_string($value)) {
        $num = shiftjis_strlen($value);
    }
    if (is_numeric($value)) {
        $num = (float) $value;
    }
    if ($num >= (int) $param['min'] && $num <= (int) $param['max']) {
        return true;
    }
    return false;
});
SiValidator::defineRule('alpha', function ($value) {
    if (preg_match('/^[a-zA-Z]+$/', $value)) {
        return true;
    }
    return false;
});
SiValidator::defineRule('alpha_dash', function ($value) {
    if (preg_match('/^[-_a-zA-Z]+$/', $value)) {
        return true;
    }
    return false;
});
SiValidator::defineRule('alpha_num', function ($value) {
    if (preg_match('/^[0-9a-zA-Z]+$/', $value)) {
        return true;
    }
    return false;
});

SiValidator::defineRule('numeric', function ($value, $params, $values) {
    return is_numeric($value);
});

SiValidator::defineRule('boolean', function ($value) {
    $validate = ['1', '0', 1, 0, true, false, 'true', 'false'];
    if (in_array($value, $validate, true)) {
        return true;
    }
    return false;
});

SiValidator::defineRule('confirmed:other', function ($value, $param, $ctx) {
    if (
        $ctx[$param['other']] != '' &&
        !is_null($ctx[$param['other']]) &&
        $value === $ctx[$param['other']]
    ) {
        return true;
    }
    return false;
});
SiValidator::defineRule('different:value', function ($value, $param, $ctx) {
    if ($param['value'] != $value) {
        return true;
    }
    return false;
});

SiValidator::defineRule('digits:num', function ($value, $param, $ctx) {
    $digit = function ($number) {
        $number = (string) $number; // 数値を文字列に変換
        return strlen($number);
    };

    if (is_numeric($value) && $digit($value) == $param['num']) {
        return true;
    }
    return false;
});

SiValidator::defineRule('digits_between:min,max', function ($value, $param) {
    if (
        is_numeric($value) &&
        ((float) $param['min'] <= (float) $value &&
            (float) $param['max'] >= (float) $value)
    ) {
        return true;
    }
    return false;
});

SiValidator::defineRule('email', function ($value) {
    $format =
        '/^[a-zA-Z0-9_+-]+(.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/';

    if (
        preg_match($format, $value) &&
        filter_var($value, FILTER_VALIDATE_EMAIL)
    ) {
        return true;
    }
    return false;
});

SiValidator::defineRule('maxword:max', function ($value, $param) {
    $value = str_replace(["\r\n", "\r", "\n"], '', $value);
    $length = shiftjis_strlen($value);
    return (int) $param['max'] >= $length;
});

SiValidator::defineRule('not_regex:pattern', function ($value, $param) {
    if (!preg_match($param['pattern'], $value)) {
        return true;
    }
    return false;
});

SiValidator::defineRule('regex:pattern', function ($value, $param) {
    if (preg_match($param['pattern'], $value)) {
        return true;
    }
    return false;
});

SiValidator::defineRule('size:value', function ($value, $param) {
    if (
        is_string($value) &&
        shiftjis_strlen($value) === (int) $param['value']
    ) {
        return true;
    }
    if (is_numeric($value) && (float) $value === (float) $param['value']) {
        return true;
    }
    return false;
});

SiValidator::defineRule('string', function ($value) {
    if (is_string($value)) {
        return true;
    }
    return false;
});

SiValidator::defineRule('timezone', function ($value) {
    if (in_array($value, timezone_identifiers_list())) {
        return true;
    }
    return false;
});

SiValidator::defineRule('unique:table,column', function ($value, array $param) {
    $result = SpiralDB::title($param['table'])
        ->where($param['column'], $value)
        ->get();

    if ((int) $result->count() === 0) {
        return true;
    }
    return false;
});

SiValidator::defineRule('url', function ($value) {
    if (filter_var($value, FILTER_VALIDATE_URL)) {
        return true;
    }
    return false;
});

SiValidator::language('ja');
SiValidator::errorMessages([
    'ja' => [
        'numeric' => '{field} は数字でなければなりません。',
        'date_format:format' =>
            '{field}が {format} の日付フォーマットと一致しません',
        'date_equals:date' => '{field}が {date} の日付と一致しません',
        'confirmed:other' => '{field}が {other} と一致しません',
        'boolean' =>
            "{field}は 'true,false,1,0' のいずれかである必要があります ",
        'accepted' =>
            "{field}は 'true,1,yes,on,はい' のいずれかである必要があります ",
        'accepted_if:other,operator,if_value' =>
            "{other}が{operator}{if_value}である場合、{field}は 'true,1,yes,on,はい' のいずれかである必要があります ",
        'declined' =>
            "{field}は 'false,0,no,off,いいえ' のいずれかである必要があります ",
        'declined_if:other,operator,if_value' =>
            "{other}が{operator}{if_value}である場合、{field}は 'false,0,no,off,いいえ' のいずれかである必要があります ",
        'after:other' => '{field}は{other}以降の日付でなければいけません',
        'before:other' => '{field}は{other}以前の日付でなければいけません',
        'date' => '{field}は日付のフォーマットではありません',
        'min:min' => '{field}は{min}以上でなければいけません',
        'between:min,max' => '{field}は{min}以上{max}以下でなければいけません',
        'required' => '{field}は入力必須です',
        'alpha' => '{field}はアルファベットで入力してください',
        'alpha_dash' => '{field}はアルファベットと-_の記号で入力してください',
        'alpha_num' => '{field}はアルファベットと数字で入力してください',
        'different:value' => '{field}は{value}ではない値を入力してください',
        'digits:num' => '{field}は{num}桁ではありません',
        'digits_between:min,max' =>
            '{field}は{min}桁以上{max}桁以下でなければいけません',
        'email' => '{field}は有効なメールアドレスではありません',
        'maxword:max' =>
            '{field}は半角{max}文字以内で入力してください（全角は半分の文字数以内で入力してください）',
        'not_regex:pattern' => '{field}に使用できない文字が含まれています。',
        'regex:pattern' => '{field}が正しくありません。',
        'size:value' => '{field}は{value}文字で入力してください。',
        'string' => '{field}は文字列である必要があります。',
        'timezone' => '{field}が正しくありません。',
        'unique:table,column' => '{field}はすでに使用されています。',
        'url' => '{field}が正しくありません。',
    ],
    'en' => [
        'required' => '{field} is required.',
        'maxword:max' => '{field} must be no more than {max} characters.',
        'not_regex:pattern' => '{field} contains invalid characters.',
        'regex:pattern' => '{field} is not valid.',
        'size:value' => '{field} must be {value} characters.',
        'string' => '{field} must be a string.',
        'timezone' => '{field} is invalid.',
        'unique:table,column' => '{field} has already been taken.',
        'url' => '{field} is invalid.',
    ],
]);
