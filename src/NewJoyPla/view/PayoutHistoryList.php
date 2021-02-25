<?php
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/lib/Func.php";
$userInfo = new App\Lib\UserInfo($SPIRAL);

$myPageID = '';
if(isset($_POST['MyPageID']) && $_POST['MyPageID'] != '' ){
	$myPageID = $_POST['MyPageID'];
}

//管理者以外は全表示不可
if($userInfo->getUserPermission() != "1" && $myPageID != ''){
	App\Lib\viewNotPossible();
	exit;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 払出履歴一覧</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
  </head>
  <body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>払出履歴一覧</span></li>
				</ul>
		    	<h2 class="page_title">払出履歴一覧</h2>
		    	<hr>
		    	<div class="" id="tablearea">
		    		%sf:usr:payoutList:mstfilter%
		    	</div>
		    </div>
		</div>
	</div>
  </body>
</html>
