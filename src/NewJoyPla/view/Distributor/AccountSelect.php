
<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<div class="uk-width-2-3@m uk-margin-auto">
	    	    <form class="uk-form-horizontal uk-margin-large">
                    <fieldset class="uk-fieldset">
                
                        <legend class="uk-legend">アカウント切り替え</legend>
                
                         <div class="uk-margin">
                            <label class="uk-form-label" for="form-horizontal-select">所属情報</label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="affiliationId" id="form-horizontal-select">
                                    <option value=''> ----- アカウントを選択してください ----- </option>
                                    <?php 
                                    foreach($affiliation as $a){
                                        $selected =  "";
                                        if($a->affiliationId === $current_affiliation){
                                            $selected =  "selected";
                                        }
                                        echo "<option value='".$a->affiliationId."' ".$selected.">病院：".$a->hospitalName."／卸業者：".$a->distributorName."</option>";  
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin uk-text-center">
                            <button type="button" class="uk-button uk-button-primary" onclick="account_select.update()">アカウントを切り替える</button>
                        </div>     
                
                    </fieldset>
                </form>
	    	</div>
	    </div>
	</div>
</div>
<script>
	class AccountSelect {
	    
        update()
        {
            if($('select[name=affiliationId]').val() == "")
            {
                UIkit.modal.alert('所属情報からアカウントを選択してください');
                return false;
            }
            loading();
            $.ajax({
                async: false,
                url: "<?php echo $api_url ?>",
                type:'POST',
                data:{
                    autoloadCheckSkip : true,
                    _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                    Action : "userAffiliationIdUpdate",
                    affiliationId : $('select[name=affiliationId]').val()
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
                if(data.code != 0){
                    UIkit.modal.alert('更新に失敗しました');
                    return false;
                }
                location.href = "%url/rel:mpgt:oroshiTopPage%";
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
                UIkit.modal.alert('更新に失敗しました');
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
                loading_remove();
            });
        }
	}
	let account_select = new AccountSelect();
</script>