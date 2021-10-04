<?php

class FieldSet {

    public $name;
    public $validateType;
    public $notNullFlg;
    public $method;
    public $label;
    public $attr;
    public $option;
    public $currentValue = ' ';
    public $message;

    public function __construct($name , $validateType , $notNullFlg = 'f', $method = 'text', $label = '', $attr = array(), $option = array()){
        $this->name = $name;
        $this->validateType = $validateType;
        $this->notNullFlg = $notNullFlg;
        $this->method = $method;
        $this->label = $label;
        $this->attr = $attr;
        $this->option = $option;
        $this->getCurrentValue();
    }

    public function getCurrentValue()
    {
        global $SPIRAL ;
        $this->currentValue = $SPIRAL->getParam($this->name);
    }

    public function validate()
    {
        $DbField = \field\DbField::of($this->name , $this->validateType,' ',$this->currentValue , array( 'notNullFlg'=>$this->notNullFlg ));
        if($DbField->isFailed()){
            $this->message = $DbField->getValue()->message;
        }
        return $this;
    }
}