<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <h1 class="text-2xl mb-2">発注伝票の詳細</h1>
        <hr>
        <div class="p-4 text-base bg-gray-100 border border-gray-400 flex flex-col md:flex-row md:gap-6 gap-4 mb-6">
          <v-button-primary type="button" class="md:w-1/6 w-full" :disabled="! isChange" @click.native="onUpdate">内容を訂正</v-button-primary>
          <v-button-primary type="button" class="md:w-1/6 w-full" @click.native="openReceipt">入荷照合を開く</v-button-primary>
          <v-button-default type="button" class="md:w-1/6 w-full" @click.native="openLabel">発注商品ラベル発行</v-button-default>
          <v-button-default type="button" class="md:w-1/6 w-full inline-flex items-center relative" :disabled="order.sent.disabled" @click.native="sentSlip()">
            <template v-if="order.sentFlag && ! order.sent.disabled">
              <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-3 h-5 w-5 stroke-sushi-700 absolute" viewBox="0 96 960 960" width="48"><path d="M633 976 472 815l43-43 118 118 244-244 43 43-287 287ZM478 529l334-213H144l334 213Zm0 60L140 372v452h256l60 60H140q-24 0-42-18t-18-42V316q0-24 18-42t42-18h677q24 0 42 18t18 42v244l-60 60V372L478 589Zm1 9Zm-1-69Zm1 60Z"/></svg>
              <p class="w-full">発注対応：完了</p>
            </template>
            <template v-if="! order.sentFlag && ! order.sent.disabled">
              <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-3 h-5 w-5 stroke-sushi-700 absolute" viewBox="0 96 960 960" width="48"><path d="M140 896q-24 0-42-18t-18-42V316q0-24 18-42t42-18h680q24 0 42 18t18 42v520q0 24-18 42t-42 18H140Zm340-302L140 371v465h680V371L480 594Zm0-60 336-218H145l335 218ZM140 371v-55 520-465Z"/></svg>
              <p class="w-full">発注対応：未完了<br>（クリックで完了します）</p>
            </template>
            <template v-if="order.sent.disabled">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 stroke-sushi-700 absolute" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="w-full">お待ちください...</p>
            </template>
          </v-button-default>
          <v-button-default type="button" class="md:w-1/6 w-full" @click.native="openMedicalLabel">医事ラベル発行</v-button-default>
        </div>
        <div class="p-4 text-base bg-gray-100 border border-gray-400">
          <v-text title="登録日" class="flex w-full gap-6">{{ order.registDate }}</v-text>
          <v-text title="発注番号" class="flex w-full gap-6">{{ order.orderId }}</v-text>
          <v-text title="発注ステータス" class="flex w-full gap-6">
            <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 2">
              {{ order.orderStatusToString}} 
            </span>
            <span class="bg-amber-100 text-amber-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 3">
              {{ order.orderStatusToString}} 
            </span>
            <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 4">
              {{ order.orderStatusToString}} 
            </span>
            <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 5">
              {{ order.orderStatusToString}} 
            </span>
            <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 6">
              {{ order.orderStatusToString}} 
            </span>
            <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 7">
              {{ order.orderStatusToString}} 
            </span>
            <span class="bg-zinc-100 text-zinc-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 8">
              {{ order.orderStatusToString}} 
            </span>
          </v-text>
          <v-text title="発注元部署" class="flex w-full gap-6">{{ order.division.divisionName }}</v-text>
          <v-text title="入荷先部署" class="flex w-full gap-6">{{ order.receivedDivisionName }}</v-text>
          <v-text title="発注担当者" class="flex w-full gap-6">{{ order.orderUserName }}</v-text>
          <v-text title="卸業者" class="lg:flex w-full gap-6">{{ order.distributor.distributorName }}</v-text>
          <v-text title="発注方法" class="lg:flex w-full gap-6">{{ order.distributor.orderMethod }}</v-text>
          <v-text title="発注タイプ" class="flex w-full gap-6">{{ order.adjustmentToString }}</v-text>
          <fieldset class="md:flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal" v-if="title != ''">
            備考
            </div>
            <div class="flex-auto lg:w-5/6 ">
              <p class=" whitespace-pre-wrap">{{ order.orderComment }}</p>
            </div>
          </fieldset>
          <fieldset class="md:flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal" v-if="title != ''">
              卸業者備考
            </div>
            <div class="flex-auto lg:w-5/6 ">
              <p class=" whitespace-pre-wrap">{{ order.distributorComment }}</p>
            </div>
          </fieldset>
        </div>
        <hr>
        <div class="p-4 text-lg font-bold">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">合計金額</div>
            <div class="flex-auto">&yen; {{ numberFormat( order.totalAmount) }}</div>
          </div>
        </div>
        <hr>
        <div class="p-4 text-base">
          <div class="lg:flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-full text-lg font-bold">商品情報</div>
            <div class="flex-auto">
              <div class="w-full mt-3" v-for="(item, idx) in fields" :key="item.key">
                <div class="lg:flex ">
                  <div class="lg:flex-1 flex lg:w-3/4">
                    <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="item.value.itemImage"></item-view>
                    <div class="flex-1 pl-4 lg:flex gap-6 break-all">
                      <div class="flex-auto lg:w-3/5 w-full">
                        <h3 class="text-xl font-bold font-heading">{{ item.value.item.makerName }}</h3>
                        <p class="text-md font-bold font-heading">{{ item.value.item.itemName }}</p>
                        <p class="text-md text-gray-500">{{ item.value.item.itemCode }}</p>
                        <p class="text-md text-gray-500">{{ item.value.item.itemStandard }}</p>
                        <p class="text-md text-gray-500">{{ item.value.item.itemJANCode }}</p>
                        <div>
                            <span class="text-blue-700 text-lg mr-4" v-if="item.value.dueDate != ''">納期：{{ item.value.dueDate }}</span>
                            <span class="text-orange-700 text-lg mr-4" v-else>納期：未定</span>
                        </div>
                        <div>
                          <span class="text-blue-700 text-lg mr-4">&yen; {{ numberFormat(item.value.orderPrice) }}</span>
                          <span class="text-sm text-gray-900">( &yen; {{ numberFormat(item.value.price) }} / {{ item.value.quantity.itemUnit }} )</span>
                          <blowing :message="item.value.priceNotice" title="金額管理備考" v-if="item.value.priceNotice != ''"></blowing>
                        </div>
                      </div>
                      <div class="flex-auto lg:w-2/5 w-full">
                          <div class="md:flex gap-6 ">
                              <div class="font-bold w-32">発注数</div>
                              <div>{{ numberFormat(item.value.orderQuantity) }} {{ item.value.quantity.itemUnit }}</div>
                          </div>
                          <div class="md:flex gap-6 ">
                              <div class="font-bold w-32">現在入庫数</div>
                              <div>{{ numberFormat(item.value.receivedQuantity) }} {{ item.value.quantity.itemUnit }}</div>
                          </div>
                          <div class="md:flex gap-6 ">
                              <div class="font-bold w-32">入庫状況</div>
                              <div>
                                <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="item.value.orderItemReceivedStatus == 1">
                                  {{ item.value.orderItemReceivedStatusToString }}
                                </span>
                                <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="item.value.orderItemReceivedStatus == 2">
                                  {{ item.value.orderItemReceivedStatusToString }}
                                </span>
                                <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="item.value.orderItemReceivedStatus == 3">
                                  {{ item.value.orderItemReceivedStatusToString }}
                                </span> 
                              </div>
                          </div>
                          <div class="md:flex gap-6" v-if="item.value.useMedicode">
                              <div class="font-bold w-32">Medicode-Web</div>
                              <div>
                                  <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="item.value.medicodeStatus == 1">
                                    未送信
                                  </span>
                                  <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="item.value.medicodeStatus == 2">
                                    送信済み
                                  </span>
                                  <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="item.value.medicodeStatus == 3">
                                    連携エラー
                                  </span>
                              </div>
                          </div>
                          <template v-if="item.value.orderItemReceivedStatus != 3">
                            <v-input-number
                                :rules="{ required: true , between: ( (item.value.orderQuantity > 0)? [ item.value.receivedQuantity , item.value.orderQuantity ] : [ item.value.orderQuantity  , item.value.receivedQuantity ] ) }" 
                                :name="`orderItems[${idx}].revisedOrderQuantity`"
                                label="【訂正】発注数（個数）" 
                                :unit="item.value.quantity.itemUnit" 
                                :step="1" 
                                :title="`【訂正】発注数（個数）/${item.value.quantity.quantityNum}${ item.value.quantity.quantityUnit }入り`" 
                                @change="isChange = true" 
                                change-class-name="inputChange"
                                ></v-input-number>
                            </template>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <hr>
      </div>
    </div>
  </div>
