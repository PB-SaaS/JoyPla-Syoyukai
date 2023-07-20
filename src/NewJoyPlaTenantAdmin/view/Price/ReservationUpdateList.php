<div class="uk-section uk-section-default uk-preserve-color animsition uk-padding-remove">
    <div class="uk-container uk-container-expand">
    	<h1>金額情報予約一覧</h1>
			%sf:usr:search70%
		</div>
    </div>
</div>
<script>
$(document).ready(function () {
    loading();
    setTimeout(function () {
        loading_remove()
    }, 1000);
});
function loading() {
    if ($("#loading").length == 0) {
        $(".animsition").before(
            '<div style="z-index: 1;position: fixed;" id="loading" class="uk-position-cover' +
            ' uk-overlay uk-overlay-default uk-flex uk-flex-center uk-flex-middle"><span uk' +
            '-spinner="ratio: 4.5" class="uk-icon uk-spinner"></span></div>'
        );
    }
}

function loading_remove() {
    if ($("#loading").length != 0) {
        $('.animsition').css({opacity: "1"});
        $('#loading').remove();
    }
}
</script>
