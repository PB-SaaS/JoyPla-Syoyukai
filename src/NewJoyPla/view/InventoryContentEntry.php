<style>
	.id {
		min-width: 24px;
		max-width: 24px;
	}

	.shouhin-data .itemName {
		font-size: 14px;
	}

	.shouhin-data .makerName {
		font-size: 12px;

	}

	.shouhin-data .tana {
		font-size: 8px;
	}

	.shouhin-data .itemCode {
		font-size: 8px;

	}

	.shouhin-data .constant {
		font-size: 8px;

	}

	.shouhin-data .quantity {
		font-size: 8px;

	}

	.shouhin-data .price {
		font-size: 8px;

	}

	.shouhin-data .JANCode {
		font-size: 8px;

	}

	.shouhin-data .officialFlag {
		font-size: 8px;

	}

	.itemCount {
		position: relative;
	}

	.itemCount->after {
		content: attr(data-format);
		/* ここが重要!! */
		width: 10%;
		height: 20px;
		position: absolute;
		bottom: 4px;
	}

	.itemCountInput {
		width: 90%;
	}

	.uk-table th,
	.uk-table td {
		word-break: break-word;
		padding: 12px 8px;
		vertical-align: middle;
	}

	.uk-table tfoot tr {
		border-bottom: #e5e5e5 1px solid;
		border-top: #e5e5e5 1px solid;
	}

	table.uk-table {
		counter-reset: rowCount;
	}

	table.uk-table>tbody>tr {
		counter-increment: rowCount;
	}

	table.uk-table>tbody>tr>td:first-child::before {
		content: counter(rowCount);
	}

	.asc::after {
		content: "▲";
	}

	.desc::after {
		content: "▼";
	}
	.multiselect__tags {
		border-radius: 0px !important; 
		height:40px !important;
	}
