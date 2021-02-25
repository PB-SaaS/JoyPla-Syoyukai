<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/api/GetTenantData.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);
$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$getTenantData = new App\Api\GetTenantData($spiralDataBase,$userInfo);
$tenantData = $getTenantData->select();

?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 卸業者一覧</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>卸業者一覧</span></li>
				</ul>
		    	<div class="uk-width-1-1" uk-grid>
		    		<div class="uk-width-3-4@l uk-width-2-3">
		    			<h2>卸業者一覧</h2>
					</div>
		    	</div>
		    	<hr>
					<?php if($userInfo->getUserPermission() == '1' && $tenantData['data'][0]['tenantKind'] == '1'): ?>
		    	<div uk-grid="" uk-margin="" class="uk-grid uk-grid-stack">
					<div class="uk-width-1-1 uk-grid-margin uk-first-column uk-margin-small-top">
		    			<form action="/regist/is" target="_blank" method="post">
		    				%SMPAREA%
							<input type="hidden" name="SMPFORM" value="%smpform:distributorReg%">
							<input type="hidden" name="hospitalId" value="%val:usr:hospitalId%">
							<input type="submit" value="卸業者登録" class="uk-button uk-button-primary">
		    			</form>
					</div>
				</div>
					<?php endif ?>
		    	<div class="uk-margin">
				%sf:usr:search81:table%
				</div>
			</div>
		</div>
	</div>
</body>
</html>
