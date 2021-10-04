<?php

/**
 * UIKIT ClassName
 */

class FieldSet {

    public $field_name;
    
    public $field_type;
    
    public $field_view_name;

    public $field_default_value;

    public $field_placeholder;

    public $field_error_message;

    public $field_cautions_text;

    public function __construct(string $field_name , string $field_type , string $field_view_name , string $field_default_value = null)
    {
        $this->field_name = $field_name ;
        $this->field_type = $field_type ;
        $this->field_view_name = $field_view_name ;
        $this->field_default_value = $field_default_value ;
    }

}