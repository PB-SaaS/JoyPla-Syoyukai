<?php if($breadcrumb != ""): ?>
<ul class="uk-breadcrumb no_print uk-margin-top uk-padding-remove-top uk-padding uk-padding-remove-bottom">
    	   <?php echo $breadcrumb ?>
</ul>
<?php endif ?>
<div class="uk-cover-container animsition" uk-height-viewport="expand: true">
    
<iframe id="iframeForm" name="iframeForm" frameborder="0" style="overflow: hidden; height: 100%;
        width: 100%; position: absolute;"></iframe>
</div>
<form action="<?php echo $url ?>" target="iframeForm" id="iframe_form">
    %SMPAREA%
    <input type="hidden" value="%url/rel:mpg:top%" name="topPageLink">
    <?php 
    foreach( $hiddens as $name => $val )
    {
        echo '<input type="hidden" name="'.$name.'" value="'.$val.'"  >'.PHP_EOL;  
    }
    ?>
</form>
<script>
$('form#iframe_form').submit();

</script>