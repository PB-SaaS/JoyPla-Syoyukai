<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <h1 class="text-2xl mb-2">商品リスト</h1>
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
        <div class="w-full md:flex border-b-2 border-gray-200 py-4">
          <div class="flex-auto md:w-1/5" data-micromodal-trigger="openCreateModal">
            <v-button-primary type="button" class="w-full" @click="onOpenCreateModal">商品リスト作成</v-button-primary>
          </div>
          <div class="flex-auto md:w-4/5">
          </div>
        </div>
        <div>
          {{ (totalCount == 0)? 0 : ( parseInt(values.perPage) * ( values.currentPage - 1 ) ) + 1 }}件 - {{ (( parseInt(values.perPage) * values.currentPage )  < totalCount ) ?  parseInt(values.perPage) * values.currentPage : totalCount  }}件 / 全 {{ totalCount }}件
        </div>
        <div class="mt-4">
          <table class="table-auto w-full text-sm">
            <thead>
              <tr>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">No</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">部署</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">名称</th>
                <th class="border-b font-medium p-4 pr-8 pt-0 pb-3 text-left">品目数</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(itemList) in itemLists">
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ itemList._id }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ itemList._division?.divisionName }}</td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">
                  {{ itemList.itemListName }}
                  <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-green-600 bg-green-200 uppercase last:mr-0 mr-1" v-if="itemList.usableStatus == '1'">部署限定</span>
                </td>
                <td class="border-b border-slate-100 p-4 pr-8 text-slate-500">{{ numberFormat(itemList.itemsNumber) }}</td>
                <td class="border-b border-slate-100 p-4" v-if="
                  itemList.usableStatus == '0' || 
                  (itemList.usableStatus == '1' && ('%val:usr:userPermission:id%' == '1' || '%val:usr:userPermission:id%' == '3')) || 
                  (itemList.usableStatus == '1' && itemList.divisionId == '%val:usr:divisionId%' && '%val:usr:userPermission:id%' == '2')
                ">
                  <v-button-primary type="button" @click.native="openSlip(itemList.itemListId)">詳細</v-button-primary>
                </td>
                <td class="border-b border-slate-100 p-4" v-else>
                </td>
                <td class="border-b border-slate-100 p-4" v-if="
                  itemList.usableStatus == '0' || 
                  (itemList.usableStatus == '1' && ('%val:usr:userPermission:id%' == '1' || '%val:usr:userPermission:id%' == '3')) || 
                  (itemList.usableStatus == '1' && itemList.divisionId == '%val:usr:divisionId%' && '%val:usr:userPermission:id%' == '2')
                ">
                  <v-button-danger type="button" @click.native="deleteSlip(itemList.itemListId)">削除</v-button-danger>
                </td>
                <td class="border-b border-slate-100 p-4" v-else>
                </td>
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
  <v-open-modal ref="openCreateModal" headtext="商品リスト作成" id="openCreateModal">
    <div class="flex flex-col">
      <div class="w-full overflow-y-auto" style="max-height: 85vh;">
        <div class="flex flex-wrap">
          <div class="w-full px-3 my-6 md:mb-0">
            <div class="my-4">
              <v-select-division
                name="register.divisionId"
                label="部署" 
                title="部署指定"
                :absolute="false"
                :is-only-my-division="<?php var_export(
                    gate('list_of_itemList_slips')->isOnlyMyDivision()
                ); ?>"
              ></v-select-division>
            </div>
            <div class="my-4">
              <v-select
                name="register.usableStatus"
                label="使用権限" 
                title="使用権限設定"
                :absolute="true"
                :options="[{ label: '全体', value: 0 },{ label: '部署限定', value: 1 }]"
              ></v-select>
            </div>
            <div class="my-4">
              <v-input
                name="register.itemListName"
                type="text"
                label="商品リスト名称"
                title="商品リスト名称"
                ></v-input>
            </div>
            <div class="mx-auto lg:w-2/3 mb-4 text-center flex items-center gap-6 justify-center">
              <v-button-primary type="button" @click.native="createItemList">作成</v-button-primary>
            </div>
          </div>
        </div>
      </div>
    </div>
  </v-open-modal>
  <v-open-modal ref="openModal" headtext="絞り込み" id="openModal">
    <div class="flex flex-col">
      <div class="w-full overflow-y-auto" style="max-height: 85vh;">
        <div class="flex flex-wrap">
          <div class="w-full px-3 my-6 md:mb-0">
            <div class="my-4">
              <v-multiple-select-division
                name="divisionIds"
                title="部署名"
                ></v-multiple-select-division>
            </div>
            <div class="my-4">
              <v-input
                name="itemListName"
                type="text"
                label="商品リスト名称"
                title="商品リスト名称"
                ></v-input>
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
    <v-input type="hidden" name="makerName"></v-input>
    <v-input type="hidden" name="itemName"></v-input>
    <v-input type="hidden" name="itemCode"></v-input>
    <v-input type="hidden" name="itemStandard"></v-input>
    <v-input type="hidden" name="itemJANCode"></v-input>
    <v-input type="hidden" name="currentPage"></v-input>
    <v-input type="hidden" name="perPage"></v-input>
  </div>
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
      'v-select-division' : vSelectDivision,
      'v-select-distributor' : vSelectDistributor,
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

      const pagetitle = "itemListIndex";

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
        url.searchParams.set('yearMonth',values.yearMonth);
        url.searchParams.set('perPage',values.perPage);
        url.searchParams.set('currentPage',values.currentPage);
        url.searchParams.set('divisionIds',values.divisionIds);
        url.searchParams.set('distributorIds',values.distributorIds);
        history.pushState({}, '', url);
      }

      const { meta , validate , values , setFieldValue , resetForm} = useForm({
        initialValues: {
          perPage: (Number.isInteger(getParam("perPage"))) ? getParam("perPage") : "10",
          currentPage : (Number.isInteger(parseInt(getParam("currentPage")))) ? parseInt(getParam("currentPage")) : 1,
          divisionIds: (getParam("divisionIds")) ? ( Array.isArray(getParam("divisionIds"))? getParam("divisionIds") : (getParam("divisionIds")).split(',') ) : [],
          itemListName: (getParam("itemListName")) ? getParam("itemListName") : "",
          register : {
            divisionId : '',
            usableStatus: 0,
            itemListName : '',
          }
        },
      });

      const breadcrumbs = [
          {
            text: '商品メニュー',
            disabled: false,
            href: _ROOT + '&path=/product',
          },
          {
            text: '商品リスト',
            disabled: true, 
          }
        ];

      const openModal = ref();
      const openCreateModal = ref();

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };

      const onOpenModal = () => {
        openModal.value.open();
      }
      
      const onOpenCreateModal = () => {
        openCreateModal.value.open();
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

      const itemLists = ref([]);
            
      const perPageOptions = [{ label: "10件表示", value: "10" },{ label: "50件表示", value: "50" },{ label: "100件表示", value: "100" }];

      const searchCount = ref(0);
      
      const listGet = () => {
        let params = new URLSearchParams();
        params.append("path", "/api/product/itemList/index");
        params.append("search", JSON.stringify(encodeURIToObject(values)));
        params.append("_csrf", _CSRF);

        setParam(values);

        start();

        axios.post(_APIURL,params)
        .then( (response) => {
          itemLists.value = response.data.data;
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
        values.yearMonth = '';
        values.divisionIds = [];
        values.distributorIds = [];
        listGet();
      };

      const openSlip = ( url ) => {
        location.href = _ROOT + "&path=/product/itemList/" + url;    
      }

      const deleteSlip = ( itemListId ) => 
      {
          Swal.fire({
            title: '商品リストを削除',
            text: "削除後は元に戻せません。\r\nよろしいですか？",
            icon: 'warning',
            confirmButtonText: '削除します',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
          }).then( async (result) => {
            if(result.isConfirmed){
              start();
              
              let params = new URLSearchParams();
              params.append("path", "/api/product/itemList/"+itemListId+"/delete");
              params.append("_method", 'delete');
              params.append("_csrf", _CSRF);

              const res = await axios.post(_APIURL,params);
              
              complete();
              if(res.data.code != 200) {
                throw new Error(res.data.message)
              }
              Swal.fire({
                  icon: 'success',
                  title: '商品リストの削除が完了しました。',
              }).then((result) => {
                location.reload();
              });
              return true ;
            }
          }).catch((error) => {
            Swal.fire({
              icon: 'error',
              title: 'システムエラー',
              text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
            });
          });
      }

      const createItemList = () => {
        let params = new URLSearchParams();
        params.append("divisionId", values.register.divisionId);
        params.append("usableStatus", values.register.usableStatus);
        params.append("itemListName", encodeURI(values.register.itemListName));
        params.append("_csrf", _CSRF);
        params.append("_method", 'post');
        params.append("path", "/api/product/itemList/register");

        start();

        axios.post(_APIURL,params)
        .then( (response) => {
          if(response.data.code != 200){
            Swal.fire({
              icon: 'error',
              title: 'システムエラー',
              text: '商品リストの作成に失敗しました。\r\nしばらく経ってから再度送信してください。',
            });
            return '';
          }
          Swal.fire({
            icon: 'success',
            title: '商品リストの作成が完了しました。',
          }).then( () => {
            location.reload();
          })
        }) 
        .catch((error) => {
          Swal.fire({
            icon: 'error',
            title: 'システムエラー',
            text: '商品リストの作成に失敗しました。\r\nしばらく経ってから再度送信してください。',
          });
        })
        .then(() => {
          complete();
        });
      }

      return {
        deleteSlip,
        loading, 
        start, 
        complete,
        openSlip,
        searchClear,
        searchExec,
        changeParPage,
        perPageOptions,
        listGet,
        totalCount,
        showPages,
        values,
        itemLists,
        onOpenModal,
        openModal,
        onOpenCreateModal,
        openCreateModal,
        breadcrumbs,
        numberFormat,
        createItemList,
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