<div id="top" v-cloak>
  <header-navi></header-navi>
  <v-loading :show="loading" :text="loadingText"></v-loading>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <h1 class="text-2xl mb-2">個別消費</h1>
        <hr>
        <div>
          <div class="mb-2 lg:w-1/3">
            <v-input
              name="consumeDate"
              type="date"
              :rules="{ required: true, regex: /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/ }"
              label="消費日"
              title="消費日指定"
              ></v-input>
          </div>
          <div class="mb-2 lg:w-1/3">
            <v-select-division 
            name="divisionId" 
            label="消費部署" 
            :rules="{ required : true }"
            title="消費部署指定"
            :disabled="values.divisionId != '' && fields.length > 0"
            :is-only-my-division="<?php var_export(
                gate('register_of_consumption_slips')->isOnlyMyDivision()
            ); ?>"
            />
          </div>
          <?php if(gate('is_use_direct_delivery')): ?>
          <div class="mb-2 lg:w-1/3">
            <v-select 
            name="type" 
            label="消費タイプ指定" 
            :rules="{ required : false }"
            title="消費タイプ指定"
            :options="consumeTypes"
            />
          </div>
          <?php endif; ?>
          <div class="my-4 grid grid-cols-3 gap-4 lg:w-1/3">
            <v-button-default type="button" data-micromodal-trigger="inHospitalItemModal">商品検索</v-button-default>
            <v-in-hospital-item-modal v-on:additem="additem" :unit-price-use="consumptionUnitPriceUseFlag">
            </v-in-hospital-item-modal>
          </div>
          <div class="my-4 w-full items-center flex">
            <div class="mx-2">
              <v-switch id="isOrder" v-model="isOrder" :message="(isOrder)? '発注書を作成する' : '発注書を作成しない'"></v-switch>
            </div>
            <div class="mx-2" v-if="isOrder">
              <v-switch id="integrate" v-model="integrate" :message="(integrate)? '既存の未発注伝票に追加します' : '新規発行します'"></v-switch>
            </div>
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
            <v-button-primary type="button" class="w-full" @click.native="onSubmit">消費登録</v-button-primary>
          </div>
          <transition-group tag="div" name="list" appear>
            <div class="my-2" v-for="(item , idx) in fields" :key="item.key">
              <div class="w-full lg:flex mt-3">
                <div class="lg:flex-1 flex lg:w-3/4">
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
                        <div class="w-full text-lg text-blue-500 font-bold font-heading flex gap-6">
                          <span class="text-xl text-orange-600 font-bold font-heading">&yen; {{ numberFormat(item.value.unitPrice) }}/{{ item.value.quantityUnit }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="lg:flex-1 lg:w-1/4">
                  <div class="lg:flex gap-6">
                    <div class="lg:w-1/2">
                      <v-input 
                      :name="`consumeItems[${idx}].consumeLotNumber`" 
                      label="ロット番号" 
                      :rules="{ required : isRequired(idx) ,lotnumber: true , twoFieldRequired : [ '消費期限', `@consumeItems[${idx}].consumeLotDate`]  }" 
                      type="text" 
                      title="ロット番号"></v-input>
                    </div>
                    <div class="lg:w-1/2">
                      <v-input 
                      :name="`consumeItems[${idx}].consumeLotDate`" 
                      label="消費期限" 
                      :rules="{ required : isRequired(idx) , twoFieldRequired : [ 'ロット番号' , `@consumeItems[${idx}].consumeLotNumber`] }" 
                      type="date" 
                      title="消費期限"></v-input>
                    </div>
                  </div>
                  <div class="lg:flex gap-6">
                    <div class="lg:w-1/2">
                      <v-input-number 
                      :rules="{ between: (values.type === '1') && [0 , 99999] || (values.type === '3') && [-99999 , 99999] }" 
                      :name="`consumeItems[${idx}].consumeUnitQuantity`"
                      :min="(values.type === '1') && 0 || (values.type === '3') && -99999"
                      label="消費数（個数）" 
                      :unit="item.value.itemUnit" 
                      :step="1" 
                      :title="`消費数（個数）/${item.value.quantity}${ item.value.quantityUnit }入り`" 
                      ></v-input-number>
                    </div>
                    <div class="lg:w-1/2">
                      <v-input-number 
                      :rules="{ between:  (values.type === '1') && [0 , 99999] || (values.type === '3') && [-99999 , 99999] }" 
                      :name="`consumeItems[${idx}].consumeQuantity`" 
                      label="消費数（入数）" 
                      :min="(values.type === '1') && 0 || (values.type === '3') && -99999"
                      :unit="item.value.quantityUnit" 
                      :step="1"
                      title="消費数（入数）" ></v-input-number>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 flex">
                <v-button-danger type="button" @click.native="remove(idx)">削除</v-button-danger>
                <div class="flex-1 items-center ">
                  <p class="text-xl text-gray-800 font-bold font-heading text-right">&yen; {{ numberFormat(consumePrice(idx) ) }} ( {{ consumeQuantity(idx) }}{{ item.value.quantityUnit }} )</p>
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
                      <div
                          class="flex flex-wrap items-center mb-3"
                          v-for="(elem, index) in selectInHospitalItems">
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
      
      const { ref, toRef , toRefs , reactive ,onMounted} = Vue;
      const { useFieldArray , useForm } = VeeValidate;
      const consumptionUnitPriceUseFlag = "<?php echo $consumptionUnitPriceUseFlag; ?>";

      const loading = ref(false);
      const loadingText = ref('');
      const start = (text) => {
          loading.value = true;
      }

      const complete = () => {
          loading.value = false;
      }

      const sleepComplate = () => {
          window.setTimeout(function () {
              complete();
          }, 500);
      }
      start();
      
      onMounted( () => {
        sleepComplate()
      });


      const date = new Date();
      const yyyy = date.getFullYear();
      const mm = ("0"+(date.getMonth()+1)).slice(-2);
      const dd = ("0"+date.getDate()).slice(-2);

      const { handleSubmit , control, meta , validate , values , isSubmitting  } = useForm({
        initialValues: {
          consumeItems: [],
          divisionId: "",
          consumeDate: yyyy+'-'+mm+'-'+dd,
          type : '1',
        },
        validateOnMount : false
      });
      const { remove, insert, fields , update , replace } = useFieldArray('consumeItems' , control);

      const alertModel = reactive({
        message: "",
        headtext: "",
        okMethod: function(){ console.log('') },
      });

      const confirmModel = reactive({
        message: "",
        headtext: "",
        okMethod: function(){ console.log('') },
        cancelMethod: function(){ console.log('') },
      });

      const breadcrumbs = [
          {
            text: '消費メニュー',
            disabled: false,
            href: _ROOT + '&path=/consumption',
          },
          {
            text: '個別消費',
            disabled: true, 
          }];

      const createConsumptionModel = ( values ) => {
        let items = values.consumeItems;
        let consumeItems = [];
        items.forEach(function(item, idx){
          if( consumeQuantity(idx) !== 0 ){ consumeItems.push({
            'inHospitalItemId': item.inHospitalItemId,
            'consumeLotDate': item.consumeLotDate,
            'consumeLotNumber': item.consumeLotNumber,
            'consumeQuantity': item.consumeQuantity,
            'consumeUnitQuantity': item.consumeUnitQuantity,
            'divisionId' : values.divisionId,
            'cardId' : item.cardId,
            'lotManagement' : false,
          })}
        });
        return consumeItems;
      };
      
      const createOrderModel = (values) => {
        let items = values.consumeItems;
        let orderItems = [];
        items.forEach(function(item, idx) {
          if (orderQuantity(idx) !== 0) {
            orderItems.push({
              'inHospitalItemId': item.inHospitalItemId,
              'orderUnitQuantity': parseInt(orderQuantity(idx)),
              'divisionId': values.divisionId,
            });
          }
        });
        return orderItems;
      };

      const consumeQuantity = (idx) => {
        let num = 0;
        num += parseInt(values.consumeItems[idx].consumeQuantity);
        num += parseInt(values.consumeItems[idx].quantity * values.consumeItems[idx].consumeUnitQuantity);
        return num;
      };
      
      const orderQuantity = (idx) => {
        let num = 0;
        num += parseInt(values.consumeItems[idx].consumeUnitQuantity);
        num += Math.ceil(values.consumeItems[idx].consumeQuantity / values.consumeItems[idx].quantity);
        return num;
      };

      const consumePrice = (idx) => {
        return values.consumeItems[idx].unitPrice * consumeQuantity(idx);
      };

      const totalAmount = () => {
        let num = 0;
        values.consumeItems.forEach((v, idx) => {
          num += consumePrice(idx);
        });
        return num;
      };

      const itemCount = () => {
        let num = 0;
        values.consumeItems.forEach((v, idx) => {
          num += ( consumeQuantity(idx) > 0 )? 1 : 0;
        });
        return num;
      };

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
          //return new Intl.NumberFormat('ja-JP').format(value);
      }

      const isRequired = (idx) => {
        if( fields.value[idx].value.lotManagement == "1" )
        {
          return true;
        }
        return false;
      };

      const alertSetting = toRefs(alertModel);
      const confirmSetting = toRefs(confirmModel);

      const alert = ref();
      const confirm = ref();

      const onSubmit = async () =>{
        const { valid, errors } = await validate();

        if(!valid){
          Swal.fire({
            icon: 'error',
            title: '入力エラー',
            text: '入力エラーがございます。ご確認ください',
          })
        } 
        else 
        {
          text = '消費登録を行います。よろしいですか？';
          if(values.type === '3'){
            text = '直納処理を行います。よろしいですか？<br>※直納処理は在庫変動しません';
          }
          Swal.fire({
            title: '確認',
            html: text,
            icon: 'info',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then((result) => {
            if(result.isConfirmed){
              consumptionRegister();
            }
          })
        }
      };

      const postConsumptionRegister = async (consumptionModels) => {
        let params = new URLSearchParams();
        params.append("path", "/api/consumption/register");
        params.append("_method", 'post');
        params.append("_csrf", _CSRF);
        params.append("consumptionType", values.type);
        params.append("consumptionDate", values.consumeDate);
        params.append("consumptionItems", JSON.stringify(encodeURIToObject(consumptionModels)));

        return await axios.post(_APIURL,params);
      }

      const postOrderRegister = async (orderModels) => {

        let params = new URLSearchParams();
        params.append("path", "/api/order/register");
        params.append("_method", 'post');
        params.append("_csrf", _CSRF);
        //params.append("orderDate", values.orderDate);
        params.append("integrate", integrate.value);
        params.append("orderItems", JSON.stringify(encodeURIToObject(orderModels)));

        return await axios.post(_APIURL,params);
      }

      const consumptionRegister = handleSubmit(async (values) => {
        try {
            let orderModels = [];
            const consumptionModels = createConsumptionModel(values);
            if( consumptionModels.length === 0)
            {
              Swal.fire({
                icon: 'error',
                title: '登録する商品がありませんでした。',
                text: '内容を確認の上、再送信をしてください。',
              })
              return false;
            }

            if(isOrder.value){
              orderModels = createOrderModel(values);
              console.log(orderModels);
              if (orderModels.length === 0) {
                Swal.fire({
                  icon: 'error',
                  title: '登録する商品がありませんでした。',
                  text: '内容を確認の上、再送信をしてください。',
                })
                return false;
              }
            }

            loadingText.value = '消費登録中...';
            const res = await postConsumptionRegister(consumptionModels);

            if(res.data.code != 200) {
              throw new Error(res.data.message)
            }

            if(isOrder.value){
              loadingText.value = '発注登録中...';
              const orderRes = await postOrderRegister(orderModels);
              if(orderRes.data.code != 200) {
                throw new Error(orderRes.data.message)
              }
            }

            Swal.fire({
                icon: 'success',
                title: '登録が完了しました。',
            }).then((result) => {
              replace([]);
            });
            return true ;
          } catch (error) {
            Swal.fire({
              icon: 'error',
              title: 'システムエラー',
              text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
            });
          }
      });

      const updateItem = (idx, key , value) => {
        let object = JSON.parse(JSON.stringify(fields[idx].value));
        object[key] = value;
        update(idx, object);
      };

      const searchCardId = (cardId) => {
        return fields.value.find((x) => 
          ( x.value.cardId === cardId)
          );
      }

      const additem = (item) =>
      {
        item = JSON.parse(JSON.stringify(item));
        item.consumeQuantity = ( item.consumeQuantity ) ? parseInt(item.consumeQuantity) : 0 ;
        item.consumeUnitQuantity = ( item.consumeUnitQuantity ) ? parseInt(item.consumeUnitQuantity) : 0 ;
        item.consumeLotNumber = ( item.lotNumber ) ? item.lotNumber : "" ;
        item.consumeLotDate =  ( item.lotDate ) ? item.lotDate : "" ;
        item.cardId = (item.cardId)? item.cardId : "";
        if( !values.consumeItems ){
          values.consumeItems = [];
        }
        insert(0,item);
      };

      const openModal = ref();
      const selectInHospitalItems = ref([]);
      const addItemByBarcode = (items) => 
      {
        selectInHospitalItems.value = [];
        if (!items.item || items.item.length === 0) {
            Swal.fire({
                icon: 'info',
                title: '商品が見つかりませんでした',
            });
            return false;
        }

        if(items.type == "received")
        {
          items.item.forEach((x , id)=>{
            items.item[id].consumeUnitQuantity = 1;
          });
        }
        
        if(items.type == "payout")
        {
          items.item.forEach((x , id)=>{
            items.item[id].consumeQuantity = parseInt(items.item[id].payoutQuantity);
          });
        }
        if(items.type == "card")
        {
          let exist = false
          items.item.forEach((x , id)=>{
            if(searchCardId(items.item[id].barcode) !== undefined)
            {
              exist = true;
            }
          })
          if( exist )
          {
            Swal.fire({
                icon: 'error',
                title: 'エラー',
                text: 'すでに読み込まれたカードです。',
              });
            return false;
          }          
          items.item.forEach((x , id)=>{
            items.item[id].cardId = items.item[id].barcode;
            items.item[id].consumeQuantity = parseInt(items.item[id].cardQuantity);
          });
        }
        if(items.type == "customlabel")
        {
          items.item.forEach((x , id)=>{
            items.item[id].consumeQuantity = parseInt(items.item[id].customQuantity);
          });
        }

        if (items.item.length === 1) {
          if(items.item[0].divisionId)
          {
            if(values.divisionId !== items.item[0].divisionId)
            {
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

      const consumeTypes =  [
        { label: "通常消費", value: "1" },
        { label: "直納処理", value: "3" }
      ];
      
      const integrate = ref(localStorage.joypla_unorder_slip_integrate === 'true');

      const isOrder = ref(localStorage.joypla_is_order === 'true');

      return {
        isOrder,
        integrate,
        consumeTypes,
        values,
        addItemByBarcode,
        selectInHospitalItems,
        openModal,
        loading, 
        loadingText,
        start, 
        complete,
        itemCount,
        isSubmitting,
        alert,
        confirm,
        consumeQuantity,
        consumePrice,
        totalAmount,
        additem,
        isRequired,
        onSubmit,
        breadcrumbs,
        alertSetting,
        confirmSetting,
        numberFormat,
        meta,
        fields,
        remove,
        validate,
        consumptionUnitPriceUseFlag,
      };
    },
    watch: {
      integrate(bool) {
        localStorage.joypla_unorder_slip_integrate = bool;
      },
      isOrder(bool) {
        localStorage.joypla_is_order = bool;
      },
      isSubmitting(){
        this.loading = this.isSubmitting;
      },
      fields: {
        async handler(val, oldVal) {
          await this.validate();
        },
        deep: true
      },
    },


    components: {
      'v-barcode-search' : vBarcodeSearch,
      'v-loading' : vLoading,
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
      'v-open-modal': vOpenModal,
      'v-switch': vSwitch,
      'header-navi' : headerNavi
    },
}).mount('#top');
</script> 