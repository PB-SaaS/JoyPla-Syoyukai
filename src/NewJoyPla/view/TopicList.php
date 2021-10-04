<?php
if($SPIRAL->getContextByFieldTitle("distributorId") != ''){
	include_once 'NewJoyPla/lib/ApiSpiral.php';
	include_once "NewJoyPla/lib/Define.php";
	include_once "NewJoyPla/lib/UserInfo.php";
	include_once "NewJoyPla/lib/SpiralDataBase.php";
	include_once "NewJoyPla/lib/Func.php";
	include_once "NewJoyPla/api/GetDistributor.php";
	$userInfo = new App\Lib\UserInfo($SPIRAL);
	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	$getDistributor = new App\Api\GetDistributor($spiralDataBase,$userInfo);
	
	$getMyDistributorData = $getDistributor->getMyDistributor($SPIRAL->getContextByFieldTitle("distributorId"));
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla トピック一覧</title>
	<?php include_once 'NewJoyPla/src/Head.php'; ?>
</head>
<body>
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>トピック一覧</span></li>
				</ul>
		    	<div class='uk-width-1-1' uk-grid>
		    		<div class="uk-width-3-4@m">
		    			<h2>トピック一覧</h2>
					</div>
		    		<div class="uk-width-1-4@m uk-text-right@m">
		    			<?php if($SPIRAL->getContextByFieldTitle("distributorId") != ''): ?>
		    			<a href="#" class="uk-button uk-button-primary" onclick="document.form.submit()">トピック作成</a>
		    			<form method="post" action="/regist/is" name="form" target="_blank">
							%SMPAREA%
							<input type="hidden" name="SMPFORM" value="%smpform:topicReg%">
							<input type="hidden" name="hospitalId" value="%val:usr:hospitalId%" >
							<input type="hidden" name="distributorId" value="%val:usr:distributorId%" >
							<input type="hidden" name="topicName" value="%val:usr:name%" >
							<input type="hidden" name="tenantId" value="%val:usr:tenantId%" >
							<input type="hidden" name="distributorName" value="<?php echo $getMyDistributorData['data'][0][0] ?>" >
						</form>
		    			<?php else: ?>
		    			<a href="%url/rel:mpgt:page_266351%" class="uk-button uk-button-primary" target="_blank">トピック作成</a>
		    			<?php endif ?>
					</div>
		    	</div>
		    	<hr>
		    	<div uk-grid="" uk-margin="" class="uk-grid uk-grid-stack">
					<div class="uk-width-1-1 uk-grid-margin uk-first-column uk-margin-small-top">
					</div>
				</div>
		    	<div class="uk-margin-auto uk-width-4-5@m">
				%sf:usr:search88:table%
				</div>
			</div>
		</div>
	</div>
</body>
</html>
