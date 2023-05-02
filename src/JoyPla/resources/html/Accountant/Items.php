<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <h1 class="text-2xl mb-2">会計データ</h1>
        <hr>
        <div class="w-full flex border-b-2 border-gray-200 py-4">
          <div class="flex-auto w-1/2">
            <v-select
                name="search.perPage"
                :options="perPageOptions"
                @change="searchExec"
              ></v-select>
          </div>
          <div class="flex-auto w-1/2">
            <div class="ml-auto h-10 w-20 cursor-pointer" data-micromodal-trigger="openModal">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="h-8 w-8 mx-auto" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" @click="onOpenModal">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              <p class="text-sm text-center">絞り込み</p>
            </div>
          </div>
        </div>
        <div>
          {{ (totalCount == 0)? 0 : ( parseInt(values.search.perPage) * ( values.search.currentPage - 1 ) ) + 1 }}件 - {{ (( parseInt(values.search.perPage) * values.search.currentPage )  < totalCount ) ?  parseInt(values.search.perPage) * values.search.currentPage : totalCount  }}件 / 全 {{ totalCount }}件
        </div>
        <div class="mt-4">
          <table class="table-auto w-full text-sm">
            <thead>
              <tr>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">No</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">会計番号</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">発注番号</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">検収番号</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">部署</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">卸業者</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">登録元</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">アクション</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">商品ID</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">商品名</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">メーカー名</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">製品コード</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">規格</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">JANコード</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">個数</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">価格</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">税率</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">小計</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item) in values.items">
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ item._id }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <v-pagination
        :show-pages="showPages"
        v-model:current-page="values.search.currentPage"
        :total-count="totalCount"
        :per-page="parseInt(values.search.perPage)"
        ></v-pagination>
      </div>
    </div>
  </div>
  <v-open-modal ref="openModal" headtext="絞り込み" id="openModal">
    <div class="flex flex-col">
      <div class="w-full overflow-y-auto" style="max-height: 85vh;">
        <div class="flex flex-wrap">
          <div class="w-full px-3 my-6 md:mb-0">
            <div class="my-4">
              <v-input
                name="search.yearMonth"
                type="month"
                label="消費年月"
                title="消費年月"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="search.makerName"
                type="text"
                label="メーカー名"
                title="メーカー名"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="search.itemName"
                type="text"
                label="商品名"
                title="商品名"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="search.itemCode"
                type="text"
                label="製品コード"
                title="製品コード"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="search.itemStandard"
                type="text"
                label="規格"
                title="規格"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="search.itemJANCode"
                type="text"
                label="JANコード"
                title="JANコード"
                ></v-input>
            </div>
            <?php if (!gate('list_of_accountant_slips')->isOnlyMyDivision()): ?>
            <div class="my-4">
              <v-multiple-select-division
                name="search.divisionIds"
                title="部署名"
                ></v-multiple-select-division>
            </div>
            <?php endif; ?>
            <div class="my-4">
              <v-multiple-select-distributor
                name="search.distributorIds"
                title="卸業者"
                ></v-multiple-select-distributor>
            </div>
            <div class="mx-auto lg:w-2/3 mb-4 text-center flex items-center gap-6 justify-center">
              <v-button-default type="button" @click.native="searchClear">クリア</v-button-default>
              <v-button-primary type="button" @click.native="searchExec">絞り込み</v-button-primary>
            </div>
          </div>
        </div>
      </div>
    </div>
  </v-open-modal>
  <div class="hidden">
    <v-input type="hidden" name="search.makerName"></v-input>
    <v-input type="hidden" name="search.itemName"></v-input>
    <v-input type="hidden" name="search.itemCode"></v-input>
    <v-input type="hidden" name="search.itemStandard"></v-input>
    <v-input type="hidden" name="search.itemJANCode"></v-input>
    <v-input type="hidden" name="search.currentPage"></v-input>
    <v-input type="hidden" name="search.perPage"></v-input>
  </div>
<script>

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

