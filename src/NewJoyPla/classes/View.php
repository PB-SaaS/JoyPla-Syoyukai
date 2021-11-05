<?php
class View {

    protected $file = null;
    public function __construct(string $file = null , array $data = array() , bool $filter = true )
    {
        $this->file = $file;
        foreach($data as $key => $d){
            if($filter){
                $d = htmlspecialchars($d, ENT_QUOTES, 'UTF-8');
            }
            $this->data[$key] = $d;
        }
    }

    public function forge(string $file = null , array $data = array() , bool $filter = true ): View
    {
        return new \View($file,$data,$filter);
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
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        $this->data[$key] = $value;
    }

    public function add_values(array $data , bool $filter = true): void
    {
        foreach($data as $key => $d){
            if($filter){
                $d = htmlspecialchars($d, ENT_QUOTES, 'UTF-8');
            }
            $this->data[$key] = $d;
        }
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
        include($this->file.'.php');
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