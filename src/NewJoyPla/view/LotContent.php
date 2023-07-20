<style>

	a.top-to-icon {
			zoom : 1.4;
		}
		
		.page_title{
		}
		
		.shouhin-data{
		}
		
		.id{
			min-width: 24px;
			max-width: 24px;
		}
		
		.shouhin-data .itemName{
			font-size: 14px;
		}
		.shouhin-data .makerName{
			font-size: 12px;
			
		}
		.shouhin-data .tana{
			font-size: 8px;
		}
		.shouhin-data .itemCode{
			font-size: 8px;
			
		}
		.shouhin-data .constant{
			font-size: 8px;
			
		}
		.shouhin-data .quantity{
			font-size: 8px;
			
		}
		.shouhin-data .price{
			font-size: 8px;
			
		}
		.shouhin-data .JANCode{
			font-size: 8px;
			
		}
		.shouhin-data .officialFlag{
			font-size: 8px;
			
		}
		.itemCount{
			position: relative;
		}
		.itemCount->after {
		   content: attr(data-format); /* ここが重要!! */
		   width: 10%;
		   height: 20px;
		   position: absolute;
		   bottom: 4px;
		}
		.itemCountInput{
			width: 90%;
		}
		.uk-table th, .uk-table td{
			word-break: break-word;
			padding: 12px 8px;
			vertical-align: middle;
		}
		.uk-table tfoot tr{
			border-bottom: #e5e5e5 1px solid;
			border-top: #e5e5e5 1px solid;
		}
		
		table.uk-table {
			counter-reset: rowCount;
		}

		table.uk-table > tbody > tr {
			counter-increment: rowCount;
		}

		table.uk-table > tbody > tr > td:first-child::before {
			content: counter(rowCount);
		}
	</style>
<div id="app" class="animsition" uk-height-viewport="expand: true">
	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		<div class="uk-container uk-container-expand">
			<ul class="uk-breadcrumb">
				<li><a href="%url/rel:mpg:top%">TOP</a></li>
					<li><a href="%url/rel:mpg:top%&path=stock">在庫メニュー</a></li>
				<li><span>ロット調整</span></li>
			</ul>
			<h2 class="page_title">ロット調整</h2>
			<hr>
			<div class="uk-width-1-3@m">
				<div class="uk-margin">
					<searchable-select name="busyo" v-model="divisionId" id="divisionId" v-bind:disabled="lists.length > 0" :options="divisionOptions"></searchable-select>
				</div>
			</div>
			<div class="uk-margin-bottom">
				<div uk-margin>
					<button class="uk-button uk-button-default" v-on:click="sanshouClick">商品マスタを開く</button>
					<button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
					<button class="uk-button uk-button-primary" type="submit" v-on:click="lotRegister">ロット調整実行</button>
				</div>
			</div>

			<div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
				<form action='#' method="post" onsubmit="app.barcodeSearch($('input[name=barcode]').val() ,'' , '',true);$('input[name=barcode]').val('') ; $('input[name=barcode]').focus(); return false;">
					<input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." autofocus="true" name="barcode" autocomplete="off">
					<button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
				</form>
			</div>
			<div class="shouhin-table uk-width-expand uk-overflow-auto">
				<table class="uk-table uk-table-striped">
					<thead>
						<tr>
							<th class="uk-text-nowrap">id</th>
							<th class="uk-table-expand">メーカー</th>
							<th class="uk-table-expand">商品名</th>
							<th class="uk-table-expand">製品コード</th>
							<th class="uk-table-expand">規格</th>
							<th class="uk-table-expand">JANコード</th>
							<th class="uk-table-expand">入数</th>
							<th class="uk-table-expand">ロット番号</th>
							<th class="uk-table-expand">使用期限</th>
							<th class="uk-text-nowrap">
								調整数
							</th>
							<th class="uk-text-nowrap">
								変更理由
							</th>
							<th>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(list, key) in lists" :id="'tr_' + key">
							<td>{{list.text}}</td>
							<td>{{list.maker}}</td>
							<td>{{list.shouhinName}}</td>
							<td>{{list.code}}</td>
							<td>{{list.kikaku}}</td>
							<td>{{list.jan}}</td>
							<td class="uk-text-nowrap">{{list.irisu}}{{list.unit}}</td>
							<td>
								<input type="text" maxlength="20" class="uk-input lot" style="width: 180px;" v-model="list.lotNumber" v-bind:style="list.lotNumberStyle" v-on:change="addLotNumberStyle(key)">
							</td>
							<td>
								<input type="date" class="uk-input lotDate" v-model="list.lotDate" v-bind:style="list.lotDateStyle" v-on:change="addLotDateStyle(key)">
							</td>
							<td class="uk-text-nowrap">
								<input type="number" step="1" class="uk-input" style="width: 96px;" v-bind:style="list.countStyle" v-model="list.lotCountNum" v-on:change="addCountStyle(key)">
								<span class="uk-text-bottom">{{list.unit}}</span>
							</td>
							<td class="uk-text-nowrap">
								<textarea class="uk-textarea uk-width-small" v-model="list.changeReason" v-bind:style="list.changeReasonStyle"  v-on:change="addChangeReasonStyle(key)"></textarea>
							</td>
							<td uk-margin class="uk-text-center uk-text-nowrap">
								<input type="button" class="uk-button uk-button-danger uk-button-small" value="削除" v-on:click="deleteList(key)">
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
						</tr>
						<tr>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
						</tr>
						<tr>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
							<td>&emsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>


