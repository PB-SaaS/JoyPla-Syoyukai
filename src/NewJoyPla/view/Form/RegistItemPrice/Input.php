<?php
//<!-- SMP_DYNAMIC_PAGE DISPLAY_ERRORS=OFF NAME=formdesign -->
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>JoyPla 金額情報登録 - 入力</title>
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
                    <h1>金額情報登録 - 入力</h1>

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
                                    卸業者
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data ">
                                    <select
                                        name="distributorId"
                                        id="distributorId"
                                        class="uk-select $errorInputColor:distributorId$"
                                        >
                                        <option value="">
                                            --- 選択してください ---
                                        </option>
                                        <?php
                                        foreach($distributor as $dist)
                                        {
                                            $selected = "";
                                            if($dist->distributorId == $currentDistributorId){
                                                $selected = "selected";
                                            }
                                            echo "<option value='".$dist->distributorId."' ".$selected.">".$dist->distributorName."</option>";
                                        }
                                        ?>
                                    </select>
                                    <br>
                                    <span class="msg">$error:distributorId$</span>
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
                                </dt>
                                <dd class="data ">
                                    $quantityUnit$
                                    <br>
                                    <span class="msg">$error:quantityUnit$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    個数単位
                                    <span class="need">必須</span>
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
                                    購買価格
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data real">

                                    <input
                                        class="input $errorInputColor:price$"
                                        type="text"
                                        name="price"
                                        value="$price$"
                                        maxlength="20"
                                        style="text-align: right;">
                                    <br>
                                    <span class="msg">$error:price$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    単価
                                </dt>
                                <dd class="data real">

                                    <input
                                        class="input $errorInputColor:unitPrice$"
                                        type="text"
                                        name="unitPrice"
                                        value="$unitPrice$"
                                        maxlength="20"
                                        style="text-align: right;">
                                    <br>
                                    <button type="button" onclick="getUnitPrice()" class="uk-button uk-button-default">単価を自動計算</button>
                                    <script>
                                        function getUnitPrice()
                                        {
                                            let price = $('input[name=price]')[0].value;
                                            let quantity = $('input[name=quantity]')[0].value;

                                            let unitPrice = 0;
                                            if( price == "" || price == 0 ){ unitPrice = 0 }
                                            if( quantity == "" || quantity == 0 ){ unitPrice = 0 }
                                            unitPrice = ( price / quantity );

                                            $('input[name=unitPrice]')[0].value = unitPrice ;
                                        }
                                    </script>
                                    <br>
                                    <span class="msg">$error:unitPrice$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    特記事項
                                </dt>
                                <dd class="data ">
                                    <textarea class="$errorInputColor:notice$" name="notice" rows="4" wrap="soft">$notice$</textarea><br>
                                    <span class="msg">$error:notice$</span>
                                </dd>
                            </dl>
                        </div>
                        <input type="hidden" name="detect" value="判定">
                        <!-- HIDDEN_PARAM START -->
                        $form:hidden$<input type="hidden" name="quantityUnit" value="$quantityUnit$">
                        <input type="hidden" name="itemId" value="$itemId$">
                        <input type="hidden" name="hospitalId" value="$hospitalId$">
                        <input type="hidden" name="requestFlg" value="1">
                        <input type="hidden" name="distributorName" value="">
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