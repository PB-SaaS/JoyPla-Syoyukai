<div class="uk-section uk-dark uk-background-muted uk-padding">
    <div class="uk-container" uk-height-viewport="expand: true">
        <div class="uk-width-1-3@m uk-margin-auto">
            <div class="uk-logo uk-margin uk-text-center">
                <img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" /><br>
                <span class="uk-text-meta">テナント管理</span>
            </div>
            <div class="uk-card uk-card-default uk-card-small uk-card-body ">
                <?php if($error): ?>
                <div class="uk-text-center uk-margin">
                    <span class="uk-text-danger"><?php echo $error ?></span>
                </div>
                <?php endif ?>
                <form method="post" action="/area/Login" autocomplete="off">
                    <fieldset class="uk-fieldset">
                        <div class="uk-margin">
                            <label for="">ログインID</label>
                            <input class="uk-input" type="text" name="SMPID">
                        </div>
                    
                        <div class="uk-margin">
                            <label for="">パスワード</label>
                            <input class="uk-input" type="password" name="SMPPASSWORD">
                        </div>
                        %area:hidden%
                        <button type="submit" class="uk-button uk-button-primary uk-width-1-1">ログイン</button>
                        <div class="uk-margin uk-text-center">
                            <a href="%url/area:rereg:TenantAdmin%" >パスワードを忘れた方はこちら</a>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>