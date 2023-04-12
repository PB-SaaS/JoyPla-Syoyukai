
<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
		<div class="uk-container uk-container-expand">
			<?php if ($isExistUnorder): ?>	
			<script>
				$(function(){
					UIkit.modal.alert('未発注伝票があります。<br>未発注伝票一覧へ移動します。').then(function(){
						location.href="%url/rel:mpgt:Order%&Action=unorderedList";
					});
				});
			</script>
			
			 /* ?>
				<div class="uk-inline uk-margin-small-left">
					<div uk-grid class="uk-margin-remove">
						<div class="uk-padding-remove">
							<label class="uk-switch" for="default-1">
								<input type="checkbox" id="default-1" v-model="integrate" />
								<div class="uk-switch-slider uk-switch-big"></div>
							</label>
						</div>
						<div style="padding: 7px 0px 7px 8px;">
							<small v-if="integrate">既存の未発注伝票に追加する</small>
							<small v-else>新規で未発注伝票を発行する</small>
						</div>
					</div>
				</div>
				<?php */<?php
       /* ?>
				<div class="uk-inline uk-margin-small-left">
					<div uk-grid class="uk-margin-remove">
						<div class="uk-padding-remove">
							<label class="uk-switch" for="default-1">
								<input type="checkbox" id="default-1" v-model="integrate" />
								<div class="uk-switch-slider uk-switch-big"></div>
							</label>
						</div>
						<div style="padding: 7px 0px 7px 8px;">
							<small v-if="integrate">既存の未発注伝票に追加する</small>
							<small v-else>新規で未発注伝票を発行する</small>
						</div>
					</div>
				</div>
				<?php */
       ?>else: ?>
			<ul class="uk-breadcrumb no_print uk-margin-top">
				<li><a href="%url/rel:mpg:top%">TOP</a></li>
                	<li><a href="%url/rel:mpg:top%&page=page1">消費・発注</a></li>
				<li><span>定数発注</span></li>
			</ul>
			<div class="no_print" uk-margin id="app">
				<input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
				<input class="print_hidden uk-button uk-button-primary" type="button" value="表示内容で未発注伝票を作成" v-on:click="sendUnorderedSlip">
				<?php
       /* ?>
				<div class="uk-inline uk-margin-small-left">
					<div uk-grid class="uk-margin-remove">
						<div class="uk-padding-remove">
							<label class="uk-switch" for="default-1">
								<input type="checkbox" id="default-1" v-model="integrate" />
								<div class="uk-switch-slider uk-switch-big"></div>
							</label>
						</div>
						<div style="padding: 7px 0px 7px 8px;">
							<small v-if="integrate">既存の未発注伝票に追加する</small>
							<small v-else>新規で未発注伝票を発行する</small>
						</div>
					</div>
				</div>
				<?php */
       ?>
			</div>
			<h2 class="page_title">定数発注</h2>
			<hr>
			<div class="" id="tablearea">
				%sf:usr:search75:mstfilter%
			</div>
			
			<?php endif; ?>
		</div>
	</div>
</div>
<script>
const divisitonData = <?php echo json_encode($divisionData); ?>;
var app = new Vue({
	el: '#app',
	data: {
		divisitonData : divisitonData,
		lists : [],
		integrate: false,
	},
	mounted() {
		if (localStorage.joypla_unorder_slip_integrate) {
			//this.integrate = (localStorage.joypla_unorder_slip_integrate === 'true');
		}
	},
	filters: {
        number_format: function(value) {
            if (! value ) { return 0; }
            return new Intl.NumberFormat('ja-JP').format(value);
        },
    },
    watch: {
		integrate(bool) {
			//localStorage.joypla_unorder_slip_integrate = bool;
		},
    },
	methods: {
		validateChack: function(){
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
		},
		sendUnorderedSlip: function(){
			UIkit.modal.confirm("未発注伝票を作成します。<br>よろしいですか").then(function(){
				if(!app.validateChack()){
					return;
				}
				app.getItems();
				$.ajax({
					async: false,
					url:'%url/rel:mpgt:Order%&Action=regUnorderedDivisionApi',
					type:'POST',
					data:{
						_csrf: "<?php echo $csrf_token; ?>",
						ordered : JSON.stringify( app.lists ),
						integrate : app.integrate,
					},
					dataType: 'json'
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
					
					if(! data.result){
						UIkit.modal.alert("未発注伝票の作成に失敗しました");
						return false;
					}
					
					UIkit.modal.alert("未発注伝票を作成しました").then(function(){
						UIkit.modal.alert("未発注書一覧へ移動します").then(function(){
							location.href="%url/rel:mpgt:Order%&Action=unorderedList";
						});
					});
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
					UIkit.modal.alert("未発注伝票の作成に失敗しました");
				})
				// Ajaxリクエストが成功・失敗どちらでも発動
				.always( (data) => {
				});
			},function(){
				UIkit.modal.alert("中止しました");
			});
		},
		getItems: function(){
			item = $('input.inHospitalItemId');
			elm = $('input.orderQuantity');
			ids = $('input.id');
			for(index = 0 ; index < elm.length ; index++)
			{
				if(elm[index].value != 0){
					app.lists.push({
						'inHospitalItemId' : item[index].value,
						'orderQuantity' : elm[index].value,
						'id' : ids[index].value,
					});
				}
			}
		}
	}

});

	$(function(){
      makeDivitionSelect();
   	});


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
	
   </script>
