<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 商品情報一覧</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
  </head>
  <body>

	<script>
	function hanei(elm){
		let shouhinTr = $(elm.parentNode.parentNode);
		shouhinObject = JSON.parse($('.json',shouhinTr).text().replace(/\r?\n/g, '').trim());
		window.opener.addTr(shouhinObject);
	}
	</script>    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>商品情報一覧</span></li>
				</ul>
		    	<h2 class="page_title">商品情報一覧</h2>
		    	<hr>
		    	<div class="" id="tablearea">
		    		%sf:usr:search9%
		    	</div>
		    </div>
		</div>
	</div>
  </body>
</html>
