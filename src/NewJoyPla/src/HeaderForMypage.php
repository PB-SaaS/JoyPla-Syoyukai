<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/lib/Func.php';
include_once 'NewJoyPla/api/GetHospitalData.php';
include_once 'NewJoyPla/api/GetDistributor.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
$userInfo = new App\Lib\UserInfo($SPIRAL);
if($userInfo->getDistributorId() == ''){
	$getUserData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
	$byouinResult = $getUserData->select();
} else {
	$getUserData = new App\Api\GetDistributor($spiralDataBase,$userInfo);
	$oroshiResult = $getUserData->getMyDistributor($userInfo->getDistributorId());
}
?>

<nav class="uk-navbar-container no_print" uk-navbar>
	<div class="uk-navbar-left">
		<a href="%url/rel:mpg:top%" class="uk-navbar-item uk-logo">
			<img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
		</a>
	</div>

	<div class="uk-navbar-right uk-margin-right" style="flex-wrap: nowrap;">
		<p class="uk-margin-auto-vertical uk-visible@s uk-margin-right uk-text-right">
			<?php echo ($oroshiResult['data'][0][0] != '')? $oroshiResult['data'][0][0].'<br>' : '' ; ?>
			<?php echo ($byouinResult['data'][0]['hospitalName'] != '')? $byouinResult['data'][0]['hospitalName'].'<br>' : '' ; ?>
			<?php echo $userInfo->getName();?> 様
		</p>
		<a href="%url/rel:mpg:top%" class="uk-icon-button uk-margin-right" uk-icon="icon: home; ratio: 1.5" title="TOPへ戻る"></a>
		<a href="#" class="uk-icon-button uk-margin-right" uk-icon="icon: mail; ratio: 1.5" title="お問合せ" onclick="document.contactUs.submit()"></a>
		<form method="post" action="/regist/is" name="contactUs" target="_blank">
			%SMPAREA%
			<input type="hidden" name="SMPFORM" value="%smpform:contactUs%">
		</form>
		<a href="%form:act:logout%" class="uk-icon-button" uk-icon="icon: sign-out; ratio: 1.5" title="ログアウト"></a>
	</div>

</nav>