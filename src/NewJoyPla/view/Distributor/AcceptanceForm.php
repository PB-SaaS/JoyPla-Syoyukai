
<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb no_print">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
			    <li><a href="<?php echo $link ?>&table_cache=true"><span><?php echo $link_title ?></span></a></li>
			    <li><span>検収書</span></li>
			</ul>
			<div class="no_print uk-margin" uk-margin>
				<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
			</div>
	    	<div class="uk-text-center uk-text-large">
	    		<p class="uk-text-bold title_spacing" style="font-size: 32px">検収書</p>
	    	</div>
	    	<div uk-grid>
		    	<div class="uk-width-1-2@m">
	    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
	    				<tr class="uk-text-large">
	    					<td colspan="2">
	    						<b>%val:usr:distributorName% 御中</b>
	    					</td>
	    				</tr>
	    			</table>
		    	</div>
		    	<div class="uk-width-1-2@m">
		    		<div class="uk-float-right uk-width-2-3@m">
		    			<table class="uk-table uk-width-1-1 uk-table-divider"> 
		    				<tr>
		    					<td>検収日時</td>
		    					<td>%val:usr:registrationTime%</td>
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
	    				<div id="nouhin_barcode" class="uk-text-center">
    						<span id="nouhin_num">%val:usr:receivingHId%</span>
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
		    		<div class="uk-overflow-auto">
			    		<table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap" id="tbl-Items">
			    			<thead>
			    				<tr>
									<th>NO</th>
									<th style="min-width:150px">メーカー</th>
									<th style="min-width:150px">商品名</th>
									<th>製品コード</th>
									<th>規格</th>
									<th>JANコード</th>
									<th>入数</th>
									<th>発注数</th>
									<th>入庫数</th>
									<th>入荷状況</th>
			    				</tr>
			    			</thead>
			    			<tbody>
			    				<?php
			    					$num = 1;
			    					foreach($receivingData as $record){
			    						echo "<tr>";
			    						echo "<td>".$num."</td>";
			    						echo "<td>".$record->makerName."</td>";
			    						echo "<td>".$record->itemName."</td>";
			    						echo "<td>".$record->itemCode."</td>";
			    						echo "<td>".$record->itemStandard."</td>";
			    						echo "<td>".$record->itemJANCode."</td>";
			    						echo "<td>".$record->quantity.$record->quantityUnit."</td>";
			    						echo "<td>".$record->orderQuantity.$record->itemUnit."</td>";
			    						echo "<td>".$record->receivingCount.$record->itemUnit."</td>";
			    						$text = "入庫完了";
			    						if((int)$record->orderQuantity > (int)$record->receivingCount){
			    							$text = "一部入庫(".$record->receivingCount." / ".$record->orderQuantity.")";
			    						}
			    						
			    						$returnText = "";
			    						if((int)$record->totalReturnCount > 0){
			    							$returnText = "<span class='uk-text-danger'>一部返品(".$record->totalReturnCount." / ".$record->receivingCount.")</span>";
			    						}
			    						echo "<td>".$text."<br>".$returnText."</td>";
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