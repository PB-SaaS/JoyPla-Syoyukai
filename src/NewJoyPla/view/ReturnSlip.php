<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span>返品一覧</span></a></li>
				    <li><span>返品伝票</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold title_spacing" style="font-size: 32px">返品伝票</p>
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
		    					<td>
		    						返品合計金額
		    					</td>
		    					<td>
		    						￥-<script>price(fixed("%val:usr:returnTotalPrice%"));</script>
		    					</td>
		    				</tr>
		    			</table>
			    	</div>
			    	<div class="uk-width-1-2@m">
			    		<div class="uk-float-right uk-width-2-3@m">
			    			<table class="uk-table uk-width-1-1 uk-table-divider">
			    				<tr>
			    					<td>返品日時</td>
			    					<td>%val:usr:registrationTime%</td>
			    				</tr>
			    				<tr>
			    					<td>返品番号</td>
			    					<td>%val:usr:returnHistoryID%</td>
			    				</tr>
			    				<tr>
			    					<td>検収番号</td>
			    					<td>%val:usr:receivingHId%</td>
			    				</tr>
			    				<tr>
			    					<td>発注番号</td>
			    					<td>%val:usr:orderHistoryId%</td>
			    				</tr>
			    			</table>
							<div id="return_barcode" class="uk-text-center">
								<span id="return_num">%val:usr:returnHistoryID%</span>
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
										<th class="uk-table-expand">メーカー</th>
										<th class="uk-table-expand">商品名</th>
										<th class="uk-table-expand">製品コード</th>
										<th class="uk-table-expand">規格</th>
										<th class="uk-table-expand">JANコード</th>
										<th class="uk-text-nowrap">ロット番号</th>
										<th class="uk-text-nowrap">使用期限</th>
										<th class="uk-text-nowrap">入数</th>
										<th class="uk-text-nowrap">入庫数</th>
										<th class="uk-text-nowrap">返品数</th>
										<th class="uk-text-nowrap">返品金額</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
				    					$num = 1;
										foreach($return_items as $record){
				    						echo "<tr>";
				    						echo "<td>".$num."</td>";
				    						echo "<td>".$record->makerName."</td>";
				    						echo "<td>".$record->itemName."</td>";
				    						echo "<td>".$record->itemCode."</td>";
				    						echo "<td>".$record->itemStandard."</td>";
				    						echo "<td>".$record->itemJanCode."</td>";
				    						echo "<td>".$record->lotNumber."</td>";
				    						echo "<td>".$record->lotDate."</td>";
				    						echo "<td>".$record->quantity.$record->quantityUnit."</td>";
				    						echo "<td>".$record->receivingCount.$record->itemUnit."</td>";
				    						echo "<td>".$record->returnCount.$record->itemUnit."</td>";
				    						echo "<td>￥".number_format($record->returnPrice,2)."</td>";
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
		$(function(){
		 let return_num = $("#return_num").text();
		 //$("#hacchu_num").remove();
		 $("#return_barcode").html("<svg id='barcode_return'></svg>");
		 generateBarcode("barcode_return",return_num);
		 //$("td#order_barcode div").barcode({code:order_num, crc:false }, "int25",{barWidth: 3 ,barHeight: 40 , output: "css"});
		});
	</script>