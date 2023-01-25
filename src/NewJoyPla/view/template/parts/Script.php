
<script> 
$(function(){
	
  $('input[type="number"]').not('.joypla-333').on('change', function(e){
	changeForInputNumber(this);
  });
});

String.prototype.bytes = function () {
	var length = 0;
		for (var i = 0; i < this.length; i++) {
		var c = this.charCodeAt(i);
		if ((c >= 0x0 && c < 0x81) || (c === 0xf8f0) || (c >= 0xff61 && c < 0xffa0) || (c >= 0xf8f1 && c < 0xf8f4)) {
			length += 1;
		} else {
			length += 2;
		}
	}
	return length;
};

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
	/*
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
	 */
	 loading();
	 setTimeout(function(){loading_remove()},1000);
});	


function loading(){
	if($("#loading").length == 0)
	{
		$(".animsition").before('<div style="z-index: 1;position: fixed;" id="loading" class="uk-position-cover uk-overlay uk-overlay-default uk-flex uk-flex-center uk-flex-middle"><span uk-spinner="ratio: 4.5" class="uk-icon uk-spinner"></span></div>');
	
	}
	/*
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
	});
	*/
 
}

function loading_remove(){
	if($("#loading").length != 0)
	{
		$('.animsition').css({  
	        opacity: "1"  
	    });  
		$('#loading').remove();
	}
}

function generateBarcode(idname,value){
	JsBarcode("#"+idname,value,{width: 1.8, height: 50,fontSize: 14});
	//JsBarcode("#"+idname,value,{width: 1.8, height: 50,fontSize: 14});
	//JsBarcode("#"+idname,value,{ width: 1.8, height: 50,fontSize: 14});
//$(elm).barcode(value.replace(/\r?\n/g,"").trim(), btype, settings);
}

function isJanCheckDigit(barcodeStr) {
	// 短縮用処理
	barcodeStr = ('00000' + barcodeStr).slice(-13);
	let evenNum = 0, oddNum = 0;
	for (var i = 0; i < barcodeStr.length - 1; i++) {
		if (i % 2 == 0) { // 「奇数」かどうか（0から始まるため、iの偶数と奇数が逆）
			oddNum += parseInt(barcodeStr[i]);
		} else {
			evenNum += parseInt(barcodeStr[i]);
		}
	}
	// 結果
	let m = 10 - parseInt((evenNum * 3 + oddNum).toString().slice(-1));
	if(m === 10 ){ m = 0}
	return m === parseInt(barcodeStr.slice(-1));
}
    
gs1128_object = {'01':'','17':'','10':'','21':'','30':''};

function addCheckDigit(barcodeStr) { // 引数は文字列
    // 短縮用処理
    barcodeStr = ('00000' + barcodeStr).slice(-13);
    let evenNum = 0, oddNum = 0;
    for (var i = 0; i < barcodeStr.length - 1; i++) {
        if (i % 2 == 0) { // 「奇数」かどうか（0から始まるため、iの偶数と奇数が逆）
            oddNum += parseInt(barcodeStr[i]);
        } else {
            evenNum += parseInt(barcodeStr[i]);
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

var date = '';
function gs1128_date_chack(code){
	let allcheck = false;
	
	if(code.indexOf("01") === 0){
		code = code.slice( 2 );
		code = code.slice( 14 );
	}else if(code.indexOf("17") === 0){
		code = code.slice( 2 );
		date = code.slice( 0, 6 ); // insert
		code = code.slice( 6 );
	}else if(code.indexOf("10") === 0){
		code = code.slice( 2 );
		if(code.indexOf(" ") == -1){
			code = '';
		} else {
			code = code.slice( code.indexOf(" ") + 1 );
		}
	}else if(code.indexOf("21") === 0){
		code = code.slice( 2 );
		if(code.indexOf(" ") == -1){
			code = '';
		} else {
			code = code.slice( code.indexOf(" ") + 1 );
		}
	}else if(code.indexOf("30") === 0){
		code = code.slice( 2 );
		if(code.indexOf(" ") == -1){
			code = '';
		} else {
			code = code.slice( code.indexOf(" ") + 1 );
		}
	}else{
		allcheck = true;
	}
	
	if(code.length != 0 && !allcheck){
		return check_gs1128(code);
	}

	return date;
}

function check_gs1128(code){
	try {
		console.log(parseBarcode(code));
		let answer = parseBarcode(code);
		gs1128_object = {'01':'','17':'','10':'','21':'','30':'','7003':'',};
		
		answer.parsedCodeItems.forEach((element) => {
			if(element.ai == "17")
			{
			var y = element.data.getFullYear();
			var m = ("00" + (element.data.getMonth()+1)).slice(-2);
			var d = ("00" + element.data.getDate()).slice(-2);
			element.data = y + m + d;
			}
			if(element.ai == "7003" && element.data.match(/^(\d{10})$/) !== null)
			{
				let ymd = "20" + element.data.substring(0,2) + "-" + element.data.substring(2,4) + "-" + element.data.substring(4,6);
				let dt = new Date(ymd);
				if(Number.isNaN(dt.getTime()) === false){ //入力形式判定
					var y = dt.getFullYear();
					var m = ("00" + (dt.getMonth()+1)).slice(-2);
					var d = ("00" + dt.getDate()).slice(-2);
					element.data = y + m + d;
				}else{
					element.data = "";
				}
			}
			gs1128_object[element.ai] = element.data;
		})
		console.log(gs1128_object);
	} catch (error) {
		// console.error(error);
		// expected output: ReferenceError: nonExistentFunction is not defined
		// Note - error messages will vary depending on browser
	}
	return gs1128_object;
}

    function price(num){
		if(num == "") {
			num = "0";
		}
		/*
		let _num = num.replace( /^(-?\d+)(\d{3})/, "$1,$2" );
		if(_num !== num) {
			return price(_num);
	    }
	    */
	    _num = parseFloat(num).toLocaleString('ja-JP', {maximumFractionDigits: 2});
	    document.write(_num);
	}
	function price_text(num){
		if(num == "") {
			num = "0";
		}
		/*
		let _num = num.replace( /^(-?\d+)(\d{3})/, "$1,$2" );
		if(_num !== num) {
			return price_text(_num);
	    }
	    */
	    _num = parseFloat(num).toLocaleString('ja-JP', {maximumFractionDigits: 2});
	    return _num;
	}
	function fixed(num){
		if(num == ""){ return 0 }
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
	
	let custom_loading = false;
	$("#content").ajaxStart(function() {
		if(custom_loading !== true){
			loading();
		}
	});
	
	$("#content").ajaxComplete(function() {
		if(custom_loading !== true){
			loading_remove();
		}
	});
</script>