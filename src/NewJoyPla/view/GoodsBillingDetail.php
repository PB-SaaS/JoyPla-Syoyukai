
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
                	<li><a href="%url/rel:mpg:top%&page=page1">消費・発注</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span>消費一覧</span></a></li>
				    <li><span>消費物品</span></li>
				</ul>
				<div class="no_print uk-margin">
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<?php if($userInfo->isAdmin() || $userInfo->isUser()):?>
					<input class="print_hidden uk-button uk-button-danger" type="submit" value="消費取消" onclick="billingDelete();return false;">
					<?php endif ?>
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold title_spacing" style="font-size: 32px">消費物品</p>
		    	</div>
		    	<div uk-grid>
			    	<div class="uk-width-1-2">
		    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider uk-text-nowrap">
		    				<tr>
		    					<td class="uk-text-bold">消費日時</td>
		    					<td class="uk-text-right">%val:usr:registrationTime%</td>
		    				</tr>
		    				<tr>
		    					<td class="uk-text-bold">部署名</td>
		    					<td class="uk-text-right">%val:usr:divisionName%</td>
		    				</tr>
		    				<tr>
		    					<td class="uk-text-bold">品目数</td>
		    					<td class="uk-text-right">%val:usr:itemsNumber%</td>
		    				</tr>
		    				<tr>
		    					<td class="uk-text-bold">合計金額</td>
		    					<td class="uk-text-right">￥<script>price('%val:usr:totalAmount%')</script> - </td>
		    				</tr>
		    			</table>
			    	</div>
			    </div>
		    	
		    	<div class="uk-margin" id="tablearea">
		    		<form>
			    		<div class="uk-overflow-auto">
				    		<table class="uk-table uk-table-hover uk-table-middle uk-table-divider" id="tbl-Items">
				    			<thead>
				    				<tr>
										<th class="uk-text-nowrap">NO</th>
										<th style="min-width:150px">メーカー</th>
										<th style="min-width:150px">商品名</th>
										<th class="uk-table-expand">製品コード</th>
										<th class="uk-table-expand">規格</th>
										<th class="uk-table-expand">JANコード</th>
										<th class="uk-table-expand">ロット番号</th>
										<th class="uk-table-expand">使用期限</th>
										<th class="uk-text-nowrap">入数</th>
										<th class="uk-text-nowrap">価格</th>
										<th class="uk-text-nowrap">単価</th>
										<th class="uk-text-nowrap">消費数</th>
										<th class="uk-text-nowrap">金額</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
				    					$num = 1;
				    					//'orderNumber','makerName','itemName','itemCode','itemStandard','price','quantity','orderQuantity','orderPrice','itemJANCode'
				    					foreach($billingData as $record){
				    						echo '<tr>';
				    						echo '<td>'.$num.'</td>';
				    						echo '<td>'.$record->makerName.'</td>';
				    						echo '<td>'.$record->itemName.'</td>';
				    						echo '<td>'.$record->itemCode.'</td>';
				    						echo '<td>'.$record->itemStandard.'</td>';
				    						echo '<td class="uk-text-nowrap">'.$record->itemJANCode.'</td>';
				    						echo '<td>'.$record->lotNumber.'</td>';
				    						echo '<td>'.$record->lotDate.'</td>';
				    						echo '<td class="uk-text-nowrap">'.number_format_jp($record->quantity).$record->quantityUnit.'</td>';
				    						echo '<td class="uk-text-nowrap">￥'.number_format_jp($record->price).' / '.$record->itemUnit.'</td>';
				    						echo '<td class="uk-text-nowrap">￥'.number_format_jp($record->unitPrice).'</td>';
				    						echo '<td class="uk-text-nowrap">'.number_format_jp($record->billingQuantity).$record->quantityUnit.'</td>';
				    						echo '<td class="uk-text-nowrap">￥'.number_format_jp($record->billingAmount).'</td>';
				    						echo '</tr>';
				    						$num++;
				    					}
				    				?>
				    			</tbody>
				    			
				    		</table>
				    	</div>
			    	
		    		</form>
		    	</div>
		    </div>
		</div>
	</div>
	<script>
		let canAjax = true;
		function billingDelete(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			UIkit.modal.confirm('消費情報を取消します。<br>よろしいですか<br>注意:在庫数を戻します。').then(function () {
				
				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:'<?php echo $api_url ?>',
	                type:'POST',
	                data:{
	                	Action : "consumeSlipDeleteApi",
	                	_csrf: "<?php echo $csrf_token ?>",
	                },
	                dataType: 'json'
	            })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
	                if(! data.result){
	            		UIkit.modal.alert("消費取消に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
	            		return false;
	                }
	                UIkit.modal.alert("消費取消が完了しました").then(function () {
						canAjax = true; // 再びAjaxできるようにする
						location.href ="<?php echo $link ?>&table_cache=true";
					});
	            })
	            // Ajaxリクエストが失敗した時発動
	            .fail( (data) => {
	        		UIkit.modal.alert("消費取消に失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
	        		return false;
	            })
	            // Ajaxリクエストが成功・失敗どちらでも発動
	            .always( (data) => {
	            	loading_remove();
	            });
			}, function () {
				UIkit.modal.alert("中止しました");
			});
		}
		
	</script>