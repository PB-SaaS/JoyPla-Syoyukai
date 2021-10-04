<?php

class ListResponse {
    public $tablel_id = null;
    public $data = null;
    public $count = 0;
    public $limit = 0;
    public $currentpage = 1;
    public $header = array();
    public $fields = array();
    public $code = 0;
    public $message = null;

    public function __construct(string $tablel_id , $data = null ,  $count = 0 , $limit = 10 , $currentpage = 1 , $header = array() , $fields = array() , $code = 0 , $message = null)
    {
        $this->tablel_id = $tablel_id;
        $this->data = $data;
        $this->count = $count;
        $this->limit = $limit;
        $this->currentpage = $currentpage;
        $this->code = $code;
        $this->message = $message;
    }

    public function toString(): string
    {
        return json_encode(
            array(
                "data"=> $this->data ,
                "count"=> $this->count ,
                "limit"=> $this->code ,
                "currentpage"=> $this->currentpage ,
                "code"=> $this->code,
                "message"=> $this->message
            )
        );
    }
    
    /**
     * ページャーを組み立てる
     * @return string
     */

    public function pagination() : string
    {
        $count = (int)$this->count;
        $limit = $this->limit;


        //レコード総数がゼロのときは何も出力しない
        if (0 === $count) {
            return '';
        }

        //現在表示中のページ番号（ゼロスタート）
        $intCurrentPage = $this->currentpage;

        //ページの最大数
        $intMaxpage = ceil($count / $limit);

        $length = 5;

        $leftLength = ceil(( ($length - 1) / 2 ));

        $rightLength = ($length - 1) - $leftLength;

        $intStartpage = (( $intCurrentPage - $leftLength ) < 1 )? 1 : $intCurrentPage - $leftLength;
        $intEndpage = (( $intCurrentPage + $rightLength ) > $intMaxpage )? $intMaxpage : $intCurrentPage + $rightLength ;

        if($intMaxpage >= $length)
        {
            $intStartpage = (( $intMaxpage - $intEndpage ) == 0 )? $intMaxpage - ($length - 1) : $intStartpage;
            $intEndpage = ( $intStartpage == 1 )? $length : $intEndpage;
        }

        //url組み立て
        $url = $_SERVER['QUERY_STRING'];
        parse_str($url, $urlparams);

        $items = [];

        if($intStartpage != 1)
        {
            $urlparams['page'.$this->tablel_id] = 1;
            $items[] = sprintf('<li><span%s>%s</span></li>'
                , ($intCurrentPage == $i) ? ' class="uk-active"' : ''
                , ($intCurrentPage == $i) ? '1' : '<a href="?' .http_build_query($urlparams). '">1</a>' 
            );
        }

        if(($intStartpage - 1) > 1)
        {
            $items[] = sprintf('<li><span%s>%s</span></li>'
                , ' class="uk-disabled"'
                , '...'
            );
        }

        for ($i = $intStartpage; $i <= $intEndpage; $i++) 
        {
            $urlparams['page'.$this->tablel_id] = $i;
            $items[] = sprintf('<li><span%s>%s</span></li>'
                , ($intCurrentPage == $i) ? ' class="uk-active"' : ''
                , ($intCurrentPage == $i) ?  $i : '<a href="?' .http_build_query($urlparams). '">'.$i.'</a>'
            );
        }

        if($intEndpage < ( $intMaxpage - 1 ))
        {
            $items[] = sprintf('<li><span%s>%s</span></li>'
                    , ' class="uk-disabled"'
                    , '...'
            );
        }

        if($intEndpage != $intMaxpage)
        {
            $urlparams['page'.$this->tablel_id] = $intMaxpage;
            $items[] = sprintf('<li><span%s>%s</span></li>'
                , ($intCurrentPage == $i) ? ' class="uk-active"' : ''
                , ($intCurrentPage == $i) ? $intMaxpage : '<a href="?' .http_build_query($urlparams). '">'.$intMaxpage.'</a>' 
            );
        }


        return sprintf('<ul class="uk-pagination" uk-margin>%s</ul>', implode(PHP_EOL, $items));
    }
}