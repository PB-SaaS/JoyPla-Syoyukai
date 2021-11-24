
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span>棚卸履歴一覧</span></a></li>
				    <li><a href="#" onclick="end_inventory_slip.search('%val:usr:inventoryEndId%')"><span>棚卸結果報告</span></a></li>
				    <li><span>%val:usr:divisionName% 棚卸結果報告</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
			    	<?php if($delete_button_view_flg): ?>
					<input class="print_hidden uk-button uk-button-danger" type="button" value="%val:usr:divisionName% 棚卸取消" onclick="end_inventory_slip.deleteInvForDiv();return false;">
			    	<?php endif ?>
                	<input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click()">
				</div>
		    	<div class="uk-text-left uk-text-large">
		    		<p class="uk-text-bold" style="font-size: 32px">%val:usr:divisionName% 棚卸結果報告</p>
		    	<hr>
		    	</div>
		    	
		    	<div uk-grid>
			    	<div class="uk-width-1-2@m">
		    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
		    				<tr>
		    					<td class="uk-text-bold">棚卸登録日時</td>
		    					<td class="uk-text-right">%val:usr:registrationTime%</td>
		    				</tr>
		    				<tr>
		    					<td class="uk-text-bold">棚卸更新日時</td>
		    					<td class="uk-text-right">%val:usr:updateTime%</td>
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
		    					<td class="uk-text-right">￥<script>price("%val:usr:totalAmount%")</script> - </td>
		    				</tr>
		    			</table>
			    	</div>
			    </div>

		    	<div class="uk-margin" style="margin-bottom: 50px;">
		    		<h3>棚卸総数</h3>
			  		<div class="uk-margin">
			  			<div class="uk-overflow-auto">
			  				<table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap">
			  					<thead>
			  						<tr>
			  							<th>NO</th>
			  							<th>卸業者</th>
			  							<th>メーカー名</th>
			  							<th>商品名</th>
			  							<th>製品コード</th>
			  							<th>規格</th>
			  							<th>JANコード</th>
			  							<th>購買価格</th>
			  							<th>単価</th>
			  							<th>計算上在庫</th>
			  							<th>棚卸数量</th>
			  							<th>棚卸金額</th>
			  							<th>数量差分</th>
			  						</tr>
			  					</thead>
			  					<tbody>
				    				<?php
				    					$num = 1;
										foreach ($inventory_total_items as $record) {
				    						echo "<tr>";
				    						echo "<td>".$num."</td>";
				    						echo "<td>".$record->distributorName."</td>";
				    						echo "<td>".$record->makerName."</td>";
				    						echo "<td>".$record->itemName."</td>";
				    						echo "<td>".$record->itemCode."</td>";
				    						echo "<td>".$record->itemStandard."</td>";
				    						echo "<td>".$record->itemJANCode."</td>";
				    						echo "<td>￥<script>price(\"".$record->price."\")</script></td>";
				    						echo "<td>￥<script>price(\"".$record->unitPrice."\")</script></td>";
				    						//echo "<td>".(int)$record->stockQuantity."<span class='uk-text-small'>".$record->quantityUnit."</span></td>";
				    						echo "<td>".(int)$record->calculatingStock."<span class='uk-text-small'>".$record->quantityUnit."</span></td>";
				    						echo "<td>".$record->inventryNum."<span class='uk-text-small'>".$record->quantityUnit."</span></td>";
				    						echo "<td>￥<script>price(\"".$record->inventryAmount."\")</script></td>";
				    						echo "<td>".((int)$record->calculatingStock - (int)$record->inventryNum)."<span class='uk-text-small'>".$record->quantityUnit."</span></td>";
				    						echo "</tr>";
				    						$num++;
										}
				    				?>
			  					</tbody>
			  				</table>
			  			</div>
			  		</div>
		    	</div>

			    <div class="uk-margin" id="tablearea">
			    　　<?php echo $table ?>
		        </div>
		    	
		    </div>
		</div>
	</div>
	<script>
	
	class EndInventorySlip {
		deleteInvForDiv(){
			UIkit.modal.confirm("伝票を取消します。<br>よろしいですか").then(function () {
				loading();
				$.ajax({
					async: false,
                    url: "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "deleteSlipApi",
                    },
					dataType: "json"
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
					
					if(! data.result){
						UIkit.modal.alert("取消に失敗しました");
						return false;
					}
					UIkit.modal.alert("取消しました").then(function(){
						end_inventory_slip.search('%val:usr:inventoryEndId%');
					});
					
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
					
					UIkit.modal.alert("取消に失敗しました");
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
		search(searchValue){
			loading();
			$.ajax({
				async: false,
				url:'%url/rel:mpgt:barcodeSearchAPI%',
				type:'POST',
				data:{
					searchValue :searchValue
				},
				dataType: 'json'
			})
			// Ajaxリクエストが成功した時発動
			.done( (data) => {
				if(data.code != 0){
					return false;
				}
				location.href=data.urls[0]+'&table_cache=true';
			})
			// Ajaxリクエストが失敗した時発動
			.fail( (data) => {
			})
			// Ajaxリクエストが成功・失敗どちらでも発動
			.always( (data) => {
				loading_remove();
			});
		}
	}
	
	let end_inventory_slip = new EndInventorySlip();
</script>