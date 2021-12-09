
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
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
		    				<tr>
		    					<td class="uk-text-bold">棚卸完了日時</td>
		    					<td class="uk-text-right">%val:usr:inventoryTime%</td>
		    				</tr>
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
		    </div>
		</div>
	</div>
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
		UIkit.modal.confirm("棚卸を確定します。<br>よろしいですか").then(function () {
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