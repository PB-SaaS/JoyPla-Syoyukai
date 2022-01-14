<div id="app" class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><span>払出予定商品一覧</span></li>
            </ul>
            <div class="no_print uk-margin">
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
            </div>
            <h2 class="page_title uk-margin-remove">払出予定商品一覧</h2>
            <hr>
            <div class="uk-width-1-1 uk-margin-auto">
                <form class="uk-form-stacked" name="searchForm" method="POST" action="<?php echo $form_url; ?>">
                    <input type="hidden" name="Action" value="<?php echo $action ?>">
                    <div class="uk-width-3-4@m uk-margin-auto">
                        <h3>検索</h3>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">登録日時</label>
                            <div class="uk-child-width-1-2@m" uk-grid>
                                <div>
                                    <div>
                                        <input type="date" class="uk-input uk-width-4-5" name="registration_time_start" value="<?php echo $registration_time_start ?>">
                                        <span class="uk-width-1-5'">から</span>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <input type="date" class="uk-input uk-width-4-5" name="registration_time_end" value="<?php echo $registration_time_end ?>">
                                        <span class="uk-width-1-5'">まで</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">払出予定日</label>
                            <div class="uk-child-width-1-2@m" uk-grid>
                                <div>
                                    <div>
                                        <input type="date" class="uk-input uk-width-4-5" name="payout_plan_time_start" value="<?php echo $payout_plan_time_start ?>">
                                        <span class="uk-width-1-5'">から</span>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <input type="date" class="uk-input uk-width-4-5" name="payout_plan_time_end" value="<?php echo $payout_plan_time_end ?>">
                                        <span class="uk-width-1-5'">まで</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">分類</label>
                            <div class="uk-child-width-1-1">
                                <label v-for="(c , index) in category_label" class="uk-margin-small-right">
                                    <input type="checkbox" class="uk-checkbox uk-margin-small-right" :value="index" name="category" v-bind:checked="( category.indexOf(index) != -1 )">{{ c }}
                                </label>
                            </div>
                        </div>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">払出元部署</label>
                            <div class="uk-child-width-1-1">
                                <div>
                                    <select name="source_division" class="uk-select" v-model="source_division">
                                        <option value="">----- 選択してください -----</option>
                                        <option v-for="(di , index) in division_info" :value="di.divisionId">{{ di.divisionName }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">払出先部署</label>
                            <div class="uk-child-width-1-1">
                                <div>
                                    <select name="target_division" class="uk-select" v-model="target_division">
                                        <option value="">----- 選択してください -----</option>
                                        <option v-for="(di , index) in division_info" :value="di.divisionId">{{ di.divisionName }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">ステータス</label>
                            <div class="uk-child-width-1-1">
                                <label v-for="(c , index) in out_of_stock_status_label" class="uk-margin-small-right" v-if="index != 2">
                                    <input type="checkbox" class="uk-checkbox uk-margin-small-right" :value="index" name="out_of_stock_status" v-bind:checked="( out_of_stock_status.indexOf(index) != -1 )">{{ c }}
                                </label>
                            </div>
                        </div>
                        <div class="uk-text-center">
                            <input class="uk-margin-top uk-button uk-button-default" type="submit" value="検索">
                        </div>
                    </div>
                    <div>
                        <table-offset :current_page="<?php echo $page ?>" :total_rec="<?php echo $count ?>" :limit="<?php echo $limit ?>" ></table-offset>
                        <limit-select :attr="{'uk-width-1-3@m':true}" :select="[10,50,100]" :limit="<?php echo $limit ?>"></limit-select>
                        <pagination :current_page="<?php echo $page ?>" :total_rec="<?php echo $count ?>" :limit="<?php echo $limit ?>" :show_nav="5"></pagination>
                        <div class="uk-overflow-auto">
                            <table class="uk-table uk-table-hover uk-table-middle uk-table-divider">
                                <thead>
                                    <tr>
                                        <th class="uk-text-nowrap">
                                            <input type="checkbox" class="uk-checkbox" v-on:click="allchecked">
                                        </th>
                                        <th class="uk-text-nowrap">
                                            <sort-link title="id" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">id</sort-link>
                                        </th>
                                        <th class="uk-table-expand">
                                            <sort-link title="registrationTime" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">登録日時</sort-link>
                                        </th>
                                        <th class="uk-table-expand">
                                            <sort-link title="payoutPlanTime" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">払出予定日</sort-link>
                                        </th>
                                        <th class="uk-table-expand">
                                            <sort-link title="outOfStockStatus" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">ステータス</sort-link>
                                        </th>
                                        <th class="uk-table-expand">
                                            <sort-link title="inHospitalItemId" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">院内商品ID</sort-link>
                                        </th>
                                        <th class="uk-table-expand">
                                            <sort-link title="category" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">分類</sort-link>
                                        </th>
                                        <th class="uk-table-expand">
                                            <sort-link title="itemName" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">商品名</sort-link>
                                        </th>
                                        <th class="uk-table-expand">
                                            <sort-link title="itemCode" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">製品コード</sort-link>
                                        </th>
                                        <th class="uk-table-expand">
                                            <sort-link title="itemStandard" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">規格</sort-link>
                                        </th>
                                        <th class="uk-table-expand">
                                            <sort-link title="itemJANCode" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">JANコード</sort-link>
                                        </th>
                                        <th class="uk-table-expand">払出元</th>
                                        <th class="uk-table-expand">払出先</th>
                                        <th class="uk-table-expand">
                                            <sort-link title="payoutQuantity" asc="<?php echo ( $sort_asc === 'asc' )?'true':'false' ?>" current_title="<?php echo $sort_title ?>">払出予定数</sort-link>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(li, index)  in lists" class="uk-text-break" :key="index" @click="tr_click(index,li)">
                                        <th class="uk-text-middle">
                                            <input type="checkbox" class="uk-checkbox" v-model="li.check">
                                        </th>
                                        <td>{{ li.id }}</td>
                                        <td>{{ li.registrationTime }}</td>
                                        <td>{{ li.payoutPlanTime | date_format }}</td>
                                        <td>
                                            <span class="uk-label" :class="{ 'uk-label-warning' : (li.outOfStockStatus_id = '1'),'uk-label-danger' : (li.outOfStockStatus_id = '3'), }">
                                            {{ li.outOfStockStatus }}
                                            </span>
                                        </td>
                                        <td>{{ li.inHospitalItemId }}</td>
                                        <td>{{ li.category }}</td>
                                        <td>{{ li.itemName }}</td>
                                        <td>{{ li.itemCode }}</td>
                                        <td>{{ li.itemStandard }}</td>
                                        <td>{{ li.itemJANCode }}</td>
                                        <td>{{ li.sourceDivision }}</td>
                                        <td>{{ li.targetDivision }}</td>
                                        <td>{{ li.payoutQuantity }}{{ li.quantityUnit }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <pagination :current_page="<?php echo $page ?>" :total_rec="<?php echo $count ?>" :limit="<?php echo $limit ?>" :show_nav="5"></pagination>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://i02.smp.ne.jp/u/joypla/new/components/pagination.js"></script>
<script src="https://i02.smp.ne.jp/u/joypla/new/components/table-offset.js"></script>
<script src="https://i02.smp.ne.jp/u/joypla/new/components/limit-select.js"></script>
<script src="https://i02.smp.ne.jp/u/joypla/new/components/sort-link.js"></script>
<script>
let payout_schedule_items = <?php echo json_encode($pay_schedule_items) ?>;
let division_info = <?php echo json_encode($division_info) ?>;
let start = "<?php echo ($limit * ($page - 1) + 1) ?>";
let source_division = "<?php echo $source_division ?>";
let target_division = "<?php echo $target_division ?>";
let category_label = <?php echo json_encode($category_label) ?>;
let category = <?php echo json_encode($category) ?>;
let out_of_stock_status_label = <?php echo json_encode($out_of_stock_status_label) ?>;
let out_of_stock_status = <?php echo json_encode($out_of_stock_status) ?>;
var app = new Vue({
	el: '#app',
	data: {
        start: start,
        lists : payout_schedule_items,
        division_info: division_info,
        source_division: source_division,
        target_division: target_division,
        category_label: category_label, 
        category: category,
        out_of_stock_status_label: out_of_stock_status_label, 
        out_of_stock_status: out_of_stock_status,
    },
	filters: {
        number_format: function(value) {
            if (! value ) { return 0; }
            return value.toString().replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,' );
        },
        date_format: function(value)
        {
            let arr = [{'format':'年','replace':'-'},{'format':'月','replace':'-'},{'format':'日','replace':''},{'format':'時','replace':':'},{'format':'分','replace':':'},{'format':'秒','replace':''}]
            let str = value;
            for (const element of arr) {
                str = str.replace(element.format, element.replace);
            }

            let today = new Date(str);
            let year = today.getFullYear();
            let month = today.getMonth() + 1;
            let day = today.getDate();
            return ( year + '年' + month + '月' + day + '日');
        },
    },
    methods:{
        allchecked: function(event){
            app.lists.forEach(function (elem, index) {
                let changeObject = elem;
                changeObject.check = event.target.checked;
                app.$set(app.lists, index, changeObject);
            });
        },
        tr_click(index,li)
        {
            li.check = !li.check;
            app.$set(app.lists, index, li);
        },
    }
});
</script>