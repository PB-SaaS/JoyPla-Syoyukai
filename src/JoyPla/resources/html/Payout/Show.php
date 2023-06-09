<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index mx-auto mb-96">
        <h1 class="text-2xl mb-2">払出伝票</h1>
        <hr>
        <div class="p-4 text-base bg-gray-100 border border-gray-400 flex flex-col md:flex-row md:gap-6 gap-4 mb-6">
          <?php if( !gate('is_approver')) : ?>
            <v-button-primary class="md:w-1/6 w-full" @click.native="onPayoutUpdate()" v-if="!isUser || (isUser && userDivisionId === values.payout.sourceDivisionId )">
                更新
            </v-button-primary>
          <?php endif; ?>
          <?php if( !gate('is_approver')) : ?>
            <v-button-danger class="md:w-1/6 w-full" @click.native="onDelete()" v-if="!isUser || (isUser && userDivisionId === values.payout.sourceDivisionId )">
                削除
            </v-button-danger>
          <?php endif; ?>
            <v-button-default class="md:w-1/6 w-full" @click.native="openLabelPage()">
                払出ラベル発行
            </v-button-default>
            <v-button-default class="md:w-1/6 w-full" @click.native="openPrintPage()">
                払出伝票印刷
            </v-button-default>
        </div>
        <div class="p-4 text-base bg-gray-100 border border-gray-400">
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">払出日</div>
                <div class="flex-auto">{{ values.payout.payoutDate }}</div>
            </div>
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">払出番号</div>
                <div class="flex-auto">{{ values.payout.payoutHistoryId }}</div>
            </div>
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">払出元部署</div>
                <div class="flex-auto">{{ values.payout?._sourceDivision?.divisionName }}</div>
            </div>
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">払出先部署</div>
                <div class="flex-auto">{{ values.payout?._targetDivision?.divisionName }}</div>
            </div>
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">合計金額</div>
                <div class="flex-auto">&yen;{{ numberFormat( totalPrice(values.payout._inHospitalItems) ) }}
                </div>
            </div>
        </div>
        <hr>
        <div>
          <table class="table-auto w-full text-sm" id="payoutTable">
              <thead>
                <tr>
                  <th class="border-b font-medium p-4 pr-8 text-left border">No</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">商品情報</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">ロット番号</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">使用期限</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">カード番号</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">払出数</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">合計金額</th>
                </tr>
              </thead>
              <tbody>
                  <template  v-for="(elem , index ) in values.payout._inHospitalItems">
                      <template v-for="(payoutItem , payoutIndex ) in elem._payoutItems">
                          <tr>
                              <td class="text-left px-3 py-4 border" v-if="payoutIndex === 0" :rowspan="elem._payoutItems.length">{{ index + 1 }}</td>
                              <td class="text-left px-3 py-4 border" v-if="payoutIndex === 0" :rowspan="elem._payoutItems.length">
                                <p class="text-gray-500" v-if="elem.makerName">{{ elem.makerName }}</h3>
                                <p class="text-gray-500" v-if="elem.itemName">{{ elem.itemName }}</p>
                                <p class="text-gray-500" v-if="elem.itemCode">{{ elem.itemCode }}</p>
                                <p class="text-gray-500" v-if="elem.itemStandard">{{ elem.itemStandard }}</p>
                                <p class="text-gray-500" v-if="elem.itemJANCode">{{ elem.itemJANCode }}</p>
                                <p class="text-gray-500">&yen;{{ numberFormat(payoutItem.unitPrice) }} / {{ payoutItem.quantityUnit }}</p>
                              </td>
                              <td class="text-left px-3 py-4 border">{{ payoutItem.lotNumber }}</td>
                              <td class="text-left px-3 py-4 border">{{ payoutItem.lotDate }}</td>
                              <td class="text-left px-3 py-4 border">{{ payoutItem.cardId }}</td>
                              <td class="text-left px-3 py-4 border">
                                <?php if( !gate('is_approver')) : ?>
                                <template v-if="!isUser || (isUser && userDivisionId === values.payout.sourceDivisionId )">
                                  <v-input-number
                                    :rules="{ between: [0 , Math.max(0 , payoutItem.payoutQuantity) ] }" 
                                    :name="`payout._inHospitalItems[${index}]._payoutItems[${payoutIndex}].payoutChangeQuantity`" 
                                    label="払出数" 
                                    :min="0" 
                                    :unit="payoutItem.quantityUnit" 
                                    :step="1" 
                                    title="払出数" 
                                    :disabled="payoutItem.cardId != ''"
                                    class=" w-[240px]" >
                                  </v-input-number>
                                </template>
                                <template v-if="!( !isUser || (isUser && userDivisionId === values.payout.sourceDivisionId ) )">
                                  {{ numberFormat(payoutItem.payoutQuantity) }}{{payoutItem.quantityUnit}}
                                </template>
                                <?php else: ?>
                                  {{ numberFormat(payoutItem.payoutQuantity) }}{{payoutItem.quantityUnit}}
                                <?php endif; ?>
                              </td>
                              <td class="text-left px-3 py-4 border" v-if="payoutIndex === 0" :rowspan="elem._payoutItems.length">&yen;{{ numberFormat(priceCalc(elem)) }}</td>
                            </tr>
                      </template>
                  </template>
              </tbody>
            </table>
          </div>
        </div>
        <hr>
        <div class="p-4 text-base">
          
        </div>
        <hr>
      </div>
    </div>
  </div>
