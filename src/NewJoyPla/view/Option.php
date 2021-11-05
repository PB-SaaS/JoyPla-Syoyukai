<div class="animsition" uk-height-viewport="expand: true">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb no_print">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
			    <li><span>オプション情報</span></li>
			</ul>
			
	    	<div class='uk-width-1-1' uk-grid>
	    		<div class="uk-width-5-6@m uk-width-2-3">
	    			<h2>オプション情報</h2>
				</div>
	    	</div>
    		<p class="uk-text-warning">オプションの変更はお問合せよりご連絡ください。</p>
	    	<div class='uk-width-4-5@m uk-margin-auto uk-margin-remove-top' >
	    		<table class="uk-table uk-table-divider uk-table-responsive">
				        <tr>
				            <th>テナント種別</th>
				            <td><?php echo ($tenant->tenantKind == '1')? "シングルテナント" : "マルチテナント" ;?></td>
				        </tr>
				        <tr>
				            <th>入庫先設定</th>
				            <td><?php echo ($hospital->receivingTarget == '1')? "大倉庫" : "発注部署" ;?></td>
				        </tr>
				        <tr>
				            <th>登録可能ユーザー数</th>
				            <td><?php echo $hospital->registerableNum ?> 人まで</td>
				        </tr>
				        <tr>
				            <th>消費計算方法</th>
				            <td><?php echo ($hospital->billingUnitPrice == '1')? '単価フィールドを使用する' : '購買価格を使用する' ?></td>
				        </tr>
				        <tr>
				            <th>払出計算方法</th>
				            <td><?php echo ($hospital->payoutUnitPrice == '1')? '単価フィールドを使用する' : '購買価格を使用する' ?></td>
				        </tr>
				        <tr>
				            <th>棚卸計算方法</th>
				            <td><?php echo ($hospital->invUnitPrice == '1')? '単価フィールドを使用する' : '購買価格を使用する' ?></td>
				        </tr>
				</table>
	    	</div>
		</div>
	</div>
</div>