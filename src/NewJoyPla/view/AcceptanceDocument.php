
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
					<li><a href="%url/rel:mpg:top%&page=page1">消費・発注</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span><?php echo $link_title ?></span></a></li>
				    <li><span>検収書</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<?php if(!$user_info->isApprover()): ?>
					<input class="print_hidden uk-button uk-button-danger" type="submit" value="返品伝票作成" onclick="location.href='<?php echo $api_url ?>&Action=returnSlipEntry'">
					<?php endif ?>
					<form action="%url/rel:@mpgt:ReceivingLabel%" target="_blank" method="post" class="print_hidden uk-inline">
						<input class="print_hidden uk-button uk-button-primary" type="submit" value="ラベル発行">
						<input type="hidden" value="<?php echo $receiving_history->receivingHId ?>" name="receivingId">
					</form>
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold  title_spacing" style="font-size: 32px">検収書</p>
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
		    				<div id="nouhin_barcode" class="uk-text-center">
	    						<span id="nouhin_num"><?php echo $receiving_history->receivingHId ?></span>
	    					</div>
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
										<th class="uk-text-nowrap">入荷状況</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
				    					$num = 1;
										foreach($receiving_item as $record){
				    						echo "<tr>";
				    						echo "<td>".$num."</td>";
				    						echo "<td>".$record->makerName."</td>";
				    						echo "<td>".$record->itemName."</td>";
				    						echo "<td>".$record->itemCode."</td>";
				    						echo "<td>".$record->itemStandard."</td>";
				    						echo "<td>".$record->lotNumber."</td>";
				    						echo "<td>".$record->lotDate."</td>";
				    						echo "<td class='uk-text-nowrap'>".$record->quantity.$record->quantityUnit."</td>";
				    						echo "<td class='uk-text-nowrap'>".$record->orderQuantity.$record->itemUnit."</td>";
				    						echo "<td class='uk-text-nowrap'>".$record->receivingCount.$record->itemUnit."</td>";
				    						$text = "入庫完了";
				    						if(abs((int)$record->orderQuantity) > abs((int)$record->receivingCount)){
				    							$text = "一部入庫(".$record->receivingCount." / ".$record->orderQuantity.")";
				    						}
				    						
				    						$returnText = "";
				    						if((int)$record->totalReturnCount > 0){
				    							$returnText = "<span class='uk-text-danger'>返品(".$record->totalReturnCount." / ".$record->receivingCount.")</span>";
				    						}
				    						echo "<td class='uk-text-nowrap'>".$text."<br>".$returnText."</td>";
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
		 let nouhin_num = $("#nouhin_num").text();
		 //$("#hacchu_num").remove();
		 $("#nouhin_barcode").html("<svg id='barcode_nouhin'></svg>");
		 generateBarcode("barcode_nouhin",nouhin_num);
		 //$("td#order_barcode div").barcode({code:order_num, crc:false }, "int25",{barWidth: 3 ,barHeight: 40 , output: "css"});
		});
		
	</script>