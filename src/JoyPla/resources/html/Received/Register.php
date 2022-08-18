<div id="top" v-cloak>
  <header-navi></header-navi>
  <v-loading :show="loading"></v-loading>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <h1 class="text-2xl mb-2">個別入荷</h1>
        <hr>
        <form>
          <div class="my-4 grid grid-cols-2 gap-4 lg:w-1/2 items-center">
            <v-button-default type="button" data-micromodal-trigger="orderItemModal">発注済商品検索</v-button-default>
            <v-order-item-modal v-on:additem="additem" :is-only-my-division="<?php var_export(gate('receipt')->isOnlyMyDivision()) ?>">
            </v-order-item-modal>
          </div>
          <div class="p-2 bg-gray-300">
            <v-barcode-search-for-order-data @additem="addItemByBarcode"></v-barcode-search-for-order-data>
          </div>
          <div class="my-2" v-if="fields.length == 0">
            <div class="max-h-full h-full grid place-content-center w-full lg:flex border border-sushi-600 bg-white mt-3">
              <div class="flex-1 p-4 relative text-center">商品を選択または、バーコードを読み取ってください</div>
            </div>
          </div>
          <div class="mt-4 p-4 shadow-md drop-shadow-md" v-if="fields.length > 0">
            <p class=" text-xl">登録アイテム数: {{ numberFormat(itemCount()) }} アイテム</p>
            <p class=" text-xl">合計金額: &yen; {{ numberFormat(totalAmount()) }} </p>
            <v-button-primary type="button" class="w-full" @click.native="onSubmit">個別入荷登録</v-button-primary>
          </div>
          <transition-group tag="div" name="list" appear>
            <div class="my-2" v-for="(item, idx) in fields" :key="item.key">
              <div class="p-4 text-base bg-gray-100 border border-gray-400 my-2">
                <div class="flex gap-2">
                  <div class="flex-none">発注番号</div>
                  <div class="flex-1">{{ item.value.orderId }}</div>
                </div>
                <div class="flex gap-2">
                  <div class="flex-none">発注日</div>
                  <div class="flex-1">{{ item.value.orderDate }}</div>
                </div>
                <div class="flex gap-2">
                  <div class="flex-none">発注元部署</div>
                  <div class="flex-1">{{ item.value.division.divisionName }}</div>
                </div>
                <div class="gap-2">
                  <div class="flex-1">
                      合計入荷数
                      {{ item.value.sumReceivedQuantity }}{{ item.value.quantity.itemUnit }}
                      / 入荷可能数(
                      {{ item.value.orderQuantity - item.value.receivedQuantity }}{{ item.value.quantity.itemUnit }}
                      )
                  </div>
                  <v-input
                      :name="`receivedItems[${idx}].sumReceivedQuantity`"
                      label="合計入荷数"
                      :rules="{ between: ( (item.value.orderQuantity > 0)? [ 0 , item.value.orderQuantity - item.value.receivedQuantity ] : [ item.value.orderQuantity - item.value.receivedQuantity , 0 ] ) }"
                      type="hidden"
                      title=""></v-input>
                </div>
              </div>
              <div class="w-full lg:flex mt-3">
                <div class="flex-auto lg:flex-1 flex">
                  <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="item.value.inItemImage"></item-view>
                  <div class="flex-1 px-4 relative">
                    <div class="flex-auto lg:flex justify-between leading-normal lg:space-y-0 space-y-4 gap-6">
                      <div class="break-all">
                        <div class="w-full">
                          <h3 class="text-xl font-bold font-heading" v-if="item.value.item.makerName">{{ item.value.item.makerName }}</h3>
                          <p class="text-md font-bold font-heading" v-if="item.value.item.itemName">{{ item.value.item.itemName }}</p>
                          <p class="text-gray-500" v-if="item.value.item.itemCode">{{ item.value.item.itemCode }}</p>
                          <p class="text-gray-500" v-if="item.value.item.itemStandard">{{ item.value.item.itemStandard }}</p>
                          <p class="text-gray-500" v-if="item.value.distributor.distributorName">{{ item.value.distributor.distributorName }}</p>
                        </div>
                        <div class="w-full text-lg font-bold font-heading flex gap-6">
                          <span class="text-xl text-orange-600 font-bold font-heading">&yen; {{ numberFormat(item.value.price) }}/{{ item.value.quantity.itemUnit }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="lg:flex-1 flex-col">
                <div v-for="(received , rid) in item.value.receiveds">
                  <div class="lg:flex gap-6 py-4">
                    <div class="lg:w-1/3">
                      <v-input 
                      :name="`receivedItems[${idx}].receiveds[${rid}].lotNumber`" 
                      label="ロット番号" 
                      :rules="{ required : isRequired(idx) ,lotnumber: true , twoFieldRequired : [ '消費期限', `@receivedItems[${idx}].receiveds[${rid}].lotDate`]  }" 
                      type="text" 
                      change-class-name="inputChange"
                      title="ロット番号"></v-input>
                    </div>
                    <div class="lg:w-1/3">
                      <v-input 
                      :name="`receivedItems[${idx}].receiveds[${rid}].lotDate`" 
                      label="消費期限" 
                      :rules="{ required : isRequired(idx) , twoFieldRequired : [ 'ロット番号' , `@receivedItems[${idx}].receiveds[${rid}].lotNumber`] }" 
                      type="date" 
                      change-class-name="inputChange"
                      title="消費期限"></v-input>
                    </div>
                    <div class="lg:w-1/3">
                      <v-input-number 
                      :rules="{ required : true , between: ( (item.value.orderQuantity > 0)? [ 0 , item.value.orderQuantity - item.value.receivedQuantity ] : [ item.value.orderQuantity - item.value.receivedQuantity , 0 ] ) }" 
                      :name="`receivedItems[${idx}].receiveds[${rid}].receivedUnitQuantity`"
                      label="入荷数（個数）" 
                      :unit="item.value.quantity.itemUnit" 
                      @change="receivedQuantitySum(idx)"  
                      change-class-name="inputChange"
                      :step="1" 
                      :title="`入荷数（個数）/${item.value.quantity.quantityNum}${ item.value.quantity.quantityUnit }入り`" ></v-input-number>
                    </div>
                    <div class="lg:mt-0 mt-2 flex justify-between gap-4">
                      <v-text
                          title=" ">
                          <v-button-danger 
                          type="button" 
                          class="whitespace-pre" 
                          @click.native="deleteReceived(idx,rid)">削除</v-button-danger>
                      </v-text>
                      <v-text class="order-last"
                          title=" ">
                          <v-button-primary 
                          type="button" 
                          class="whitespace-pre" 
                          @click.native="addReceived(idx)">追加</v-button-primary>
                      </v-text>
                    </div>
                </div>
                <div class="pt-4 pb-2 w-full">
                  <div class="border-t border-gray-200"></div>
                </div> 
              </div>
              <div class="mt-4 flex">
                <div class="flex-1 items-center ">
                  <p class="text-xl text-gray-800 font-bold font-heading text-right">&yen; {{ numberFormat(receivedPrice(idx) ) }} ( {{ item.value.sumReceivedQuantity }}{{ item.value.quantity.itemUnit }} )</p>
                </div>
              </div>
              <div class="pt-4 pb-2 w-full">
                <div class="border-t border-gray-200"></div>
              </div> 
            </div>
          </transition-group>
        </form>
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
                      <template
                          v-for="(order, index) in selectOrderItems">
                          <template v-for="(orderItem , idx ) in order.orderItems">
                            <div class="p-4 text-base lg:mx-4 mx-0 bg-gray-100 border border-gray-400 my-2">
                              
                              <div class="flex gap-2">
                                <div class="flex-none">発注番号</div>
                                <div class="flex-1">{{ order.orderId }}</div>
                              </div>
                              <div class="flex gap-2">
                                <div class="flex-none">発注日</div>
                                <div class="flex-1">{{ order.orderDate }}</div>
                              </div>
                              <div class="flex gap-2">
                                <div class="flex-none">発注元部署</div>
                                <div class="flex-1">{{ orderItem.division.divisionName }}</div>
                              </div>
                            </div>
                            <div class="flex flex-wrap items-center mb-3">
                              <div class="w-full lg:w-5/6 lg:px-4 px-0 mb-6 lg:mb-0">
                                <div class="flex flex-wrap items-center gap-4">
                                  <div class="flex-none">
                                    <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="orderItem.itemImage"></item-view>
                                  </div>
                                  <div class="break-words flex-1 box-border w-44">
                                    <h3 class="text-xl font-bold font-heading">{{ orderItem.item.makerName }}</h3>
                                    <p class="text-md font-bold font-heading">{{ orderItem.item.itemName }}</p>
                                    <p class="text-md font-bold font-heading">{{ orderItem.item.itemJANCode }}</p>
                                    <p class="text-gray-500">{{ orderItem.item.itemCode }}<br>{{ orderItem.item.itemStandard }}</p>
                                    <p class="text-gray-500">{{ orderItem.quantity.quantityNum }}{{ orderItem.quantity.quantityUnit }} 入り</p>
                                    <p>
                                      <span class="text-xl text-orange-600 font-bold font-heading">
                                        {{ numberFormat( orderItem.orderQuantity ) }}{{ orderItem.quantity.itemUnit }} 発注中
                                      </span>
                                      <span class="text-xl text-orange-600 font-bold font-heading">
                                        {{ numberFormat( ( orderItem.orderQuantity - orderItem.receivedQuantity) ) }}{{ orderItem.quantity.itemUnit }} 未入荷
                                      </span>
                                    </p>
                                    <p class="text-gray-800">ロット番号：{{ orderItem.lotNumber }}</p>
                                    <p class="text-gray-800">使用期限：{{ orderItem.lotDate }}</p>
                                    <p class="text-gray-800">{{ orderItem.distributor.distributorName }}</p>
                                  </div>
                                </div>
                              </div>
                              <div class="w-full lg:block lg:w-1/6 px-4 py-4">
                                <v-button-default type="button" class="w-full" v-on:click.native="additem(order , idx)">反映</v-button-default>
                              </div>
                              <div class="py-2 px-4 w-full">
                                <div class="border-t border-gray-200"></div>
                              </div>
                            </div>
                          </template>
                      </template>
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

      const loading = ref(false);
      const start = () => {
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
          receivedItems: [],
          barcode: "",
          orderDate: yyyy+'-'+mm+'-'+dd,
        },
        validateOnMount : false
      });
      const { remove, push, fields , update , replace } = useFieldArray('receivedItems' , control);
    
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
            text: '発注メニュー',
            disabled: false,
            href: _ROOT + '&path=/order',
          },
          {
            text: '個別入荷',
            disabled: true, 
          }];

      const createReceivedModel = ( values ) => {
        let items = values.receivedItems;
        let receivedItems = [];
        items.forEach(function(item, idx){
          if(item.sumReceivedQuantity != 0)
          {
            receivedItems.push({
              'orderItemId': item.orderItemId,
              'receiveds': item.receiveds,
            });
          }
        });
        return receivedItems;
      };

      const receivedQuantitySum = (idx) => {
        if (!fields.value[idx] || !fields.value[idx].value.receiveds) {
            fields
                .value[idx]
                .value
                .receiveds = [];
        }
        fields
            .value[idx]
            .value
            .sumReceivedQuantity = fields
            .value[idx]
            .value
            .receiveds
            .reduce(function (sum, element) {
                return sum + element.receivedUnitQuantity;
            }, 0);
      }
    

      const receivedPrice = (idx) => {
        return values.receivedItems[idx].price * values.receivedItems[idx].sumReceivedQuantity;
      };

      const totalAmount = () => {
        let num = 0;
        values.receivedItems.forEach((v, idx) => {
          num += receivedPrice(idx);
        });
        return num;
      };

      const itemCount = () => {
        let num = 0;
        values.receivedItems.forEach((v, idx) => {
          num += ( v.sumReceivedQuantity > 0 )? 1 : 0;
        });
        return num;
      };

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      }
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
          Swal.fire({
            title: '個別入荷登録を行います。',
            text: "よろしいですか？",
            icon: 'info',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then((result) => {
            if(result.isConfirmed){
              receivedRegister();
            }
          })
        }
      };

      const receivedRegister = handleSubmit(async (values) => {
        try {
              
            const receivedModels = createReceivedModel(values);
            if( receivedModels.length === 0)
            {
              Swal.fire({
                icon: 'error',
                title: '登録する商品がありませんでした。',
                text: '内容を確認の上、再送信をしてください。',
              })
              return false;
            }
            
            let params = new URLSearchParams();
            params.append("path", "/api/received/register");
            params.append("_csrf", _CSRF);
            params.append("receivedItems", JSON.stringify(encodeURIToObject(receivedModels)));

            const res = await axios.post(_APIURL,params);
            
            if(res.data.code != 200) {
              throw new Error(res.data.message)
            }
            
            Swal.fire({
                icon: 'success',
                title: '登録が完了しました。',
            }).then((result) => {
              let tmp = [];
              replace(tmp);
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

      const deleteReceived = (idx, ridx) => {
        let result = fields.value[idx].value.receiveds.filter((value, index) => {
            if (index === ridx) {
                return;
            }
            return value;
        })
        .filter(e => e);
        fields
            .value[idx]
            .value
            .receiveds = (result)
                ? result
                : [];

        if( !fields.value[idx].value.receiveds || fields.value[idx].value.receiveds.length === 0 )
        {
          remove(idx);
        }
    };

    const isChange = ref(false);
      
    const addReceived = (idx) => {
        if (!fields.value[idx].value.receiveds) {
            fields
                .value[idx]
                .value
                .receiveds = [];
        }
        fields
            .value[idx]
            .value
            .receiveds
            .push({'receivedUnitQuantity': 1, 'lotNumber': "", 'lotDate': ""});
        receivedQuantitySum(idx);
    };


      const additem = (list , idx) =>
      {
        list.orderItems[idx].orderDate = list.orderDate;
        let item = list.orderItems[idx];
        item = JSON.parse(JSON.stringify(item)); 
        item.sumReceivedQuantity = 1;
        item.receiveds = [
          {
            'lotNumber' : (item.lotNumber)? item.lotNumber : "" ,
            'lotDate' : (item.lotDate)? item.lotDate : "",
            'receivedUnitQuantity' : 1,
          }
        ];

        let checked = false;
        if(Array.isArray(values.receivedItems))
        {
            values.receivedItems.forEach((v, idx) => {
            if( 
              v.orderItemId === item.orderItemId
            )
            {
              let lotCheck = false;
              v.receiveds.forEach((received) => {
                if(
                  received.lotNumber === item.receiveds[0].lotNumber &&
                  received.lotDate === item.receiveds[0].lotDate
                ){
                  received.receivedUnitQuantity++;
                  lotCheck = true;
                }
              });
              if(!lotCheck)
              {
                v.receiveds.push(item.receiveds[0]);
              }
              receivedQuantitySum(idx);
              checked = true;
            }
          });
        }
        if( ! checked ){        
          push(item);
        }
      };

      const isRequired = (idx) => {
        if( fields.value[idx].value.lotManagement == "1" )
        {
          return true;
        }
        return false;
      };
      const openModal = ref();
      const selectOrderItems = ref([]);
      const addItemByBarcode = (items) => {
          selectOrderItems.value = [];
          if (items.item.length === 0) {
              return false;
          }

          if(items.type != "gs1-128" && items.type != "jancode" && items.type != "customLabel")
          {
            Swal.fire({
              icon: 'error',
              title: 'エラー',
              text: 'GS1-128・JANコード・ラベル以外のバーコードは読むことができません',
            });
          }
          selectOrderItems.value = items.item;
          openModal
              .value
              .open();
      };
      return {
        isChange,
        addReceived,
        deleteReceived,
        receivedQuantitySum,
        openModal,
        selectOrderItems,
        addItemByBarcode,
        loading, 
        isRequired,
        start, 
        complete,
        itemCount,
        isSubmitting,
        receivedPrice,
        totalAmount,
        additem,
        onSubmit,
        breadcrumbs,
        numberFormat,
        meta,
        fields,
        remove,
        validate,
      };
    },
    watch: {
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
      'v-text' : vText,
      'v-barcode-search-for-order-data' : vBarcodeSearchForOrderData,
      'v-switch' : vSwitch,
      'v-loading' : vLoading,
      'item-view': itemView,
      VForm: VeeValidate.Form,
      'v-field': VeeValidate.Field,
      ErrorMessage: VeeValidate.ErrorMessage,
      'v-breadcrumbs': vBreadcrumbs,
      'v-input': vInput,
      'v-select': vSelect,
      'v-select-division': vSelectDivision,
      'v-button-default': vButtonDefault,
      'v-button-primary': vButtonPrimary,
      'v-button-danger': vButtonDanger,
      'v-input-number': vInputNumber,
      'v-order-item-modal': vOrderItemModal,
      'header-navi' : headerNavi,
      'blowing' : blowing,
      'v-open-modal' : vOpenModal
    },
}).mount('#top');
</script> 