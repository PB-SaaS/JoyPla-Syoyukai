
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once 'NewJoyPla/api/GetHospitalData.php';
include_once 'NewJoyPla/api/GetTenantData.php';
include_once "NewJoyPla/api/UpdateRequestStatus.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("NJ_QRequestDB",$SPIRAL->getCardId(),"requestId","requestStatus");

if($card["data"][0][1] == "1"){
	$updateRequestStatus = new App\Api\UpdateRequestStatus($spiralDataBase);
	$updateRequestStatus->open($card["data"][0][0]);
}

$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();
$getTenantData = new App\Api\GetTenantData($spiralDataBase,$userInfo);
$tenantData = $getTenantData->select($hospitalData['data'][0]['tenantId']);

?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 見積依頼詳細</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="%url/table:back%">見積依頼一覧</a></li>
				    <li><span>見積依頼詳細</span></li>
				</ul>
		    	<hr>
		    	<div class="uk-margin-auto uk-width-2-3@m">
		    		<article class="uk-article">

					    <h1 class="uk-article-title">%val:usr:requestTitle%</h1>
					    
					    <p class="uk-article-meta">
					        ステータス: %val:usr:requestStatus% <br>
                            依頼者 %val:usr:hospitalName% %val:usr:requestUName% <br>
					    	卸業者：%val:usr:distributorName%
					    </p>
					
					    <p class="">
					    	見積期限：%val:usr:quotePeriod% <br> <br>
					    	%val:usr:requestDetail:br%
					    	</p>
					
						<div class="uk-grid-small uk-child-width-auto" uk-grid>
							<div class="uk-width-1-2">
					    		%val:usr:registrationTime%
							</div>
					        
					    </div>
						
					</article>
				</div>
				<?php if($tenantData['data'][0]['tenantKind'] == '1') : ?>
				<hr>
				<div class="uk-margin">
					<p>見積商品一覧</p>
					<div>%sf:usr:search20:mstfilter:table%</div>
				</div>
				<form name="reqItemsReg" action="/regist/is" target="_blank" method="POST">
					%SMPAREA%
					<input type="hidden" name="SMPFORM" value="%smpform:330_reqItemsReg%">
					<input type="hidden" name="requestId" value="%val:usr:requestId%">
					<input type="hidden" name="tenantId" value="%val:usr:tenantId%">
					<input type="hidden" name="distributorId" value="%val:usr:distributorId%">
				</form>
				<?php endif; ?>
				<hr>
				<div class="uk-margin">
					<p>金額見積依頼の商品一覧</p>
					<div>%sf:usr:search16:mstfilter:table%</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
