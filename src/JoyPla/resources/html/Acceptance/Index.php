<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <h1 class="text-2xl mb-2">出庫一覧</h1>
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
        <div class="pt-2 hover:bg-sushi-50" v-for="(acceptance) in acceptances">
          <div class="border-b-2 border-solid border-gray-100 w-full">
            <div class="lg:flex lg:divide-x ">
              <div class="lg:w-1/5 p-2">
                <p class="text-md font-bold">出庫日<br>{{ acceptance.acceptanceDate }}</p>
                <p class="text-md">
                  払出元部署：{{ acceptance?._sourceDivision?.divisionName }}<br>
                  払出先部署：{{ acceptance?._targetDivision?.divisionName }}<br>
                  合計金額：&yen; {{ numberFormat( acceptance.acceptancePrice) }}<br>
                </p>
                <p class="text-md my-2" v-if="!isUser || (isUser && ( userDivisionId === acceptance?._targetDivision?.divisionId || userDivisionId === acceptance?._sourceDivision?.divisionId ))">
                  ステータス：
                  <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="acceptance.payoutTotalCount == 0 && acceptance.acceptanceTotalCount > 0">
                    未入庫
                  </span>
                  <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="acceptance.payoutTotalCount > 0 && acceptance.payoutTotalCount !== acceptance.acceptanceTotalCount">
                    一部未入庫
                  </span>
                  <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="acceptance.payoutTotalCount == acceptance.acceptanceTotalCount">
                    入庫済み
                  </span>
                </p>
                <div class="flex flex-col gap-3 mb-3" v-if="!isUser || (isUser && ( userDivisionId === acceptance?._targetDivision?.divisionId || userDivisionId === acceptance?._sourceDivision?.divisionId ))">
                  <v-button-default type="button" class="w-full" @click.native="openSlip( acceptance.acceptanceId )">
                    出庫伝票を表示
                  </v-button-default>
                </div>
                <?php if(!gate('is_approver')): ?>
                <div class="flex flex-col gap-3 mb-3" v-if="!isUser || (isUser && userDivisionId === acceptance?._targetDivision?.divisionId)">
                  <v-button-primary type="button" class="w-full" @click.native="onPayoutAllUpdate( acceptance.acceptanceId )">
                    一括入庫登録
                  </v-button-primary>
                </div>
                <?php endif; ?>
                <div class="flex flex-col gap-3 mb-3" v-if="!isUser || (isUser && ( userDivisionId === acceptance?._targetDivision?.divisionId || userDivisionId === acceptance?._sourceDivision?.divisionId ))">
                  <v-button-default type="button" class="w-full" @click.native="openPrint( acceptance.acceptanceId )">
                    出庫伝票を印刷
                  </v-button-default>
                </div>
              </div>
              <div class="lg:w-4/5 p-2">
                <template v-if="isUser && acceptance._items.length === 0">
                  <p>閲覧権限がありません</p>
                </template>
                <div class="w-full lg:flex mt-3" v-for="(acceptanceItem) in acceptance._items">
                  <div class="lg:flex-1 flex lg:w-3/4">
                    <div class="flex-1 pl-4 lg:flex gap-6 break-all">
                      <div class="flex-auto lg:w-1/3 w-full">
                        <h3 class="text-xl font-bold font-heading">{{ acceptanceItem.makerName }}</h3>
                        <p class="text-md font-bold font-heading">{{ acceptanceItem.itemName }}</p>
                        <p class="text-md text-gray-500">{{ acceptanceItem.itemCode }}</p>
                        <p class="text-md text-gray-500">{{ acceptanceItem.itemStandard }}</p>
                        <p class="text-md text-gray-500">{{ acceptanceItem.itemJANCode }}</p>
                      </div>
                      <div class="flex-auto lg:w-2/3 w-full">
                        <p class="text-md text-gray-900" v-if="( acceptanceItem.lotNumber != '' && acceptanceItem.lotDate != '' )">
                        ロット情報 : {{ acceptanceItem.lotNumber }} / {{ acceptanceItem.lotDate }} 
                        </p>
                        <p class="text-base text-gray-900">
                          出庫数 : {{ numberFormat(acceptanceItem.acceptanceCount) }}{{ acceptanceItem.quantityUnit }} / 入庫数 : {{ numberFormat(acceptanceItem.payoutCount) }}{{ acceptanceItem.quantityUnit }}
                        </p>
                        <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="status(acceptanceItem) == 1">
                          未入庫
                        </span>
                        <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="status(acceptanceItem) == 2">
                          一部未入庫 
                        </span>
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="status(acceptanceItem) == 3">
                          入庫済み
                        </span>
                        <p>
                          <span class="text-blue-700 text-lg mr-4">&yen; {{ numberFormat(acceptanceItem.acceptancePrice) }}</span>
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
                name="yearMonth"
                type="month"
                label="出庫年月"
                title="出庫年月"
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

      const pagetitle = "acceptanceindex";

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
        url.searchParams.set('acceptanceDate',values.acceptanceDate);
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
          acceptanceDate: (getParam("acceptanceDate")) ? getParam("acceptanceDate") : "",
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
            text: '出庫一覧',
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

      const acceptances = ref([]);
            
      const perPageOptions = [{ label: "10件表示", value: "10" },{ label: "50件表示", value: "50" },{ label: "100件表示", value: "100" }];


      const searchCount = ref(0);
      
      const listGet = () => {
        let params = new URLSearchParams();
        params.append("path", "/api/acceptance/index");
        params.append("search", JSON.stringify(encodeURIToObject(values)));
        params.append("_csrf", _CSRF);

        setParam(values);

        start();

        axios.post(_APIURL,params)
        .then( (response) => {
          acceptances.value = response.data.data;
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
        values.acceptanceDate = '';
        values.sourceDivisionIds = [];
        values.targetDivisionIds = [];
        listGet();
      };

      const openSlip = ( url ) => {
        location.href = _ROOT + "&path=/acceptance/" + url;    
      }
      const openPrint = ( id ) => {
        let url = _ROOT + "&path=/acceptance/" + id + "/print";  
        window.open(url, '_blank')   
      }
      
      const registerAllPayout = (acceptanceId) => {
        let params = new URLSearchParams();
        params.append("path", "/api/acceptance/"+acceptanceId + "/payout");
        params.append("_method", 'post');
        params.append("_csrf", _CSRF);
        params.append("isAll", 'true');
        return axios.post(_APIURL,params);
      }
      const payoutAllReg = async (acceptanceId) => {
        const res = await registerAllPayout(acceptanceId);
        if(res.data.code != 200) {
          throw new Error(res.data.message)
        }
      };

      const onPayoutAllUpdate = async (acceptanceId) => {
        Swal.fire({
            title: '一括入庫登録',
            text: "一括入庫登録を行います。\r\nよろしいですか？",
            icon: 'warning',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
        }).then(async (result) => {
            if (result.isConfirmed) {
              start();
              await payoutAllReg(acceptanceId);
              complete();
              await Swal.fire({
                icon: 'success',
                title: '一括入庫登録が完了しました',
              }).then((result) => {
                  location.reload();
              });
            }
        }).catch((error) => {
          complete();
          Swal.fire({
            icon: 'error',
            title: 'システムエラー',
            text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
          });
        })
      }

      const status = (item) => {
        const payoutCount = parseInt(item.payoutCount)
        const acceptanceCount = parseInt(item.acceptanceCount)
        if(payoutCount == 0 && acceptanceCount > 0) {
          return 1
        }
        if(payoutCount > 0 && payoutCount < acceptanceCount) {
          return 2
        }
        if(payoutCount == acceptanceCount) {
          return 3
        }
      }

      return {
        status,
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
        acceptances,
        onOpenModal,
        openModal,
        breadcrumbs,
        numberFormat,
        onPayoutAllUpdate,
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