<?php

namespace Component {

    abstract class Result{
        protected $code = '' ;
        protected $message = '' ;
        public function getCode(): string
        {
            return $this->code;
        }

        public function getMessage(): string
        {
            return $this->message;
        }
    }

    class Failed extends Result {

        public function __construct(array $res)
        {
            $this->code = $res['code'] ;
            $this->message = $res['message'] ;
        }
    }

    class Success extends Result {

        public function __construct(array $res)
        {
            $this->code = $res['code'] ;
            $this->message = $res['message'] ;
        }
    }

    class ListComponent {
        private $page_id = null; 
        private $spiralDataBase ; 
        private $page = 1;
        private $limit = 10;

        private $setting = array(
            'database' => '',
            'selectField' => array('f0001','aaa'),
            'linkFiled' => array('f0001'),
            'search' => array(array('name'=>'fieldtitle','value'=>'value','mode'=> '=','type'=>'and')),//可変 一覧表条件
            'sort' => array('f0001','asc'),//可変
        );

        public function __construct(array $setting , \App\Lib\SpiralDataBase $spiralDataBase)
        {
            $this->setting = $setting;
            $this->spiralDataBase = $spiralDataBase;
        }

        public function setPageId(string $page_id): void
        {
            $this->page_id = $page_id;
        } 

        public function setPage(int $page) : void
        {
            $this->page = $page;
        }

        public function setSort(array $sort_filed) : void 
        {
            $this->setting['sort'] = $sort_filed;
        }
        
        public function setLimit(int $limit) : void 
        {
            if($limit > 0 && $limit <= 100){
                $this->limit = $limit;
            }
        }

        public function setDatabase(): void
        {   
            $this->spiralDataBase->setDataBase($this->setting['database']);
        }

        public function addSearchField(array $search): void
        {
		    $this->setting['search'][] = $search;
        }
        
        public function setting(array $post_data): Result
        {
            if( ! isset($post_data['page_id']))
            {
                return new Failed( [ 'code' => 1 , 'message' => 'not page id'] );
            }

            if( ! isset($post_data['page']) )
            {
                return new Failed( [ 'code' => 1 , 'message' => 'not page'] );
            }
            
            if( ! isset($post_data['sort']) )
            {
                return new Failed( [ 'code' => 1 , 'message' => 'not sort filed'] );
            }

            if( ! isset($post_data['limit']) )
            {
                return new Failed( [ 'code' => 1 , 'message' => 'not limit'] );
            }

            $this->setPageId($post_data['page_id']);
            $this->setPage($post_data['page']);
            $this->setSort($post_data['sort']);
            $this->setLimit($post_data['limit']);

            if(! isset($post_data['search']) )
            {
                return new Failed( [ 'code' => 1 , 'message' => 'not search data'] );
            }

            foreach( $post_data['search'] as $search_data )
            {
                $this->setSearchField($search_data);
            }
            
            return new Success( [ 'code' => 0 , 'message' => 'success'] );
        }

        public function doSelect(): array
        {
            $this->spiralDataBase->addSelectFieldsToArray($setting['selectField']);
            foreach( $setting['search'] as $search )
            {
                $this->spiralDataBase->addSearchCondition($search['name'],$search['value'],$search['mode'],$search['type']);
            }
            
            return $this->spiralDataBase->doSelectLoop();
        }
        
        public function pager(int $page = 1 , int $total_rec = 0 , int $page_rec = 10 , int $show_nav = 3) : array
        {
            $pagination = array();
            $total_page = ceil($total_rec / $page_rec); //総ページ数
            
            //全てのページ数が表示するページ数より小さい場合、総ページを表示する数にする
            if ($total_page < $show_nav) {
                $show_nav = $total_page;
            }

            //トータルページ数が2以下か、現在のページが総ページより大きい場合
            if ($total_page <= 1 || $total_page < $current_page ){
                $pagination[] = array( 'prev' , '1' , '');
                return $pagination;
            }

            //総ページの半分
            $show_navh = ceil($show_nav / 2);
            //現在のページをナビゲーションの中心にする
            $loop_start = $current_page - $show_navh;
            $loop_end = $current_page + $show_navh;
            
            //現在のページが両端だったら端にくるようにする
            if ($loop_start <= 0) {
                $loop_start  = 1;
                $loop_end = $show_nav;
            }
            if ($loop_end > $total_page) {
                $loop_start  = $total_page - $show_nav +1;
                $loop_end =  $total_page;
            }
            
            if ( $current_page > 4 && $total_page > $show_nav ) $pagination[] = array( 'prev' , '1' , '');
            if ( $current_page > 4 && $total_page > $show_nav ) $pagination[] = array( 'disable' , '...' , '');
            for ($i=$loop_start; $i <= $loop_end; $i++) {
                if ($i > 0 && $total_page >= $i) {
                    if($i == $current_page) $pagination[] = array( 'active' , '1' , '');
                    else $pagination[] = array( 'active' , $i , '?page_'.$this->page_id.'='.$i);
                }
            }
            if ( $current_page < $total_page - 3 && $total_page > $show_nav ) $pagination[] = array( 'disable' , '...' , '');
            if ( $current_page < $total_page - 3 && $total_page > $show_nav ) $pagination[] = array( 'active' , $total_page , '?page_'.$this->page_id.'='.$total_page); 
            return $pagination;
        }
    }
}