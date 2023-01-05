<div id="top" v-cloak>
  <header-navi></header-navi>
  <v-loading :show="loading"></v-loading>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <h1 class="text-2xl mb-2">個別発注</h1>
        <hr>
        <div>
          <div class="mb-2 lg:w-1/3">
            <v-select-division name="divisionId" label="発注部署" :rules="{ required : true }" title="発注部署指定" :disabled="values.divisionId != '' && fields.length > 0" :is-only-my-division="<?php var_export(gate('register_of_unordered_slips')->isOnlyMyDivision()); ?>" />
          </div>
          <div class="lg:flex lg:flex-row gap-4">
            <div class="my-4 w-1/3 lg:w-1/6">
              <v-button-default class="w-full" type="button" data-micromodal-trigger="inHospitalItemModal">商品検索</v-button-default>
              <v-in-hospital-item-modal v-on:additem="additem">
              </v-in-hospital-item-modal>
            </div>
            <div class="my-4 w-1/3 lg:w-1/6">
              <v-button-default class="w-full" type="button" :disabled="values.divisionId == ''" data-micromodal-trigger="consumptionHistoryModalForOrder">伝票検索</v-button-default>
              <v-consumption-history-modal-for-order v-on:addconsumptions="addconsumptions" :division-id="values.divisionId">
              </v-consumption-history-modal-for-order>
            </div>
          </div>
          <div class="my-4 lg:w-1/3 items-center">
            <v-switch id="integrate" v-model="integrate" :message="(integrate)? '既存の未発注伝票に追加します' : '新規発行します'"></v-switch>
          </div>
          <div class="p-2 bg-gray-300">
            <v-barcode-search @additem="addItemByBarcode"></v-barcode-search>
          </div>
          <div class="my-2" v-if="fields.length == 0">
            <div class="max-h-full h-full grid place-content-center w-full lg:flex border border-sushi-600 bg-white mt-3">
              <div class="flex-1 p-4 relative text-center">商品を選択または、バーコードを読み取ってください</div>
            </div>
          </div>
          <div class="mt-4 p-4 shadow-md drop-shadow-md" v-if="fields.length > 0">
            <p class=" text-xl">登録アイテム数: {{ numberFormat(itemCount()) }} アイテム</p>
            <p class=" text-xl">合計金額: &yen; {{ numberFormat(totalAmount()) }} </p>
            <v-button-primary type="button" class="w-full" @click.native="onSubmit">発注登録</v-button-primary>
          </div>
          <transition-group tag="div" name="list" appear>
            <div class="my-2" v-for="(item, idx) in fields" :key="item.key">
              <div class="w-full lg:flex mt-3">
                <div class="flex-auto lg:flex-1 flex lg:w-3/4">
                  <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="item.value.inItemImage"></item-view>
                  <div class="flex-1 px-4 relative">
                    <div class="flex-auto lg:flex justify-between leading-normal lg:space-y-0 space-y-4 gap-6">
                      <div class="break-all">
                        <div class="w-full">
                          <h3 class="text-xl font-bold font-heading" v-if="item.value.makerName">{{ item.value.makerName }}</h3>
                          <p class="text-md font-bold font-heading" v-if="item.value.itemName">{{ item.value.itemName }}</p>
                          <p class="text-gray-500" v-if="item.value.itemCode">{{ item.value.itemCode }}</p>
                          <p class="text-gray-500" v-if="item.value.itemStandard">{{ item.value.itemStandard }}</p>
                          <p class="text-gray-500" v-if="item.value.distributorName">{{ item.value.distributorName }}</p>
                        </div>
                        <div class="w-full text-lg font-bold font-heading flex gap-6">
                          <span class="text-xl text-orange-600 font-bold font-heading">&yen; {{ numberFormat(item.value.price) }}/{{ item.value.itemUnit }}</span>
                          <blowing :message="item.value.priceNotice" title="金額管理備考" v-if="item.value.priceNotice != ''"></blowing>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex-initial lg:flex gap-6 lg:w-1/4 items-end flex-row-reverse">
                  <v-input-number :rules="{ between: [-99999 , 99999] }" :name="`orderItems[${idx}].orderUnitQuantity`" label="発注数（個数）" :unit="item.value.itemUnit" :step="1" :title="`発注数（個数）/${item.value.quantity}${ item.value.quantityUnit }入り`"></v-input-number>
                </div>
              </div>
              <div class="mt-4 flex">
                <v-button-danger type="button" @click.native="remove(idx)">削除</v-button-danger>
                <div class="flex-1 items-center ">
                  <p class="text-xl text-gray-800 font-bold font-heading text-right">&yen; {{ numberFormat(orderPrice(idx) ) }} ( {{ item.value.orderUnitQuantity }}{{ item.value.itemUnit }} )</p>
                </div>
              </div>
              <div class="pt-4 pb-2 w-full">
                <div class="border-t border-gray-200"></div>
              </div>
            </div>
          </transition-group>
        </div>
      </div>
    </div>
  </div>
  <v-open-modal ref="openModal" headtext="商品選択" id="openModal">
    <div class="flex flex-col" style="max-height: 68vh;">
      <div class="overflow-y-scroll my-6">
        <div class="w-full mb-8 xl:mb-0">
          <div class="hidden lg:flex w-full sticky top-0 bg-white py-4 flex-wrap">
            <div class="w-full lg:w-5/6">
              <h4 class="font-bold font-heading text-gray-500 text-center">商品情報</h4>
            </div>
            <div class="w-full lg:w-1/6">
              <h4 class="font-bold font-heading text-gray-500 text-center">反映</h4>
            </div>
          </div>
          <div class="lg:pt-0 pt-4">
            <div class="flex flex-wrap items-center mb-3" v-for="(elem, index) in selectInHospitalItems">
              <div class="w-full lg:w-5/6 lg:px-4 px-0 mb-6 lg:mb-0">
                <div class="flex flex-wrap items-center gap-4">
                  <div class="flex-none">
                    <item-view class="md:h-44 md:w-44 h-32 w-32" :base64=""></item-view>
                  </div>
                  <div class="break-words flex-1 box-border w-44">
                    <h3 class="text-xl font-bold font-heading">{{ elem.makerName }}</h3>
                    <p class="text-md font-bold font-heading">{{ elem.itemName }}</p>
                    <p class="text-gray-500">{{ elem.itemCode }}<br>{{ elem.itemStandard }}</p>
                    <p class="text-gray-500">{{ elem.quantity }}{{ elem.quantityUnit }}
                      入り</p>
                    <p>
                      <span class="text-xl text-orange-600 font-bold font-heading">&yen;
                        {{ numberFormat(elem.price) }}</span>
                      <span class="text-gray-400">
                        ( &yen;
                        {{ numberFormat(elem.unitPrice) }}/{{ elem.quantityUnit }}
                        )</span>
                    </p>
                    <p class="text-gray-800">ロット番号：{{ elem.lotNumber }}</p>
                    <p class="text-gray-800">使用期限：{{ elem.lotDate }}</p>
                    <p class="text-gray-800">{{ elem.distributorName }}</p>
                  </div>
                </div>
              </div>
              <div class="w-full lg:block lg:w-1/6 px-4 py-4">
                <v-button-default type="button" class="w-full" v-on:click.native="additem(elem)">反映</v-button-default>
              </div>
              <div class="py-2 px-4 w-full">
                <div class="border-t border-gray-200"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </v-open-modal>
