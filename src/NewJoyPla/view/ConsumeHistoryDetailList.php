<?php
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/lib/Func.php";
$userInfo = new App\Lib\UserInfo($SPIRAL);

$myPageID = '';
if(isset($_POST['MyPageID']) && $_POST['MyPageID'] != '' ){
	$myPageID = $_POST['MyPageID'];
}

if($userInfo->isUser() && $myPageID != ''){
	App\Lib\viewNotPossible();
	exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <title>JoyPla 消費履歴詳細一覧</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
                	<li><a href="%url/rel:mpg:top%&page=page1">消費・発注</a></li>
				    <li><span>消費履歴詳細一覧</span></li>
				</ul>
		    	<div class="no_print uk-margin">
				  <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
				  <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click()">
				</div>
		    	<h2 class="page_title uk-margin-remove">消費履歴詳細一覧</h2>
		    	<hr>
				<div>
					<button class="uk-button uk-button-primary" type="button" name="Action" uk-toggle="target: #price-select-bulk-update">選択した情報を一括更新する</button>
				</div>
				<!-- This is the modal -->
				<div id="price-select-bulk-update" uk-modal>
					<div class="uk-modal-dialog uk-modal-body">
						<h2 class="uk-modal-title">選択した情報の一括更新</h2>
						<dl class="cf">
							<dt class="title">
								単価
							</dt>
							<dd class="data real">
								<input
									class="uk-input"
									type="number"
									name="unitPrice"
									value=""
									maxlength="20">
								<br>
							</dd>
						</dl>
						<dl class="cf">
							<dt class="title">
								購買価格
							</dt>
							<dd class="data real">
								<input
									class="uk-input"
									type="number"
									name="price"
									value=""
									maxlength="20">
								<br>
							</dd>
						</dl>
						<p class="uk-text-right">
							<button class="uk-button uk-button-primary" type="button" onclick="update()">更新</button>
						</p>
					</div>
				</div>

				<script>
					function update() {
						let elems = $('input[id^=smp-table-check-]:checked');

						if( elems.length === 0)
						{
							return UIkit.modal.alert("情報を選択してください");
						}

						let ids = [];
						elems.each(function(index, element){ 
							ids.push(element.value);
						});

						UIkit.modal.confirm(elems.length + "件の更新を行います。よろしいですか").then(function () {
							
							loading();
							$.ajax({
								async: false,
								url: "<?php echo $price_api_url ?>",
								type:'POST',
								data:{
									_csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
									Action : "bulkPriceUpdate",
									ids : JSON.stringify(ids),
									unitPrice : $('input[name="unitPrice"]')[0].value,
									price : $('input[name="price"]')[0].value,
								},
								dataType: 'json'
							})
							// Ajaxリクエストが成功した時発動
							.done( (data) => {
								UIkit.modal.alert('更新しました。').then(function(){
									location.reload();
								});
							})
							// Ajaxリクエストが失敗した時発動
							.fail( (data) => {
								UIkit.modal.alert('更新に失敗しました');
							})
							// Ajaxリクエストが成功・失敗どちらでも発動
							.always( (data) => {
								loading_remove();
							});

						}, function () {
						});
					}
				</script>
		    	<div class="" id="tablearea">
					%sf:usr:search29:mstfilter%
		    	</div>
			</div>
		</div>
	</div>
</body>
</html>