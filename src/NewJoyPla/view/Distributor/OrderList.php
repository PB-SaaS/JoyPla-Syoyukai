<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true" id='app'>
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
			    <li><span><?php echo $title ?></span></li>
			</ul>
			<div class="no_print uk-margin">
				<?php if($print): ?>
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
				<?php endif ?>
				<?php if($export): ?>
                <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click()">
				<?php endif ?>
				<?php echo $add_button ?>
            </div>
			<?php /*
			<div uk-grid>
				<div class="uk-margin uk-width-1-3@m">
					<label class="uk-form-label" for="form-horizontal-select">所属情報</label>
					<div class="uk-form-controls uk-width-1-1">
						<select class="uk-select uk-width-4-5" name="affiliationId" id="form-horizontal-select">
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
						<button onclick="account_select.update()" class="uk-float-right uk-button uk-button-primary uk-width-1-5">変更</button>
					</div>
				</div>
			</div>
			*/ ?>
			<h2 class="page_title"><?php echo $title ?></h2>
	    	<hr>
	    	<div class="" id="tablearea">
	    		<?php echo $table ?>
	    	</div>
	    </div>
	</div>
</div>
<script>

class AccountSelect {
		update()
        {
            $.ajax({
                async: false,
                url: "%url/rel:mpg:top%",
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
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
                location.reload();
            });
        }
	}
	let account_select = new AccountSelect();


	let elem = $('table tbody a');
	let param = "<?php echo $param ?>";
	if(param != "")
	{
    	for(let index = 0 ; index < elem.length ; index++){
    		elem[index].href += "&Action="+param;
    	};
	}
</script>
<?php 
echo $script 
?>