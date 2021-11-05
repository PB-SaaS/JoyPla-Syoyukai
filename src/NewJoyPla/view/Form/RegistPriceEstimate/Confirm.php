<?php
//<!-- SMP_DYNAMIC_PAGE DISPLAY_ERRORS=OFF NAME=formdesign -->
?>

<!DOCTYPE html>
<html>
    <head>
        <title>JoyPla 見積情報登録 - 確認</title>
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
                    <h1>見積金額登録 - 確認</h1>
                    <!-- SMP_TEMPLATE_HEADER end -->
                    <!-- SMP_TEMPLATE_FORM start -->
                    <form method="post" action="/regist/Reg2">
                        <input type="hidden" name="confirm" value="true">
                        <div class="smp_tmpl">
                            <dl class="cf">
                                <dt class="title">
                                    商品ID
                                </dt>
                                <dd class="data ">
                                    $itemId$
                                    <br>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    メーカー
                                </dt>
                                <dd class="data ">
                                    <?= $SPIRAL->getParam('makerName'); ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    分類
                                </dt>
                                <dd class="data ">
                                    <?= $SPIRAL->getParam('category'); ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    商品名
                                </dt>
                                <dd class="data ">
                                    <?= $SPIRAL->getParam('itemName'); ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    製品コード
                                </dt>
                                <dd class="data ">
                                    <?= $SPIRAL->getParam('itemCode'); ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    規格
                                </dt>
                                <dd class="data ">
                                    <?= $SPIRAL->getParam('itemStandard'); ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    JANコード
                                </dt>
                                <dd class="data ">
                                    <?= $SPIRAL->getParam('itemJANCode'); ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    カタログNo
                                </dt>
                                <dd class="data ">
                                    <?= $SPIRAL->getParam('catalogNo'); ?>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    シリアルNo
                                </dt>
                                <dd class="data ">
                                    <?= $SPIRAL->getParam('serialNo'); ?>
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
                        $form:hidden$
                        <!-- HIDDEN_PARAM END -->
                        <input class="submit" type="submit" name="SMPFORM_BACK" value="戻る">
                        <input class="submit" type="submit" name="submit" value="変更">
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

                $("form ul").addClass("uk-list uk-margin-remove");

                $(".error").addClass("uk-form-danger");
                $(".msg").addClass("uk-text-danger");
                $(".header_emesg").addClass("uk-alert-danger uk-alert");

                $("#mainPage").show();

            });
        </script>
    </body>
</html>