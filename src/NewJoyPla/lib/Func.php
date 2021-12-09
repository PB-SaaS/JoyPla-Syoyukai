<?php

namespace App\Lib;

define('CHARSET', 'UTF-8');
define('REPLACE_FLAGS', ENT_QUOTES);

function html($string = '') {
    return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
}

function changeDateFormat($formats , $date , $format){
	if($date == ""){return "";}
	$date = \DateTime::createFromFormat($formats, $date);
	return $date->format($format);
}

function requestUrldecode(array $array){
    $result = array();
    foreach($array as $key => $value){
        if(is_array($value)){
            $result[$key] = \App\Lib\requestUrldecode($value);
        } else {
            $result[$key] = urldecode($value);
        }
    }
    return $result;
}

function isMypage(){
    global $_POST;
    $myPageID = '';
    if(isset($_POST['MyPageID']) && $_POST['MyPageID'] != '' ){
    	$myPageID = $_POST['MyPageID'];
    }
    
    return ($myPageID != '');
}

function viewNotPossible(){
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <title>閲覧不可</title>
        </head>
        <body>
            <p>閲覧権限はありません</p>
        </body>
    </html>
    <?php
    return true;
}

function pager($c, $t, $limit) {
    $current_page = $c;     //現在のページ 
    $total_rec = $t;    //総レコード数
    $page_rec   = $limit;   //１ページに表示するレコード
    $total_page = ceil($total_rec / $page_rec); //総ページ数
    $show_nav = 5;  //表示するナビゲーションの数
     
    //全てのページ数が表示するページ数より小さい場合、総ページを表示する数にする
    if ($total_page < $show_nav) {
        $show_nav = $total_page;
    }
    //トータルページ数が2以下か、現在のページが総ページより大きい場合
    if ($total_page <= 1 || $total_page < $current_page ): 
        ?>
        <div id="pagination">
            <ul class="uk-pagination">
                <li class="prev">1</li>
            </ul>
        </div>
        <?php
        return;
    endif;
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
    ?>
    <div id="pagination">
        <ul class="uk-pagination">
            <?php 
            if ( $current_page > 3 && $total_page > $show_nav ) echo '<li class="prev"><a href="javascript:pageSubmit(1)">1</a></li>';
            if ( $current_page > 3 && $total_page > $show_nav ) echo '<li class="uk-disabled"><span>...</span></li>';
            for ($i=$loop_start; $i <= $loop_end; $i++) {
                if ($i > 0 && $total_page >= $i) {
                    if($i == $current_page) echo '<li class="uk-active">';
                    else echo '<li>';
                    echo '<a href="javascript:pageSubmit('.$i.')">'.$i.'</a>';
                    echo '</li>';
                }
            }
            if ( $current_page < $total_page - 2 && $total_page > $show_nav ) echo '<li class="uk-disabled"><span>...</span></li>';
            if ( $current_page < $total_page - 2 && $total_page > $show_nav ) echo '<li class="next"><a href="javascript:pageSubmit('.$total_page.')">'.$total_page.'</a></li>';
            ?>
        </ul>
    </div>
    <?php
}
?>
