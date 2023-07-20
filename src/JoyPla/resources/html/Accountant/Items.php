<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto w-full">
      <div class="index px-1 w-full mx-auto mb-96">
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
        <div class="w-full flex border-b-2 border-gray-200 py-4">
            <v-button-primary type="button" class="md:w-1/4" data-micromodal-trigger="downloadModal">ダウンロード</v-button-primary>
            <download-modal :search="values.search" download-path="/api/accountant/items/download"></download-modal>
        </div>
        <div class="w-full flex border-b-2 border-gray-200 py-4 text-xl">
         <p>合計金額 : &yen;{{ numberFormat(totalPrice) }} </p>
        </div>
        <div>
          {{ (totalCount == 0)? 0 : ( parseInt(values.search.perPage) * ( values.search.currentPage - 1 ) ) + 1 }}件 - {{ (( parseInt(values.search.perPage) * values.search.currentPage )  < totalCount ) ?  parseInt(values.search.perPage) * values.search.currentPage : totalCount  }}件 / 全 {{ totalCount }}件
        </div>
        <div class="mt-4">
          <table-component table-id="accountant_items_table" :headers="headers" :rows-data="rowsData" :sort-direction="values.search.sortDirection" :sort-columns="values.search.sortColumns" @request-sort="handleSortRequest" />
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
                label="会計年月"
                title="会計年月"
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
      'download-modal' : downloadModal,
      'v-select-division' : vSelectDivision,
      'v-select-distributor' : vSelectDistributor,
      'v-multiple-select-division' : vMultipleSelectDivision,
      'v-multiple-select-distributor' : vMultipleSelectDistributor,
      'v-pagination' : vPagination,
      'table-component' : tableComponent
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

        const showPages = ref(5);
        const totalCount = ref(0);

        const pagetitle = "AccountantItems";

        const getParam = (name, defaultValue) => {
            let url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");

            if(getCache() === "true")
            {
                let obj = sessionStorage.getItem(pagetitle);
                if(obj===null){ return defaultValue ?? "" }
                console.log(obj);
                return (JSON.parse(obj))[name] ?? defaultValue;
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
            url.searchParams.set('sortColumn',values.sortColumn);
            url.searchParams.set('sortDirection',values.sortDirection);
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

        console.log(getParam("perPage"));

        const { handleSubmit , control, meta , validate , values , isSubmitting } = useForm({
            initialValues: {
                search: {
                    sortColumn  : getParam("sortColumn","id"),
                    sortDirection  :getParam("sortDirection","desc"),
                    itemName  : getParam("itemName"),
                    makerName :  getParam("makerName"),
                    itemCode : getParam("itemCode"),
                    itemStandard : getParam("itemStandard"),
                    itemJANCode : getParam("itemJANCode"),
                    yearMonth: getParam("yearMonth"),
                    perPage: getParam("perPage" , '10'),
                    currentPage : parseInt(getParam("currentPage",1)),
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

        const rowsData = ref([]);
        const createRowData = (obj) => {
          rowsData.value = [];
          for (let key in obj) {
            rowsData.value.push(
              [
                obj[key].id,
                obj[key].accountantDate,
                "<a class='text-blue-600 hover:underline' href='"+_ROOT+"&path=/accountant/"+ obj[key].accountantId + "'>"+obj[key].accountantId+"</a>",
                obj[key].accountantItemId,
                obj[key].orderNumber,
                obj[key].receivingNumber,
                obj[key]._division?.divisionName,
                obj[key]._distributor?.distributorName,
                obj[key].method,
                obj[key].action,
                obj[key].itemId,
                obj[key].itemName,
                obj[key].makerName,
                obj[key].itemCode,
                obj[key].itemStandard,
                obj[key].itemJANCode,
                numberFormat(obj[key].count) + obj[key].unit ,
                '&yen;'+ numberFormat(obj[key].price) ,
                obj[key].taxrate + "%",
                '&yen;'+ numberFormat(itemSubtotal(obj[key])) ,
              ]
            );
          }
        }

        const fetchData = async () => {
            startLoading();
            const res = await getData();
            createRowData(res.data);
            totalCount.value = parseInt(res.count);
            setParam(values.search);
            completeLoading();
        };

        const totalPrice = ref(0);

        const getTotalPrice = async () => {
            let params = new URLSearchParams();
            params.append("path", "/api/accountant/items/totalPrice");
            params.append("search", JSON.stringify(encodeURIToObject(values.search)));
            params.append("_method", 'get');
            params.append("_csrf", _CSRF);

            const res = await axios.post(_APIURL,params);
            
            if(res.data.code != 200) {
                throw new Error(res.data.message)
            }
            totalPrice.value = res.data.data
        };

        
        onMounted(async () => {
            await sleepCompleteLoading();
            try {
                await fetchData();
                await getTotalPrice();
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

        const openDownloadModal = ref();
        const onOpenDownloadModal = () => {
          openDownloadModal.value.open();
        }
        
        const searchExec = async () => {
            values.search.currentPage = 1;
            try {
                await fetchData();
                Toast.fire({
                icon: 'success', 
                title: '検索が完了しました'
                })
                await getTotalPrice();
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
                await getTotalPrice();
            } catch ( e ){
                Toast.fire({
                icon: 'error',
                title: '検索に失敗しました。再度お試しください。'
                })
            }
        };
        
        const perPageOptions = [{ label: "10件表示", value: "10" },{ label: "50件表示", value: "50" },{ label: "100件表示", value: "100" },{ label: "1000件表示", value: "1000" }];
      
        return {
            totalPrice,
            numberFormat,
            itemSubtotal,
            fetchData,
            showPages,
            totalCount,
            values,
            openModal,
            openDownloadModal,
            onOpenDownloadModal,
            perPageOptions,
            searchClear,
            searchExec,
            onOpenModal,
            loading,
            breadcrumbs,
            rowsData
        };
    },
    data() {
      return {
        headers: [
          { name: 'id', text: 'id' },
          { name: 'accountantDate', text: '会計日' },
          { name: 'accountantId', text: '会計番号' },
          { name: 'accountantItemId', text: '会計商品ID' },
          { name: 'orderNumber', text: '発注番号' },
          { name: 'receivingNumber', text: '検収番号' },
          { name: 'divisionId', text: '部署' },
          { name: 'distributorId', text: '卸業者' },
          { name: 'method', text: '登録元' },
          { name: 'action', text: 'アクション' },
          { name: 'itemId', text: '商品ID' },
          { name: 'itemName', text: '商品名' },
          { name: 'makerName', text: 'メーカー名' },
          { name: 'itemCode', text: '製品コード' },
          { name: 'itemStandard', text: '規格' },
          { name: 'itemJANCode', text: 'JANコード' },
          { name: 'count', text: '個数' },
          { name: 'price', text: '価格' },
          { name: 'taxrate', text: '税率' },
          { name: '', text: '小計' },
        ],
      };
    },
    methods: {
      handleSortRequest(sortDetails) {
        this.values.search.sortDirection = sortDetails.direction;
        this.values.search.sortColumn = sortDetails.column;
        this.searchExec();
      }
    },
    watch: {
        'values.search.currentPage': function(val) {
            this.fetchData();
            window.scrollTo(0, 0);
        }
    }
}).mount("#top");
</script>
?>