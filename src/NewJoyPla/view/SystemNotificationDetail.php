<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla システム通知詳細</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="%url/table:back%">システム通知一覧</a></li>
				    <li><span>システム通知詳細</span></li>
				</ul>
		    	<hr>
		    	<div uk-grid="" uk-margin="" class="uk-grid uk-grid-stack">
					<div class="uk-width-1-1 uk-grid-margin uk-first-column uk-margin-small-top">
					</div>
				</div>
		    	<div class="uk-margin-auto uk-width-2-3@m">
		    		<article class="uk-article uk-margin-bottom">

					    <h1 class="uk-article-title">%val:usr:title%</h1>
					
					    <p class="uk-article-meta">作成者 %val:usr:creator%</p>
					
					    <p class="">%val:usr:content:br%</p>
						<div class="uk-grid-small uk-child-width-auto uk-grid" uk-grid>
								<div class="uk-width-1-2 uk-first-column">
						    		%val:usr:registrationTime%
								</div>
						    </div>
					</article>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
