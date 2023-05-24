	<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
		<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
			<div class="uk-container uk-container-expand">
				<ul class="uk-breadcrumb no_print">
					<li><a href="%url/rel:mpg:top%">TOP</a></li>
					<li><a href="%url/rel:mpg:top%&path=stocktaking">棚卸メニュー</a></li>
					<li><a href="<?php echo $link ?>&table_cache=true"><span>棚卸履歴一覧</span></a></li>
					<li><span>棚卸結果報告</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<?php if(! $end_flg ): ?>
						<?php if($user_info->isAdmin() || $user_info->isApprover()): ?>
						<input class="print_hidden uk-button uk-button-danger" type="submit" value="棚卸情報削除" onclick="inventoryDelete();return false;">
						<input class="print_hidden uk-button uk-button-primary" type="submit" value="棚卸完了" onclick="inventoryFinish();return false;">
						<?php endif ?>
					<?php endif ?>
				</div>
				<div class="no_print">
					<span class="uk-label uk-padding-small <?php echo ($end_flg)? 'uk-label-success' : 'uk-label-warning' ?> uk-padding-remove-vertical uk-text-large">%val:usr:inventoryStatus%</span>
				</div>
				<div class="uk-text-left uk-text-large">
					<p class="uk-text-bold" style="font-size: 32px">棚卸結果報告</p>
				<hr>
				</div>
				<div uk-grid>
					<div class="uk-width-1-2@m">
						<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
							<?php if($end_flg ): ?>
							<tr>
								<td class="uk-text-bold">棚卸完了日時</td>
								<td class="uk-text-right">%val:usr:inventoryTime%</td>
							</tr>
							<?php endif ?>
							<tr>
								<td class="uk-text-bold">品目数</td>
								<td class="uk-text-right">%val:usr:itemsNumber%</td>
							</tr>
							<tr>
								<td class="uk-text-bold">合計金額</td>
								<td class="uk-text-right">￥<script>price(fixed("%val:usr:totalAmount%"))</script> - </td>
							</tr>
						</table>
					</div>
				</div>
				
				<div class="uk-margin" id="tablearea">
					%sf:usr:search13:mstfilter:table%
				</div>

				<?php if(!empty($notInventoriedDivisions)){ ?>
				<div class="uk-margin">
					<p class="uk-text-bold" style="font-size: 32px">棚卸未入力部署　<?php echo count($notInventoriedDivisions) ;?>部署</p>
					<div style="margin-bottom:50px;">
						<table style="font-size: 20px;" id="notInventoriedDivisions">
							<thead>
								<tr>
									<th>No.</th>
									<th>部署名</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$num = 1;
								foreach ($notInventoriedDivisions as $record) {
									echo <<< EOF
									<tr style="border-bottom:1px;">
										<td>{$num}</td>
										<td>{$record['divisionName']}</td>
									</tr>
									EOF;
									$num++;
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<?php }?>
			</div>
		</div>
	</div>
	<style>
		#notInventoriedDivisions{
			width: 25%;
		}
		#notInventoriedDivisions tr th{
			border-bottom: 1px solid #000;
			text-align: left;
		}
		#notInventoriedDivisions tr td{
			border-bottom: 1px solid #000;
			padding: 10px;
			text-align: left;
		}
	</style>
	<script>
	let canAjax = true;
	function inventoryDelete(){
		if(!canAjax) { 
			console.log('通信中');
			return;
		}
		UIkit.modal.confirm("伝票を取消します。<br>よろしいですか").then(function () {
			loading();
			
			canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
			$.ajax({
				async: false,
				url: "<?php echo $api_url ?>",
				type:"POST",
				data:{
					'Action' : 'slipDeleteApi',
					'_csrf' : '<?php echo $csrf_token ?>',
				},
				dataType: "json"
			})
			// Ajaxリクエストが成功した時発動
			.done( (data) => {
				
				if(! data.result){
					UIkit.modal.alert("取消に失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
					return false;
				}
				UIkit.modal.alert("取消しました").then(function(){
					location.href ="<?php echo $link ?>&table_cache=true";
				});
				
			})
			// Ajaxリクエストが失敗した時発動
			.fail( (data) => {
				UIkit.modal.alert("取消に失敗しました").then(function(){
					canAjax = true; // 再びAjaxできるようにする
				});
				return false;
			})
			// Ajaxリクエストが成功・失敗どちらでも発動
			.always( (data) => {
				loading_remove();
			});
		}, function () {
			UIkit.modal.alert("中止します");
		});
	}
	
	function inventoryFinish(){
		UIkit.modal.confirm("棚卸を確定します。<br>よろしいですか<br>※一時保存の情報は完了に変更します").then(function () {
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			loading();
			canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
			$.ajax({
				async: false,
				url: "<?php echo $api_url ?>",
				type:"POST",
				data:{
					'Action' : 'slipFixApi',
					'_csrf' : '<?php echo $csrf_token ?>',
				},
				dataType: "json"
			})
			// Ajaxリクエストが成功した時発動
			.done( (data) => {
				if(! data.result){
					UIkit.modal.alert("棚卸完了に失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
					return false;
				}
				UIkit.modal.alert("棚卸を完了しました").then(function(){
					location.href ="<?php echo $link ?>&table_cache=true";
				});
				
			})
			// Ajaxリクエストが失敗した時発動
			.fail( (data) => {
				
				UIkit.modal.alert("棚卸完了に失敗しました").then(function(){
					canAjax = true; // 再びAjaxできるようにする
				});
				return false;
			})
			// Ajaxリクエストが成功・失敗どちらでも発動
			.always( (data) => {
				loading_remove();
			});
		});
	}
</script>