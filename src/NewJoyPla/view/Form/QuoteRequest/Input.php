
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
<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">
    <div class="uk-container uk-container-expand">
        <div
            class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom"
            id="mainPage">

            <!-- SMP_TEMPLATE_HEADER start -->
                <h1>見積依頼 - 入力</h1>
            	<!--SMP:DISP:REG:START-->
            	<p class="header_rmesg">必要事項をご入力の上、送信ボタンを押してください。</p>
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
                            依頼者氏名
                            <span class="need">必須</span>
                        </dt>
                        <dd class="data ">

                            <input
                                class="input $errorInputColor:requestUName$"
                                type="text"
                                name="requestUName"
                                value="$requestUName$"
                                maxlength="64">

                            <br>
                            <span class="msg">$error:requestUName$</span>
                        </dd>
                    </dl>
                    <dl class="cf">
                        <dt class="title">
                            卸業者
                            <span class="need">必須</span>
                        </dt>
                        <dd class="data " id="distributor">
                            <searchable-select name="distributorId" :error="'$errorInputColor:distributorId$' == 'error'" v-model="distributorId" :default="distributorId" :options="distributorOptions"></searchable-select>
                            <br>
                            <span class="msg">$error:distributorId$</span>
                        </dd>
                    </dl>
                    <dl class="cf">
                        <dt class="title">
                            見積期限
                            <span class="need">必須</span>
                        </dt>
                        <dd class="data time">
                            <input
                                    class="uk-input year $errorInputColor:quotePeriod$"
                                    type="datetime-local"
                                    name="quotePeriod"
                                    value="$quotePeriod$"
                                    min="<?php echo date('Y-m-d') .
                                        'T00:00'; ?>">
                            <span class="msg">$error:quotePeriod$</span>
                        </dd>
                    </dl>
                    <dl class="cf">
                        <dt class="title">
                            依頼タイトル
                            <span class="need">必須</span>
                        </dt>
                        <dd class="data ">

                            <input
                                class="input $errorInputColor:requestTitle$"
                                type="text"
                                name="requestTitle"
                                value="$requestTitle$"
                                maxlength="128">
                            <br>
                            <span class="msg">$error:requestTitle$</span>
                        </dd>
                    </dl>
                    <dl class="cf">
                        <dt class="title">
                            依頼内容
                            <span class="need">必須</span>
                        </dt>
                        <dd class="data ">
                            <textarea
                                class="$errorInputColor:requestDetail$"
                                name="requestDetail"
                                rows="4"
                                wrap="soft">$requestDetail$</textarea><br>
                            <span class="msg">$error:requestDetail$</span>
                        </dd>
                    </dl>
                </div>
                <input type="hidden" name="detect" value="判定">
                <!-- HIDDEN_PARAM START -->
                $form:hidden$<input type="hidden" name="hospitalId" value="$hospitalId$">
                <input type="hidden" name="requestUName" value="$requestUName$">
                <input type="hidden" name="mail" value="$mail$">
                <input type="hidden" name="tenantId" value="$tenantId$">
                <!-- HIDDEN_PARAM END -->
                <input class="submit" type="submit" name="submit" value="確認">
            </form>
            <!-- SMP_TEMPLATE_FORM end -->
        </div>
    </div>
</div>
<script>
    <?php
    $distributorOptions = [
        [
            'value' => '',
            'text' => '----- 卸業者を選択してください -----',
        ],
    ];

    foreach ($distributors as $data) {
        $distributorOptions[] = [
            'value' => $data->distributorId,
            'text' => $data->distributorName,
        ];
    }
    ?>
var distributor = new Vue({
    el: '#distributor',
    data: {
        distributorOptions: <?php echo json_encode($distributorOptions); ?>,
        distributorId: "<?php echo $current_distributor_Id; ?>",
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

        $(".error").addClass("uk-form-danger");
        $(".msg").addClass("uk-text-danger");
        $(".header_emesg").addClass("uk-alert-danger uk-alert");

        $("#mainPage").show();

    });
</script>