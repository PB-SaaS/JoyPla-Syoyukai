<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <h1 class="text-2xl mb-2">消費伝票の詳細</h1>
        <hr>
        <div class="p-4 text-base bg-gray-100 border border-gray-400 flex flex-col md:flex-row md:gap-6 gap-4 mb-6">
          <?php if (gate('update_of_consumption_slips')->can() && $viewModel->consumption['consumptionStatus'] != 2 && 
          ! ( gate('is_user') && $viewModel->consumption['consumptionStatus'] == 3) ): ?>
          <v-button-primary type="button" class="md:w-1/6 w-full" @click.native="updateSlip( consumption.consumptionId )" >
            消費伝票を更新
          </v-button-primary>
          <?php endif; ?>
          <?php if (gate('cancellation_of_consumption_slips')->can() && $viewModel->consumption['consumptionStatus'] != 2 && 
          ! ( gate('is_user') && $viewModel->consumption['consumptionStatus'] == 3) ): ?>
          <v-button-danger type="button" class="md:w-1/6 w-full" @click.native="deleteSlip( consumption.consumptionId )" >
            消費伝票を削除
          </v-button-danger>
          <?php endif; ?>
          <v-button-default type="button" class="md:w-1/6 w-full" @click.native="openPrint( consumption.consumptionId )">
            消費伝票を印刷
          </v-button-default>
        </div>
        <div class="p-4 text-base bg-gray-100 border border-gray-400">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">消費日</div>
            <div class="flex-auto">{{ consumption.consumptionDate }}</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">消費番号</div>
            <div class="flex-auto">{{ consumption.consumptionId }}</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">消費タイプ</div>
            <div class="flex-auto">
              {{ 
                ( consumption.consumptionStatus === 1) && "通常消費" ||
                ( consumption.consumptionStatus === 2) && "貸出品" ||
                ( consumption.consumptionStatus === 3) && "直納処理"
              }}
            </div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">消費部署</div>
            <div class="flex-auto">{{ consumption.division.divisionName }}</div>
          </div>
        </div>
        <hr>
        <div class="p-4 text-lg font-bold">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">合計金額</div>
            <div class="flex-auto">&yen; {{ numberFormat( consumption.totalAmount) }}</div>
          </div>
        </div>
        <hr>
        <div class="p-4 text-base">
          <div class="lg:flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-full text-lg font-bold">商品情報</div>
            <div class="flex-auto">
              <div class="w-full lg:flex mt-3" v-for="(consumptionItem, idx) in values.consumptionItems">
                <div class="lg:flex-1 flex lg:w-3/4">
                  <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="consumptionItem.itemImage"></item-view>
                  <div class="flex-1 pl-4 lg:flex gap-6 break-all">
                    <div class="flex-auto lg:w-4/5 w-full">
                      <h3 class="text-xl font-bold font-heading">{{ consumptionItem.item.makerName }}</h3>
                      <p class="text-md font-bold font-heading">{{ consumptionItem.item.itemName }}</p>
                      <p class="text-md text-gray-500">{{ consumptionItem.item.itemCode }}</p>
                      <p class="text-md text-gray-500">{{ consumptionItem.item.itemStandard }}</p>
                      <p class="text-md text-gray-500">{{ consumptionItem.item.itemJANCode }}</p>
                      <p class="text-md text-gray-900" v-if="( consumptionItem.lot.lotNumber != '' && consumptionItem.lot.lotDate != '' )">
                      ロット情報：{{ consumptionItem.lot.lotNumber }} / {{ consumptionItem.lot.lotDate }}
                      </p>
                      <?php if (gate('update_of_consumption_slips')->can() && $viewModel->consumption['consumptionStatus'] == 1 ): ?>
                        <v-input-number
                            :rules="{ between:  [0 , consumptionItem.consumptionOriginalQuantity] }" 
                            :name="`consumptionItems[${idx}].consumptionQuantity`" 
                            label="消費数（入数）" 
                            :unit="consumptionItem.quantity.quantityUnit" 
                            :min="0"
                            :step="1"
                            title="消費数（入数）"
                          ></v-input-number>
                      <?php elseif (gate('update_of_consumption_slips')->can() && $viewModel->consumption['consumptionStatus'] == 3 && ! gate('is_user')): ?>
                        <v-input-number
                            :rules="{ between:  [-99999 , 99999] }" 
                            :name="`consumptionItems[${idx}].consumptionQuantity`" 
                            label="消費数（入数）" 
                            :unit="consumptionItem.quantity.quantityUnit" 
                            :step="1"
                            title="消費数（入数）"
                          ></v-input-number>
                      <?php else: ?>
                        <p class="text-base text-gray-900">
                        {{ numberFormat(consumptionItem.consumptionQuantity) }}{{ consumptionItem.quantity.quantityUnit }}
                        </p>
                      <?php endif; ?>
                      <p>
                        <span class="text-blue-700 text-lg mr-4">&yen; {{ numberFormat(consumptionItem.consumptionPrice) }}</span>
                        <span class="text-sm text-gray-900">( &yen; {{ numberFormat(consumptionItem.unitPrice) }} / {{ consumptionItem.quantity.quantityUnit }} )</span>
                      </p>
                      <?php if (
                        ( gate('cancellation_of_consumption_slips')->can() && $viewModel->consumption['consumptionStatus'] != 2 ) &&
                        ! ( gate('is_user') && $viewModel->consumption['consumptionStatus'] == 3 )
                        ): ?>
                      <div class="mt-3">
                        <v-button-danger @click.native="deleteItem(consumption.consumptionId , consumptionItem.id)">削除</v-button-danger>
                      </div>
                      <?php endif; ?>
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
      'v-loading' : vLoading,
      'header-navi' : headerNavi,
      'v-breadcrumbs': vBreadcrumbs,
      'v-input-number': vInputNumber,
      'item-view' : itemView,
      'v-button-primary' : vButtonPrimary,
      'v-button-default' : vButtonDefault,
      'v-button-danger' : vButtonDanger
    },
    setup(){
      const {ref , onCreated , onMounted} = Vue;
      const { useForm, useFieldArray } = VeeValidate;
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
            text: '消費メニュー',
            disabled: false,
            href: _ROOT+'&path=/consumption',
          },
          {
            text: '消費一覧',
            disabled: false,
            href: _ROOT+'&path=/consumption/index&isCache=true',
          },
          {
            text: '消費伝票の詳細',
            disabled: true,
          }
        ];

      const consumption = PHPData.consumption;

      const { handleSubmit , control, meta , validate , values , isSubmitting  } = useForm({
        initialValues: {
          'consumptionItems' : consumption.consumptionItems.map(item => {
            return {
              ...item,
              consumptionOriginalQuantity: item.consumptionQuantity,
            };
          }),
        },
        validateOnMount : false
      });

      const { remove, push, fields , update , replace } = useFieldArray('consumption' , control);

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };


      const deleteSlip = ( consumptionId ) => 
      {
          Swal.fire({
            title: '消費伝票を削除',
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
              params.append("path", "/api/consumption/"+consumptionId+"/delete");
              params.append("_method", 'delete');
              params.append("_csrf", _CSRF);

              const res = await axios.post(_APIURL,params);
              
              complete();
              if(res.data.code != 200) {
                throw new Error(res.data.message)
              }
              Swal.fire({
                  icon: 'success',
                  title: '消費伝票の削除が完了しました。',
              }).then((result) => {
                location.href = _ROOT+'&path=/consumption/index&isCache=true';
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
      const openPrint = ( url ) => {
        location.href = _ROOT + "&path=/consumption/" + url + "/print";    
      }

      const deleteItem = (consumptionId , id) => {
          Swal.fire({
            title: '消費商品を削除',
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
              params.append("path", "/api/consumption/"+consumptionId+"/item/delete");
              params.append("_method", 'delete');
              params.append("deleteItemId", id);
              params.append("_csrf", _CSRF);

              const res = await axios.post(_APIURL,params);
              
              complete();
              if(res.data.code != 200 && res.data.code != 201) {
                throw new Error(res.data.message)
              }
              if(res.data.code == 201){
                Swal.fire({
                    icon: 'success',
                    title: '消費伝票の削除が完了しました。',
                    text: "商品がすべて削除されたので伝票も削除しました。",
                }).then((result) => {
                  location.href = _ROOT+'&path=/consumption/index&isCache=true';
                });
                return true ;
              } else {
                Swal.fire({
                    icon: 'success',
                    title: '消費商品の削除が完了しました。',
                }).then((result) => {
                  location.reload();
                });
                return true ;
              }
            }
          }).catch((error) => {
            Swal.fire({
              icon: 'error',
              title: 'システムエラー',
              text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
            });
          });
      }

      const updateSlip = async (consumptionId) => {
          const { valid, errors } = await validate();
          if(!valid){
              Swal.fire({
                  icon: 'error',
                  title: '入力エラー',
                  text: '入力エラーがございます。ご確認ください',
              })
              return false;
          } 
          Swal.fire({
            title: '消費伝票を更新',
            text: "消費伝票を更新します。\r\nよろしいですか？",
            icon: 'warning',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
          }).then( async (result) => {
            if(result.isConfirmed){
              start();
              
              let params = new URLSearchParams();
              params.append("path", "/api/consumption/"+consumptionId);
              params.append("_method", 'patch');
              params.append("items", JSON.stringify(encodeURIToObject(values.consumptionItems.map(item => {
                  return {
                    'id': item.id,
                    'quantity': item.consumptionQuantity
                  };
                })
              )));
              params.append("_csrf", _CSRF);

              const res = await axios.post(_APIURL,params);
              
              complete();
              if(res.data.code != 200 && res.data.code != 201) {
                throw new Error(res.data.message)
              }
              Swal.fire({
                  icon: 'success',
                  title: '更新が完了しました。',
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

      return {
        values,
        openPrint,
        deleteSlip,
        numberFormat,
        consumption,
        breadcrumbs,
        loading, 
        start, 
        complete,
        deleteItem,
        updateSlip
      }
  },
  watch: {
  }
}).mount('#top');
</script> 