
function createClosePage(){
	let html = '<nav class="uk-navbar-container" uk-navbar>';
	html += '<div class="uk-navbar-left">';
	html += '<a href="#" class="uk-navbar-item uk-logo">';
	html += '<img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />';
	html += '</a>';
	html += '</div>';
	html += '</nav>';
	html += '<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">';
	html += '<div class="uk-container uk-container-expand" uk-height-viewport="expand: true">';
	html += '<div class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom uk-text-center" id="mainPage">';
	html += '<h1><span uk-icon="icon: warning; ratio: 2.5" class="uk-margin-right"></span><span class="uk-text-middle">締め切りました</span></h1>';
	html += '<div class="uk-card uk-card-default uk-text-center">';
	html += '<div class="uk-card-body">';
	html += '<p class="uk-card-title uk-text-warning">現在登録・変更ができません</p>';
	html += '</div>';
	html += '<div class="uk-card-footer">';
	html += '<p class="uk-background-muted uk-width-middle uk-text-bold uk-text-left uk-padding-small">お問合せ</p>';
	html += '<p class="uk-text-left">';
	html += '株式会社パイプドビッツ<br>';
	html += 'ホスピタルソリューション事業部<br>';
	html += 'TEL 03-5575-6601<br>';
	html += 'Mail　JoyPla-spd@pi-pe.co.jp<br>';
	html += 'URL　https://www.pi-pe.co.jp<br>';
	html += '</p>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	$('body').html(html);
	$('title').text('JoyPla 登録・変更を締め切りました');
	$('head').append('<link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">');
}


function createErrorPage2(code,message){
	let html = '<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">';
	html += '<div class="uk-container uk-container-expand" uk-height-viewport="expand: true">';
	html += '<div class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom uk-text-center" id="mainPage">';
	html += '<h1><span uk-icon="icon: warning; ratio: 2.5" class="uk-margin-right"></span><span class="uk-text-middle">システムエラー</span></h1>';
	html += '<div class="uk-card uk-card-default uk-text-center">';
	html += '<div class="uk-card-body">';
	html += '<p class="uk-card-title uk-text-danger">エラーが発生しました。<br>';
	html += 'ブラウザを閉じ再度アクセスしてください。</p>';
	html += '<p class="uk-text-danger">'+code+' : '+message+'</p>';
	html += '</div>';
	html += '<div class="uk-card-footer">';
	html += '<p class="uk-background-muted uk-width-middle uk-text-bold uk-text-left uk-padding-small">お問合せ</p>';
	html += '<p class="uk-text-left">';
	html += '株式会社パイプドビッツ<br>';
	html += 'ホスピタルソリューション事業部<br>';
	html += 'TEL 03-5575-6601<br>';
	html += 'Mail　JoyPla-spd@pi-pe.co.jp<br>';
	html += 'URL　https://www.pi-pe.co.jp<br>';
	html += '</p>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	$('body').html(html);
	$('title').text('JoyPla システムエラー');
	$('head').append('<link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">');
}

function createErrorPage(code,message){
	let html = '<nav class="uk-navbar-container" uk-navbar>';
	html += '<div class="uk-navbar-left">';
	html += '<a href="#" class="uk-navbar-item uk-logo">';
	html += '<img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />';
	html += '</a>';
	html += '</div>';
	html += '</nav>';
	html += '<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">';
	html += '<div class="uk-container uk-container-expand" uk-height-viewport="expand: true">';
	html += '<div class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom uk-text-center" id="mainPage">';
	html += '<h1><span uk-icon="icon: warning; ratio: 2.5" class="uk-margin-right"></span><span class="uk-text-middle">システムエラー</span></h1>';
	html += '<div class="uk-card uk-card-default uk-text-center">';
	html += '<div class="uk-card-body">';
	html += '<p class="uk-card-title uk-text-danger">エラーが発生しました。<br>';
	html += 'ブラウザを閉じ再度アクセスしてください。</p>';
	html += '<p class="uk-text-danger">'+code+' : '+message+'</p>';
	html += '</div>';
	html += '<div class="uk-card-footer">';
	html += '<p class="uk-background-muted uk-width-middle uk-text-bold uk-text-left uk-padding-small">お問合せ</p>';
	html += '<p class="uk-text-left">';
	html += '株式会社パイプドビッツ<br>';
	html += 'ホスピタルソリューション事業部<br>';
	html += 'TEL 03-5575-6601<br>';
	html += 'Mail　JoyPla-spd@pi-pe.co.jp<br>';
	html += 'URL　https://www.pi-pe.co.jp<br>';
	html += '</p>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	$('body').html(html);
	$('title').text('JoyPla システムエラー');
	$('head').append('<link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">');
}
//createLoginErrorPage('1','%val:error:msg%','%val:error:referrer%','%area:hidden%','%area_rereg_url%');
//pagetype = 1 success pagetype = 2 error

