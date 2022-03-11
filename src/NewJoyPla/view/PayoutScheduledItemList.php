<div id="app" class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><span>払出予定商品一覧</span></li>
            </ul>
            <div class="no_print uk-margin">
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                <input class="print_hidden uk-button uk-button-primary" type="button" value="ピッキングリスト作成" v-on:click="regist_picking">
            </div>
            <h2 class="page_title uk-margin-remove">払出予定商品一覧</h2>
            <hr>
            <div class="uk-width-1-1 uk-margin-auto ">
                <form class="uk-form-stacked" name="searchForm" method="POST" :action="form_url">
                    <input type="hidden" name="Action" :value="search_action">
                    <input type="hidden" name="sortTitle" :value="sort_title">
                    <input type="hidden" name="sort" :value="( sort_asc === 'true' )? 'asc' : 'desc' ">
                    <div class="uk-width-3-4@m uk-margin-auto">
                        <h3>検索</h3>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">登録日時</label>
                            <div class="uk-child-width-1-2@m" uk-grid>
                                <div>
                                    <div>
                                        <input type="date" class="uk-input uk-width-4-5" name="registration_time_start" v-model="registration_time_start">
                                        <span class="uk-width-1-5'">から</span>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <input type="date" class="uk-input uk-width-4-5" name="registration_time_end" v-model="registration_time_end">
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
                                        <input type="date" class="uk-input uk-width-4-5" name="payout_plan_time_start" v-model="payout_plan_time_start">
                                        <span class="uk-width-1-5'">から</span>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <input type="date" class="uk-input uk-width-4-5" name="payout_plan_time_end" v-model="payout_plan_time_end">
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
                                        <option v-for="(di , index) in source_division_info" :value="di.divisionId">{{ di.divisionName }}</option>
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
                                        <option v-for="(di , index) in target_division_info" :value="di.divisionId">{{ di.divisionName }}</option>
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
                    <div class="no_print uk-margin">
                        <input id="smp-table-delete-button" class=" uk-button uk-button-danger uk-margin-small-right" type="button" name="smp-table-submit-button" value="削除" @click="delete_items">
                    </div>
                    <div>
                        <table-offset :current_page="current_page" :total_rec="total_rec" :limit="limit" ></table-offset>
                        <limit-select :attr="{'uk-width-1-3@m':true}" :select="[10,50,100]" :limit="limit"></limit-select>
                        <pagination :current_page="current_page" :total_rec="total_rec" :limit="limit" :show_nav="5"></pagination>
                        <div class="uk-overflow-auto">
                            <table class="uk-table uk-table-hover uk-table-middle uk-table-divider">
                                <thead>
                                    <tr>
                                        <th class="uk-text-nowrap no_print">
                                            <input type="checkbox" class="uk-checkbox" v-on:click="allchecked">
                                        </th>
                                        <th class="uk-text-nowrap">
                                            <sort-link title="id" :asc="sort_asc" :current_title="sort_title">id</sort-link>
                                        </th>
                                        <th>
                                            <sort-link title="registrationTime" :asc="sort_asc" :current_title="sort_title">登録日時</sort-link>
                                        </th>
                                        <th>
                                            <sort-link title="payoutPlanTime" :asc="sort_asc" :current_title="sort_title">払出予定日</sort-link>
                                        </th>
                                        <th class="uk-width-1-3">
                                         商品情報
                                        </th>
                                        <th class="uk-table-expand">払出元<br><span uk-icon="icon: arrow-right"></span>払出先</th>
                                        <th>
                                            <sort-link title="payoutQuantity" :asc="sort_asc" :current_title="sort_title">払出予定数</sort-link>
                                        </th>
                                        <th>
                                            <sort-link title="outOfStockStatus" :asc="sort_asc" :current_title="sort_title">ステータス</sort-link>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(li, index)  in lists" class="uk-text-break" :key="index" @click="tr_click(index,li)">
                                        <th class="uk-text-middle no_print">
                                            <input type="checkbox" class="uk-checkbox" v-model="li.check">
                                        </th>
                                        <td>{{ li.id }}</td>
                                        <td>{{ li.registrationTime }}</td>
                                        <td>
                                            <span class="uk-text-bold" :class="{'uk-text-danger' : ( li.payoutPlanTimeStatus == 2 ),'uk-text-warning' : ( li.payoutPlanTimeStatus == 1 ), }">{{ li.payoutPlanTime | date_format }}</span>
                                        </td>
                                        <td>
                                            <div uk-grid margin="0">
                                                <div class="uk-width-1-4 uk-text-muted">院内商品ID</div>
                                                <div class="uk-width-3-4">{{ li.inHospitalItemId }}</div>
                                                <div class="uk-width-1-4 uk-text-muted">分類</div>
                                                <div class="uk-width-3-4">{{ li.category }}</div>
                                                <div class="uk-width-1-4 uk-text-muted">メーカー</div>
                                                <div class="uk-width-3-4">{{ li.makerName }}</div>
                                                <div class="uk-width-1-4 uk-text-muted">商品名</div>
                                                <div class="uk-width-3-4">{{ li.itemName }}</div>
                                                <div class="uk-width-1-4 uk-text-muted">製品コード</div>
                                                <div class="uk-width-3-4">{{ li.itemCode }}</div>
                                                <div class="uk-width-1-4 uk-text-muted">規格</div>
                                                <div class="uk-width-3-4">{{ li.itemStandard }}</div>
                                                <div class="uk-width-1-4 uk-text-muted">JANコード</div>
                                                <div class="uk-width-3-4">{{ li.itemJANCode }}</div>
                                            </div>
                                        </td>
                                        <td>{{ li.sourceDivision }}<br><span uk-icon="icon: arrow-right"></span>{{ li.targetDivision }}</td>
                                        <td>{{ li.payoutQuantity }}{{ li.quantityUnit }}</td>
                                        <td>
                                            <span class="uk-label" :class="{ 'uk-label-warning' : (li.outOfStockStatus_id == '1'),'uk-label-danger' : (li.outOfStockStatus_id == '3'), }">
                                            {{ li.outOfStockStatus }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <pagination :current_page="current_page" :total_rec="total_rec" :limit="limit" :show_nav="5"></pagination>
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


let register_data = {
     payout_schedule_items :<?php echo json_encode($payout_schedule_items) ?>,
     source_division_info :<?php echo json_encode($source_division_info) ?>,
     target_division_info :<?php echo json_encode($target_division_info) ?>,
     start :parseInt("<?php echo ($search->limit * ($search->page - 1) + 1) ?>"),
     registration_time_start :"<?php echo $search->registration_time_start ?>",
     registration_time_end :"<?php echo $search->registration_time_end ?>",
     payout_plan_time_start :"<?php echo $search->payout_plan_time_start ?>",
     payout_plan_time_end :"<?php echo $search->payout_plan_time_end ?>",
     source_division : "<?php echo $search->source_division ?>",
     target_division : "<?php echo $search->target_division ?>",
     category_label :<?php echo json_encode($category_label) ?>,
     category : "<?php echo $search->category ?>",
     out_of_stock_status_label :<?php echo json_encode($out_of_stock_status_label) ?>,
     out_of_stock_status : "<?php echo $search->out_of_stock_status ?>",
     form_url :"<?php echo $form_url ?>",
     search_action :"<?php echo $search_action ?>",
     total_rec :parseInt("<?php echo $count ?>"),
     sort_asc :"<?php echo ( $search->sort_asc ==='asc' )?'true':'false' ?>",
     sort_title :"<?php echo $search->sort_title ?>",
     current_page :parseInt("<?php echo $search->page ?>"),
     limit :parseInt("<?php echo $search->limit ?>"),
     csrf_token: "<?php echo $csrf_token ?>",
};


var app = new Vue({
	el: '#app',
	data: {
        csrf_token: register_data.csrf_token,
        start: register_data.start,
        registration_time_start :register_data.registration_time_start,
        registration_time_end :register_data.registration_time_end,
        payout_plan_time_start :register_data.payout_plan_time_start,
        payout_plan_time_end :register_data.payout_plan_time_end,
        lists : register_data.payout_schedule_items,
        source_division_info: register_data.source_division_info,
        target_division_info: register_data.target_division_info,
        source_division: register_data.source_division,
        target_division: register_data.target_division,
        category_label: register_data.category_label, 
        category: register_data.category,
        out_of_stock_status_label: register_data.out_of_stock_status_label, 
        out_of_stock_status: register_data.out_of_stock_status,
        form_url :register_data.form_url,
        search_action :register_data.search_action,
        sort_asc :register_data.sort_asc,
        sort_title :register_data.sort_title,
        current_page :register_data.current_page,
        total_rec :register_data.total_rec,
        limit :register_data.limit,
        start :register_data.start,
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
        delete_items()
        {
            let ids = this.get_checked_ids();

            if(ids.length === 0)
            {
				UIkit.modal.alert('削除したい払出予定商品を選択してください');
                return false;
            }

            
			UIkit.modal.confirm('払出予定商品を削除します<br>よろしいですか').then(function(){
                
				$.ajax({
	                url: "<?php echo $api_url ?>",
					type:'POST',
					data:{
	                	Action : 'pickingItemsDeleteApi',
						_csrf: app.csrf_token,  // CSRFトークンを送信
	                	ids : JSON.stringify( objectValueToURIencode(ids) ),
					},
					dataType: 'json'
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
	                if(data.code != '0'){
	            		UIkit.modal.alert("払出予定商品の削除に失敗しました");
	            		return false;
	                }
	                UIkit.modal.alert("払出予定商品の削除が完了しました").then(function(){
						location.reload();
					});
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
	                UIkit.modal.alert("払出予定商品の削除に失敗しました");
				})
				// Ajaxリクエストが成功・失敗どちらでも発動
				.always( (data) => {
				});
				
			},function(){
				UIkit.modal.alert("中止しました");
			});

        },
        regist_picking()
        {
            let ids = this.get_checked_ids();

            if(ids.length === 0)
            {
				UIkit.modal.alert('ピッキングしたい払出予定商品を選択してください');
                return false;
            }

            
			UIkit.modal.confirm('ピッキングリストを作成します<br>よろしいですか').then(function(){
                
				$.ajax({
	                url: "<?php echo $api_url ?>",
					type:'POST',
					data:{
	                	Action : 'pickingItemsRegistApi',
						_csrf: app.csrf_token,  // CSRFトークンを送信
	                	ids : JSON.stringify( objectValueToURIencode(ids) ),
					},
					dataType: 'json'
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
	                if(data.code != '0'){
	            		UIkit.modal.alert("ピッキングリストの作成に失敗しました");
	            		return false;
	                }
	                UIkit.modal.alert("ピッキングリストの作成が完了しました").then(function(){
						location.reload();
					});
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
	                UIkit.modal.alert("ピッキングリストの作成に失敗しました");
				})
				// Ajaxリクエストが成功・失敗どちらでも発動
				.always( (data) => {
				});
				
			},function(){
				UIkit.modal.alert("中止しました");
			});

        },
        get_checked_ids()
        {
            let ids = [];
            app.lists.forEach(function (elem, index) {
                if(elem.check)
                {
                    ids.push(elem.id);
                }
            });
            return ids;
        }
    }
});
</script>