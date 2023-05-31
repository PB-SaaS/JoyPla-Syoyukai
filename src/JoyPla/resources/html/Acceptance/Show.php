<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index mx-auto mb-96">
        <h1 class="text-2xl mb-2">出庫伝票</h1>
        <hr>
        <div class="mb-2 lg:w-1/3">
            <v-input type="date" name="payoutDate" :rules="{}" title="払出日指定" label="払出日指定"></v-input>
        </div>
        <div class="p-4 text-base bg-gray-100 border border-gray-400 flex flex-col md:flex-row md:gap-6 gap-4 mb-6">
            <v-pulldown-button class="md:w-1/6 w-full" variant="primary" :selects="pulldownSelect" v-if="pulldownSelect.length !== 0">
                入庫アクション選択
            </v-pulldown-button>
            <v-button-default @click.native="openLabelPage" class="md:w-1/6 w-full">
                払出ラベル発行
            </v-button-default>
            <v-button-default class="md:w-1/6 w-full">
                出庫伝票印刷
            </v-button-default>
        </div>
        <div class="p-4 text-base bg-gray-100 border border-gray-400">
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">出庫日</div>
                <div class="flex-auto">{{ values.acceptance.acceptanceDate }}</div>
            </div>
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">出庫番号</div>
                <div class="flex-auto">{{ values.acceptance.acceptanceId }}</div>
            </div>
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">ステータス</div>
                <div class="flex-auto">
                  <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="values.acceptance.payoutTotalCount == 0 && values.acceptance.acceptanceTotalCount > 0">
                    未入庫
                  </span>
                  <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="values.acceptance.payoutTotalCount > 0 && values.acceptance.payoutTotalCount !== values.acceptance.acceptanceTotalCount">
                    一部未入庫
                  </span>
                  <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="values.acceptance.payoutTotalCount === values.acceptance.acceptanceTotalCount">
                    入庫完了
                  </span>
                </div>
            </div>
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">払出元部署</div>
                <div class="flex-auto">{{ values.acceptance?._sourceDivision?.divisionName }}</div>
            </div>
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">払出先部署</div>
                <div class="flex-auto">{{ values.acceptance?._targetDivision?.divisionName }}</div>
            </div>
            <div class="flex w-full gap-6">
                <div class="flex-initial lg:w-1/6 w-1/3">合計金額</div>
                <div class="flex-auto">&yen;{{ numberFormat( values.acceptance.acceptancePrice ) }}
                </div>
            </div>
        </div>
        <hr>
        <div class="p-4 text-lg font-bold">
        <div class="p-2 bg-gray-300">
          <v-barcode-search @additem="addItemByBarcode"></v-barcode-search>
        </div>
        <div>
          <table class="table-auto w-full text-sm" id="acceptanceTable">
              <thead>
                <tr>
                  <th class="border-b font-medium p-4 pr-8 text-left border">No</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">商品情報</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">ロット番号</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">使用期限</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">出庫数</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">入庫済み数</th>
                  <?php if ($isPayoutSuccess): ?>
                  <th class="border-b font-medium p-4 pr-8 text-left border">カード番号</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">入庫数</th>
                  <th class="border-b font-medium p-4 pr-8 text-left border">合計金額</th>
                  <?php endif; ?>
                  <th class="border-b font-medium p-4 pr-8 text-left border">ステータス</th>
                  <?php if ($isPayoutSuccess): ?>
                  <th class="border-b font-medium p-4 pr-8 text-left border" colspan=2></th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                  <template  v-for="(elem , index ) in values.acceptance._inHospitalItems">
                      <template v-for="(acceptanceItem , acceptanceIndex ) in elem._acceptanceItems">
                        <template v-if="acceptanceItem._payouts.length === 0">
                          <tr>
                              <td class="text-left px-3 py-4 border">{{ index + 1 }}</td>
                              <td class="text-left px-3 py-4 border">
                                <p class="text-gray-500" v-if="elem.makerName">{{ elem.makerName }}</h3>
                                <p class="text-gray-500" v-if="elem.itemName">{{ elem.itemName }}</p>
                                <p class="text-gray-500" v-if="elem.itemCode">{{ elem.itemCode }}</p>
                                <p class="text-gray-500" v-if="elem.itemStandard">{{ elem.itemStandard }}</p>
                                <p class="text-gray-500" v-if="elem.itemJANCode">{{ elem.itemJANCode }}</p>
                                <p class="text-gray-500">&yen;{{ numberFormat(acceptanceItem.unitPrice) }} / {{ numberFormat(acceptanceItem.quantity) }}{{ acceptanceItem.quantityUnit }}</p>
                              </td>
                              <td class="text-left px-3 py-4 border" >{{ acceptanceItem.lotNumber }}</td>
                              <td class="text-left px-3 py-4 border">{{ acceptanceItem.lotDate }}</td>
                              <td class="text-left px-3 py-4 border">{{ acceptanceItem.acceptanceCount }}{{ acceptanceItem.quantityUnit }}
                              </td>
                              <td class="text-left px-3 py-4 border">{{ acceptanceItem.payoutCount }}{{ acceptanceItem.quantityUnit }}</td>
                              
                              <?php if ($isPayoutSuccess): ?>
                              <td class="text-left px-3 py-4 border"></td>
                              <td class="text-left px-3 py-4 border"></td>
                              <td class="text-left px-3 py-4 border"></td>
                              <?php endif; ?>
                              <td class="text-left px-3 py-4 border">
                                <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="acceptanceItem.payoutCount == acceptanceItem.acceptanceCount">
                                  入庫済み
                                </span>
                              </td>
                              <?php if ($isPayoutSuccess): ?>
                              <td class="text-center px-3 py-4 border">
                              </td>
                              <td class="text-center px-3 py-4 border">
                              </td>
                              <?php endif; ?>
                          </tr>
                        </template>
                        <template v-for="(payout , payoutIndex ) in acceptanceItem._payouts">
                          <tr>
                              <td class="text-left px-3 py-4 border" v-if="acceptanceIndex === 0 && payoutIndex === 0" :rowspan="elem._acceptanceItems.length + acceptanceItem._payouts.length - 1">{{ index + 1 }}</td>
                              <td class="text-left px-3 py-4 border" v-if="acceptanceIndex === 0 && payoutIndex === 0" :rowspan="elem._acceptanceItems.length + acceptanceItem._payouts.length - 1">
                                <p class="text-gray-500" v-if="elem.makerName">{{ elem.makerName }}</h3>
                                <p class="text-gray-500" v-if="elem.itemName">{{ elem.itemName }}</p>
                                <p class="text-gray-500" v-if="elem.itemCode">{{ elem.itemCode }}</p>
                                <p class="text-gray-500" v-if="elem.itemStandard">{{ elem.itemStandard }}</p>
                                <p class="text-gray-500" v-if="elem.itemJANCode">{{ elem.itemJANCode }}</p>
                                <p class="text-gray-500">&yen;{{ numberFormat(acceptanceItem.unitPrice) }} / {{ numberFormat(acceptanceItem.quantity) }}{{ acceptanceItem.quantityUnit }}</p>
                              </td>
                              <td class="text-left px-3 py-4 border" v-if="payoutIndex === 0" :rowspan="acceptanceItem._payouts.length">{{ acceptanceItem.lotNumber }}</td>
                              <td class="text-left px-3 py-4 border" v-if="payoutIndex === 0" :rowspan="acceptanceItem._payouts.length">{{ acceptanceItem.lotDate }}</td>
                              <td class="text-left px-3 py-4 border" v-if="payoutIndex === 0" :rowspan="acceptanceItem._payouts.length">
                                <?php if ($isUpdateSuccess): ?>
                                <v-input-number
                                  :rules="{ between: [ parseInt(acceptanceItem.payoutCount) , Math.max(0 , acceptanceItem.acceptanceCount) ] }" 
                                  :name="`acceptance._inHospitalItems[${index}]._acceptanceItems[${acceptanceIndex}].acceptanceChangeCount`" 
                                  label="出庫数" 
                                  :min="0" 
                                  :unit="acceptanceItem.quantityUnit" 
                                  :step="1" 
                                  title="出庫数" 
                                  class=" w-[240px]" >
                                </v-input-number>
                                <?php else: ?>
                                  {{ numberFormat(acceptanceItem.acceptanceCount) }}{{acceptanceItem.quantityUnit}}
                                <?php endif;?>
                              </td>
                              <td class="text-left px-3 py-4 border" v-if="payoutIndex === 0" :rowspan="acceptanceItem._payouts.length">{{ acceptanceItem.payoutCount }}{{ acceptanceItem.quantityUnit }}</td>
                              
                              <?php if ($isPayoutSuccess): ?>
                              <td class="text-left px-3 py-4 border">{{ payout.cardId }}</td>
                              <td class="text-left px-3 py-4 border">
                                <v-input-number
                                  :rules="{ between: [0 , Math.max(0 , ( parseInt(acceptanceItem.acceptanceChangeCount) - parseInt(acceptanceItem.payoutCount) - ( payoutTotalCount(acceptanceItem) - payout.count ))) ] }" 
                                  :name="`acceptance._inHospitalItems[${index}]._acceptanceItems[${acceptanceIndex}]._payouts[${payoutIndex}].count`" 
                                  label="入庫数" 
                                  :min="0" 
                                  :unit="acceptanceItem.quantityUnit" 
                                  :step="1" 
                                  title="入庫数" 
                                  :disabled="payout.cardId != ''"
                                  class=" w-[240px]" >
                                </v-input-number>
                              </td>
                              <td class="text-left px-3 py-4 border" v-if="acceptanceIndex === 0 && payoutIndex === 0" :rowspan="elem._acceptanceItems.length + acceptanceItem._payouts.length - 1">&yen;{{ numberFormat(priceCalc(acceptanceItem)) }}</td>
                              <?php endif; ?>
                              <td class="text-left px-3 py-4 border" v-if="payoutIndex === 0" :rowspan="acceptanceItem._payouts.length">
                                <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="status(acceptanceItem) == 1">
                                  未入庫
                                </span>
                                <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="status(acceptanceItem) == 2">
                                  一部未入庫 
                                </span>
                                <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="status(acceptanceItem) == 3">
                                  入庫済み
                                </span>
                              </td>
                              <?php if ($isPayoutSuccess): ?>
                              <td class="text-center px-3 py-4 border">
                                <v-button-default @click.native="copy(index,acceptanceIndex)">複製</v-button-default>
                              </td>
                              <td class="text-center px-3 py-4 border">
                                <v-button-danger v-if="payout.isDeleteButton" @click.native="removeItem(index,acceptanceIndex,payoutIndex)">削除</v-button-danger>
                              </td>
                              <?php endif; ?>
                          </tr>
                        </template>
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

