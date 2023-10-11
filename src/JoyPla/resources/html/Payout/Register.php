<div id="top" v-cloak>
  <header-navi></header-navi>
  <v-loading :show="loading" :text="loadingText"></v-loading>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <h1 class="text-2xl mb-2">出庫・払出登録</h1>
        <hr>
        <div>
          <div class="lg:flex lg:flex-row gap-4">
            <div class="mb-2 lg:w-1/3">
              <v-input type="date" name="payoutDate" :rules="{}" title="払出日指定" label="払出日指定"></v-input>
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
          <div class="sticky top-0 bg-white" style="z-index: 2;">
            <div class="lg:flex lg:flex-row gap-4">
              <div class="my-4 w-1/3 lg:w-1/6">
                <v-button-default class="w-full" type="button" data-micromodal-trigger="inHospitalItemModal" :disabled="values.targetDivisionId == values.sourceDivisionId || (values.targetDivisionId == '' || values.sourceDivisionId == '')">商品検索</v-button-default>
                <v-in-hospital-item-modal v-on:additem="additem" :division-id="values.sourceDivisionId" :unit-price-use="payoutUnitPriceUseFlag">
                </v-in-hospital-item-modal>
              </div>
              <div class="my-4 w-1/3 lg:w-1/6">
                <v-button-default class="w-full" type="button" :disabled="values.targetDivisionId == values.sourceDivisionId || (values.targetDivisionId == '' || values.sourceDivisionId == '')" data-micromodal-trigger="consumptionHistoryModalForItemRequest">伝票検索</v-button-default>
                <v-consumption-history-modal-for-item-request v-on:addconsumptions="additemsForSlip" :source-division-id="values.targetDivisionId">
                </v-consumption-history-modal-for-item-request>
              </div>
              <div class="my-4 w-full items-center flex">
                <div class="mx-2">
                  <v-switch id="labelCreate" v-model="labelCreate" :message="(labelCreate)? 'ラベル発行をする' : 'ラベル発行をしない'"></v-switch>
                </div>
                <?php 
                if(gate('is_admin')): 
                ?>
                <div class="mx-2">
                  <v-switch id="isConsumption" v-model="isConsumption" :message="(isConsumption)? '払出先の消費登録を行う' : '払出先の消費登録を行わない'"></v-switch>
                </div>
                <?php 
                endif;
                ?>
              </div>
            </div>
            <div class="p-2 bg-gray-300">
              <v-barcode-search @additem="addItemByBarcode" :disabled="values.targetDivisionId == values.sourceDivisionId || (values.targetDivisionId == '' || values.sourceDivisionId == '')"></v-barcode-search>
            </div>
            <div class="my-2" v-if="fields.length == 0">
              <div class="max-h-full h-full grid place-content-center w-full lg:flex border border-sushi-600 bg-white mt-3">
                <div class="flex-1 p-4 relative text-center">商品を選択または、バーコードを読み取ってください</div>
              </div>
            </div>
            <div class="mt-4 p-4 shadow-md drop-shadow-md" v-if="fields.length > 0">
              <p class=" text-xl">登録アイテム数: {{ numberFormat(totalItemCount()) }} アイテム</p>
              <p class=" text-xl">合計金額: &yen; {{ numberFormat(totalAmount()) }} </p>
              <div class="lg:flex lg:flex-row gap-4">
                <v-button-primary type="button" class="w-full lg:w-1/2" @click.native="onSubmit">払出登録</v-button-primary>
                <v-button-primary type="button" class="w-full lg:w-1/2" @click.native="onAcceptanceRegisterSubmit">出庫登録</v-button-primary>
              </div>
            </div>
          </div>
          <div style="z-index: 1;" class="height-[80vh]">
            <table class="w-full" id="payoutTable">
              <thead class="bg-gray-50 whitespace-nowrap text-sm font-medium text-gray-700 text-center border">
                <tr>
                  <th class="px-2 py-4 border w-10">No</th>
                  <th class="min-w-[225px] px-6 py-4 border">商品情報</th>
                  <th class="px-2 py-4 border">払出元在庫数</th>
                  <th class="px-2 py-4 border">ロット番号</th>
                  <th class="px-2 py-4 border">使用期限</th>
                  <th class="px-2 py-4 border w-[120px]">カード番号</th>
                  <th class="px-2 py-4 border">払出数<br><v-button-default type="button" @click.native="toQuantity">入数を反映</v-button-default></th>
                  <th class="tpx-2 py-4 border">合計払出数</th>
                  <th class="px-2 py-4 border">合計金額</th>
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
                        <td class="text-center px-3 py-4 border" :rowspan="item._payout.length">{{ numberFormat(item?._stock?.stockQuantity) }}{{ item.quantityUnit }}</td>
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
                      <td class="text-center px-3 py-4 border">
                        <v-input-number 
                          :rules="{ between: [0 , 99999] }" 
                          :name="`payoutItems[${idx}]._payout[${pid}].count`" 
                          label="払出数" 
                          :min="0" 
                          :unit="item.quantityUnit" 
                          :step="1" 
                          title="払出数" 
                          class=" w-[240px]" 
                          :disabled="_payout.card != ''"
                          change-class-name="inputChange">
                        </v-input-number>
                      </td>
                      <template v-if="pid == 0">
                        <td class="text-center px-3 py-4 border" :rowspan="item._payout.length">{{ numberFormat(totalCount(item)) }}{{item.quantityUnit}}</td>
                        <td class="text-center px-3 py-4 border" :rowspan="item._payout.length">&yen;{{ numberFormat(totalPrice(item)) }}</td>
                      </template>
                      <td class="px-3 py-4 border">
                        <div class="flex gap-4">
                        <v-button-primary type="button" @click.native="clonePayout(idx,pid)">複製</v-button-primary>
                        <v-button-danger type="button" @click.native="deletePayout(idx,pid)">削除</v-button-danger>
                        </div>
                      </td>
                    </tr> 
                  </template>
                </template>
              </tbody>
            </table>
          </div>
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

      const loadingText = ref('');
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
      const isConsumption = ref(localStorage.joypla_is_consumption === 'true');
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

      const createAcceptanceModel = (values) => {
        let items = values.payoutItems;
        let payoutItems = [];
        items.forEach(function(item, idx) {
          item._payout.forEach(function(payout, pidx) {
            if (payout.count != 0) {
              payoutItems.push({
                'inHospitalItemId': item.inHospitalItemId,
                'acceptanceQuantity': parseInt(payout.count),
                'targetDivisionId' : values.targetDivisionId,
                'sourceDivisionId' : values.sourceDivisionId,
                'lotNumber' : payout.lotNumber,
                'lotDate' : payout.lotDate,
                'card' : payout.card,
              })
            }
          })
        });
        return payoutItems;
      };

      const totalAmount = () => {
        let num = 0;
        values.payoutItems.forEach((v, idx) => {
          num += totalPrice(v);
        });
        return num;
      };

      const totalItemCount = () => {
        let num = 0;
        values.payoutItems.forEach((v, idx) => {
          num += ( totalCount(v) > 0) ? 1 : 0;
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
            title: '払出登録を行います。',
            text: "よろしいですか？",
            icon: 'info',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then((result) => {
            if (result.isConfirmed) {
              payoutRegister();
            }
          })
        }
      };

      
      const onAcceptanceRegisterSubmit = async () => {
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
            title: '出庫登録を行います。',
            text: "よろしいですか？",
            icon: 'info',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then((result) => {
            if (result.isConfirmed) {
              acceptanceRegister();
            }
          })
        }
      };
      
      const acceptanceRegister = handleSubmit(async (values) => {
        try {
          const acceptanceModels = createAcceptanceModel(values);
          if (acceptanceModels.length === 0) {
            Swal.fire({
              icon: 'error',
              title: '登録する商品がありませんでした。',
              text: '内容を確認の上、再送信をしてください。',
            })
            return false;
          }
          
          <?php 
          if(gate('is_admin')): 
          ?>
          if(isConsumption.value){
            consumptionModels = createConsumptionModel(values);
            if (consumptionModels.length === 0) {
              Swal.fire({
                icon: 'error',
                title: '登録する商品がありませんでした。',
                text: '内容を確認の上、再送信をしてください。',
              })
              return false;
            }
          }
          
          if(isConsumption.value){
            loadingText.value = '消費登録中...';
            const consumeRes = await postConsumptionRegister(consumptionModels);
            if (consumeRes.data.code != 200) {
              throw new Error(res.data.message)
            }
          }
          <?php 
          endif;
          ?>

          loadingText.value = '出庫登録中...';
          let params = new URLSearchParams();
          params.append("path", "/api/acceptance/register");
          params.append("_method", 'post');
          params.append("_csrf", _CSRF);
          params.append("isOnlyAcceptance", 'true');
          params.append("acceptanceItems", JSON.stringify(encodeURIToObject(acceptanceModels)));

          const res = await axios.post(_APIURL, params);

          if (res.data.code != 200) {
            throw new Error(res.data.message)
          }

          Swal.fire({
            icon: 'success',
            title: '登録が完了しました。',
          }).then((result) => {
            let tmp = [];
            replace(tmp);
            if(labelCreate.value && res.data.data[0]){
              const url = _ROOT + '&path=/label/acceptance/' + res.data.data[0];
              window.open(url, '_blank')
            }
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


      const postConsumptionRegister = async (consumptionModels) => {
        let params = new URLSearchParams();
        params.append("path", "/api/consumption/register");
        params.append("_method", 'post');
        params.append("_csrf", _CSRF);
        params.append("consumptionType", '1');
        params.append("consumptionDate", values.payoutDate);
        params.append("consumptionItems", JSON.stringify(encodeURIToObject(consumptionModels)));

        return await axios.post(_APIURL,params);
      }
      
      const postPayoutRegister = async (payoutModels) => {
          let params = new URLSearchParams();
          params.append("path", "/api/payout/register");
          params.append("_method", 'post');
          params.append("_csrf", _CSRF);
          params.append("isOnlyPayout", 'true');
          params.append("payoutDate", values.payoutDate);
          params.append("payoutItems", JSON.stringify(encodeURIToObject(payoutModels)));

        return await axios.post(_APIURL,params);
      }


      const createPayoutModel = (values) => {
        let items = values.payoutItems;
        let payoutItems = [];
        items.forEach(function(item, idx) {
          item._payout.forEach(function(payout, pidx) {
            if (payout.count != 0) {
              payoutItems.push({
                'inHospitalItemId': item.inHospitalItemId,
                'payoutQuantity': parseInt(payout.count),
                'targetDivisionId' : values.targetDivisionId,
                'sourceDivisionId' : values.sourceDivisionId,
                'lotNumber' : payout.lotNumber,
                'lotDate' : payout.lotDate,
                'card' : payout.card,
              })
            }
          })
        });
        return payoutItems;
      };
      
      const createConsumptionModel = ( values ) => {
        let items = values.payoutItems;
        let consumeItems = [];
        items.forEach(function(item, idx) {
          item._payout.forEach(function(payout, pidx) {
            if (payout.count != 0) {
              consumeItems.push({
                'inHospitalItemId': item.inHospitalItemId,
                'consumeLotDate': '',
                'consumeLotNumber': '',
                'consumeQuantity': parseInt(payout.count),
                'consumeUnitQuantity': 0,
                'divisionId' : values.targetDivisionId,
                'cardId' : '',
                'lotManagement' : false,
              })
            }
          })
        });
        return consumeItems;
      };

      const payoutRegister = handleSubmit(async (values) => {
        try {
          let consumptionModels = [];
          const payoutModels = createPayoutModel(values);
          if (payoutModels.length === 0) {
            Swal.fire({
              icon: 'error',
              title: '登録する商品がありませんでした。',
              text: '内容を確認の上、再送信をしてください。',
            })
            return false;
          }
          
          <?php 
          if(gate('is_admin')): 
          ?>
          if(isConsumption.value){
            consumptionModels = createConsumptionModel(values);
            if (consumptionModels.length === 0) {
              Swal.fire({
                icon: 'error',
                title: '登録する商品がありませんでした。',
                text: '内容を確認の上、再送信をしてください。',
              })
              return false;
            }
          }

          if(isConsumption.value){
            loadingText.value = '消費登録中...';
            const consumeRes = await postConsumptionRegister(consumptionModels);
            if (consumeRes.data.code != 200) {
              throw new Error(res.data.message)
            }
          }
          <?php 
          endif;
          ?>

          loadingText.value = '払出登録中...';
          const res = await postPayoutRegister(payoutModels);

          if (res.data.code != 200) {
            throw new Error(res.data.message)
          }

          Swal.fire({
            icon: 'success',
            title: '登録が完了しました。',
          }).then((result) => {
            let tmp = [];
            replace(tmp);
            if(labelCreate.value && res.data.data[0]){
              const url = _ROOT + '&path=/label/payout/' + res.data.data[0];
              window.open(url, '_blank')
            }
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
        let object = JSON.parse(JSON.stringify(fields.value[idx].value));
        object[key] = value;
        update(idx, object);
      };

      const getStockCount = (inHospitalItemId) => {
    
        let params = new URLSearchParams();
        params.append("path", "/api/stock/"+ values.sourceDivisionId + "/" + inHospitalItemId);
        params.append("_method", 'get');
        params.append("_csrf", _CSRF);

        return axios.post(_APIURL, params);

      }


      const additem = async(item) => {
        item = JSON.parse(JSON.stringify(item));
        
        item._payout = (item._payout)? item._payout : [{
          count : 0,
          lotNumber : item.lotNumber ?? '',
          lotDate : item.lotDate ?? '',
          card : '',
        }];

        item.orderUnitQuantity = (item.orderUnitQuantity) ? item.orderUnitQuantity : 1;
        
        let checked = false;
        if (Array.isArray(values.payoutItems)) {
          values.payoutItems.forEach((v, idx) => {
            if (
              v.inHospitalItemId === item.inHospitalItemId
            ) {
              v._payout.push(item._payout[0]);
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

          await getStockCount(item.inHospitalItemId).then((res) => {
            if (res.data.code != 200) {
              throw new Error(res.data.message)
            }

            let itemIndex = values.payoutItems.findIndex((v) => v.inHospitalItemId === item.inHospitalItemId);

            updateItem(itemIndex, "_stock", res.data.data);
          })
        }

      };

      
      const addPayout = (item , type) => {
        item = JSON.parse(JSON.stringify(item));

        let itemCheck = false;

        if (Array.isArray(values.payoutItems)) {
          values.payoutItems.forEach((v, idx) => {
            if (
              v.inHospitalItemId === item.inHospitalItemId
            ) {
              itemCheck = true;
              let payoutCheck = false;
              v._payout.forEach((payout) => {
                if (
                  payout.count === 0 && payoutCheck === false
                ) {
                  payoutCheck = true;
                  payout.count = item.payoutCount;
                  payout.card = (type === 'card')? item.barcode : '';
                }
              });
              if (!payoutCheck) {
                v._payout.push({
                  count : item.payoutCount,
                  lotNumber : '',
                  lotDate : '',
                  card : (type === 'card')? item.barcode : '',
                });
              }
            }
          });
        }
        if(!itemCheck)
        {
          item._payout = [];
          item._payout.push({
            count : item.payoutCount,
            lotNumber : '',
            lotDate : '',
            card : (type === 'card')? item.barcode : '',
          });
          additem(item);
        }
      }
    
      const searchCardId = (card) => {
        let exist = false;
        if (Array.isArray(values.payoutItems)) {
          values.payoutItems.forEach((v, idx) => {
            v._payout.forEach((payout) => {
              if (
                payout.card == card
              ) {
                exist = true;
              }
            });
          });
        }
        return exist;
      }

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
            items.item[id].payoutCount = 1;
          });
        }
        
        if (items.type == "payout") {
          items.item.forEach((x, id) => {
            items.item[id].payoutCount = items.item[id].payoutQuantity
          });
        }
        if (items.type == "card") {
          items.item.forEach((x, id) => {
            items.item[id].payoutCount = items.item[id].cardQuantity
          });
        }
        if (items.type == "customlabel") {
          items.item.forEach((x, id) => {
            items.item[id].payoutCount = items.item[id].customQuantity
            items.item[id].divisionId = values.targetDivisionId
          });
        }

        if (items.item.length === 1) {
          if (items.item[0].divisionId) {
            if(values.targetDivisionId === items.item[0].divisionId){
              if(items.type == "card" && searchCardId(items.item[0].barcode ))
              {
                Swal.fire({
                    icon: 'error',
                    title: 'エラー',
                    text: 'すでに読み込まれたカードです。',
                  });
                return false;
              }
              // 払出先のカードを紐づける
              addPayout(items.item[0] , items.type)
              return true;
            } else if(values.sourceDivisionId === items.item[0].divisionId)
            {
              additem(items.item[0]);
            } else {
              Swal.fire({
                icon: 'error',
                title: 'エラー',
                text: '読み込んだ値と選択している部署が一致しませんでした',
              });
              return false;
            }
          } else {
            items.item[0].divisionId = values.sourceDivisionId
            additem(items.item[0])
          }
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
            count += parseInt(x.count);
          }) 
        }

        return count;
      }
      
      const totalPrice = (item) => {
        let count = totalCount(item);
        return item.unitPrice * count;
      }

      const clonePayout = (item_id , payout_id) => {
        let tmp = values.payoutItems[item_id];
        tmp = JSON.parse(JSON.stringify(tmp));
        tmp._payout.push({
          count : tmp._payout[payout_id].count,
          lotNumber : tmp._payout[payout_id].lotNumber,
          lotDate : tmp._payout[payout_id].lotDate,
          card :'',
        })
        update(item_id , tmp)
      }

      const deletePayout = (item_id , payout_id) => {
        let tmp = values.payoutItems[item_id];
        tmp = JSON.parse(JSON.stringify(tmp));

        tmp._payout.splice(payout_id , 1);

        update(item_id , tmp)
        
        if( values.payoutItems[item_id]._payout.length === 0 )
        {
          remove(item_id);
        }
      }

      const toQuantity = () => {
        Swal.fire({
            title: '一括入数反映',
            text: "払出数が0の情報に対して入数を反映してよろしいですか？",
            icon: 'info',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then((result) => {
            if (result.isConfirmed) {
              if (Array.isArray(values.payoutItems)) {
                values.payoutItems.forEach((v, idx) => {
                    let lotCheck = false;
                    v._payout.forEach((payout) => {
                    if (
                        payout.count == 0
                      ){
                        payout.count = v.quantity
                    }
                  });
                });
              }
            }
          })
      }
      const formatNumber = (num) => {
          if (num >= 9999) {
              return '9999';
          } else {
              return num.toString().padStart(4, '0');
          }
      }
      const additemsForSlip =  (items) => {
        items = JSON.parse(JSON.stringify(items));
        items.forEach(async (elm, index) => {
            let exist = false;
            await searchInHospitalItem("inHPItem"+ elm.inHospitalItemId + formatNumber(elm.consumptionQuantity))
              .then((response) => {
                complete();
                addItemByBarcode(response.data.data);
              })
              .catch((error) => {
                complete();
                Swal.fire({
                  icon: "error",
                  title: "検索に失敗しました。再度お試しください。",
                });
              });
        });
      }

      const searchInHospitalItem = (barcode) => {
        if (!barcode) {
          return false;
        }
        let params = new URLSearchParams();
        params.append("path", "/api/barcode/search");
        params.append("barcode", barcode);
        params.append("_csrf", _CSRF);
        start();

        return axios
          .post(_APIURL, params)
      }
      return {
        loadingText,
        isConsumption,
        isRequired,
        values,
        openModal,
        selectInHospitalItems,
        addItemByBarcode,
        labelCreate,
        loading, 
        start, 
        complete,
        totalItemCount,
        isSubmitting,
        alert,
        confirm,
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
        totalPrice,
        totalCount,
        payoutUnitPriceUseFlag,
        getStockCount,
        deletePayout,
        clonePayout,
        toQuantity,
        onAcceptanceRegisterSubmit,
        additemsForSlip
      };
    },
    watch: {
      isSubmitting() {
        this.loading = this.isSubmitting;
      },
      labelCreate(bool) {
        localStorage.joypla_payoutLabelCreate = bool;
      },
      isConsumption(bool) {
        localStorage.joypla_is_consumption = bool;
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
      'v-consumption-history-modal-for-item-request': vConsumptionHistoryModalForItemRequest
    },
  }).mount('#top');
</script> 
<script>
    window.addEventListener('DOMContentLoaded', function() {
        new ScrollHint('#payoutTable', {
            scrollHintIconAppendClass: 'scroll-hint-icon-white', // white-icon will appear
            applyToParents: true,
            i18n: {
                scrollable: 'スクロールできます'
            }
        });
    });
</script>