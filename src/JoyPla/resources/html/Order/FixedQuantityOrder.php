<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <h1 class="text-2xl mb-2">定数発注</h1>
        <hr>
        <div class="w-full flex border-b-2 border-gray-200 py-4">
          <div class="flex-auto w-1/2">
            <v-select name="perPage" :options="perPageOptions" @change="changeParPage"></v-select>
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
        <div class="mt-4 p-4 shadow-md drop-shadow-md" v-if="fields.length > 0">
          <p class=" text-xl">登録アイテム数: {{ numberFormat(itemCount()) }} アイテム</p>
          <p class=" text-xl">合計金額: &yen; {{ numberFormat(totalAmount()) }} </p>
          <v-button-primary type="button" class="w-full" @click.native="onSubmit">発注登録</v-button-primary>
        </div>
        <div class="pt-2 w-full lg:flex mt-3" v-for="(stock , idx) in fields">
          <div class="lg:flex-1 flex">
            <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="stock.value.itemImage"></item-view>
            <div class="flex-1 pl-4 lg:flex gap-6 break-all">
              <div class="flex-auto lg:w-2/3 w-full">
                <h3 class="text-xl font-bold font-heading">{{ stock.value.division.divisionName }}[ {{ stock.value.rackName }} ]</h3>
                <h3 class="text-xl font-bold font-heading">{{ stock.value.item.makerName }}</h3>
                <p class="text-md font-bold font-heading">{{ stock.value.item.itemName }}</p>
                <p class="text-md text-gray-500">{{ stock.value.item.itemCode }}</p>
                <p class="text-md text-gray-500">{{ stock.value.item.itemStandard }}</p>
                <p class="text-md text-gray-500">
                  {{ stock.value.item.itemJANCode }}
                </p>
                <div class="w-full text-lg font-bold font-heading flex gap-6">
                  <span class="text-xl text-orange-600 font-bold font-heading">&yen; {{ numberFormat(stock.value.price) }}/{{ stock.value.quantity.itemUnit }}</span>
                  <blowing :message="stock.value.priceNotice" title="金額管理備考" v-if="stock.value.priceNotice != ''"></blowing>
                </div>
                <p class="text-base text-gray-900">
                  {{ numberFormat(stock.value.quantity.quantityNum) }}{{ stock.value.quantity.quantityUnit }}
                </p>
                <p class="text-base text-gray-900">
                  {{ stock.value.distributor.distributorName }}
                </p>

              </div>
              <div class="flex-auto lg:w-1/3 w-full">
                <div class="lg:flex gap-6 ">
                  <div class="font-bold w-32">部署定数</div>
                  <div>{{ numberFormat(stock.value.constant) }} {{ stock.value.quantity.quantityUnit }}</div>
                </div>
                <div class="lg:flex gap-6 ">
                  <div class="font-bold w-32">現在在庫数</div>
                  <div>{{ numberFormat(stock.value.inventory) }} {{ stock.value.quantity.quantityUnit }}</div>
                </div>
                <div class="lg:flex gap-6 ">
                  <div class="font-bold w-32">発注済数</div>
                  <div>{{ numberFormat(stock.value.orderedQuantity) }} {{ stock.value.quantity.quantityUnit }}</div>
                </div>
                <div class="lg:flex gap-6 ">
                  <div class="font-bold w-32">必要数</div>
                  <div>{{ numberFormat( stock.value.numberOfOrdersRequired )  }} {{ stock.value.quantity.quantityUnit }} /
                    {{ numberFormat( stock.value.orderQuantity )  }} {{ stock.value.quantity.itemUnit }}
                  </div>
                </div>
                <v-input-number :rules="{ between: [0 , 99999] }" :name="`stockItems[${idx}].orderUnitQuantity`" label="発注数" :min="0" :unit="stock.value.quantity.itemUnit" :step="1" :title="`発注数`" change-class-name="inputChange"></v-input-number>
              </div>
            </div>
          </div>
        </div>
        <v-pagination :show-pages="showPages" v-model:current-page="values.currentPage" :total-count="totalCount" :per-page="parseInt(values.perPage)"></v-pagination>
      </div>
    </div>
  </div>
  <v-open-modal ref="openModal" headtext="絞り込み" id="openModal">
    <div class="flex flex-col">
      <div class="w-full overflow-y-auto" style="max-height: 85vh;">
        <div class="flex flex-wrap">
          <div class="w-full px-3 my-6 md:mb-0">
            <div class="my-4">
              <v-input name="makerName" type="text" label="メーカー名" title="メーカー名"></v-input>
            </div>
            <div class="my-4">
              <v-input name="itemName" type="text" label="商品名" title="商品名"></v-input>
            </div>
            <div class="my-4">
              <v-input name="itemCode" type="text" label="製品コード" title="製品コード"></v-input>
            </div>
            <div class="my-4">
              <v-input name="itemStandard" type="text" label="規格" title="規格"></v-input>
            </div>
            <div class="my-4">
              <v-input name="itemJANCode" type="text" label="JANコード" title="JANコード"></v-input>
            </div>
            <?php if (!gate('fixed_quantity_order_slips')->isOnlyMyDivision): ?>
              <div class="my-4">
                <v-multiple-select-division name="divisionIds" title="発注書元部署名"></v-multiple-select-division>
              </div>
            <?php endif; ?>
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
      'v-loading': vLoading,
      'v-breadcrumbs': vBreadcrumbs,
      'v-button-default': vButtonDefault,
      'v-button-primary': vButtonPrimary,
      'v-button-danger': vButtonDanger,
      'header-navi': headerNavi,
      'v-open-modal': vOpenModal,
      'v-input': vInput,
      'item-view': itemView,
      'v-pagination': vPagination,
      'v-select': vSelect,
      'v-text': vText,
      'v-input-number': vInputNumber,
      'v-multiple-select-division': vMultipleSelectDivision,
      'blowing': blowing
    },
    setup() {

      const {
        ref,
        toRef,
        toRefs,
        reactive,
        onMounted
      } = Vue;
      const {
        useFieldArray,
        useForm
      } = VeeValidate;

      const loading = ref(false);
      const start = () => {
        loading.value = true;
      }

      const complete = () => {
        loading.value = false;
      }

      const sleepComplate = () => {
        window.setTimeout(function() {
          complete();
        }, 500);
      }
      start();

      onMounted(() => {
        sleepComplate()
      });

      const date = new Date();
      const yyyy = date.getFullYear();
      const mm = ("0" + (date.getMonth() + 1)).slice(-2);
      const dd = ("0" + date.getDate()).slice(-2);

      const getCache = () => {
        let url = window.location.href;
        name = 'isCache';
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
          results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
      }

      const getParam = (name) => {
        let url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");

        if (getCache() === "true") {
          let obj = sessionStorage.getItem(pagetitle);
          if (obj === null) {
            return ""
          }
          return (JSON.parse(obj))[name];
        }

        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
          results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
      }


      const setParam = (values) => {
        sessionStorage.setItem(pagetitle, JSON.stringify(values));
        const url = new URL(window.location);
        url.searchParams.set('itemName', values.itemName);
        url.searchParams.set('makerName', values.makerName);
        url.searchParams.set('itemCode', values.itemCode);
        url.searchParams.set('itemStandard', values.itemStandard);
        url.searchParams.set('itemJANCode', values.itemJANCode);
        url.searchParams.set('registerDate', values.registerDate);
        url.searchParams.set('perPage', values.perPage);
        url.searchParams.set('currentPage', values.currentPage);
        url.searchParams.set('divisionIds', values.divisionIds);
        history.pushState({}, '', url);
      }


      const {
        handleSubmit,
        control,
        meta,
        validate,
        values,
        isSubmitting,
        resetForm
      } = useForm({
        initialValues: {
          itemName: (getParam("itemName")) ? getParam("itemName") : "",
          makerName: (getParam("makerName")) ? getParam("makerName") : "",
          itemCode: (getParam("itemCode")) ? getParam("itemCode") : "",
          itemStandard: (getParam("itemStandard")) ? getParam("itemStandard") : "",
          itemJANCode: (getParam("itemJANCode")) ? getParam("itemJANCode") : "",
          registerDate: (getParam("registerDate")) ? getParam("registerDate") : "",
          perPage: (Number.isInteger(getParam("perPage"))) ? getParam("perPage") : "100",
          currentPage: (Number.isInteger(parseInt(getParam("currentPage")))) ? parseInt(getParam("currentPage")) : 1,
          divisionIds: (getParam("divisionIds")) ? (Array.isArray(getParam("divisionIds")) ? getParam("divisionIds") : (getParam("divisionIds")).split(',')) : [],
          stockItems: [],
        },
        validateOnMount: false
      });
      const {
        remove,
        push,
        fields,
        update,
        replace
      } = useFieldArray('stockItems', control);


      const pagetitle = "stockshow";

      const breadcrumbs = [{
          text: '発注・入荷メニュー',
          disabled: false,
          href: _ROOT + '&path=/order',
        },
        {
          text: '定数発注',
          disabled: true,
        }
      ];

      const openModal = ref();

      const numberFormat = (value) => {
        if (!value) {
          return 0;
        }
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

      const stocks = ref([]);

      const perPageOptions = [{
        label: "10件表示",
        value: "10"
      }, {
        label: "50件表示",
        value: "50"
      }, {
        label: "100件表示",
        value: "100"
      }, {
        label: "1000件表示",
        value: "1000"
      }];


      const searchCount = ref(0);

      const listGet = () => {
        let params = new URLSearchParams();
        params.append("path", "/api/order/fixedQuantityOrder");
        params.append("search", JSON.stringify(encodeURIToObject(values)));
        params.append("_csrf", _CSRF);

        setParam(values);

        start();
        replace([]);

        axios.post(_APIURL, params)
          .then((response) => {

            let data = response.data.data.map((x) => {
              x.orderUnitQuantity = x.orderQuantity;
              return x;
            });


            replace(data);
            totalCount.value = parseInt(response.data.count);
          })
          .catch((error) => {
            complete();
            if (searchCount.value > 0) {
              Toast.fire({
                icon: 'error',
                title: '検索に失敗しました。再度お試しください。'
              })
            }
            searchCount.value++;
          })
          .then(() => {
            complete();
            if (searchCount.value > 0) {
              Toast.fire({
                icon: 'success',
                title: '検索が完了しました'
              })
            }
            searchCount.value++;
          });
      };

      const changeParPage = () => {
        values.currentPage = 1;
        listGet();
      };

      const add = (elem) => {
        context.emit('additem', elem);
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
      const searchClear = () => {
        values.currentPage = 1;
        resetForm({
          itemName: "",
          makerName: "",
          itemCode: "",
          itemStandard: "",
          itemJANCode: "",
          registerDate: "",
          divisionIds: [],
          currentPage: 1,
          perPage: values.perPage,
        });
        listGet();
      };

      const openSlip = (url) => {
        location.href = _ROOT + "&path=/order/unapproved/" + url;
      }
      const openPrint = (url) => {
        location.href = _ROOT + "&path=/order/" + url + "/print";
      }


      const createOrderModel = (values) => {
        let items = values.stockItems;
        let stockItems = [];
        items.forEach(function(item, idx) {
          if (item.orderUnitQuantity != 0) {
            stockItems.push({
              'inHospitalItemId': item.inHospitalItemId,
              'orderUnitQuantity': item.orderUnitQuantity,
              'divisionId': item.division.divisionId,
            })
          }
        });
        return stockItems;
      };

      const onSubmit = async () => {
        const {
          valid,
          errors
        } = await validate();

        if (!valid) {
          Swal.fire({
            icon: 'error',
            title: '入力エラー',
            text: '入力エラーがございます。ご確認ください',
          })
        } else {
          Swal.fire({
            title: '発注登録を行います。',
            text: "よろしいですか？",
            icon: 'info',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then((result) => {
            if (result.isConfirmed) {
              orderRegister();
            }
          })
        }
      };

      const orderRegister = handleSubmit(async (values) => {
        try {
          const orderModels = createOrderModel(values);
          if (orderModels.length === 0) {
            Swal.fire({
              icon: 'error',
              title: '登録する商品がありませんでした。',
              text: '内容を確認の上、再送信をしてください。',
            })
            return false;
          }
          start();

          let params = new URLSearchParams();
          params.append("path", "/api/fixedQuantityOrder/register");
          params.append("_method", 'post');
          params.append("_csrf", _CSRF);
          params.append("orderItems", JSON.stringify(encodeURIToObject(orderModels)));

          const res = await axios.post(_APIURL, params);
          complete();
          if (res.data.code != 200) {
            throw new Error(res.data.message)
          }
          Swal.fire({
            icon: 'success',
            title: '登録が完了しました。',
            text: 'メールに登録した発注番号を記載しています',
          }).then((result) => {
            location.href = _ROOT + "&path=/order/unapproved/show";
          });
          return true;
        } catch (error) {
          Swal.fire({
            icon: 'error',
            title: 'システムエラー',
            text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
          });
        }
      });

      const orderPrice = (idx) => {
        return values.stockItems[idx].price * values.stockItems[idx].orderUnitQuantity;
      };

      const totalAmount = () => {
        let num = 0;
        values.stockItems.forEach((v, idx) => {
          num += orderPrice(idx);
        });
        return num;
      };

      const itemCount = () => {
        let num = 0;
        values.stockItems.forEach((v, idx) => {
          num += (v.orderUnitQuantity > 0) ? 1 : 0;
        });
        return num;
      };

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
        stocks,
        onOpenModal,
        openModal,
        breadcrumbs,
        numberFormat,
        fields,
        onSubmit,
        totalAmount,
        itemCount
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