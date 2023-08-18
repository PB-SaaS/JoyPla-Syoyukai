<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index mx-auto">
        <h1 class="text-2xl mb-2">発注商品一覧</h1>
        <hr>
        <div class="w-full flex border-b-2 border-gray-200 py-4">
          <div class="flex-auto w-1/2">
            <v-select
                name="perPage"
                :options="perPageOptions"
                @change="changeParPage"
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
          {{ (totalCount == 0)? 0 : ( parseInt(values.perPage) * ( values.currentPage - 1 ) ) + 1 }}件 - {{ (( parseInt(values.perPage) * values.currentPage )  < totalCount ) ?  parseInt(values.perPage) * values.currentPage : totalCount  }}件 / 全 {{ totalCount }}件
        </div>
        <div class="overflow-x-auto w-full mt-4">
          <table class="table-auto text-sm w-full whitespace-nowrap">
            <thead>
              <tr>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                <input
                    type="checkbox"
                    class="form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain mr-2 cursor-pointer"
                    @click="(e) => allSelected(e)"
                  />  
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                発注番号
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                作成年月
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                発注年月
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  メーカー名
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  分類
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  小分類
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  商品名
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  製品コード
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  規格
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  JANコード
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  償還フラグ
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  償還価格
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  部署名
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  卸業者名
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  保険請求分類（医科）
                </th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">
                  保険請求分類（在宅）
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(order , index) in orders" :key="index" @click="toggleSelect(order.orderCNumber)">
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">
                  <input
                    type="checkbox"
                    :checked="selected.includes(order.orderCNumber)"
                    class="form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain mr-2 cursor-pointer"
                    :value="order.orderCNumber" 
                  />
                </td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.orderNumber }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.registrationTime }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.orderTime }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.makerName }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ getLabelByValue( order.category , categoryOptions ) }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.smallCategory }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.itemName }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.itemStandard }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.itemCode }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.itemJANCode }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ (order.officialFlag === '1') ? '償還' : '' }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">&yen;{{ numberFormat(order.officialprice) }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.divisionName }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.distributorName }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.medicineCategory }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ order.homeCategory }}</td>
             </tr>
            </tbody>
          </table>
        </div>
        <v-pagination
        :show-pages="showPages"
        v-model:current-page="values.currentPage"
        :total-count="totalCount"
        :per-page="parseInt(values.perPage)"
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
                name="orderId"
                type="text"
                label="発注番号"
                title="発注番号"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="registerDate"
                type="month"
                label="作成年月"
                title="作成年月"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="orderDate"
                type="month"
                label="発注年月"
                title="発注年月"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="makerName"
                type="text"
                label="メーカー名"
                title="メーカー名"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="itemName"
                type="text"
                label="商品名"
                title="商品名"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="itemCode"
                type="text"
                label="製品コード"
                title="製品コード"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="itemStandard"
                type="text"
                label="規格"
                title="規格"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="itemJANCode"
                type="text"
                label="JANコード"
                title="JANコード"
                ></v-input>
            </div>
            <div class="my-4">
              <v-select
                :options="categoryOptions"
                name="category"
                label="分類"
                title="分類"
                ></v-select>
            </div>
            <div class="my-4">
              <v-input
                name="smallCategory"
                label="小分類"
                title="小分類"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="catalogNo"
                label="カタログNo"
                title="カタログNo"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="medicineCategory"
                label="保険請求分類（医科）"
                title="保険請求分類（医科）"
                ></v-input>
            </div>
            <div class="my-4">
              <v-input
                name="homeCategory"
                label="保険請求分類（在宅）"
                title="保険請求分類（在宅）"
                ></v-input>
            </div>
            <?php if (!gate('list_of_order_slips')->isOnlyMyDivision): ?>
            <div class="my-4">
              <v-multiple-select-division
                name="divisionIds"
                title="部署名"
                ></v-multiple-select-division>
            </div>
            <?php endif; ?>
            <div class="my-4">
              <v-multiple-select-distributor
                name="distributorIds"
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
</div>
<script>
var JoyPlaApp = Vue.createApp({
    components: {
      'v-loading' : vLoading,
      'card-button' : cardButton,
      'v-breadcrumbs': vBreadcrumbs,
      'v-button-default': vButtonDefault,
      'v-button-primary': vButtonPrimary,
      'v-button-danger': vButtonDanger,
      'header-navi' : headerNavi,
      'v-open-modal': vOpenModal,
      'v-input' : vInput ,
      'item-view': itemView,
      'v-checkbox': vCheckbox,
      'v-pagination' : vPagination,
      'v-select' : vSelect,
      'v-text' : vText,
      'v-multiple-select-division' : vMultipleSelectDivision,
      'v-multiple-select-distributor' : vMultipleSelectDistributor
    },
    setup(){
      const { ref , onMounted } = Vue;
      const { useForm } = VeeValidate;

      const loading = ref(false);
      const start = () => {
          loading.value = true;
      }

      const complete = () => {
          loading.value = false;
      }

      start();

      const getCache = () => {
          let url = window.location.href;
          name = 'isCache';
          var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
              results = regex.exec(url);
          if (!results) return null;
          if (!results[2]) return '';
          return decodeURIComponent(results[2].replace(/\+/g, " "));
      }

      const pagetitle = "ordershow";

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
        url.searchParams.set('orderId',values.orderId);
        url.searchParams.set('itemName',values.itemName);
        url.searchParams.set('makerName',values.makerName);
        url.searchParams.set('itemCode',values.itemCode);
        url.searchParams.set('itemStandard',values.itemStandard);
        url.searchParams.set('itemJANCode',values.itemJANCode);
        url.searchParams.set('category',values.category);
        url.searchParams.set('smallCategory',values.smallCategory);
        url.searchParams.set('medicineCategory',values.medicineCategory);
        url.searchParams.set('homeCategory',values.homeCategory);
        url.searchParams.set('catalogNo',values.catalogNo);
        url.searchParams.set('registerDate',values.registerDate);
        url.searchParams.set('orderDate',values.orderDate);
        url.searchParams.set('perPage',values.perPage);
        url.searchParams.set('currentPage',values.currentPage);
        url.searchParams.set('divisionIds',values.divisionIds);
        url.searchParams.set('distributorIds',values.distributorIds);
        history.pushState({}, '', url);
      }

      const { meta , validate , values , setFieldValue , resetForm} = useForm({
        initialValues: {
          orderId  : (getParam("orderId")) ? getParam("orderId") : "",
          itemName  : (getParam("itemName")) ? getParam("itemName") : "",
          makerName : (getParam("makerName")) ? getParam("makerName") : "",
          itemCode : (getParam("itemCode")) ? getParam("itemCode") : "",
          itemStandard : (getParam("itemStandard")) ? getParam("itemStandard") : "",
          itemJANCode : (getParam("itemJANCode")) ? getParam("itemJANCode") : "",
          category : (getParam("category")) ? getParam("category") : "",
          smallCategory : (getParam("smallCategory")) ? getParam("smallCategory") : "",
          catalogNo : (getParam("catalogNo")) ? getParam("catalogNo") : "",
          medicineCategory : (getParam("medicineCategory")) ? getParam("medicineCategory") : "",
          homeCategory : (getParam("homeCategory")) ? getParam("homeCategory") : "",
          registerDate: (getParam("registerDate")) ? getParam("registerDate") : "",
          orderDate: (getParam("orderDate")) ? getParam("orderDate") : "",
          perPage: (Number.isInteger(parseInt(getParam("perPage")))) ? getParam("perPage") : "10",
          currentPage : (Number.isInteger(parseInt(getParam("currentPage")))) ? parseInt(getParam("currentPage")) : 1,
          divisionIds: (getParam("divisionIds")) ? ( Array.isArray(getParam("divisionIds"))? getParam("divisionIds") : (getParam("divisionIds")).split(',') ) : [],
          distributorIds: (getParam("distributorIds")) ? ( Array.isArray(getParam("distributorIds"))? getParam("divisionIds") : (getParam("divisionIds")).split(',') ) : [],
              },
      });
      const breadcrumbs = [
          {
            text: '発注・入荷メニュー',
            disabled: false,
            href: _ROOT + '&path=/order',
          },
          {
            text: '発注商品一覧',
            disabled: true, 
          }
        ];

      const openModal = ref();

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };

      const onOpenModal = () => {
        openModal.value.open();
      }
      
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

      const showPages = ref(5);
      const totalCount = ref(0);

      const orders = ref([]);
            
      const perPageOptions = [{ label: "10件表示", value: "10" },{ label: "50件表示", value: "50" },{ label: "100件表示", value: "100" }];


      const searchCount = ref(0);
      
      const listGet = () => {
        let params = new URLSearchParams();
        params.append("path", "/api/order/items");
        params.append("search", JSON.stringify(encodeURIToObject(values)));
        params.append("_csrf", _CSRF);

        setParam(values);

        start();

        axios.post(_APIURL,params)
        .then( (response) => {
          orders.value = response.data.data.map(order => {
            return order;
          });
          totalCount.value = parseInt(response.data.count);
        }) 
        .catch((error) => {
          complete();
          if(searchCount.value > 0){
            Toast.fire({
              icon: 'error',
              title: '検索に失敗しました。再度お試しください。'
            })
          }
          searchCount.value ++;
        })
        .then(() => {
          complete();
          if(searchCount.value > 0){
            Toast.fire({
              icon: 'success', 
              title: '検索が完了しました'
            })
          }
          searchCount.value ++;
        });
      };

      const changeParPage = () =>
      {
        values.currentPage = 1;
        listGet();
      };

      const add = (elem) => {
        context.emit('additem',elem);
        Toast.fire({
          icon: 'success',
          title: '反映しました'
        })
      };

      onMounted(() => {
        listGet();
      });

      const searchExec = () => {
        values.currentPage = 1;
        listGet();
      };
      
      const searchClear = () =>
      {
        values.currentPage = 1;
        values.orderId = '';
        values.itemName = '';
        values.makerName = '';
        values.itemCode = '';
        values.itemStandard = '';
        values.itemJANCode = '';
        values.category = '';
        values.smallCategory = '';
        values.catalogNo = '';
        values.medicineCategory = '';
        values.homeCategory = '';
        values.yearMonth = '';
        values.divisionIds = [];
        values.distributorIds = [];
        listGet();
      };

      const openPrint = ( url ) => {
        location.href = _ROOT + "&path=/order/" + url + "/print";    
      }

      const selected = ref([]);

      const toggleSelect = (orderCNumber) => {
        if (selected.value.includes(orderCNumber)) {
          selected.value.splice(selected.value.findIndex(v => v === orderCNumber), 1)
        } else {
          selected.value.push(orderCNumber)
        }
      } 

      const allSelected = (e) => {
         selected.value = [];
        if(e.target.checked) {
          orders.value.map((order) => {
            selected.value.push(order.orderCNumber)
          });
        }
      }

      const categoryOptions = [
        { label: "---選択してください---", value: "" },
        { label: "医療材料", value: "1" },
        { label: "薬剤", value: "2" },
        { label: "試薬", value: "3" },
        { label: "日用品", value: "4" },
        { label: "その他", value: "99" }
      ];

      const getLabelByValue = (value, options) => {
        if(!value){ return null; } 
        const foundOption = options.find(option => option.value === value);
        return foundOption ? foundOption.label : null;
      }

      return {
        loading, 
        start, 
        complete,
        openPrint,
        searchClear,
        searchExec,
        changeParPage,
        perPageOptions,
        listGet,
        totalCount,
        showPages,
        values,
        orders,
        onOpenModal,
        openModal,
        breadcrumbs,
        numberFormat,
        selected,
        toggleSelect,
        allSelected,
        categoryOptions,
        getLabelByValue
      }
  },
  watch: {
    'values.currentPage': function(val) {
      this.listGet();
      window.scrollTo(0, 0);
    }
  }
}).mount('#top');
</script> 