</div>
<script>

const payoutHistoryId = '<?php echo $payoutHistoryId; ?>';
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
var JoyPlaApp = Vue.createApp({ 
    components: {
      'v-text' : vText,
      'v-select' : vSelect,
      'v-barcode-search': vBarcodeSearch,
      'v-button-danger' : vButtonDanger,
      'v-button-primary' : vButtonPrimary,
      'v-button-default' : vButtonDefault,
      'v-checkbox': vCheckbox,
      'v-loading' : vLoading,
      'header-navi' : headerNavi,
      'v-breadcrumbs': vBreadcrumbs,
      'item-view' : itemView,
      'v-input-number': vInputNumber,
      'v-input': vInput,
      'v-pulldown-button' : vPullDownButton,
    },
    setup(){
      const isUnitPrice = <?php echo ($isUnitPrice) ? 'true' : 'false'; ?>

      const {ref , onCreated , onMounted , reactive} = Vue;
      const { useFieldArray , useForm } = VeeValidate;

      const date = new Date();
      const yyyy = date.getFullYear();
      const mm = ("0" + (date.getMonth() + 1)).slice(-2);
      const dd = ("0" + date.getDate()).slice(-2);

      const { handleSubmit , control, meta , validate , values , isSubmitting  } = useForm({
        initialValues: {
            payoutDate:  yyyy + '-' + mm + '-' + dd,
            payout : {},
        },
        validateOnMount : false
      });

      const loading = ref(false);
      const setLoading = (value) => {
        loading.value = value;
      };

      const startLoading = () => {
        setLoading(true);
      };

      const completeLoading = () => {
        setLoading(false);
      };
      
      const sleepCompleteLoading = async () => {
        await sleep(500);
        completeLoading();
      };
      const pulldownSelect = ref([]);
      onMounted(async () => {
        await sleepCompleteLoading();
        await fetchData();
      });
      
      const isUser = <?php echo (gate('is_user'))? 'true' : 'false' ?>;
      const userDivisionId = '<?php echo $userDivisionId ?>';
      
      const breadcrumbs = [
          {
            text: '払出メニュー',
            disabled: false,
            href: _ROOT + '&path=/payout',
          },
          {
            text: '払出一覧',
            disabled: false,
            href: _ROOT + '&path=/payout/index&isCache=true',
          },
          {
            text: '払出伝票',
            disabled: true,
          }
        ];
    
        
      const getSlipData = async ( payoutHistoryId ) => 
      {
        startLoading();
        
        let params = new URLSearchParams();
        params.append("path", "/api/payout/"+payoutHistoryId);
        params.append("_method", 'get');
        params.append("_csrf", _CSRF);

        const res = await axios.post(_APIURL,params);
        
        completeLoading();

        if(res.data.code != 200) {
          throw new Error(res.data.message)
        }
        return res.data ;
      }

      const convertpayout = (data) => {
        let payout = data;
        payout._inHospitalItems = Object.values(payout._inHospitalItems);
        payout._inHospitalItems.forEach((item , index) => {
            payout._inHospitalItems[index]._payoutItems = [];
            Object.values(payout._items).forEach((item2, index2) => {
                if(item2.inHospitalItemId == item.inHospitalItemId){
                    if(isUnitPrice){
                      item2.unitPrice = (item2.price == 0 || item2.quantity == 0 )? 0 : (item2.price / item2.quantity);
                    }
                    item2.payoutChangeQuantity = item2.payoutQuantity;
                    payout._inHospitalItems[index]._payoutItems.push(
                        item2
                    );
                }
            })
        });

        return payout;
      }

      const fetchData = async () => {
        startLoading();
        const response = await getSlipData(payoutHistoryId);
        values.payout = convertpayout(response.data);
        completeLoading();
      };

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };
      

      const removeItem = (idx,pIdx, payoutHistoryIdx) => {
        values.payout._inHospitalItems[idx]._payoutItems[pIdx]._payouts.splice( payoutHistoryIdx, 1 );
      }

      const updatePayout = (updateItems) => {
        let params = new URLSearchParams();
        params.append("path", "/api/payout/"+payoutHistoryId);
        params.append("_method", 'patch');
        params.append("_csrf", _CSRF);
        params.append("updateItems", JSON.stringify(encodeURIToObject(updateItems)));
        return axios.post(_APIURL,params);
      }
      
      const deletePayout = () => {
        let params = new URLSearchParams();
        params.append("path", "/api/payout/"+payoutHistoryId);
        params.append("_method", 'delete');
        params.append("_csrf", _CSRF);
        return axios.post(_APIURL,params);
      }

      const createPayoutUpdateModel = (values) => {
        let model = [];
        values.payout._inHospitalItems.forEach((item) => {
          item._payoutItems.forEach((payoutItem) => {
            if(payoutItem.payoutChangeQuantity != payoutItem.payoutQuantity)
            {
              model.push({
                'payoutItemId': payoutItem.payoutId,
                'payoutQuantity': payoutItem.payoutChangeQuantity,
              })
            }
          });
        })
        return model;
      }

      const slipUpdate = handleSubmit(async (values) => {
        const updateModels = createPayoutUpdateModel(values);
        const res = await updatePayout(updateModels);
        if(res.data.code != 200 && res.data.code != 201) {
          throw new Error(res.data.message)
        }
        return res.data
      });

      const slipDelete = handleSubmit(async (values) => {
        const res = await deletePayout();
        if(res.data.code != 200) {
          throw new Error(res.data.message)
        }
      });

      const onPayoutUpdate = async () => {
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
              return false;
          } else {
            Swal.fire({
                title: '更新',
                text: "更新を行います。\r\nよろしいですか？",
                icon: 'warning',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            }).then(async (result) => {
                if (result.isConfirmed) {
                  startLoading();
                  slipUpdate().then(async data => {

                    completeLoading();
                    if(data.code == '200'){
                      await Swal.fire({
                        icon: 'success',
                        title: '更新が完了しました',
                      }).then((result) => {
                          location.reload();
                      });
                    } else {
                      await Swal.fire({
                        icon: 'success',
                        title: '更新が完了しました',
                        text: '商品情報がなくなったため、伝票も削除されました'
                      }).then((result) => {
                          location.href = _ROOT + '&path=/payout/index&isCache=true';
                      });
                    }
                  })
                }
            }).catch((error) => {
              completeLoading();
              Swal.fire({
                icon: 'error',
                title: 'システムエラー',
                text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
              });
            })
        }
      }
      
      const onDelete = async () => {
          Swal.fire({
              title: '削除',
              text: "削除を行います。\r\nよろしいですか？",
              icon: 'warning',
              showCancelButton: true,
              reverseButtons: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'OK'
          }).then(async (result) => {
              if (result.isConfirmed) {
                  startLoading();
                await slipDelete();
                  completeLoading();
                await Swal.fire({
                  icon: 'success',
                  title: '削除が完了しました',
                }).then((result) => {
                    location.href = _ROOT + '&path=/payout/index&isCache=true';
                });
              }
          }).catch((error) => {
              completeLoading();
            Swal.fire({
              icon: 'error',
              title: 'システムエラー',
              text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
            });
          })
      }

      const openLabelPage = () => {
          const url = _ROOT + '&path=/label/payout/' + payoutHistoryId;
          window.open(url, '_blank')
      }      
      
      const openPrintPage = () => {
          const url = _ROOT + '&path=/payout/' + payoutHistoryId + '/print';
          window.open(url, '_blank')
      }

      const payoutTotalCount = (inHospitalItem) => {
        let count = 0;
        inHospitalItem._payoutItems.forEach((elem) => {
          count += parseInt(elem.payoutChangeQuantity);
        })
        return count;
      }

      const priceCalc = (inHospitalItem) => {
        let price = 0;
        inHospitalItem._payoutItems.forEach((elem) => {
          price += (elem.unitPrice != '')? elem.unitPrice * parseInt(elem.payoutChangeQuantity) : 0;
        })
        return price;
      }

      const totalPrice = (payout) => {
        let price = 0;
        payout?.forEach((elem) => {
          price += priceCalc(elem);
        })

        return price;
      }

      return {
        openLabelPage,
        removeItem,
        alert,
        numberFormat,
        breadcrumbs,
        loading, 
        values,
        pulldownSelect,
        priceCalc,
        totalPrice,
        onPayoutUpdate,
        onDelete,
        openPrintPage,
        userDivisionId,
        isUser,
      }
  },
  watch: {
  }
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