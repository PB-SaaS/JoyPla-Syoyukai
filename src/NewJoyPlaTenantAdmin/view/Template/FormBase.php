<!DOCTYPE html>
<html lang="ja"> 
<head>
    <title><?php echo $title; ?></title>
    <?php echo $head ?>
    <?php echo $style; ?>
    <?php echo $script; ?>
</head>
<body>
    <div>
        <?php echo $header ?>
    </div>
    <div id="content">
        <?php echo $content; ?>
    </div>
    <footer>
        <?php echo $footer; ?>
    </footer>
    <?php echo $before_script; ?>
</body>
</html>