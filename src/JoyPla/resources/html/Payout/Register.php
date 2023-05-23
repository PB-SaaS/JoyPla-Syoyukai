<div id="top" v-cloak>
  <header-navi></header-navi>
  <v-loading :show="loading"></v-loading>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <h1 class="text-2xl mb-2">出庫・払出登録</h1>
        <hr>
        <div>
          <div class="lg:flex lg:flex-row gap-4">
            <div class="mb-2 lg:w-1/3">
              <v-input type="date" name="receivedDate" :rules="{}" title="払出日指定" label="払出日指定"></v-input>
            </div>
            <div class="mb-2 lg:w-1/3">
              <v-select-division :is-only-use-data="true" name="sourceDivisionId" label="払出元部署" :rules="{ required : true ,  notTwoFieldSameAs : [ '払出先部署', `@targetDivisionId`] }" title="払出元部署指定" :disabled="fields.length > 0" :is-only-my-division="<?php var_export(
                  gate('register_of_unordered_slips')->isOnlyMyDivision()
              ); ?>" />
            </div>
            <div class="mb-2 lg:w-1/3">
              <v-select-division :is-only-use-data="true" name="targetDivisionId" label="払出先部署" :rules="{ required : true ,  notTwoFieldSameAs : [ '払出元部署', `@sourceDivisionId`] }" title="払出先部署指定" :disabled="fields.length > 0" />
            </div>
          </div>
          <div class="lg:flex lg:flex-row gap-4">
            <div class="my-4 w-1/3 lg:w-1/6">
              <v-button-default class="w-full" type="button" data-micromodal-trigger="inHospitalItemModal" :disabled="values.targetDivisionId == values.sourceDivisionId || (values.targetDivisionId == '' || values.sourceDivisionId == '')">商品検索</v-button-default>
              <v-in-hospital-item-modal v-on:additem="additem" :division-id="values.sourceDivisionId" :unit-price-use="payoutUnitPriceUseFlag">
              </v-in-hospital-item-modal>
            </div>
            <div class="my-4 w-1/3 lg:w-1/6">
              <v-button-default class="w-full" type="button" :disabled="values.targetDivisionId == values.sourceDivisionId || (values.targetDivisionId == '' || values.sourceDivisionId == '')" data-micromodal-trigger="consumptionHistoryModalForOrder">伝票検索</v-button-default>
              <v-consumption-history-modal-for-order v-on:addconsumptions="addconsumptions" :division-id="values.targetDivisionId">
              </v-consumption-history-modal-for-order>
            </div>
          </div>
          <div class="my-4 lg:w-1/3 items-center">
            <v-switch id="labelCreate" v-model="labelCreate" :message="(labelCreate)? 'ラベル発行をする' : 'ラベル発行をしない'"></v-switch>
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
            <div class="lg:flex lg:flex-row gap-4">
              <v-button-primary type="button" class="w-full lg:w-1/2" @click.native="onSubmit">払出登録</v-button-primary>
              <v-button-primary type="button" class="w-full lg:w-1/2" @click.native="onSubmit">出庫登録</v-button-primary>
            </div>
          </div>
          <table class="w-full">
            <thead class="bg-gray-50 whitespace-nowrap text-sm font-medium text-gray-700 text-center border">
              <tr>
                <th class="px-2 py-4 border w-10">No</th>
                <th class="min-w-[225px] px-6 py-4 border">商品情報</th>
                <th class="px-2 py-4 border">払出元在庫数</th>
                <th class="px-2 py-4 border">ロット番号</th>
                <th class="px-2 py-4 border">使用期限</th>
                <th class="px-2 py-4 border w-[120px]">カード番号</th>
                <th class="px-2 py-4 border">払出数</th>
                <th class="tpx-2 py-4 border">合計払出数</th>
                <th class="px-2 py-4 border">合計金額</th>
                <th class="px-2 py-4 border"></th>
                <th class="px-2 py-4 border"></th>
              </tr>
            </thead>
            <tbody class="text-sm font-medium text-gray-700 border">
            <template v-for="(item, idx) in values.payoutItems">
              <template v-for="(_payout, pid) in item._payout">
                <tr class="my-2" :key="item.key + '/' + pid">
                  <template v-if="pid == 0">
                    <td class="text-center px-3 py-4 border" :rowspan="item._payout.length">{{ idx + 1 }}</td>
                    <td class="text-left px-3 py-4 border" :rowspan="item._payout.length">
                      <p class="text-gray-500" v-if="item.makerName">{{ item.makerName }}</h3>
                      <p class="text-gray-500" v-if="item.itemName">{{ item.itemName }}</p>
                      <p class="text-gray-500" v-if="item.itemCode">{{ item.itemCode }}</p>
                      <p class="text-gray-500" v-if="item.itemStandard">{{ item.itemStandard }}</p>
                      <p class="text-gray-500" v-if="item.itemJANCode">{{ item.itemJANCode }}</p>
                      <p class="text-gray-500">&yen;{{ numberFormat(item.unitPrice) }} / {{ numberFormat(item.quantity) }}{{ item.quantityUnit }}</p>
                    </td>
                    <td class="text-center" :rowspan="item._payout.length">{{ numberFormat(item._stock.stockQuantity) }}{{ item.quantityUnit }}</td>
                  </template>
                  <td class="px-3 py-4 border">
                    <v-input 
                        v-model="_payout.lotNumber"
                          :name="`payoutItems[${idx}]._payout[${pid}].lotNumber`" label="ロット番号" :rules="{ required : isRequired(idx) ,lotnumber: true , twoFieldRequired : [ '使用期限', `@payoutItems[${idx}]._payout[${pid}].lotDate`]  }" type="text" change-class-name="inputChange" title="ロット番号"></v-input>
                  </td>
                  <td class="px-3 py-4 border">
                    <v-input 
                        v-model="_payout.lotDate"
                        :name="`payoutItems[${idx}]._payout[${pid}].lotDate`" label="使用期限" :rules="{ required : isRequired(idx) ,lotdate: true , twoFieldRequired : [ 'ロット番号', `@payoutItems[${idx}]._payout[${pid}].lotNumber`]  }" type="date" change-class-name="inputChange" title="使用期限"></v-input>
                  </td>
                  <td class="text-center px-3 py-4 border">{{ _payout.card }}</td>
                  <td class="text-center px-3 py-4 border w-1/6">
                    <v-input-number :rules="{ between: [0 , 99999] }" :name="`payoutItems[${idx}]._payout[${pid}].count`" label="払出数" :min="0" :unit="item.quantityUnit" :step="1" title="払出数"></v-input-number>
                  </td>
                  <template v-if="pid == 0">
                    <td class="text-center px-3 py-4 border" :rowspan="item._payout.length">{{ numberFormat(totalCount(item)) }}{{item.quantityUnit}}</td>
                    <td class="text-center px-3 py-4 border" :rowspan="item._payout.length">&yen;{{ numberFormat(totalPrice(item)) }}</td>
                  </template>
                  <td class="px-3 py-4 border"><v-button-primary type="button" @click.native="onSubmit">複製</v-button-primary></td>
                  <td class="px-3 py-4 border"><v-button-danger type="button" @click.native="onSubmit">削除</v-button-danger></td>
                </tr> 
              </template>
            </template>
            </tbody>
          </table>
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
      
      const payoutUnitPriceUseFlag = "<?php echo $payoutUnitPriceUseFlag; ?>";

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
          sourceDivisionId: "",
          targetDivisionId: "",
          payoutItems: [],
          barcode: "",
          payoutDate: yyyy + '-' + mm + '-' + dd,
        },
        validateOnMount: false
      });
      const {
        remove,
        insert,
        fields,
        update,
        replace
      } = useFieldArray('payoutItems', control);
    
      const labelCreate = ref(localStorage.joypla_payoutLabelCreate === 'true');

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
          text: '払出メニュー',
          disabled: false,
          href: _ROOT + '&path=/payout',
        },
        {
          text: '出庫・払出登録',
          disabled: true,
        }
      ];

      const createOrderModel = (values) => {
        let items = values.payoutItems;
        let payoutItems = [];
        items.forEach(function(item, idx) {
          if (item.orderUnitQuantity != 0) {
            payoutItems.push({
              'inHospitalItemId': item.inHospitalItemId,
              'orderUnitQuantity': parseInt(item.orderUnitQuantity),
            })
          }
        });
        return payoutItems;
      };

      const orderPrice = (idx) => {
        return values.payoutItems[idx].price * parseInt(values.payoutItems[idx].orderUnitQuantity);
      };

      const totalAmount = () => {
        let num = 0;
        values.payoutItems.forEach((v, idx) => {
          num += orderPrice(idx);
        });
        return num;
      };

      const itemCount = () => {
        let num = 0;
        values.payoutItems.forEach((v, idx) => {
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
          params.append("payoutDate", values.payoutDate);
          params.append("payoutItems", JSON.stringify(encodeURIToObject(orderModels)));

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
        
        item._payout = [];
        item._payout.push({
          count : 0,
          lotNumber : item.lotNumber ?? '',
          lotDate : item.lotDate ?? '',
          cardId : '',
        })

        item.orderUnitQuantity = (item.orderUnitQuantity) ? item.orderUnitQuantity : 1;
        
        let checked = false;
        if (Array.isArray(values.payoutItems)) {
          values.payoutItems.forEach((v, idx) => {
            if (
              v.inHospitalItemId === item.inHospitalItemId
            ) {

              let lotCheck = false;
              v._payout.forEach((payout) => {
                if (
                  payout.lotNumber === item._payout[0].lotNumber &&
                  payout.lotDate === item._payout[0].lotDate
                ) {
                  lotCheck = true;
                }
              });
              if (!lotCheck) {
                v._payout.push(item._payout[0]);
              }
              
              checked = true;
            }
          });
        }
        if (!values.payoutItems) {
          values.payoutItems = [];
        }
        if (!checked) {
          item.priceNotice = (item.priceNotice) ? item.priceNotice : "";
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
          if (Array.isArray(values.payoutItems)) {
            values.payoutItems.forEach((v, idx) => {
              if (v.inHospitalItemId === elm.inHospitalItemId) {
                exist = true;
                let quantity = (elm.orderableQuantity > 0) ? parseInt(elm.orderableQuantity) : 0;
                v.orderUnitQuantity += quantity;
              }
            });
          }

          if (!values.payoutItems) {
            values.payoutItems = [];
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
        if (!items.item || items.item.length === 0) {
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

      const isRequired = (idx) => {
        if (fields.value[idx].value.lotManagement == "1") {
          return true;
        }
        return false;
      };

      const totalCount = (item) => {
        let count = 0;

        if(item._payout){
          item._payout.forEach((x, id) => {
            count += x.count;
          }) 
        }

        return count;
      }
      
      const totalPrice = (item) => {
        let count = totalCount(item);

        return item.unitPrice * count;
      }

      return {
        isRequired,
        values,
        openModal,
        selectInHospitalItems,
        addItemByBarcode,
        labelCreate,
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
        addconsumptions,
        totalPrice,
        totalCount,
        payoutUnitPriceUseFlag
      };
    },
    watch: {
      isSubmitting() {
        this.loading = this.isSubmitting;
      },
      labelCreate(bool) {
        console.log(this.labelCreate);
        localStorage.joypla_payoutLabelCreate = bool;
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