<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto w-full">
      <div class="index px-1 w-full mx-auto mb-96">
        <h1 class="text-2xl mb-2">商品リストの詳細</h1>
        <hr>
        <div class="p-4 text-base bg-gray-100 border border-gray-400 flex flex-col md:flex-row md:gap-6 gap-4 mb-6">
          <v-button-primary type="button" class="md:w-1/6 w-full" @click.native="itemRegister">
            商品リストを更新
          </v-button-primary>
          <v-button-danger type="button" class="md:w-1/6 w-full" @click.native="slipDelete">
            商品リストを削除
          </v-button-danger>
          <v-button-default type="button" class="md:w-1/6 w-full" @click.native="openPrint( values.itemList?.itemListId )">
            商品リストを印刷
          </v-button-default>
          <v-button-primary type="button" class="md:w-1/6 w-full" @click.native="downloadList">
            商品リストをダウンロード
          </v-button-primary>
        </div>
        <div class="p-4 text-base bg-gray-100 border border-gray-400">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">商品リストID</div>
            <div class="flex-auto">{{ values.itemList?.itemListId }}</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">商品リスト名称</div>
            <div class="flex-auto">
              <v-input
                name="itemListName"
                type="text"
                ></v-input>
            </div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">部署</div>
            <div class="flex-auto">{{ values.itemList?._division?.divisionName }}</div>
          </div>
        </div>
        <hr>
        <div class="text-base">
          <div class="lg:flex w-full gap-6">
            <div class="flex-auto w-full">
              <div class="p-4 text-lg font-bold">商品情報</div>
              <div class="overflow-x-auto w-full">
                <table class="table-auto text-sm w-full whitespace-nowrap">
                  <thead>
                    <tr>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">No</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">商品名</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">メーカー名</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">製品コード</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">規格</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">JANコード</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">卸業者</th>
                      <th class="border-b font-medium p-2 pr-4 pt-0 pb-3 text-left">入数</th>
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
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.itemName }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.makerName }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.itemCode }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.itemStandard }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.itemJANCode }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.distributorName }}</td>
                      <td class="border-b border-slate-100 p-2 pr-4 text-slate-500">{{ item.quantity }}{{ item.quantityUnit }}/{{ item.itemUnit }}</td>
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
              <div class="lg:flex w-full gap-6">
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

