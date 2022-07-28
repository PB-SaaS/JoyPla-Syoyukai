<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/lib/Func.php";
include_once "NewJoyPla/api/GetDistributor.php";

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
$userInfo = new App\Lib\UserInfo($SPIRAL);

$getDistributor = new App\Api\GetDistributor($spiralDataBase,$userInfo);

$distributorData = $getDistributor->getDistributor();

$myPageID = '';
if(isset($_POST['MyPageID']) && $_POST['MyPageID'] != '' ){
	$myPageID = $_POST['MyPageID'];
}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <title>JoyPla 金額情報一覧</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>金額情報一覧</span></li>
				</ul>
                        <div class="no_print uk-margin">
				  <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
				  <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click()">
				</div>
                        <h2 class="page_title uk-margin-remove">金額情報一覧</h2>
                        <hr>
		    	<div class="uk-margin" id="tablearea">
					%sf:usr:search16%
		    	</div>
			</div>
		</div>
	</div>
</body>

	<script>
   const distributorData = <?php echo json_encode($distributorData["data"]); ?>;
   $(function(){
      makeDistributorSelect();
   })

   function makeDistributorSelect(){
   		let selectval = $("#distributorId").val();
		//html = "<input type="number" class="uk-input" style="width:72px" step="10">";
	    html = document.createElement("div");
	    select = document.createElement("select");
	    select.className = "uk-select";
	    select.onchange  = function () {  
	    	onchangeSelect(this.value);
	    };
	    
	    option = document.createElement("option");
	    option.value = "";
	    option.text = " --- 卸業者を選択してください ---";
		select.appendChild(option);
	    
		Object.keys(distributorData).forEach(function (key) {
		    
		    option = document.createElement("option");
		    option.value = distributorData[key][1];
		    option.text = distributorData[key][0];
		    //input.step = listObject[object.recordId].irisu;
		    //<span class="uk-text-bottom">個</span>
		    
		    if(distributorData[key][1] == selectval){
		    	option.selected = "selected";
		    }
			select.appendChild(option);
		});
	
		html.appendChild(select);
		
		$("#distributorIdDiv").append(html);
   }
   
   function onchangeSelect(val){
   	$("#distributorId").val(val);
   }
	</script>
</html>
