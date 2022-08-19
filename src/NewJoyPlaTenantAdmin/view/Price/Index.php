<div class="uk-section uk-section-default uk-preserve-color animsition uk-padding-remove">
    <div class="uk-container uk-container-expand">
    	<h1>金額管理</h1>
		<div class="uk-margin spiral_table_area" style="display:none">
            <div>
                <button class="uk-button uk-button-primary" type="button" name="Action" uk-toggle="target: #price-select-bulk-update">選択した情報を一括更新する</button>
            </div>
            <!-- This is the modal -->
            <div id="price-select-bulk-update" uk-modal>
                <div class="uk-modal-dialog uk-modal-body">
                    <h2 class="uk-modal-title">選択した情報の一括更新</h2>
                    <dl class="cf">
                        <dt class="title">
                            単価
                        </dt>
                        <dd class="data real">
                            <input
                                class="uk-input"
                                type="number"
                                name="unitPrice"
                                value=""
                                maxlength="20">
                            <br>
                        </dd>
                    </dl>
                    <dl class="cf">
                        <dt class="title">
                            購買価格
                        </dt>
                        <dd class="data real">
                            <input
                                class="uk-input"
                                type="number"
                                name="price"
                                value=""
                                maxlength="20">
                            <br>
                        </dd>
                    </dl>
                    <p class="uk-text-right">
                        <button class="uk-button uk-button-primary" type="button" onclick="update()">更新</button>
                    </p>
                </div>
            </div>

            <script>
                function update() {
                    let elems = $('input[id^=smp-table-check-]:checked');

                    if( elems.length === 0)
                    {
                        return UIkit.modal.alert("情報を選択してください");
                    }

                    let ids = [];
                    elems.each(function(index, element){ 
                        ids.push(element.value);
                    });

                    UIkit.modal.confirm(elems.length + "件の更新を行います。よろしいですか").then(function () {
                        
                        loading();
                        $.ajax({
                            async: false,
                            url: "<?php echo $price_api_url ?>",
                            type:'POST',
                            data:{
                                _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                                Action : "bulkPriceUpdate",
                                ids : JSON.stringify(ids),
                                unitPrice : $('input[name="unitPrice"]')[0].value,
                                price : $('input[name="price"]')[0].value,
                            },
                            dataType: 'json'
                        })
                        // Ajaxリクエストが成功した時発動
                        .done( (data) => {
                            if(data.code != 0)
                            {
                                UIkit.modal.alert('更新に失敗しました');
                            }
                            UIkit.modal.alert('更新しました。').then(function(){
                                location.reload();
                            });
                        })
                        // Ajaxリクエストが失敗した時発動
                        .fail( (data) => {
                            UIkit.modal.alert('更新に失敗しました');
                        })
                        // Ajaxリクエストが成功・失敗どちらでも発動
                        .always( (data) => {
                            loading_remove();
                        });

                    }, function () {
                    });
                }
            </script>
			%sf:usr:search29%
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