var JoyPlaApp = Vue.createApp({ 
    components: {
      'v-open-modal': vOpenModal,
      'v-loading' : vLoading,
      'header-navi' : headerNavi,
      'v-breadcrumbs': vBreadcrumbs,
      'v-input': vInput,
      'v-select' : vSelect,
      'v-input-number': vInputNumber,
      'item-view' : itemView,
      'v-button-default' : vButtonDefault,
      'v-button-danger' : vButtonDanger,
      'v-button-primary' : vButtonPrimary,
      'v-in-hospital-item-modal': vInHospitalItemModal,
      'v-select-division' : vSelectDivision,
      'v-select-distributor' : vSelectDistributor,
      'v-multiple-select-division' : vMultipleSelectDivision,
      'v-multiple-select-distributor' : vMultipleSelectDistributor
    },
    setup(){
        const {ref , onCreated , onMounted} = Vue;
        const {useFieldArray, useForm} = VeeValidate;

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        const getCache = () => {
            let url = window.location.href;
            name = 'isCache';
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }

        const pagetitle = "AccountantItemsIndex";

        const getParam = (name) => {
            let url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");

            if(getCache() === "true")
            {
                let obj = sessionStorage.getItem(pagetitle);
                if(obj===null){ return "" }
                return (JSON.parse(obj))[name];
            }

            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }

        const setParam = (values) => {
            sessionStorage.setItem(pagetitle,JSON.stringify(values));
            const url = new URL(window.location);
            url.searchParams.set('itemName',values.itemName);
            url.searchParams.set('makerName',values.makerName);
            url.searchParams.set('itemCode',values.itemCode);
            url.searchParams.set('itemStandard',values.itemStandard);
            url.searchParams.set('itemJANCode',values.itemJANCode);
            url.searchParams.set('yearMonth',values.yearMonth);
            url.searchParams.set('perPage',values.perPage);
            url.searchParams.set('currentPage',values.currentPage);
            url.searchParams.set('divisionIds',values.divisionIds);
            url.searchParams.set('distributorIds',values.distributorIds);
            history.pushState({}, '', url);
        }

        const { handleSubmit , control, meta , validate , values , isSubmitting } = useForm({
            initialValues: {
                items: [],
                search: {
                    itemName  : (getParam("itemName")) ? getParam("itemName") : "",
                    makerName : (getParam("makerName")) ? getParam("makerName") : "",
                    itemCode : (getParam("itemCode")) ? getParam("itemCode") : "",
                    itemStandard : (getParam("itemStandard")) ? getParam("itemStandard") : "",
                    itemJANCode : (getParam("itemJANCode")) ? getParam("itemJANCode") : "",
                    yearMonth: (getParam("yearMonth")) ? getParam("yearMonth") : "",
                    perPage: (Number.isInteger(getParam("perPage"))) ? getParam("perPage") : "10",
                    currentPage : (Number.isInteger(parseInt(getParam("currentPage")))) ? parseInt(getParam("currentPage")) : 1,
                    divisionIds: (getParam("divisionIds")) ? ( Array.isArray(getParam("divisionIds"))? getParam("divisionIds") : (getParam("divisionIds")).split(',') ) : [],
                    distributorIds: (getParam("distributorIds")) ? ( Array.isArray(getParam("distributorIds"))? getParam("distributorIds") : (getParam("distributorIds")).split(',') ) : [],
                }
            },
            validateOnMount : false
        });

        const openRegistPage = ref(false);
        
        //const { remove, push, fields , update , replace , insert} = useFieldArray('items' , control);

        const loading = ref(false);
        const setLoading = (value) => {
            loading.value = value;
        };

        const startLoading = () => {
            setLoading(true);
        };

        const completeLoading = () => {
            setLoading(false);
        };

        const sleepCompleteLoading = async () => {
            await sleep(500);
            completeLoading();
        };

        const breadcrumbs = [
            {
            text: '会計メニュー',
            disabled: false,
            href: _ROOT+'&path=/accountant',
            },
            {
            text: '会計データ',
            disabled: true,
            }
        ];
        

        const getData = async() => 
        {
            let params = new URLSearchParams();
            params.append("path", "/api/accountant/items");
            params.append("search", JSON.stringify(encodeURIToObject(values.search)));
            params.append("_method", 'get');
            params.append("_csrf", _CSRF);

            const res = await axios.post(_APIURL,params);
            
            if(res.data.code != 200) {
                throw new Error(res.data.message)
            }
            return res.data ;
        }

        const fetchData = async () => {
            startLoading();
            const res = await getData();
            values.items = res.data
            completeLoading();
        };
        
        onMounted(async () => {
            await sleepCompleteLoading();
            
            try {
                await fetchData();
            } catch ( e ){
                Toast.fire({
                icon: 'error',
                title: '検索に失敗しました。再度お試しください。'
                })
            }
        });

        const numberFormat = (value) => {
            if (! value ) { return 0; }
            return new Intl.NumberFormat('ja-JP').format(value);
        };

        const itemSubtotal = (item) => {
            if(!item){
                console.log(item);
                return 0;
            }
            item = {
                price : item.price ?? 0,
                count : item.count ?? 0,
                taxrate : item.taxrate ?? 0,
            }

            // 価格、数量、税率を整数に変換
            const priceInt = Math.round(item.price * 100);
            const countInt = Math.round(item.count * 100);
            const taxRateInt = Math.round(item.taxrate);

            // 小計と税額を計算
            const itemTotalInt = priceInt * countInt / 100;
            const taxAmountInt = itemTotalInt * taxRateInt / 100;

            // 小計と税額を加算して、結果を小数に戻して返す
            return (itemTotalInt + taxAmountInt) / 100 ?? 0;
        }

        const openModal = ref();
        const onOpenModal = () => {
            openModal.value.open();
        }
        
        const searchExec = async () => {
            values.search.currentPage = 1;
            try {
                await fetchData();
                Toast.fire({
                icon: 'success', 
                title: '検索が完了しました'
                })
            } catch ( e ){
                Toast.fire({
                icon: 'error',
                title: '検索に失敗しました。再度お試しください。'
                })
            }
        }

        const searchClear = async () =>
        {
            values.search.currentPage = 1;
            values.search.itemName = '';
            values.search.makerName = '';
            values.search.itemCode = '';
            values.search.itemStandard = '';
            values.search.itemJANCode = '';
            values.search.yearMonth = '';
            values.search.divisionIds = [];
            values.search.distributorIds = [];
            try {
                await fetchData();
                Toast.fire({
                icon: 'success', 
                title: '検索が完了しました'
                })
            } catch ( e ){
                Toast.fire({
                icon: 'error',
                title: '検索に失敗しました。再度お試しください。'
                })
            }
        };
        
        const perPageOptions = [{ label: "10件表示", value: "10" },{ label: "50件表示", value: "50" },{ label: "100件表示", value: "100" }];

        return {
            values,
            openModal,
            perPageOptions,
            searchClear,
            searchExec,
            onOpenModal,
            loading,
            breadcrumbs
        };
    },
}).mount("#top");
</script> 