<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>JoyPla 金額情報変更 - 入力</title>
        <?php include_once "NewJoyPla/src/Head.php"; ?>

        <style>
            .uk-navbar-container {
                border-bottom: solid 2px #98CB00;
            }
            .bk-application-color {
                background: #98CB00;
            }
            #mainPage {
                display: none;
            }

            dl.cf {
                padding-bottom: 20px;
                border-bottom: 1px solid #e5e5e5 !important;
            }
        </style>
    </head>
    <body>
        <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">
            <div class="uk-container uk-container-expand" uk-height-viewport="expand: true">
                <div
                    class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom"
                    id="mainPage">

                    <!-- SMP_TEMPLATE_HEADER start -->
                    <h1>金額情報変更 - 入力</h1>

                    <!--SMP:DISP:REG:START-->
                    <p class="header_rmesg">必要事項をご入力の上、確認ボタンを押してください。</p>
                    <!--SMP:DISP:REG:END-->

                    <!--SMP:DISP:ERR:START-->
                    <p class="header_emesg">ご入力内容に不備がございます。エラーが発生した項目を修正し、送信しなおしてください。</p>
                    <!--SMP:DISP:ERR:END-->
                    <!-- SMP_TEMPLATE_HEADER end -->
                    <!-- SMP_TEMPLATE_FORM start -->
                    <form method="post" action="/regist/Reg2">
                        <div class="smp_tmpl">
                            <dl class="cf">
                                <dt class="title">
                                    不使用フラグ
                                </dt>
                                <dd class="data multi2">
                                    $notUsedFlag:v$
                                    <br>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                卸業者管理コード
                                </dt>
                                <dd class="data integer">

                                    <input
                                        class="input $errorInputColor:distributorMCode$"
                                        type="text"
                                        name="distributorMCode"
                                        value="$distributorMCode$"
                                        style="text-align: right;">
                                    <br>
                                    <span class="msg">$error:distributorMCode$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    商品ID
                                </dt>
                                <dd class="data ">
                                    $itemId$
                                    <br>
                                    <span class="msg">$error:itemId$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    メーカー
                                </dt>
                                <dd class="data ">
                                    <?php echo $makerName ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    商品名
                                </dt>
                                <dd class="data ">
                                    <?php echo $itemName ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    製品コード
                                </dt>
                                <dd class="data ">
                                    <?php echo $itemCode ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    規格
                                </dt>
                                <dd class="data ">
                                    <?php echo $itemStandard ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    JANコード
                                </dt>
                                <dd class="data ">
                                    <?php echo $itemJANCode ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    入数
                                </dt>
                                <dd class="data integer">
                                    $quantity$
                                    <br>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    入数単位
                                </dt>
                                <dd class="data ">
                                    $quantityUnit$
                                    <br>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    個数単位
                                </dt>
                                <dd class="data ">
                                    $itemUnit$
                                    <br>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    単価
                                </dt>
                                <dd class="data real">
                                    $unitPrice$
                                    <br>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    購買価格
                                </dt>
                                <dd class="data real">
                                    $price$
                                    <br>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    特記事項
                                </dt>
                                <dd class="data ">
                                    $notice:br$
                                    <br>
                                </dd>
                            </dl>
                        </div>
                        <input type="hidden" name="detect" value="判定">
                        <!-- HIDDEN_PARAM START -->
                        $form:hidden$<input type="hidden" name="id" value="$id$">
                        <input type="hidden" name="itemId" value="$itemId$">
                        <input type="hidden" name="priceId" value="$priceId$">
                        <input type="hidden" name="authKey" value="$authKey:val$">
                        <!-- HIDDEN_PARAM END -->
                        <input class="submit" type="submit" name="submit" value="確認">
                    </form>
                    <!-- SMP_TEMPLATE_FORM end -->
                </div>
            </div>
        </div>
        <script>
            $(function () {
                $("form").addClass(
                    "uk-form-horizontal uk-margin-large uk-text-center uk-margin-remove"
                );

                $("table").addClass("uk-table");

                $("div.smp_tmpl").addClass("uk-text-left");

                $("input[type='text']").addClass("uk-input uk-width-expand");

                $("input[type='password']").addClass("uk-input uk-width-expand");
                $("select").addClass("uk-select");
                $("input[type='checkbox']").addClass("uk-checkbox");
                $("input[type='checkbox']").addClass("uk-margin-small-right");

                $("input[type='submit']").addClass(
                    "uk-button uk-button-primary uk-margin-large-right uk-margin-large-left"
                );
                $("input[type='reset']").addClass("uk-button uk-button-default");
                $("input[name='SMPFORM_BACK']").removeClass("uk-button-primary");
                $("input[name='SMPFORM_BACK']").addClass("uk-button-default");
                $("input").css('text-align', 'left');
                $("textarea").addClass("uk-textarea");

                $(".title").addClass("uk-form-label uk-margin-remove-top");
                $(".data").addClass("uk-form-controls");

                $(".header").addClass("uk-text-large");
                $(".need").addClass("uk-label uk-label-danger");

                $(".sample").addClass("uk-text-meta uk-text-small");
                $(".suffix").addClass("uk-text-meta uk-text-small uk-width-1-5");
                $(".caution").addClass("uk-text-meta uk-text-small");
                $(".caution").before("<br>");

                $("ul").addClass("uk-list uk-margin-remove");

                $(".error").addClass("uk-form-danger");
                $(".msg").addClass("uk-text-danger");
                $(".header_emesg").addClass("uk-alert-danger uk-alert");

                $("#mainPage").show();

            });
        </script>
    </body>
</html>