<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>JoyPla 利用規約同意</title>
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
                    <h1>利用規約同意</h1>

                    <!--SMP:DISP:REG:START-->
                    <p class="header_rmesg">規約をご確認の上、送信ボタンを押してください。</p>
                    <!--SMP:DISP:REG:END-->

                    <!--SMP:DISP:ERR:START-->
                    <p class="header_emesg">ご入力内容に不備がございます。エラーが発生した項目を修正し、送信しなおしてください。</p>
                    <!--SMP:DISP:ERR:END-->
                    <!-- SMP_TEMPLATE_HEADER end -->
                    <!-- SMP_TEMPLATE_FORM start -->
                    <form method="post" action="/regist/Reg2">
                        <div class="smp_tmpl">
                            <div class="uk-margin-auto">
                                <div class="uk-background-muted uk-padding">
                                    <?php echo $agreementText ?>
                                </div>
                            </div>
                            <dl class="cf">
                                <dt class="title">
                                    利用規約同意
                                    <span class="need">必須</span>
                                </dt>
                                <dd class="data  uk-grid-small uk-child-width-auto">
                                    <label><input
                                        class="uk-checkbox $errorInputColor:termsAgreement$"
                                        type="checkbox"
                                        value="2"
                                        name="termsAgreement">
                                        同意します</label>
                                    <br>
                                    <span class="msg">$error:termsAgreement$</span>
                                </dd>
                            </dl>
                        </div>
                        <input type="hidden" name="detect" value="判定">
                        <!-- HIDDEN_PARAM START -->
                        $form:hidden$<input type="hidden" name="authKey" value="$authKey:val$">
                        <input type="hidden" name="loginId" value="$loginId$">
                        <!-- HIDDEN_PARAM END -->
                        <input class="submit" type="submit" name="submit" value="送信">
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

                $("form ul").addClass("uk-list");

                $(".error").addClass("uk-form-danger");
                $(".msg").addClass("uk-text-danger");
                $(".header_emesg").addClass("uk-alert-danger uk-alert");

                $("#mainPage").show();

            });
        </script>
    </body>
</html>