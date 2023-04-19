<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto w-full">
      <div class="index px-1 w-full mx-auto mb-96">
        <h1 class="text-2xl mb-2">会計伝票の詳細</h1>
        <hr>
        <div class="p-4 text-base bg-gray-100 border border-gray-400 flex flex-col md:flex-row md:gap-6 gap-4 mb-6">
          <?php if (true): ?>
          <v-button-danger type="button" class="md:w-1/6 w-full" >
            会計伝票を削除
          </v-button-danger>
          <?php endif; ?>
        </div>
        <div class="p-4 text-base bg-gray-100 border border-gray-400">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">会計日</div>
            <div class="flex-auto">{{ values.accountant?.accountantDate }}</div>
         </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">会計番号</div>
            <div class="flex-auto">{{ values.accountant?.accountantId }}</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">部署</div>
            <div class="flex-auto">{{ values.accountant?._division?.divisionName }}</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">卸業者</div>
            <div class="flex-auto">{{ values.accountant?._distributor?.distributorName }}</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">発注番号</div>
            <div class="flex-auto">{{ values.accountant?.orderId }}</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">検収番号</div>
            <div class="flex-auto">{{ values.accountant?.receivedId }}</div>
          </div>
        </div>
        <hr>
        <div class="p-4 text-lg font-bold">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">合計金額</div>
            <div class="flex-auto">&yen;{{ numberFormat(totalAmount()) }}</div>
          </div>
        </div>
        <div class="p-4">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3 text-lg font-bold">税率一括反映</div>
            <div class="flex-auto">
              <div class="flex gap-6">
                <v-input
                  name="bulkTax"
                  type="number"
                  label=""
                  title=""
                  prefix="%"
                  class="h-full"
                ></v-input>
                <v-button-default type="button" @click.native="bulkUpdateTax">反映</v-button-default>
              </div>
              <p>※税率が0%で記載されている情報に対して上書きします</p>
            </div>
          </div>
        </div>
        <div class="text-base">
          <div class="lg:flex w-full gap-6">
            <div class="flex-auto w-full">
              <div class="p-4 text-lg font-bold">商品情報</div>
              <div class="overflow-x-auto w-full">
                <table class="table-auto text-sm w-full">
                  <thead>
                    <tr>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">No</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">登録方法</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">アクション</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">商品名</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">メーカー名</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">製品コード</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">規格</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">JANコード</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">
                        <div class="w-24">数量</div>
                      </th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">
                        <div class="w-24">価格</div>
                      </th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">
                        <div class="w-24">税率</div>
                      </th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">合計金額</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index)  in fields.sort((a, b) => a.value.index - b.value.index)" 
                        class="draggable"
                        :key="item.key" 
                        :draggable="true"
                        @dragstart="dragHandlers.start(index)"
                        @dragenter="dragHandlers.enter(index)"
                        @dragover.prevent
                        @dragend="dragHandlers.end(index)"
                        :class="index === dragIndex ? 'dragging' : ''"
                      >
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ index + 1 }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.value.method }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.value.action }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.value.itemName }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.value.makerName }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.value.itemCode }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.value.itemStandard }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.value.itemJANCode }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">
                        <v-input
                          v-model="item.value.count"
                          :name="`items[${index}].count`"
                          type="number"
                          label=""
                          title=""
                          suffix=""
                          class="w-24"
                          change-class-name="inputChange"
                          ></v-input>
                      </td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">
                        <v-input
                          v-model="item.value.price"
                          :name="`items[${index}].price`"
                          type="number"
                          label=""
                          title=""
                          suffix="&yen;"
                          class="w-24"
                          change-class-name="inputChange"
                          ></v-input>
                      </td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">
                        <v-input
                          v-model="item.value.tax"
                          :name="`items[${index}].tax`"
                          type="number"
                          label=""
                          title=""
                          prefix="%"
                          class="w-24"
                          change-class-name="inputChange"
                          ></v-input>
                      </td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">&yen;{{ numberFormat(itemSubtotal(item.value)) }}</td>
                      <td class="border-b border-slate-100">
                        <v-button-primary type="button" @click.native="copyItem(index)">複製</v-button-primary>
                      </td>
                      <td class="border-b border-slate-100">
                        <v-button-danger type="button" @click.native="deleteItem(index)">削除</v-button-danger>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="w-full" v-if="!openRegistPage">
                <v-button-default type="button" class="w-full" @click.native="openRegistPage = true">+ 行を追加</v-button-default>
              </div>
              <div class="lg:flex w-full gap-6" v-if="openRegistPage">
                <div class="flex-auto">
                  <div class="p-4 text-lg font-bold">商品情報追加</div>
                  <div>
                    <div class="flex flex-wrap">
                      <div class="md:w-1/2 w-full px-4 mx-auto">
                        <div>
                          <v-button-default type="button" data-micromodal-trigger="inHospitalItemModal" class="w-full">商品検索</v-button-default>
                          <v-in-hospital-item-modal v-on:additem="addInHospitalItem">
                          </v-in-hospital-item-modal>
                        </div>
                        <div class="my-4">
                          <v-select
                              label="アクション"
                              title="アクション"
                              :rules="{ required: true}"
                              name="register.action"
                              :options="actionOptions"
                            ></v-select>
                        </div>
                        <div class="my-4">
                          <v-input
                            name="register.itemName"
                            type="text"
                            label="商品名"
                            :rules="{ required: true}"
                            title="商品名"
                            ></v-input>
                        </div>
                        <div class="my-4">
                          <v-input
                            name="register.makerName"
                            type="text"
                            label="メーカー名"
                            title="メーカー名"
                            ></v-input>
                        </div>
                        <div class="my-4">
                          <v-input
                            name="register.itemCode"
                            type="text"
                            label="製品コード"
                            title="製品コード"
                            ></v-input>
                        </div>
                        <div class="my-4">
                          <v-input
                            name="register.itemStandard"
                            type="text"
                            label="規格"
                            title="規格"
                            ></v-input>
                        </div>
                        <div class="my-4">
                          <v-input
                            name="register.itemJANCode"
                            type="text"
                            label="JANコード"
                            title="JANコード"
                            ></v-input>
                        </div>
                        <div class="flex">
                          <div class="my-4 w-1/2">
                            <v-input
                              name="register.count"
                              type="number"
                              :rules="{ required: true}"
                              label="数量"
                              title="数量"
                              ></v-input>
                          </div>
                          <div class="my-4 w-1/2">
                            <v-input
                              name="register.unit"
                              type="text"
                              :rules="{ required: true}"
                              label="単位"
                              title="単位"
                              ></v-input>
                          </div>
                        </div>
                        <div class="my-4">
                          <v-input
                            name="register.price"
                            type="number"
                            :rules="{ required: true}"
                            label="価格"
                            title="価格"
                            suffix="&yen;"
                            ></v-input>
                        </div>
                        <div class="my-4">
                          <v-input
                            name="register.tax"
                            type="number"
                            :rules="{ required: true}"
                            label="税率"
                            title="税率"
                            prefix="%"
                            ></v-input>
                        </div>
                        <div class="mx-auto lg:w-2/3 mb-4 text-center flex items-center gap-6 justify-center">
                          <v-button-default type="button" @click.native="clear">クリア</v-button-default>
                          <v-button-primary type="button" @click.native="addItem">追加</v-button-primary>
                        </div>
                        <div class="w-full">
                          <v-button-default type="button" @click.native="openRegistPage = false" class="w-full">閉じる</v-button-default>
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

