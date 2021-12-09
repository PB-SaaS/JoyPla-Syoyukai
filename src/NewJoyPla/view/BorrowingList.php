
<div class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><span>貸出リスト</span></li>
            </ul>
            <div class="no_print uk-margin">
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="borrowing_list.export()">
            </div>
            <h2 class="page_title uk-margin-remove">貸出リスト</h2>
            <hr>
            <div class="no_print uk-margin">
                <div class="uk-margin-large uk-width-1-3 uk-margin-auto">
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-horizontal-text">使用日</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-horizontal-text" type="date" name="usedDate" value="<?php echo date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="uk-margin uk-text-center">
                        <input class="print_hidden uk-button uk-button-primary" type="button" value="使用申請" onclick="borrowing_list.usedTemporaryReport()">
                    </div>
                </div>
            </div>
            <hr>
            <div class="uk-margin" id="tablearea">
                %sf:usr:search26:mstfilter%
            </div>
        </div>
    </div>
</div>
<script>

class BorrowingList
{
    export()
    {
        $("#exportButton").click();
    }
    
    usedTemporaryReport()
    {
        let tmp = this;
        UIkit.modal.confirm("使用申請を行いますか").then(function () {
            if( ! tmp.is_checked_box_validate() )
            {
                return UIkit.modal.alert("使用済みとして登録する商品にチェックを入れてください");
            }
            if( ! tmp.is_used_date_validate() )
            {
                return UIkit.modal.alert("使用日を入力してください");
            }

            loading();
            $.ajax({
                async: false,
                url: "<?php echo $api_url ?>",
                type:'POST',
                data:{
                    _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                    Action : "usedTemporaryReportApi",
                    used_ids : JSON.stringify(tmp.get_used_ids()),
                    used_date : $('input[name="usedDate"]').val(),
                    divisionId : $('select[name="busyo"]').val()
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
                if(data.code != 0){
                    UIkit.modal.alert('使用申請に失敗しました');
                    return false;
                }
                UIkit.modal.alert('使用申請が完了しました').then(function () {
                    location.reload();
                });
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
                UIkit.modal.alert('使用申請に失敗しました');
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
                loading_remove();
            });

        }, function () {
        });
    }

    is_used_date_validate()
    {
        let used_date_value = $('input[name="usedDate"]').val();
        
        return (used_date_value != null && used_date_value != ""); //OK true NG false
    }

    is_checked_box_validate()
    {
        let used_check = $('input.usedCheck:checked');

        return (used_check.length > 0); //OK true NG false
    }

    get_used_ids()
    {
        let used_check = $('input.usedCheck:checked');
        let ids = new Array;

        for(let num = 0 ; num < used_check.length ; num++)
        {
            ids.push(used_check[num].value);
        }
        
        return ids;
    }
}

let borrowing_list = new BorrowingList();
</script>