const acceptanceId = '<?php echo $acceptanceId; ?>';
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
            acceptance : {},
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
        <?php if ($isPayoutSuccess && $isUpdateSuccess): ?>
        pulldownSelect.value = [
          { onclick: () => onAcceptanceUpdate() , 'text': '変更を保存', variant : 'default' , disabled: false},
          { onclick: () => onPayoutUpdate() , 'text': '入庫登録', variant : 'default' , disabled: false},
          { onclick: () => onPayoutAllUpdate() , 'text': '一括入庫登録', variant : 'default' , disabled: false},
          { variant : 'border'},
          { onclick: () => onDelete() , 'text': '削除', variant : 'danger' , disabled: ! (values.acceptance.payoutTotalCount == 0 && values.acceptance.acceptanceTotalCount > 0)},
        ];
        <?php elseif ($isUpdateSuccess): ?>
        pulldownSelect.value = [
          { onclick: () => onAcceptanceUpdate() , 'text': '変更を保存', variant : 'default' , disabled: false},
          { variant : 'border'},
          { onclick: () => onDelete() , 'text': '削除', variant : 'danger' , disabled: ! (values.acceptance.payoutTotalCount == 0 && values.acceptance.acceptanceTotalCount > 0)},
        ];
        <?php elseif ($isPayoutSuccess): ?>
        pulldownSelect.value = [
          { onclick: () => onPayoutUpdate() , 'text': '入庫登録', variant : 'default' , disabled: false},
          { onclick: () => onPayoutAllUpdate() , 'text': '一括入庫登録', variant : 'default' , disabled: false},
          { variant : 'border'},
          { onclick: () => onDelete() , 'text': '削除', variant : 'danger' , disabled: ! (values.acceptance.payoutTotalCount == 0 && values.acceptance.acceptanceTotalCount > 0)},
        ];
        <?php endif; ?>
      });
      const breadcrumbs = [
          {
            text: '払出メニュー',
            disabled: false,
            href: _ROOT + '&path=/payout',
          },
          {
            text: '出庫一覧',
            disabled: false,
            href: _ROOT + '&path=/acceptance&isCache=true',
          },
          {
            text: '出庫伝票',
            disabled: true,
          }
        ];
    
        
      const getSlipData = async ( acceptanceId ) => 
      {
        startLoading();
        
        let params = new URLSearchParams();
        params.append("path", "/api/acceptance/"+acceptanceId);
        params.append("_method", 'get');
        params.append("_csrf", _CSRF);

        const res = await axios.post(_APIURL,params);
        
        completeLoading();

        if(res.data.code != 200) {
          throw new Error(res.data.message)
        }
        return res.data ;
      }

      const convertAcceptance = (data) => {
        let acceptance = data;
        acceptance._inHospitalItems = Object.values(acceptance._inHospitalItems);
        acceptance._inHospitalItems.forEach((item , index) => {
            acceptance._inHospitalItems[index]._acceptanceItems = [];
            Object.values(acceptance._items).forEach((item2, index2) => {
                if(item2.inHospitalItemId == item.inHospitalItemId){
                    item2._payouts = [];
                    if(item2.payoutCount != item2.acceptanceCount)
                    {
                      item2._payouts.push({
                          count: 0 ,
                          cardId: '' ,
                      });
                    }
                    if(isUnitPrice){
                      item2.unitPrice = (item2.price == 0 || item2.quantity == 0 )? 0 : (item2.price / item2.quantity);
                    }
                    item2.acceptanceChangeCount = item2.acceptanceCount;
                    acceptance._inHospitalItems[index]._acceptanceItems.push(
                        item2
                    );
                }
            })
        });

        return acceptance;
      }

      const payoutTotalCount = (acceptanceItem) => {
        let count = 0;
        acceptanceItem._payouts.forEach((elem) => {
          count += parseInt(elem.count);
        })
        return count;
      }

      const priceCalc = (acceptanceItem) => {
        let count = payoutTotalCount(acceptanceItem);
        if(count === 0){
          return 0;
        }
        return (acceptanceItem.unitPrice === 0 )? 0 : acceptanceItem.unitPrice * count;
      }

      const fetchData = async () => {
        startLoading();
        const response = await getSlipData(acceptanceId);
        values.acceptance = convertAcceptance(response.data);
        completeLoading();
      };

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };
      
      
      const copy = (idx,pIdx) => {
          values.acceptance._inHospitalItems[idx]._acceptanceItems[pIdx]._payouts.push({
              count: 0 ,
              cardId: '' ,
              isDeleteButton : true
          });
      }

      const removeItem = (idx,pIdx, payoutIdx) => {
        values.acceptance._inHospitalItems[idx]._acceptanceItems[pIdx]._payouts.splice( payoutIdx, 1 );
      }

      const searchCardId = (cardId) => {
        let exist = false;
        values.acceptance._inHospitalItems.forEach((inHospitalItem, idx) => {
          inHospitalItem._acceptanceItems.forEach((acceptanceItem) => {
            acceptanceItem._payouts.forEach((item) => {
              if (
                item.cardId == cardId
              ) {
                exist = true;
              }
            })
          });
        });
        return exist;
      }

      const addPayout = (item , type) => {
        item = JSON.parse(JSON.stringify(item));
        let itemCheck = false;
        if (Array.isArray(values.acceptance._inHospitalItems)) {
          values.acceptance._inHospitalItems.forEach((inHospitalItem, idx) => {
            if (
              inHospitalItem.inHospitalItemId === item.inHospitalItemId
            ) {
              itemCheck = true;
              inHospitalItem._acceptanceItems.forEach((acceptanceItem) => {
                acceptanceItem._payouts.push({
                  count : item.payoutCount,
                  cardId : (type === 'card')? item.barcode : '',
                  isDeleteButton: true
                });
              });
            }
          });
          if(!itemCheck) {
              Swal.fire({
                icon: 'error',
                title: 'エラー',
                text: '一致する商品がありませんでした',
              });
              return false;
            }
        }
      }
      const addItemByBarcode = (items) => {
        if (!items.item || items.item.length === 0 || items.type !== "card") {
          Swal.fire({
            icon: 'info',
            title: '商品が見つかりませんでした',
          });
          return false;
        }

        if (items.type == "card") {
          items.item.forEach((x, id) => {
            items.item[id].payoutCount = items.item[id].cardQuantity
          });
        }

        if (items.item.length === 1) {
          if (items.item[0].divisionId) {
            if(values.acceptance.targetDivisionId === items.item[0].divisionId){
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
            } else {
              Swal.fire({
                icon: 'error',
                title: 'エラー',
                text: '読み込んだ値と選択している部署が一致しませんでした',
              });
              return false;
            }
          } else {
            items.item[0].divisionId = values.acceptance.targetDivisionId
            addPayout(items.item[0] , items.type)
          }
        }
      }

      const updateAcceptance = (updateItems) => {
        let params = new URLSearchParams();
        params.append("path", "/api/acceptance/"+acceptanceId);
        params.append("_method", 'patch');
        params.append("_csrf", _CSRF);
        params.append("updateItems", JSON.stringify(encodeURIToObject(updateItems)));
        return axios.post(_APIURL,params);
      }
      
      const registerPayout = (payoutItems) => {
        let params = new URLSearchParams();
        params.append("path", "/api/acceptance/"+acceptanceId + "/payout");
        params.append("_method", 'post');
        params.append("payoutDate", values.payoutDate);
        params.append("_csrf", _CSRF);
        params.append("payoutItems", JSON.stringify(encodeURIToObject(payoutItems)));
        return axios.post(_APIURL,params);
      }

      const registerAllPayout = (payoutItems) => {
        let params = new URLSearchParams();
        params.append("path", "/api/acceptance/"+acceptanceId + "/payout");
        params.append("_method", 'post');
        params.append("payoutDate", values.payoutDate);
        params.append("_csrf", _CSRF);
        params.append("payoutItems", JSON.stringify(encodeURIToObject(payoutItems)));
        params.append("isAll", 'true');
        return axios.post(_APIURL,params);
      }

      const deleteAcceptance = () => {
        let params = new URLSearchParams();
        params.append("path", "/api/acceptance/"+acceptanceId);
        params.append("_method", 'delete');
        params.append("_csrf", _CSRF);
        return axios.post(_APIURL,params);
      }

      const createAcceptanceUpdateModel = (values) => {
        let model = [];
        values.acceptance._inHospitalItems.forEach((item) => {
          item._acceptanceItems.forEach((acceptanceItem) => {
            if(acceptanceItem.acceptanceChangeCount != acceptanceItem.acceptanceCount)
            {
              model.push({
                'acceptanceItemId': acceptanceItem.acceptanceItemId,
                'acceptanceCount': acceptanceItem.acceptanceChangeCount,
              })
            }
          });
        })
        return model;
      }

      const createPayoutRegisterModel = (values) => {
        let model = [];
        values.acceptance._inHospitalItems.forEach((item) => {
          item._acceptanceItems.forEach((acceptanceItem) => {
            acceptanceItem._payouts.forEach((payoutItem) => {
              if(payoutItem.count > 0){
                model.push({
                  'acceptanceItemId': acceptanceItem.acceptanceItemId,
                  'payoutCount': payoutItem.count,
                  'cardId' : payoutItem.cardId,
                })
              }
            })
          });
        })
        return model;
      }


      const slipUpdate = handleSubmit(async (values) => {
        const updateModels = createAcceptanceUpdateModel(values);
        const res = await updateAcceptance(updateModels);
        if(res.data.code != 200) {
          throw new Error(res.data.message)
        }
      });

      const payoutReg = handleSubmit(async (values) => {
        const registerModels = createPayoutRegisterModel(values);
        const res = await registerPayout(registerModels);
        if(res.data.code != 200) {
          throw new Error(res.data.message)
        }
      });

      const payoutAllReg = handleSubmit(async (values) => {
        const registerModels = createPayoutRegisterModel(values);
        const res = await registerAllPayout(registerModels);
        if(res.data.code != 200) {
          throw new Error(res.data.message)
        }
      });

      const slipDelete = handleSubmit(async (values) => {
        const res = await deleteAcceptance();
        if(res.data.code != 200) {
          throw new Error(res.data.message)
        }
      });

      const onAcceptanceUpdate = async () => {
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
                  await slipUpdate();
                  completeLoading();
                  await Swal.fire({
                    icon: 'success',
                    title: '更新が完了しました',
                  }).then((result) => {
                      location.reload();
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
      }

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
                title: '入庫登録',
                text: "入庫登録を行います。\r\nよろしいですか？",
                icon: 'warning',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            }).then(async (result) => {
                if (result.isConfirmed) {
                  startLoading();
                  <?php if ($isUpdateSuccess): ?>
                  await slipUpdate();
                  <?php endif;?>
                  await payoutReg();
                  completeLoading();
                  await Swal.fire({
                    icon: 'success',
                    title: '入庫登録が完了しました',
                  }).then((result) => {
                      location.reload();
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
      }

      
      const onPayoutAllUpdate = async () => {
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
                title: '一括入庫登録',
                text: "一括入庫登録を行います。\r\nよろしいですか？",
                icon: 'warning',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            }).then(async (result) => {
                if (result.isConfirmed) {
                  startLoading();
                  await slipUpdate();
                  await payoutAllReg();
                  completeLoading();
                  await Swal.fire({
                    icon: 'success',
                    title: '一括入庫登録が完了しました',
                  }).then((result) => {
                      location.reload();
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
                    location.href = _ROOT + '&path=/acceptance&isCache=true';
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
          const url = _ROOT + '&path=/label/acceptance/' + acceptanceId;
          window.open(url, '_blank')
      }

      const status = (item) => {
        const payoutCount = parseInt(item.payoutCount)
        const acceptanceCount = parseInt(item.acceptanceCount)
        if(payoutCount == 0 && acceptanceCount > 0) {
          return 1
        }
        if(payoutCount > 0 && payoutCount < acceptanceCount) {
          return 2
        }
        if(payoutCount == acceptanceCount) {
          return 3
        }
      }


      return {
        status,
        openLabelPage,
        addItemByBarcode,
        removeItem,
        copy,
        priceCalc,
        payoutTotalCount,
        alert,
        numberFormat,
        breadcrumbs,
        loading, 
        values,
        pulldownSelect
      }
  },
  watch: {
  }
}).mount('#top');
</script> 
<script>
    window.addEventListener('DOMContentLoaded', function() {
        new ScrollHint('#acceptanceTable', {
            scrollHintIconAppendClass: 'scroll-hint-icon-white', // white-icon will appear
            applyToParents: true,
            i18n: {
                scrollable: 'スクロールできます'
            }
        });
    });
</script>