const itemListId = '<?php echo $itemListId; ?>';
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
const vBarcode = {
    //vue-barcodeを参考に作成
    props:{
        value:{
            type:String,
            default:undefined,
        },
        options: {
            type: Object,
            default: undefined,
        },
    },
    watch: {
        $props: {
            deep: true,
            immediate: true,

            handler() {
                if (this.$el) {
                    this.generate();
                }
            },
        },
    },
    mounted() {
        this.generate();
    },
    methods:{
        generate(){
            JsBarcode(this.$el, String(this.value), this.options);
        }
    },
    render(){
        return Vue.h("svg", this.$slots.default);
    }
}

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
      'v-barcode' : vBarcode,
    },
    setup(){
      const {ref , onCreated , onMounted} = Vue;
      const {useFieldArray, useForm} = VeeValidate;

      const { handleSubmit , control, meta , validate , values , isSubmitting } = useForm({
          initialValues: {
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
            items : [],
            itemListName : "",
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
          text: '商品メニュー',
          disabled: false,
          href: _ROOT+'&path=/product',
        },
        {
          text: '商品リスト',
          disabled: false,
          href: _ROOT+'&path=/product/itemList/index&isCache=true',
        },
        {
          text: '商品リストの詳細',
          disabled: true,
        }
      ];

      const getSlipData = async ( itemListId ) => 
      {
        startLoading();
        
        let params = new URLSearchParams();
        params.append("path", "/api/product/itemList/"+itemListId);
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
        const response = await getSlipData(itemListId);
        values.itemList = response.data;
        values.itemListName = response.data.itemListName;
        replace(response.data.items.sort((a, b) => a.index - b.index));

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
          itemListRowId: (typeof(item.itemListRowId) ? null : item.itemListRowId),
          itemId: item.itemId,
          inHospitalItemId: item.inHospitalItemId,
          itemName: item.itemName,
          makerName: item.makerName,
          itemCode: item.itemCode,
          itemStandard: item.itemStandard,
          itemJANCode: item.itemJANCode,
          quantity: item.quantity,
          quantityUnit: item.quantityUnit,
          itemUnit: item.itemUnit,
          itemLabelBarcode: item.itemLabelBarcode,
          distributorId: item.distributorId,
          distributorName: item.distributorName,
        });
      }

      const makeLabelBarcode = (labelId, quantity) => 
      {
        let quantityNum = '';
        if(quantity >= 10000 ){
          quantityNum = '9999';
        } else {
          if(quantity < 1 ){
            quantity = 1
          }
          quantity = quantity.toString();
          quantityNum = quantity.padStart(4, "0");
        }
        $labelBarcode = "01"+labelId+quantityNum;
        return $labelBarcode;
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

      const slipDelete = () => {
          Swal.fire({
            title: '商品リストを削除',
            text: "削除後は元に戻せません。\r\nよろしいですか？",
            icon: 'warning',
            confirmButtonText: '削除します',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
          }).then( async (result) => {
            if(result.isConfirmed){
              try {
                startLoading();
                let params = new URLSearchParams();
                params.append("path", "/api/product/itemList/"+itemListId+"/delete");
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
                  location.href = _ROOT+'&path=/product/itemList/index&isCache=true';
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
            }
          });
        };

      const itemRegister = handleSubmit(async (values) => {
        try {
          startLoading();
          let params = new URLSearchParams();
          params.append("path", "/api/product/itemList/"+itemListId+"/update");
          params.append("_method", 'patch');
          params.append("_csrf", _CSRF);
          params.append("itemList", JSON.stringify(encodeURIToObject(
            {
              'items' : values.items ?? []
            })));
          params.append("itemListName", encodeURI(values.itemListName));

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

      const deleteItem = (index) => {
        remove(index);
      }

      const addInHospitalItem =  (elem) => {
        register = {
          itemId : elem.itemId,
          makerName : elem.makerName,
          itemName : elem.itemName,
          itemCode : elem.itemCode,
          itemStandard : elem.itemStandard,
          itemJANCode : elem.itemJANCode,
          price : elem.price,
          unitPrice : elem.unitPrice,
          quantity : elem.quantity,
          quantityUnit : elem.quantityUnit,
          itemUnit : elem.itemUnit,
          inHospitalItemId : elem.inHospitalItemId,
          distributorId : elem.distributorId,
          distributorName : elem.distributorName,
          itemLabelBarcode : makeLabelBarcode(elem.labelId, elem.quantity),
        }
        insertItem(register)
        MicroModal.close("inHospitalItemModal", {// モーダルを閉じる処理
          awaitCloseAnimation: true // 開閉時のアニメーションを可能に
        });
      }

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
          const headers = ['院内商品ID'];

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

              let inHospitalItemIds = [];
              linesArr.forEach(function (line , index) { 
                inHospitalItemIds.push(line['data'][0]);
              });

              //APIをたたく
              try {
                startLoading();
                let params = new URLSearchParams();
                params.append("path", "/api/inHospitalItem/show");
                params.append("_method", 'get');
                params.append("_csrf", _CSRF);
                params.append("search", JSON.stringify(encodeURIToObject(
                  {
                    'inHospitalItemIds' : inHospitalItemIds ?? []
                  })));

                axios.post(_APIURL, params).then((res) => {
                  console.log(res);
                  if (res.data.code != 200) {
                    throw new Error(res.data.message)
                  }
                  if (res.data.count == 0) {
                    throw new Error( "該当する院内商品が存在しませんでした。" );
                  }

                  Swal.fire({
                    icon: 'success',
                    title: '読み込みが完了しました。',
                  });

                  let inHospitalItems = res.data.data;
                  inHospitalItems.forEach(function(line, index){
                    line.itemLabelBarcode = makeLabelBarcode(line.labelId, line.quantity);
                    insertItem(line);
                  });

                }).catch((err) => {
                  console.log(err);
                  Swal.fire({
                    icon: "error",
                    title: "ファイルエラー",
                    text: err.message,
                  });
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

      const openPrint = ( url ) => {
        location.href = _ROOT + "&path=/product/itemList/" + url + "/print";    
      }

      const downloadList = handleSubmit(async(values) => {
        let content = 'No.\t院内商品ID\t商品名\tメーカー名\t製品コード\t規格\tJANコード\t入数\t入数単位\t個数単位\t卸業者\r\n';
        let rowNum = 0;
        for (const key of values.items) {
          rowNum++;
          content += rowNum + "\t" + key.inHospitalItemId + "\t" + key.itemName + "\t" + key.makerName + "\t" + key.itemCode + "\t" + key.itemStandard + "\t" + key.itemJANCode + "\t" + key.quantity.toString() + "\t" + key.quantityUnit + "\t" + key.itemUnit + "\t" + key.distributorName + "\r\n" ;
        }
        let blob = new Blob([content], {type: "text/plain"});
        let blobUrl = window.URL.createObjectURL(blob);
        let obj = document.createElement("a");
        obj.href = blobUrl;
        obj.download = values.itemListName+".txt";
        document.body.appendChild(obj);
        obj.click();
        obj.parentNode.removeChild(obj);
      })

    return {
      slipDelete,
      itemRegister,
      loadCsvFile,
      dragIndex,
      dragHandlers,
      addInHospitalItem,
      openRegistPage,
      addItem: addItemHandler,
      deleteItem,
      values,
      fields,
      numberFormat,
      breadcrumbs,
      loading,
      startLoading,
      completeLoading,
      makeLabelBarcode,
      openPrint,
      downloadList,
    };
  },
}).mount("#top");
</script> 