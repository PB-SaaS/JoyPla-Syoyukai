<?php
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/lib/Func.php";
$userInfo = new App\Lib\UserInfo($SPIRAL);

$myPageID = '';
if(isset($_POST['MyPageID']) && $_POST['MyPageID'] != '' ){
	$myPageID = $_POST['MyPageID'];
}

if($userInfo->getUserPermission() != "1" && $myPageID != ''){
	App\Lib\viewNotPossible();
	exit;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla ロット管理</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>ロット管理</span></li>
				</ul>
		    	<h2 class="page_title">ロット管理</h2>
		    	<hr>
		    	<div class="" id="tablearea">
					%sf:usr:search76:mstfilter%
		    	</div>
			</div>
		</div>
	</div>
	<script>
		$(function(){
			let nowDate = new Date(<?php echo date("Y") ?>,  <?php echo date("m") ?> - 1, <?php echo date("d") ?>, "00", "00", "00");
			let checkDate = new Date(<?php echo date("Y",strtotime("+2 week")) ?>,  <?php echo date("m",strtotime("+2 week")) ?> - 1, <?php echo date("d",strtotime("+2 week")) ?>, "00", "00", "00");
			
			for(let i = 0; i < $("#tablearea .lotDate").length ; i++){
				str = $("#tablearea .lotDate")[i].innerText;
				if(str == ""){
					continue;
				}
				result = str.split("年");
				result2 = result[1].split("月");
				result3 = result2[1].split("日");
				
				Y = result[0];
				m = result2[0];
				d =result3[0];
				
				lotDate = new Date(Y,  parseInt(m) - 1, d, "00", "00", "00");
				if(nowDate.getTime() <= lotDate.getTime() && checkDate.getTime() >= lotDate.getTime()){
					$("#tablearea .lotDate")[i].classList.add( "uk-text-warning" );
					$("#tablearea .lotDate")[i].classList.add( "uk-text-bold" );
				}
				if(nowDate.getTime() > lotDate.getTime()){
					$("#tablearea .lotDate")[i].classList.add( "uk-text-danger" );
					$("#tablearea .lotDate")[i].classList.add( "uk-text-bold" );
				}
			}
		});
	</script>
</body>
</html>
