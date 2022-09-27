<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <h1 class="text-2xl mb-2">入荷照合</h1>
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
        <div class="pt-2 hover:bg-sushi-50" v-for="(order) in orders">
          <div class="border-b-2 border-solid border-gray-100 w-full">
            <div class="lg:flex lg:divide-x ">
              <div class="lg:w-1/5 p-2">
                <p class="text-md font-bold">登録日時<br>{{ order.registDate }}</p>
                <p class="text-md font-bold">発注日時<br>{{ order.orderDate }}</p>
                <p class="text-md">
                  発注番号：{{ order.orderId }}<br>
                  発注ステータス：
                  <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 2">
                    {{ order.orderStatusToString}} 
                  </span>
                  <span class="bg-amber-100 text-amber-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 3">
                    {{ order.orderStatusToString}} 
                  </span>
                  <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 4">
                    {{ order.orderStatusToString}} 
                  </span>
                  <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 5">
                    {{ order.orderStatusToString}} 
                  </span>
                  <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 6">
                    {{ order.orderStatusToString}} 
                  </span>
                  <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 7">
                    {{ order.orderStatusToString}} 
                  </span>
                  <span class="bg-zinc-100 text-zinc-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 8">
                    {{ order.orderStatusToString}} 
                  </span>
                  <br>
                  発注元部署：{{ order.division.divisionName }}<br>
                  合計金額：&yen; {{ numberFormat( order.totalAmount) }}
                </p>
                <div class="flex flex-col gap-3">
                  <v-button-default type="button" class="w-full" @click.native="openSlip( order.orderId )">
                    入荷照合を行う
                  </v-button-default>
                </div>
              </div>
              <div class="lg:w-4/5 p-2">
                <div class="w-full lg:flex mt-3" v-for="(orderItem) in order.orderItems">
                  <div class="lg:flex-1 flex lg:w-3/4">
                    <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="orderItem.itemImage"></item-view>
                    <div class="flex-1 pl-4 lg:flex gap-6 break-all">
                      <div class="flex-auto lg:w-4/5 w-full">
                        <h3 class="text-xl font-bold font-heading">{{ orderItem.item.makerName }}</h3>
                        <p class="text-md font-bold font-heading">{{ orderItem.item.itemName }}</p>
                        <p class="text-md text-gray-500">{{ orderItem.item.itemCode }}</p>
                        <p class="text-md text-gray-500">{{ orderItem.item.itemStandard }}</p>
                        <p class="text-md text-gray-500">{{ orderItem.item.itemJANCode }}</p>
                        <p class="text-base text-gray-900">
                        {{ numberFormat(orderItem.orderQuantity) }}{{ orderItem.quantity.itemUnit }}
                        </p>
                        <p>
                          <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="orderItem.orderItemReceivedStatus == 1">
                            {{ orderItem.orderItemReceivedStatusToString }}
                          </span>
                          <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="orderItem.orderItemReceivedStatus == 2">
                            {{ orderItem.orderItemReceivedStatusToString }}
                          </span>
                          <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="orderItem.orderItemReceivedStatus == 3">
                            {{ orderItem.orderItemReceivedStatusToString }}
                          </span>
                        </p>
                        <p>
                          <span class="text-blue-700 text-lg mr-4">&yen; {{ numberFormat(orderItem.orderPrice) }}</span>
                          <span class="text-sm text-gray-900">( &yen; {{ numberFormat(orderItem.price) }} / {{ orderItem.quantity.itemUnit }} )</span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
            <?php if(! gate('list_of_order_slips' )->isOnlyMyDivision ): ?>
            <div class="my-4">
              <v-multiple-select-division
                name="divisionIds"
                title="発注書元部署名"
                ></v-multiple-select-division>
            </div>
            <?php endif ?>
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
      'v-pagination' : vPagination,
      'v-select' : vSelect,
      'v-text' : vText,
      'v-multiple-select-division' : vMultipleSelectDivision
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

      const pagetitle = "receivedOrderList";

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
        url.searchParams.set('registerDate',values.registerDate);
        url.searchParams.set('orderDate',values.orderDate);
        url.searchParams.set('perPage',values.perPage);
        url.searchParams.set('currentPage',values.currentPage);
        url.searchParams.set('divisionIds',values.divisionIds);
        history.pushState({}, '', url);
      }

      const { meta , validate , values , setFieldValue , resetForm} = useForm({
        initialValues: {
          itemName  : (getParam("itemName")) ? getParam("itemName") : "",
          makerName : (getParam("makerName")) ? getParam("makerName") : "",
          itemCode : (getParam("itemCode")) ? getParam("itemCode") : "",
          itemStandard : (getParam("itemStandard")) ? getParam("itemStandard") : "",
          itemJANCode : (getParam("itemJANCode")) ? getParam("itemJANCode") : "",
          registerDate: (getParam("registerDate")) ? getParam("registerDate") : "",
          orderDate: (getParam("orderDate")) ? getParam("orderDate") : "",
          perPage: (Number.isInteger(getParam("perPage"))) ? getParam("perPage") : "10",
          currentPage : (Number.isInteger(parseInt(getParam("currentPage")))) ? parseInt(getParam("currentPage")) : 1,
          divisionIds: (getParam("divisionIds")) ? ( Array.isArray(getParam("divisionIds"))? getParam("divisionIds") : (getParam("divisionIds")).split(',') ) : [],
        },
      });
      const breadcrumbs = [
          {
            text: '発注メニュー',
            disabled: false,
            href: _ROOT + '&path=/order',
          },
          {
            text: '入荷照合',
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
        params.append("path", "/api/received/order/list");
        params.append("search", JSON.stringify(encodeURIToObject(values)));
        params.append("_csrf", _CSRF);

        setParam(values);

        start();

        axios.post(_APIURL,params)
        .then( (response) => {
          orders.value = response.data.data;
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
        resetForm({
          itemName  : "",
          makerName : "",
          itemCode : "",
          itemStandard :  "",
          itemJANCode :  "",
          registerDate: "",
          orderDate: "",
          divisionIds: [],
          currentPage : 1,
          perPage: values.perPage,
        });
        listGet();
      };

      const openSlip = ( url ) => {
        location.href = _ROOT + "&path=/received/order/" + url;    
      }
      const openPrint = ( url ) => {
        location.href = _ROOT + "&path=/received/order/" + url + "/print";    
      }

      return {
        loading, 
        start, 
        complete,
        openPrint,
        openSlip,
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