<?php

function view(string $template, array $param = array() , bool $filter = true): framework\Http\View
{
    return new framework\Http\View($template , $param , $filter);
}

// ビューの生成
namespace framework\Http;

use stdClass;
class View {

    protected $file = null;
    public $data = [];

    public function __construct(string $file = null , array $data = array() , bool $filter = true )
    {
        $this->file = $file;
        foreach($data as $key => $d){
            if($filter){
                $d = $this->filter($d);
            }
            $this->data[$key] = $d;
        }
    }

    public static function forge(string $file = null , array $data = array() , bool $filter = true ): View
    {
        return new View($file,$data,$filter);
    }

    public function set_filename(string $file = null): void
    {
        $this->file = $file;
    }

    public function get(string $key = null , string $default = null): mixed
    {
        if($key == null){
            return $this->data;
        }

        return ($this->data->$key)? $this->data[$key] : $default;
    }

    public function set(string $key , string $value = null , bool $filter = true): void
    {
        if($filter){
            $value = $this->filter($value);
        }
        $this->data[$key] = $value;
    }

    public function add_values(array $data , bool $filter = true): void
    {
        foreach($data as $key => $d){
            if($filter){
                $d = $this->filter($d);
            }
            $this->data[$key] = $d;
        }
    }

    public function filter($value)
    {
        if ( ! is_object($value) && ! is_array($value)  ) return htmlspecialchars($value, ENT_QUOTES, "UTF-8");//PHPサーバーはUTF-8
        
        if( is_object($value)){
            unset($value->spiralDataBase);
            unset($value->spiralSendMail);
            unset($value->spiralDBFilter);
            $tmp = new stdClass;
            foreach((array)$value as $k => $t)
            {
                $t = $this->filter($t);
                $tmp->{$k} = $t;
            }
            
            return $tmp;
        }

        
        if( is_array($value)){
            $tmp = [];
            foreach($value as $k => $t)
            {
                $t = $this->filter($t);
                $tmp[$k] = $t;
            }
            return $tmp;
        }

        return $value;
    }

    public function render( string $file = null ): string
    {
        if($file != null){
            $this->file = $file ;
        }
        if(is_array($this->data)){
            extract($this->data, EXTR_PREFIX_SAME, "t_");
        }
        ob_start(); //バッファ制御スタート
        include( VIEW_FILE_ROOT ."/". $this->file.'.php');
        $html = ob_get_clean(); //バッファ制御終了＆変数を取得

        return $html;
    }
    
    
    public function form_render( string $file = null ): string
    {
        global $SPIRAL;
        
        $is_error = ( $SPIRAL->getParam('detect') != '' && $SPIRAL->getParam('confirm') == '');
        
        $html = $this->render();
        
        $pattern = "/<!--SMP:DISP:ERR:START-->(.*)<!--SMP:DISP:ERR:END-->/s";
        
        if($is_error)
        {
            $pattern = "/<!--SMP:DISP:REG:START-->(.*)<!--SMP:DISP:REG:END-->/s";
        }
        
        if (preg_match($pattern, $html)) 
        {
            $html = preg_replace($pattern, '', $html);
        }

        return $html;
    }
}

