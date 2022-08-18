<!DOCTYPE html>
<html lang="ja">
<head>
    <title><?php echo $title; ?></title>
    <?php echo $head ?>
    <?php echo $style; ?>
    <?php echo $script; ?>
    <link rel="stylesheet" href="https://i02.smp.ne.jp/u/joypla_developer/340/scroll-hint.css">
    <script src="https://i02.smp.ne.jp/u/joypla_developer/340/scroll-hint.min.js"></script>
    <style>
    
.absolute {
  position: absolute;
}

.relative {
  position: relative;
}

.right-0 {
  right: 0px;
}

.left-0 {
  left: 0px;
}

.top-\[80px\] {
  top: 80px;
}

.z-50 {
  z-index: 50;
}

.mt-\[15px\] {
  margin-top: 15px;
}

.box-border {
  box-sizing: border-box;
}

.block {
  display: block;
}

.hidden {
  display: none;
}

.flex-wrap {
  flex-wrap: wrap;
}

.bg-white {
  --tw-bg-opacity: 1;
  background-color: rgb(255 255 255 / var(--tw-bg-opacity));
}

.p-0 {
  padding: 0px;
}

.text-\[\#666\] {
  --tw-text-opacity: 1;
  color: rgb(102 102 102 / var(--tw-text-opacity));
}

@media (min-width: 768px) {
  .md\:right-\[20px\] {
    right: 20px;
  }

  .md\:left-auto {
    left: auto;
  }

  .md\:w-\[400px\] {
    width: 400px;
  }
}
    [v-cloak] {
      display: none;
    }
    </style>
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
    <script>
      new ScrollHint('.uk-table', {
        scrollHintIconAppendClass: 'scroll-hint-icon-white', // white-icon will appear
        applyToParents: true,
        i18n: {
          scrollable: 'スクロールできます'
        }
      });
    </script>
</body>
</html>