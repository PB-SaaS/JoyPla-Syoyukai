<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>JoyPla
            <?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">
        <link rel="stylesheet" href="https://i02.smp.ne.jp/u/joypla/new/css/normalize.css" />
        <script src="https://unpkg.com/vue@3.2.36"></script>
        <script src="https://cdn.jsdelivr.net/npm/bwip-js@3.1.0/dist/bwip-js.min.js"></script>
        
        <script>
        const _CSRF = "<?php echo Csrf::generate(16) ?>";
        const _APIURL = "%url/rel:mpgt:ApiRoot%";
        const _ROOT = "%url/rel:mpgt:Root%";
        </script>
        <style>
            @page {
                size: A4;
            }
            * {
                margin: 0;
                padding: 0;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
            @page {
                margin: 0;
            }
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            body .paper > div {
                page-break-after: always;
            }
            .paper.A3 > div {
                width: 297mm;
                height: 418mm;
            }
            .paper.A3.landscape > div {
                width: 420mm;
                height: 295mm;
            }
            .paper.A4 > div {
                width: 210mm;
                height: 295mm;
            }
            .paper.A4.landscape > div {
                width: 297mm;
                height: 208mm;
            }
            .paper.A5 > div {
                width: 148mm;
                height: 208mm;
            }
            .paper.A5.landscape > div {
                width: 210mm;
                height: 146mm;
            }
            .paper.letter > div {
                width: 216mm;
                height: 278mm;
            }
            .paper.letter.landscape > div {
                width: 280mm;
                height: 214mm;
            }
            .paper.legal > div {
                width: 216mm;
                height: 355mm;
            }
            .paper.legal.landscape > div {
                width: 357mm;
                height: 214mm;
            }
            @media screen {
                body {
                    background-color: #ccc;
                }
                body .paper > div {
                    display: block;
                    margin: 0 auto;
                    background-color: #fff;
                    box-shadow: 0 0.5mm 2mm rgba(0,0,0,0.3);
                    margin-top: 5mm;
                }
            }
        </style>
        <style>
        <?php 
        require_once "JoyPla/resources/parts/output.css.php"; 
        ?>
        
        [v-cloak] {
        display: none;
        }
        </style>
    </head>
    <body>
        <?php echo $body ?>
    </body>
</html>