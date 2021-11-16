<div class="" uk-height-viewport="expand: true">
    <iframe id="iframeForm" name="iframeForm" frameborder="0" style="overflow: hidden;
            width: 100%; position: absolute;"></iframe>
</div>
<form action="<?php echo $url ?>" target="iframeForm" id="iframe_form">
    %SMPAREA%
    <input type="hidden" value="%url/rel:mpg:top%" name="topPageLink">
    <?php 
    foreach( $hiddens as $name => $val )
    {
        echo "<input type='hidden' name='".$name."' value='".$val."'  >".PHP_EOL;  
    }
    ?>
</form>
<script>
$('form#iframe_form').submit();
const sub = document.getElementById("iframeForm");
function resize() {
  sub.style.height = sub.contentWindow.document.body.scrollHeight + "px";
  $('#content').animate({ scrollTop: 0 }, 0);
}
</script>