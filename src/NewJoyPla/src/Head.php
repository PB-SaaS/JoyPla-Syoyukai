<link rel="icon" href="https://i02.smp.ne.jp/u/joypla/new/favicon.ico">
    <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- UIkit CSS -->
	<link rel="stylesheet" href="https://i02.smp.ne.jp/u/joypla/new/css/uikit.min.css" />
	<link rel="stylesheet" href="https://i02.smp.ne.jp/u/joypla/new/css/normalize.css" />
 
	<!-- UIkit JS -->
	<script src="https://i02.smp.ne.jp/u/joypla/new/js/uikit.min.js"></script>
	<script src="https://i02.smp.ne.jp/u/joypla/new/js/uikit-icons.min.js"></script>
 
    <script src="https://i02.smp.ne.jp/u/joypla/new/js/jquery-3.5.1.js"></script>

	<link rel="stylesheet" href="https://i02.smp.ne.jp/u/joypla/new/css/animsition.min.css">
	<script type="text/javascript" src="https://i02.smp.ne.jp/u/joypla/new/js/animsition.min.js"></script>
	
    <script src="https://i02.smp.ne.jp/u/joypla/new/js/JsBarcode.all.min.js"></script>
    <script src="https://i02.smp.ne.jp/u/joypla/new/js/BarcodeParser.js"></script>
	<script src="https://i02.smp.ne.jp/u/joypla/new/js/vue.js"></script>
    
    <script>
	$(function(){
	  $('input[type="number"]').on('change', function(e){
		changeForInputNumber(this);
	  });
	});

	function changeForInputNumber(elm){
	    var month = parseInt($(elm).val());
	    var monthMax = parseInt($(elm).attr('max'));
	    var monthMin = parseInt($(elm).attr('min'));
	    if(month > monthMax){ $(elm).val(monthMax).change(); }
	    if(month < monthMin){ $(elm).val(monthMin).change(); }
	    if(isNaN(month)){ 
			if(monthMin){ $(elm).val(monthMin).change(); 
			} else {$(elm).val(monthMin).change();}
		}
	}

    $(document).ready(function() {
    	//$(".animsition").show();

    	$(".animsition").animsition({
			inClass: 'fade-in', // ロード時のエフェクト
			outClass: 'fade-out', // 離脱時のエフェクト
			inDuration: 500, // ロード時の演出時間
			outDuration: 80, // 離脱時の演出時間
			//linkElement: '.animsition-link', // アニメーションを行う要素
			// e.g. linkElement: 'a:not([target="_blank"]):not([href^="#"])'
			loading: true, // ローディングの有効/無効
			loadingParentElement: 'body', // ローディング要素のラッパー
			loadingClass: 'animsition-loading', // ローディングのクラス
			loadingInner: '', // e.g '' ローディングの内容
			timeout: true, // 一定時間が経ったらアニメーションをキャンセルの有効/無効
			timeoutCountdown: 500, // アニメーションをキャンセルするまでの時間
			onLoadEvent: true, // onLoadイベント後にアニメーションをするかの有効/無効
			browser: [ 'animation-duration', '-webkit-animation-duration'],
			// "browser" option allows you to disable the "animsition" in case the css property in the array is not supported by your browser.
			// The default setting is to disable the "animsition" in a browser that does not support "animation-duration".
			// ブラウザが配列内のCSSプロパティをサポートしていない場合、アニメーションを中止します。デフォルトは「animation-duration」をサポートしていない場合です。
			//overlay : false, // オーバーレイの有効/無効
			//overlayClass : 'animsition-overlay-slide', // オーバーレイのクラス
			//overlayParentElement : 'body', // オーバーレイ要素のラッパー
			//transition: function(url){ window.location.href = url; } // transition後にどこに遷移させるかを設定、urlは「linkElement」のhref
			}
		 );
    });	
    
    
	function loading(){
		$(".animsition").animsition({
			inDuration: 500, // ロード時の演出時間
			outDuration: 80, // 離脱時の演出時間
			linkElement: '.animsition-link', // アニメーションを行う要素
			// e.g. linkElement: 'a:not([target="_blank"]):not([href^="#"])'
			loading: true, // ローディングの有効/無効
			loadingParentElement: 'body', // ローディング要素のラッパー
			loadingClass: 'animsition-loading', // ローディングのクラス
			loadingInner: '', // e.g '' ローディングの内容
			timeout: false, // 一定時間が経ったらアニメーションをキャンセルの有効/無効
			timeoutCountdown: 500, // アニメーションをキャンセルするまでの時間
			onLoadEvent: true, // onLoadイベント後にアニメーションをするかの有効/無効
			browser: [ 'animation-duration', '-webkit-animation-duration'],
			// "browser" option allows you to disable the "animsition" in case the css property in the array is not supported by your browser.
			// The default setting is to disable the "animsition" in a browser that does not support "animation-duration".
			// ブラウザが配列内のCSSプロパティをサポートしていない場合、アニメーションを中止します。デフォルトは「animation-duration」をサポートしていない場合です。
			overlay : false, // オーバーレイの有効/無効
			overlayClass : 'animsition-overlay-slide', // オーバーレイのクラス
			overlayParentElement : 'body', // オーバーレイ要素のラッパー
			//transition: function(url){ window.location.href = url; } // transition後にどこに遷移させるかを設定、urlは「linkElement」のhref
		}
	 );
	}
	
	function loading_remove(){
		$('.animsition-loading').remove();
	}
	
	function generateBarcode(idname,value){
    	JsBarcode("#"+idname,value,{format: "ITF", width: 1.8, height: 50,fontSize: 14});
    	//JsBarcode("#"+idname,value,{format: "CODE128", width: 1.8, height: 50,fontSize: 14});
    	//JsBarcode("#"+idname,value,{ width: 1.8, height: 50,fontSize: 14});
	//$(elm).barcode(value.replace(/\r?\n/g,"").trim(), btype, settings);
	}
        
    gs1128_object = {'01':'','17':'','10':'','21':'','30':''};

	function addCheckDigit(barcodeStr) { // 引数は文字列
        // 短縮用処理
        barcodeStr = ('00000' + barcodeStr).slice(-13);
        let evenNum = 0, oddNum = 0;
        for (var i = 0; i < barcodeStr.length - 1; i++) {
            if (i % 2 == 0) { // 「奇数」かどうか（0から始まるため、iの偶数と奇数が逆）
                oddNum += parseInt(barcodeStr[i]);//奇数
            } else {
                evenNum += parseInt(barcodeStr[i]);//偶数
            }
        }
        // 結果
        return String(barcodeStr.slice(0,12)) + String(10 - parseInt((evenNum * 3 + oddNum).toString().slice(-1)));
    }
	    
	function eanCheckDigit(barcodeStr) { // 引数は文字列
	    // 短縮用処理
	    if(barcodeStr.length == 12 )
	    {
	    	barcodeStr = barcodeStr + "0";
	    }
	    barcodeStr = ('00000' + barcodeStr).slice(-13);
	    let evenNum = 0, oddNum = 0;
	    for (var i = 0; i < barcodeStr.length - 1; i++) {
	        if (i % 2 == 0) {
	            oddNum += parseInt(barcodeStr[i]);
	        } else {
	            evenNum += parseInt(barcodeStr[i]); 
	        }
	    }
	    // 結果
	    let num = 10 - parseInt((evenNum * 3 + oddNum).toString().slice(-1));
	    return String(num).slice(-1);
	}
    
    
	function removeCheckDigit(barcodeStr) { // 引数は文字列
        if(barcodeStr.length == 14)
        {
        	return barcodeStr = barcodeStr.slice(0,13);	
        }
        return barcodeStr;
    }
    
    function gs1_01_to_jan(gs1_01)
    {
    	gs1_01 = gs1_01.slice(1);
    	gs1_01 = gs1_01.slice( 0, -1 );
    	return gs1_01 + eanCheckDigit(gs1_01);
    }
	/*
	function check_gs1128(code){
		let allcheck = false;
		if(code.indexOf("01") === 0){
			code = code.slice( 2 );
			gs1128_object['01'] = code.slice( 0, 14 );
			code = code.slice( 14 );
		}else if(code.indexOf("17") === 0){
			code = code.slice( 2 );
			gs1128_object['17'] = code.slice( 0, 6 );
			code = code.slice( 6 );
		}else if(code.indexOf("10") === 0){
			code = code.slice( 2 );
			if(code.indexOf(" ") == -1){
				gs1128_object['10'] = code;
				code = '';
			} else {
				gs1128_object['10'] = code.slice( 0, code.indexOf(" "));
				code = code.slice( code.indexOf(" ") + 1 );
			}
		}else if(code.indexOf("21") === 0){
			code = code.slice( 2 );
			if(code.indexOf(" ") == -1){
				gs1128_object['21'] = code;
				code = '';
			} else {
				gs1128_object['21'] = code.slice( 0, code.indexOf(" "));
				code = code.slice( code.indexOf(" ") + 1 );
			}
		}else if(code.indexOf("30") === 0){
			code = code.slice( 2 );
			if(code.indexOf(" ") == -1){
				gs1128_object['30'] = code;
				code = '';
			} else {
				gs1128_object['30'] = code.slice( 0, code.indexOf(" "));
				code = code.slice( code.indexOf(" ") + 1 );
			}
		}else{
			allcheck = true;
		}
		
		if(code.length != 0 && !allcheck){
			return check_gs1128(code);
		}
		return gs1128_object;
	}
	*/
	
	
	function check_gs1128(code){
		try {
			let answer = parseBarcode(code);
			
			answer.parsedCodeItems.forEach((element) => {
				if(element.ai == "17")
				{
				var y = element.data.getFullYear();
				var m = ("00" + (element.data.getMonth()+1)).slice(-2);
				var d = ("00" + element.data.getDate()).slice(-2);
				element.data = y + m + d;
				}
				gs1128_object[element.ai] = element.data;
			})
			console.log(gs1128_object);
		} catch (error) {
			console.error(error);
			// expected output: ReferenceError: nonExistentFunction is not defined
			// Note - error messages will vary depending on browser
		}
		return gs1128_object;
	}
	
	    function price(num){
			if(num === "") {
				num = "0";
			}
			let _num = num.replace( /^(-?\d+)(\d{3})/, "$1,$2" );
			if(_num !== num) {
				return price(_num);
		    }
		    document.write(_num);
		}
		function price_text(num){
			if(num == "") {
				num = "0";
			}
			let _num = num.replace( /^(-?\d+)(\d{3})/, "$1,$2" );
			if(_num !== num) {
				return price_text(_num);
		    }
		    return _num;
		}
		function fixed(num){
		
			return parseFloat(num).toFixed(2);
		}
		
		function objectValueToURIencode(object){
			let result = {};
			
			if(object == null){
				return null;
			}
			Object.keys(object).forEach(function (key) {
				if( typeof object[key] == "object"){
					result[key] = objectValueToURIencode(object[key]);
				} else {
					result[key] = encodeURI(object[key]);
				}
			});
			return result;
		}
	</script>
    <style>
		.uk-input[type="number"]{
			text-align: right;
		}
        table.uk-table-divider tr:last-of-type{
          border-bottom: 1px solid #e5e5e5;
        }
        table#tbl-Items tr td{
        	padding: 12px 0px 12px 12px !important;
        }
    	.uk-navbar-container{
    		border-bottom : solid 2px #98CB00;
    	}
    	.bk-application-color{
    		background : #98CB00;
    	}
    	
		table td.active{
		    background-color: #AACC44 !important;
		}
		table td{
			vertical-align: middle !important;
		}
		table.uk-table td, table.uk-table th{
			font-size: 1.0em;
		}
		
		.uk-navbar-container{
			border-bottom : solid 2px #98CB00;
		}
		.bk-application-color{
			background : #98CB00;
		}
		
		.uk-table th{
			text-transform: none !important;
		}
		
		.resultarea{
			display:none;
		}
    	@media print{
	    	.no_print{
		        display: none;
		    }
        	.printarea{
                page-break-after: always;
                font-size: 12px;
            }
		<?php if($labelPrint != true): ?>
			.print-width-1-1{
				width:100% !important;
			}
			table.uk-table-responsive{
			    display: table;
			} 
			table.uk-table-responsive tbody{
			    display: table-row-group;
			} 
			table.uk-table-responsive td, 
			table.uk-table-responsive th{
			    display: table-cell;
				padding: 16px 12px !important;
			} 
			table.uk-table-responsive .uk-table-link:not(:last-child)>a, 
			table.uk-table-responsive td:not(:last-child):not(.uk-table-link), 
			table.uk-table-responsive th:not(:last-child):not(.uk-table-link){
				padding: 16px 12px !important;
			}
			table.uk-table-responsive .uk-table-link:not(:first-child)>a, 
			table.uk-table-responsive td:not(:first-child):not(.uk-table-link), 
			table.uk-table-responsive th:not(:first-child):not(.uk-table-link){
				padding: 16px 12px !important;
			}

			table.uk-table-responsive tr {
			    display: table-row;
			}

			.uk-text-nowrap{
				white-space: normal;
				/*white-space:break-spaces !important;*/
			}
			.uk-table th{
				white-space: nowrap !important;
			}
            body{
            	zoom: 60%; /* Equal to scaleX(0.7) scaleY(0.7) */
            }
            
			/* Single Widths
			 ========================================================================== */
			/*
			 * 1. `max-width` is needed for the pixel-based classes
			 */
			[class*='uk-width'] {
			  box-sizing: border-box;
			  width: 100%;
			  /* 1 */
			  max-width: 100%;
			}
			/* Halves */
			.uk-width-1-2,
			.uk-width-1-2\@s,
			.uk-width-1-2\@m,
			.uk-width-1-2\@l{
			  width: 50%;
			}
			/* Thirds */
			.uk-width-1-3,
			.uk-width-1-3\@s,
			.uk-width-1-3\@m,
			.uk-width-1-3\@l {
			  width: calc(100% * 1 / 3.001);
			}
			.uk-width-2-3,
			.uk-width-2-3\@s,
			.uk-width-2-3\@m,
			.uk-width-2-3\@l {
			  width: calc(100% * 2 / 3.001);
			}
			/* Quarters */
			.uk-width-1-4,
			.uk-width-1-4\@s,
			.uk-width-1-4\@m,
			.uk-width-1-4\@l {
			  width: 25%;
			}
			.uk-width-3-4,
			.uk-width-3-4\@s,
			.uk-width-3-4\@m,
			.uk-width-3-4\@l {
			  width: 75%;
			}
			/* Fifths */
			.uk-width-1-5,
			.uk-width-1-5\@s,
			.uk-width-1-5\@m,
			.uk-width-1-5\@l {
			  width: 20%;
			}
			.uk-width-2-5,
			.uk-width-2-5\@s,
			.uk-width-2-5\@m,
			.uk-width-2-5\@l {
			  width: 40%;
			}
			.uk-width-3-5,
			.uk-width-3-5\@s,
			.uk-width-3-5\@m,
			.uk-width-3-5\@l {
			  width: 60%;
			}
			.uk-width-4-5,
			.uk-width-4-5\@s,
			.uk-width-4-5\@m,
			.uk-width-4-5\@l {
			  width: 80%;
			}
			/* Sixths */
			.uk-width-1-6,
			.uk-width-1-6\@s,
			.uk-width-1-6\@m,
			.uk-width-1-6\@l {
			  width: calc(100% * 1 / 6.001);
			}
			.uk-width-5-6,
			.uk-width-5-6\@s,
			.uk-width-5-6\@m,
			.uk-width-5-6\@l {
			  width: calc(100% * 5 / 6.001);
			}
			/* Pixel */
			.uk-width-small {
			  width: 150px;
			}
			.uk-width-medium {
			  width: 300px;
			}
			.uk-width-large {
			  width: 450px;
			}
			.uk-width-xlarge {
			  width: 600px;
			}
			.uk-width-2xlarge {
			  width: 750px;
			}
			/* Auto */
			.uk-width-auto {
			  width: auto;
			}
			/* Expand */
			.uk-width-expand {
			  flex: 1;
			  min-width: 1px;
			} 
			.uk-table .fix .uk-button, .uk-table .no_fix .uk-button, .uk-table .labelCreate .uk-button{
				display: none;
			}
		<?php endif; ?>
    	}
/*
 * Primary
 */
.uk-button-primary {
  background-color: #7AAE36;
  color: #fff;
  border: 1px solid transparent;
}
/* Hover + Focus */
.uk-button-primary:hover,
.uk-button-primary:focus {
  background-color: #93BD5B;
  color: #fff;
}
.uk-button-primary:disabled:hover,
.uk-button-primary:disabled:focus {
  background-color: transparent;
  color: #999;
}
/* OnClick + Active */
.uk-button-primary:active,
.uk-button-primary.uk-active {
  background-color: #B2D08B;
  color: #fff;
}
/*
 * Success
 */
.uk-label-success {
  background-color: #7AAE36;
  color: #fff;
}

.uk-table tr:last-child{
	border-bottom: 1px solid #e5e5e5;
}

.uk-table tr.tr-gray{
  background: #e5e5e5;
}

.title_spacing {
	letter-spacing : 1em;
}
</style>