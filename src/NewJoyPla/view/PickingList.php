<div id="app" class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><span>ピッキングリスト一覧</span></li>
            </ul>
            <div class="no_print uk-margin">
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
            </div>
            <h2 class="page_title uk-margin-remove">ピッキングリスト一覧</h2>
            <hr>
            <div class="uk-width-1-1 uk-margin-auto">
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
                            <label class="uk-form-label">払出元部署</label>
                            <div class="uk-child-width-1-1">
                                <div>
                                    <select name="division_id" class="uk-select" v-model="division_id">
                                        <option value="">----- 選択してください -----</option>
                                        <option v-for="(di , index) in division_info" :value="di.divisionId">{{ di.divisionName }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">ステータス</label>
                            <div class="uk-child-width-1-1">
                                <label v-for="(c , index) in picking_status_label" class="uk-margin-small-right">
                                    <input type="checkbox" class="uk-checkbox uk-margin-small-right" :value="index" name="picking_status" v-bind:checked="( picking_status.indexOf(index) != -1 )">{{ c }}
                                </label>
                            </div>
                        </div>
                        <div class="uk-text-center no_print">
                            <input class="uk-margin-top uk-button uk-button-default" type="submit" value="検索">
                        </div>
                    </div>
                    <div>
                        <table-offset :current_page="current_page" :total_rec="total_rec" :limit="limit" ></table-offset>
                        <limit-select :attr="{'uk-width-1-3@m':true}" :select="[10,50,100]" :limit="limit"></limit-select>
                        <pagination :current_page="current_page" :total_rec="total_rec" :limit="limit" :show_nav="5"></pagination>
                        <div class="uk-overflow-auto">
                            <table class="uk-table uk-table-hover uk-table-middle uk-table-divider">
                                <thead>
                                    <tr>
                                        <th class="uk-text-nowrap">
                                            <sort-link title="id" :asc="sort_asc" :current_title="sort_title">id</sort-link>
                                        </th>
                                        <th>
                                            <sort-link title="registrationTime" :asc="sort_asc" :current_title="sort_title">登録日時</sort-link>
                                        </th>
                                        <th>
                                            <sort-link title="updateTime" :asc="sort_asc" :current_title="sort_title">更新日時</sort-link>
                                        </th>
                                        <th>
                                            <sort-link title="pickingId" :asc="sort_asc" :current_title="sort_title">ピッキングID</sort-link>
                                        </th>
                                        <th>
                                            払出元部署
                                        </th>
                                        <th>
                                            ステータス
                                        </th>
                                        <th class="uk-text-nowrap uk-table-shrink"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(li, index) in lists" class="uk-text-break" :key="index">
                                        <td class="uk-text-nowrap uk-table-shrink">{{ li.id }}</td>
                                        <td>{{ li.registrationTime }}</td>
                                        <td>{{ li.updateTime }}</td>
                                        <td>{{ li.pickingId }}</td>
                                        <td>{{ li.divisionName }}</td>
                                        <td>
                                            <span class="uk-text-bold" :class="{'uk-text-danger': (li.pickingStatus_id == 1) , 'uk-text-primary': (li.pickingStatus_id == 2) }">
                                            {{ li.pickingStatus }}
                                            </span>
                                        </td>
                                        <td class="uk-text-nowrap no_print">
                                            <a class="uk-button uk-button-primary" target="_self" v-on:click="silp_link(li.id)">詳細</a>
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
     picking_history :<?php echo json_encode($picking_history) ?>,
     division_info :<?php echo json_encode($division_info) ?>,
     picking_status_label :<?php echo json_encode($picking_status_label) ?>,
     division_id :"<?php echo $search->division_id ?>",
     picking_status :<?php echo json_encode($search->picking_status) ?>,
     registration_time_start :"<?php echo $search->registration_time_start ?>",
     registration_time_end :"<?php echo $search->registration_time_end ?>",
     form_url :"<?php echo $form_url ?>",
     search_action :"<?php echo $search_action ?>",
     total_rec :parseInt("<?php echo $count ?>"),
     sort_asc :"<?php echo ( $search->sort_asc === 'asc' )?'true':'false' ?>",
     sort_title :"<?php echo $search->sort_title ?>",
     current_page :parseInt("<?php echo $search->page ?>"),
     limit :parseInt("<?php echo $search->limit ?>"),
     start :parseInt("<?php echo ($search->limit * ($search->page - 1) + 1) ?>"),
     slip_id: 0,
     slip_link_action: "<?php echo $slip_link_action ?>",
     csrf_token: "<?php echo $csrf_token ?>",
};

var app = new Vue({
	el: '#app',
	data: {
        csrf_token: register_data.csrf_token,
        lists :register_data.picking_history,
        division_info :register_data.division_info,
        division_id :register_data.division_id,
        picking_status_label :register_data.picking_status_label,
        picking_status :register_data.picking_status,
        registration_time_start :register_data.registration_time_start,
        registration_time_end :register_data.registration_time_end,
        form_url :register_data.form_url,
        search_action :register_data.search_action,
        sort_asc :register_data.sort_asc,
        sort_title :register_data.sort_title,
        current_page :register_data.current_page,
        total_rec :register_data.total_rec,
        limit :register_data.limit,
        start :register_data.start,
        slip_id :register_data.slip_id,
        slip_link_action :register_data.slip_link_action,
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
        silp_link: function(id)
        {
            location.href = this.form_url + '&Action=' + this.slip_link_action + '&id=' + id;
        }
    }
});
</script>