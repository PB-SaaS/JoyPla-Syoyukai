<?php
//<!-- SMP_DYNAMIC_PAGE DISPLAY_ERRORS=OFF NAME=formdesign -->
?>

<!DOCTYPE html>
<html>
    <head>
        <title>JoyPla 商品情報変更 - 入力</title>
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
                    <h1>商品情報変更 - 入力</h1>

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
                                    商品名
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:itemName$"
                                        type="text"
                                        name="itemName"
                                        value="$itemName$"
                                        maxlength="128">
                                    <br>
                                    <span class="msg">$error:itemName$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    分類
                                </dt>
                                <dd class="data ">

                                    <select class="$errorInputColor:category$" name="category">
                                        <option value="">----- 選択してください -----</option>
                                        <option value="1" $category:1$="$category:1$">医療材料</option>
                                        <option value="2" $category:2$="$category:2$">薬剤</option>
                                        <option value="3" $category:3$="$category:3$">試薬</option>
                                        <option value="4" $category:4$="$category:4$">日用品</option>
                                        <option value="99" $category:99$="$category:99$">その他</option>
                                    </select>
                                    <br>
                                    <span class="msg">$error:category$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    製品コード
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:itemCode$"
                                        type="text"
                                        name="itemCode"
                                        value="$itemCode$"
                                        maxlength="128">
                                    <br>
                                    <span class="msg">$error:itemCode$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    規格
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:itemStandard$"
                                        type="text"
                                        name="itemStandard"
                                        value="$itemStandard$"
                                        maxlength="128">
                                    <br>
                                    <span class="msg">$error:itemStandard$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    JANコード
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:itemJANCode$"
                                        type="text"
                                        name="itemJANCode"
                                        value="$itemJANCode$"
                                        maxlength="128">
                                    <br>
                                    <span class="msg">$error:itemJANCode$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    メーカー名
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:makerName$"
                                        type="text"
                                        name="makerName"
                                        value="$makerName$"
                                        maxlength="128">
                                    <br>
                                    <span class="msg">$error:makerName$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    カタログNo
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:catalogNo$"
                                        type="text"
                                        name="catalogNo"
                                        value="$catalogNo$"
                                        maxlength="128">
                                    <br>
                                    <span class="msg">$error:catalogNo$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    シリアルNo
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:serialNo$"
                                        type="text"
                                        name="serialNo"
                                        value="$serialNo$"
                                        maxlength="128">
                                    <br>
                                    <span class="msg">$error:serialNo$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    ロット管理フラグ
                                </dt>
                                <dd class="data multi2">
                                    <ul class="cf">
                                        <li>
                                            <label><input
                                                class="input"
                                                type="checkbox"
                                                name="lotManagement"
                                                value="1"
                                                $lotManagement$>
                                                <span>はい</span></label>
                                        </li>
                                    </ul>
                                    <input type="hidden" value="" name="lotManagement">
                                    <span class="msg">$error:lotManagement$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    償還価格フラグ
                                </dt>
                                <dd class="data multi2">
                                    <ul class="cf">
                                        <li>
                                            <label><input
                                                class="input"
                                                type="checkbox"
                                                name="officialFlag"
                                                value="1"
                                                $officialFlag$>
                                                <span>はい</span></label>
                                        </li>
                                    </ul>
                                    <input type="hidden" value="" name="officialFlag">
                                    <span class="msg">$error:officialFlag$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    償還価格
                                </dt>
                                <dd class="data real">

                                    <input
                                        class="input $errorInputColor:officialprice$"
                                        type="text"
                                        name="officialprice"
                                        value="$officialprice$"
                                        maxlength="20"
                                        style="text-align: right;">
                                    <br>
                                    <span class="msg">$error:officialprice$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    旧償還価格
                                </dt>
                                <dd class="data real">

                                    <input
                                        class="input $errorInputColor:officialpriceOld$"
                                        type="text"
                                        name="officialpriceOld"
                                        value="$officialpriceOld$"
                                        maxlength="20"
                                        style="text-align: right;">
                                    <br>
                                    <span class="msg">$error:officialpriceOld$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    入数
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data integer">

                                    <input
                                        class="input $errorInputColor:quantity$"
                                        type="text"
                                        name="quantity"
                                        value="$quantity$"
                                        maxlength="10"
                                        style="text-align: right;">
                                    <br>
                                    <span class="msg">$error:quantity$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    入数単位
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:quantityUnit$"
                                        type="text"
                                        name="quantityUnit"
                                        value="$quantityUnit$"
                                        maxlength="32">
                                    <br>
                                    <span class="msg">$error:quantityUnit$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    個数単位
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:itemUnit$"
                                        type="text"
                                        name="itemUnit"
                                        value="$itemUnit$"
                                        maxlength="32">
                                    <br>
                                    <span class="msg">$error:itemUnit$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    定価
                                </dt>
                                <dd class="data real">

                                    <input
                                        class="input $errorInputColor:minPrice$"
                                        type="text"
                                        name="minPrice"
                                        value="$minPrice$"
                                        maxlength="20"
                                        style="text-align: right;">
                                    <br>
                                    <span class="msg">$error:minPrice$</span>
                                </dd>
                            </dl>
                        </div>
                        <input type="hidden" name="detect" value="判定">
                        <!-- HIDDEN_PARAM START -->
                        $form:hidden$<input type="hidden" name="itemsAuthKey" value="$itemsAuthKey:val$">
                        <input type="hidden" name="itemId" value="$itemId$">
                        <input type="hidden" name="tenantId" value="$tenantId$">
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

                $("form ul").addClass("uk-list uk-margin-remove");

                $(".error").addClass("uk-form-danger");
                $(".msg").addClass("uk-text-danger");
                $(".header_emesg").addClass("uk-alert-danger uk-alert");

                $("#mainPage").show();

            });
        </script>
    </body>
</html>