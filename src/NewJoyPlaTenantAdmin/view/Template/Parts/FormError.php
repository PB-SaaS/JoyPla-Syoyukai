<h1 id="errorHeader">
    <span uk-icon="icon: warning; ratio: 2.5" class="uk-margin-right"></span>
    <span class="uk-text-middle">システムエラー</span>
</h1>
<div class="uk-card uk-card-default uk-text-center">
    <div class="uk-card-body">
        <p class="uk-card-title uk-text-danger">
            登録できませんでした。<br>
        </p>
        <p class="uk-text-danger">
            <?php echo $code ?> :
            <?php echo $message ?>
        </p>
    </div>
    <div class="uk-card-footer">
        <p class="uk-background-muted uk-width-middle uk-text-bold uk-text-left uk-padding-small">お問合せ</p>
        <p class="uk-text-left">
            株式会社パイプドビッツ<br>
            ホスピタルソリューション事業部<br>
            TEL 03-5575-6601<br>
            Mail JoyPla-spd@pi-pe.co.jp<br>
            URL https://www.pi-pe.co.jp<br></p>
    </div>
</div>
<script>
    $(function() {
        $('h1').not('#errorHeader').css({
            'display': 'none'
        });
    })
</script>