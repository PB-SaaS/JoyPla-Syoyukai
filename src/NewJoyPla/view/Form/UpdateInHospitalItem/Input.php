<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>JoyPla 院内商品情報変更 - 入力</title>
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
                    <h1>院内商品情報変更 - 入力</h1>

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
                                    <ul class="cf">
                                        <li>
                                            <label><input
                                                class="input"
                                                type="checkbox"
                                                name="notUsedFlag"
                                                value="1"
                                                $notUsedFlag$="$notUsedFlag$">
                                                <span>不使用</span></label>
                                        </li>
                                    </ul>
                                    <input type="hidden" value="" name="notUsedFlag">
                                    <span class="msg">$error:notUsedFlag$</span>
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
                                    <textarea
                                        class="$errorInputColor:medicineCategory$"
                                        name="medicineCategory"
                                        rows="4"
                                        wrap="soft">$medicineCategory$</textarea><br>
                                    <span class="msg">$error:medicineCategory$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    保険請求分類（在宅）
                                </dt>
                                <dd class="data ">
                                    <textarea
                                        class="$errorInputColor:homeCategory$"
                                        name="homeCategory"
                                        rows="4"
                                        wrap="soft">$homeCategory$</textarea><br>
                                    <span class="msg">$error:homeCategory$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    測定機器名
                                </dt>
                                <dd class="data ">
                                    <input
                                        class="input $errorInputColor:measuringInst$"
                                        type="text"
                                        name="measuringInst"
                                        value="$measuringInst$"
                                        maxlength="128"><br>
                                    <span class="msg">$error:measuringInst$</span>
                                </dd>
                            </dl>
                            <p>現在の登録情報</p>
                            <dl class="cf">
                                <dt class="title">
                                    入数
                                </dt>
                                <dd class="data integer">
                                    $quantity$ $quantityUnit$
                                    <br>
                                    <span class="msg">$error:quantity$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    個数単位
                                </dt>
                                <dd class="data ">
                                    $itemUnit$
                                    <br>
                                    <span class="msg">$error:itemUnit$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    購買価格
                                </dt>
                                <dd class="data real">￥<script>
                                        price("$price$")
                                    </script>
                                </dd>
                            </dl>
                            <p>価格データ</p>
                            <dl class="cf">
                                <dt class="title">
                                    採用価格
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data ">
                                    <div div="div" class="uk-child-width-1-1@m" uk-grid="uk-grid">
                                        <?php
                                         foreach($price_data as $key => $record){
                                          $checked = "";
                                          if($current_price == $record->priceId){
                                           $checked = "checked";
                                          }
                                         ?>
                                        <div>
                                            <label>
                                                <div class="uk-card uk-card-default uk-card-small uk-card-hover">
                                                    <div class="uk-card-body selectPrice" style="cursor: pointer">
                                                        <div class="uk-text-right uk-float-right"><input
                                                            type="radio"
                                                            class="uk-radio"
                                                            name="priceId"
                                                            value="<?= $record->priceId ?>"
                                                            <?= $checked ?>></div>
                                                        <span>卸業者：<?= $record->distributorName ?></span><br>
                                                        <span>入数：<?= $record->quantity ?><?= $record->quantityUnit ?>
                                                            /
                                                            <?= $record->itemUnit ?></span><br>
                                                        <span>単価：￥<script>
                                                                price("<?= $price->unitPrice ?>")
                                                            </script></span><br>
                                                        <span>購買価格：￥<script>
                                                                price("<?= $record->price ?>")
                                                            </script>
                                                            /
                                                            <?= $record->itemUnit ?></span><br>
                                                        <p>特記事項:<br><?= nl2br($record->notice) ?></p>
                                                        <input type="hidden" name="distQuantity" value="<?= $record->quantity ?>">
                                                        <input type="hidden" name="distPrice" value="<?= $record->price ?>">
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                    </div><br>
                                    <span class="msg">$error:priceId$</span>
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
                        $form:hidden$<input type="hidden" name="makerName" value="$makerName$">
                        <input type="hidden" name="itemName" value="$itemName$">
                        <input type="hidden" name="itemCode" value="$itemCode$">
                        <input type="hidden" name="itemStandard" value="$itemStandard$">
                        <input type="hidden" name="itemJANCode" value="$itemJANCode$">
                        <input type="hidden" name="catalogNo" value="$catalogNo$">
                        <input type="hidden" name="serialNo" value="$serialNo$">
                        <input type="hidden" name="quantity" value="$quantity$">
                        <input type="hidden" name="quantityUnit" value="$quantityUnit$">
                        <input type="hidden" name="itemUnit" value="$itemUnit$">
                        <input type="hidden" name="priceId" value="$priceId$">
                        <input type="hidden" name="distributorId" value="$distributorId$">
                        <input type="hidden" name="hospitalId" value="$hospitalId$">
                        <input type="hidden" name="itemId" value="$itemId$">
                        <input type="hidden" name="price" value="$price$">
                        <input type="hidden" name="inHospitalItemId" value="$inHospitalItemId$">
                        <input type="hidden" name="authKey" value="$authKey:val$">
                        <input type="hidden" name="oldPrice" value="<?php echo $oldPrice ?>">
                        <input type="hidden" name="oldUnitPrice" value="<?php echo $oldUnitPrice ?>">
                        <!-- HIDDEN_PARAM END -->
                        <input class="submit" type="submit" name="submit" value="確認">
                    </form>
                    <!-- SMP_TEMPLATE_FORM end -->
                </div>
            </div>
        </div>
        <script>
        $(function(){
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
            $(document).on("change", "input[name='priceId']", function() {
                let price = parseInt(
                    $(this).parents(".selectPrice").find("input[name='distPrice']").val()
                );
                let quantity = parseInt(
                    $(this).parents(".selectPrice").find("input[name='distQuantity']").val()
                );
                let val = Math.round(price / quantity);
                $(this)
                    .parents("dl")
                    .next("dl")
                    .find("input[name='unitPrice']")
                    .val(val);
            });
        </script>
    </body>
</html>