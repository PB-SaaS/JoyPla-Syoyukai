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
          <v-button-primary type="button" class="md:w-1/6 w-full" @click.native="itemRegister">
            会計伝票を更新
          </v-button-primary>
          <v-button-danger type="button" class="md:w-1/6 w-full" @click.native="slipDelete">
            会計伝票を削除
          </v-button-danger>
          <v-button-default type="button" class="md:w-1/6 w-full" >
            印刷
          </v-button-default>
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
                <table class="table-auto text-sm w-full whitespace-nowrap">
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
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">数量
                      </th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">価格
                      </th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">税率
                      </th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">合計金額</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index)  in values.items" 
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
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.method }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.action }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.itemName }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.makerName }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.itemCode }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.itemStandard }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.itemJANCode }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">
                        <v-input
                          v-model="item.count"
                          :name="`items[${index}].count`"
                          type="number"
                          label="数量"
                          title=""
                          suffix=""
                          :prefix="item.unit"
                          class="w-full"
                          :rules="{ between: [-99999 , 99999] }"
                          change-class-name="inputChange"
                          ></v-input>
                      </td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">
                        <v-input
                          v-model="item.price"
                          :name="`items[${index}].price`"
                          type="number"
                          label="価格"
                          title=""
                          suffix="&yen;"
                          :rules="{ between: [-99999 , 99999] }"
                          class="w-24"
                          change-class-name="inputChange"
                          ></v-input>
                      </td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">
                        <v-input
                          v-model="item.taxrate"
                          :name="`items[${index}].taxrate`"
                          type="number"
                          label="税率"
                          title=""
                          prefix="%"
                          :rules="{ between: [-99999 , 99999] }"
                          class="w-24"
                          change-class-name="inputChange"
                          ></v-input>
                      </td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">&yen;{{ numberFormat(itemSubtotal(item)) }}</td>
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
              <div class="flex items-center justify-center w-full mt-4">
                <label for="file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 ">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg aria-hidden="true" class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="mb-2 text-sm text-gray-500 ">ファイルをアップロードしてください</p>
                        <p class="text-xs text-gray-500 ">TSV,CSV,TXT</p>
                    </div>
                    <input 
                      id="file" 
                      type="file" 
                      class="hidden"
                      accept=".csv,.tsv,.txt"
                      @change="loadCsvFile" />
                </label>
              </div> 
              <div class="inline-flex items-center justify-center w-full">
                  <hr class="w-64 h-px my-8 bg-gray-200 border-0 ">
                  <span class="absolute px-3 font-medium text-gray-900 -translate-x-1/2 bg-white left-1/2 ">or</span>
              </div>
              <div class="w-full mt-4" v-if="!openRegistPage">
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
                              :rules="{ between: [-99999 , 99999] }"
                              label="数量"
                              title="数量"
                              ></v-input>
                          </div>
                          <div class="my-4 w-1/2">
                            <v-input
                              name="register.unit"
                              type="text"
                              label="単位"
                              title="単位"
                              ></v-input>
                          </div>
                        </div>
                        <div class="my-4">
                          <v-input
                            name="register.price"
                            type="number"
                            :rules="{ between: [-99999 , 99999] }" 
                            label="価格"
                            title="価格"
                            suffix="&yen;"
                            ></v-input>
                        </div>
                        <div class="my-4">
                          <v-input
                            name="register.taxrate"
                            type="number"
                            :rules="{ between: [-99999 , 99999] }" 
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
              itemId: "",
              action  : "",
              itemName  : "",
              makerName : "",
              itemCode : "",
              itemStandard : "",
              itemJANCode : "",
              taxrate: 0,
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
        replace(response.data.items);
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

      const insertItem = (item) => {
        if (!values.items) {
            values.items = [];
          }

        insert(fields.value.length, {
          index: fields.value.length,
          method: "手動",
          accountantItemId: '',
          itemId: item.itemId,
          action: item.action,
          makerName: item.makerName,
          itemName: item.itemName,
          itemCode: item.itemCode,
          itemStandard: item.itemStandard,
          itemJANCode: item.itemJANCode,
          count: item.count,
          price: item.price,
          taxrate: item.taxrate,
          unit: item.unit,
        });
      }

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

        insertItem(values.register)
      };

      const slipDelete = (values) => {
          Swal.fire({
            title: '伝票を削除',
            text: "削除後は元に戻せません。\r\nよろしいですか？",
            icon: 'warning',
            confirmButtonText: '削除します',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
          }).then( async (result) => {
            try {
              startLoading();
              let params = new URLSearchParams();
              params.append("path", "/api/accountant/"+accountantId+"/delete");
              params.append("_method", 'delete');
              params.append("_csrf", _CSRF);

              const res = await axios.post(_APIURL, params);

              if (res.data.code != 200) {
                throw new Error(res.data.message)
              }

              Swal.fire({
                icon: 'success',
                title: '削除が完了しました。',
              }).then((result) => {
                location.href = _ROOT+'&path=/accountant/index&isCache=true';
              });

              return true;
            } catch (error) {
              Swal.fire({
                icon: 'error',
                title: 'システムエラー',
                text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
              });
            } finally {
              completeLoading();
            }
          });
        };

      const itemRegister = handleSubmit(async (values) => {
        try {
          startLoading();
          let params = new URLSearchParams();
          params.append("path", "/api/accountant/"+accountantId+"/update");
          params.append("_method", 'patch');
          params.append("_csrf", _CSRF);
          params.append("accountant", JSON.stringify(encodeURIToObject(
            {
              'items' : values.items ?? []
            })));

          const res = await axios.post(_APIURL, params);

          if (res.data.code != 200) {
            throw new Error(res.data.message)
          }

          Swal.fire({
            icon: 'success',
            title: '更新が完了しました。',
          }).then((result) => {
            location.reload();
          });

          return true;
        } catch (error) {
          Swal.fire({
            icon: 'error',
            title: 'システムエラー',
            text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
          });
        } finally {
          completeLoading();
        }
      });

      const itemSubtotal = (item) => {
        if(!item){
          console.log(item);
          return 0;
        }
        item = {
          price : item.price ?? 0,
          count : item.count ?? 0,
          taxrate : item.taxrate ?? 0,
        }

        // 価格、数量、税率を整数に変換
        const priceInt = Math.round(item.price * 100);
        const countInt = Math.round(item.count * 100);
        const taxRateInt = Math.round(item.taxrate);

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

      const copyItem = (index) => {
        const originalItem = values.items[index];
        const copiedItem = JSON.parse(JSON.stringify({ ...originalItem, index: originalItem.index + 1 , accountantItemId: ''}));

        fields.value.forEach((field, idx) => {
          if (field.value.index > fields.value[index].value.index) {
            const updatedItem = { ...fields.value[idx].value, index: fields.value[idx].value.index + 1 };
            update(idx, updatedItem);
          }
        });

        // 新しいアイテムを挿入
        insert(index + 1, copiedItem);
      };

      const addInHospitalItem =  (elem) => {
        values.register.itemId = elem.itemId
        values.register.makerName = elem.makerName
        values.register.itemName = elem.itemName
        values.register.itemCode = elem.itemCode
        values.register.itemStandard = elem.itemStandard
        values.register.itemJANCode = elem.itemJANCode
        values.register.count = 1
        values.register.price = elem.price
        values.register.taxrate = 0
        values.register.unit = elem.itemUnit
        MicroModal.close("inHospitalItemModal", {// モーダルを閉じる処理
          awaitCloseAnimation: true // 開閉時のアニメーションを可能に
        });
      }
      
      const clear = () => {
        values.register.itemId = '',
        values.register.action = ''
        values.register.makerName = ''
        values.register.itemName = ''
        values.register.itemCode = ''
        values.register.itemStandard = ''
        values.register.itemJANCode = ''
        values.register.count = 0
        values.register.price = 0
        values.register.taxrate = 0
        values.register.unit = ''
      }

      const bulkUpdateTax = () => {
        fields.value.forEach((element , index) => {
          if(element.value.taxrate === 0)
          {
            element.value.taxrate = values.bulkTax;
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

      const loadCsvFile = (e) => {
          const headers = ['アクション','商品名','メーカー名','製品コード','規格','JANコード','数量','単位','価格','税率'];
      
          let file = e
              .target
              .files[0];

          let extension = file
              .name
              .split('.')
              .pop();

          if (!file.type.match("text/csv") && !file.type.match("application/vnd.ms-excel") && (extension != 'tsv' && extension != 'txt')) {
              throw new Error( "CSV/TSV ファイルを選択してください" );
          }

          let extension_type = extension;
          if ((file.type.match("text/csv") || file.type.match("application/vnd.ms-excel"))) {
              extension_type = 'csv';
          }

          if ((extension == 'tsv' || extension == 'txt')) {
              extension_type = 'tsv';
          }

          let reader = new FileReader();
          reader.readAsArrayBuffer(file);
          //reader.readAsText(file);
          startLoading();
          reader.onload = function (e) {
            try{
              // 8ビット符号なし整数値配列と見なす
              let array = new Uint8Array(e.target.result);

              // 文字コードを取得
              switch (Encoding.detect(array)) {
                  case 'UTF16':
                      // 16ビット符号なし整数値配列と見なす
                      array = new Uint16Array(e.target.result);
                      break;
                  case 'UTF32':
                      // 32ビット符号なし整数値配列と見なす
                      array = new Uint32Array(e.target.result);
                      break;
              }

              // Unicodeの数値配列に変換
              let unicodeArray = Encoding.convert(array, 'UNICODE');
              // Unicodeの数値配列を文字列に変換
              let text = Encoding.codeToString(unicodeArray);

              let lines = text.split(/\r\n|\n/);
              let linesArr = [];

              for (let i = 0; i < lines.length; i++) {
                  if (lines[i] === "") {
                      lines.splice(i, 1);
                  }
              }
              /*
              if (5000 < lines.length) {
                  messages.value.push("読み込んだファイルは5000件を超えています");
                  return false;
              }
              */
              for (let i = 0; i < lines.length; i++) {
                  if (lines[i] === "") {
                      continue;
                  }
                  linesArr[i] = {
                      'index': '',
                      'data': []
                  };
                  if (extension_type == 'csv') {
                      console.log(lines[i].split(/,(?=(?:[^"]*"[^"]*")*[^"]*$)/));
                      linesArr[i]['data'] = lines[i].split(/,(?=(?:[^"]*"[^"]*")*[^"]*$)/);
                      //linesArr[i] = lines[i].split(",");
                  }
                  if (extension_type == 'tsv') {
                      linesArr[i]['data'] = lines[i].split("\t");
                      //linesArr[i] = lines[i].split("\t");
                  }
                  if (headers.length !== linesArr[i]['data'].length) {
                      throw new Error( "カラム数がヘッダーと一致しません" );
                  }
                  for (let j = 0; j < linesArr[i]['data'].length; j++) {
                      if (linesArr[i]['data'][j].match(/^"/)) {
                          linesArr[i]['data'][j] = linesArr[i]['data'][j].replace(/^"/, "");
                      }
                      if (linesArr[i]['data'][j].match(/"$/)) {
                          linesArr[i]['data'][j] = linesArr[i]['data'][j].replace(/"$/, "");
                      }
                  }
                  let percent = 100;
                  if ((lines.length - 1) > 0) {
                      percent = (1 / (lines.length - 1)) * 100;
                  }
              }

              linesArr.forEach(function (line , index) {
                insertItem({
                  method: "手動",
                  itemId: '',
                  action: line['data'][0],
                  itemName: line['data'][1],
                  makerName: line['data'][2],
                  itemCode: line['data'][3],
                  itemStandard: line['data'][4],
                  itemJANCode: line['data'][5],
                  count: line['data'][6],
                  unit: line['data'][7],
                  price: line['data'][8],
                  taxrate: line['data'][9],
                });
              })
            } catch (error){
              Swal.fire({
                icon: "error",
                title: "ファイルエラー",
                text: error.message,
              });
            }  finally {
              document.getElementById('file').value = "";
              completeLoading();
            }
          };
      };
    return {
      slipDelete,
      itemRegister,
      loadCsvFile,
      dragIndex,
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