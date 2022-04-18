
	<?php if($is_deleted): ?>
		<script>
			UIkit.modal.alert("発注商品が0件となりました。<br>未発注書一覧へ戻ります。").then(function(){
				location.href ="<?php echo $link ?>&table_cache=true";
			});
		</script>
	<?php  else: ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
					<li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span>未発注書一覧</span></a></li>
				    <li><span>未発注書</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default uk-margin-small-top" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<?php if($userInfo->isAdmin() || $userInfo->isUser()): ?>
					<input class="print_hidden uk-button uk-button-danger uk-margin-small-top" type="submit" value="発注伝票取消" onclick="orderedDelete();return false;">
					<?php endif; ?>
					<?php if(!$userInfo->isUser()): ?>
					<input class="print_hidden uk-button uk-button-primary uk-margin-small-top" type="submit" id="orderFixButton" value="発注確定" onclick="orderFix();return false;">
					<?php endif; ?>
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold title_spacing" style="font-size: 32px">（未）発注書</p>
		    	</div>
		    	<div uk-grid>
			    	<div class="uk-width-1-2@m">
		    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
		    				<tr class="uk-text-large">
		    					<td colspan="2">
		    						<b>%val:usr:distributorName% 御中</b>
		    					</td>
		    				</tr>
		    				<tr class="uk-text-large">
		    					<td>合計金額</td>
		    					<td class="uk-text-right">￥<script>price("%val:usr:totalAmount%")</script> - </td>
		    				</tr>
		    			</table>
			    	</div>
			    	<div class="uk-width-1-2@m">
			    		<div class="uk-float-right uk-width-2-3@m">
			    			<table class="uk-table uk-width-1-1 uk-table-responsive uk-table-divider">
			    				<tr>
			    					<td>発注日時</td>
			    					<td> --- </td>
			    				</tr>
			    				<tr>
			    					<td>発注番号</td>
			    					<td>%val:usr:orderNumber%</td>
			    				</tr>
			    			</table>
		    				<div id="order_barcode" class="uk-text-center">
	    						<span id="hacchu_num">%val:usr:orderNumber%</span>
	    					</div>
		    				<div class="uk-text-left">
	    						<span>%val:usr:hospitalName%</span><br>
	    						<span>〒%val:usr:postalCode%</span><br>
	    						<span>%val:usr:prefectures% %val:usr:address%</span><br>
	    						<span>電話番号：%val:usr:phoneNumber%</span><br>
	    						<span>発注担当者：%val:usr:ordererUserName%</span><br>
	    						<span>発注部署：%val:usr:divisionName%</span><br>
	    					</div>
	    				</div>
		    			<div class="uk-clearfix"></div>
			    	</div>
			    </div>
		    	
		    	<div class="uk-margin" id="tablearea">
		    		<form>
		    			<?php if($userInfo->isAdmin() || $userInfo->isUser()): ?>
			    		<div class="no_print">
			    			<input class="print_hidden uk-button uk-button-primary" type="button" value="更新" onclick="itemlistUpdate();return false;">
			    			<input class="print_hidden uk-button uk-button-default" type="reset" value="リセット">
			    			<input class="print_hidden uk-button uk-button-danger" type="button" value="削除" onclick="itemlistDelete();return false;">
			    		</div>
			    		<?php endif ?>
			    		<div class="uk-overflow-auto">
				    		<table class="uk-table uk-table-hover uk-table-middle uk-table-divider" id="tbl-Items">
				    			<thead>
				    				<tr>
										<th class="uk-text-nowrap">NO</th>
		    							<?php if($userInfo->isAdmin() || $userInfo->isUser()): ?>
										<th class="uk-text-nowrap"><input type="checkbox" onclick="allItems(this.checked)" class="uk-checkbox"></th>
			    						<?php endif ?>
										<th class="uk-table-expand">メーカー</th>
										<th class="uk-table-expand">商品名</th>
										<th class="uk-table-expand">製品コード</th>
										<th class="uk-table-expand">規格</th>
										<th class="uk-table-expand">JANコード</th>
										<th class="uk-text-nowrap">価格</th>
										<th class="uk-text-nowrap">入数</th>
										<th class="uk-text-nowrap">数量</th>
										<th class="uk-text-nowrap">金額</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
										$num = 1;
										foreach($order_data as $record){
											$attr = [];
				    						echo "<tr>";
				    						echo "<td class='uk-text-nowrap'>".$num."</td>";
				    						if($userInfo->isAdmin() || $userInfo->isUser()){
				    							echo "<td class='uk-text-nowrap'><input type='checkbox' class='uk-checkbox itemsCheckBox' name='check".$num."'></td>";
				    						}
				    						echo "<td>".$record->makerName."</td>";
				    						echo "<td>".$record->itemName."</td>";
				    						echo "<td>".$record->itemCode."</td>";
				    						echo "<td>".$record->itemStandard."</td>";
				    						echo "<td>".$record->itemJANCode."</td>";
				    						echo "<td class='uk-text-nowrap'>￥".number_format($record->price,2)."<span class='uk-text-small'> / 1".$record->itemUnit."</span></td>";
				    						echo "<td class='uk-text-nowrap'>".$record->quantity."<span class='uk-text-small'>".$record->quantityUnit."</span></td>";
				    						if( $record->orderQuantity > 0 )
											{
												$attr['min'] = 1;
											}
											else if( $record->orderQuantity < 0 )
											{
												$attr['max'] = -1;
											}
				    						if($userInfo->isAdmin() || $userInfo->isUser()){
					    						echo "<td><input type='number' step='1' class='uk-input' style='color:#444444;width:100px;' onchange='active(this,".$num.")' ";
												foreach($attr as $key => $val) echo " $key='$val' ";
												echo "value='".$record->orderQuantity."'><span class='uk-text-small uk-text-middle'>".$record->itemUnit."</span></td>";
				    						}
				    						if($userInfo->isApprover()){
				    							echo "<td class='uk-text-nowrap'>".$record->orderQuantity."<span class='uk-text-small'>".$record->itemUnit."</span></td>";
				    						}
				    						echo "<td>￥".number_format($record->orderPrice,2)."</td>";
				    						echo "<td style='display:none'>".$record->orderCNumber."</td>";
				    						echo "</tr>";
				    						$num++;
				    					}
				    				?>
				    			</tbody>
				    			
				    		</table>
				    	</div>
			    	
		    		</form>
		    	</div>
		    	<div class="uk-width-3-4 uk-margin">
		    		<table class="uk-table uk-table-middle uk-table-divider">
		    			<thead>
		    				<tr>
		    					<td colspan="2">備考</td>
		    				</tr>
		    			</thead>
		    			<tbody>
		    				<tr>
		    					<td class="uk-width-1-5">
		    					    %val:usr:divisionName%	
		    					</td>
		    					<td class="uk-width-4-5">
	    							<?php if($userInfo->isAdmin() || $userInfo->isUser()): ?>
		    						<textarea class="uk-textarea uk-width-1-1" rows="5" name="ordercomment" placeholder="備考を記入...">%val:usr:ordercomment%</textarea>
									<input class="print_hidden uk-button uk-button-primary uk-align-center" type="button" value="備考を更新" onclick="commentUpdate();return false;">
									<?php endif ?>
	    							<?php if($userInfo->isApprover()): ?>
									<textarea class="uk-textarea uk-width-1-1 uk-hidden" rows="5" name="ordercomment" placeholder="備考を記入...">%val:usr:ordercomment%</textarea>
									%val:usr:ordercomment:br%
									<?php endif ?>
								</td>
							</tr>
		    			</tbody>
		    		</table>
		    	</div>
		    </div>
		</div>
	</div>
    <script>
		let canAjax = true;
    	let upToData = [];
		$(function(){
		 
		 let order_num = $("#hacchu_num").text();
		 //$("#hacchu_num").remove();
		 $("#order_barcode").html("<svg id='barcode_hacchu'></svg>");
		 generateBarcode("barcode_hacchu",order_num);
		 //$("td#order_barcode div").barcode({code:order_num, crc:false }, "int25",{barWidth: 3 ,barHeight: 40 , output: "css"});
		});
		
    	
    	function active(elm,num){
    		elm.style.backgroundColor = "rgb(255, 204, 153)";
    		$("#tbl-Items tbody input[type='checkbox'][name='check"+num+"']").prop("checked", true);
    		$("#orderFixButton").prop("disabled", true);
    	}
    	
    	function allItems(checked){
    		$("#tbl-Items tbody input[type='checkbox']").prop("checked", checked);
    	}
		
		function itemlistUpdate(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			upToData = [];
			$("#tbl-Items tbody tr").map(function () {
				let td = $(this).children();
				if($(td[1]).children("input")[0].checked ){
					upToData.push({
						orderCNumber: $(td[11]).text(),
						num: $(td[9]).children("input").val(),
					});
				}
			});
			if(Object.keys(upToData).length == 0){
				UIkit.modal.alert("更新対象がありません").then(function(){
					loading_remove();
				});
				return
			}
			UIkit.modal.confirm(Object.keys(upToData).length+"件更新します。<br>よろしいですか").then(function () {
                loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"<?php echo $api_url ?>",
	                type:"POST",
	                data:{
	                	data:JSON.stringify(upToData),
	                	_csrf : '<?php echo $csrf_token ?>',
	                	Action : 'itemUpdate',
	                },
	                dataType: "json"
		        })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
					if(! data.result){
						UIkit.modal.alert("更新に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
	            	
	        		UIkit.modal.alert("更新が完了しました").then(function(){
	        			location.reload();
					});
	            })
	            .fail( (data) => {
	        		UIkit.modal.alert("更新に失敗しました").then(function(){
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
		
		function itemlistDelete(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			upToData = [];
			$("#tbl-Items tbody tr").map(function () {
				let td = $(this).children();
				if($(td[1]).children("input")[0].checked){
					upToData.push({
						orderCNumber: $(td[11]).text(),
						num: $(td[9]).children("input").val(),
					});
				}
			});
			if(Object.keys(upToData).length == 0){
				UIkit.modal.alert("削除対象がありません");
				return
			}
			UIkit.modal.confirm(Object.keys(upToData).length+"件削除します。<br>よろしいですか").then(function () {
                loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"<?php echo $api_url ?>",
	                type:"POST",
	                data:{
	                	data:JSON.stringify(upToData),
	                	_csrf : '<?php echo $csrf_token ?>',
	                	Action : 'itemDelete',
	                },
	                dataType: "json"
		        })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
					if(! data.result){
						UIkit.modal.alert("削除に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
	            	
	        		UIkit.modal.alert("削除が完了しました").then(function(){
						location.reload();
					});
	            })
	            .fail( (data) => {
	        		UIkit.modal.alert("削除に失敗しました").then(function(){
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

		function commentUpdate(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			
			if ($("textarea[name='ordercomment']").val().bytes() > 512)
			{
				UIkit.modal.alert('備考は全角256文字以内、半角512文字以内で入力してください');
				return false;
			}
			UIkit.modal.confirm("備考を更新します。<br>よろしいですか").then(function () {
            	loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"<?php echo $api_url ?>",
	                type:"POST",
	                data:{
	                	Action : 'orderCommentApi',
	                	_csrf : '<?php echo $csrf_token ?>',
	                	ordercomment: encodeURI($("textarea[name='ordercomment']").val()),
	                },
	                dataType: "json"
		        })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
					if(! data.result){
						UIkit.modal.alert("備考の更新に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
	        		UIkit.modal.alert("備考を更新しました").then(function(){
						location.reload();
					});
	            })
	            .fail( (data) => {
	        		UIkit.modal.alert("備考の更新に失敗しました").then(function(){
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
		function orderFix(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			let itemsToJs = objectValueToURIencode( <?php echo json_encode($ItemsToJs); ?> );
			UIkit.modal.confirm("発注を確定します。<br>よろしいですか").then(function () {
            	loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"<?php echo $api_url ?>",
	                type:"POST",
	                data:{
	                	_csrf : '<?php echo $csrf_token ?>',
	                	ordercomment : encodeURI($("textarea[name='ordercomment']").val()),
	                	Action : 'orderFix',
	                },
	                dataType: "json"
		        })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
					if(! data.result){
						UIkit.modal.alert("発注書確定に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
	        		UIkit.modal.alert("発注を確定しました").then(function(){
						UIkit.modal.alert("一覧表へ戻ります").then(function(){
							location.href ="<?php echo $link ?>&table_cache=true";
						});
					});
	            })
	            .fail( (data) => {
	        		UIkit.modal.alert("発注に失敗しました").then(function(){
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
		
		function orderedDelete(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			UIkit.modal.confirm("発注書を削除します。<br>よろしいですか").then(function () {
				let itemsToJs = objectValueToURIencode( <?php echo json_encode($ItemsToJs); ?> );
				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"<?php echo $api_url ?>",
					type:"POST",
					data:{
						Action: "orderDelete",
	                	_csrf : '<?php echo $csrf_token ?>',
					},
					dataType: "json"
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
					
					if(! data.result){
						UIkit.modal.alert("発注書取消に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
					UIkit.modal.alert("発注書取消が完了しました").then(function(){
						location.href ="<?php echo $link ?>&table_cache=true";
					});
					
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
					
					UIkit.modal.alert("伝票削除に失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});	
					return false;
				})
				// Ajaxリクエストが成功・失敗どちらでも発動
				.always( (data) => {
					loading_remove();
				});
			},function(){
				UIkit.modal.alert("中止します");
			});
		}
    </script>
	<?php  endif ?>