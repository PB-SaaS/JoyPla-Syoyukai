<?php
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/lib/Func.php";
$userInfo = new App\Lib\UserInfo($SPIRAL);

$myPageID = '';
if(isset($_POST['MyPageID']) && $_POST['MyPageID'] != '' ){
	$myPageID = $_POST['MyPageID'];
}

if($userInfo->isUser() && $myPageID != ''){
	App\Lib\viewNotPossible();
	exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <title>JoyPla 払出履歴詳細一覧</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
                	<li><a href="%url/rel:mpg:top%&path=payout">払出メニュー</a></li>
				    <li><span>払出履歴詳細一覧</span></li>
				</ul>
		    	<div class="no_print uk-margin">
				  <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
				  <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click()">
				</div>
		    	<h2 class="page_title uk-margin-remove">払出履歴詳細一覧</h2>
		    	<hr>
		    	<div class="" id="tablearea">
					%sf:usr:payoutHDetailLv2:mstfilter%
		    	</div>
			</div>
		</div>
	</div>
</body>
</html>