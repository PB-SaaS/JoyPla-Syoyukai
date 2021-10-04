<nav class="uk-navbar-container no_print" uk-navbar>
	<div class="uk-navbar-left">
		<a href="%url/rel:mpg:top%" class="uk-navbar-item uk-logo">
			<img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
		</a>
	</div>

	<div class="uk-navbar-right uk-margin-right" style="flex-wrap: nowrap;">
		<p class="uk-margin-auto-vertical uk-visible@s uk-margin-right uk-text-right">
			<?php echo $facilityName ?><br>
			<?php echo $userName ?> 様
		</p>
		<a href="%url/rel:mpg:top%" class="uk-icon-button uk-margin-right" uk-icon="icon: home; ratio: 1.5" title="TOPへ戻る"></a>
		<a href="#" class="uk-icon-button uk-margin-right" uk-icon="icon: mail; ratio: 1.5" title="お問合せ" onclick="document.contactUs.submit()"></a>
		<form method="post" action="/regist/is" name="contactUs" target="_blank">
			%SMPAREA%
			<input type="hidden" name="SMPFORM" value="%smpform:contactUs%">
		</form>
		<a href="%form:act:logout%" class="uk-icon-button" uk-icon="icon: sign-out; ratio: 1.5" title="ログアウト"></a>
	</div>

</nav>