<?php

namespace framework\Library;

use DateTime;
use Exception;
use stdClass;

class SiValidator {

    private static string $language = "ja";
    private static array $defineRules = [] ;
    private static array $errorMessages = [];
    private static array $values = [];
    private static array $labels = [];

    public static function make($values , $rules , $labels = [] , $messages = [] )
    {
        $result = [];

        self::errorMessages($messages);

        self::$values = $values;
        self::$labels = $labels;

        foreach($values as $key => $value)
        {
            $label = (isset($labels[$key]))? $labels[$key] : $key;
            $result[$key] = self::validate($value, $label , $rules[$key]);
        }

        return $result;
    }

    public static function language($lang)
    {
        self::$language = $lang;
    }

    private static function of($value ,array $rules)
    {
        $ruleName = self::isValid($value , $rules);
        return ($ruleName === "");
    }

    
    private static function isValid($value , $rule)
    {
        if(self::processable($rule))
        {
            if(! self::exec($value , $rule))
            {
                return false;
            }
        }
        return true;
    }
    
    private static function validate($value , $field , array $rules)
    {
        $result = [];
        foreach($rules as $rule)
        {
            if(  ! is_string($rule) && is_callable($rule))
            {
                $message = $rule($value, $field );
                $result[] = new SiValidateResult((is_string($message) && $message === '') , $message , $value);
                continue;
            }
            //return ($rule !== '')? self::errorMessage( $rule , $field) : "";
            $message = (! self::isValid($value , $rule))? self::errorMessage( $rule , $field) : "";
            $result[] = new SiValidateResult(($rule === '') , $message , $value);
        }
        return $result;
    }

    private static function exec($value , $validateRule)
    {
        return self::$defineRules[ self::getRuleName($validateRule) ]( $value , self::param($validateRule) , self::$values);
    }

    private static function getRuleName(string $validateRule)
    {
        foreach(self::$defineRules as $ruleName => $defineRule)
        {
            $rule = $ruleName;
            if(self::startsWith($rule , ':'))
            {
                $rule = explode(":", $rule)[0];
                $validateRule = explode(":", $validateRule)[0];
            }
            if($rule === $validateRule)
            {
                return $ruleName;
            }
        }
        return null;
    }


    private static function processable(string $validateRule)
    {
        $ruleName = self::getRuleName($validateRule);
        if($ruleName !== null){
            return true;
        }
        return false;
    }

    private static function param(string $validateRule)
    {
        $ruleName = self::getRuleName($validateRule);
        if($ruleName !== null){
            $rules = explode(":", $ruleName);
            $validateRule = explode(":", $validateRule);

            if(isset($rules[1]) && isset($validateRule[1])){
                $paramKeys = explode(",", $rules[1]);
                $params = explode(",", $validateRule[1]);
                if(count( $paramKeys ) !== count( $params ))
                {
                    throw new Exception('The number of parameters does not match');
                }
                return array_combine($paramKeys , $params);
            }
        }
        return [];
    }

    public static function defineRule($ruleName , callable $func)
    {
        self::$defineRules[ $ruleName ] = $func;
    }

    public static function errorMessages( array $errorMessages )
    {
        self::$errorMessages = array_merge(self::$errorMessages , $errorMessages);
    }

    public static function startsWith($haystack, $needle) {
        return (strpos($haystack, $needle) !== false);
    }

    private static function errorMessage($validateRule , $field)
    {
        $ruleName = self::getRuleName($validateRule);
        $param = self::param($validateRule);
        $message = self::$errorMessages[self::$language][ $ruleName ];
        $message = str_replace('{field}', $field , $message);

        if(isset($param['other']) && isset(self::$labels[$param['other']] ))
        {
            $message = str_replace('{other}', self::$labels[$param['other']] , $message);
        }

        foreach($param as $key => $v)
        {
            $message = str_replace("{{$key}}", $v , $message);
        }

        return $message;
    }

    public static function help()
    {
        $help = [];
        foreach(self::$defineRules as $ruleName => $func)
        {
            $errorMessage = [];

            foreach(self::$errorMessages as $lang => $message)
            {
                $errorMessage[$lang] = $message[$ruleName];
            }

            $help[] = [ 'rule_name' => $ruleName , 'errorMessage' => $errorMessage ];
        }

        print_r($help);
    }
}

class SiValidateResult {

    private bool $result = true;
    private string $message = '';
    private string $value = '';

    public function __construct(bool $result , string $message = "" , $value = "")
    {
        $this->result = $result;
        $this->message = $message;
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }

    public function message()
    {
        return $this->message;
    }

    public function isValid()
    {
        return $this->result;
    }
}