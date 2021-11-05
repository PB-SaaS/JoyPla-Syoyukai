
<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">
    <div class="uk-container uk-container-expand" uk-height-viewport="expand: true">
    	<?php //<ul class="uk-breadcrumb no_print uk-margin-top">
    	   // echo $breadcrumb 
		    //</ul>
    	 ?>
        <div
            class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom"
            id="mainPage">

            <?php echo $form_content; ?>
        </div>
    </div>
</div>
<script>
    $(function () {
        $("form").addClass(
            "uk-form-horizontal uk-margin-large uk-text-center uk-margin-remove"
        );

        $("table").addClass("uk-table");

        $("div.smp_tmpl").addClass("uk-text-left");

        $("input[type='text']").addClass("uk-input uk-width-expand");

        $("input[type='password']").addClass("uk-input uk-width-expand");
        $("select").addClass("uk-select");
        $("input[type='checkbox']").addClass("uk-checkbox");
        $("input[type='checkbox']").addClass("uk-margin-small-right");

        $("input[type='submit']").addClass(
            "uk-button uk-button-primary uk-margin-large-right uk-margin-large-left"
        );
        $("input[type='reset']").addClass("uk-button uk-button-default");
        $("input[name='SMPFORM_BACK']").removeClass("uk-button-primary");
        $("input[name='SMPFORM_BACK']").addClass("uk-button-default");
        $("input").css('text-align', 'left');
        $("textarea").addClass("uk-textarea");

        $(".title").addClass("uk-form-label uk-margin-remove-top");
        $(".data").addClass("uk-form-controls");

        $(".header").addClass("uk-text-large");
        $(".need").addClass("uk-label uk-label-danger");

        $(".sample").addClass("uk-text-meta uk-text-small");
        $(".suffix").addClass("uk-text-meta uk-text-small uk-width-1-5");
        $(".caution").addClass("uk-text-meta uk-text-small");
        $(".caution").before("<br>");


        $(".error").addClass("uk-form-danger");
        $(".msg").addClass("uk-text-danger");
        $(".header_emesg").addClass("uk-alert-danger uk-alert");
        $("#mainPage").show();
    });
</script>