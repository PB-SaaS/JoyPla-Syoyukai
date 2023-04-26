<?php
$auth = new App\Lib\Auth(); ?>
<div class="uk-background-muted uk-padding uk-padding-remove-top no_print"  uk-height-viewport="offset-top: true" style="border-right:#98CB00 1px solid">
    <a href="%url/rel:mpg:top%" class="uk-navbar-item uk-logo">
        <img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
    </a>
    <div class="uk-text-center">
        <span class="uk-text-small"><?php echo $auth->tenantName; ?></span><br>
        <span class="uk-text-default"><?php echo $auth->name; ?></span>
    </div>
    <ul class="uk-nav-default uk-nav-divider uk-nav-parent-icon uk-margin" uk-nav>
        <li class="uk-parent <?php echo $n1; ?>">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon: users"></span> 病院情報管理</a>
            <ul class="uk-nav-sub">
                <?php if ($auth->Gate('FacilityList')): ?>
                <li class=" <?php echo $n1_1; ?>"><a href="%url/rel:mpgt:Facility%&table_cache=true">病院情報管理</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('FacilityRegist')): ?>
                <li class=" <?php echo $n1_3; ?>"><a href="%url/rel:mpgt:Facility%&Action=Regist&table_cache=true">病院情報登録</a></li>
                <?php endif; ?>
            </ul>
        </li>
        <li class="uk-parent <?php echo $n2; ?>">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon: users"></span> 卸業者管理</a>
            <ul class="uk-nav-sub">
                <li class=" <?php echo $n2_1; ?>"><a href="%url/rel:mpgt:Distributor%">卸業者管理</a></li>
                <?php if ($auth->Gate('DistributorBlukInsert')): ?>
                <li class=" <?php echo $n2_2; ?>"><a href="%url/rel:mpgt:Distributor%&Action=bulkInsert">卸業者一括登録・更新</a></li>
                <?php endif; ?>
            </ul>
        </li>
        <li class="uk-parent <?php echo $n3; ?>">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon: album"></span> 商品管理</a>
            <ul class="uk-nav-sub">
                <li class=" <?php echo $n3_1; ?>"><a href="%url/rel:mpgt:Goods%&table_cache=true">商品管理</a></li>
                <li class=" <?php echo $n3_8; ?>"><a href="%url/rel:mpgt:Goods%&Action=insert">商品登録</a></li>
                <?php if ($auth->Gate('ItemBulkUpsert')): ?>
                <li class=" <?php echo $n3_2; ?>"><a href="%url/rel:mpgt:BulkItem%">商品一括登録・更新</a></li>
                <?php endif; ?>
                <li class=" <?php echo $n3_3; ?>"><a href="%url/rel:mpgt:BulkItem%&Action=logsList">商品登録更新履歴</a></li>
                <li class=" <?php echo $n3_4; ?>"><a href="%url/rel:mpgt:PriceCont%">金額管理</a></li>
                <?php if ($auth->Gate('PriceBulkUpsert')): ?>
                <li class=" <?php echo $n3_5; ?>"><a href="%url/rel:mpgt:PriceCont%&Action=bulkUpsert">金額情報一括登録・更新</a></li>
                <?php endif; ?>
                <li class=" <?php echo $n3_6; ?>"><a href="%url/rel:mpgt:InHospitalItem%">院内商品管理</a></li>
                <?php if ($auth->Gate('PriceAndInHospitalItemBulkInsert')): ?>
                <li class=" <?php echo $n3_7; ?>"><a href="%url/rel:mpgt:InHospitalItem%&Action=bulkInsert">金額・院内商品一括登録</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('NewFacilityItemsBulkInsert')): ?>
                <li class=" <?php echo $n3_9; ?>"><a href="%url/rel:mpgt:Goods%&Action=allBulkInsert">新規導入用一括登録</a></li>
                <?php endif; ?>
                <li class=" <?php echo $n3_10; ?>"><a href="%url/rel:mpgt:Goods%&Action=allInsertForm">商品・金額・院内商品登録</a></li>
                <?php if ($auth->Gate('PriceBulkUpsert')): ?>
                <li class=" <?php echo $n3_11; ?>"><a href="%url/rel:mpgt:PriceCont%&Action=priceInformationReservationUpdate">金額情報予約更新</a></li>
                <li class=" <?php echo $n3_12; ?>"><a href="%url/rel:mpgt:PriceCont%&Action=priceInformationReservationList">金額情報予約一覧</a></li>
                <?php endif; ?>
            </ul>
        </li>
        <li class="uk-parent <?php echo $n5; ?>">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon:database"></span> 在庫管理</a>
            <ul class="uk-nav-sub">
                <li class=" <?php echo $n5_1; ?>"><a href="%url/rel:mpgt:Stocks%">在庫管理</a></li>
                <li class=" <?php echo $n5_2; ?>"><a href="%url/rel:mpgt:Stocks%&Action=Lots">ロット管理</a></li>
                <li class=" <?php echo $n5_3; ?>"><a href="%url/rel:mpgt:Stocks%&Action=Cards">カード情報管理</a></li>
                <li class=" <?php echo $n5_4; ?>"><a href="%url/rel:mpgt:Stocks%&Action=CardsBulkInsert">カード情報一括登録</a></li>
            </ul>
        </li>
        <li class="uk-parent <?php echo $n6; ?>">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon: print"></span> 見積管理</a>
            <ul class="uk-nav-sub">
                <li class=" <?php echo $n6_1; ?>"><a href="%url/rel:mpgt:Request%">見積管理</a></li>
            </ul>
        </li>
        <li class="uk-parent <?php echo $n4; ?>">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon: file-edit"></span> 実績確認</a>
            <ul class="uk-nav-sub">
                <?php if ($auth->Gate('ConsumedHistory')): ?>
                <li class=" <?php echo $n4_1; ?>"><a href="%url/rel:mpgt:History%&table_cache=true">消費履歴詳細一覧</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('OrderedHistory')): ?>
                <li class=" <?php echo $n4_2; ?>"><a href="%url/rel:mpgt:History%&Action=OrderedHistoryList&table_cache=true">注文履歴詳細一覧</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('ReceivedHistory')): ?>
                <li class=" <?php echo $n4_3; ?>"><a href="%url/rel:mpgt:History%&Action=ReceivedHistoryList&table_cache=true">入荷履歴詳細一覧</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('ItemRequestHistory')): ?>
                <li class=" <?php echo $n4_13; ?>"><a href="%url/rel:mpgt:History%&Action=ItemRequestHistoryList&table_cache=true">請求履歴詳細一覧</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('PayoutHistory')): ?>
                <li class=" <?php echo $n4_5; ?>"><a href="%url/rel:mpgt:History%&Action=PayoutHistoryList&table_cache=true">払出履歴詳細一覧</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('ConsumMR')): ?>
                <li class=" <?php echo $n4_6; ?>"><a href="%url/rel:mpgt:MonthlyReport%&Action=GoodsBilling&table_cache=true">消費実績</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('OrderMR')): ?>
                <li class=" <?php echo $n4_7; ?>"><a href="%url/rel:mpgt:MonthlyReport%&Action=Order&table_cache=true">注文実績</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('ReceivingMR')): ?>
                <li class=" <?php echo $n4_8; ?>"><a href="%url/rel:mpgt:MonthlyReport%&Action=Receiving&table_cache=true">入庫実績</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('PayoutMR')): ?>
                <li class=" <?php echo $n4_9; ?>"><a href="%url/rel:mpgt:MonthlyReport%&Action=Payout&table_cache=true">払出実績</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('InventoryMovement')): ?>
                <li class=" <?php echo $n4_12; ?>"><a href="%url/rel:mpgt:MonthlyReport%&Action=InventoryMovement&table_cache=true">棚卸実績</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('ReceiveHistoryMCH')): ?>
                <li class=" <?php echo $n4_10; ?>"><a href="%url/rel:mpgt:History%&Action=ReceivingHistoryMCH&table_cache=true">入庫実績(MCH様)</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('ReturnHistoryMCH')): ?>
                <li class=" <?php echo $n4_11; ?>"><a href="%url/rel:mpgt:History%&Action=ReturnHistoryMCH&table_cache=true">返品実績(MCH様)</a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php