</div>

<script>
  var JoyPlaApp = Vue.createApp({
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

      const {
        handleSubmit,
        control,
        meta,
        validate,
        values,
        isSubmitting
      } = useForm({
        initialValues: {
          orderItems: [],
          divisionId: "",
          barcode: "",
          orderDate: yyyy + '-' + mm + '-' + dd,
        },
        validateOnMount: false
      });
      const {
        remove,
        insert,
        fields,
        update,
        replace
      } = useFieldArray('orderItems', control);

      const integrate = ref(localStorage.joypla_unorder_slip_integrate === 'true');

      const alertModel = reactive({
        message: "",
        headtext: "",
        okMethod: function() {
          console.log('')
        },
      });

      const confirmModel = reactive({
        message: "",
        headtext: "",
        okMethod: function() {
          console.log('')
        },
        cancelMethod: function() {
          console.log('')
        },
      });

      const breadcrumbs = [{
          text: '発注メニュー',
          disabled: false,
          href: _ROOT + '&path=/order',
        },
        {
          text: '個別発注',
          disabled: true,
        }
      ];

      const createOrderModel = (values) => {
        let items = values.orderItems;
        let orderItems = [];
        items.forEach(function(item, idx) {
          if (item.orderUnitQuantity != 0) {
            orderItems.push({
              'inHospitalItemId': item.inHospitalItemId,
              'orderUnitQuantity': parseInt(item.orderUnitQuantity),
              'divisionId': values.divisionId,
            })
          }
        });
        return orderItems;
      };

      const orderPrice = (idx) => {
        return values.orderItems[idx].price * parseInt(values.orderItems[idx].orderUnitQuantity);
      };

      const totalAmount = () => {
        let num = 0;
        values.orderItems.forEach((v, idx) => {
          num += orderPrice(idx);
        });
        return num;
      };

      const itemCount = () => {
        let num = 0;
        values.orderItems.forEach((v, idx) => {
          num += (v.orderUnitQuantity > 0) ? 1 : 0;
        });
        return num;
      };

      const numberFormat = (value) => {
        if (!value) {
          return 0;
        }
        return new Intl.NumberFormat('ja-JP').format(value);
      }

      const alertSetting = toRefs(alertModel);
      const confirmSetting = toRefs(confirmModel);

      const alert = ref();
      const confirm = ref();

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

          let params = new URLSearchParams();
          params.append("path", "/api/order/register");
          params.append("_method", 'post');
          params.append("_csrf", _CSRF);
          params.append("orderDate", values.orderDate);
          params.append("integrate", integrate.value);
          params.append("orderItems", JSON.stringify(encodeURIToObject(orderModels)));

          const res = await axios.post(_APIURL, params);

          if (res.data.code != 200) {
            throw new Error(res.data.message)
          }

          Swal.fire({
            icon: 'success',
            title: '登録が完了しました。',
            text: 'メールに登録・追加した発注番号を記載しています',
          }).then((result) => {
            let tmp = [];
            replace(tmp);
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

      const updateItem = (idx, key, value) => {
        let object = JSON.parse(JSON.stringify(fields[idx].value));
        object[key] = value;
        update(idx, object);
      };

      const additem = (item) => {
        item = JSON.parse(JSON.stringify(item));
        item.orderUnitQuantity = (item.orderUnitQuantity) ? item.orderUnitQuantity : 1;
        let checked = false;
        if (Array.isArray(values.orderItems)) {
          values.orderItems.forEach((v, idx) => {
            if (v.inHospitalItemId === item.inHospitalItemId) {
              checked = true;
              v.orderUnitQuantity++;
            }
          });
        }
        if (!values.orderItems) {
          values.orderItems = [];
        }
        if (!checked) {
          insert(0, item);
        }
      };

      const addconsumptions = (consumptions) => {
        consumptions = JSON.parse(JSON.stringify(consumptions));
        consumptions.forEach((elm, index) => {
          if ((typeof elm.orderableQuantity !== "undefined") && (parseInt(elm.orderableQuantity) < 1)) {
            return;
          }

          let exist = false;
          if (Array.isArray(values.orderItems)) {
            values.orderItems.forEach((v, idx) => {
              if (v.inHospitalItemId === elm.inHospitalItemId) {
                exist = true;
                let quantity = (elm.orderableQuantity > 0) ? parseInt(elm.orderableQuantity) : 0;
                v.orderUnitQuantity += quantity;
              }
            });
          }

          if (!values.orderItems) {
            values.orderItems = [];
          }

          if (!exist) {
            let orderItem = new Object();
            orderItem.orderUnitQuantity = (elm.orderableQuantity && elm.orderableQuantity > 0) ? parseInt(elm.orderableQuantity) : 0;
            orderItem.makerName = (elm.item.makerName) ? elm.item.makerName : "";
            orderItem.itemName = (elm.item.itemName) ? elm.item.itemName : "";
            orderItem.itemCode = (elm.item.itemCode) ? elm.item.itemCode : "";
            orderItem.itemStandard = (elm.item.itemStandard) ? elm.item.itemStandard : "";
            orderItem.itemJANCode = (elm.itemJANCode) ? elm.itemJANCode : "";
            orderItem.quantity = (elm.quantity.quantityNum) ? parseInt(elm.quantity.quantityNum) : 0;
            orderItem.price = (elm.price) ? parseInt(elm.price) : 0;
            orderItem.quantityUnit = (elm.quantity.quantityUnit) ? elm.quantity.quantityUnit : "";
            orderItem.itemUnit = (elm.quantity.itemUnit) ? elm.quantity.itemUnit : "";
            orderItem.cardId = (elm.cardId) ? elm.cardId : "";
            orderItem.itemId = (elm.item.itemId) ? elm.item.itemId : "";
            orderItem.inHospitalItemId = (elm.inHospitalItemId) ? elm.inHospitalItemId : "";
            orderItem.serialNo = (elm.item.serialNo) ? elm.item.serialNo : "";
            orderItem.catalogNo = (elm.item.catalogNo) ? elm.item.catalogNo : "";
            orderItem.inItemImage = (elm.itemImage) ? elm.itemImage : "";
            orderItem.priceNotice = (elm.priceNotice) ? elm.priceNotice : "";
            insert(0, orderItem);
          }
        });
      };

      const openModal = ref();
      const selectInHospitalItems = ref([]);
      const addItemByBarcode = (items) => {
        selectInHospitalItems.value = [];
        if (items.item.length === 0) {
          Swal.fire({
            icon: 'info',
            title: '商品が見つかりませんでした',
          });
          return false;
        }

        if (items.type == "received") {
          items.item.forEach((x, id) => {
            items.item[id].orderUnitQuantity = 1;
          });
        }

        if (items.type == "payout") {
          items.item.forEach((x, id) => {
            items.item[id].orderUnitQuantity = Math.ceil(parseInt(items.item[id].payoutQuantity) / parseInt(items.item[id].quantity));
          });
        }
        if (items.type == "card") {
          items.item.forEach((x, id) => {
            items.item[id].orderUnitQuantity = Math.ceil(parseInt(items.item[id].cardQuantity) / parseInt(items.item[id].quantity));
          });
        }
        if (items.type == "customlabel") {
          items.item.forEach((x, id) => {
            items.item[id].orderUnitQuantity = Math.ceil(parseInt(items.item[id].customQuantity) / parseInt(items.item[id].quantity));
          });
        }

        if (items.item.length === 1) {
          if (items.item[0].divisionId) {
            if (values.divisionId !== items.item[0].divisionId) {
              Swal.fire({
                icon: 'error',
                title: 'エラー',
                text: '読み込んだ値と選択している部署が一致しませんでした',
              });
              return false;
            }
          }
          additem(items.item[0]);
        } else {
          selectInHospitalItems.value = items.item;
          openModal
            .value
            .open();
        }
      }

      return {
        values,
        openModal,
        selectInHospitalItems,
        addItemByBarcode,
        integrate,
        loading,
        start,
        complete,
        itemCount,
        isSubmitting,
        alert,
        confirm,
        orderPrice,
        totalAmount,
        additem,
        onSubmit,
        breadcrumbs,
        alertSetting,
        confirmSetting,
        numberFormat,
        meta,
        fields,
        remove,
        validate,
        addconsumptions
      };
    },
    watch: {
      isSubmitting() {
        this.loading = this.isSubmitting;
      },
      integrate(bool) {
        console.log(this.integrate);
        localStorage.joypla_unorder_slip_integrate = bool;
      },
      fields: {
        async handler(val, oldVal) {
          await this.validate();
        },
        deep: true
      },
    },
    components: {
      'v-open-modal': vOpenModal,
      'v-barcode-search': vBarcodeSearch,
      'v-switch': vSwitch,
      'v-loading': vLoading,
      'item-view': itemView,
      VForm: VeeValidate.Form,
      'v-field': VeeValidate.Field,
      ErrorMessage: VeeValidate.ErrorMessage,
      'v-alert': vAlert,
      'v-confirm': vConfirm,
      'v-breadcrumbs': vBreadcrumbs,
      'v-input': vInput,
      'v-select': vSelect,
      'v-select-division': vSelectDivision,
      'v-button-default': vButtonDefault,
      'v-button-primary': vButtonPrimary,
      'v-button-danger': vButtonDanger,
      'v-input-number': vInputNumber,
      'v-in-hospital-item-modal': vInHospitalItemModal,
      'header-navi': headerNavi,
      'blowing': blowing,
      'v-consumption-history-modal-for-order': vConsumptionHistoryModalForOrder
    },
  }).mount('#top');
</script>