</style>
<div id="app" class="animsition" uk-height-viewport="expand: true">
	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		<div class="uk-container uk-container-expand">
			<ul class="uk-breadcrumb">
				<li><a href="%url/rel:mpg:top%">TOP</a></li>
				<li><a href="%url/rel:mpg:top%&path=stocktaking">棚卸メニュー</a></li>
				<li><span>棚卸内容入力</span></li>
			</ul>
			<h2 class="page_title">棚卸内容入力</h2>
			<hr>
			<div class="uk-width-1-2@m">
				<div class="uk-margin uk-flex">
					<searchable-select name="busyo" v-model="divisionId" id="divisionId" v-bind:disabled="lists.length > 0" :options="divisionOptions"></searchable-select>
					<button class="uk-button uk-button-default" style="white-space: nowrap;" v-on:click="getTemporaryData" v-bind:disabled="divisionId == '' || lists.length > 0">一時保存情報取得</button>
				</div>
			</div>
			<div class="uk-margin-bottom">
				<div class="" uk-margin>
					<button class="uk-button uk-button-default" v-on:click="sanshouClick">商品マスタを開く</button>
					<button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
					<button class="uk-button uk-button-primary" v-on:click="sendInventory" :disabled="isTemporaryData" v-if="!isTemporaryData">一時保存</button>
					<button class="uk-button uk-button-primary" v-on:click="sendInventory" :disabled="!isTemporaryData" v-if="isTemporaryData">上書き保存</button>
					<button class="uk-button uk-button-primary" v-on:click="getLotAndStock" v-bind:disabled="lists.length > 0">理論在庫から表を作成</button>
					<button class="uk-button uk-button-primary" v-on:click="getStocktakingList" v-bind:disabled="lists.length > 0">理論在庫と棚卸商品管理表から表を作成</button>
				</div>
			</div>

			<div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
				<form action='#' method="post" onsubmit="app.barcodeSearch($('input[name=barcode]').val() , '' ,'' ,true);$('input[name=barcode]').val('') ; $('input[name=barcode]').focus();return false;">
					<input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." name="barcode" autocomplete="off">
					<button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
				</form>
			</div>
			<div class="uk-width-2-3 uk-margin-auto uk-margin-top">
				<ul uk-accordion>
					<li>
						<a class="uk-accordion-title" href="#">絞り込み</a>
						<div class="uk-accordion-content">
							<form class="uk-form-stacked">
								<div class="uk-margin">
									<label class="uk-form-label" for="form-itemName-text">商品名</label>
									<div class="uk-form-controls">
										<input class="uk-input" id="form-itemName-text" v-model="search.itemName" type="text" placeholder="Some text...">
									</div>
								</div>
								<div class="uk-margin">
									<label class="uk-form-label" for="form-makerName-text">メーカー名</label>
									<div class="uk-form-controls">
										<input class="uk-input" id="form-makerName-text" v-model="search.makerName" type="text" placeholder="Some text...">
									</div>
								</div>
								
								<div class="uk-margin">
									<label class="uk-form-label" for="form-makerName-text">卸業者名</label>
									<div class="uk-form-controls">
										<multiselect :multiple='true' :close-on-select="false" :clear-on-select="false" v-model="search.distributorIds" label="text" track-by="value" name="filter-distributor" :options="distributors">
											<template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length" v-show="!isOpen">{{ values.length }} options selected</span></template>
										</multiselect>
									</div>
								</div>


								<div class="uk-margin">
									<div class="uk-form-label">棚卸必須</div>
									<div class="uk-form-controls">
										<label><input class="uk-checkbox" type="checkbox" name="check1" value="t" v-model="search.mandatory"> 必須</label><br>
										<label><input class="uk-checkbox" type="checkbox" name="check1" value="f" v-model="search.mandatory"> 任意</label>
									</div>
								</div>

							</form>
						</div>
					</li>
				</ul>
				
			</div>
			<div class="shouhin-table uk-width-expand uk-overflow-auto">
				<table class="uk-table uk-table-striped">
					<thead>
						<tr>
							<th class="uk-text-nowrap">id</th>
							<th class="uk-table-expand">
								<a href="#" @click="sortBy('rackName')" :class="addClass('rackName')">棚名</a>
							</th>
							<th class="uk-table-expand">
								<a href="#" @click="sortBy('mandatoryFlag')" :class="addClass('mandatoryFlag')">棚卸必須</a>
							</th>
							<th class="uk-table-expand">
								<a href="#" @click="sortBy('maker')" :class="addClass('maker')">メーカー</a>
							</th>
							<th class="uk-table-expand">
								<a href="#" @click="sortBy('shouhinName')" :class="addClass('shouhinName')">商品名</a>
							</th>
							<th class="uk-table-expand">
								<a href="#" @click="sortBy('code')" :class="addClass('code')">製品コード</a>
							</th>
							<th class="uk-table-expand">
								<a href="#" @click="sortBy('kikaku')" :class="addClass('kikaku')">規格</a>
							</th>
							<th class="uk-table-expand">
								<a href="#" @click="sortBy('jan')" :class="addClass('jan')">JANコード</a>
							</th>
							<th class="uk-table-expand">
								<a href="#" @click="sortBy('oroshi')" :class="addClass('oroshi')">卸業者</a>
							</th>
							<th class="uk-text-nowrap">
								<a href="#" @click="sortBy('irisu')" :class="addClass('irisu')">入数</a>
							</th>
							<th class="uk-text-nowrap">
								<a href="#" @click="sortBy('kakaku')" :class="addClass('kakaku')">価格</a>
							</th>
							<th class="uk-text-nowrap">
								<a href="#" @click="sortBy('unitPrice')" :class="addClass('unitPrice')">単価</a>
							</th>
							<th class="uk-text-nowrap" style="padding-right: 5px;">棚卸数量
							</th>
							<th class="uk-text-nowrap" style="padding-left: 0px;">
								<input type="button" class="uk-button uk-button-default uk-button-small" v-on:click="countToIrisu" value="入数を反映">
							</th>
							<th class="uk-text-nowrap">
								<a href="#" @click="sortBy('lotFlagBool')" :class="addClass('lotFlagBool')">ロット管理</a>
							</th>
							<th class="uk-table-expand">ロット番号
							</th>
							<th class="uk-table-expand">使用期限
							</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(list, key) in sort_lists" :id="'tr_' + key" v-bind:class="list.class">
							<td class="uk-text-nowrap"></td>
							<td>{{list.rackName}}</td>
							<td>
								<span v-if="list.mandatoryFlag==1">必須</span>
							</td>
							<td>{{list.maker}}</td>
							<td>{{list.shouhinName}}</td>
							<td>{{list.code}}</td>
							<td>{{list.kikaku}}</td>
							<td>{{list.jan}}</td>
							<td>{{list.oroshi}}</td>
							<td class="uk-text-nowrap">{{list.irisu | number_format}}{{list.unit}}</td>
							<td class="uk-text-nowrap">￥{{list.kakaku | number_format}}</td>
							<td class="uk-text-nowrap">
								￥<span v-if="useUnitPrice == 1">{{list.unitPrice | number_format}}</span>
								<span v-else>{{(list.kakaku / list.irisu)| number_format}}</span>
							</td>
							<td class="uk-text-nowrap" colspan="2">
								<input type="number" step="1" min="0" class="uk-input" style="width: 96px;" v-bind:style="list.countStyle" v-model="list.countNum" v-bind:disabled="list.countNumDisabled" v-on:change="addCountStyle(key)">
								<span class="uk-text-bottom">{{list.unit}}</span>
							</td>
							<td class="uk-text-nowrap">
								<span v-if="list.lotFlagBool == 1" class="uk-text-danger">必須</span>
								<span v-else>任意</span>
							</td>
							<td class="uk-text-nowrap">
								<input type="text" class="uk-input lot" style="width:180px" maxlength="20" v-model="list.lotNumber" v-bind:style="list.lotNumberStyle" v-on:change="addLotNumberStyle(key)">
							</td>
							<td class="uk-text-nowrap">
								<input type="date" class="uk-input lotDate" v-model="list.lotDate" v-bind:style="list.lotDateStyle" v-on:change="addLotDateStyle(key)">
							</td>
							<td class="uk-text-nowrap">
								<input type="button" class="uk-button uk-button-danger uk-button-small" value="削除" v-on:click="deleteList(key)">
							</td>
							<td class="uk-text-nowrap">
								<input type="button" class="uk-button uk-button-default uk-button-small" value="追加" v-on:click="copyList(key)">
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
						<th class="uk-text-nowrap uk-table-shrink">id</th>
						<th class="uk-table-shrink"></th>
						<th class="uk-table-expand">メーカー</th>
						<th class="uk-table-expand">商品名</th>
						<th class="uk-table-expand">製品コード</th>
						<th class="uk-table-expand">規格</th>
						<th class="uk-table-expand">JANコード</th>
						<th class="uk-text-nowrap uk-table-shrink">入数</th>
						<th class="uk-text-nowrap uk-table-shrink">価格</th>
						<th class="uk-text-nowrap uk-table-shrink">単価</th>
						<th class="uk-table-expand">卸業者</th>
						<th class="uk-text-nowrap uk-table-shrink">ロット管理フラグ</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(list , key) in select_items">
						<td></td>
						<td><button type="button" v-on:click="addObject(key)" class="uk-text-nowrap uk-button uk-button-primary uk-button-small">反映</button></td>
						<td class="uk-text-nowrap uk-text-middle">{{list.maker}}</td>
						<td class="uk-text-middle">{{list.shouhinName}}</td>
						<td class="uk-text-middle">{{list.code}}</td>
						<td class="uk-text-middle">{{list.kikaku}}</td>
						<td class="uk-text-middle">{{list.jan}}</td>
						<td class="uk-text-middle">
							<span class="irisu">{{list.irisu | number_format}}</span><span class="unit uk-text-small">{{list.unit}}</span>
						</td>
						<td class="uk-text-nowrap uk-text-middle">￥{{list.kakaku}}</td>
						<td class="uk-text-nowrap uk-text-middle">
							￥<span v-if="useUnitPrice == 1">{{list.unitPrice | number_format}}</span>
							<span v-else>{{(list.kakaku / list.irisu)| number_format}}</span>
						</td>
						<td class="uk-text-nowrap uk-text-middle">{{list.oroshi}}</td>
						<td class="uk-text-nowrap uk-text-middle">{{list.lotFlag}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

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
		components: {
			Multiselect: window.VueMultiselect.default
		},
		data: {
			lists: [],
			isTemporaryData: false,
        	divisionOptions: <?php echo json_encode($options); ?>,
        	distributors: <?php echo json_encode($distributorOptions); ?>,
			divisionId: "<?php echo $defaultDivisionId; ?>",
			rackNames: [],
			search: {
				makerName : '',
				itemName: '',
				distributorIds: [],
				mandatory: [],
			},
			sort_key: "",
			sort_asc: true,
			useUnitPrice: parseInt(<?php echo json_encode($useUnitPrice); ?>),
		},
		computed: {
			sort_lists() {
				let temp = this.lists;
				if (this.sort_key != "") {
					let set = 1;
					this.sort_asc ? (set = 1) : (set = -1);
					temp.sort((a, b) => {
						if (a[this.sort_key] < b[this.sort_key]) return -1 * set;
						if (a[this.sort_key] > b[this.sort_key]) return 1 * set;
						return 0;
					});
				}

				if(this.search.itemName != ''){
					let lowerCaseSearchText = this.search.itemName.toLowerCase();
					temp = temp.filter(item => item.shouhinName.toLowerCase().includes(lowerCaseSearchText));
				}
				
				if(this.search.makerName != ''){
					let lowerCaseSearchText = this.search.makerName.toLowerCase();
					temp = temp.filter(item => item.maker.toLowerCase().includes(lowerCaseSearchText));
				}

				if(this.search.distributorIds.length > 0){
					let searchTerms = this.search.distributorIds.map(item => item.value);
					temp = temp.filter(item => searchTerms.includes(item.distributorId));
				}

				if(this.search.mandatory.length === 1 && this.search.mandatory.includes('t')){
					temp = temp.filter(item => item.mandatoryFlag == '1');
				}
				
				if(this.search.mandatory.length === 1 && this.search.mandatory.includes('f')){
					temp = temp.filter(item => item.mandatoryFlag == '0');
				}

				return temp;
			},
		},
		filters: {
			number_format: function(value) {
				if (!value) {
					return 0;
				}
				return new Intl.NumberFormat('ja-JP').format(value);
			},
		},
		watch: {
			divisionId: function() {
				this.getRackNames(this.divisionId);
			},
			lists: function() {
				this.$nextTick(function() {
					if ($('.target').length > 0) {
						$(window).scrollTop($('.target').offset().top - 100);
						app.lists.forEach(function(elem, index) {
							let changeObject = null;
							changeObject = app.lists[index];
							changeObject.class.target = false;
							app.$set(app.lists, index, changeObject);
						});
					}
				})
			}
		},
		methods: {
			addClass(key) {
				return {
					asc: this.sort_key === key && this.sort_asc,
					desc: this.sort_key === key && !this.sort_asc,
				};
			},
			sortBy(key) {
				this.sort_key === key ?
					(this.sort_asc = !this.sort_asc) :
					(this.sort_asc = true);
				this.sort_key = key;
			},
			addList: function(object) {
				object.class = ((object.class == null) ? {
					'target': true
				} : object.class);
				object.countNum = ((object.countNum == null) ? 0 : object.countNum);
				object.lotNumber = ((object.lotNumber == null) ? '' : object.lotNumber);
				object.lotDate = ((object.lotDate == null) ? '' : object.lotDate);
				object.countStyle = ((object.countStyle == null) ? {} : object.countStyle);
				object.lotDateStyle = ((object.lotDateStyle == null) ? {} : object.lotDateStyle);
				object.lotNumberStyle = ((object.lotNumberStyle == null) ? {} : object.lotNumberStyle);
				let rackName = this.rackNames.find((x) => (x.inHospitalItemId === object.recordId));
				object.rackName = "";
				if (rackName) {
					object.rackName = rackName.rackName;
				}
				object.mandatoryFlag = ((object.mandatoryFlag == null) ? 0 : object.mandatoryFlag);
				this.lists.push(object);
			},
			copyList: function(key) {
				let original = JSON.parse(JSON.stringify(this.lists));
				this.lists.splice(0, original.length);
				let num = 0;
				for (num; num <= key; num++) {
					this.addList(JSON.parse(JSON.stringify(original[num])));
				}

				let copy = JSON.parse(JSON.stringify(original[key]));
				copy.countNum = 0;
				copy.class.target = true;
				copy.lotNumber = null;
				copy.lotDate = null;
				copy.countStyle = null;
				copy.lotDateStyle = null;
				copy.lotNumberStyle = null;

				this.addList(copy); //コピー

				for (num; num < original.length; num++) {
					this.addList(JSON.parse(JSON.stringify(original[num])));
				}

			},
			deleteList: function(key) {
				this.lists.splice(key, 1);
			},
			sanshouClick: function() {
				if (!this.divisionCheck()) {
					return false;
				}
				window.open('%url/rel:mpgt:page_175973%', '_blank', 'scrollbars=yes,width=1220,height=600');
			},

			divisionCheck: function() {
				if (!this.divisionId) {
					UIkit.modal.alert('部署を選択してください');
					return false;
				}
				return true;
			},
			countToIrisu: function() {
				UIkit.modal.confirm("数量に入数を自動挿入しますか。<br>数量0に設定されている商品が対象です").then(function() {
					app.lists.forEach(function(elem, index) {
						let changeObject = null;
						if (app.lists[index].countNum == 0) {
							changeObject = app.lists[index];
							changeObject.countNum = changeObject.irisu;
							app.$set(app.lists, index, changeObject);
							app.addCountStyle(index);
						}
					});
				}, function() {});
			},
			addCountStyle: function(index) {
				let changeObject = app.lists[index];
				changeObject.countStyle = {
					'backgroundColor': "rgb(255, 204, 153)",
					'color': "rgb(68, 68, 68)"
				};
				app.$set(app.lists, index, changeObject);
			},
			addLotNumberStyle: function(index) {
				let changeObject = app.lists[index];
				changeObject.lotNumberStyle = {
					'backgroundColor': "rgb(255, 204, 153)",
					'color': "rgb(68, 68, 68)"
				};
				app.$set(app.lists, index, changeObject);
			},
			addLotDateStyle: function(index) {
				let changeObject = app.lists[index];
				changeObject.lotDateStyle = {
					'backgroundColor': "rgb(255, 204, 153)",
					'color': "rgb(68, 68, 68)"
				};
				app.$set(app.lists, index, changeObject);
			},
			isCard: function(barcode) {
				return (barcode.length == 18 && barcode.startsWith('90'));
			},

			cardCheck: function(barcode) {
				let exist = false;
				app.lists.forEach(function(elem, index) {
					let changeObject = null;
					if (app.lists[index].card == barcode) {
						exist = true;
					}
				});
				return exist;
			},
			barcodeSearch: function(barcode, lotNumber, lotDate, gs1_128_search_flg) {

				if (!app.divisionCheck()) {
					return false;
				}

				if (app.isCard(barcode)) {
					if (app.cardCheck(barcode)) {
						UIkit.modal.alert("すでに読み込んでいるカードです");
						return false;
					}
				}

				$.ajax({
						async: false,
						url: '%url/rel:mpgt:labelBarcodeSAPI%',
						type: 'POST',
						data: {
							_csrf: "<?php echo $csrf_token; ?>", // CSRFトークンを送信
							divisionId: app.divisionId,
							barcode: barcode,
						},
						dataType: 'json'
					})
					// Ajaxリクエストが成功した時発動
					.done((data) => {
						let value = 0;
						if (data.code != 0 || data.data.length == 0) {
							if (gs1_128_search_flg) {
								gs1_128.check_gs1_128(barcode);
							} else {
								UIkit.modal.alert("商品が見つかりませんでした");
							}
							return false;
						}
						if (data.count == 1) {
							data = data.data;

							if (data.divisionId != "" && app.divisionId != data.divisionId) {
								UIkit.modal.alert("読み込んだバーコードの部署が払出元の部署と一致しません");
								return false;
							}
							if (lotNumber != '') {
								data.lotNumber = lotNumber;
							}
							if (lotDate != '') {
								data.lotDate = lotDate;
							}
							data.countNum = data.count;
							data.labelCountStyle = {
								'backgroundColor': "rgb(255, 204, 153)",
								'color': "rgb(68, 68, 68)"
							};
							data.countStyle = {
								'backgroundColor': "rgb(255, 204, 153)",
								'color': "rgb(68, 68, 68)"
							};
							data.lotNumberStyle = {
								'backgroundColor': "rgb(255, 204, 153)",
								'color': "rgb(68, 68, 68)"
							};
							data.lotDateStyle = {
								'backgroundColor': "rgb(255, 204, 153)",
								'color': "rgb(68, 68, 68)"
							};
							if (app.isCard(barcode)) {
								data.card = barcode;
							}
							this.addList(data);

							$('input[name="barcode"]').val('');
						} else {
							data = data.data;
							modal_sections.clear();
							for (let num = 0; num < data.length; num++) {
								data[num].lotNumber = lotNumber;
								data[num].lotDate = lotDate;
								modal_sections.addList(data[num]);
							}
							UIkit.modal.alert("複数の商品が見つかりました").then(function() {
								modal_sections.openModal();
							});
						}
					})
					// Ajaxリクエストが失敗した時発動
					.fail((data) => {
						UIkit.modal.alert("商品が見つかりませんでした");
					})
					// Ajaxリクエストが成功・失敗どちらでも発動
					.always((data) => {
						loading_remove();
					});
			},

			validationCheck: function() {
				if (app.lists.length == 0) {
					UIkit.modal.alert('商品を選択してください');
					return false;
				}

				checkflg = true;
				app.lists.forEach(function(elem, index) {
					elem.countStyle.border = '';
					if (app.lists[index].countNum < 0) {
						let changeObject = app.lists[index];
						changeObject.countStyle.border = 'red 2px solid';
						app.$set(app.lists, index, changeObject);
						checkflg = false;
					}
				});

				if (!checkflg) {
					UIkit.modal.alert('プラスの値を入力してください');
					return false;
				}

				let chkLot = true;
				app.lists.forEach(function(elem, index) {
					elem.lotNumberStyle.border = '';
					elem.lotDateStyle.border = '';
					if (app.lists[index].countNum > 0 && app.lists[index].lotFlagBool == 1) {
						if (!(app.lists[index].lotNumber && app.lists[index].lotDate)) {
							let changeObject = app.lists[index];
							changeObject.lotNumberStyle.border = 'red 2px solid';
							changeObject.lotDateStyle.border = 'red 2px solid';
							app.$set(app.lists, index, changeObject);
							chkLot = false;
						}
					}
				});
				if (!chkLot) {
					UIkit.modal.alert('ロット管理が必須のものはすべてロット情報を入力してください');
					return false;
				}

				chkLot = true;
				app.lists.forEach(function(elem, index) {
					elem.lotNumberStyle.border = '';
					elem.lotDateStyle.border = '';
					if (app.lists[index].countNum > 0) {
						if (!app.lists[index].lotNumber && app.lists[index].lotDate) {
							let changeObject = app.lists[index];
							changeObject.lotNumberStyle.border = 'red 2px solid';
							app.$set(app.lists, index, changeObject);
							chkLot = false;
						} else if (app.lists[index].lotNumber && !app.lists[index].lotDate) {
							let changeObject = app.lists[index];
							changeObject.lotDateStyle.border = 'red 2px solid';
							app.$set(app.lists, index, changeObject);
							chkLot = false;
						}
					}
				});

				if (!chkLot) {
					UIkit.modal.alert('ロット情報を入力してください');
					return false;
				}


				chkLot = true;
				let regex = /^[a-zA-Z0-9!-/:-@¥[-`{-~]+$/;
				app.lists.forEach(function(elem, index) {
					elem.lotNumberStyle.border = '';
					if (app.lists[index].lotNumber) {
						if ((!regex.test(app.lists[index].lotNumber)) ||
							(encodeURI(app.lists[index].lotNumber).replace(/%../g, '*').length > 20)) {
							let changeObject = app.lists[index];
							changeObject.lotNumberStyle.border = 'red 2px solid';
							app.$set(app.lists, index, changeObject);
							chkLot = false;
						}
					}
				});

				if (!chkLot) {
					UIkit.modal.alert('ロット番号の入力を確認してください');
					return false;
				}

				return true;

			},
			sendInventory: function() {
				let keywd = (app.isTemporaryData) ? "上書き保存" : "一時保存";

				UIkit.modal.confirm('棚卸の' + keywd + 'を行います').then(function() {
					if (!app.validationCheck()) {
						return false;
					}
					$.ajax({
							async: false,
							url: "<?php echo $api_url; ?>",
							type: 'POST',
							data: {
								_csrf: "<?php echo $csrf_token; ?>", // CSRFトークンを送信
								Action: "inventoryRegistApi",
								isTemporaryData: app.isTemporaryData,
								inventory: JSON.stringify(objectValueToURIencode(app.lists)),
								divisionId: app.divisionId,
							},
							dataType: 'json'
						})
						// Ajaxリクエストが成功した時発動
						.done((data) => {
							if (data.code == '1') {
								UIkit.modal.alert(data.message);
								return false;
							}
							if (!data.result) {
								UIkit.modal.alert("棚卸" + keywd + "に失敗しました");
								return false;
							}
							UIkit.modal.alert("棚卸" + keywd + "が完了しました").then(function() {
								app.lists.splice(0, app.lists.length);
								app.isTemporaryData = false;
							});
						})
						// Ajaxリクエストが失敗した時発動
						.fail((data) => {
							UIkit.modal.alert("棚卸" + keywd + "に失敗しました");
						})
						// Ajaxリクエストが成功・失敗どちらでも発動
						.always((data) => {
							loading_remove();
						});

				});
			},

			getTemporaryData: function() {
				UIkit.modal.confirm('一時保存されている情報を取得します').then(function() {
					if (!app.divisionCheck()) {
						return false;
					}
					$.ajax({
							async: false,
							url: "<?php echo $api_url; ?>",
							type: 'POST',
							data: {
								_csrf: "<?php echo $csrf_token; ?>", // CSRFトークンを送信
								Action: "getTemporaryData",
								divisionId: app.divisionId,
							},
							dataType: 'json'
						})
						// Ajaxリクエストが成功した時発動
						.done((data) => {
							if (data.code == '1') {
								UIkit.modal.alert(data.message);
								return false;
							}
							if (!data.result) {
								UIkit.modal.alert("取得に失敗しました");
								return false;
							}
							if (data.count > 0) {
								app.isTemporaryData = true;
								data.data.forEach(function(elem, index) {
									app.addList(elem);
								});
							} else {
								UIkit.modal.alert("データがありませんでした");
							}
						})
						// Ajaxリクエストが失敗した時発動
						.fail((data) => {
							UIkit.modal.alert("取得に失敗しました");
						})
						// Ajaxリクエストが成功・失敗どちらでも発動
						.always((data) => {
							loading_remove();
						});

				});
			},
			getLotAndStock: function() {
				UIkit.modal.confirm('在庫表とロット一覧から理論在庫を取得します').then(function() {
					if (!app.divisionCheck()) {
						return false;
					}
					$.ajax({
							async: false,
							url: "<?php echo $api_url; ?>",
							type: 'POST',
							data: {
								_csrf: "<?php echo $csrf_token; ?>", // CSRFトークンを送信
								Action: "getLotAndStockApi",
								divisionId: app.divisionId,
							},
							dataType: 'json'
						})
						// Ajaxリクエストが成功した時発動
						.done((data) => {
							if (data.code == '1') {
								UIkit.modal.alert(data.message);
								return false;
							}
							if (!data.result) {
								UIkit.modal.alert("取得に失敗しました");
								return false;
							}
							if (data.count > 0) {
								data.data.forEach(function(elem, index) {
									app.addList(elem);
								});
							} else {
								UIkit.modal.alert("データがありませんでした");
							}
						})
						// Ajaxリクエストが失敗した時発動
						.fail((data) => {
							UIkit.modal.alert("取得に失敗しました");
						})
						// Ajaxリクエストが成功・失敗どちらでも発動
						.always((data) => {
							loading_remove();
						});
				});
			},

			//棚卸商品管理表から取得
			getStocktakingList: function() {
				UIkit.modal.confirm('在庫表、ロット一覧、棚卸商品管理表から理論在庫を取得します').then(function() {
					if (!app.divisionCheck()) {
						return false;
					}
					$.ajax({
							async: false,
							url: "<?php echo $api_url; ?>",
							type: 'POST',
							data: {
								_csrf: "<?php echo $csrf_token; ?>", // CSRFトークンを送信
								Action: "getStocktakingListApi",
								divisionId: app.divisionId,
							},
							dataType: 'json'
						})
						// Ajaxリクエストが成功した時発動
						.done((data) => {
							if (data.code == '1') {
								UIkit.modal.alert(data.message);
								return false;
							}
							if (!data.result) {
								UIkit.modal.alert("取得に失敗しました");
								return false;
							}
							if (data.count > 0) {
								data.data.forEach(function(elem, index) {
									app.addList(elem);
								});
							} else {
								UIkit.modal.alert("データがありませんでした");
							}
						})
						// Ajaxリクエストが失敗した時発動
						.fail((data) => {
							UIkit.modal.alert("取得に失敗しました");
						})
						// Ajaxリクエストが成功・失敗どちらでも発動
						.always((data) => {
							loading_remove();
						});
				});
			},

			getRackNames: function(divisionId) {
				$.ajax({
						async: false,
						url: "<?php echo $api_url; ?>",
						type: 'POST',
						data: {
							_csrf: "<?php echo $csrf_token; ?>", // CSRFトークンを送信
							Action: "getRackNames",
							divisionId: divisionId,
						},
						dataType: 'json'
					})
					// Ajaxリクエストが成功した時発動
					.done((data) => {
						app.rackNames = data.data;
					})
					// Ajaxリクエストが失敗した時発動
					.fail((data) => {
						UIkit.modal.alert("取得に失敗しました");
					})
					// Ajaxリクエストが成功・失敗どちらでも発動
					.always((data) => {
						loading_remove();
					});
			}
		}
	});

	var modal_sections = new Vue({
		el: '#modal-sections',
		data: {
			select_items: [],
			useUnitPrice: parseInt(<?php echo json_encode($useUnitPrice); ?>),
		},
		filters: {
			number_format: function(value) {
				if (!value) {
					return 0;
				}
				return new Intl.NumberFormat('ja-JP').format(value);
			},
		},
		methods: {
			clear: function() {
				let original = JSON.parse(JSON.stringify(this.select_items));
				this.select_items.splice(0, original.length);
			},
			addList: function(object) {
				this.select_items.push(object);
			},
			addObject: function(index) {
				app.addList(JSON.parse(JSON.stringify(this.select_items[index])));
			},
			openModal: function() {
				UIkit.modal('#modal-sections').show();
			}
		}
	});

	var gs1_128 = new Vue({
		data: {
			gs1_128: {}
		},
		methods: {
			changeDate: function(text) {
				if (!text) {
					return "";
				}
				if (text.length == "6") {
					text = 20 + text;
				}
				let date = text.slice(6, 8);
				if (parseInt(text.slice(6, 8)) == 0) {
					date = '01';
				}
				return text.slice(0, 4) + "-" + text.slice(4, 6) + "-" + date;
			},
			check_gs1_128: function(gs1128) {
				if (gs1128.indexOf("]C1") !== 0) {
					//UIkit.modal.alert("GS1-128ではありません");
					//return ;
					return this.check_gs1_128("]C1" + gs1128);
				}

				gs1128 = gs1128.slice(3);
				let obj = check_gs1128(gs1128);

				if (!obj.hasOwnProperty("01")) {
					UIkit.modal.alert("商品情報が含まれておりませんでした。").then(function() {});
					return;
				}

				let searchJan = gs1_01_to_jan(obj["01"]);
				let objkey = null;
				let setObj = {};

				let objLotNumber = (obj["10"] === void 0) ? "" : obj["10"]; //lotNumber
				let objLotDate = (obj["17"] === void 0) ? "" : this.changeDate(obj["17"]); //lotDate
				let existflg = false;
				let changeObject = null;


				if (!existflg) {
					app.barcodeSearch(searchJan, objLotNumber, objLotDate, false);
					//UIkit.modal.alert("対象の発注商品が見つかりませんでした。").then(function(){
					//	UIkit.modal($('#gs1-128')).show();
					//});
				}
			}
		}
	});

	function addTr(object, type, count) {
		app.addList(object);
	}
</script>