const accountantId = '<?php echo $accountantId; ?>';
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

var JoyPlaApp = Vue.createApp({ 
    components: {
      'v-open-modal': vOpenModal,
      'v-loading' : vLoading,
      'header-navi' : headerNavi,
      'v-breadcrumbs': vBreadcrumbs,
      'v-input': vInput,
      'v-select' : vSelect,
      'v-input-number': vInputNumber,
      'item-view' : itemView,
      'v-button-default' : vButtonDefault,
      'v-button-danger' : vButtonDanger,
      'v-button-primary' : vButtonPrimary,
      'v-in-hospital-item-modal': vInHospitalItemModal,
    },
    setup(){
      const {ref , onCreated , onMounted} = Vue;
      const {useFieldArray, useForm} = VeeValidate;

      const { handleSubmit , control, meta , validate , values , isSubmitting } = useForm({
          initialValues: {
            bulkTax: 0,
            register : {
              inHospitalItemId: "",
              action  : "",
              itemName  : "",
              makerName : "",
              itemCode : "",
              itemStandard : "",
              itemJANCode : "",
              tax: 0,
              unit : "",
              count : 0,
              price : 0,
            },
            accountant : [],
            items : [],
          },
          validateOnMount : false
      });

      const openRegistPage = ref(false);
      
      const { remove, push, fields , update , replace , insert} = useFieldArray('items' , control);

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

      const breadcrumbs = [
        {
          text: '会計メニュー',
          disabled: false,
          href: _ROOT+'&path=/accountant',
        },
        {
          text: '会計一覧',
          disabled: false,
          href: _ROOT+'&path=/accountant/index&isCache=true',
        },
        {
          text: '会計伝票の詳細',
          disabled: true,
        }
      ];

      const getSlipData = async ( accountantId ) => 
      {
        startLoading();
        
        let params = new URLSearchParams();
        params.append("path", "/api/accountant/"+accountantId);
        params.append("_method", 'get');
        params.append("_csrf", _CSRF);

        const res = await axios.post(_APIURL,params);
        
        completeLoading();

        if(res.data.code != 200) {
          throw new Error(res.data.message)
        }
        return res.data ;
      }

      const fetchData = async () => {
        startLoading();
        const response = await getSlipData(accountantId);
        values.accountant = response.data;
        replace([]);
        completeLoading();
      };
      
      onMounted(async () => {
        await sleepCompleteLoading();
        await fetchData();
      });

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };

      const addItemHandler = async () => {
        const { valid, errors } = await validate();

        if (!valid) {
          Swal.fire({
            icon: "error",
            title: "入力エラー",
            text: "入力エラーがございます。ご確認ください",
          });
          return;
        }

        if (!values.items) {
          values.items = [];
        }

        insert(fields.value.length, {
          index: fields.value.length,
          method: "手動",
          inHospitalItemId: values.register.inHospitalItemId,
          action: values.register.action,
          makerName: values.register.makerName,
          itemName: values.register.itemName,
          itemCode: values.register.itemCode,
          itemStandard: values.register.itemStandard,
          itemJANCode: values.register.itemJANCode,
          count: values.register.count,
          price: values.register.price,
          tax: values.register.tax,
          unit: values.register.unit,
        });
      };
      
      const itemSubtotal = (item) => {
        if(!item){
          console.log(item);
          return 0;
        }
        item = {
          price : item.price ?? 0,
          count : item.count ?? 0,
          tax : item.tax ?? 0,
        }

        // 価格、数量、税率を整数に変換
        const priceInt = Math.round(item.price * 100);
        const countInt = Math.round(item.count * 100);
        const taxRateInt = Math.round(item.tax);

        // 小計と税額を計算
        const itemTotalInt = priceInt * countInt / 100;
        const taxAmountInt = itemTotalInt * taxRateInt / 100;

        // 小計と税額を加算して、結果を小数に戻して返す
        return (itemTotalInt + taxAmountInt) / 100 ?? 0;
      }

      const totalAmount = () => {
        return fields.value.reduce((sum, item) => {
          return sum + itemSubtotal(item.value);
        }, 0);
      }

      const deleteItem = (index) => {
        remove(index);
      }

      const debounce = (func, wait) => {
        let timeout;
        return function (...args) {
          const context = this;
          clearTimeout(timeout);
          timeout = setTimeout(() => func.apply(context, args), wait);
        };
      }

      const copyItem = debounce((index) => {
        // indexプロパティをインクリメントする必要があるアイテムのキーを収集
        const keysToUpdate = fields.value
          .filter((field) => field.value.index > fields.value[index].value.index)
          .map((field) => field.key);

        // コピー元のアイテムのindexプロパティより大きい値を持つすべてのアイテムのindexプロパティをインクリメント
        keysToUpdate.forEach((key) => {
          const updatedItem = { ...fields.value[key].value, index: fields.value[key].value.index + 1 };
          update(key, updatedItem);
        });

        // 新しいアイテムをコピーして、適切なindexプロパティを設定
        const copiedItem = JSON.parse(JSON.stringify({ ...fields.value[index].value, index: fields.value[index].value.index + 1 }));

        // 新しいアイテムを挿入
        insert(index + 1, copiedItem);
      }, 300);

      const addInHospitalItem =  (elem) => {
        values.register.inHospitalItemId = elem.inHospitalItemId
        values.register.makerName = elem.makerName
        values.register.itemName = elem.itemName
        values.register.itemCode = elem.itemCode
        values.register.itemStandard = elem.itemStandard
        values.register.itemJANCode = elem.itemJANCode
        values.register.count = 1
        values.register.price = elem.price
        values.register.tax = 0
        values.register.unit = elem.itemUnit
        MicroModal.close("inHospitalItemModal", {// モーダルを閉じる処理
          awaitCloseAnimation: true // 開閉時のアニメーションを可能に
        });
      }
      
      const clear = () => {
        values.register.inHospitalItemId = '',
        values.register.action = ''
        values.register.makerName = ''
        values.register.itemName = ''
        values.register.itemCode = ''
        values.register.itemStandard = ''
        values.register.itemJANCode = ''
        values.register.count = 0
        values.register.price = 0
        values.register.tax = 0
        values.register.unit = ''
      }

      const bulkUpdateTax = () => {
        fields.value.forEach((element , index) => {
          if(element.value.tax === 0)
          {
            element.value.tax = values.bulkTax;
          }
          update(index, element.value)
        });
      }
      
      const actionOptions = [{ label: "消費", value: "消費" },{ label: "入荷", value: "入荷" },{ label: "払出", value: "払出" },{ label: "その他", value: "その他" }];

      const dragIndex = ref(null)

      const dragHandlers = {
        start: (index) => {
          dragIndex.value = index;
        },
        enter: (index) => {
          if (index === dragIndex.value) return;

          const draggedItem = fields.value[dragIndex.value].value;
          const targetItem = fields.value[index].value;

          // コピー元のアイテムのindexプロパティとコピー先のアイテムのindexプロパティを交換
          const updatedDraggedItem = { ...draggedItem, index: targetItem.index };
          const updatedTargetItem = { ...targetItem, index: draggedItem.index };

          // 更新されたアイテムを適切な位置に挿入
          update(dragIndex.value, updatedTargetItem);
          update(index, updatedDraggedItem);

          // ドラッグされているアイテムのインデックスを更新
          dragIndex.value = index;
        },
        end: (index) => {
          dragIndex.value = null;
        },
      };

    return {
      dragHandlers,
      bulkUpdateTax,
      clear,
      addInHospitalItem,
      openRegistPage,
      copyItem,
      addItem: addItemHandler,
      deleteItem,
      totalAmount,
      itemSubtotal,
      values,
      fields,
      numberFormat,
      breadcrumbs,
      loading,
      startLoading,
      completeLoading,
      actionOptions,
    };
  },
}).mount("#top");
</script> 