<!-- This is a button toggling the modal with the default close button -->
<!-- This is the modal with the default close button -->
<div id="gs1-128" uk-modal>
	<div class="uk-modal-dialog uk-modal-body">
		<form onsubmit="gs1_128.check_gs1_128($('#GS1-128').val());return false;" action="#">
			<button class="uk-modal-close" type="button" uk-close></button>
			<h2 class="uk-modal-title">GS1-128 読取</h2>
			<input type="text" class="uk-input" placeholder="GS1-128" id="GS1-128" autofocus="true">
			<p class="uk-text-right">
				<button class="uk-button uk-button-primary" type="button" onclick="gs1_128.check_gs1_128($('#GS1-128').val());">反映</button>
			</p>
		</form>
	</div>
</div>

<div id="modal-sections" class="uk-modal-container" uk-modal>
	<div class="uk-modal-dialog">
		<button class="uk-modal-close-default" type="button" uk-close></button>
		<div class="uk-modal-header">
			<h2 class="uk-modal-title">商品選択</h2>
		</div>
	<div class="uk-modal-body uk-width-expand uk-overflow-auto">
		 	<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed uk-table-divider">
				<thead>
					<tr>
						<th class="uk-text-nowrap">id</th>
						<th class="uk-text-nowrap"></th>
						<th class="uk-table-expand">メーカー</th>
						<th class="uk-table-expand">商品名</th>
						<th class="uk-table-expand">製品コード</th>
						<th class="uk-table-expand">規格</th>
						<th class="uk-table-expand">JANコード</th>
						<th class="uk-table-expand">入数</th>
						<th class="uk-table-expand">価格</th>
						<th class="uk-table-expand">卸業者</th>
						<th class="uk-table-expand">ロット管理フラグ</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(list , key) in select_items">
						<td></td>
						<td><button type="button" v-on:click="addObject(key)" class="uk-text-nowrap uk-button uk-button-primary uk-button-small">反映</button></td>
						<td class="uk-text-middle">{{list.maker}}</td>
						<td class="uk-text-middle">{{list.shouhinName}}</td>
						<td class="uk-text-middle">{{list.code}}</td>
						<td class="uk-text-middle">{{list.kikaku}}</td>
						<td class="uk-text-middle">{{list.jan}}</td>
						<td class="uk-text-middle">
						<span class="irisu uk-text-nowrap">{{list.irisu}}</span><span class="unit uk-text-small">{{list.unit}}</span>
						</td>
						<td class="uk-text-middle">￥{{list.kakaku}}</td>
						<td class="uk-text-middle">{{list.oroshi}}</td>
						<td class="uk-text-middle">{{list.lotFlag}}</td>
					</tr>
				</tbody>
			</table>   
		</div>
	</div>
</div>

<form action="<?php echo $api_url; ?>" target="_blank" method="post" class="print_hidden uk-inline" id="createLabelForm">
	<input type="hidden" value="" name="card_ids">
	<input type="hidden" value="cardLabelPrint" name="Action">
</form>