function createLoginPage(pagetype,msg,referrer,hidden,area_rereg_url){
	afterMsg = '';
	alertMsg = '';
	if(pagetype == 2){
		afterMsg = 'エラー';
		alertMsg = '<p class="uk-text-danger">'+msg+'</p>';
	}
	let html = '<nav class="uk-navbar-container" uk-navbar>';
	html += '<div class="uk-navbar-left">';
	html += '<a href="#" class="uk-navbar-item uk-logo">';
	html += '<img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />';
	html += '</a>';
	html += '</div>';
	html += '</nav>';
	html += '<div class="uk-section uk-section-default uk-preserve-color">';
	html += '<div class="uk-container uk-container-small" uk-height-viewport="expand: true">';
	html += '<div class="uk-card uk-card-default">';
	html += '<div class="uk-card-header">';
	html += '<h3 class="uk-card-title">JoyPla 病院様ログイン</h3>';
	html += '</div>';
	html += '<div class="uk-card-body">';
	html += alertMsg;
	html += '<form class="uk-form-stacked" action="/area/Login" autocomplete="off" method="post">';
	html += '<div class="uk-margin">';
	html += '<label class="uk-form-label" for="SMPID">ログインID</label>';
	html += '<div class="uk-form-controls">';
	html += '<input class="uk-input" type="text" name="SMPID" id="SMPID" placeholder="ログインID">';
	html += '</div>';
	html += '</div>';
	html += '<div class="uk-margin">';
	html += '<label class="uk-form-label" for="SMPID">パスワード</label>';
	html += '<div class="uk-form-controls">';
	html += '<input class="uk-input uk-password" type="password" id="SMPPASSWORD" name="SMPPASSWORD">';
	html += '</div>';
	html += '</div>';
	html += '<div class="uk-text-center">';
	html += '<button class="uk-button uk-button-primary">ログイン</button>';
	html += '</div>';
	html += '<input name="SMPBACKURL" type="hidden" value="'+referrer+'"><br>';
	html += '<div class="uk-text-center uk-margin-top">';
	html += '<a href="'+area_rereg_url+'">パスワード登録・変更手続き</a>';
	html += '</div>';
	html += '<div class="uk-hidden">'+hidden+'</div>';
	html += '</form>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	$('body').html(html);
	$('title').text('JoyPla ログイン'+afterMsg);
	$('head').append('<link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">');
}


function createOroshiLoginPage(pagetype,msg,referrer,hidden,area_rereg_url){
	afterMsg = '';
	alertMsg = '';
	if(pagetype == 2){
		afterMsg = 'エラー';
		alertMsg = '<p class="uk-text-danger">'+msg+'</p>';
	}
	let html = '<nav class="uk-navbar-container" uk-navbar>';
	html += '<div class="uk-navbar-left">';
	html += '<a href="#" class="uk-navbar-item uk-logo">';
	html += '<img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />';
	html += '</a>';
	html += '</div>';
	html += '</nav>';
	html += '<div class="uk-section uk-section-default uk-preserve-color">';
	html += '<div class="uk-container uk-container-small" uk-height-viewport="expand: true">';
	html += '<div class="uk-card uk-card-default">';
	html += '<div class="uk-card-header">';
	html += '<h3 class="uk-card-title">JoyPla 卸業者様ログイン</h3>';
	html += '</div>';
	html += '<div class="uk-card-body">';
	html += alertMsg;
	html += '<form class="uk-form-stacked" action="/area/Login" autocomplete="off" method="post">';
	html += '<div class="uk-margin">';
	html += '<label class="uk-form-label" for="SMPID">ログインID</label>';
	html += '<div class="uk-form-controls">';
	html += '<input class="uk-input" type="text" name="SMPID" id="SMPID" placeholder="ログインID">';
	html += '</div>';
	html += '</div>';
	html += '<div class="uk-margin">';
	html += '<label class="uk-form-label" for="SMPID">パスワード</label>';
	html += '<div class="uk-form-controls">';
	html += '<input class="uk-input uk-password" type="password" id="SMPPASSWORD" name="SMPPASSWORD">';
	html += '</div>';
	html += '</div>';
	html += '<div class="uk-text-center">';
	html += '<button class="uk-button uk-button-primary">ログイン</button>';
	html += '</div>';
	html += '<input name="SMPBACKURL" type="hidden" value="'+referrer+'"><br>';
	html += '<div class="uk-text-center uk-margin-top">';
	html += '<a href="'+area_rereg_url+'">パスワード登録・変更手続き</a>';
	html += '</div>';
	html += '<div class="uk-hidden">'+hidden+'</div>';
	html += '</form>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';

	$('body').html(html);
	$('title').text('JoyPla ログイン'+afterMsg);
	$('head').append('<link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">');
}