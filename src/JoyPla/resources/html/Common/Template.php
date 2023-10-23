<!DOCTYPE html>
<html lang="ja">
<head>
    <title>JoyPla <?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">
    <?php echo $head; ?>
    <?php echo $style; ?>
    <!-- <link rel="stylesheet" href="https://i02.smp.ne.jp/u/joypla/new/css/animsition.min.css"> -->
    <!-- <script type="text/javascript" src="https://i02.smp.ne.jp/u/joypla/new/js/animsition.min.js"></script> -->

    <script src="https://i02.smp.ne.jp/u/joypla/new/js/JsBarcode.all.min.js" charset="UTF-8"></script>
    <script src="https://i02.smp.ne.jp/u/joypla/new/js/BarcodeParser_20220331.js"></script>

    <script src="https://i02.smp.ne.jp/u/joypla/new/js/encoding.min.js"></script>
    
    <script src="https://i02.smp.ne.jp/u/joypla_developer/340/vue.global.prod.js"></script>
    
    <script src="https://i02.smp.ne.jp/u/joypla_developer/340/axios.min.js"></script>
    
    <script src="https://i02.smp.ne.jp/u/joypla_developer/340/vee-validate.min.js"></script>
    <script src="https://i02.smp.ne.jp/u/joypla_developer/340/vee-validate-rules.min.js" charset="UTF-8"></script>
    <script src="https://i02.smp.ne.jp/u/joypla_developer/340/vee-validate-i18n.min.js"></script>
	
    <script src="https://i02.smp.ne.jp/u/joypla_developer/340/micromodal.min.js"></script>
    <script src="https://i02.smp.ne.jp/u/joypla_developer/340/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://i02.smp.ne.jp/u/joypla_developer/340/scroll-hint.css">
    <script src="https://i02.smp.ne.jp/u/joypla_developer/340/scroll-hint.min.js"></script>
    
	<link rel="stylesheet" href="https://i02.smp.ne.jp/u/joypla/new/css/uikit.min.css" />
	<script src="https://i02.smp.ne.jp/u/joypla/new/js/uikit.min.js"></script>
	<script src="https://i02.smp.ne.jp/u/joypla/new/js/uikit-icons.min.js"></script>
    <style>
    @media print{
        .no_print{
            display: none;
        }
        .printarea{
            page-break-after: always;
            font-size: 12px;
        }
    }
    .uk-input[type="number"]{
        text-align: right;
    }
    table.uk-table-divider tr:last-of-type{
    border-bottom: 1px solid #e5e5e5;
    }
    table#tbl-Items tr td{
        padding: 12px 0px 12px 12px !important;
    }
    .uk-navbar-container{
        border-bottom : solid 2px #98CB00;
    }
    .bk-application-color{
        background : #98CB00;
    }

    table td.active{
        background-color: #AACC44 !important;
    }
    table td{
        vertical-align: middle !important;
    }
    table.uk-table td, table.uk-table th{
        font-size: 1.0em;
    }

    .uk-navbar-container{
        border-bottom : solid 2px #98CB00;
    }
    .bk-application-color{
        background : #98CB00;
    }

    .uk-table th{
        text-transform: none !important;
    }

    .resultarea{
        display:none;
    }

    /*
    * Primary
    */
    .uk-button-primary {
        background-color: #7AAE36;
        color: #fff;
        border: 1px solid transparent;
    }
    .uk-text-primary {
        color: #7AAE36 !important;
    }
    /* Hover + Focus */
    .uk-button-primary:hover,
    .uk-button-primary:focus {
        background-color: #93BD5B;
        color: #fff;
    }
    .uk-button-primary:disabled:hover,
    .uk-button-primary:disabled:focus {
        background-color: transparent;
        color: #999;
    }
    /* OnClick + Active */
    .uk-button-primary:active,
    .uk-button-primary.uk-active {
        background-color: #B2D08B;
        color: #fff;
    }
    /*
    * Success
    */
    .uk-label-success {
        background-color: #7AAE36;
        color: #fff;
    }

    .uk-table tr:last-child{
        border-bottom: 1px solid #e5e5e5;
    }

    .uk-table tr.tr-gray{
        background: #e5e5e5;
    }

    .title_spacing {
        letter-spacing : 1em;	
        text-indent: 1em;
    }
    </style>
    <script>
    // import all the rules that come with vee-validate
    Object.keys(VeeValidateRules.default).forEach(rule => {
        VeeValidate.defineRule(rule, VeeValidateRules[rule])
    })
    
    VeeValidate.configure({
        // Generates an English message locale generator
        //generateMessage: VeeValidateI18n.localize('ja'),
        generateMessage: VeeValidateI18n.localize('ja', {
            messages: {
                "alpha": "{field}はアルファベットのみ使用できます",
                "alpha_num": "{field}は英数字のみ使用できます",
                "alpha_dash": "{field}は英数字とハイフン、アンダースコアのみ使用できます",
                "alpha_spaces": "{field}はアルファベットと空白のみ使用できます",
                "between": "{field}は 0:{min} から 1:{max} の間でなければなりません",
                "confirmed": "{field}が一致しません",
                "digits": "{field}は{length}桁の数字でなければなりません",
                "dimensions": "{field}は幅 0:{width}px、高さ 1:{height}px 以内でなければなりません",
                "email": "{field}は有効なメールアドレスではありません",
                "excluded": "{field}は不正な値です",
                "ext": "{field}は有効なファイル形式ではありません",
                "image": "{field}は有効な画像形式ではありません",
                "integer": "{field}は整数のみ使用できます",
                "is": "{field}が一致しません",
                "length": "{field}は 0:{length} 文字でなければなりません",
                "max_value": "{field}は 0:{max} 以下でなければなりません",
                "max": "{field}は 0:{length} 文字以内にしてください",
                "mimes": "{field}は有効なファイル形式ではありません",
                "min_value": "{field}は 0:{min} 以上でなければなりません",
                "min": "{field}は 0:{length} 文字以上でなければなりません",
                "numeric": "{field}は数字のみ使用できます",
                "one_of": "{field}は有効な値ではありません",
                "regex": "{field}のフォーマットが正しくありません",
                "required": "{field}は必須項目です",
                "required_if": "{field}は必須項目です",
                "size": "{field}は 0:{size}KB 以内でなければなりません"
            },
        }),
    });
    </script>

    <script>
        const encodeURIToObject = (object) => {
            let result = {};
            if(object == null){
                return null;
            }
            Object.keys(object).forEach(function (key) {
                if( typeof object[key] == "object"){
                result[key] = encodeURIToObject(object[key]);
                } else {
                result[key] = encodeURI(object[key]);
                }
            });
            return result;
        };
        
        const _CSRF = "<?php echo Csrf::generate(16); ?>";
        const _APIURL = "%url/rel:mpgt:ApiRoot%";
        const _ROOT = "%url/rel:mpgt:Root%";
        console.log(_ROOT);
    </script>

    <script>
        <?php
        require_once 'JoyPla/resources/parts/component/form-components.php';
        require_once 'JoyPla/resources/parts/component/components.php';
        require_once 'JoyPla/resources/parts/component/v-loading.php';
        require_once 'JoyPla/resources/parts/component/v-breadcrumbs.php';
        require_once 'JoyPla/resources/parts/component/customVeeValidate.php';
        require_once 'JoyPla/resources/parts/component/db-select-components.php';
        ?>
    </script>

    <style>
    <?php require_once 'JoyPla/resources/parts/output.css.php'; ?>
    
    [v-cloak] {
      display: none;
    }
    </style>
    <?php echo $script; ?>
</head>
<body>
    <?php echo $body; ?>
    <?php
/*
    <div class="h-screen ">
        <v-loading :show="loading" id="joypla"></v-loading>
        <?php echo $header ?> 
        <div id="content" class="flex h-full px-1">
            <div class="flex-auto overflow-x-auto">
                <!-- 各アクションの内容を読み込む-->
                <?php echo $content; ?>
            </div>
        </div>
    </div>
    */
?>
</body>
</html>