</div>
<script>

const PHPData = <?php echo json_encode($viewModel, true); ?>;

var JoyPlaApp = Vue.createApp({ 
    components: {
      'v-text' : vText,
      'v-select' : vSelect,
      'v-button-danger' : vButtonDanger,
      'v-button-primary' : vButtonPrimary,
      'v-button-default' : vButtonDefault,
      'v-checkbox': vCheckbox,
      'v-loading' : vLoading,
      'header-navi' : headerNavi,
      'v-breadcrumbs': vBreadcrumbs,
      'item-view' : itemView,
      'v-input-number': vInputNumber
    },
    setup(){
      const {ref , onCreated , onMounted , reactive} = Vue;
      const { useFieldArray , useForm } = VeeValidate;

      const order =  reactive(PHPData.order);
      order.sent = { disabled : false }

      const { handleSubmit , control, meta , validate , values , isSubmitting  } = useForm({
        initialValues: {
          'orderId' : order.orderId,
          'adjustment' : order.adjustment,
          'orderItems' : order.orderItems.map(x => {
            x.orderQuantity = parseInt(x.orderQuantity);
            x.revisedOrderQuantity = parseInt(x.orderQuantity);
            return x;
          })
        },
        validateOnMount : false
      });

      const { remove, push, fields , update , replace } = useFieldArray('orderItems' , control);
    
      console.log(fields);

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
      const breadcrumbs = [
          {
            text: '発注・入荷メニュー',
            disabled: false,
            href: _ROOT + '&path=/order',
          },
          {
            text: '発注一覧',
            disabled: false,
            href: _ROOT + '&path=/order/show&isCache=true',
          },
          {
            text: '発注伝票の詳細',
            disabled: true,
          }
        ];

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };
      const isChange = ref(false);

      const createRevisedOrderQuantityModel = () => {
        return values.orderItems.map(x => {
          return {
            'orderItemId': x.orderItemId,
            'revisedOrderQuantity': x.revisedOrderQuantity,
          };
        });
      }

      const onUpdate = async () =>{
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
            title: '発注伝票の訂正',
            text: "発注伝票の訂正を行います。\r\nよろしいですか？",
            icon: 'warning',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then( async (result) => {
            try {
              if(result.isConfirmed){
                let params = new URLSearchParams();
                const revisedOrderQuantityModel = createRevisedOrderQuantityModel();
                params.append("path", "/api/order/"+values.orderId+"/revised");
                params.append("_method", 'patch');

                params.append("revisedOrderQuantityModel", JSON.stringify(encodeURIToObject(revisedOrderQuantityModel)));
                params.append("_csrf", _CSRF);
                start();
                const res = await axios.post(_APIURL,params);
                complete();
                if(res.data.code != 200) {
                  throw new Error(res.data.message)
                }
                 
                Swal.fire({
                    icon: 'success',
                    title: '発注伝票の訂正が完了しました。',
                }).then((result) => {
                    location.reload();
                });
                return true ;
              }
            } catch (error) {
              Swal.fire({
                icon: 'error',
                title: 'システムエラー',
                text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
              });
            }
          })
        }
      };

      const openReceipt = () => {
        location.href = _ROOT + "&path=/received/order/" + order.orderId;
      }

      const openLabel = () => {
        const url = _ROOT + "&path=/label/order/" + order.orderId;
        window.open(url, '_blank');
      }

      const openMedicalLabel = () => {
        const url = _ROOT +  "&path=/label/medicalOrder/" + order.orderId;  
        window.open(url, '_blank');
      }

      const sentSlip = async() => 
      {
          if(order.sent.disabled){
            return '';
          }
          if(order.sentFlag){
            return '';
          }

          order.sent.disabled = true;

          let params = new URLSearchParams();
          params.append("path", "/api/order/"+order.orderId+"/sent");
          params.append("_method", 'post');
          params.append("_csrf", _CSRF);

          const res = await axios.post(_APIURL,params);
          
          if(res.data.code != 200) {
            order.sent.disabled = false;
          }

          order.sent.disabled = false;
          order.sentFlag = true;
      }
      
      return {
        sentSlip,
        onUpdate,
        isChange,
        numberFormat,
        order,
        fields,
        breadcrumbs,
        loading, 
        start, 
        complete,
        openReceipt,
        openLabel,
        openMedicalLabel,
      }
  },
  watch: {
  }
}).mount('#top');
</script> 