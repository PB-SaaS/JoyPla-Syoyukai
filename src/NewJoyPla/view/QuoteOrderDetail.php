<?php
include_once 'NewJoyPla/lib/UserInfo.php';
$userInfo = new App\Lib\UserInfo($SPIRAL);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 見積依頼詳細</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="%url/table:back%">見積依頼一覧</a></li>
				    <li><span>見積依頼詳細</span></li>
				</ul>
		    	<hr>
		    	<div class="uk-margin-auto uk-width-2-3@m">
		    		<article class="uk-article">

					    <h1 class="uk-article-title">%val:usr:requestTitle%</h1>
					    
					    <p class="uk-article-meta">
					        ステータス: %val:usr:requestStatus% <br>
                            依頼者 %val:usr:hospitalName% %val:usr:requestUName% <br>
					    	卸業者：%val:usr:distributorName%
					    </p>
					
					    <p class="">
					    	見積期限：%val:usr:quotePeriod% <br> <br>
					    	%val:usr:requestDetail:br%
					    	</p>
					
						<div class="uk-grid-small uk-child-width-auto" uk-grid>
							<div class="uk-width-1-2">
					    		%val:usr:registrationTime%
							</div>
					        
					    </div>
						
					</article>
				</div>
				<div>
					<p>見積商品一覧</p>
                    <?php if($userInfo->getUserPermission() == '1') : ?>
					<div>%sf:usr:search95:mstfilter:table%</div>
                    <?php else: ?>
					<div>%sf:usr:tantouMitsumori:mstfilter:table%</div>
                    <?php endif ?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
