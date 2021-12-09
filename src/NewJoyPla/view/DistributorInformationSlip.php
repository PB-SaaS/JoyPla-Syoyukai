
<div class="animsition" uk-height-viewport="expand: true">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb no_print">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
			    <li><a href="<?php echo $link ?>&table_cache=true">卸業者一覧</a></li>
			    <li><span>卸業者情報詳細</span></li>
			</ul>
			<div class="no_print uk-margin">
				<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
				<?php if($user_info->isAdmin()): ?>
				<input class="print_hidden uk-button uk-button-primary" type="submit" value="卸業者情報変更" onclick="document.distributorChang.submit()">
				<form action="<?php echo $api_url ?>" method="post" name="distributorChang" class="uk-hidden">
					<input type="hidden" name="Action" value="updateDistributor">
				</form>
				<?php endif ?>
				<?php if($user_info->isAdmin()): ?>
				<input class="print_hidden uk-button uk-button-primary" type="submit" value="卸業者ユーザー招待" onclick="document.distributorUserReg.submit()">
				<form action="<?php echo $api_url ?>" method="post" class="uk-hidden" name="distributorUserReg">
					<input type="hidden" name="Action" value="distributorUserReg">
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
	    			<h2>卸業者ユーザー招待情報</h2>
				</div>
			</div>
	    	<div class="uk-width-1-1 uk-margin-top">
				<?php if($user_info->isAdmin()): ?>
	    		%sf:usr:search14:mstfilter:table%
				<?php else: ?>
				%sf:usr:search16:mstfilter:table%
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
		location.href = url;
	}
</script>