<script>

	
<?php
$options = [
    [
        'value' => '',
        'text' => '----- 部署を選択してください -----',
    ],
];
foreach ($division->data as $data) {
    if ($data->divisionType === '1') {
        $options[] = [
            'value' => $data->divisionId,
            'text' => $data->divisionName,
        ];
    }
}
foreach ($division->data as $data) {
    if ($data->divisionType === '2') {
        $options[] = [
            'value' => $data->divisionId,
            'text' => $data->divisionName,
        ];
    }
}
$defaultDivisionId = $user_info->isUser() ? $user_info->getDivisionId() : '';
?>
var app = new Vue({
	el: '#app',
	data: {
		lists: [],
		divisionOptions: <?php echo json_encode($options); ?>,
		divisionId: "<?php echo $defaultDivisionId; ?>",
	},
	watch: {
	},
	methods: {
		addList: function(object) {
			object.lotCountNum = 0;
			object.lotDateStyle = {};
			object.lotNumberStyle = {};
			object.countStyle = {};
			object.changeReason = "";
			object.changeReason = "";
			app.lists.push(object);
			
		},
		lotRegister: function() {
			UIkit.modal.confirm("ロット調整を行います。<br>よろしいでしょうか。<br>※在庫数は変動しません").then(function(){
				if(!app.check()){
					return false;
				}
				if(!app.checkLot()){
					return false;
				}
				loading();
				$.ajax({
					async: false,
					url: "<?php echo $api_url; ?>",
					type:'POST',
					data:{
						_csrf: "<?php echo $csrf_token; ?>",  // CSRFトークンを送信
						Action : 'lotRegister',
						lots : JSON.stringify( objectValueToURIencode(app.lists) ),
						divisionId : app.divisionId,
					},
					dataType: 'json'
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
					if(data.code != 0){
						UIkit.modal.alert("ロット調整に失敗しました");
						return false;
					} 
					UIkit.modal.alert("ロット調整が完了しました").then(function(){
						app.lists.splice(0, app.lists.length);
					});
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
					UIkit.modal.alert("ロット調整に失敗しました");
				})
				// Ajaxリクエストが成功・失敗どちらでも発動
				.always( (data) => {
					loading_remove();
				});
				
			}, function () {
			});
		},
		deleteList: function(key) {
			this.lists.splice(key, 1);
		},
		sanshouClick: function() {
			if(! this.checkDivision())
			{
				return false;
			}
			window.open('%url/rel:mpgt:page_175973%', '_blank','scrollbars=yes,width=1220,height=600');
		},
		checkDivision: function(){
			if(app.divisionId == ''){
				UIkit.modal.alert('部署を選択してください');
				return false ;
			}
			return true;
		},
		checkLot: function(){
			let chkLot = true;
			app.lists.forEach(function (elem, index) {
				elem.lotNumberStyle.border = '';
				elem.lotDateStyle.border = '';
				if( !( app.lists[index].lotNumber && app.lists[index].lotDate)) {
					let changeObject = app.lists[index];
					changeObject.lotNumberStyle.border = 'red 2px solid';
					changeObject.lotDateStyle.border = 'red 2px solid';
					app.$set(app.lists, index, changeObject);
					chkLot = false;
				}
			});
			if(!chkLot){
				UIkit.modal.alert('ロット情報を入力してください');
				return false ;
			}
			
			chkLot = true;
			let regex = /^[a-zA-Z0-9!-/:-@¥[-`{-~]+$/;
			app.lists.forEach(function (elem, index) {
				elem.lotDateStyle.border = '';
				if(app.lists[index].lotNumber) {
					if((!regex.test(app.lists[index].lotNumber)) ||
					   (encodeURI(app.lists[index].lotNumber).replace(/%../g, '*').length > 20)) {
						let changeObject = app.lists[index];
						changeObject.lotNumberStyle.border = 'red 2px solid';
						app.$set(app.lists, index, changeObject);
						chkLot = false;
					}
				}
			});
			
			if(!chkLot){
				UIkit.modal.alert('ロット番号の入力を確認してください<br>英数20桁以内で入力してください');
				return false ;
			}
			
			return true;
		},
		check: function(){
			
			if(app.divisionId == ''){
				UIkit.modal.alert('部署を選択してください');
				return false ;
			}
			
			if(app.lists.length === 0){
				UIkit.modal.alert('商品を選択してください');
				return false ;
			}
			
			let chk = true;
			app.lists.forEach(function (elem, index) {
				elem.countStyle.border = '';
				if( app.lists[index].lotCountNum == "" ) {
					let changeObject = app.lists[index];
					changeObject.countStyle.border = 'red 2px solid';
					app.$set(app.lists, index, changeObject);
					chk = false;
				}
			});
			if(!chk){
				UIkit.modal.alert('調整数を確認してください');
				return false ;
			}


			let checkflg = true;
			let errormsg = new Object();
			errormsg.changeReason = "";
			app.lists.forEach(function(elem, index) {
				let checkObject = app.lists[index];
				if(checkObject.changeReason.bytes() > 1024 || checkObject.changeReason.bytes() == 0) {
					errormsg.changeReason = "<br>変更理由：半角1024文字全角512文字以内で入力してください";
					checkObject.changeReasonStyle = { 'border' : "2px solid red"};
					checkflg = false;
				}
				app.$set(app.lists, index, checkObject);
			});
			
			if(!checkflg)
			{
				UIkit.modal.alert('値が正しく入力されていません'+ errormsg.changeReason );
				return false;
			}

			return true;
		},
		addLotNumberStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.lotNumberStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.$set(app.lists, index, changeObject);
		},
		addLotDateStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.lotDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.$set(app.lists, index, changeObject);
		},
		addCountStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.$set(app.lists, index, changeObject);
		},
		addChangeReasonStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.changeReasonStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.$set(app.lists, index, changeObject);
		},
		
		barcodeSearch: function(barcode , lotNumber , lotDate , gs1_128_search_flg) {
			if(! this.checkDivision()){
				return false;
			}
			$.ajax({
				async: false,
				url:'%url/rel:mpgt:labelBarcodeSAPI%',
				type:'POST',
				data:{
					_csrf: "<?php echo $csrf_token; ?>",  // CSRFトークンを送信
					divisionId : app.divisionId,
					barcode : barcode,
				},
				dataType: 'json'
			})
			// Ajaxリクエストが成功した時発動
			.done( (data) => {
				let value = 0;
				if(data.code != 0 || data.data.length == 0){
					if(gs1_128_search_flg)
					{
						gs1_128.check_gs1_128(barcode);
					} else {
						UIkit.modal.alert("商品が見つかりませんでした");
					}
					return false;
				}
				if(data.count == 1)
				{
					data = data.data;
					data.lotNumber = lotNumber;
					data.lotDate = lotDate;
					app.addList(data);
					
					$('input[name="barcode"]').val('');
				} else {
					data = data.data;
					modal_sections.clear();
					for(let num = 0 ; num < data.length ; num++)
					{
						data[num].lotNumber = lotNumber;
						data[num].lotDate = lotDate;
						modal_sections.addList(data[num]);
					}
					UIkit.modal.alert("複数の商品が見つかりました").then(function(){
						modal_sections.openModal();
					});
				}
			})
			// Ajaxリクエストが失敗した時発動
			.fail( (data) => {
				UIkit.modal.alert("商品が見つかりませんでした");
			})
			// Ajaxリクエストが成功・失敗どちらでも発動
			.always( (data) => {
				loading_remove();
			});
		}
	}
});

var modal_sections = new Vue({
	el:	'#modal-sections',
	data: {
		select_items: [],
	},
	methods: {
		clear: function(){
			let original = JSON.parse(JSON.stringify(this.select_items));
			this.select_items.splice(0, original.length);
		},
		addList: function(object){
			this.select_items.push(object);
		},
		addObject: function(index){
			app.addList(JSON.parse(JSON.stringify(this.select_items[index])));
		},
		openModal: function(){
			UIkit.modal('#modal-sections').show();
		}
	}
});

var gs1_128 = new Vue({
	data: {
		gs1_128: {}
	},
	methods: {
		changeDate: function (text){
			if(!text){
				return "";
			}
			if(text.length == "6"){
				text = 20 + text;
			}
			let date = text.slice(6, 8);
			if(parseInt(text.slice(6, 8)) == 0){
				date = '01';
			}
			return text.slice(0, 4) + "-" + text.slice(4, 6) + "-" + date;
		},
		check_gs1_128: function (gs1128)
		{
			if(gs1128.indexOf("]C1") !== 0){
				//UIkit.modal.alert("GS1-128ではありません");
				//return ;
				return this.check_gs1_128("]C1"+gs1128);
			}
			
			gs1128 = gs1128.slice( 3 );
			let obj = check_gs1128(gs1128);
				
			if(!obj.hasOwnProperty("01")){
				UIkit.modal.alert("商品情報が含まれておりませんでした。").then(function(){
					UIkit.modal($('#gs1-128')).show();
				});
				return;
			}
			
			let searchJan = gs1_01_to_jan(obj["01"]);
			let objkey = null;
			let setObj = {};
			
			let objLotNumber = (obj["10"] === void 0) ? "" : obj["10"]; //lotNumber
			let objLotDate = (obj["17"] === void 0) ? "" : this.changeDate(obj["17"]); //lotDate
			if(objLotDate === ""){
				objLotDate = (obj["7003"] === void 0) ? "" : this.changeDate(obj["7003"]); //lotDate
			}
			let existflg = false;
			let changeObject = null;
			
			if(!existflg){
				app.barcodeSearch(searchJan,objLotNumber,objLotDate , false );
				//UIkit.modal.alert("対象の発注商品が見つかりませんでした。").then(function(){
				//	UIkit.modal($('#gs1-128')).show();
				//});
				return;
			} 
			
			$("#GS1-128").val("");
			document.getElementById("GS1-128").focus();
		}
	}
});

function addTr(object, type, count){
	app.addList(object);
}
</script>