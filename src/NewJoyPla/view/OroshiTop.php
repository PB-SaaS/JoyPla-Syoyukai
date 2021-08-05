<!DOCTYPE html>
<?php

include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/GetTenantData.php';
include_once 'NewJoyPla/api/GetHospitalData.php';
include_once 'NewJoyPla/lib/SpiralDataBase.php';
$userInfo = new App\Lib\UserInfo($SPIRAL);
$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();
$getTenantData = new App\Api\GetTenantData($spiralDataBase,$userInfo);
$tenantData = $getTenantData->select($hospitalData['data'][0]['tenantId']);

?>
<html>
  <head>
    <title>JoyPla TOP</title>
	<?php include_once 'NewJoyPla/src/Head.php'; ?>
    <style>
    	.nj_card{
    		background: #fff;
		    color: #666;
		    box-shadow: 0 5px 15px rgba(0,0,0,.08);
    		position: relative;
			height: 100px;
			box-sizing: border-box;
    	}
		.nj_card a{
			position: absolute;
		    top: 0;
		    bottom: 0;
		    right: 0;
		    left: 0;
		}
		
		.nj_card .menu-body .icon {
		    font-size: 45px;
		    position: absolute;
		    top: -8px;
		    right: 0px;
		    color: #fff;
		    display: block;
		    height: 60px;
		    width: 60px;
		    background-repeat: no-repeat;
		    background-position: right bottom;
		}

		.nj_card .menu-body{
    		color: #ffffff;
    		text-align: left;
			position: absolute;
			left: 12px;
			right: 12px;
			top: 16px;
			bottom: 16px;
			box-sizing: border-box;
		}
    	
		.nj_card .menu-body p{
			margin: 0px;
		    line-height: 1;
			position: absolute;
			top: 10px;
		}
		
		
		.nj_card .menu-body .title{
			font-size: 20px;
			font-weight: 600;
			color : #fff;
			font-family:'メイリオ', 'Meiryo','ＭＳ ゴシック','Hiragino Kaku Gothic ProN','ヒラギノ角ゴ ProN W3',sans-serif;
		}
		
		.nj_card .menu-body .text{
			font-size: 13px;
			color : #fff;
			font-family:'メイリオ', 'Meiryo','ＭＳ ゴシック','Hiragino Kaku Gothic ProN','ヒラギノ角ゴ ProN W3',sans-serif;
		}
		
		.nj_card .menu-foot{
			bottom: 0px;
			left: 0px;
			right: 0px;
			position: absolute;
			padding: 3px;
		}
		
		.nj_card .menu-foot span{
			font-size: 13px;
			display: block;
		}
		
		.category-title{
			padding: 8px;
			font-weight: 600;
		}
		
		.content-1{
    		background: #87c7d7;
    	}
		
		.nj_card.content-1 .menu-body .icon{
			background-image: url(https://i02.smp.ne.jp/u/joypla/images/menu_icon/1.png);	
		}
		
		.nj_card.content-1 .menu-foot{
			background: #79b3c1;
			color: #bcd9e0;
		}
		
		.content-1-1{
    		background: #5daac1;
    	}
    	
		.content-1-1 .menu-foot{
			background: #5499ad;
			color: #aaccd6;
		}
    	/*
		.content-1-1{
    		background: #87c7d7;
    	}
    	
		.content-1-1 .menu-foot{
			background: #79b3c1;
			color: #bcd9e0;
		}
    	*/
		.content-1-2{
    		background: #5daac1;
    	}
    	.content-1-2 .menu-foot{
    		background: #5499ad;
    		color: #aaccd6;
    	}
    	.content-1-2-sp{
    		background: #d7392c;
    	}
    	.content-1-2-sp .menu-foot{
    		background: #ad2525;
    		color: #ffffff;
    	}
		
		.content-2{
    		background: #52adb3;
    	}
		
		.nj_card.content-2 .menu-body .icon{
			background-image: url(https://i02.smp.ne.jp/u/joypla/images/menu_icon/2.png);		
		}
		
		.nj_card.content-2 .menu-foot{
			background: #4a9ba1;
			color: #9bc8cb;
		}
		
		.content-2-1{
    		background: #52adb3;
    	}
    	
		.content-2-1 .menu-foot{
			background: #4a9ba1;
			color: #9bc8cb;
		}
		
		.content-3{
			background: #ea87af;
    	}
		
		.nj_card.content-3 .menu-body .icon{
			background-image: url(https://i02.smp.ne.jp/u/joypla/images/menu_icon/3.png);	
		}
		
		.nj_card.content-3 .menu-foot{
			background: #d2799d;
			color: #e9bcce;
		}
		
		.content-3-1{
    		background: #ea87af;
    	}
    	
		.content-3-1 .menu-foot{
			background: #d2799d;
			color: #e9bcce;
		}
		
		
		.content-4{
			background: #d9c43d;
    	}
		
		.nj_card.content-4 .menu-body .icon{
			background-image: url(https://i02.smp.ne.jp/u/joypla/images/menu_icon/4.png);		
		}
		
		.nj_card.content-4 .menu-foot{
			background: #c3b037;
			color: #e1d89b;
		}
		
		
		.content-5{
    		background: #f3a43c;
    	}
		
		.nj_card.content-5 .menu-body .icon{
			background-image: url(https://i02.smp.ne.jp/u/joypla/images/menu_icon/5.png);		
		}
		
		.nj_card.content-5 .menu-foot{
			background: #da9336;
			color: #edc99b;
		}
		
		
		.content-6{
    		background: #849d3e;
    	}
		
		.nj_card.content-6 .menu-body .icon{
			background-image: url(https://i02.smp.ne.jp/u/joypla/images/menu_icon/6.png);		
		}
		
		.nj_card.content-6 .menu-foot{
			background: #778d38;
			color: #bbc69c;
		}
		
		
		.content-6-1{
    		background: #849d3e;
    	}
    	
		.content-6-1 .menu-foot{
			background: #778d38;
			color: #bbc69c;
		}
    	
		.content-6-2{
    		background: #849d3e;
    	}
    	.content-6-2 .menu-foot{
    		background: #778d38;
    		color: #bbc69c;
    	}
		
		.content-7{
    		background: #b6a1cb;
    	}
		
		.nj_card.content-7 .menu-body .icon{
			background-image: url(https://i02.smp.ne.jp/u/joypla/images/menu_icon/7.png);		
		}
		
		.nj_card.content-7 .menu-foot{
			background: #a391b6;
			color: #d1c8db;
		}
		
		.content-7-1{
    		background: #b6a1cb;
    	}
    	
		.content-7-1 .menu-foot{
			background: #a391b6;
			color: #d1c8db;
		}
		
		.content-8{
    		background: #874989;
    	}
		
		.nj_card.content-8 .menu-body .icon{
			background-image: url(https://i02.smp.ne.jp/u/joypla/images/menu_icon/8.png);		
		}
		
		.nj_card.content-8 .menu-foot{
			background: #79427b;
			color: #bca1bd;
		}
		
		.content-8-1{
    		background: #874989;
    	}
    	
		.content-8-1 .menu-foot{
			background: #79427b;
			color: #bca1bd;
		}
		
		a.top-to-icon {
			zoom : 1.4;
		}
		
		.limiter{
			display: none !important;
		}
		.uk-pagination{
			display: none !important;
			
		}
		
		.topic .limiter{
			display: none !important;
		}
		.topic .uk-pagination{
			display: none !important;
			
		}
		.topic .title{
			padding: 8px;
			background:#999999;
			color:white;
			margin: 0px 0 0px 0;
		}
		.topic{
			border: solid 1px #999999;
			height: 400px;
		}
		
    </style>
    <script>
	let canAjax = true;
    $(document).ready(function() {
		 pageShow('<?php echo $_POST["page"] ?>');
    });
    	function paging(id){
    		let url = location.href ;
    		let param = '';
    		if(id){
    			param = '&page='+id ;
    		}
    		window.location.href = "/area/servlet/area.MyPageBundle?MyPageID=<?php echo $_POST["MyPageID"] ?>"+param;
    	}
    	
    	function pageShow(page){
    		if(page == null || page == ''){
    			page = 'page_top';
    		}
			$("[id^='page']").hide();
		    $('#'+page).show();
    	}
    	
		function search(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			let searchValue =  $('input[name="searchValue"]').val();
			if(searchValue == ''){
				UIkit.modal.alert('検索したいバーコードを読み取りまたは、入力してください');
				return false;
			}
            loading();
            
			canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
			$.ajax({
				async: false,
                url:'%url/rel:mpgt:ObarcodeSAPI%',
                type:'POST',
                data:{
                	searchValue :searchValue
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
                if(data.code != 0){
            		UIkit.modal.alert("伝票が見つかりませんでした").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
            		return false;
                }
                UIkit.modal.alert("伝票が見つかりました").then(function(){
					location.href=data.urls[0];
				});
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
            	UIkit.modal.alert("伝票が見つかりませんでした").then(function(){
					canAjax = true; // 再びAjaxできるようにする
				});
                
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
				loading_remove();
            });
		}
    </script>
  </head>
  <body>
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding uk-padding-remove-horizontal" id="page_top">
		    <div class="uk-container uk-container-large	">
		    	<div class="uk-margin">
		    		<form action="#" onsubmit="search(); return false;" method="post">
				    	<div class="uk-padding-top uk-background-muted uk-padding-small uk-width-1-2@m" >
		    				<input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." autofocus="true" name="searchValue" autocomplete="off"> 
			    			<button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
			    		</div>
		    		</form> 
	    		</div>
		    	<div class="uk-child-width-1-4@m uk-text-center" uk-grid>
				    <div>
				        <div class="nj_card content-1">
	                        <div class="menu-content">
	                            <div class="menu-body">
	                                <i class="icon"></i>
	                                <p>
		                                <span class="title">発注書一覧</span><br>
		                                <span class="text">Orders List</span>
	                                </p>
	                            </div>
	                            <div class="menu-foot">
	                                <span>More Info</span>
	                            </div>
	                            <a href="%url/rel:mpgt:page_266217%" class="slide1 animsition-link"  data-animsition-out-class="fade-out"></a>
	                        </div>
				        </div>
				    </div>
				    <div>
				        <div class="nj_card content-2">
	                        <div class="menu-content">
	                            <div class="menu-body">
	                                <i class="icon"></i>
	                                <p>
		                                <span class="title">検収書一覧</span><br>
		                                <span class="text">Acceptance Form List</span>
	                                </p>
	                            </div>
	                            <div class="menu-foot">
	                                <span>More Info</span>
	                            </div>
	                            <a href="%url/rel:mpgt:page_266905%" class="slide2"></a>
	                        </div>
				        </div>
				    </div>
				    <div>
				        <div class="nj_card content-3">
	                        <div class="menu-content">
	                            <div class="menu-body">
	                                <i class="icon"></i>
	                                <p>
		                                <span class="title">実績</span><br>
		                                <span class="text">Achievement</span>
	                                </p>
	                            </div>
	                            <div class="menu-foot">
	                                <span>More Info</span>
	                            </div>
	                            <a href="%url/rel:mpgt:page_169059%" class="slide3"></a>
	                        </div>
				        </div>
				    </div>
				    <div>
				        <div class="nj_card content-5">
	                        <div class="menu-content">
	                            <div class="menu-body">
	                                <i class="icon"></i>
	                                <p>
		                                <span class="title">情報提供</span><br>
		                                <span class="text">Information Provision</span>
	                                </p>
	                            </div>
	                            <div class="menu-foot">
	                                <span>More Info</span>
	                            </div>
	                            <a href="%url/rel:mpgt:page_266278%" class="slide5"></a>
	                        </div>
				        </div>
				    </div>
				    <div>
				        <div class="nj_card content-7">
	                        <div class="menu-content">
	                            <div class="menu-body">
	                                <i class="icon"></i>
	                                <p>
		                                <span class="title">商品・見積</span><br>
		                                <span class="text">Product Quotation</span>
	                                </p>
	                            </div>
	                            <div class="menu-foot">
	                                <span>More Info</span>
	                            </div>
	                            <a href="javascript:paging('page1')" class="slide1"></a>
	                        </div>
				        </div>
				    </div>
				    <div>
				        <div class="nj_card content-8">
	                        <div class="menu-content">
	                            <div class="menu-body">
	                                <i class="icon"></i>
	                                <p>
		                                <span class="title">ユーザー管理</span><br>
		                                <span class="text">User Management</span>
	                                </p>
	                            </div>
	                            <div class="menu-foot">
	                                <span>More Info</span>
	                            </div>
	                            <a href="javascript:paging('page8')" class="slide8"></a>
	                        </div>
				        </div>
				    </div>
				    <div>
				    </div>
				    <div>
				    </div>
				    
					<div class="uk-width-1-2@m uk-text-left">
						<div class="topic">
				    		<div class="title">	
				    			<div uk-grid>
					    			<p class="uk-width-1-2 ">
					    				<span>システム通知</span>
					    			</p>
					    			<p class="uk-width-1-2 uk-text-right">
					    				<a href="%url/rel:mpgt:page_266378%" class="uk-link" style="color:#ffffff">More Info</a>
					    			</p>	
				    			</div>
				    		</div>
				    		<div class="uk-padding-small">
				    				%sf:usr:search91:table%
				    		</div>
			    		</div>
					</div>
					<div class="uk-width-1-2@m uk-text-left">
						<div class="topic">
				    		<div class="title">
				    			<div uk-grid>
					    			<p class="uk-width-1-2 ">
					    				<span>新着トピック</span>
					    			</p>
					    			<p class="uk-width-1-2 uk-text-right">
					    				<a href="%url/rel:mpgt:page_266278%" class="uk-link" style="color:#ffffff">More Info</a>
					    			</p>	
				    			</div>
				    		</div>
				    		<div class="uk-padding-small">
				    				%sf:usr:search88:table%
				    		</div>
			    		</div>
					</div>
					
				</div>
		    </div>
		</div>
		
		<div class="uk-section uk-section-default uk-preserve-color uk-padding-small uk-padding-remove-horizontal" id="page1">
		    <div class="uk-container uk-container-large">
		    	<div class="uk-child-width-1-1">
                    <div class="uk-text-left uk-child-width-1-2 uk-margin-remove" uk-grid>
	                    <div class="uk-text-left uk-inline">
	                    	<a href="javascript:paging()" class="uk-position-center-left top-to-icon" uk-icon="icon: chevron-left"></a>
	                    </div>
	                    <div class="uk-padding-remove-right">
	                        <p class="uk-text-right">
	                            <span class="title">商品・見積</span><br>
	                            <span class="sub-title">Product Quotation</span>
	                        </p>
	                    </div>
                    </div>
                </div>
                <div class="uk-child-width-1-2@m uk-text-left@m uk-margin-small-top"  style="color: #ffffff" uk-grid>
		    		<div>
			    		<div>	
				    		<p class="uk-width-1-1 content-7-1 category-title">見積</p>
			    		</div>
				    	<div class="uk-child-width-1-2@m uk-text-center" uk-grid>
						    <div>
						        <div class="nj_card content-7-1">
			                        <div class="menu-content">
			                            <div class="menu-body">
			                                <p>
				                                <span class="title">見積依頼一覧</span><br>
				                                <span class="text">Quote Request List</span>
			                                </p>
			                            </div>
			                            <div class="menu-foot">
			                                <span>More Info</span>
			                            </div>
			                            <a href="%url/rel:mpgt:page_266428%" class="slide1 animsition-link"  data-animsition-out-class="fade-out"></a>
			                        </div>
						        </div>
						    </div>
						</div>
					</div>
		    		<div>
			    		<div>	
				    		<p class="uk-width-1-1 content-7-1 category-title">商品</p>
			    		</div>
				    	<div class="uk-child-width-1-2@m uk-text-center" uk-grid>
						    <div>
						        <div class="nj_card content-7-1">
			                        <div class="menu-content">
			                            <div class="menu-body">
			                                <p>
				                                <span class="title">院内商品一覧</span><br>
				                                <span class="text">In Hospital Items List</span>
			                                </p>
			                            </div>
			                            <div class="menu-foot">
			                                <span>More Info</span>
			                            </div>
			                            <a href="%url/rel:mpgt:page_169057%" class="slide1 animsition-link"  data-animsition-out-class="fade-out"></a>
			                        </div>
						        </div>
						    </div>
						</div>
					</div>
		    	</div>
		    </div>
		</div>
		
	
		<div class="uk-section uk-section-default uk-preserve-color uk-padding-small uk-padding-remove-horizontal" id="page8">
		    <div class="uk-container uk-container-large">
		    	<div class="uk-child-width-1-1">
                    <div class="uk-text-left uk-child-width-1-2 uk-margin-remove" uk-grid>
	                    <div class="uk-text-left uk-inline">
	                    	<a href="javascript:paging()" class="uk-position-center-left top-to-icon" uk-icon="icon: chevron-left"></a>
	                    </div>
	                    <div class="uk-padding-remove-right">
	                        <p class="uk-text-right">
	                            <span class="title">ユーザー管理</span><br>
	                            <span class="sub-title">User Management</span>
	                        </p>
	                    </div>
                    </div>
                </div>
		    	<div class="uk-child-width-1-1@m uk-text-left@m uk-margin-small-top"  style="color: #ffffff" uk-grid>
		    		<div>
			    		<div>	
				    		<p class="uk-width-1-1 content-8-1 category-title">ユーザー管理</p>
			    		</div>
				    	<div class="uk-child-width-1-4@m uk-text-center" uk-grid>
						    <div>
						        <div class="nj_card content-8-1">
			                        <div class="menu-content">
			                            <div class="menu-body">
			                                <p>
				                                <span class="title">ユーザー情報変更</span><br>
				                                <span class="text">User Info Change</span>
			                                </p>
			                            </div>
			                            <div class="menu-foot">
			                                <span>More Info</span>
			                            </div>
			                            <a href="#" onclick="document.userInfoChange.submit();" class="slide1"></a>
											<form method="post" action="/regist/is" name="userInfoChange" target="_blank">
										%SMPAREA%
											<input type="hidden" name="userPermission" value="%val:usr:userPermission:id%">
											<input type="hidden" name="loginId" value="%val:usr:loginId%">
											<input type="hidden" name="name" value="%val:usr:name%">
											<input type="hidden" name="nameKana" value="%val:usr:nameKana%">
											<input type="hidden" name="mailAddress" value="%val:usr:mailAddress%">
											<input type="hidden" name="remarks" value="%val:usr:remarks%">
											<input type="hidden" name="SMPFORM" value="%smpform:oroshiUserChange%">
											<input type="hidden" name="id" value="%val:sys:id%">
											<input type="hidden" name="authKey" value="%val:usr:authKey%" >
										</form>
			                        </div>
						        </div>
						    </div>
						    <div>
						        <div class="nj_card content-8-1">
			                        <div class="menu-content">
			                            <div class="menu-body">
			                                <p>
				                                <span class="title">卸業者ユーザー一覧</span><br>
				                                <span class="text">Distributor User List</span>
			                                </p>
			                            </div>
			                            <div class="menu-foot">
			                                <span>More Info</span>
			                            </div>
			                            <a href="%url/rel:mpgt:page_266244%" class="slide1 animsition-link"  data-animsition-out-class="fade-out"></a>
			                        </div>
						        </div>
						    </div>
						</div>
					</div>
		    	</div>
		    </div>
		</div>
		
	</div>
  </body>
</html>
