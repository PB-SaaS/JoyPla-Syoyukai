
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