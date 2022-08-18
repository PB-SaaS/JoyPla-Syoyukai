<div id="top" v-cloak="v-cloak">
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto mb-96">
                <h1 class="text-2xl mb-2">検収書</h1>
                <hr>
                <div class="p-4 text-base bg-gray-100 border border-gray-400 flex flex-col md:flex-row md:gap-6 gap-4 mb-6">
                    <?php if(gate('register_return_slips')->can()): ?>
                    <v-button-danger type="button" class="md:w-1/6 w-full" :disabled="! isChange" @click.native="onRegister">返品登録</v-button-danger>
                    <?php endif ?>
                    <v-button-default type="button" class="md:w-1/6 w-full" @click.native="label( received.receivedId )">ラベルプリント</v-button-default>
                </div>
                <div class="p-4 text-base bg-gray-100 border border-gray-400">
                    <v-text title="登録日" class="flex w-full gap-6">{{ received.registDate }}</v-text>
                    <v-text title="発注番号" class="flex w-full gap-6">{{ received.orderId }}</v-text>
                    <v-text title="入庫番号" class="flex w-full gap-6">{{ received.receivedId }}</v-text>
                    <v-text title="発注元部署" class="flex w-full gap-6">{{ received.division.divisionName }}</v-text>
                </div>
                <hr>
                <div class="p-4 text-lg font-bold">
                    <div class="flex w-full gap-6">
                        <div class="flex-initial lg:w-1/6 w-1/3">合計金額</div>
                        <div class="flex-auto">&yen;
                            {{ numberFormat( received.totalAmount) }}</div>
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
                                                <p class="text-md text-gray-900" v-if="( item.value.lot.lotNumber != '' && item.value.lot.lotDate != '' )">
                                                    ロット情報：{{ item.value.lot.lotNumber }} / {{ item.value.lot.lotDate }} 
                                                </p>
                                                <div>
                                                    <span class="text-blue-700 text-lg mr-4">&yen;
                                                        {{ numberFormat(item.value.receivedPrice) }}</span>
                                                    <span class="text-sm text-gray-900">( &yen;
                                                        {{ numberFormat(item.value.price) }}
                                                        /
                                                        {{ item.value.quantity.itemUnit }}
                                                        )</span>
                                                </div>
                                            </div>
                                            <div class="flex-auto lg:w-2/5 w-full">
                                                <div class="md:flex gap-6 ">
                                                    <div class="font-bold w-32">入荷数</div>
                                                    <div>{{ numberFormat(item.value.receivedQuantity) }}
                                                        {{ item.value.quantity.itemUnit }}</div>
                                                </div>
                                                <div class="md:flex gap-6 " v-if="item.value.returnQuantity > 0">
                                                    <div class="font-bold w-32">返品数</div>
                                                    <div>{{ numberFormat(item.value.returnQuantity) }}
                                                        {{ item.value.quantity.itemUnit }}</div>
                                                </div>
                                                <?php if(gate('register_return_slips')->can()): ?>
                                                <div class="p-4 text-base bg-gray-100 border border-gray-400 my-2" v-if="( item.value.receivedQuantity - item.value.returnQuantity ) > 0">
                                                    <v-input-number
                                                        :rules="{ between: [ 1 , ( item.value.receivedQuantity - item.value.returnQuantity) ] }"
                                                        :name="`receivedItems[${idx}].returnQuantityEdit`"
                                                        label="返品数"
                                                        :unit="item.value.quantity.itemUnit"
                                                        :step="1"
                                                        @change="isChange = true"
                                                        change-class-name="inputChange"
                                                        title="返品数"></v-input-number>
                                                </div>
                                                <?php endif ?>
                                            </div>
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
<script>

const PHPData = <?php echo json_encode($viewModel, true) ?>;

