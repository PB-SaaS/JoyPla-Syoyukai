<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				<?php if($submenu): ?>
			    <li><a href="<?php echo $submenulink ?>"><?php echo $submenu ?></a></li>
				<?php endif ?>
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
<script>
	$(function(){
		$('#content table[id^=smp-table-] td').each(function(){
			var txt = $(this).html();
			$(this).html(
				txt.replace(/\\/g,'&yen;')
			);
		});
        $('#content table[id^=smp-table-] colgroup').remove();
        $('#content table[id^=smp-table-] select[name^=_limit_]').addClass('uk-select uk-form-width-small');
        $('#content table[id^=smp-table-] button').addClass('uk-button');
        $('#content table[id^=smp-table-] input[type=checkbox]').addClass('uk-checkbox');
        $('#content table[id^=smp-table-] input[type=button]').addClass('uk-button');
        $('#content table[id^=smp-table-] input[type=button][value="ダウンロード"]').addClass('uk-button-primary');
        $('#content table[id^=smp-table-] input[type=button][value="更新"]').addClass('uk-button-primary');
        $('#content table[id^=smp-table-] input[type=submit]').addClass('uk-button');
        $('#content table[id^=smp-table-] input[type=submit][value="更新"]').addClass('uk-button-primary');
        $('#content table[id^=smp-table-] input[type=reset]').addClass('uk-button uk-button-default'); 
        $('#content table[id^=smp-table-] input[type=text]').addClass('uk-input uk-width-small');
        $('#content table[id^=smp-table-] .smp-pager').replaceWith(function() {
            $('td.smp-current-page',this).replaceWith(function() {
                $(this).replaceWith("<li class='uk-active'><span>"+$(this).html()+"</span></li>");
            });
            $('td.smp-page-space',this).replaceWith(function() {
                $(this).replaceWith("<li class='uk-disabled'><span>"+$(this).html()+"</span></li>");
            });
            $('td',this).replaceWith(function() {
                $(this).replaceWith("<li>"+$(this).html()+"</li>");
            });
            $(this).replaceWith("<ul class='uk-pagination'>"+$(this).html()+"</ul>")
        });
        $('#content table[id^=smp-table-] .smp-pager td').addClass('uk-padding-remove');
        $('#content table[id^=smp-table-] *').removeAttr('style');
        $('#content table[id^=smp-table-]').wrap('<div class="uk-overflow-auto" />');
        $('#content table[id^=smp-table-]').addClass('uk-table uk-table-middle'); 
        $('#content table[id^=smp-table-] tr.smp-row-data td').addClass('uk-text-nowrap uk-text-left'); 
        $('#content table[id^=smp-table-] tr.smp-row-sort td').addClass('uk-text-nowrap uk-text-left');
        
        $('#content .smp-search-form td table').replaceWith(function() {
            $(this).replaceWith("<div>"+$(this).html()+"</div>");
        });
        $('#content .smp-search-form td div').replaceWith(function() {
            $(this).replaceWith(""+$(this).html()+"");
        });
        
        $('#content .smp-search-form').addClass('uk-width-2-3@m uk-margin-auto');
        $('#content .smp-search-form table.smp-search-form-table').addClass('uk-table uk-table-middle uk-table-divider');
        
        $('#content .smp-search-form table.smp-search-form-table td').each(function(index, elem){
           $('label',elem).wrapAll('<div class="uk-margin uk-margin-top uk-grid-small uk-child-width-auto uk-grid" />');
        });
        
        $('#content .smp-search-form').prepend('<h3 class="uk-text-center uk-background-muted uk-padding-small">検索</h3>');
        $('#content .smp-search-form select').addClass('uk-select uk-form-width-small');
        $('#content .smp-search-form button').addClass('uk-button');
        $('#content .smp-search-form input[type=checkbox]').addClass('uk-checkbox');
        $('#content .smp-search-form input[type=radio]').addClass('uk-radio');
        $('#content .smp-search-form input[type=button]').addClass('uk-button');
        $('#content .smp-search-form input[type=submit]').addClass('uk-button uk-button-default');
        $('#content .smp-search-form input[type=text]').addClass('uk-input uk-width-expand');
        $('#content .smp-search-form .smp-pager td').addClass('uk-padding-remove');
        $('#content .smp-search-form *').removeAttr('style');
        $('#content .smp-search-form').wrap('<div class="uk-overflow-auto" />');
        $('#content .smp-search-form').addClass('uk-table uk-table-middle'); 
        $('#content .smp-search-form .smp-sf-head').addClass('uk-width-1-5'); 
        
        
        $('.spiral_table_area').show();
		
		new ScrollHint('.uk-table', {
			scrollHintIconAppendClass: 'scroll-hint-icon-white', // white-icon will appear
			applyToParents: true,
			i18n: {
			scrollable: 'スクロールできます'
			}
		});
		
    });
</script>
<?php 
echo $script;
?>