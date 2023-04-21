<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <h1 class="text-2xl mb-2">未発注伝票の詳細</h1>
        <hr>
        <div class="p-4 text-base bg-gray-100 border border-gray-400 flex flex-col md:flex-row md:gap-6 gap-4 mb-6">
          <?php if (gate('revision_of_unordered_slips')->can()): ?>
          <v-button-primary type="button" :disabled="! isChange" class="md:w-1/6 w-full" @click.native="onUpdate">内容を更新</v-button-primary>
          <?php endif; ?>
          <?php if (gate('decision_of_order_slips')->can()): ?>
          <v-button-primary type="button" class="md:w-1/6 w-full" @click.native="approvalSlip( order.orderId )">
            承認
          </v-button-primary>
          <?php endif; ?>
          <?php if (gate('deletion_of_unordered_slips')->can()): ?>
          <v-button-danger type="button" class="md:w-1/6 w-full" @click.native="deleteSlip( order.orderId )">
            削除
          </v-button-danger>
          <?php endif; ?>
        </div>
        <div class="p-4 text-base bg-gray-100 border border-gray-400">
          <v-text title="登録日" class="lg:flex w-full gap-6">{{ order.registDate }}</v-text>
          <v-text title="発注番号" class="lg:flex w-full gap-6">{{ order.orderId }}</v-text>
          <v-text title="発注ステータス" class="lg:flex w-full gap-6">
            <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded">
              {{ order.orderStatusToString}} 
            </span>
          </v-text>
          <v-text title="発注元部署" class="lg:flex w-full gap-6">{{ order.division.divisionName }}</v-text>
          <v-text title="発注担当者" class="lg:flex w-full gap-6">{{ order.orderUserName }}</v-text>
          <v-text title="卸業者" class="lg:flex w-full gap-6">{{ order.distributor.distributorName }}</v-text>
          <?php if (gate('revision_of_unordered_slips')->can()): ?>
          <v-select class="lg:flex w-full gap-6" @change="isChange = true" change-class-name="inputChange" :options="[{ label: '定数発注', value: 1 },{ label: '個別発注', value: 2 }]" name="adjustment" :rules="{required: true}" title="発注タイプ" label="発注タイプ"></v-select>
          <v-textarea title="備考" @change="isChange = true" change-class-name="inputChange" name="comment" :rules="{strlen: 512}" label="備考" class="lg:flex w-full gap-6 mt-4">
          </v-textarea>
          <?php else: ?>
          <v-text title="発注タイプ" class="flex w-full gap-6">{{ order.adjustmentToString }}</v-text>
          <v-text title="備考" class="flex w-full gap-6"><p class=" whitespace-pre-wrap">{{ order.orderComment }}</p></v-text>
          <?php endif; ?>
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
                      <div class="flex-auto lg:w-4/5 w-full">
                        <h3 class="text-xl font-bold font-heading">{{ item.value.item.makerName }}</h3>
                        <p class="text-md font-bold font-heading">{{ item.value.item.itemName }}</p>
                        <p class="text-md text-gray-500">{{ item.value.item.itemCode }}</p>
                        <p class="text-md text-gray-500">{{ item.value.item.itemStandard }}</p>
                        <p class="text-md text-gray-500">{{ item.value.item.itemJANCode }}</p>
                        <p class="text-md text-gray-500">在庫数: {{ numberFormat(item.value.stockCount ) }}{{item.value.quantity.quantityUnit}}</p>
                        <?php if (
                            gate('revision_of_unordered_slips')->can()
                        ): ?>
                        <p class="text-base text-gray-900 lg:w-1/2">
                          <v-input-number
                            :rules="{ required: true , between: ( (item.value.rowOrderQuantity > 0)? [ 1 , 99999 ] : [ -99999 , -1 ] ) }" 
                            :name="`orderItems[${idx}].orderQuantity`"
                            label="発注数（個数）" 
                            :unit="item.value.quantity.itemUnit" 
                            :step="1" 
                            :title="`発注数（個数）/${numberFormat(item.value.quantity.quantityNum)}${ item.value.quantity.quantityUnit }入り`" 
                            @change="isChange = true" 
                            change-class-name="inputChange"
                            ></v-input-number>
                        </p>
                        <?php else: ?>
                        <div class="md:flex gap-6 ">
                            <div class="font-bold w-32">発注数</div>
                            <div>{{ numberFormat(item.value.orderQuantity) }} {{ item.value.quantity.itemUnit }}</div>
                        </div>
                        <?php endif; ?>
                        <div>
                          <span class="text-blue-700 text-lg mr-4">&yen; {{ numberFormat(item.value.orderPrice) }}</span>
                          <span class="text-sm text-gray-900">( &yen; {{ numberFormat(item.value.price) }} / {{ item.value.quantity.itemUnit }} )</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php if (gate('revision_of_unordered_slips')->can()): ?>
                <div class="py-4">
                  <v-button-danger  class="w-full mx-auto lg:w-auto" type="button" @click.native="itemDelete(idx)">削除</v-button-danger>
                </div>
                <?php endif; ?>
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
      'v-checkbox': vCheckbox,
      'v-loading' : vLoading,
      'header-navi' : headerNavi,
      'v-breadcrumbs': vBreadcrumbs,
      'item-view' : itemView,
      'v-input-number': vInputNumber,
      'v-textarea' : vTextarea
    },
    setup(){
      const {ref , onCreated , onMounted} = Vue;
      const { useFieldArray , useForm } = VeeValidate;


      const order = PHPData.order;

      const { handleSubmit , control, meta , validate , values , isSubmitting  } = useForm({
        initialValues: {
          'orderId' : order.orderId,
          'adjustment' : order.adjustment,
          'comment': order.orderComment,
          'orderItems' : order.orderItems.map(x => {
            x.rowOrderQuantity = parseInt(x.orderQuantity);
            x.orderQuantity = parseInt(x.orderQuantity);
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
            text: '未発注一覧',
            disabled: false,
            href: _ROOT + '&path=/order/unapproved/show&isCache=true',
          },
          {
            text: '未発注伝票の詳細',
            disabled: true,
          }
        ];

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };
      const isChange = ref(false);

      const createUpdateModel = () => {
        return values.orderItems.map(x => {
          return {
            'orderItemId': x.orderItemId,
            'orderQuantity': x.orderQuantity,
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
            title: '未発注伝票の更新',
            text: "未発注伝票の更新を行います。\r\nよろしいですか？",
            icon: 'warning',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then( async (result) => {
              if(result.isConfirmed){
                start();
                let params = new URLSearchParams();
                const updateModel = createUpdateModel();
                params.append("path", "/api/order/unapproved/"+values.orderId+"/update");
                params.append("adjustment", values.adjustment);
                params.append("comment", encodeURI(values.comment));
                params.append("updateModel", JSON.stringify(encodeURIToObject(updateModel)));
                params.append("_method", 'patch');
                params.append("_csrf", _CSRF);

                const res = await axios.post(_APIURL,params);
                complete();
                if(res.data.code != 200) {
                  throw new Error(res.data.message)
                }
                 
                Swal.fire({
                    icon: 'success',
                    title: '未発注伝票の更新が完了しました。',
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
          };
      };

      const itemDelete = (idx) => {
        Swal.fire({
          title: '商品削除の確認',
          text: "商品を削除します。よろしいですか？",
          icon: 'info',
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'OK'
        }).then( async (result) => {
          if(result.isConfirmed){
            start();
            let params = new URLSearchParams();
            params.append("path", "/api/order/unapproved/"+values.orderId+"/"+values.orderItems[idx].orderItemId+"/delete");
            params.append("_method", 'delete');
            params.append("_csrf", _CSRF);

            const res = await axios.post(_APIURL,params);
            complete();
            if(res.data.code != 200) {
              throw new Error(res.data.message)
            }
            
            let addComment = "";
            if(res.data.data.isOrderDeleted)
            {
              addComment = "\r\n商品情報がなくなりましたので伝票も削除しました。";
            }

            Swal.fire({
                icon: 'success',
                title: '商品の削除が完了しました。',
                text: addComment,
            }).then((result) => {
              if(res.data.data.isOrderDeleted)
              {
                location.href = _ROOT + "&path=/order/unapproved/show&isCache=true"
              }
              else
              {
                location.reload();
              }
              
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
        
      const approvalSlip = ( orderId ) => 
      {
        Swal.fire({
          title: '発注書を承認',
          text: "発注書の承認をします。\r\nよろしいですか？",
          icon: 'warning',
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'OK'
        }).then( async (result) => {
          if(result.isConfirmed){
            start();
            let params = new URLSearchParams();
            params.append("path", "/api/order/unapproved/"+orderId+"/approval");
            params.append("_method", 'patch');
            params.append("_csrf", _CSRF);

            const res = await axios.post(_APIURL,params);
            complete();
            if(res.data.code != 200) {
              throw new Error(res.data.message);
            }
            
            Swal.fire({
                icon: 'success',
                title: '発注書を承認が完了しました。',
            }).then((result) => {
                location.href = _ROOT + "&path=/order/unapproved/show&isCache=true"
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

      const deleteSlip = ( orderId ) => 
      {
        Swal.fire({
          title: '発注書を削除',
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
            params.append("path", "/api/order/unapproved/"+orderId+"/delete");
            params.append("_method", 'delete');
            params.append("_csrf", _CSRF);

            const res = await axios.post(_APIURL,params);

            complete();
            if(res.data.code != 200) {
              throw new Error(res.data.message)
            }
            
            Swal.fire({
                icon: 'success',
                title: '発注書の削除が完了しました。',
            }).then((result) => {
                location.href = _ROOT + "&path=/order/unapproved/show&isCache=true"
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
      
      return {
        approvalSlip,
        deleteSlip,
        onUpdate,
        itemDelete,
        isChange,
        numberFormat,
        order,
        fields,
        breadcrumbs,
        loading, 
        start, 
        complete
      }
  },
  watch: {
  }
}).mount('#top');
</script> 