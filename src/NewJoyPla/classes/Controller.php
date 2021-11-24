<?php

class Controller
{
    private $name   = 'controller';
    protected function __construct()
    {
    }
    // ログの出力
    public function logging($message, string $file_name = 'app.log')
    {
        //$Logger = new Logger('logger');
        //$Logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/' . $file_name, Logger::INFO));
        //$Logger->addInfo($message);
    }

    // ビューの生成
    public function view(string $template, array $param = array() , bool $filter = true): View
    {
        return new \View($template , $param , $filter);
    }
    
    protected function makeId($id = '00')
    {
        /*
        '02' => HP_BILLING_PAGE,
        '03_unorder' => HP_UNORDER_PAGE,
        '03_order' => HP_ORDER_PAGE,
        '04' => HP_RECEIVING_PAGE,
        '06' => HP_RETERN_PAGE,
        '05' => HP_PAYOUT_PAGE,
        */
        $id .= date("ymdHis");
        $id .= str_pad(substr(rand(),0,3) , 4, "0"); 
        
        return $id;
    }

    protected function requestUrldecode(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->requestUrldecode($value);
            } else {
                $result[$key] = (string)urldecode(preg_replace('/^%EF%BB%BF/', '', $value));
            }
        }
        return $result;
    }
    protected function sanitize($string = '') {
        return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
    }
    
    protected function makeRandNum($length) {
        $str = array_merge(range('0', '9'));
        $r_str = null;
        for ($i = 0; $i < $length; $i++) {
            $r_str .= $str[rand(0, count($str) - 1)];
        }
        return $r_str;
    }
}