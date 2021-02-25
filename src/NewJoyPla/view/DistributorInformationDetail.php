<?php
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/ApiSpiral.php';
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
    <title>JoyPla 卸業者情報詳細</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="%url/table:back%&table_cache=true">卸業者一覧</a></li>
				    <li><span>卸業者情報詳細</span></li>
				</ul>
				<div class="no_print uk-margin">
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<?php if($userInfo->getUserPermission() == '1' && $tenantData['data'][0]['tenantKind'] == '1'): ?>
					<input class="print_hidden uk-button uk-button-primary" type="submit" value="卸業者情報変更" onclick="document.distributorChang.submit()">
					<form action="/regist/is" method="post" name="distributorChang" target="_blank" class="uk-hidden">
						%SMPAREA%
						<input type="hidden" name="SMPFORM" value="%smpform:distributorChang%">
						<input type="hidden" name="authKey" value="%val:usr:authKey%">
						<input type="hidden" name="distributorId" value="%val:usr:distributorId%">
						<input type="hidden" name="distributorName" value="%val:usr:distributorName%">
						<input type="hidden" name="postalCode" value="%val:usr:postalCode%">
						<input type="hidden" name="prefectures" value="%val:usr:prefectures%">
						<input type="hidden" name="address" value="%val:usr:address%">
						<input type="hidden" name="phoneNumber" value="%val:usr:phoneNumber%">
						<input type="hidden" name="faxNumber" value="%val:usr:faxNumber%">
					</form>
					<?php endif ?>
					<?php if($userInfo->getUserPermission() == '1'): ?>
					<input class="print_hidden uk-button uk-button-primary" type="submit" value="卸業者ユーザー登録" onclick="document.distributorUserReg.submit()">
					<form action="/regist/is" target="_blank" method="post" class="uk-hidden" name="distributorUserReg">
						%SMPAREA%
						<input type="hidden" name="distributorId" value="%val:usr:distributorId%">
						<input type="hidden" name="SMPFORM" value="%smpform:oroshiUserReg%">
						<input type="hidden" name="distAuthKey" value="%val:usr:authKey%">
						<input type="hidden" name="hospitalId" value="%val:usr:hospitalId%">
					</form>
					<?php endif ?>
				</div>
				
				
				
		    	<div class="uk-width-1-1" uk-grid>
		    		<div class="uk-width-5-6@m uk-width-2-3">
		    			<h2>卸業者情報</h2>
					</div>
		    	</div>
		    	<div class="uk-width-4-5@m uk-margin-auto uk-margin-remove-top">
		    		<table class="uk-table uk-table-divider uk-table-responsive">
					        <tr>
					            <th>卸業者ID</th>
					            <td colspan="3">%val:usr:distributorId%</td>
					       </tr>
					        <tr>
					            <th>登録日時</th>
					            <td>%val:usr:registrationTime%</td>
					            <th>更新日時</th>
					            <td colspan="3">%val:usr:updateTime%</td>
					        </tr>
					        <tr>
					            <th>卸業者名</th>
					            <td colspan="5">%val:usr:distributorName%</td>
					        </tr>
					        <tr>
					            <th>郵便番号</th>
					            <td>〒%val:usr:postalCode%</td>
					            <th>都道府県</th>
					            <td>%val:usr:prefectures%</td>
					            <th>住所</th>
					            <td>%val:usr:address%</td>
					        </tr>
					        <tr>
					            <th>電話番号</th>
					            <td>%val:usr:phoneNumber%</td>
					            <th>FAX番号</th>
					            <td colspan="3">%val:usr:faxNumber%</td>
					        </tr>
					</table>
		    	</div>
		    	<div uk-grid>
		    		<div class="uk-width-3-4@m uk-width-2-3">
		    			<h2>卸業者ユーザー情報</h2>
					</div>
				</div>
		    	<div class="uk-width-1-1 uk-margin-top">
					<?php if($userInfo->getUserPermission() == '1'): ?>
		    		%sf:usr:search79:mstfilter:table%
					<?php else: ?>
					%sf:usr:oroshiUserList:mstfilter:table%
					<?php endif ?>
               </div>
			</div>
		</div>
	</div>
	<script>
		
	$(function(){
		let storage = JSON.parse(localStorage.getItem("joypla_inHpItemsList"));
		let dispObj = {};
		if(!storage){
			for(let i = 1 ; i <= 22 ; i++){
				dispObj[i] = true;
			}
		} else {
			dispObj = storage;
		}
		dispSet(dispObj);
	});
	function table_field_selector(){
		let count = $("input[class^='uk-checkbox chk_']").length;;
		let disp = {};
		for(let i = 1 ; i <= count ; i++){
			disp[i] = false;
			if($("input.chk_"+( "00" + i ).slice( -2 )).is(":checked")){
				disp[i] = true;
			}
		}
		localStorage.setItem("joypla_inHpItemsList", JSON.stringify(disp));
		location.reload();
	}
	
	function dispSet(settingObj){
		Object.keys(settingObj).forEach(function (key) {
		  $(".chk_"+( "00" + key ).slice( -2 )).prop("checked", settingObj[key]);
		  if(settingObj[key]){
		  	$(".f_"+( "00" + key ).slice( -2 )).show();
		  } else {
		  	$(".f_"+( "00" + key ).slice( -2 )).hide();
		  }
		});
	}
		function deleteOroshiUserCheck(url,loginId){
			window.open(url, '_blank');
		}
	</script>
</body>
</html>