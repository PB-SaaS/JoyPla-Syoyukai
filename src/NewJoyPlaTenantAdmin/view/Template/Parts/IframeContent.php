<div  uk-height-viewport="expand: true">
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

$(function(){
    $('#iframeForm').on('load', function(){
        $('#iframeForm').contents().find('head').append('<style>        /*     * Primary     */    .uk-button-primary {      background-color: #7AAE36;      color: #fff;      border: 1px solid transparent;    }    /* Hover + Focus */    .uk-button-primary:hover,    .uk-button-primary:focus {      background-color: #93BD5B;      color: #fff;    }    .uk-button-primary:disabled:hover,    .uk-button-primary:disabled:focus {      background-color: transparent;      color: #999;    }    /* OnClick + Active */    .uk-button-primary:active,    .uk-button-primary.uk-active {      background-color: #B2D08B;      color: #fff;    }        .uk-tab > .uk-active > a {        color: #333;        border-color: #7AAE36;    }    </style>        ');
    });
});
</script>