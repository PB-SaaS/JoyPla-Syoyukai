<div id="top" v-cloak>
  <header-navi></header-navi>
  <v-loading :show="loading"></v-loading>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <h1 class="text-2xl mb-2">未発注商品一覧</h1>
        <hr>
        <div class="flex my-2 lg:w-1/3">
          <div class="mb-3 w-full">
            <v-button-default type="button" class="w-full" v-on:click.native="download">未発注商品情報を出力</v-button-default>
          </div>
        </div>
        <div class="flex mb-2 lg:w-1/3">
          <div class="mb-3 w-full">
            <label for="formFile" class="form-label inline-block mb-2 text-gray-700">インポートファイル選択（ TSV / CSVファイル ）</label>
            <input
                @change="loadCsvFile"
                accept=".csv,.tsv,.txt"
                class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                type="file"
                id="formFile">
          </div>
        </div>
        <div class="flex mb-2 lg:w-1/3">
          <div class="mb-3 w-full">
            <v-button-primary type="button" class="w-full" v-on:click.native="onSubmit">未発注商品情報更新</v-button-primary>
          </div>
        </div>
        <p class="text-red-500 font-bold" v-for="msg in messages">{{ msg }}</p>
        <div class='overflow-x-scroll whitespace-nowrap md:whitespace-normal'>
          <table class='table-auto overflow-scroll w-full text-left'>
            <thead class="border-b bg-gray-800">
                <tr>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                        #
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                      発注番号
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                      発注商品番号
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                    部署名
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                    卸業者名
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                    商品名
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                    メーカー名
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                    製品コード
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                    規格
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                    JANコード
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 min-w-[100px]">
                    入数
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 min-w-[100px]">
                    在庫数
                    </th>
                    <th scope="col" class="text-sm font-medium text-white px-2 py-4 min-w-[240px]">
                    発注数
                    </th>
                </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in fields" :key="item.key" class="border-b">
                <td class="text-sm px-2 py-4 ">{{ idx + 1 }}</td>
                <td class="text-sm px-2 py-4 ">{{ item.value.orderId }}</td>
                <td class="text-sm px-2 py-4 ">{{ item.value.orderItemId }}</td>
                <td class="text-sm px-2 py-4 ">{{ item.value.division.divisionName }}</td>
                <td class="text-sm px-2 py-4 ">{{ item.value.distributor.distributorName }}</td>
                <td class="text-sm px-2 py-4 ">{{ item.value.item.itemName }}</td>
                <td class="text-sm px-2 py-4 ">{{ item.value.item.makerName }}</td>
                <td class="text-sm px-2 py-4 ">{{ item.value.item.itemCode }}</td>
                <td class="text-sm px-2 py-4 ">{{ item.value.item.itemStandard }}</td>
                <td class="text-sm px-2 py-4 ">{{ item.value.item.itemJANCode }}</td>
                <td class="text-sm px-2 py-4 ">{{ numberFormat(item.value.quantity.quantityNum) }}{{ item.value.quantity.quantityUnit }}</td>
                <td class="text-sm px-2 py-4 ">{{ numberFormat(item.value.stockQuantity) }}{{ item.value.quantity.quantityUnit }}</td>
                <td class="text-sm px-2 py-4 ">
                  <v-input-number
                  :rules="{ required: true , between: ( (item.value.rowOrderQuantity > 0)? [ 0 , 99999 ] : [ -99999 , 0 ] ) }" 
                  :name="`orderItems[${idx}].orderQuantity`"
                  label="発注数（個数）" 
                  :unit="item.value.quantity.itemUnit" 
                  :step="1" 
                  :title="`発注数（個数）/${numberFormat(item.value.quantity.quantityNum)}${ item.value.quantity.quantityUnit }入り`" 
                  @change="isChange = true" 
                  change-class-name="inputChange"
                  ></v-input-number>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script> 
  const PHPData = <?php echo json_encode($orderitems, false); ?>;

  var JoyPlaApp = Vue.createApp({
    setup() {
      const {
        ref,
        toRef,
        toRefs,
        reactive,
        onMounted
      } = Vue;
      const {
        useFieldArray,
        useForm
      } = VeeValidate;

      const loading = ref(false);
      const start = () => {
        loading.value = true;
      }

      const complete = () => {
        loading.value = false;
      }

      const sleepComplate = () => {
        window.setTimeout(function() {
          complete();
        }, 500);
      }

      start();
      
      onMounted(() => {
        sleepComplate()
      });


      const date = new Date();
      const yyyy = date.getFullYear();
      const mm = ("0" + (date.getMonth() + 1)).slice(-2);
      const dd = ("0" + date.getDate()).slice(-2);

      const {
        handleSubmit,
        control,
        meta,
        validate,
        values,
        isSubmitting
      } = useForm({
        initialValues: {
          orderItems: [],
        },
        validateOnMount: false
      });
      const {
        remove,
        insert,
        fields,
        update,
        replace
      } = useFieldArray('orderItems', control);

      const temp = PHPData.map(value => {
        value.rowOrderQuantity = value.orderQuantity
        return value;
      });
      replace(temp);
    
      const alertModel = reactive({
        message: "",
        headtext: "",
        okMethod: function() {
          console.log('')
        },
      });

      const confirmModel = reactive({
        message: "",
        headtext: "",
        okMethod: function() {
          console.log('')
        },
        cancelMethod: function() {
          console.log('')
        },
      });

      const breadcrumbs = [{
          text: '発注・入荷メニュー',
          disabled: false,
          href: _ROOT + '&path=/order',
        },
        {
          text: '一括編集',
          disabled: true,
        }
      ];

      const numberFormat = (value) => {
        if (!value) {
          return 0;
        }
        return new Intl.NumberFormat('ja-JP').format(value);
      }

      const alertSetting = toRefs(alertModel);
      const confirmSetting = toRefs(confirmModel);

      const alert = ref();
      const confirm = ref();

      const onSubmit = async () => {
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
        } else {
          Swal.fire({
            title: '発注商品の個別更新を行います。',
            text: "よろしいですか？",
            icon: 'info',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then((result) => {
            if (result.isConfirmed) {
              orderRegister();
            }
          })
        }
      };

      const updateOrderItemModel = (values) => {
        let items = values.orderItems;
        let orderItems = [];
        items.forEach(function(item, idx) {
          if(item.orderQuantity !== item.rowOrderQuantity)
          {
            orderItems.push({
              'orderItemId': item.orderItemId,
              'orderQuantity': parseInt(item.orderQuantity),
            })
          }
        });
        return orderItems;
      };

      const orderRegister = handleSubmit(async (values) => {
        try {
          const orderModels = updateOrderItemModel(values);
          if (orderModels.length === 0) {
            Swal.fire({
              icon: 'error',
              title: '登録する商品がありませんでした。',
              text: '内容を確認の上、再送信をしてください。',
            })
            return false;
          }
          
          let params = new URLSearchParams();
          params.append("path", "/api/order/item/bulkUpdate");
          params.append("_method", 'patch');
          params.append("_csrf", _CSRF);
          params.append("orderItems", JSON.stringify(encodeURIToObject(orderModels)));

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
        }
      });

      const updateItem = (idx, key, value) => {
        let object = JSON.parse(JSON.stringify(fields.value[idx].value));
        object[key] = value;
        update(idx, object);
      };

      const additem = (item) => {
        item = JSON.parse(JSON.stringify(item));
        item.orderUnitQuantity = (item.orderUnitQuantity) ? item.orderUnitQuantity : 1;
        let checked = false;
        if (Array.isArray(values.orderItems)) {
          values.orderItems.forEach((v, idx) => {
            if (v.inHospitalItemId === item.inHospitalItemId) {
              checked = true;
              v.orderUnitQuantity++;
            }
          });
        }
        if (!values.orderItems) {
          values.orderItems = [];
        }
        if (!checked) {
          item.priceNotice = (item.priceNotice) ? item.priceNotice : "";
          insert(0, item);
        }
      };

      const addOrderItems = (consumptions) => {
        consumptions = JSON.parse(JSON.stringify(consumptions));
        consumptions.forEach((elm, index) => {
          if ((typeof elm.orderableQuantity !== "undefined") && (parseInt(elm.orderableQuantity) < 1)) {
            return;
          }

          let exist = false;
          if (Array.isArray(values.orderItems)) {
            values.orderItems.forEach((v, idx) => {
              if (v.inHospitalItemId === elm.inHospitalItemId) {
                exist = true;
                let quantity = (elm.orderableQuantity > 0) ? parseInt(elm.orderableQuantity) : 0;
                v.orderUnitQuantity += quantity;
              }
            });
          }

          if (!values.orderItems) {
            values.orderItems = [];
          }

          if (!exist) {
            let orderItem = new Object();
            orderItem.orderUnitQuantity = (elm.orderableQuantity && elm.orderableQuantity > 0) ? parseInt(elm.orderableQuantity) : 0;
            orderItem.makerName = (elm.item.makerName) ? elm.item.makerName : "";
            orderItem.itemName = (elm.item.itemName) ? elm.item.itemName : "";
            orderItem.itemCode = (elm.item.itemCode) ? elm.item.itemCode : "";
            orderItem.itemStandard = (elm.item.itemStandard) ? elm.item.itemStandard : "";
            orderItem.itemJANCode = (elm.itemJANCode) ? elm.itemJANCode : "";
            orderItem.quantity = (elm.quantity.quantityNum) ? parseInt(elm.quantity.quantityNum) : 0;
            orderItem.price = (elm.price) ? parseInt(elm.price) : 0;
            orderItem.quantityUnit = (elm.quantity.quantityUnit) ? elm.quantity.quantityUnit : "";
            orderItem.itemUnit = (elm.quantity.itemUnit) ? elm.quantity.itemUnit : "";
            orderItem.cardId = (elm.cardId) ? elm.cardId : "";
            orderItem.itemId = (elm.item.itemId) ? elm.item.itemId : "";
            orderItem.inHospitalItemId = (elm.inHospitalItemId) ? elm.inHospitalItemId : "";
            orderItem.serialNo = (elm.item.serialNo) ? elm.item.serialNo : "";
            orderItem.catalogNo = (elm.item.catalogNo) ? elm.item.catalogNo : "";
            orderItem.inItemImage = (elm.itemImage) ? elm.itemImage : "";
            orderItem.priceNotice = (elm.priceNotice) ? elm.priceNotice : "";
            insert(0, orderItem);
          }
        });
      };
    
      const openModal = ref();
      const selectInHospitalItems = ref([]);
      const addItemByBarcode = (items) => {
        selectInHospitalItems.value = [];
        if (!items.item || items.item.length === 0) {
          Swal.fire({
            icon: 'info',
            title: '商品が見つかりませんでした',
          });
          return false;
        }

        if (items.type == "received") {
          items.item.forEach((x, id) => {
            items.item[id].orderUnitQuantity = 1;
          });
        }
        
        if (items.type == "payout") {
          items.item.forEach((x, id) => {
            items.item[id].orderUnitQuantity = Math.ceil(parseInt(items.item[id].payoutQuantity) / parseInt(items.item[id].quantity));
          });
        }
        if (items.type == "card") {
          items.item.forEach((x, id) => {
            items.item[id].orderUnitQuantity = Math.ceil(parseInt(items.item[id].cardQuantity) / parseInt(items.item[id].quantity));
          });
        }
        if (items.type == "customlabel") {
          items.item.forEach((x, id) => {
            items.item[id].orderUnitQuantity = Math.ceil(parseInt(items.item[id].customQuantity) / parseInt(items.item[id].quantity));
          });
        }

        if (items.item.length === 1) {
          if (items.item[0].divisionId) {
            if (values.divisionId !== items.item[0].divisionId) {
              Swal.fire({
                icon: 'error',
                title: 'エラー',
                text: '読み込んだ値と選択している部署が一致しませんでした',
              });
              return false;
            }
          }
          additem(items.item[0]);
        } else {
          selectInHospitalItems.value = items.item;
          openModal
            .value
            .open();
        }
      }
      const messages = ref([]);
      const filename = ref('');
      const headers = ['id', '発注商品番号', '部署名', '卸業者名', '商品名', 'メーカー名', '製品コード', '規格', 'JANコード','入数','入数単位','在庫数','在庫単位','発注数','発注単位'];
      
      const download = () => {
        let result = [];
        let count = 1;
        fields.value.forEach((orderItem) => {
          result.push(
            [
              count,
              orderItem.value.orderItemId,
              orderItem.value.division.divisionName,
              orderItem.value.distributor.distributorName,
              orderItem.value.item.itemName,
              orderItem.value.item.makerName,
              orderItem.value.item.itemCode,
              orderItem.value.item.itemStandard,
              orderItem.value.item.itemJANCode,
              orderItem.value.quantity.quantityNum,
              orderItem.value.quantity.quantityUnit,
              orderItem.value.stockQuantity,
              orderItem.value.quantity.quantityUnit,
              orderItem.value.rowOrderQuantity,
              orderItem.value.quantity.itemUnit,
            ]
          );
          count++
        });

        result.unshift(headers);
        exportTSV(result);
      }
      
      const exportTSV = (records) => {
          let data = records.map((record) => record.join('\t')).join('\r\n');
          data = Encoding.stringToCode(data);
          let shiftJisCodeList = Encoding.convert(data, 'sjis', 'unicode');
          let uInt8List = new Uint8Array(shiftJisCodeList);

          //let bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
          let blob = new Blob([uInt8List], {
              type: 'text/tab-separated-values'
          });
          let url = (window.URL || window.webkitURL).createObjectURL(blob);
          let link = document.createElement('a');
          link.download = 'orderitems_<?php echo date('Ymd'); ?>.tsv';
          link.href = url;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
      }

      const changeOrderQuantity = ( orderItemId , orderQuantity ) => {
        const idx = fields.value.findIndex( val => val.value.orderItemId === orderItemId );
        if(idx > -1){
          updateItem(idx, 'orderQuantity', orderQuantity)
        } else {
          messages.value.push("発注商品番号：" + orderItemId + " は見つかりませんでした");
        }
      }

     const loadCsvFile = (e) => {
          messages.value = [];
          let file = e
              .target
              .files[0];

          filename.value = file.name;
          let extension = file
              .name
              .split('.')
              .pop();

          if (!file.type.match("text/csv") && !file.type.match("application/vnd.ms-excel") && (extension != 'tsv' && extension != 'txt')) {
              messages
                  .value
                  .push("CSV/TSV ファイルを選択してください");
              return;
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
          reader.onload = function (e) {
              start();
              // 8ビット符号なし整数値配列と見なす
              var array = new Uint8Array(e.target.result);

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
              var unicodeArray = Encoding.convert(array, 'UNICODE');
              // Unicodeの数値配列を文字列に変換
              var text = Encoding.codeToString(unicodeArray);

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
                      messages.value.push("カラム数がヘッダーと一致しません");
                      complete();
                      return false;
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
                if(index !== 0 && line.data[1] !== ''){ 
                  changeOrderQuantity(line.data[1],line.data[13]); 
                }
              })
              
              complete();
              
          };
      };

      return {
        values,
        openModal,
        selectInHospitalItems,
        addItemByBarcode,
        loading, 
        start, 
        complete,
        isSubmitting,
        alert,
        confirm,
        additem,
        onSubmit,
        breadcrumbs,
        alertSetting,
        confirmSetting,
        numberFormat,
        meta,
        fields,
        remove,
        validate,
        addOrderItems,
        download,
        loadCsvFile,
        messages
      };
    },
    watch: {
      isSubmitting() {
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
      'v-open-modal': vOpenModal,
      'v-barcode-search': vBarcodeSearch,
      'v-switch': vSwitch,
      'v-loading': vLoading,
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
      'v-in-hospital-item-modal': vInHospitalItemModal,
      'header-navi': headerNavi,
      'blowing': blowing,
      'v-consumption-history-modal-for-order': vConsumptionHistoryModalForOrder
    },
  }).mount('#top');
</script> 