<!DOCTYPE html>
<html lang="ja">
    <head>
        <?php include_once 'NewJoyPla/src/Head.php'; ?>
        <title>JoyPla 病院ユーザー情報変更 - 入力</title>

        <?php include_once 'NewJoyPla/view/template/parts/Script.php'; ?>
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
                    <h1>病院ユーザー情報変更 - 入力</h1>

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
                                    部署
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data " id="division">
                                    <searchable-select name="divisionId" :error="'$errorInputColor:divisionId$' == 'error'" v-model="divisionId" :default="divisionId" :options="divisionOptions"></searchable-select>
                                    <br>
                                    <span class="msg">$error:divisionId$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    ユーザー権限
                                </dt>
                                <dd class="data ">

                                    <select class="$errorInputColor:userPermission$" name="userPermission">
                                        <option value="">----- 選択してください -----</option>
                                        <option value="1" $userPermission:1$>管理者</option>
                                        <option value="2" $userPermission:2$>担当者</option>
                                        <option value="3" $userPermission:3$>承認者</option>
                                    </select>
                                    <br>
                                    <span class="msg">$error:userPermission$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    ログインID
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:loginId$"
                                        type="text"
                                        name="loginId"
                                        value="$loginId$"
                                        maxlength="32">
                                    <br>
                                    <span class="msg">$error:loginId$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    氏名
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:name$"
                                        type="text"
                                        name="name"
                                        value="$name$"
                                        maxlength="64">
                                    <br>
                                    <span class="msg">$error:name$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    氏名（カナ）
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:nameKana$"
                                        type="text"
                                        name="nameKana"
                                        value="$nameKana$"
                                        maxlength="64">
                                    <br>
                                    <span class="msg">$error:nameKana$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    メールアドレス
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data ">

                                    <input
                                        class="input $errorInputColor:mailAddress$"
                                        type="text"
                                        name="mailAddress"
                                        value="$mailAddress$"
                                        maxlength="129"><br>
                                    （確認用）<br>
                                    <input
                                        class="input $errorInputColor:mailAddress$"
                                        type="text"
                                        name="mailAddress:cf"
                                        value="$mailAddress:cf$"
                                        maxlength="129"><br>

                                    <span class="msg">$error:mailAddress$</span>
                                </dd>
                            </dl>
                            <dl class="cf">
                                <dt class="title">
                                    備考
                                </dt>
                                <dd class="data ">
                                    <textarea class="$errorInputColor:remarks$" name="remarks" rows="4" wrap="soft">$remarks$</textarea><br>
                                    <span class="msg">$error:remarks$</span>
                                </dd>
                            </dl>
                        </div>
                        <input type="hidden" name="detect" value="判定">
                        <!-- HIDDEN_PARAM START -->
                        $form:hidden$<input type="hidden" name="id" value="$id$">
                        <input type="hidden" name="authKey" value="$authKey:val$">
                        <input type="hidden" name="divisionName" value="">
                        <!-- HIDDEN_PARAM END -->
                        <input class="submit" type="submit" name="submit" value="確認">
                    </form>
                    <!-- SMP_TEMPLATE_FORM end -->
                </div>
            </div>
        </div>
        <script>
            <?php
            $divisionOptions = [
                [
                    'value' => '',
                    'text' => '----- 部署を選択してください -----',
                ],
            ];
            foreach ($division_data as $data) {
                if ($data->divisionType === '1') {
                    $divisionOptions[] = [
                        'value' => $data->divisionId,
                        'text' => $data->divisionName,
                    ];
                }
            }
            foreach ($division_data as $data) {
                if ($data->divisionType === '2') {
                    $divisionOptions[] = [
                        'value' => $data->divisionId,
                        'text' => $data->divisionName,
                    ];
                }
            }
            ?>
            var division = new Vue({
                el: '#division',
                data: {
                    divisionOptions: <?php echo json_encode(
                        $divisionOptions
                    ); ?>,
                    divisionId: "<?php echo $current_division; ?>",
                },
            });
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

                $("ul").addClass("uk-list");

                $(".error").addClass("uk-form-danger");
                $(".msg").addClass("uk-text-danger");
                $(".header_emesg").addClass("uk-alert-danger uk-alert");

                $("#mainPage").show();
            });
        </script>
    </body>
</html>