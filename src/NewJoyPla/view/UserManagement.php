<div class="animsition" uk-height-viewport="expand: true">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb no_print">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
			    <li><span>ユーザー管理</span></li>
			</ul>
    			<h2 class="page_title uk-margin-remove">ユーザー管理</h2>
	    	<hr>
	    	<div class="">
				<div uk-grid uk-margin>
					<div class="uk-width-2-3@m">
						<h3>病院ユーザー一覧</h3>
					</div>
					<div class="uk-width-1-3@m uk-text-right@m">
						<form action="%url/rel:mpgt:userManagement%" method="post">
							<input type="hidden" value="userRegist" name="Action">
							<input type="submit" value="病院ユーザー登録" class="uk-button uk-button-primary">
						</form>
					</div>
				</div>
				<div class="uk-margin">
					%sf:usr:search78:table%
				</div>
	    	</div>
	    </div>
	</div>
</div>
