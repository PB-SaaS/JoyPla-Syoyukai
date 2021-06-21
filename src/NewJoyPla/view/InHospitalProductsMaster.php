
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 院内商品マスタ</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>院内商品マスタ</span></li>
				</ul>
		    	<div class="no_print uk-margin">
				  <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
				  <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click()">
				</div>
		    	<div class="uk-width-1-1 uk-margin-remove-left" uk-grid>
		    		<div class="uk-width-3-4@m uk-padding-remove">
		    			<h2>院内商品マスタ</h2>
					</div>
		    		<div class="uk-width-1-4@m uk-padding-remove no_print">
				    	<ul uk-accordion class="uk-background-muted uk-padding-small">
						    <li>
						        <a class="uk-accordion-title" href="#">表示項目選択</a>
						        <div class="uk-accordion-content" hidden>
						    		<a class="uk-button uk-button-secondary uk-button-small uk-width-1-1" href="#" onclick="table_field_selector()">反映</a>
						        	<ul class="uk-list uk-list-striped">
									
									<li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_01" type="checkbox"> id</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_02" type="checkbox"> 使用状況</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_03" type="checkbox"> 院内商品ID</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_04" type="checkbox"> 登録日時</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_05" type="checkbox"> 更新日時</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_06" type="checkbox"> 商品ID</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_07" type="checkbox"> メーカー</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_08" type="checkbox"> 商品名</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_09" type="checkbox"> 製品コード</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_10" type="checkbox"> 規格</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_11" type="checkbox"> JANコード</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_12" type="checkbox"> カタログNO</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_13" type="checkbox"> シリアルNO</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_14" type="checkbox"> 保険請求分類（医科）</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_15" type="checkbox"> 保険請求分類（在宅）</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_16" type="checkbox"> 入数</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_17" type="checkbox"> 入数単位</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_18" type="checkbox"> 個数単位</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_19" type="checkbox"> 購買価格</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_20" type="checkbox"> 院内在庫数</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_21" type="checkbox"> 償還フラグ</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_22" type="checkbox"> 償還価格</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_23" type="checkbox"> 旧償還価格</label>
								            </div>
										</li>
									    <li>
									    	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								            	<label><input class="uk-checkbox chk_24" type="checkbox"> 卸業者</label>
								            </div>
										</li>
									</ul>
						        </div>
						    </li>
						</ul>
					</div>
		    		
		    	</div>
		    	<div class="uk-margin-top">
				%sf:usr:search5%
				</div>
			</div>
		</div>
	</div>
	<script>
	$(function(){
		let storage = JSON.parse(localStorage.getItem("joypla_inHpItemsList"));
		let dispObj = {};
		if(!storage){
			for(let i = 1 ; i <= 24 ; i++){
				dispObj[i] = true;
			}
		} else {
			dispObj = storage;
		}
		dispSet(dispObj);
	});
	function table_field_selector(){
		let count = $("input[class^='uk-checkbox chk_']").length;
		let disp = {};
		for(let i = 1 ; i <= count ; i++){
			disp[i] = false;
			if($("input.chk_"+( "00" + i ).slice( -2 )).is(":checked")){
				disp[i] = true;
			}
		}
		localStorage.setItem("joypla_inHpItemsList", JSON.stringify(disp));
		location.reload();
	}
	
	function dispSet(settingObj){
		Object.keys(settingObj).forEach(function (key) {
		  $(".chk_"+( "00" + key ).slice( -2 )).prop("checked", settingObj[key]);
		  if(settingObj[key]){
		  	$(".f_"+( "00" + key ).slice( -2 )).show();
		  } else {
		  	$(".f_"+( "00" + key ).slice( -2 )).hide();
		  }
		});
	}
	</script>
</body>
</html>
