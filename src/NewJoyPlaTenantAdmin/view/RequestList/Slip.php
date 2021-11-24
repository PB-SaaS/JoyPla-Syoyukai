<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top">
    <div class="uk-container uk-container-expand">
        <div uk-grid>
            <div class="uk-width-1-2@m">
                <h1>見積依頼詳細</h1>
            </div>
        </div>
        <div>
            <ul class="uk-child-width-expand uk-tab" >
                <li class="<?php echo $switch_1 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&table_cache=true'">依頼内容</a></li>
                <li class="<?php echo $switch_2 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&Switcher=PriceReg'">金額見積</a></li>
            </ul>
        </div>
        <?php if($switch_1 !== ""): ?>
        
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
        <?php elseif($switch_2 !== ""): ?>
    	<div class="uk-margin spiral_table_area" style="display:none">
    		%sf:usr:search54:table:mstfilter%
    	</div>
        <?php endif ?>
        <script>
            let elem = $('table tbody a');
            let param = "<?php echo $_POST['BACK'] ?>";
            if(param != "")
            {
            	for(let index = 0 ; index < elem.length ; index++){
            		elem[index].href += "&BACK="+param;
            	};
            }
        </script>
    </div>
</div>