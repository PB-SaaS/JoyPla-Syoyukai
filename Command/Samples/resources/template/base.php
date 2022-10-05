<?php
echo '<!DOCTYPE html>
<html lang="ja">
<head>
    <title><?php echo $title; ?></title>
    <script defer src="https://unpkg.com/alpinejs@3.10.3/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $head ?>
    <?php echo $style; ?>
    <script>
        const _CSRF = "<?php echo Csrf::generate(16) ?>";
        const _APIURL = "%url/rel:mpgt:xxxx%";
        const _ROOT = "%url/rel:mpgt:xxxx%";
    </script>
    <?php echo $script; ?>
</head>
<body>
    <?php echo $body ?>
</body>
</html>
';