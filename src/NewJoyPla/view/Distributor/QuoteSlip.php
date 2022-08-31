<div class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                	<li><a href="%url/rel:mpg:top%&page=page1">商品・見積</a></li>
                <li><a href="<?php echo $link; ?>"><?php echo $link_title; ?></a></li>
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
            <?php if(!$isMulti) : ?>
            <hr>
            <div class="uk-margin">
                <p>見積商品一覧</p>
                <div>%sf:usr:search44:mstfilter:table%</div>
            </div>
            <form name="reqItemsReg" action="<?php echo $api_url; ?>" method="POST">
                <input type="hidden" name="Action" value="regQuoteItem">
            </form>
            <?php endif; ?>
            <hr>
            <div class="uk-margin">
                <p>金額見積依頼の商品一覧</p>
                <div>%sf:usr:search45:mstfilter:table%</div>
            </div>
        </div>
    </div>
</div>