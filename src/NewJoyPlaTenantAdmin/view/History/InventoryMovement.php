<style>
	.asc::after {
		content: "▲";
	}
	.desc::after {
		content: "▼";
	}
</style>
<script>

    $(document).ready(function () {
        loading();
        setTimeout(function () {
            loading_remove()
        }, 1000);
    });
    function loading() {
        if ($("#loading").length == 0) {
            $(".animsition").before(
                '<div style="z-index: 1;position: fixed;" id="loading" class="uk-position-cover' +
                ' uk-overlay uk-overlay-default uk-flex uk-flex-center uk-flex-middle"><span uk' +
                '-spinner="ratio: 4.5" class="uk-icon uk-spinner"></span></div>'
            );
        }
    }

    function loading_remove() {
        if ($("#loading").length != 0) {
            $('.animsition').css({opacity: "1"});
            $('#loading').remove();
        }
    }
</script>

<div id="app">
    <div
        class="uk-section uk-section-default uk-preserve-color uk-padding-remove animsition no_print">
        <div class="uk-container uk-container-expand">
            <h1>棚卸実績</h1>
            <div class="uk-width-1-1 uk-margin-auto">
                <div class="no_print uk-margin" uk-margin="uk-margin">
                    <input
                        class="print_hidden uk-button uk-button-default"
                        type="submit"
                        value="印刷プレビュー"
                        onclick="window.print();return false;">
                    <button
                        v-on:click="listDl"
                        :disabled="!complete"
                        class="uk-button uk-button-primary">出力</button>
                </div>
                    <div class="uk-width-3-4@m uk-margin-auto">
                        <h3>検索</h3>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">施設</label>
                            <div class="uk-child-width-1-1">
                                <div>
                                    <select
                                        class="uk-select"
                                        v-model="select_data.hospitalId"
                                        v-on:change="hospitalSelected">
                                        <option value="">----- 選択してください -----</option>
                                        <option v-for="h in hospitals" :value="h.hospitalId">{{ h.hospitalName }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">基準棚卸日</label>
                            <div class="uk-child-width-1-1">
                                <div>
                                    <select
                                        class="uk-select"
                                        v-model="select_data.inventoryEndId"
                                        v-on:change="getItemNums">
                                        <option value="">----- 選択してください -----</option>
                                        <option v-for="h in histories" :value="h.inventoryEndId">
                                            <template v-if="h.inventoryTime == ''">
                                                現在日時（棚卸中）
                                            </template>
                                            <template v-else="v-else">
                                                {{ h.inventoryTime }}
                                            </template>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                            検索期間：{{searchStartDate}}
                            ~
                            {{searchEndDate}}
                        </div>
                    </div>
                <div v-if="items.length > 0">
                    <div class="uk-overflow-auto">
                        <table class="uk-table uk-table-hover uk-table-middle uk-table-divider">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th><a href="#" @click="sortBy('divisionName')" :class="addClass('divisionName')">部署名</a></th>
                                    <th><a href="#" @click="sortBy('rackName')" :class="addClass('rackName')">棚名</a></th>
                                    <th><a href="#" @click="sortBy('categoryToString')" :class="addClass('categoryToString')">分類</a></th>
                                    <th class="uk-width-1-6">商品情報</th>
                                    <th>卸業者</th>
                                    <th>価格</th>
                                    <th>単価</th>
                                    <th>入数</th>
                                    <th>前回数量</th>
                                    <th>前回金額</th>
                                    <th>入荷数量</th>
                                    <th>入荷金額</th>
                                    <th>消費数量</th>
                                    <th>消費金額</th>
                                    <th>棚卸数量</th>
                                    <th>棚卸金額</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="( i , key) in sort_items">
                                    <td>{{ key + 1 }}</td>
                                    <td>{{ i.divisionName }}</td>
                                    <td>{{ i.rackName }}</td>
                                    <td>{{ i.categoryToString }}</td>
                                    <td>
                                        <div margin="0">
                                            <div>{{ i.makerName }}</div>
                                            <div>{{ i.itemName }}</div>
                                            <div>{{ i.itemCode }}</div>
                                            <div>{{ i.itemStandard }}</div>
                                            <div>{{ i.itemJANCode }}</div>
                                        </div>
                                    </td>
                                    <td>{{ i.distributorName }}</td>
                                    <td>&yen;{{ i.price | number_format }}</td>
                                    <td>&yen;{{ i.unitPrice  | number_format }}</td>
                                    <td>{{ i.quantity | number_format }}{{ i.quantityUnit }}</td>
                                    <td>{{ get_before_inventory_nums(i.inHospitalItemId , i.divisionId).count }}{{ i.quantityUnit }}</td>
                                    <td>&yen;{{ Math.round( get_before_inventory_nums(i.inHospitalItemId , i.divisionId).count * i.unitPrice * 100 ) / 100 | number_format }}</td>
                                    <td>{{ get_receiving_nums(i.inHospitalItemId , i.divisionId).count }}{{ i.quantityUnit }}</td>
                                    <td>&yen;{{ get_receiving_nums(i.inHospitalItemId , i.divisionId).price  | number_format }}</td>
                                    <td>{{ get_consumed_nums(i.inHospitalItemId , i.divisionId).count }}{{ i.quantityUnit }}</td>
                                    <td>&yen;{{ get_consumed_nums(i.inHospitalItemId , i.divisionId).price  | number_format }}</td>
                                    <td>{{ get_inventory_nums(i.inHospitalItemId , i.divisionId).count }}{{ i.quantityUnit }}</td>
                                    <td>&yen;{{ Math.round( get_inventory_nums(i.inHospitalItemId , i.divisionId).count * i.unitPrice * 100 ) / 100  | number_format }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <td colspan="9">合計</td>
                                <td></td>
                                <td>&yen;{{ get_before_inventory_total_price() | number_format }}</td>
                                <td></td>
                                <td>&yen;{{ get_receivig_total_price() | number_format }}</td>
                                <td></td>
                                <td>&yen;{{ get_consumed_total_price() | number_format }}</td>
                                <td></td>
                                <td>&yen;{{ get_inventory_total_price() | number_format }}</td>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- print Only -->
    <div id="detail-sheet">
        <section class="sheet">
            <p class="uk-text-center print-text-xlarge title_spacing">棚卸実績</p>
            <div>
                <div uk-grid="uk-grid">
                    <div class="uk-width-2-3">
                        <span>棚卸確定日：{{ (completeDate !== '')? completeDate : "未確定" }}</span><br>
                        <span>期間：{{searchStartDate}}
                            ~
                            {{searchEndDate}}</span><br>
                        <span>病院名：{{get_hospital(select_data.hospitalId).hospitalName}}</span><br>
                    </div>
                    <div class="uk-width-1-3" style="height:100pt">
                        <table style="line-height: normal;" class="print-table">
                            <tr>
                                <th style="width:10pt; height:10pt"></th>
                                <th style="width:10pt; height:10pt"></th>
                                <th style="width:10pt; height:10pt"></th>
                            </tr>
                            <tr>
                                <td style="height:40pt"></td>
                                <td style="height:40pt"></td>
                                <td style="height:40pt"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <table class="print-table" style="line-height: normal; width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>部署名</th>
                            <th>棚名</th>
                            <th>分類</th>
                            <th class="uk-width-1-6">商品情報</th>
                            <th>卸業者</th>
                            <th>価格</th>
                            <th>単価</th>
                            <th>入数</th>
                            <th>前回<br>数量</th>
                            <th>前回<br>金額</th>
                            <th>入荷<br>数量</th>
                            <th>入荷<br>金額</th>
                            <th>消費<br>数量</th>
                            <th>消費<br>金額</th>
                            <th>棚卸<br>数量</th>
                            <th>棚卸<br>金額</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="( i , key) in sort_items">
                            <td>{{ key + 1 }}</td>
                            <th>{{ i.divisionName }}</th>
                            <th>{{ i.rackName }}</th>
                            <th>{{ i.categoryToString }}</th>
                            <td>
                                <div margin="0">
                                    <div>{{ i.makerName }}</div>
                                    <div>{{ i.itemName }}</div>
                                    <div>{{ i.itemCode }}</div>
                                    <div>{{ i.itemStandard }}</div>
                                    <div>{{ i.itemJANCode }}</div>
                                </div>
                            </td>
                            <td>{{ i.distributorName }}</td>
                            <td>&yen;{{ i.price | number_format }}</td>
                            <td>&yen;{{ i.unitPrice  | number_format }}</td> 
                            <td>{{ i.quantity | number_format }}{{ i.quantityUnit }}</td>
                            <td>{{ get_before_inventory_nums(i.inHospitalItemId , i.divisionId).count }}{{ i.quantityUnit }}</td>
                            <td>&yen;{{ Math.round( get_before_inventory_nums(i.inHospitalItemId , i.divisionId).count * i.unitPrice * 100 ) / 100 | number_format }}</td>
                            <td>{{ get_receiving_nums(i.inHospitalItemId , i.divisionId).count }}{{ i.quantityUnit }}</td>
                            <td>&yen;{{ get_receiving_nums(i.inHospitalItemId , i.divisionId).price  | number_format }}</td>
                            <td>{{ get_consumed_nums(i.inHospitalItemId , i.divisionId).count }}{{ i.quantityUnit }}</td>
                            <td>&yen;{{ get_consumed_nums(i.inHospitalItemId , i.divisionId).price  | number_format }}</td>
                            <td>{{ get_inventory_nums(i.inHospitalItemId , i.divisionId).count }}{{ i.quantityUnit }}</td>
                            <td>&yen;{{ Math.round( get_inventory_nums(i.inHospitalItemId , i.divisionId).count * i.unitPrice * 100 ) / 100  | number_format }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <td colspan=9>合計</td>
                        <td></td>
                        <td>&yen;{{ get_before_inventory_total_price() | number_format }}</td>
                        <td></td>
                        <td>&yen;{{ get_receivig_total_price() | number_format }}</td>
                        <td></td>
                        <td>&yen;{{ get_consumed_total_price() | number_format }}</td>
                        <td></td>
                        <td>&yen;{{ get_inventory_total_price() | number_format }}</td>
                    </tfoot>
                </table>
            </div>
        </section>
    </div>
</div>
<script>

    var datas = {
        'hospitals': <?php echo json_encode($hospital) ?>,
        'divisions': [],
        'hospitalName': '',
        'histories': {},
        'select_data': {
            inventoryEndId: "",
            divisionId: "",
            hospitalId: ""
        },
        'inventoryNums': [],
        'beforeInventoryNums': [],
        'receivingNums': [],
        'consumedNums': [],
        'items': [],
        'searchStartDate': '',
        'searchEndDate': '',
        'complete': false,
        'completeDate' : '',
        sort_key: "",
        sort_asc: true,
    };

    var app = new Vue({
        el: '#app',
        data: datas,
        mounted() {},
        computed: {
            sort_items() {
                if (this.sort_key != "") {
                let set = 1;
                this.sort_asc ? (set = 1) : (set = -1);
                this.items.sort((a, b) => {
                    if (a[this.sort_key] < b[this.sort_key]) return -1 * set;
                    if (a[this.sort_key] > b[this.sort_key]) return 1 * set;
                    return 0;
                });
                return this.items;
                } else {
                return this.items;
                }
            },
        },
        filters: {
            number_format: function (value) {
                if (!value) {
                    return 0;
                }
                return value
                    .toString()
                    .replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g, '$1,');
            }
        },
        watch: {},
        methods: {
            addClass(key) {
                return {
                    asc: this.sort_key === key && this.sort_asc,
                    desc: this.sort_key === key && !this.sort_asc,
                };
            },
            sortBy(key) {
                this.sort_key === key
                ? (this.sort_asc = !this.sort_asc)
                : (this.sort_asc = true);
                this.sort_key = key;
            },
            get_before_inventory_total_price: function()
            {
                let num = 0;
                this.items.forEach(function(i){
                    num += Math.round( app.get_before_inventory_nums(i.inHospitalItemId , i.divisionId).count * i.unitPrice * 100 ) / 100;
                }); 
                return num;
            },
            get_receivig_total_price: function()
            {
                let num = 0;
                this.items.forEach(function(i){
                    num += Math.round( app.get_receiving_nums(i.inHospitalItemId , i.divisionId).count * i.unitPrice * 100 ) / 100;
                }); 
                return num;
            },
            get_consumed_total_price: function()
            {
                let num = 0;
                this.items.forEach(function(i){
                    num += Math.round( app.get_consumed_nums(i.inHospitalItemId , i.divisionId).count * i.unitPrice * 100 ) / 100;
                }); 
                return num;
            },
            get_inventory_total_price: function()
            {
                let num = 0;
                this.items.forEach(function(i){
                    num += Math.round( app.get_inventory_nums(i.inHospitalItemId , i.divisionId).count * i.unitPrice * 100 ) / 100;
                }); 
                return num;
            },
            get_hospital: function (hospitalId) {
                if (!hospitalId) {
                    return {'hospitalName': ''};
                }
                if ( app.hospitals.length === 0 )
                {
                    return {'hospitalName': ''};
                }
                let res = app
                    .hospitals
                    .find(i => i.hospitalId === hospitalId);
                if (res) {
                    return res;
                } else {
                    return {'hospitalName': ''};
                }
            },
            get_before_inventory_nums: function (in_hospital_id , division_id) {
                if (!in_hospital_id) {
                    return {'count': 0, 'price': 0};
                }
                if ( app.beforeInventoryNums.length === 0 )
                {
                    return {'count': 0, 'price': 0};
                }
                let res = app
                    .beforeInventoryNums
                    .find(i => i.inHospitalItemId === in_hospital_id && i.divisionId === division_id);
                if (res) {
                    return res;
                } else {
                    return {'count': 0, 'price': 0};
                }
            },
            get_inventory_nums: function (in_hospital_id, division_id) {
                if (!in_hospital_id) {
                    return {'count': 0, 'price': 0};
                }
                if ( app.inventoryNums.length === 0 )
                {
                    return {'count': 0, 'price': 0};
                }
                let res = app
                    .inventoryNums
                    .find(i => i.inHospitalItemId === in_hospital_id && i.divisionId === division_id);
                if (res) {
                    return res;
                } else {
                    return {'count': 0, 'price': 0};
                }
            },
            get_receiving_nums: function (in_hospital_id, division_id) {
                if (!in_hospital_id) {
                    return {'count': 0, 'price': 0};
                }
                if ( app.receivingNums.length === 0 )
                {
                    return {'count': 0, 'price': 0};
                }
                let res = app
                    .receivingNums
                    .find(i => i.inHospitalItemId === in_hospital_id && i.divisionId === division_id);
                if (res) {
                    return res;
                } else {
                    return {'count': 0, 'price': 0};
                }
            },
            get_consumed_nums: function (in_hospital_id, division_id) {
                if (!in_hospital_id) {
                    return {'count': 0, 'price': 0};
                }
                if ( app.consumedNums.length === 0 )
                {
                    return {'count': 0, 'price': 0};
                }
                let res = app
                    .consumedNums
                    .find(i => i.inHospitalItemId === in_hospital_id && i.divisionId === division_id);
                if (res) {
                    return res;
                } else {
                    return {'count': 0, 'price': 0};
                }
            },
            hospitalSelected: function () {
                app.select_data.inventoryEndId = '';
                app.inventoryNums = [];
                app.receivingNums = [];
                app.consumedNums = [];
                app.beforeInventoryNums = [];
                app.items = [];
                app.searchStartDate = '';
                app.searchEndDate = '';
                app.complete = false;
                app.completeDate = '',

                custom_loading = true;

                function ajax1() {
                    let dfd = $.Deferred();
                    $
                        .ajax({
                            async: true,
                            url: '%url/rel:mpgt:MonthlyReport%',
                            type: 'POST',
                            data: {
                                Action: "hospitalInventorySelectApi",
                                _csrf: "<?php echo $csrf_token ?>",
                                hospitalId: app.select_data.hospitalId,
                            },
                            dataType: 'json'
                        })
                        .done(function (returnData) {
                            app.histories = returnData.data;
                        });
                    // fail()は省略
                    return dfd.promise();
                }

                function ajax2() {
                    let dfd = $.Deferred();
                    $
                        .ajax({
                            async: true,
                            url: '%url/rel:mpgt:MonthlyReport%',
                            type: 'POST',
                            data: {
                                Action: "hospitalItemsSelectApi",
                                _csrf: "<?php echo $csrf_token ?>",
                                hospitalId: app.select_data.hospitalId,
                            },
                            dataType: 'json'
                        })
                        .done(function (returnData) {
                            app.items = returnData.data;
                        });
                    // fail()は省略
                    return dfd.promise();
                }
                loading();
                setTimeout(function () {
                    ajax1();
                }, 0);
                setTimeout(function () {
                    ajax2().then(loading_remove());
                }, 1000);
            },
            getItemNums: function () {
                app.inventoryNums = [];
                app.beforeInventoryNums = [];
                app.consumedNums = [];
                app.receivingNums = [];
                app.searchStartDate = '';
                app.searchEndDate = '';
                app.complete = false;
                app.completeDate = '',

                app
                    .histories
                    .forEach(
                        e => (app.select_data.inventoryEndId === e.inventoryEndId)
                            ? app.searchStartDate = e.searchStartDate
                            : ""
                    );
                app
                    .histories
                    .forEach(
                        e => (app.select_data.inventoryEndId === e.inventoryEndId)
                            ? app.searchEndDate = e.searchEndDate
                            : ""
                    );

                app.histories.forEach(e => (app.select_data.inventoryEndId === e.inventoryEndId)? app.completeDate = e.inventoryTime : "");
                custom_loading = true;

                function ajax1() {
                    let dfd = $.Deferred();
                    $
                        .ajax({
                            async: true,
                            url: '%url/rel:mpgt:MonthlyReport%',
                            type: 'POST',
                            data: {
                                _csrf: "<?php echo $csrf_token ?>",
                                Action: "getInventoryItemNumsApi",
                                hospitalId: app.select_data.hospitalId,
                                inventoryEndId: app.select_data.inventoryEndId
                            },
                            dataType: 'json'
                        })
                        .done(function (returnData) {
                            let array = [];
                            app.inventoryNums = returnData.data.record;
                        });
                    // fail()は省略
                    return dfd.promise();
                }

                function ajax2() {
                    let dfd = $.Deferred();
                    $
                        .ajax({
                            async: true,
                            url: '%url/rel:mpgt:MonthlyReport%',
                            type: 'POST',
                            data: {
                                _csrf: "<?php echo $csrf_token ?>",
                                Action: "getBeforeInventoryItemNumsApi",
                                inventoryEndId: app.select_data.inventoryEndId,
                                hospitalId: app.select_data.hospitalId,
                            },
                            dataType: 'json'
                        })
                        .done(function (returnData) {
                            let array = [];
                            delete returnData.data.date;

                            app.beforeInventoryNums = returnData.data.record;
                        });
                    // fail()は省略
                    return dfd.promise();
                }

                function ajax3() {
                    let dfd = $.Deferred();
                    $
                        .ajax({
                            async: true,
                            url: '%url/rel:mpgt:MonthlyReport%',
                            type: 'POST',
                            data: {
                                _csrf: "<?php echo $csrf_token ?>",
                                Action: "getReceivingItemNumsApi",
                                hospitalId: app.select_data.hospitalId,
                                inventoryEndId: app.select_data.inventoryEndId,
                                startDate: encodeURI(app.searchStartDate),
                                endDate: encodeURI(app.searchEndDate)
                            },
                            dataType: 'json'
                        })
                        .done(function (returnData) {
                            app.receivingNums = returnData.data.record;
                        });
                    // fail()は省略
                    return dfd.promise();
                }

                function ajax4() {
                    let dfd = $.Deferred();
                    $
                        .ajax({
                            async: true,
                            url: '%url/rel:mpgt:MonthlyReport%',
                            type: 'POST',
                            data: {
                                _csrf: "<?php echo $csrf_token ?>",
                                Action: "getConsumedItemNumsApi",
                                hospitalId: app.select_data.hospitalId,
                                inventoryEndId: app.select_data.inventoryEndId,
                                startDate: encodeURI(app.searchStartDate),
                                endDate: encodeURI(app.searchEndDate)
                            },
                            dataType: 'json'
                        })
                        .done(function (returnData) {
                            app.consumedNums = returnData.data.record;
                        });
                    // fail()は省略
                    return dfd.promise();
                }

                if (app.select_data.inventoryEndId !== '') {
                    loading();
                    setTimeout(function () {
                        ajax1()
                    }, 0);
                    setTimeout(function () {
                        ajax2()
                    }, 5000);
                    setTimeout(function () {
                        ajax3();
                    }, 3000)
                    setTimeout(function () {
                        ajax4().then(loading_remove(), app.complete = true);
                    }, 5000);
                }
            },
            exportCSV: function (records) {
                let data = records
                    .map((record) => record.join('\t'))
                    .join('\r\n');
                data = Encoding.stringToCode(data);
                let shiftJisCodeList = Encoding.convert(data, 'sjis', 'unicode');
                let uInt8List = new Uint8Array(shiftJisCodeList);

                //let bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
                let blob = new Blob([uInt8List], {type: 'text/tab-separated-values'});
                let url = (window.URL || window.webkitURL).createObjectURL(blob);
                let link = document.createElement('a');
                link.download = 'inventoryMovement_<?php echo date('Ymd'); ?>.tsv';
                link.href = url;
                document
                    .body
                    .appendChild(link);
                link.click();
                document
                    .body
                    .removeChild(link);
            },
            listDl: function () {
                let result = [];
                for (let i = 0; i < app.items.length; i++) {
                    result[i] = [
                        i+1,
                        app.items[i].rackName,
                        app.items[i].categoryToString,
                        app.items[i].itemName,
                        app.items[i].itemCode,
                        app.items[i].itemStandard,
                        app.items[i].makerName,
                        app.items[i].itemJANCode,
                        app.items[i].distributorName,
                        app.items[i].price,
                        app.items[i].unitPrice,
                        app.items[i].quantity,
                        app.items[i].quantityUnit,
                        app.get_before_inventory_nums(app.items[i].inHospitalItemId , app.items[i].divisionId).count,
                        Math.round( app.get_before_inventory_nums(app.items[i].inHospitalItemId , app.items[i].divisionId).count * app.items[i].unitPrice * 100 ) / 100,
                        app.get_receiving_nums(app.items[i].inHospitalItemId , app.items[i].divisionId).count,
                        app.get_receiving_nums(app.items[i].inHospitalItemId , app.items[i].divisionId).price,
                        app.get_consumed_nums(app.items[i].inHospitalItemId , app.items[i].divisionId).count,
                        app.get_consumed_nums(app.items[i].inHospitalItemId , app.items[i].divisionId).price,
                        app.get_inventory_nums(app.items[i].inHospitalItemId , app.items[i].divisionId).count,
                        Math.round( app.get_inventory_nums(app.items[i].inHospitalItemId , app.items[i].divisionId).count * app.items[i].unitPrice * 100 ) / 100,
                        app.get_hospital(app.select_data.hospitalId).hospitalName,
                        app.items[i].divisionName
                    ];
                }

                console.log(result);

                result.unshift([
                    'id',
                    '棚名',
                    '分類',
                    '商品名',
                    '製品コード',
                    '規格',
                    'メーカー名',
                    'JANコード',
                    '卸業者名',
                    '購買価格',
                    '単価',
                    '入数',
                    '入数単位',
                    '前回数量',
                    '前回金額',
                    '入荷数量',
                    '入荷金額',
                    '消費数量',
                    '消費金額',
                    '棚卸数量',
                    '棚卸金額',
                    '病院名',
                    '部署名'
                ]);

                this.exportCSV(result);
            }
        }
    });

    $("#content").ajaxError(function () {
        UIkit
            .modal
            .alert('リクエストに失敗しました');
    });
</script>