/*
        <li class="<?php echo $n7 ?>"><a href="%url/rel:mpgt:TopPage%&Action=topics&table_cache=true"><span class="uk-margin-small-right" uk-icon="icon: comments"></span> トピック一覧</a></li>
        */
?>
        <li class="uk-parent <?php echo $n8; ?>">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon: info"></span> システム通知</a>
            <ul class="uk-nav-sub">
                <?php if ($auth->Gate('SystemNotification')): ?>
                <li class=" <?php echo $n8_1; ?>"><a href="%url/rel:mpgt:TopPage%&Action=systemNotification&table_cache=true">システム通知</a></li>
                <?php endif; ?>
                <?php if ($auth->Gate('SystemNotificationReg')): ?>
                <li class=" <?php echo $n8_2; ?>"><a href="%url/rel:mpgt:TopPage%&Action=systemNotificationReg&table_cache=true">システム通知登録</a></li>
                <?php endif; ?>
            </ul>
        </li>
        <li class="uk-parent">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon: question"></span> ヘルプ</a>
            <ul class="uk-nav-sub">
                <li><a href="https://support.joypla.jp/" target="_blank" rel="noopener nofollow noreferrer"> サポートサイト</a></li>
                <li><a href="https://reg34.smp.ne.jp/regist/is?SMPFORM=meoj-lirdmf-2830358d2ea157fb8a38fdead8ace8c9" target="_blank" rel="noopener nofollow noreferrer"> サポート依頼</a></li>
            </ul>
        </li>
        <li><a href="%form:act:logout%"><span class="uk-margin-small-right" uk-icon="icon: sign-out"></span> ログアウト</a></li>
    </ul>
</div>