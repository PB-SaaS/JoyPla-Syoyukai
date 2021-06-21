<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 商品マスタ</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>商品マスタ</span></li>
				</ul>
		    	<div class="no_print uk-margin">
				  <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
				  <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click()">
				</div>
		    	<div class="uk-width-1-1" uk-grid>
		    		<div class="uk-width-3-4@l uk-width-2-3">
		    			<h2>商品マスタ</h2>
					</div>
		    	</div>
				%sf:usr:search21%
			</div>
		</div>
	</div>
</body>
</html>
