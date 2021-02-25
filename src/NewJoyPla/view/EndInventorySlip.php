
<?php
include_once 'NewJoyPla/lib/Func.php';
$session = $SPIRAL->getSession();
$cardUrl = $session->get("cardUrl");
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 棚卸伝票</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>

  </head>
  <body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="%url/rel:mpgt:page_263631%&table_cache=true"><span>棚卸履歴一覧</span></a></li>
				    <li><a href="<?php echo $cardUrl ?>"><span>棚卸結果報告</span></a></li>
				    <li><span>%val:usr:divisionName% 棚卸結果報告</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
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
			    <div class="uk-margin" id="tablearea">
			    　　%sf:usr:search17:mstfilter:table%
		        </div>
		    	
		    </div>
		</div>
	</div>
  </body>
</html>