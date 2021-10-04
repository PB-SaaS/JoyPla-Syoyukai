<?php

class Form {

    public $fieldSetArray;
    public $formOption;

    public function __construct(array $fieldSetArray )
    {
        $this->fieldSetArray = $fieldSetArray;
    }

    public function create(): string
    {
        global $SPIRAL;
        $html = '';
        
        foreach($this->fieldSetArray as $fieldSet)
        {
            $content = '';
            switch($fieldSet->method){
                case 'text':
                    $content .= \Form::text($fieldSet->name, $fieldSet->currentValue ,$fieldSet->attr);
                    break;
                case 'number':
                    $content .= \Form::number($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'tel':
                    $content .= \Form::tel($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'search':
                    $content .= \Form::search($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'date':
                    $content .= \Form::date($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'month':
                    $content .= \Form::month($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'week':
                    $content .= \Form::week($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'time':
                    $content .= \Form::time($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'datetime':
                    $content .= \Form::datetime($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'hidden':
                    $content .= \Form::tel($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'textarea':
                    $content .= \Form::textarea($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'password':
                    $content .= \Form::password($fieldSet->name,$fieldSet->attr);
                    break;
                case 'email':
                    $content .= \Form::email($fieldSet->name, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'checkbox':
                    $content .= \Form::checkbox($fieldSet->name, $fieldSet->option, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'radio':
                    $content .= \Form::radio($fieldSet->name, $fieldSet->option, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'select':
                    $content .= \Form::select($fieldSet->name, $fieldSet->option, $fieldSet->currentValue,$fieldSet->attr);
                    break;
                case 'submit':
                    $content .= \Form::submit($fieldSet->label,$fieldSet->attr);
                    break;
                case 'button':
                    $content .= \Form::button($fieldSet->label,$fieldSet->attr);
                    break;
                default:
                    $content .= \Form::text($fieldSet->name, $fieldSet->currentValue , $fieldSet->attr);
                    break;
            }

            if($fieldSet->method == 'button' || $fieldSet->method == 'submit' )
            {
                $html .= '<div class="uk-text-center" uk-margin>';
                $html .= $content;
                $html .= '</div>';
            } 
            else
            {
                $html .= '<div class="uk-margin">';
                $html .= \Form::label($fieldSet->label,$fieldSet->name, $fieldSet->notNullFlg ,['class' => 'uk-form-label']);
                $html .= '<div class="uk-form-controls">'; 
                $html .= $content;
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        
        return $html;
    }

    public function open(array $attr = null): string
    {
        $html = "<form ";

        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= ">";
        return $html ;
    }

    public function close(): string
    {
        return "</form>";
    }

    public function token(): string
    {
        return "<input type='hidden' value='' name='token' />";
    }

    public function label(string $text , string $id , bool $not_null = false, array $attr = null): string 
    {
        $html = "<label for='$id' ";

        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= ">";

        if($not_null)
        {
            $text .= "<span class='uk-margin-small-left uk-text-danger'>必須</span>";
        }

        return $html . $text . "</label>";
    }

    public function text(string $name , string $default = null , array $attr = null):string
    {

        $html = "<input type='text' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }

    public function number(string $name , string $default = null , array $attr = null):string
    {

        $html = "<input type='number' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }

    public function tel(string $name , string $default = null , array $attr = null):string
    {
        $html = "<input type='tel' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }

    public function search(string $name , string $default = null , array $attr = null):string
    {

        $html = "<input type='search' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }

    public function date(string $name , string $default = null , array $attr = null):string
    {
        $html = "<input type='number' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }
    
    public function month(string $name , string $default = null , array $attr = null):string
    {

        $html = "<input type='month' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }
    
    public function week(string $name , string $default = null , array $attr = null):string
    {
        $html = "<input type='month' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }

    public function time(string $name , string $default = null , array $attr = null):string
    {

        $html = "<input type='month' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }
    
    public function datetime(string $name , string $default = null , array $attr = null):string
    {

        $html = "<input type='datetime-local' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }
    
    public function hidden(string $name , string $default = null , array $attr = null):string
    {
        $html = "<input type='hidden' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }
    
    public function textarea(string $name , string $default = null , array $attr = null):string
    {
        $html = "<textarea name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= ">$default</textarea>";

        return $html;
    }

    public function password(string $name , array $attr = null): string
    {

        $html = "<input type='password' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }

    public function email(string $name , string $default = null , array $attr = null): string
    {
        $html = "<input type='email' value='$default' name='$name'";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }

    public function checkbox(string $name , array $option , string $currentValue = '' , array $attr = null): string
    {
        $html = '';

        if(!is_null($option))
        {
            foreach($option as $key => $val)
            {
                $html .= "<label><input type='checkbox' value='$key' name='$name' ". ($this->currentValue == $key ) ? 'checked' : '' ." ";
                
                if(! is_null($attr))
                {
                    foreach($attr as $ot => $ov){
                        $html .= "$ot='$ov' ";
                    }
                }
                $html .= "/>$val</label>";
            }
        }

        return $html;
    }

    public function radio(string $name , array $option , string $currentValue = '', array $attr = null): string
    {
        $html = '';
        
        if(!is_null($option))
        {
            foreach($option as $key => $val)
            {
                $html .= "<label><input type='radio' value='$key' name='$name' ".($this->currentValue == $key ) ? 'checked' : ''." ";
                
                if(! is_null($attr))
                {
                    foreach($attr as $ot => $ov){
                        $html .= "$ot='$ov' ";
                    }
                }
                $html .= "/>$val</label>";
            }
        }

        return $html;
    }

    public function select(string $name , array $option , string $currentValue = null , array $attr = null): string
    {
        
        $html = "<select name='$name' ";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= ">";

        if(!is_null($option))
        {
            foreach($option as $key => $val){
                if(is_array($val))
                {
                    $html .= "<optgroup label='$key'>";
                    foreach($val as $opv => $opl )
                    {
                        $selected = "" ;
                        if($opv == $currentValue)
                        {
                            $selected = "selected";
                        }
                        $html .= "<option value='$opv'>$opl</option>";
                    }
                    $html .= "</optgroup>";
                }
                else
                {
                    $selected = "" ;
                    if($key == $currentValue)
                    {
                        $selected = "selected";
                    }
                    $html .= "<option value='$key' $selected>$val</option>";
                }
            }
        }

        $html .= "</select>";

        return $html;
    }

    public function submit(string $value, array $attr = null ): string
    {
        $html = "<input type='submit' value='$value' ";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= "/>";

        return $html;
    }

    public function button(string $label, array $attr = null ): string
    {
        $html = "<button ";
        
        if(! is_null($attr))
        {
            foreach($attr as $ot => $ov){
                $html .= "$ot='$ov' ";
            }
        }

        $html .= ">$label</button>";

        return $html;
    }

}

/*
echo Form::open(['method' => 'post','action' => 'test']);
echo Form::token();
echo Form::label('test','テスト',['class' => 'uk-label']);
echo Form::text('text','テキスト',['class' => 'uk-input']);
echo Form::textarea('textarea','テキストエリア',['class' => 'uk-input']);
echo Form::hidden('hidden','非表示');
echo Form::password('password',['class' => 'uk-password']);
echo Form::email('email','test@test.com',['class' => 'uk-password']);
echo Form::checkbox('checkbox','チェックボックスA','value1',true,['class' => 'uk-checkbox']);
echo Form::checkbox('checkbox','チェックボックスB','value2',false,['class' => 'uk-checkbox']);
echo Form::radio('radio',['1'=>'Leopard'],1,['class' => 'uk-radio']);
echo Form::radio('radio',['1'=>'Leopard'],2,['class' => 'uk-radio']);
echo Form::select('select',['cat'=>'猫','dog'=>'犬'],'dog',['class' => 'uk-select']);
echo Form::select('select',[
    'Cats'=>['leopard'=>'Leopard'], 
    'Dogs'=>['spaniel'=>'Spaniel'] 
],'spaniel',['class' => 'uk-select']);

echo Form::number('number', 25);
echo Form::date('date', '');
echo Form::week('week', '');
echo Form::time('time', '');
echo Form::datetime('datetime', '');
echo Form::submit('送信ボタン');
echo Form::button('登録', ['type' => 'submit', 'onfocus' => 'this.blur();']);
echo Form::button('リセット', ['type' => 'reset']);
echo Form::close();
*/