<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="<?php echo $link ?>&table_cache=true"><span>払出履歴一覧</span></a></li>
                <li><span>払出伝票</span></li>
            </ul>
            <div class="no_print uk-margin" uk-margin>
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                <input class="print_hidden uk-button uk-button-danger" type="button" value="払出伝票取消" onclick="payoutDelete();return false;">
                <input class="print_hidden uk-button uk-button-primary" type="button" value="払出ラベル発行" onclick="createLabel();return false;">
            </div>
            <div class="uk-text-center uk-text-large">
                <p class="uk-text-bold title_spacing" style="font-size: 32px">払出伝票</p>
            </div>
            <div uk-grid>
                <div class="uk-width-1-2@m">
                    <table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
                        <tr>
                            <td class="uk-text-bold">払出日時</td>
                            <td class="uk-text-right">%val:usr:registrationTime%</td>
                        </tr>
                        <tr>
                            <td class="uk-text-bold">払出部署</td>
                            <td class="uk-text-right">%val:usr:sourceDivision%<span uk-icon="icon: arrow-right"></span>%val:usr:targetDivision%</td>
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
                                    <th class="uk-text-nowrap">価格</th>
                                    <th class="uk-text-nowrap">単価</th>
                                    <th class="uk-text-nowrap">数量</th>
                                    <th></th>
                                    <th class="uk-text-nowrap">個数（ラベル枚数）</th>
                                    <th class="uk-text-nowrap">合計払出数</th>
                                    <th class="uk-text-nowrap">払出金額</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
				    					$num = 1;
										foreach($payoutData as $record){
				    						echo "<tr>";
				    						echo "<td class='uk-text-nowrap'>".$num."</td>";
				    						echo "<td>".$record->makerName."</td>";
				    						echo "<td>".$record->itemName."</td>";
				    						echo "<td>".$record->itemCode."</td>";
				    						echo "<td>".$record->itemStandard."</td>";
				    						echo "<td>".$record->lotNumber."</td>";
				    						echo "<td>".$record->lotDate."</td>";
				    						echo "<td class='uk-text-nowrap'>".$record->quantity.$record->quantityUnit."</td>";
				    						echo "<td class='uk-text-nowrap'>￥".number_format($record->price,2)." / ".$record->quantityUnit."</td>";
				    						echo "<td class='uk-text-nowrap'>￥".number_format($record->unitPrice,2)."</td>";
											echo "<td class='uk-text-nowrap'>".$record->payoutCount.$record->quantityUnit."</td>";
											
											echo "<td>×</td>";
											echo "<td class='uk-text-nowrap'>".$record->payoutLabelCount."枚</td>";

				    						echo "<td class='uk-text-nowrap'>".$record->payoutQuantity.$record->quantityUnit."</td>";
				    						echo "<td class='uk-text-nowrap'>￥".number_format($record->payoutAmount,2)."</td>";
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
<form action="%url/rel:@mpgt:Payout%&Action=payoutLabel" target="_blank" method="post" class="uk-hidden" name="LabelCreate">
    <input type="hidden" value="%val:usr:payoutHistoryId%" name="payoutHistoryId" id="payoutHistoryId">
    <input type="hidden" value="payout" name="pattern">
</form>
	<script>
		let canAjax = true;
		let lotdata = {};

		function payoutDelete(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			
			let flg = true;
			
			UIkit.modal.confirm("払出情報を削除します。<br>よろしいですか<br>注意:払出元へ在庫数を戻します。").then(function () {
				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"<?php echo $api_url ?>",
	                type:"POST",
	                data:{
	                	Action : "payoutSlipDeleteApi",
	                	_csrf : "<?php echo $csrf_token ?>",
	                },
	                dataType: "json"
	            })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
	            	
	                if(! data.result){
	            		UIkit.modal.alert("払出取消に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
	            		return false;
	                }
	                UIkit.modal.alert("払出取消が完了しました").then(function(){
						location.href ="<?php echo $link ?>&table_cache=true";
					});
					
	            })
	            // Ajaxリクエストが失敗した時発動
	            .fail( (data) => {
	            	
	        		UIkit.modal.alert("払出取消に失敗しました").then(function(){
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
		
		function createLabel(){
			UIkit.modal.confirm("ラベル発行を行います").then(function(){
				$('form[name=LabelCreate]').submit();
			});
			return true;
		}
	</script>