
function createClosePage(){
	const html = `
	<nav class="uk-navbar-container" uk-navbar>
<div class="uk-navbar-left">
 <a href="#" class="uk-navbar-item uk-logo">
  <img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
 </a>
</div>
</nav>
<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">
<div class="uk-container uk-container-expand" uk-height-viewport="expand: true">
 <div class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom uk-text-center" id="mainPage">
  <h1><span uk-icon="icon: warning; ratio: 2.5" class="uk-margin-right"></span><span class="uk-text-middle">���ߐ؂�܂���</span></h1>
  <div class="uk-card uk-card-default uk-text-center">
	  <div class="uk-card-body">
		  <p class="uk-card-title uk-text-warning">���ݓo�^�E�ύX���ł��܂���</p>
	  </div>
	  <div class="uk-card-footer">
		  <p class="uk-background-muted uk-width-middle uk-text-bold uk-text-left uk-padding-small">���⍇��</p>
		  <p class="uk-text-left">
			  ������Ѓp�C�v�h�r�b�c<br>
			  �z�X�s�^���\�����[�V�������ƕ�<br>
			  TEL 03-5575-6601<br>
			  Mail�@JoyPla-spd@pi-pe.co.jp<br>
			  URL�@https://www.pi-pe.co.jp<br>
		  </p>
	  </div>
  </div>
 </div>
</div>
</div>`;
	$('body').html(html);
	$('title').text('JoyPla �o�^�E�ύX����ߐ؂�܂���');
	$('head').append('<link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">');
}


function createErrorPage(code,message){
	const html = `
	<nav class="uk-navbar-container" uk-navbar>
<div class="uk-navbar-left">
 <a href="#" class="uk-navbar-item uk-logo">
  <img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
 </a>
</div>
</nav>
<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">
<div class="uk-container uk-container-expand" uk-height-viewport="expand: true">
 <div class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom uk-text-center" id="mainPage">
  <h1><span uk-icon="icon: warning; ratio: 2.5" class="uk-margin-right"></span><span class="uk-text-middle">�V�X�e���G���[</span></h1>
  <div class="uk-card uk-card-default uk-text-center">
	  <div class="uk-card-body">
		  <p class="uk-card-title uk-text-danger">�G���[���������܂����B<br>
		  �u���E�U����ēx�A�N�Z�X���Ă��������B</p>
		  <p class="uk-text-danger">${code} : ${message}</p>
	  </div>
	  <div class="uk-card-footer">
		  <p class="uk-background-muted uk-width-middle uk-text-bold uk-text-left uk-padding-small">���⍇��</p>
		  <p class="uk-text-left">
			  ������Ѓp�C�v�h�r�b�c<br>
			  �z�X�s�^���\�����[�V�������ƕ�<br>
			  TEL 03-5575-6601<br>
			  Mail�@JoyPla-spd@pi-pe.co.jp<br>
			  URL�@https://www.pi-pe.co.jp<br>
		  </p>
	  </div>
  </div>
 </div>
</div>
</div>`;
	$('body').html(html);
	$('title').text('JoyPla �V�X�e���G���[');
	$('head').append('<link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">');
}
//createLoginErrorPage('1','%val:error:msg%','%val:error:referrer%','%area:hidden%','%area_rereg_url%');
//pagetype = 1 success pagetype = 2 error

function createLoginPage(pagetype,msg,referrer,hidden,area_rereg_url){
	afterMsg = '';
	alertMsg = '';
	if(pagetype == 2){
		afterMsg = '�G���[';
		alertMsg = `<p class="uk-text-danger">${msg}</p>`;
	}
	const html = `
	<nav class="uk-navbar-container" uk-navbar>
	<div class="uk-navbar-left">
	<a href="#" class="uk-navbar-item uk-logo">
	<img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
	</a>
	</div>
	</nav>
	<div class="uk-section uk-section-default uk-preserve-color">
			<div class="uk-container uk-container-small" uk-height-viewport="expand: true">
				<div class="uk-card uk-card-default">
					<div class="uk-card-header">
						<h3 class="uk-card-title">JoyPla �a�@�l���O�C��</h3>
					</div>
					<div class="uk-card-body">
						${alertMsg}
						<form class="uk-form-stacked" action="/area/Login" autocomplete="off" method="post">
							<div class="uk-margin">
								<label class="uk-form-label" for="SMPID">���O�C��ID</label>
								<div class="uk-form-controls">
									<input class="uk-input" type="text" name="SMPID" id="SMPID" placeholder="���O�C��ID">
								</div>
							</div>
							<div class="uk-margin">
								<label class="uk-form-label" for="SMPID">�p�X���[�h</label>
								<div class="uk-form-controls">
									<input class="uk-input uk-password" type="password" id="SMPPASSWORD" name="SMPPASSWORD">
								</div>
							</div>
							<div class="uk-text-center">
								<button class="uk-button uk-button-primary">���O�C��</button>
							</div>
							<input name="SMPBACKURL" type="hidden" value="${referrer}"><br>
							<div class="uk-text-center uk-margin-top">
								<a href="${area_rereg_url}">�p�X���[�h�o�^�E�ύX�葱��</a>
							</div>
							<div class="uk-hidden">${hidden}</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	`;
	$('body').html(html);
	$('title').text('JoyPla ���O�C��'+afterMsg);
	$('head').append('<link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">');
}


function createOroshiLoginPage(pagetype,msg,referrer,hidden,area_rereg_url){
	afterMsg = '';
	alertMsg = '';
	if(pagetype == 2){
		afterMsg = '�G���[';
		alertMsg = `<p class="uk-text-danger">${msg}</p>`;
	}
	const html = `
	<nav class="uk-navbar-container" uk-navbar>
	<div class="uk-navbar-left">
	<a href="#" class="uk-navbar-item uk-logo">
	<img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
	</a>
	</div>
	</nav>
	<div class="uk-section uk-section-default uk-preserve-color">
			<div class="uk-container uk-container-small" uk-height-viewport="expand: true">
				<div class="uk-card uk-card-default">
					<div class="uk-card-header">
						<h3 class="uk-card-title">JoyPla ���Ǝҗl���O�C��</h3>
					</div>
					<div class="uk-card-body">
						${alertMsg}
						<form class="uk-form-stacked" action="/area/Login" autocomplete="off" method="post">
							<div class="uk-margin">
								<label class="uk-form-label" for="SMPID">���O�C��ID</label>
								<div class="uk-form-controls">
									<input class="uk-input" type="text" name="SMPID" id="SMPID" placeholder="���O�C��ID">
								</div>
							</div>
							<div class="uk-margin">
								<label class="uk-form-label" for="SMPID">�p�X���[�h</label>
								<div class="uk-form-controls">
									<input class="uk-input uk-password" type="password" id="SMPPASSWORD" name="SMPPASSWORD">
								</div>
							</div>
							<div class="uk-text-center">
								<button class="uk-button uk-button-primary">���O�C��</button>
							</div>
							<input name="SMPBACKURL" type="hidden" value="${referrer}"><br>
							<div class="uk-text-center uk-margin-top">
								<a href="${area_rereg_url}">�p�X���[�h�o�^�E�ύX�葱��</a>
							</div>
							<div class="uk-hidden">${hidden}</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	`;
	$('body').html(html);
	$('title').text('JoyPla ���O�C��'+afterMsg);
	$('head').append('<link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">');
	
	}