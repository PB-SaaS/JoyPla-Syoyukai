<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/api/GetHospitalData.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$userInfo = new App\Lib\UserInfo($SPIRAL);
$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();
$hospitalAuthKey = $hospitalData['data'][0]['authKey'];
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla ユーザー管理</title>
	<?php include_once 'NewJoyPla/src/Head.php'; ?>
</head>
<body>
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>ユーザー管理</span></li>
				</ul>
		    			<h2 class="page_title uk-margin-remove">ユーザー管理</h2>
		    	<hr>
		    	<div class="">
					<div uk-grid uk-margin>
						<div class="uk-width-2-3@m">
							<h3>病院ユーザー一覧</h3>
						</div>
						<div class="uk-width-1-3@m uk-text-right@m">
							<form action="/regist/is" target="_blank" method="post">
								%SMPAREA%
								<input type="hidden" name="SMPFORM" value="%smpform:hpUserReg%">
								<input type="hidden" name="hospitalId" value="%val:usr:hospitalId%">
								<input type="hidden" name="hospitalAuthKey" value="<?php echo $hospitalAuthKey ?>">
								<input type="submit" value="病院ユーザー登録" class="uk-button uk-button-primary">
							</form>
						</div>
					</div>
					<div class="uk-margin">
						%sf:usr:search78:table%
					</div>
		    	</div>
		    	<div class="uk-margin">
		    		<div uk-grid uk-margin>
						<div class="uk-width-2-3@m">
							<h3>卸業者ユーザー一覧</h3>
						</div>
						<div class="uk-width-1-3@m uk-text-right@m">
							<a href="%url/rel:mpgt:page_265118%" class="uk-button uk-button-primary">卸業者ユーザー登録</a>
						</div>
					</div>
					<div class="uk-margin">      
						%sf:usr:search79:table%
					</div>
		    	</div>
		    </div>
		</div>
	</div>
	<script>
		function deleteHpUserCheck(url,loginId){
			let myLoginId = "<?php echo $userInfo->getLoginId() ?>";
			if(myLoginId == loginId){
				UIkit.modal.alert("ログイン者自身を削除することはできません");
				return false;
			}
			window.open(url, '_blank');
		}
		function deleteOroshiUserCheck(url,loginId){
			window.open(url, '_blank');
		}
	</script>
</body>
</html>