var JoyPlaApp = Vue
.createApp({
components: {
    'v-barcode-search': vBarcodeSearch,
    'v-input': vInput,
    'v-text': vText,
    'v-select': vSelect,
    'v-button-danger': vButtonDanger,
    'v-button-default' : vButtonDefault,
    'v-button-primary': vButtonPrimary,
    'v-checkbox': vCheckbox,
    'v-loading': vLoading,
    'header-navi': headerNavi,
    'v-breadcrumbs': vBreadcrumbs,
    'item-view': itemView,
    'v-input-number': vInputNumber,
    'v-open-modal': vOpenModal
},
setup() {
    const {ref, onCreated, onMounted} = Vue;
    const {useFieldArray, useForm} = VeeValidate;

    const received = PHPData.received;

    const openModal = ref();
    const selectInHospitalItems = ref([]);

    const {
        handleSubmit,
        control,
        meta,
        validate,
        values,
        isSubmitting
    } = useForm({
        initialValues: {
            'receivedId': received.receivedId,
            'adjustment': received.adjustment,
            'receivedItems': received
                .receivedItems
                .map((x, idx) => {
                    x.receivedQuantity = parseInt(x.receivedQuantity);
                    x.returnQuantityEdit = 0;
                    return x;
                })
        },
        validateOnMount: false
    });

    const {remove, push, fields, update, replace} = useFieldArray(
        'receivedItems',
        control
    );

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

    onMounted(() => {
        sleepComplate();
    });
    const breadcrumbs = [
        {
            text: '発注メニュー',
            disabled: false,
            href: _ROOT + '&path=/order'
        }, {
            text: '検収書一覧',
            disabled: false,
            href: _ROOT + '&path=/received/show&isCache=true' 
        }, {
            text: '検収書',
            disabled: true
        }
    ];

    const numberFormat = (value) => {
        if (!value) {
            return 0;
        }
        return value
            .toString()
            .replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g, '$1,');
    };
    const isChange = ref(false);

    const createRegisterModel = () => {
        return values.receivedItems.map(x => {
          return {
            'receivedItemId': x.receivedItemId,
            'returnQuantity': x.returnQuantityEdit,
          };
        });
    }

    const onRegister = async () => {
        const {valid, errors} = await validate();

        if (!valid) {
            Swal.fire({icon: 'error', title: '入力エラー', text: '入力エラーがございます。ご確認ください'})
        } else {
            Swal
                .fire({
                    title: '返品登録',
                    text: "返品登録を行います。\r\nよろしいですか？\r\n業者への通知は行いません",
                    icon: 'warning',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                })
                .then(async (result) => {
                    try {
                        if (result.isConfirmed) {
                            const returnItems = createRegisterModel();
                            let params = new URLSearchParams();
                            params.append("path", "/api/" + values.receivedId + "/return/register");
                            params.append("_csrf", _CSRF);
                            params.append("returnItems", JSON.stringify(encodeURIToObject(returnItems)));

                            const res = await axios.post(_APIURL, params);

                            if (res.data.code != 200) {
                                throw new Error(res.data.message)
                            }

                            Swal
                                .fire({icon: 'success', title: '返品登録が完了しました。'})
                                .then((result) => {
                                    location.reload();
                                });
                            return true;
                        }
                    } catch (error) {
                        Swal.fire(
                            {icon: 'error', title: 'システムエラー', text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。'}
                        );
                    }
                })
        }
    };

    const isRequired = (idx) => {
        if (fields.value[idx].value.lotManagement == "1") {
            return true;
        }
        return false;
    };

    const addItem = (idx, item) => {
        isChange.value = true ; 
        if (!fields.value[idx].value.receiveds) {
            fields
                .value[idx]
                .value
                .receiveds = [];
        }

        fields
            .value[idx]
            .value
            .receiveds
            .push(
                {'receivedQuantity': 1, 'lotNumber': item.lotNumber, 'lotDate': item.lotDate}
            );
    };
    const addItemByBarcode = (items) => {
        selectInHospitalItems.value = [];
        if (items.length === 0) {
            return false;
        }

        let inHospitalitems = fields
            .value
            .map((x, xKey) => {
                let itemdata = items.find(
                    (y) => (x.value.inHospitalItemId === y.inHospitalItemId)
                );
                if (itemdata) {
                    return {id: xKey, item: itemdata}
                }
            })
            .filter(x => x);

        if (inHospitalitems.length === 1) {
            addItem(inHospitalitems[0].id, inHospitalitems[0].item);
        } else {
            selectInHospitalItems.value = inHospitalitems;
            openModal
                .value
                .open();
        }

    };
    const addReceived = (idx) => {
        isChange.value = true ; 
        if (fields.value[idx].value.receivedItemReceivedStatus == "3") {
            return null;
        }
        if (!fields.value[idx].value.receiveds) {
            fields
                .value[idx]
                .value
                .receiveds = [];
        }
        fields
            .value[idx]
            .value
            .receiveds
            .push({'receivedQuantity': 0, 'lotNumber': "", 'lotDate': ""});
    };

    const deleteReceived = (idx, ridx) => {
        let result = fields
            .value[idx]
            .value
            .receiveds
            .filter((value, index) => {
                if (index === ridx) {
                    return;
                }
                return value;
            })
            .filter(e => e);
        fields
            .value[idx]
            .value
            .receiveds = (result)
                ? result
                : [];
    };

    const label = ( receivedId ) => {
        let form = document.createElement('form');
        form.action = '%url/rel:mpgt:ReceivingLabel%';
        form.method = 'post';
        form.classList = 'hidden';
        form.target = "_blank";

        let q = document.createElement('input');
        q.value = receivedId;
        q.name = 'receivingId';

        form.appendChild(q);
        document.body.appendChild(form);

        form.submit();
        //location.href = _ROOT + "&path=/received/" + url + "/label";    
      }

    return {
        label,
        selectInHospitalItems,
        openModal,
        addItem,
        addItemByBarcode,
        deleteReceived,
        addReceived,
        isRequired,
        onRegister,
        isChange,
        numberFormat,
        received,
        fields,
        breadcrumbs,
        loading,
        start,
        complete
    }
},
})
.mount('#top');
</script>