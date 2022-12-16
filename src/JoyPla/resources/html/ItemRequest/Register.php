<div id="top" v-cloak>
    <header-navi></header-navi>
    <v-loading :show="loading"></v-loading>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto mb-96">
                <h1 class="text-2xl mb-2">個別請求</h1>
                <hr>
                <div>
                    <div class="my-4 mt-4 lg:w-1/3">
                        <v-select-division name="sourceDivisionId" label="請求元部署" :rules="{ required : true }" title="請求元部署指定" :disabled="values.sourceDivisionId != '' && fields.length > 0" :is-only-my-division="<?php var_export(gate('register_of_item_requests')->isOnlyMyDivision()); ?>" />
                    </div>
                    <div class="my-4 lg:w-1/3">
                        <v-select-division name="targetDivisionId" label="請求先部署" :rules="{ required : true }" title="請求先部署指定" :disabled="values.targetDivisionId != '' && fields.length > 0" />
                    </div>
                    <div class="my-4 lg:w-1/3">
                        <v-select :options="[{ label: '個別請求', value: 1 },{ label: '消費請求', value: 2 }]" name="requestType" :rules="{required: true}" title="請求タイプ" label="請求タイプ"></v-select>
                    </div>
                    <div class="lg:flex lg:flex-row gap-4">
                        <div class="my-4 w-1/3 lg:w-1/6">
                            <v-button-default class="w-full" type="button" data-micromodal-trigger="inHospitalItemModal">商品検索</v-button-default>
                            <v-in-hospital-item-modal v-on:additem="additem" :unit-price-use="payoutUnitPriceUseFlag">
                            </v-in-hospital-item-modal>
                        </div>
                        <div class="my-4 w-1/3 lg:w-1/6">
                            <v-button-default class="w-full" type="button" :disabled="values.sourceDivisionId == ''" data-micromodal-trigger="consumptionHistoryModalForItemRequest">伝票検索</v-button-default>
                            <v-consumption-history-modal-for-item-request v-on:addconsumptions="addconsumptions" :source-division-id="values.sourceDivisionId">
                            </v-consumption-history-modal-for-item-request>
                        </div>
                    </div>

                    <div class="p-2 bg-gray-300">
                        <v-barcode-search @additem="addItemByBarcode"></v-barcode-search>
                    </div>
                    <div class="my-2" v-if="fields.length == 0">
                        <div class="max-h-full h-full grid place-content-center w-full lg:flex border border-sushi-600 bg-white mt-3">
                            <div class="flex-1 p-4 relative text-center">商品を選択または、バーコードを読み取ってください</div>
                        </div>
                    </div>
                    <div class="mt-4 p-4 shadow-md drop-shadow-md" v-if="fields.length > 0">
                        <p class=" text-xl">登録アイテム数: {{ numberFormat(itemCount()) }} アイテム</p>
                        <p class=" text-xl">合計金額: &yen; {{ numberFormat(totalAmount()) }} </p>
                        <v-button-primary type="button" class="w-full" @click.native="onSubmit">請求登録</v-button-primary>
                    </div>
                    <transition-group tag="div" name="list" appear>
                        <div class="my-2" v-for="(item, idx) in fields" :key="item.key">
                            <div class="w-full lg:flex mt-3">
                                <div class="flex-auto lg:flex-1 flex lg:w-3/4">
                                    <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="item.value.inItemImage"></item-view>
                                    <div class="flex-1 px-4 relative">
                                        <div class="flex-auto lg:flex justify-between leading-normal lg:space-y-0 space-y-4 gap-6">
                                            <div class="break-all">
                                                <div class="w-full">
                                                    <h3 class="text-xl font-bold font-heading" v-if="item.value.makerName">{{ item.value.makerName }}</h3>
                                                    <p class="text-md font-bold font-heading" v-if="item.value.itemName">{{ item.value.itemName }}</p>
                                                    <p class="text-gray-500" v-if="item.value.itemCode">{{
                                                        item.value.itemCode }}</p>
                                                    <p class="text-gray-500" v-if="item.value.itemStandard">{{
                                                        item.value.itemStandard }}</p>
                                                    <p class="text-gray-500" v-if="item.value.itemJANCode">{{
                                                        item.value.itemJANCode }}</p>
                                                    <p class="text-gray-500" v-if="item.value.quantity && item.value.quantityUnit">
                                                        {{ item.value.quantity }}/{{item.value.quantityUnit }}入り</p>
                                                </div>
                                                <div class="w-full text-lg font-bold font-heading flex gap-6">
                                                    <span class="text-xl text-orange-600 font-bold font-heading">&yen;
                                                        {{ numberFormat(item.value.unitPrice) }}/{{
                                                        item.value.quantityUnit
                                                        }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="lg:flex-1 lg:w-1/4">
                                    <div class="lg:flex gap-6 items-end flex-row-reverse">
                                        <div class="lg:w-1/2">
                                            <v-input-number :rules="{ between: [0 , 99999] }" :name="`requestItems[${idx}].requestUnitQuantity`" label="請求数（個数）" :min="0" :unit="item.value.itemUnit" :step="1" :title="`請求数（個数）/${item.value.quantity}${ item.value.quantityUnit }入り`"></v-input-number>
                                        </div>
                                    </div>
                                    <div class="lg:flex gap-6 items-end flex-row-reverse">
                                        <div class="lg:w-1/2">
                                            <v-input-number :rules="{ between: [0 , 99999] }" :name="`requestItems[${idx}].requestQuantity`" label="請求数（入数）" :min="0" :unit="item.value.quantityUnit" :step="1" title="請求数（入数）"></v-input-number>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="mt-4 flex">
                                <v-button-danger type="button" @click.native="remove(idx)">削除</v-button-danger>
                                <div class="flex-1 items-center ">
                                    <p class="text-xl text-gray-800 font-bold font-heading text-right">&yen; {{
                                        numberFormat(requestPrice(idx) ) }} ( {{ requestQuantity(idx) }}{{
                                        item.value.quantityUnit }} )</p>
                                </div>
                            </div>
                            <div class="pt-4 pb-2 w-full">
                                <div class="border-t border-gray-200"></div>
                            </div>
                        </div>
                    </transition-group>
                </div>
            </div>
        </div>
    </div>
    <v-open-modal ref="openModal" headtext="商品選択" id="openModal">
        <div class="flex flex-col" style="max-height: 68vh;">
            <div class="overflow-y-scroll my-6">
                <div class="w-full mb-8 xl:mb-0">
                    <div class="hidden lg:flex w-full sticky top-0 bg-white py-4 flex-wrap">
                        <div class="w-full lg:w-5/6">
                            <h4 class="font-bold font-heading text-gray-500 text-center">商品情報</h4>
                        </div>
                        <div class="w-full lg:w-1/6">
                            <h4 class="font-bold font-heading text-gray-500 text-center">反映</h4>
                        </div>
                    </div>
                    <div class="lg:pt-0 pt-4">
                        <div class="flex flex-wrap items-center mb-3" v-for="(elem, index) in selectInHospitalItems">
                            <div class="w-full lg:w-5/6 lg:px-4 px-0 mb-6 lg:mb-0">
                                <div class="flex flex-wrap items-center gap-4">
                                    <div class="flex-none">
                                        <item-view class="md:h-44 md:w-44 h-32 w-32" :base64=""></item-view>
                                    </div>
                                    <div class="break-words flex-1 box-border w-44">
                                        <h3 class="text-xl font-bold font-heading">{{ elem.makerName }}</h3>
                                        <p class="text-md font-bold font-heading">{{ elem.itemName }}</p>
                                        <p class="text-gray-500">{{ elem.itemCode }}<br>{{ elem.itemStandard }}</p>
                                        <p class="text-gray-500">{{ elem.quantity }}{{ elem.quantityUnit }}
                                            入り</p>
                                        <p>
                                            <span class="text-xl text-orange-600 font-bold font-heading">&yen;
                                                {{ numberFormat(elem.price) }}</span>
                                            <span class="text-gray-400">
                                                ( &yen;
                                                {{ numberFormat(elem.unitPrice) }}/{{ elem.quantityUnit }}
                                                )</span>
                                        </p>
                                        <p class="text-gray-800">ロット番号：{{ elem.lotNumber }}</p>
                                        <p class="text-gray-800">使用期限：{{ elem.lotDate }}</p>
                                        <p class="text-gray-800">{{ elem.distributorName }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full lg:block lg:w-1/6 px-4 py-4">
                                <v-button-default type="button" class="w-full" v-on:click.native="additem(elem)">反映</v-button-default>
                            </div>
                            <div class="py-2 px-4 w-full">
                                <div class="border-t border-gray-200"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </v-open-modal>

</div>

<script>
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
            const payoutUnitPriceUseFlag =
                "<?php echo $payoutUnitPriceUseFlag; ?>";

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

            /*
                        const date = new Date();
                        const yyyy = date.getFullYear();
                        const mm = ("0" + (date.getMonth() + 1)).slice(-2);
                        const dd = ("0" + date.getDate()).slice(-2);
            */
            const {
                handleSubmit,
                control,
                meta,
                validate,
                values,
                isSubmitting
            } = useForm({
                initialValues: {
                    requestItems: [],
                    sourceDivisionId: "",
                    targetDivisionId: "",
                    requestType: 1
                },
                validateOnMount: false
            });
            const {
                remove,
                push,
                fields,
                update,
                replace
            } = useFieldArray('requestItems', control);

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
                    text: '請求メニュー',
                    disabled: false,
                    href: _ROOT + '&path=/itemrequest',
                },
                {
                    text: '個別請求',
                    disabled: true,
                }
            ];

            const createItemRequestModel = (values) => {
                let items = values.requestItems;
                let requestItems = [];
                items.forEach(function(item, idx) {
                    if (requestQuantity(idx) > 0) {
                        requestItems.push({
                            'inHospitalItemId': item.inHospitalItemId,
                            'requestQuantity': item.requestQuantity,
                            'sourceDivisionId': values.sourceDivisionId,
                            'targetDivisionId': values.targetDivisionId,
                            'requestType': values.requestType
                        })
                    }
                });
                return requestItems;
            };

            const requestQuantity = (idx) => {
                let num = 0;
                if (values.requestItems[idx].requestQuantity) {
                    num += parseInt(values.requestItems[idx].requestQuantity)
                };
                if (values.requestItems[idx].requestUnitQuantity) {
                    num += parseInt(values.requestItems[idx].quantity * parseInt(values.requestItems[idx]
                        .requestUnitQuantity))
                };
                return num;
            };

            const requestPrice = (idx) => {
                return values.requestItems[idx].unitPrice * requestQuantity(idx);
            };

            const totalAmount = () => {
                let num = 0;
                values.requestItems.forEach((v, idx) => {
                    num += requestPrice(idx);
                });
                return num;
            };

            const itemCount = () => {
                let num = 0;
                values.requestItems.forEach((v, idx) => {
                    num += (requestQuantity(idx) > 0) ? 1 : 0;
                });
                return num;
            };

            const numberFormat = (value) => {
                if (!value) {
                    return 0;
                }
                return new Intl.NumberFormat('ja-JP').format(value);
                //return new Intl.NumberFormat('ja-JP').format(value);
            }
            /*
                        const isRequired = (idx) => {
                            if (fields.value[idx].value.lotManagement == "1") {
                                return true;
                            }
                            return false;
                        };
            */
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
                        title: '確認',
                        text: "請求登録を行います。よろしいですか？",
                        icon: 'info',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            register();
                        }
                    })
                }
            };

            const register = handleSubmit(async (values) => {
                try {
                    const itemRequestModels = createItemRequestModel(values);
                    if (itemRequestModels.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: '登録する商品がありませんでした。',
                            text: '内容を確認の上、再送信をしてください。',
                        })
                        return false;
                    }

                    let params = new URLSearchParams();
                    params.append("path", "/api/itemrequest/register");
                    params.append("_method", 'post');
                    params.append("_csrf", _CSRF);
                    params.append("requestItems", JSON.stringify(encodeURIToObject(
                        itemRequestModels)));
                    params.append("requestType", values.requestType);
                    const res = await axios.post(_APIURL, params);

                    if (res.data.code != 200) {
                        throw new Error(res.data.message)
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '登録が完了しました。',
                    }).then((result) => {
                        replace([]);
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
                let object = JSON.parse(JSON.stringify(fields[idx].value));
                object[key] = value;
                update(idx, object);
            };

            const searchCardId = (cardId) => {
                return fields.value.find((x) =>
                    (x.value.cardId === cardId)
                );
            }

            const additem = (item) => {
                item = JSON.parse(JSON.stringify(item));
                let checked = false;
                if (Array.isArray(values.requestItems)) {
                    values.requestItems.forEach((v, idx) => {
                        if (v.inHospitalItemId === item.inHospitalItemId) {
                            checked = true;
                            let quantity = (item.requestQuantity) ? parseInt(item.requestQuantity) :
                                0;
                            let unitQuantity = (item.requestUnitQuantity) ? parseInt(item.requestUnitQuantity) : 0;
                            v.requestQuantity += quantity;
                            v.requestUnitQuantity += unitQuantity;
                        }
                    });
                }

                if (!checked) {
                    item.requestQuantity = (item.requestQuantity) ? parseInt(item.requestQuantity) : 0;
                    item.requestUnitQuantity = (item.requestUnitQuantity) ? parseInt(item.requestUnitQuantity) : 0;
                    item.cardId = (item.cardId) ? item.cardId : "";
                    item.itemJANCode = (item.itemJANCode) ? item.itemJANCode : "";
                    push(item);
                }
            };

            const addconsumptions = (consumptions) => {
                consumptions = JSON.parse(JSON.stringify(consumptions));
                consumptions.forEach((elm, index) => {
                    let exist = false;
                    if (Array.isArray(values.requestItems)) {
                        values.requestItems.forEach((v, idx) => {
                            if (v.inHospitalItemId === elm.inHospitalItemId) {
                                exist = true;
                                let quantity = (elm.consumptionQuantity) ? parseInt(elm
                                    .consumptionQuantity) : 0;
                                v.requestQuantity += quantity;
                            }
                        });
                    }

                    if (!exist) {
                        let consumption = new Object();
                        consumption.requestQuantity = (elm.consumptionQuantity) ? parseInt(elm
                            .consumptionQuantity) : 0;
                        consumption.requestUnitQuantity = 0;
                        consumption.makerName = (elm.item.makerName) ? elm.item.makerName : "";
                        consumption.itemName = (elm.item.itemName) ? elm.item.itemName : "";
                        consumption.itemCode = (elm.item.itemCode) ? elm.item.itemCode : "";
                        consumption.itemStandard = (elm.item.itemStandard) ? elm.item.itemStandard :
                            "";
                        consumption.itemJANCode = (elm.itemJANCode) ? elm.itemJANCode : "";
                        consumption.quantity = (elm.quantity.quantityNum) ? parseInt(elm.quantity
                            .quantityNum) : 0;
                        consumption.price = (elm.price) ? parseInt(elm.price) : 0;

                        consumption.quantityUnit = (elm.quantity.quantityUnit) ? elm.quantity
                            .quantityUnit : "";

                        consumption.unitPrice = 0;

                        if (payoutUnitPriceUseFlag === '1') {
                            consumption.unitPrice = (elm.unitPrice) ? parseInt(elm.unitPrice) : 0;
                        }

                        if (payoutUnitPriceUseFlag === '0') {
                            consumption.unitPrice = (consumption.price / consumption.quantity);
                        }

                        consumption.itemUnit = (elm.quantity.itemUnit) ? elm.quantity.itemUnit : "";
                        consumption.cardId = (elm.cardId) ? elm.cardId : "";
                        consumption.itemId = (elm.item.itemId) ? elm.item.itemId : "";
                        consumption.inHospitalItemId = (elm.inHospitalItemId) ? elm
                            .inHospitalItemId :
                            "";
                        consumption.serialNo = (elm.item.serialNo) ? elm.item.serialNo : "";
                        consumption.catalogNo = (elm.item.catalogNo) ? elm.item.catalogNo : "";
                        consumption.inItemImage = (elm.itemImage) ? elm.itemImage : "";

                        push(consumption);
                    }
                });
            };

            const openModal = ref();
            const selectInHospitalItems = ref([]);
            const addItemByBarcode = (items) => {
                selectInHospitalItems.value = [];
                if (items.item.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: '商品が見つかりませんでした',
                    });
                    return false;
                }

                if (items.type == "received") {
                    items.item.forEach((x, id) => {
                        items.item[id].requestUnitQuantity = 1;
                    });
                }

                if (items.type == "payout") {
                    items.item.forEach((x, id) => {
                        items.item[id].requestQuantity = parseInt(items.item[id]
                            .payoutQuantity);
                    });
                }
                if (items.type == "card") {
                    let exist = false
                    items.item.forEach((x, id) => {
                        if (searchCardId(items.item[id].barcode) !== undefined) {
                            exist = true;
                        }
                    })
                    if (exist) {
                        Swal.fire({
                            icon: 'error',
                            title: 'エラー',
                            text: 'すでに読み込まれたカードです。',
                        });
                        return false;
                    }
                    items.item.forEach((x, id) => {
                        items.item[id].cardId = items.item[id].barcode;
                        items.item[id].requestQuantity = parseInt(items.item[id]
                            .cardQuantity);
                    });
                }
                if (items.type == "customlabel") {
                    items.item.forEach((x, id) => {
                        items.item[id].requestQuantity = parseInt(items.item[id]
                            .customQuantity);
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

            return {
                values,
                addItemByBarcode,
                selectInHospitalItems,
                openModal,
                loading,
                start,
                complete,
                itemCount,
                isSubmitting,
                alert,
                confirm,
                requestQuantity,
                requestPrice,
                totalAmount,
                additem,
                addconsumptions,
                onSubmit,
                breadcrumbs,
                alertSetting,
                confirmSetting,
                numberFormat,
                meta,
                fields,
                remove,
                validate,
                payoutUnitPriceUseFlag,
            };
        },
        watch: {
            isSubmitting() {
                this.loading = this.isSubmitting;
            },
            fields: {
                async handler(val, oldVal) {
                    await this.validate();
                    console.log(JSON.stringify(this.values));
                },
                deep: true
            },
            values: {
                async handler(val, oldVal) {
                    console.log(JSON.stringify(this.values));
                },
                deep: true
            },
        },
        components: {
            'v-barcode-search': vBarcodeSearch,
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
            'v-open-modal': vOpenModal,
            'header-navi': headerNavi,
            'v-consumption-history-modal-for-item-request': vConsumptionHistoryModalForItemRequest
        },
    }).mount('#top');
</script>