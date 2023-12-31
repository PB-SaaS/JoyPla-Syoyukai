<div class="animsition" uk-height-viewport="expand: true">
	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		<div class="uk-container uk-container-expand">
			<ul class="uk-breadcrumb no_print">
				<li><a href="%url/rel:mpg:top%">TOP</a></li>
				<?php if($isOldTopPage): ?>
				<li><a href="%url/rel:mpg:top%&page=page8">ユーザー管理</a></li>
				<?php else: ?>
				<li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
				<?php endif; ?>
				<li><span>卸業者一覧</span></li>
			</ul>
			<div class="uk-width-1-1" uk-grid>
				<div class="uk-width-3-4@l uk-width-2-3">
					<h2>卸業者一覧</h2>
				</div>
			</div>
			<hr>
			<?php if(($user_info->isHospitalUser() && $user_info->isAdmin()) || ($user_info->isHospitalUser() && $user_info->isApprover())): ?>
			<div uk-grid="" uk-margin="" class="uk-grid uk-grid-stack">
				<div class="uk-width-1-1 uk-grid-margin uk-first-column uk-margin-small-top">
					<form action="<?php echo $api_url ?>"
						method="post">
						<input type="hidden" name="Action" value="regDistributor">
						<input type="submit" value="卸業者登録" class="uk-button uk-button-primary">
					</form>
				</div>
			</div>
			<?php endif ?>
			<div class="uk-margin">
				%sf:usr:search81:table%
			</div>
		</div>
	</div>
</div>