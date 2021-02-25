
<!DOCTYPE html>
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/GetHospitalData.php';
include_once 'NewJoyPla/api/GetTenantData.php';



$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
$userInfo = new App\Lib\UserInfo($SPIRAL);

$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();

$getTenantData = new App\Api\GetTenantData($spiralDataBase,$userInfo);
$tenantData = $getTenantData->select();

?>
<html>
  <head>
    <title>JoyPla オプション情報</title>
	<?php include_once 'NewJoyPla/src/Head.php'; ?>
</head>
<body>
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>オプション情報</span></li>
				</ul>
				
				
				
		    	<div class='uk-width-1-1' uk-grid>
		    		<div class="uk-width-5-6@m uk-width-2-3">
		    			<h2>オプション情報</h2>
					</div>
		    	</div>
	    		<p class="uk-text-warning">オプションの変更はお問合せよりご連絡ください。</p>
		    	<div class='uk-width-4-5@m uk-margin-auto uk-margin-remove-top' >
		    		<table class="uk-table uk-table-divider uk-table-responsive">
					        <tr>
					            <th>テナント種別</th>
					            <td><?php echo ($tenantData['data'][0]['tenantKind'] == '1')? "シングルテナント" : "マルチテナント" ;?></td>
					        </tr>
					        <tr>
					            <th>入庫先設定</th>
					            <td><?php echo ($hospitalData['data'][0]['receivingTarget'] == '1')? "大倉庫" : "発注部署" ;?></td>
					        </tr>
					        <tr>
					            <th>登録可能ユーザー数</th>
					            <td><?php echo $hospitalData['data'][0]['registerableNum'] ?> 人まで</td>
					        </tr>
					</table>
		    	</div>
			</div>
		</div>
	</div>
</body>
</html>