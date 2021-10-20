
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span><?= $title ?></span></li>
				</ul>
		    	<h2 class="page_title uk-margin-remove">参照したい部署を選択してください</h2>
		    	<hr>
                        
		    	<div class="uk-margin" id="tablearea">
                    <?= $table ?>
		    	</div>
			</div>
		</div>
	</div>
<script>
	let elem = $('table tbody a');
	for(let index = 0 ; index < elem.length ; index++){
		elem[index].href += "&Action=<?php echo $param ?>";
	};
</script>