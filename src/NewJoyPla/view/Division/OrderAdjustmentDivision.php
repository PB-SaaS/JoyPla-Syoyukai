
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/GetDivision.php";
include_once "NewJoyPla/api/GetUnordered.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$getDivision = new App\Api\GetDivision($spiralDataBase,$userInfo);

$divisionData = $getDivision->select();


$getUnordered = new App\Api\GetUnordered($spiralDataBase,$userInfo);

$getUnorderedData = $getUnordered->select();

?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 発注調整</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
  </head>
  <body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<?php
		    	if($getUnorderedData["count"] > 0):
		    	?>	
		    	<script>
		    		$(function(){
		    			UIkit.modal.alert("未発注伝票があります。<br>未発注伝票一覧へ移動します。").then(function(){
							location.href="%url/rel:mpgt:page_266849%";
						});
		    			
		    		});
		    	</script>
		    	
		    	<?php	
		    	else:
		    	?>
		    	<ul class="uk-breadcrumb no_print uk-margin-top">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>発注調整</span></li>
				</ul>
				<div class="no_print uk-margin">
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<input class="print_hidden uk-button uk-button-primary" type="submit" value="表示内容で未発注伝票を作成" onclick="sendUnorderedSlip();return false;">
				</div>
		    	<h2 class="page_title">発注調整</h2>
		    	<hr>
		    	<div class="" id="tablearea">
		    		%sf:usr:search75:mstfilter%
		    	</div>
		    	
		    	<?php	
		    	endif
		    	?>
		    </div>
		</div>
	</div>
   <script>
   let canAjax = true;
   myDivisiton = "%val:@usr:divisionId%";
   const divisitonData = <?php echo json_encode($divisionData); ?>;
   let listObject = {};
   $(function(){
	  getItemsList();
      makeDivitionSelect();
   })


   function makeDivitionSelect(){
   		let selectval = $("#divisionName").val();
		//html = "<input type="number" class="uk-input" style="width:72px" step="10">";
	    html = document.createElement("div");
	    select = document.createElement("select");
	    select.className = "uk-select";
	    select.onchange  = function () { 
	    	onchangeSelect(this.value);
	    };
	    
	    option = document.createElement("option");
	    option.value = "";
	    option.text = "----- 部署を選択してください -----";
	    //input.step = listObject[object.recordId].irisu;
	    //<span class="uk-text-bottom">個</span>
		select.appendChild(option);
	    
	    if(myDivisiton == divisitonData["store"][0][1]){
		    option = document.createElement("option");
		    option.value = divisitonData["store"][0][3];
		    option.text = divisitonData["store"][0][3] + "(大倉庫)";
		    
		    if(divisitonData["store"][0][3] == selectval){
		    	option.selected = "selected";
		    }
		    //input.step = listObject[object.recordId].irisu;
		    //<span class="uk-text-bottom">個</span>
			select.appendChild(option);
			
		    
		    //option = document.createElement("option");
		    //option.value = "";
		    //option.text = "---------------------------------------";
		    //option.disabled = "disabled";
			//select.appendChild(option);
	    }
	    
		Object.keys(divisitonData["division"]).forEach(function (key) {
		    if(divisitonData["division"][key][5] != "1"){
				if(myDivisiton == divisitonData["division"][key][1]){
				    option = document.createElement("option");
				    option.value = divisitonData["division"][key][3];
				    option.text = divisitonData["division"][key][3];
				    //input.step = listObject[object.recordId].irisu;
				    //<span class="uk-text-bottom">個</span>
				    
				    if(divisitonData["division"][key][3] == selectval){
				    	option.selected = "selected";
				    }
					select.appendChild(option);
				}
		    }
		});
	
		html.appendChild(select);
		
		$("#divisionNameDiv").append(html);
   }
   
   function onchangeSelect(val){
   	$("#divisionName").val(val);
   }
   
   function active(elm,id,totalZaiko,quantity,busyoId,inHospitalItemId){
   	elm.style.backgroundColor = "rgb(255, 204, 153)";
   	orderQuantity = quantity * elm.value;
   	$("#orderQuantityPerCarton_"+id).text(orderQuantity);
   	$("#adjustmentStock_"+id).text(totalZaiko + orderQuantity);
   	listObject[busyoId][inHospitalItemId]["countNum"] = orderQuantity;
   }
   
   
    function getItemsList(){
		$("#tablearea td.json").each(function() {
			json = JSON.parse($(this).text().replace(/\r?\n/g, "").trim());
			if(! listObject[json.divisionId]){
				listObject[json.divisionId] = {};
			}
			listObject[json.divisionId][json.inHospitalItemId] = json;
		});
	}
	
	function validateChack(){
		let flag = false;
		Object.keys(listObject).forEach(function(divkey) {
 
			Object.keys(listObject[divkey]).forEach(function(key) {
	 
			    if(listObject[divkey][key].countNum > 0){
			    	flag = true;
			    }
			})
		 
		})
		if(!flag){
			UIkit.modal.alert("発注する商品がありません");
		}
		
		return flag;
	}

	function sendUnorderedSlip(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
		if(!validateChack()){
			return;
		}
		loading();
		canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
		$.ajax({
			async: false,
            url:"%url/rel:mpgt:regUnordereByDiv%",
            type:"POST",
            data:{
            	ordered : JSON.stringify( objectValueToURIencode(listObject) ),
            	divisionId : $('select[name="busyo"]').val()
            },
            dataType: "json"
        })
        // Ajaxリクエストが成功した時発動
        .done( (data) => {
            
            if(! data.result){
        		UIkit.modal.alert("未発注伝票の作成に失敗しました").then(function(){
					canAjax = true; // 再びAjaxできるようにする
				});
        		return false;
            }
            
            UIkit.modal.alert("未発注伝票を作成しました").then(function(){
				UIkit.modal.alert("未発注伝票一覧へ移動します").then(function(){
					location.href="%url/rel:mpgt:unorderedList%";
				});
			});
        })
        // Ajaxリクエストが失敗した時発動
        .fail( (data) => {
            UIkit.modal.alert("未発注伝票の作成に失敗しました").then(function(){
				canAjax = true; // 再びAjaxできるようにする
			});
        })
        // Ajaxリクエストが成功・失敗どちらでも発動
        .always( (data) => {
			loading_remove();
        });
	}
	
   </script>
  </body>
</html>
