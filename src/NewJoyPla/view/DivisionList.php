<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 部署一覧</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>部署一覧</span></li>
				</ul>
		    	<h2 class="page_title uk-margin-remove">部署一覧</h2>
		    	<hr>
		    	
				<div uk-grid uk-margin>
					<div class="uk-width-1-1">
		    			<form action="/regist/is" target="_blank" method="post">
		    				%SMPAREA%
							<input type="hidden" name="SMPFORM" value="%smpform:divisionReg%">
							<input type="hidden" name="hospitalId" value="%val:usr:hospitalId%">
							<input type="hidden" name="divisionType" value="2">
							<input type="submit" value="部署登録" class="uk-button uk-button-primary">
		    			</form>
                        <p class="uk-text-danger uk-text-bold">※大倉庫の登録は1つまでです</p>
					</div>
				</div>
                        
		    	<div class="uk-margin" id="tablearea">
					%sf:usr:search80:table%
		    	</div>
			</div>
		</div>
	</div>
</body>
</html>
