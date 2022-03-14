<?php
/*
使用例
echo Form::open(['method' => 'post','action' => 'test']);
echo Form::token();
echo Form::label('テスト','id',true,['class' => 'uk-form-label']);
echo Form::text('text','テキスト',['class' => 'uk-input']);
echo Form::textarea('textarea','テキストエリア',['class' => 'uk-textarea']);
echo Form::hidden('hidden','非表示');
echo Form::password('password',['class' => 'uk-input']);
echo Form::email('email','test@test.com',['class' => 'uk-input']);
echo Form::checkbox('checkbox',[1=>'A',2=>'B'],'1',['class' => 'uk-checkbox']);
echo Form::radio('radio',['1'=>'Leopard','2'=>'Brass'],'1',['class' => 'uk-radio']);
echo Form::select('select',['cat'=>'猫','dog'=>'犬'],'dog',['class' => 'uk-select']);
echo Form::select('select',[
    'Cats'=>['leopard'=>'Leopard'], 
    'Dogs'=>['spaniel'=>'Spaniel'] 
],'spaniel',['class' => 'uk-select']);

echo Form::number('number', 25,['class' => 'uk-input']);
echo Form::date('date', '',['class' => 'uk-input']);
echo Form::week('week', '',['class' => 'uk-input']);
echo Form::time('time', '',['class' => 'uk-input']);
echo Form::datetime('datetime', '',['class' => 'uk-input']);
echo Form::submit('送信ボタン',['class' => 'uk-button']);
echo Form::button('登録', ['type' => 'submit', 'onfocus' => 'this.blur();','class' => 'uk-button']);
echo Form::button('リセット', ['type' => 'reset','class' => 'uk-button']);
echo Form::close();
*/

class Form {

    public function __construct()
    {
    }

    public static function generator(array $field_set_array)
    {
        $content = '';
        foreach($field_set_array as $field_set)
        {
            $content = self::create($field_set);
        }
        return $content;
    }

    public static function create($field_set)
    {
        switch($field_set->method)
        {
            case 'text':
                return self::text($field_set->name, $field_set->current_value ,$field_set->attr);
                break;
            case 'number':
                return self::number($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'tel':
                return self::tel($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'search':
                return self::search($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'date':
                return self::date($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'month':
                return self::month($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'week':
                return self::week($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'time':
                return self::time($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'datetime':
                return self::datetime($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'hidden':
                return self::tel($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'textarea':
                return self::textarea($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'password':
                return self::password($field_set->name,$field_set->attr);
                break;
            case 'email':
                return self::email($field_set->name, $field_set->current_value,$field_set->attr);
                break;
            case 'checkbox':
                return self::checkbox($field_set->name, $field_set->option, $field_set->current_value,$field_set->attr);
                break;
            case 'radio':
                return self::radio($field_set->name, $field_set->option, $field_set->current_value,$field_set->attr);
                break;
            case 'select':
                return self::select($field_set->name, $field_set->option, $field_set->current_value,$field_set->attr);
                break;
            case 'submit':
                return self::submit($field_set->label,$field_set->attr);
                break;
            case 'button':
                return self::button($field_set->label,$field_set->attr);
                break;
            default:
                return self::text($field_set->name, $field_set->current_value , $field_set->attr);
                break;
        }
    }

    public static function open(array $attr = null): string
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

    public static function close(): string
    {
        return "</form>";
    }

    public static function token(): string
    {
        return "<input type='hidden' value='' name='token' />";
    }

    public static function label(string $text , string $id , bool $not_null = false, array $attr = null): string 
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

    public static function text(string $name , string $default = null , array $attr = null):string
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

    public static function number(string $name , string $default = null , array $attr = null):string
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

    public static function tel(string $name , string $default = null , array $attr = null):string
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

    public static function search(string $name , string $default = null , array $attr = null):string
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

    public static function date(string $name , string $default = null , array $attr = null):string
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
    
    public static function month(string $name , string $default = null , array $attr = null):string
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
     
    public static function week(string $name , string $default = null , array $attr = null):string
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

    public static function time(string $name , string $default = null , array $attr = null):string
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
    
    public static function datetime(string $name , string $default = null , array $attr = null):string
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
    
    public static function hidden(string $name , string $default = null , array $attr = null):string
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
    
    public static function textarea(string $name , string $default = null , array $attr = null):string
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

    public static function password(string $name , array $attr = null): string
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

    public static function email(string $name , string $default = null , array $attr = null): string
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

    public static function checkbox(string $name , array $option , string $current_value = '' , array $attr = null): string
    {
        $html = '';

        if(!is_null($option))
        {
            foreach($option as $key => $val)
            {
                $html .= "<label><input type='checkbox' value='".$key."' name='".$name."' ".( ($current_value == $key ) ? ' checked' : '' )." ";
                
                if(! is_null($attr))
                {
                    foreach($attr as $ot => $ov){
                        $html .= " $ot='$ov' ";
                    }
                }
                $html .= "/> $val</label>";
            }
        }

        return $html;
    }

    public static function radio(string $name , array $option , string $current_value = '', array $attr = null): string
    {
        $html = '';
        
        if(!is_null($option))
        {
            foreach($option as $key => $val)
            {
                $html .= "<label><input type='radio' value='".$key."' name='".$name."' ".( ($current_value == $key ) ? ' checked' : '' )." ";
                
                if(! is_null($attr))
                {
                    foreach($attr as $ot => $ov){
                        $html .= " $ot='$ov' ";
                    }
                }
                $html .= "/> $val</label>";
            }
        }

        return $html;
    }

    public static function select(string $name , array $option , string $current_value = null , array $attr = null): string
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
                        if($opv == $current_value)
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
                    if($key == $current_value)
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

    public static function submit(string $value, array $attr = null ): string
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

    public static function button(string $label, array $attr = null ): string
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