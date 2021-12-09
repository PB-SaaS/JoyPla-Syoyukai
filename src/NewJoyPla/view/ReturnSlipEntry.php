
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span><?php echo $link_title ?></span></a></li>
				    <li><a href="<?php echo $api_url ?>"><span>検収書</span></a></li>
				    <li><span>返品伝票入力</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<input class="print_hidden uk-button uk-button-danger" type="submit" value="返品伝票登録" onclick="returnReg();return false;">
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold" style="font-size: 32px">返　品　伝　票　入　力</p>
		    	</div>
		    	<div uk-grid>
			    	<div class="uk-width-1-2@m">
		    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
		    				<tr class="uk-text-large">
		    					<td colspan="2">
		    						<b><?php echo $receiving_history->distributorName ?> 御中</b>
		    					</td>
		    				</tr>
		    			</table>
			    	</div>
			    	<div class="uk-width-1-2@m">
			    		<div class="uk-float-right uk-width-2-3@m">
			    			<table class="uk-table uk-width-1-1 uk-table-divider">
			    				<tr>
			    					<td>検収日時</td>
			    					<td><?php echo $receiving_history->registrationTime ?></td>
			    				</tr>
			    				<tr>
			    					<td>検収番号</td>
			    					<td><?php echo $receiving_history->receivingHId ?></td>
			    				</tr>
			    				<tr>
			    					<td>発注番号</td>
			    					<td><?php echo $receiving_history->orderHistoryId ?></td>
			    				</tr>
			    			</table>
		    				<div class="uk-text-left">
	    						<span><?php echo $receiving_history->hospitalName ?></span><br>
	    						<span>〒<?php echo $receiving_history->postalCode ?></span><br>
	    						<span><?php echo $receiving_history->prefectures ?> <?php echo $receiving_history->address ?></span><br>
	    						<span>電話番号：<?php echo $receiving_history->phoneNumber ?></span><br>
	    						<span>発注担当者：<?php echo $receiving_history->ordererUserName ?></span><br>
	    						<span>発注部署：<?php echo $receiving_history->divisionName ?></span><br>
	    					</div>
	    				</div>
		    			<div class="uk-clearfix"></div>
			    	</div>
			    </div>
		    	
		    	<div class="uk-margin" id="tablearea">
		    		<form>
			    		<div class="uk-overflow-auto">
				    		<table class="uk-table uk-table-hover uk-table-middle uk-table-divider" id="tbl-Items">
				    			<thead>
				    				<tr>
										<th class="uk-text-nowrap">NO</th>
										<th class="uk-table-expand" style="min-width:150px">メーカー</th>
										<th class="uk-table-expand" style="min-width:150px">商品名</th>
										<th class="uk-table-expand">製品コード</th>
										<th class="uk-table-expand">規格</th>
										<th class="uk-table-expand">ロット番号</th>
										<th class="uk-table-expand">使用期限</th>
										<th class="uk-text-nowrap">入数</th>
										<th class="uk-text-nowrap">発注数</th>
										<th class="uk-text-nowrap">入庫数</th>
										<th class="uk-text-nowrap">返品数</th>
										<th class="uk-text-nowrap">今回返品数</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
										$num = 1;
										foreach($receiving_item as $record){
											$max = 0;
											if($record->receivingCount == ''){
												$record->receivingCount = 0;
											}
											if($record->totalReturnCount == ''){
												$record->totalReturnCount = 0;
											}
											if(((int)$record->receivingCount - (int)$record->totalReturnCount) > 0){
												$max = (int)$record->receivingCount - (int)$record->totalReturnCount;
											}
				    						echo "<tr>";
				    						echo "<td class='uk-text-nowrap'>".$num."</td>";
				    						echo "<td>".$record->makerName."</td>";
				    						echo "<td>".$record->itemName."</td>";
				    						echo "<td>".$record->itemCode."</td>";
				    						echo "<td>".$record->itemStandard."</td>";
				    						echo "<td>".$record->lotNumber."</td>";
				    						echo "<td>".$record->lotDate."</td>";
				    						echo "<td class='uk-text-nowrap'>".$record->quantity.$record->quantityUnit."</td>";
				    						echo "<td class='uk-text-nowrap'>".$record->orderQuantity.$record->itemUnit."</td>";
				    						echo "<td class='uk-text-nowrap'>".$record->receivingCount.$record->itemUnit."</td>";
				    						echo "<td class='uk-text-nowrap'>".$record->totalReturnCount.$record->itemUnit."</td>";
				    						echo "<td class='uk-text-nowrap'><input type='number' step='1' class='uk-input' style='width:100px' min='0' max='".$max."' value='0' onchange='returnCount(this,\"".$record->receivingNumber."\")'><span class='uk-text-small uk-text-middle'>".$record->itemUnit."</span></td>";
				    						echo "</tr>";
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
		let itemsToJs = objectValueToURIencode( <?php echo json_encode($ItemsToJs); ?> );
		function returnReg(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			let errflag = false;
			let checked = false;
			Object.keys(itemsToJs).forEach(function (inhpkey) {
				if(!checked){
					if(parseInt(itemsToJs[inhpkey].returnCount) <= 0){
						errflag = true;	
					} else {
						errflag = false;
						checked = true;
					}
				}
			});
			
			if(errflag){
				UIkit.modal.alert("返品対象がありません");
				return;
			}
			
			errflag = false;
			Object.keys(itemsToJs).forEach(function (inhpkey) {
				if(parseInt(itemsToJs[inhpkey].receivingCount) < parseInt(itemsToJs[inhpkey].returnCount)){
					errflag = true;	
				}
			});
			
			if(errflag){
				UIkit.modal.alert("返品数が入庫数を超えています");
				return;
			}
			
			UIkit.modal.confirm("返品を行います。<br>よろしいですか").then(function () {
				UIkit.modal.alert("注意:卸業者へのご連絡はシステムでは行いません。").then(function(){
					loading();
					canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
					$.ajax({
						async: false,
						url:"<?php echo $api_url ?>",
						type:"POST",
						data:{
	                		Action : "regReturnApi",
	                		_csrf: "<?php echo $csrf_token ?>",
							returnData : JSON.stringify( itemsToJs ),
						},
						dataType: "json"
					})
					// Ajaxリクエストが成功した時発動
					.done( (data) => {
						
						if(! data.result){
							UIkit.modal.alert("返品伝票登録に失敗しました").then(function(){
								canAjax = true; // 再びAjaxできるようにする
							});
							return false;
						}
						UIkit.modal.alert("返品伝票登録が完了しました").then(function(){
							location.href ="%url/card:page_263469%";
						});
						
					})
					// Ajaxリクエストが失敗した時発動
					.fail( (data) => {
						
						UIkit.modal.alert("返品伝票登録に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					})
					// Ajaxリクエストが成功・失敗どちらでも発動
					.always( (data) => {
						loading_remove();
					});
				});
			}, function () {
				UIkit.modal.alert("中止します");
			});
		}
		
		function returnCount(elm,receivingNumber){
			itemsToJs[receivingNumber].returnCount = elm.value;
			$(elm).css({"color":"rgb(68, 68, 68)", "background-color":"rgb(255, 204, 153)", "width":"100px"});
		}
		
	</script>