<div id="top" v-cloak>
  <header-navi></header-navi>
  <v-loading :show="loading"></v-loading>
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
            title="消費部署指定" />
          </div>
          <div class="my-4 grid grid-cols-3 gap-4 lg:w-1/3">
            <v-button-default type="button" data-micromodal-trigger="inHospitalItemModal">商品検索</v-button-default>
            <v-inhospitalitem-modal v-on:additem="additem">
            </v-inhospitalitem-modal>
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
            <div class="my-2" v-for="(item, idx) in fields" :key="item.key">
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
                      :rules="{ between: [0 , 99999] }" 
                      :name="`consumeItems[${idx}].consumeUnitQuantity`"
                      label="消費数（個数）" 
                      :min="0" 
                      :unit="item.value.itemUnit" 
                      :step="1" 
                      :title="`消費数（個数）/${item.value.quantity}${ item.value.quantityUnit }入り`" 
                      ></v-input-number>
                    </div>
                    <div class="lg:w-1/2">
                      <v-input-number 
                      :rules="{ between: [0 , 99999] }" 
                      :name="`consumeItems[${idx}].consumeQuantity`" 
                      label="消費数（入数）" 
                      :min="0" 
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
  <v-confirm id="modal-confirm" ref="confirm" :headtext="confirmSetting.headtext.value" :message="confirmSetting.message.value" @ok="confirmSetting.okMethod.value" @cancel="confirmSetting.cancelMethod.value" ></v-confirm>
  <v-alert id="modal-alert" ref="alert" :headtext="alertSetting.headtext.value" :message="alertSetting.message.value" @ok="alertSetting.okMethod.value" ></v-alert>
  
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
          consumeItems: [],
          divisionId: "",
          consumeDate: yyyy+'-'+mm+'-'+dd,
          
        },
        validateOnMount : false
      });
      const { remove, push, fields , update , replace } = useFieldArray('consumeItems' , control);
    
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
            href: '%url/rel:mpgt:Root%&path=/consumption',
          },
          {
            text: '個別消費',
            disabled: true, 
          }];

      const createConsumptionModel = ( values ) => {
        let items = values.consumeItems;
        let consumeItems = [];
        items.forEach(function(item, idx){
          if( consumeQuantity(idx) > 0 ){ consumeItems.push({
            'inHospitalItemId': item.inHospitalItemId,
            'consumeLotDate': item.consumeLotDate,
            'consumeLotNumber': item.consumeLotNumber,
            'consumeQuantity': item.consumeQuantity,
            'consumeUnitQuantity': item.consumeUnitQuantity,
            'divisionId' : values.divisionId,
            'lotManagement' : false,
          })}
        });
        return consumeItems;
      };

      const consumeQuantity = (idx) => {
        let num = 0;
        num += values.consumeItems[idx].consumeQuantity;
        num += values.consumeItems[idx].quantity * values.consumeItems[idx].consumeUnitQuantity;
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
          return value.toString().replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,' );
      }

      const isRequired = (idx) => {
        console.log(fields);
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
          Swal.fire({
            title: '確認',
            text: "消費登録を行います。よろしいですか？",
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

      const consumptionRegister = handleSubmit(async (values) => {
        try {
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
            
            let params = new URLSearchParams();
            params.append("path", "/api/consumption/register");
            params.append("_csrf", _CSRF);
            params.append("consumptionDate", values.consumeDate);
            params.append("consumptionItems", JSON.stringify(encodeURIToObject(consumptionModels)));

            const res = await axios.post(_APIURL,params);
            
            if(res.data.code != 200) {
              throw new Error(res.data.message)
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

      const additem = (item) =>
      {
        item = JSON.parse(JSON.stringify(item));
        item.consumeQuantity = 0;
        item.consumeUnitQuantity = 0;
        item.consumeLotNumber = ( item.lotNumber ) ? item.lotNumber : "" ;
        item.consumeLotDate =  ( item.lotDate ) ? item.lotDate : "" ;
        push(item);
      };

      const addItemByBarcode = (items) => 
      {
        console.log(items);
      }
      


      return {
        loading, 
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
      'v-inhospitalitem-modal': vInHospitalItemModal,
      'header-navi' : headerNavi
    },
}).mount('#top');
</script> 