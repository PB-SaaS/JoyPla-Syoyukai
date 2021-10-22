
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<?php
		    	if($isExistUnorder):
		    	?>	
		    	<script>
		    		$(function(){
		    			UIkit.modal.alert('未発注伝票があります。<br>未発注伝票一覧へ移動します。').then(function(){
							location.href="%url/rel:mpgt:Order%&Action=unorderedList";
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
   const divisitonData = <?php echo json_encode($divisionData); ?>;
   let listObject = {};
   $(function(){
      makeDivitionSelect();
   })


   function makeDivitionSelect(){
   		let selectval = $('#divisionName').val();
	    html = document.createElement("div");
	    select = document.createElement("select");
	    select.className = 'uk-select';
	    select.onchange  = function () {  
	    	onchangeSelect(this.value);
	    };
	    
	    option = document.createElement("option");
	    option.value = '';
	    option.text = '----- 部署を選択してください -----';
		select.appendChild(option);
	    
		Object.keys(divisitonData).forEach(function (key) {
		    option = document.createElement("option");
		    option.value = divisitonData[key].divisionName;
		    option.text = divisitonData[key].divisionName;
		    if(divisitonData[key].divisionName == selectval){
		    	option.selected = 'selected';
		    }
			select.appendChild(option);
		});
	
		html.appendChild(select);
		
		$('#divisionNameDiv').append(html);
   }
   
	function onchangeSelect(val){
		$('#divisionName').val(val);
	}
   
	function active(elm,id,totalZaiko,quantity,busyoId,inHospitalItemId){
	   	elm.style.backgroundColor = 'rgb(255, 204, 153)';
	   	orderQuantity = quantity * elm.value;
	   	$('#orderQuantityPerCarton_'+id).text(orderQuantity);
	   	$('#adjustmentStock_'+id).text(totalZaiko + orderQuantity);
	}
	function validateChack(){
		let flag = false;
		elm = $('input.orderQuantity');
		for(index = 0 ; index < elm.length ; index++)
		{
		    if(elm[index].value != 0){
		    	flag = true;
		    }
		}
		if(!flag){
			UIkit.modal.alert("発注する商品がありません");
		}
		
		return flag;
	}
	
	function getItems()
	{
		let lists = [];
		item = $('input.inHospitalItemId');
		elm = $('input.orderQuantity');
		ids = $('input.id');
		for(index = 0 ; index < elm.length ; index++)
		{
		    if(elm[index].value != 0){
		    	lists.push({
		    		'inHospitalItemId' : item[index].value,
		    		'orderQuantity' : elm[index].value,
		    		'id' : ids[index].value,
		    	});
		    }
		}
		return lists;
	}

	function sendUnorderedSlip(){
		if(!validateChack()){
			return;
		}
		loading();
		$.ajax({
			async: false,
            url:'%url/rel:mpgt:Order%&Action=regUnorderedDivisionApi',
            type:'POST',
            data:{
            	_csrf: "<?php echo $csrf_token ?>",
            	ordered : JSON.stringify( objectValueToURIencode(getItems()) ),
            },
            dataType: 'json'
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
					location.href="%url/rel:mpgt:Order%&Action=unorderedList";
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
