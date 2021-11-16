<div class="uk-background-muted uk-padding uk-padding-remove-top"  uk-height-viewport="offset-top: true" style="border-right:#98CB00 1px solid">
    <a href="#" class="uk-navbar-item uk-logo">
        <img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
    </a>
	<ul class="uk-nav-default uk-nav-divider uk-nav-parent-icon uk-margin" uk-nav>
		<li class="uk-parent <?php echo $n1 ?>">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon: users"></span> 施設管理</a>
            <ul class="uk-nav-sub">
                <li class=" <?php echo $n1_1 ?>"><a href="%url/rel:mpgt:Facility%&table_cache=true">施設一覧</a></li>
                <li class=" <?php echo $n1_3 ?>"><a href="%url/rel:mpgt:Facility%&Action=Regist&table_cache=true">施設登録</a></li>
            </ul>
        </li>
		<li class="uk-parent <?php echo $n4 ?>">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon: users"></span> 実績確認</a>
            <ul class="uk-nav-sub">
                <li class=" <?php echo $n4_1 ?>"><a href="%url/rel:mpgt:History%">消費履歴詳細一覧</a></li>
                <li class=" <?php echo $n4_2 ?>"><a href="">注文履歴詳細一覧</a></li>
                <li class=" <?php echo $n4_3 ?>"><a href="">入庫履歴詳細一覧</a></li>
                <li class=" <?php echo $n4_4 ?>"><a href="">返品履歴詳細一覧</a></li>
                <li class=" <?php echo $n4_5 ?>"><a href="">払出履歴詳細一覧</a></li>
                <li class=" <?php echo $n4_6 ?>"><a href="">消費実績</a></li>
                <li class=" <?php echo $n4_7 ?>"><a href="">注文実績</a></li>
                <li class=" <?php echo $n4_8 ?>"><a href="">入庫実績</a></li>
                <li class=" <?php echo $n4_9 ?>"><a href="">払出実績</a></li>
                <li class=" <?php echo $n4_10 ?>"><a href="%url/rel:mpgt:History%&Action=ReceivingHistoryMCH&table_cache=true">入庫実績(MCH様)</a></li>
                <li class=" <?php echo $n4_11 ?>"><a href="%url/rel:mpgt:History%&Action=ReturnHistoryMCH&table_cache=true">払出実績(MCH様)</a></li>
            </ul>
        </li>
		<li class=" <?php echo $n2 ?>"><a href="%url/rel:mpgt:Distributor%"><span class="uk-margin-small-right" uk-icon="icon: users"></span> 卸業者管理</a></li>
		<li class="uk-parent <?php echo $n3 ?>">
            <a href="#"><span class="uk-margin-small-right" uk-icon="icon: album"></span> 商品管理</a>
            <ul class="uk-nav-sub">
                <li class=" <?php echo $n3_1 ?>"><a href="%url/rel:mpgt:Goods%&table_cache=true">商品管理</a></li>
                <li class=" <?php echo $n3_2 ?>"><a href="%url/rel:mpgt:BulkItem%">商品一括登録・更新</a></li>
                <li class=" <?php echo $n3_3 ?>"><a href="%url/rel:mpgt:BulkItem%&Action=logsList">商品登録更新履歴</a></li>
                <li class=" <?php echo $n3_4 ?>"><a href="%url/rel:mpgt:PriceCont%">金額管理</a></li>
                <li class=" <?php echo $n3_4 ?>"><a href="%url/rel:mpgt:PriceCont%&Action=bulkUpsert">金額情報一括登録</a></li>
                <li class=" <?php echo $n3_5 ?>"><a href="#">院内商品管理</a></li>
                <li class=" <?php echo $n3_6 ?>"><a href="#">院内商品一括登録</a></li>
            </ul>
        </li>
		<li><a href="#"><span class="uk-margin-small-right" uk-icon="icon: question"></span> お問合せ管理</a></li>
		<li><a href="#"><span class="uk-margin-small-right" uk-icon="icon: sign-out"></span> ログアウト</a></li>
	</ul>
</div>