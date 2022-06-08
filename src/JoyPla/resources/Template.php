<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $head ?>
    <?php echo $style; ?>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <style>
    <?php 
    require_once "JoyPla/resources/parts/output.css.php"; 
    ?>
    </style>
    <?php
    require_once "JoyPla/resources/parts/component/card-button.vue.php";
    require_once "JoyPla/resources/parts/component/v-loading.vue.php";
    ?>
    <?php echo $script; ?>
</head>
<body>
    <div class="h-screen" id="joypla">
        <v-loading></v-loading>
       <?php echo $header ?> 
       <div id="content" class="flex h-full pt-16">
            <div class="flex-auto">
                <!-- 各アクションの内容を読み込む-->
                <?php echo $content; ?>
            </div>
        </div>
    </div>
    <script>
        
        new Vue({
        el: '#joypla',
        })
    </script>
</body>
</html>