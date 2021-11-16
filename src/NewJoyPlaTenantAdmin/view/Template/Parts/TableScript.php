<script>
    $(function(){
        $('#content table[id^=smp-table-] colgroup').remove();
        $('#content table[id^=smp-table-] select[name^=_limit_]').addClass('uk-select uk-form-width-small');
        $('#content table[id^=smp-table-] button').addClass('uk-button');
        $('#content table[id^=smp-table-] input[type=button]').addClass('uk-button');
        $('#content table[id^=smp-table-] input[type=button][value="ダウンロード"]').addClass('uk-button-primary');
        $('#content table[id^=smp-table-] input[type=submit]').addClass('uk-button');
        $('#content table[id^=smp-table-] input#smp-table-update-button').addClass('uk-button uk-button-primary');
        $('#content table[id^=smp-table-] input#smp-table-delete-button').addClass('uk-button-danger');
        $('#content table[id^=smp-table-] input#smp-table-reset-button').addClass('uk-button');
        
        
        $('#content table[id^=smp-table-] input[type=text]').addClass('uk-input uk-form-width-medium');
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
        $('#content table[id^=smp-table-] tr.smp-row-data td select').addClass('uk-select uk-form-width-medium');
        $('#content table[id^=smp-table-] tr.smp-row-data input[type=checkbox]').addClass('uk-checkbox');
        $('#content table[id^=smp-table-] tr.smp-row-sort input[type=checkbox]').addClass('uk-checkbox');
        
        $('#content table[id^=smp-table-] tr.smp-row-data a').addClass('uk-button uk-button-primary');
        $('#content table[id^=smp-table-] tr.smp-row-data a').text('詳細');
        
        
        $('#content table[id^=smp-table-]').each(function () {
            var txt = $(this).html();
            $(this).html(
              txt.replace(/\\/g, '&yen;')
            );
        });
        
        $('#content .smp-search-form td table').replaceWith(function() {
            $(this).replaceWith("<div>"+$(this).html()+"</div>");
        });
        $('#content .smp-search-form td div').replaceWith(function() {
            $(this).replaceWith(""+$(this).html()+"");
        });
        
        $('#content .smp-search-form')
        
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
        
        for(num = 0 ; num < $('#content .smp-search-form .smp-sf-body').length ; num++ )
        {
            if($('input[type="text"]',$('#content .smp-search-form .smp-sf-body')[num]).length == 2)
            {
                $('input[type="text"]',$('#content .smp-search-form .smp-sf-body')[num]).addClass('uk-width-1-3');
            }
        }
        
        $('.spiral_table_area').show();
    });
</script>