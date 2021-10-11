<?php

use field\DbField;

class FieldSet {

    public $name;
    public $validate_type;
    public $not_null_Flg;
    public $method;
    public $label;
    public $attr;
    public $option;
    public $current_value = ' ';
    public $message;

    public function __construct($name , $validate_type , $not_null_Flg = 'f', $method = 'text', $label = '', $attr = array(), $option = array()){
        $this->name = $name;
        $this->validate_type = $validate_type;
        $this->not_null_Flg = $not_null_Flg;
        $this->method = $method;
        $this->label = $label;
        $this->attr = $attr;
        $this->option = $option;
        $this->getCurrentValue();
    }

    public function getCurrentValue()
    {
        global $SPIRAL ;
        $this->current_value = $SPIRAL->getParam($this->name);
    }

    public function validate()
    {
        $DbField = DbField::of($this->name , $this->validate_type,' ',$this->current_value , ['not_null_Flg'=>$this->not_null_Flg]);
        if($DbField->isFailed()){
            $this->message = $DbField->getValue()->message;
        }
        return $this;
    }
}