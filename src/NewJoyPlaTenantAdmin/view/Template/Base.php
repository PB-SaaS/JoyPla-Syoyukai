<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <?php echo $head ?>
    <?php echo $style; ?>
    <?php echo $script; ?>
</head>
<body>
    <div uk-grid>
        <div id="sidemenu" class="uk-width-auto">
            <!-- 各アクションの内容を読み込む-->
            <?php echo $sidemenu; ?>
        </div>
        <div id="content" class="uk-width-expand uk-padding-remove uk-panel-scrollable uk-inline" uk-height-viewport>
            <div class="joy-pla-progressbar uk-position-cover uk-position-fixed uk-overlay uk-overlay-default uk-flex uk-flex-center uk-flex-middle" style="z-index: 1;">
                <div class="uk-width-1-3 uk-margin-auto">
                    <progress id="js-progressbar" class="uk-progress" value="0" max="100"></progress>
                    <p class="uk-text-mute uk-text-center" id="progress-message"></p>
                </div>
            </div>
            <div>
                <!-- header.phpファイルを読み込む-->
                <?php echo $header ?>
            </div>
            <div>
                <?php if($back_url): ?>
                <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top">
                    <div class="uk-container uk-container-expand">
                        <div>
                            <a href="<?php echo $back_url ?>" class="uk-link-muted"><span class="uk-icon uk-margin-small-right" uk-icon="icon: chevron-left; ratio: 1.5"></span><span class="uk-vertical-center"><?php echo $back_text ?></span></a>
                            
                        </div>
                    </div>
                </div>
                <?php endif ?>
                <!-- 各アクションの内容を読み込む-->
                <?php echo $content; ?>
            </div>
            <div>
                <!-- footer.phpファイルを読み込む-->
                <?php echo $footer; ?>
            </div>
        </div>
    </div>
    <script>
    class progressbar 
    {
        constructor() {
            this.bar = document.getElementById('js-progressbar'); 
            this.msg = document.getElementById('progress-message'); 
            $('.joy-pla-progressbar').hide();
        }
        start(max , message = ""){   
            this.bar.value = 0;
            //this.bar.max = max;
            if(message){
                this.msg.textContent = message;
            }
            $('.joy-pla-progressbar').show();
        }
        end(){
            $('.joy-pla-progressbar').hide();
        }
        getVal(){
            return this.bar.value;
        }
        progress(val , message){
            this.bar.value = val;
            if(message){
                this.msg.textContent = message;
            }
            if(this.bar.value === this.bar.max){
                this.end();
            }
        }
    }
    let progress_bar = new progressbar();
    </script>
    <?php echo $before_script; ?>
</body>
</html>