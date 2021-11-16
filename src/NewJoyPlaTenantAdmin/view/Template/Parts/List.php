<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
			    <li><span><?php echo $title ?></span></li>
			</ul>
			<div class="no_print uk-margin">
				<?php if($print): ?>
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
				<?php endif ?>
				<?php if($export): ?>
                <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click()">
				<?php endif ?>
				<?php echo $add_button ?>
            </div>
	    	<h2 class="page_title"><?php echo $title ?></h2>
	    	<hr>
	    	<div class="" id="tablearea">
	    		<?php echo $table ?>
	    	</div>
	    </div>
	</div>
</div>
<script>
	let elem = $('table tbody a');
	let param = "<?php echo $param ?>";
	if(param != "")
	{
    	for(let index = 0 ; index < elem.length ; index++){
    		elem[index].href += "&Action="+param;
    	};
	}
</script>
<?php 
echo $script 
?>