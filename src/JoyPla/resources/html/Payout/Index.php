<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <h1 class="text-2xl mb-2">払出一覧</h1>
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
        <div class="relative overflow-x-auto sm:rounded-lg">
          <table class="w-full text-sm text-left text-gray-500 ">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                  <tr>
                      <th scope="col" class="px-6 py-3">
                          払出日
                      </th>
                      <th scope="col" class="px-6 py-3">
                      払出番号
                      </th>
                      <th scope="col" class="px-6 py-3">
                      払出元部署
                      </th>
                      <th scope="col" class="px-6 py-3">
                      払出先部署
                      </th>
                      <th scope="col" class="px-6 py-3">
                      合計金額
                      </th>
                      <th scope="col">
                      </th>
                      <th scope="col">
                      </th>
                  </tr>
              </thead>
              <tbody>
                  <tr class="bg-white border-b " v-for="(payout) in payouts">
                      <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                      {{ payout.payoutDate }}
                      </th>
                      <td class="px-6 py-4">
                      {{ payout?.payoutHistoryId }}
                      </td>
                      <td class="px-6 py-4">
                      {{ payout?._sourceDivision?.divisionName }}
                      </td>
                      <td class="px-6 py-4">
                      {{ payout?._targetDivision?.divisionName }}
                      </td>
                      <td class="px-6 py-4">
                      &yen; {{ numberFormat( payout.totalAmount) }}
                      </td>
                      <td class="px-6 py-4">
                        <v-button-default type="button" class="w-full" @click.native="openSlip( payout.payoutHistoryId )">
                          払出伝票を表示
                        </v-button-default>
                      </td>
                      <td class="px-6 py-4">
                        <v-button-default type="button" class="w-full" @click.native="openPrint(payout.payoutHistoryId)">
                            払出伝票印刷
                        </v-button-default>
                      </td>
                  </tr>
              </tbody>
          </table>
        </div>
          <?php /*
        <div class="pt-2 hover:bg-sushi-50" v-for="(payout) in payouts">
          <div class="border-b-2 border-solid border-gray-100 w-full">
            <div class="lg:flex lg:divide-x ">
              
              <div class="lg:w-1/5 p-2">
                <p class="text-md font-bold">払出日<br>{{ payout.payoutDate }}</p>
                <p class="text-md">
                  払出番号：{{ payout?.payoutHistoryId }}<br>
                  払出元部署：{{ payout?._sourceDivision?.divisionName }}<br>
                  払出先部署：{{ payout?._targetDivision?.divisionName }}<br>
                  合計金額：&yen; {{ numberFormat( payout.totalAmount) }}<br>
                </p>
                <div class="flex flex-col gap-3 mb-3" v-if="!isUser || ( isUser && (userDivisionId === payout.sourceDivisionId || userDivisionId === payout.targetDivisionId) )">
                  <v-button-default type="button" class="w-full" @click.native="openSlip( payout.payoutHistoryId )">
                    払出伝票を表示
                  </v-button-default>
                </div>
                <div class="flex flex-col gap-3 mb-3" v-if="!isUser || ( isUser && ( userDivisionId === payout.sourceDivisionId || userDivisionId === payout.targetDivisionId ) )">
                  <v-button-default type="button" class="w-full" @click.native="openPrint(payout.payoutHistoryId)">
                      払出伝票印刷
                  </v-button-default>
                </div>
              </div>
              <div class="lg:w-4/5 p-2">
                <template v-if="isUser && payout._items.length === 0">
                  <p>閲覧権限がありません</p>
                </template>
                <div class="w-full lg:flex mt-3" v-for="(payoutItem) in payout._items">
                  <div class="lg:flex-1 flex lg:w-3/4">
                    <div class="flex-1 pl-4 lg:flex gap-6 break-all">
                      <div class="flex-auto lg:w-1/3 w-full">
                        <h3 class="text-xl font-bold font-heading">{{ payoutItem.makerName }}</h3>
                        <p class="text-md font-bold font-heading">{{ payoutItem.itemName }}</p>
                        <p class="text-md text-gray-500">{{ payoutItem.itemCode }}</p>
                        <p class="text-md text-gray-500">{{ payoutItem.itemStandard }}</p>
                        <p class="text-md text-gray-500">{{ payoutItem.itemJANCode }}</p>
                      </div>
                      <div class="flex-auto lg:w-2/3 w-full">
                        <p class="text-md text-gray-900" v-if="( payoutItem.lotNumber != '' && payoutItem.lotDate != '' )">
                          ロット情報 : {{ payoutItem.lotNumber }} / {{ payoutItem.lotDate }} 
                        </p>
                        <p class="text-base text-gray-900">
                          払出数 : {{ numberFormat(payoutItem.payoutQuantity) }}{{ payoutItem.quantityUnit }}
                        </p>
                        <p>
                          <span class="text-blue-700 text-lg mr-4">&yen; {{ numberFormat(payoutItem.payoutAmount) }}</span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
          */ ?>
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
                name="yearMonth"
                type="month"
                label="払出年月"
                title="払出年月"
                ></v-input>
            </div>
            <?php
            /*
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
            */ ?>
            <div class="my-4">
              <v-multiple-select-division
                name="sourceDivisionIds"
                id="sourceDivisionIdMulti"
                title="払出元部署名"
                ></v-multiple-select-division>
            </div>
            <div class="my-4">
              <v-multiple-select-division
                name="targetDivisionIds"
                id="targetDivisionIdMulti"
                title="払出先部署名"
                ></v-multiple-select-division>
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
      
      const isUser = <?php echo (gate('is_user'))? 'true' : 'false' ?>;
      const userDivisionId = '<?php echo $userDivisionId ?>';

      const getCache = () => {
          let url = window.location.href;
          name = 'isCache';
          var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
              results = regex.exec(url);
          if (!results) return null;
          if (!results[2]) return '';
          return decodeURIComponent(results[2].replace(/\+/g, " "));
      }

      const pagetitle = "payoutindex";

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
        url.searchParams.set('payoutDate',values.payoutDate);
        url.searchParams.set('perPage',values.perPage);
        url.searchParams.set('currentPage',values.currentPage);
        url.searchParams.set('sourceDivisionIds',values.sourceDivisionIds);
        url.searchParams.set('targetDivisionIds',values.targetDivisionIds);
        history.pushState({}, '', url);
      }

      const { meta , validate , values , setFieldValue , resetForm} = useForm({
        initialValues: {
          itemName  : (getParam("itemName")) ? getParam("itemName") : "",
          makerName : (getParam("makerName")) ? getParam("makerName") : "",
          itemCode : (getParam("itemCode")) ? getParam("itemCode") : "",
          itemStandard : (getParam("itemStandard")) ? getParam("itemStandard") : "",
          itemJANCode : (getParam("itemJANCode")) ? getParam("itemJANCode") : "",
          yearMonth: (getParam("yearMonth")) ? getParam("yearMonth") : "",
          payoutDate: (getParam("payoutDate")) ? getParam("payoutDate") : "",
          perPage: (Number.isInteger(parseInt(getParam("perPage")))) ? getParam("perPage") : "10",
          currentPage : (Number.isInteger(parseInt(getParam("currentPage")))) ? parseInt(getParam("currentPage")) : 1,
          sourceDivisionIds: (getParam("sourceDivisionIds")) ? ( Array.isArray(getParam("sourceDivisionIds"))? getParam("sourceDivisionIds") : (getParam("sourceDivisionIds")).split(',') ) : [],
          targetDivisionIds: (getParam("targetDivisionIds")) ? ( Array.isArray(getParam("targetDivisionIds"))? getParam("targetDivisionIds") : (getParam("targetDivisionIds")).split(',') ) : [],
        },
      });
      const breadcrumbs = [
          {
            text: '払出メニュー',
            disabled: false,
            href: _ROOT + '&path=/payout',
          },
          {
            text: '払出一覧',
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

      const payouts = ref([]);
            
      const perPageOptions = [{ label: "10件表示", value: "10" },{ label: "50件表示", value: "50" },{ label: "100件表示", value: "100" }];


      const searchCount = ref(0);
      
      const listGet = () => {
        let params = new URLSearchParams();
        params.append("path", "/api/payout/index");
        params.append("search", JSON.stringify(encodeURIToObject(values)));
        params.append("_csrf", _CSRF);

        setParam(values);

        start();

        axios.post(_APIURL,params)
        .then( (response) => {
          payouts.value = response.data.data;
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
        values.itemName = '';
        values.makerName = '';
        values.itemCode = '';
        values.itemStandard = '';
        values.itemJANCode = '';
        values.yearMonth = '';
        values.payoutDate = '';
        values.sourceDivisionIds = [];
        values.targetDivisionIds = [];
        listGet();
      };

      const openSlip = ( url ) => {
        location.href = _ROOT + "&path=/payout/" + url;    
      }
      const openPrint = ( id ) => {
        let url = _ROOT + "&path=/payout/" + id + "/print";   
        window.open(url, '_blank') 
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
        payouts,
        onOpenModal,
        openModal,
        breadcrumbs,
        numberFormat,
        userDivisionId,
        isUser,
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