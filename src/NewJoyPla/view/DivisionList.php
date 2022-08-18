<div class="animsition" uk-height-viewport="expand: true">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb no_print">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
			    <li><span>部署一覧</span></li>
			</ul>
	    	<h2 class="page_title uk-margin-remove">部署一覧</h2>
	    	<hr>
	    	
			<div uk-grid uk-margin>
				<div class="uk-width-1-1">
	    			<form action="%url/rel:mpgt:userManagement%" method="post">
	    				%SMPAREA%
						<input type="hidden" name="Action" value="registDivision">
						<input type="submit" value="部署登録" class="uk-button uk-button-primary">
	    			</form>
                    <p class="uk-text-danger uk-text-bold">※大倉庫の登録は1つまでです</p>
				</div>
			</div>
                    
	    	<div class="uk-margin" id="tablearea">
				%sf:usr:search80:table%
	    	</div>
		</div>
	</div>
</div>
