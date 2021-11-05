<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <?php echo $head ?>
    <?php echo $style; ?>
    <?php echo $script; ?>
</head>
<body>
    <div>
        <!-- header.phpファイルを読み込む-->
        <?php echo $header ?>
    </div>
    <div id="content">
        <!-- 各アクションの内容を読み込む-->
        <?php echo $content; ?>
    </div>
    <footer>
        <!-- footer.phpファイルを読み込む-->
        <?php echo $footer; ?>
    </footer>
    <?php echo $before_script; ?>
</body>
</html>