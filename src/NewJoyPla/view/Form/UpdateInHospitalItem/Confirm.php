<!DOCTYPE html>
<html lang="ja">
    <head>
        <?php include_once "NewJoyPla/src/Head.php"; ?>
        <title>JoyPla 院内商品情報変更 - 確認</title>

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
                    <h1>院内商品情報変更 - 確認</h1>

                    <p class="header_text">変更内容をご確認の上、変更ボタンをクリックしてください。
                    </p>
                    <p class="uk-alert-danger uk-alert">内容を修正する場合は、戻るボタンをクリックしてください。</p><br>
                    <!-- SMP_TEMPLATE_HEADER end -->
                    <!-- SMP_TEMPLATE_FORM start -->
                    <form method="post" action="/regist/Reg2">
                        <input type="hidden" name="confirm" value="true">
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
                                    メーカー名
                                </dt>
                                <dd class="data ">
                                    $makerName$
                                    <br>
                                    <span class="msg">$error:makerName$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    商品名
                                </dt>
                                <dd class="data ">
                                    $itemName$
                                    <br>
                                    <span class="msg">$error:itemName$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    製品コード
                                </dt>
                                <dd class="data ">
                                    $itemCode$
                                    <br>
                                    <span class="msg">$error:itemCode$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    規格
                                </dt>
                                <dd class="data ">
                                    $itemStandard$
                                    <br>
                                    <span class="msg">$error:itemStandard$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    JANコード
                                </dt>
                                <dd class="data ">
                                    $itemJANCode$
                                    <br>
                                    <span class="msg">$error:itemJANCode$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    カタログNo
                                </dt>
                                <dd class="data ">
                                    $catalogNo$
                                    <br>
                                    <span class="msg">$error:catalogNo$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    シリアルNo
                                </dt>
                                <dd class="data ">
                                    $serialNo$
                                    <br>
                                    <span class="msg">$error:serialNo$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    保険請求分類（医科）
                                </dt>
                                <dd class="data ">
                                    $medicineCategory:br$
                                    <br>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    保険請求分類（在宅）
                                </dt>
                                <dd class="data ">
                                    $homeCategory:br$
                                    <br>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    測定機器名
                                </dt>
                                <dd class="data ">
                                    $measuringInst$
                                    <br>
                                </dd>
                            </dl>

                            <dl class="cf">
                                <dt class="title">
                                    採用価格
                                </dt>
                                <dd class="data ">
                                    <div div="div" class="uk-child-width-1-1@m" uk-grid="uk-grid">
                                        <div>
                                            <label>
                                                <div class="uk-card uk-card-default uk-card-small uk-card-hover">
                                                    <div class="uk-card-body" style="cursor: pointer">
                                                        <span>卸業者：<?= $price->distributorName ?></span><br>
                                                        <span>入数：<?= $price->quantity ?><?= $price->quantityUnit ?>
                                                            /
                                                            <?= $price->itemUnit ?></span><br>
                                                        <span>購買価格：￥<script>
                                                                price("<?= $price->price ?>")
                                                            </script>
                                                            /
                                                            <?= $price->itemUnit ?></span><br>
                                                        <p>特記事項:<br><?= nl2br($price->notice) ?></p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    単価
                                </dt>
                                <dd class="data ">
                                    $unitPrice$
                                    <br>
                                    <span class="msg">$error:unitPrice$</span>
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
                        <input type="hidden" name="ticket" value="<?php echo $ticket; ?>">
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