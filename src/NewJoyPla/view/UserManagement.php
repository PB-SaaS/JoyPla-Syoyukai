<div class="animsition" uk-height-viewport="expand: true">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb no_print">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
			    <li><span>ユーザー一覧</span></li>
			</ul>
    			<h2 class="page_title uk-margin-remove">ユーザー一覧</h2>
	    	<hr>
	    	<div class="">
				<div uk-grid uk-margin>
					<div class="uk-width-2-3@m">
						<h3>病院ユーザー一覧</h3>
					</div>
					<?php if($user_info->isHospitalUser() && $user_info->isAdmin()): ?>
					<div class="uk-width-1-3@m uk-text-right@m">
						<form action="%url/rel:mpgt:userManagement%" method="post">
							<input type="hidden" value="userRegist" name="Action">
							<input type="submit" value="病院ユーザー登録" class="uk-button uk-button-primary">
						</form>
					</div>
					<?php endif ?>
				</div>
				
				<div class="uk-margin">
				<?php if($user_info->isHospitalUser() && $user_info->isAdmin()): ?>
	    		%sf:usr:search78:table%
				<?php endif ?>
				<?php if($user_info->isHospitalUser() && $user_info->isApprover()): ?>
				%sf:usr:search62:table%
				<?php endif ?>
				</div>
	    	</div>
	    </div>
	</div>
</div>
