<div id="top" v-cloak="v-cloak">
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto mb-96">
                <h1 class="text-2xl mb-2">入荷登録</h1>
                <hr>
                <div class="py-5">
                    <v-button-primary type="button" :disabled="! isChange" @click.native="onRegister">入荷照合確定</v-button-primary>
                    <v-button-primary type="button" class="mx-4" @click.native="reflect">入荷可能数を反映</v-button-primary>
                </div>
                <div class="p-4 text-base bg-gray-100 border border-gray-400">
                    <v-text title="登録日" class="flex w-full gap-6">{{ order.registDate }}</v-text>
                    <v-text title="発注番号" class="flex w-full gap-6">{{ order.orderId }}</v-text>
                    <v-text title="発注ステータス" class="flex w-full gap-6">
                        <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 2">
                            {{ order.orderStatusToString}}
                        </span>
                        <span class="bg-amber-100 text-amber-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 3">
                            {{ order.orderStatusToString}}
                        </span>
                        <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 4">
                            {{ order.orderStatusToString}}
                        </span>
                        <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 5">
                            {{ order.orderStatusToString}}
                        </span>
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 6">
                            {{ order.orderStatusToString}}
                        </span>
                        <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 7">
                            {{ order.orderStatusToString}}
                        </span>
                        <span class="bg-zinc-100 text-zinc-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="order.orderStatus == 8">
                            {{ order.orderStatusToString}}
                        </span>
                    </v-text>
                    <v-text title="発注元部署" class="flex w-full gap-6">{{ order.division.divisionName }}</v-text>
                    <v-text title="入荷先部署" class="flex w-full gap-6">{{ order.receivedDivisionName }}</v-text>
                    <v-text title="発注担当者" class="flex w-full gap-6">{{ order.orderUserName }}</v-text>
                    <v-text title="卸業者" class="flex w-full gap-6">{{ order.distributor.distributorName }}</v-text>
                    <v-text title="発注タイプ" class="flex w-full gap-6">{{ order.adjustmentToString }}</v-text>
                </div>
                <hr>
                <div class="p-4 text-lg font-bold">
                    <div class="flex w-full gap-6">
                        <div class="flex-initial lg:w-1/6 w-1/3">合計金額</div>
                        <div class="flex-auto">&yen;
                            {{ numberFormat( order.totalAmount) }}
                        </div>
                    </div>
                </div>
                <hr>
                <div class="p-4 text-base">
                    <div class="lg:flex w-full gap-6">
                        <div class="flex-initial lg:w-1/6 w-full text-lg font-bold">商品情報</div>
                        <div class="flex-auto">
                            <div class="lg:mt-0 my-4">
                                <v-barcode-search @additem="addItemByBarcode"></v-barcode-search>
                            </div>
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
                                                <div>
                                                    <span class="text-blue-700 text-lg mr-4">&yen;
                                                        {{ numberFormat(item.value.orderPrice) }}</span>
                                                    <span class="text-sm text-gray-900">( &yen;
                                                        {{ numberFormat(item.value.price) }}
                                                        /
                                                        {{ item.value.quantity.itemUnit }}
                                                        )</span>
                                                </div>
                                            </div>
                                            <div class="flex-auto lg:w-2/5 w-full">
                                                <div class="md:flex gap-6 ">
                                                    <div class="font-bold w-32">入数</div>
                                                    <div>{{ numberFormat(item.value.quantity.quantityNum) }}
                                                        {{ item.value.quantity.quantityUnit }}
                                                    </div>
                                                </div>
                                                <div class="md:flex gap-6 ">
                                                    <div class="font-bold w-32">発注数</div>
                                                    <div>{{ numberFormat(item.value.orderQuantity) }}
                                                        {{ item.value.quantity.itemUnit }}
                                                    </div>
                                                </div>
                                                <div class="md:flex gap-6 ">
                                                    <div class="font-bold w-32">現在入荷数</div>
                                                    <div>{{ numberFormat(item.value.receivedQuantity) }}
                                                        {{ item.value.quantity.itemUnit }}
                                                    </div>
                                                </div>
                                                <div class="md:flex gap-6 ">
                                                    <div class="font-bold w-32">入荷状況</div>
                                                    <div>
                                                        <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="item.value.orderItemReceivedStatus == 1">
                                                            {{ item.value.orderItemReceivedStatusToString }}
                                                        </span>
                                                        <span class="bg-orange-100 text-orange-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="item.value.orderItemReceivedStatus == 2">
                                                            {{ item.value.orderItemReceivedStatusToString }}
                                                        </span>
                                                        <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded" v-if="item.value.orderItemReceivedStatus == 3">
                                                            {{ item.value.orderItemReceivedStatusToString }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 text-base bg-gray-100 border border-gray-400 my-2" v-if="item.value.orderItemReceivedStatus != 3">

                                    <fieldset class="w-full gap-6 font-bold text-lg">
                                        <div class="flex-1">
                                            合計入荷数
                                            {{ item.value.sumReceivedQuantity }}{{ item.value.quantity.itemUnit }}
                                            / 入荷可能数(
                                            {{ item.value.orderQuantity - item.value.receivedQuantity }}{{ item.value.quantity.itemUnit }}
                                            )
                                        </div>
                                        <v-input :name="`orderItems[${idx}].sumReceivedQuantity`" label="合計入荷数" :rules="{ between: ( (item.value.orderQuantity > 0)? [ 0 , item.value.orderQuantity - item.value.receivedQuantity ] : [ item.value.orderQuantity - item.value.receivedQuantity , 0 ] ) }" type="hidden" title=""></v-input>
                                    </fieldset>

                                    <div v-for="(received , ridx) in item.value.receiveds">
                                        <div class="lg:flex gap-6 mt-2">
                                            <div class="lg:w-1/3">
                                                <v-input 
                                                    :name="`orderItems[${idx}].receiveds[${ridx}].lotNumber`" 
                                                    label="ロット番号" 
                                                    :rules="{ required : isRequired(idx) ,lotnumber: true , twoFieldRequired : [ '消費期限', `@orderItems[${idx}].receiveds[${ridx}].lotDate`]  }" 
                                                    type="text" 
                                                    @change="isChange = true" 
                                                    title="ロット番号"
                                                ></v-input>
                                            </div>
                                            <div class="lg:w-1/3">
                                                <v-input 
                                                    :name="`orderItems[${idx}].receiveds[${ridx}].lotDate`" 
                                                    label="消費期限" 
                                                    :rules="{ required : isRequired(idx) , twoFieldRequired : [ 'ロット番号' , `@orderItems[${idx}].receiveds[${ridx}].lotNumber`] }" 
                                                    type="date" 
                                                    @change="isChange = true" 
                                                    title="消費期限"
                                                ></v-input>
                                            </div>
                                            <div class="lg:w-1/3">
                                                <v-input-number 
                                                    :rules="{ between: ( (item.value.orderQuantity > 0)? [ 1 , 99999 ] : [ -99999 , -1 ] ) }" 
                                                    :name="`orderItems[${idx}].receiveds[${ridx}].receivedQuantity`" 
                                                    label="入荷数" 
                                                    :unit="item.value.quantity.itemUnit" 
                                                    :step="1" 
                                                    @change="receivedQuantitySum(idx)" title="入荷数"></v-input-number>
                                            </div>
                                            <div class="lg:mt-0 mt-2">
                                                <v-text title=" ">
                                                    <v-button-danger type="button" class="whitespace-pre" @click.native="deleteReceived(idx,ridx)">削除</v-button-danger>
                                                </v-text>
                                            </div>
                                        </div>
                                        <div class="p-4 text-base bg-gray-100 border border-gray-400 my-2" v-show="received.cards?.length > 0">
                                            <div class="font-bold text-lg">
                                                <span>入荷予定数: {{ numberFormat( received.receivedQuantity * item.value.quantity.quantityNum ) }}{{ item.value.quantity.quantityUnit }}</span><br>
                                                <span :class="{
                                                    'text-red-500': ( received.receivedQuantity * item.value.quantity.quantityNum) - ( received.cardQuantity ?? 0 ) < 0
                                                }">割り当て可能数量: {{ numberFormat(( received.receivedQuantity * item.value.quantity.quantityNum) - ( received.cardQuantity ?? 0 )) }}{{ item.value.quantity.quantityUnit }}</span>
                                            </div>
                                            <div class="lg:flex gap-6 mt-2" v-for="(card , cidx) in received.cards">
                                                <div class="lg:w-2/3 place-self-center">
                                                    <v-text title="カードID" class="w-full gap-6">{{ card.cardId }}</v-text>
                                                </div>
                                                <div class="lg:w-1/2 place-self-center">
                                                    <v-text title="カード入数" class="w-full gap-6">{{ card.cardQuantity }}{{ item.value.quantity.quantityUnit }}</v-text>
                                                </div>
                                                <div class="lg:mt-0 mt-2">
                                                    <v-button-danger type="button" class="whitespace-pre" @click.native="deleteReceivedCard(idx,ridx , cidx)">カードを除外</v-button-danger>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-4">
                                        <v-button-primary type="button" class="w-full" @click.native="addReceived(idx)">入荷情報追加</v-button-primary>
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
                                        <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="elem.item.inItemImage"></item-view>
                                    </div>
                                    <div class="break-words flex-1 box-border w-44">
                                        <h3 class="text-xl font-bold font-heading">{{ elem.item.makerName }}</h3>
                                        <p class="text-md font-bold font-heading">{{ elem.item.itemName }}</p>
                                        <p class="text-gray-500">{{ elem.item.itemCode }}<br>{{ elem.item.itemStandard }}</p>
                                        <p class="text-gray-500">{{ elem.item.quantity }}{{ elem.item.quantityUnit }}
                                            入り</p>
                                        <p>
                                            <span class="text-xl text-orange-600 font-bold font-heading">&yen;
                                                {{ numberFormat(elem.item.price) }}</span>
                                            <span class="text-gray-400">
                                                ( &yen;
                                                {{ numberFormat(elem.item.unitPrice) }}/{{ elem.item.quantityUnit }}
                                                )</span>
                                        </p>
                                        <p class="text-gray-800">ロット番号：{{ elem.item.lotNumber }}</p>
                                        <p class="text-gray-800">使用期限：{{ elem.item.lotDate }}</p>
                                        <p class="text-gray-800">{{ elem.item.distributorName }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full lg:block lg:w-1/6 px-4 py-4">
                                <v-button-default type="button" class="w-full" v-on:click.native="addItem(elem.id,elem.item)">反映</v-button-default>
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
</div>
<script>
    const PHPData = <?php echo json_encode($viewModel, true); ?>;

    var JoyPlaApp = Vue
        .createApp({
            components: {
                'v-barcode-search': vBarcodeSearch,
                'v-input': vInput,
                'v-text': vText,
                'v-select': vSelect,
                'v-button-danger': vButtonDanger,
                'v-button-default': vButtonDefault,
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
                const {
                    ref,
                    onCreated,
                    onMounted
                } = Vue;
                const {
                    useFieldArray,
                    useForm
                } = VeeValidate;

                const order = PHPData.order;

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
                        'orderId': order.orderId,
                        'adjustment': order.adjustment,
                        'orderItems': order
                            .orderItems
                            .map((x, idx) => {
                                x.orderQuantity = parseInt(x.orderQuantity);
                                x.receiveds = [];
                                x.sumReceivedQuantity = 0;
                                return x;
                            })
                    },
                    validateOnMount: false
                });

                const {
                    remove,
                    push,
                    fields,
                    update,
                    replace
                } = useFieldArray(
                    'orderItems',
                    control
                );

                const readingCards = [];

                console.log(fields);

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
                    sleepComplate();
                });
                const breadcrumbs = [{
                    text: '発注・入荷メニュー',
                    disabled: false,
                    href: _ROOT + '&path=/order'
                }, {
                    text: '入荷照合',
                    disabled: false,
                    href: _ROOT + '&path=/received/order/list&isCache=true'
                }, {
                    text: '入荷登録',
                    disabled: true
                }];

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
                    return values.orderItems.map(x => {
                        return {
                            'orderItemId': x.orderItemId,
                            'receiveds': (x.receiveds)? x.receiveds : [],
                        };
                    });
                }

                const onRegister = async () => {
                    
                    const {
                        valid,
                        errors
                    } = await validate();

                    if (!valid || !checkCardValid()) {
                        Swal.fire({
                            icon: 'error',
                            title: '入力エラー',
                            text: '入力エラーがございます。ご確認ください'
                        })
                    } else {
                        Swal
                            .fire({
                                title: '入荷登録',
                                text: "入荷登録を行います。\r\nよろしいですか？",
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
                                        start();
                                        const registerModel = createRegisterModel();
                                        let params = new URLSearchParams();
                                        params.append("path", "/api/" + values.orderId + "/received/register");
                                        params.append("_method", 'post');
                                        params.append("_csrf", _CSRF);
                                        params.append("registerModel", JSON.stringify(encodeURIToObject(registerModel)));

                                        const res = await axios.post(_APIURL, params);
                                        complete();
                                        if (res.data.code != 200) {
                                            throw new Error(res.data.message)
                                        }

                                        Swal
                                            .fire({
                                                icon: 'success',
                                                title: '入荷登録が完了しました。'
                                            })
                                            .then((result) => {
                                                location.reload();
                                            });
                                        return true;
                                    }
                                } catch (error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'システムエラー',
                                        text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。'
                                    });
                                }
                            })
                    }
                };

                const isReceived = (idx) => {
                    let count = fields.value[idx].value.orderQuantity - fields.value[idx].value.receivedQuantity;
                    if(count == 0)
                    {
                        Swal.fire({
                            icon: 'info',
                            title: '入荷が完了している商品です',
                        });
                        return true;
                    }
                    return false;
                }

                const receivedQuantitySum = (idx) => {
                    if (!fields.value[idx].value.receiveds) {
                        fields
                            .value[idx]
                            .value
                            .receiveds = [];
                    }
                    fields
                        .value[idx]
                        .value
                        .sumReceivedQuantity = fields
                        .value[idx]
                        .value
                        .receiveds
                        .reduce(function(sum, element) {
                            return sum + element.receivedQuantity;
                        }, 0);
                }

                const isRequired = (idx) => {
                    if (fields.value[idx].value.lotManagement == "1") {
                        return true;
                    }
                    return false;
                };

                const addItem = (idx, item) => {
                    if (isReceived(idx))
                    {
                        return null;
                    }
                    isChange.value = true;
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
                        .push({
                            'receivedQuantity': 1,
                            'cardQuantity': 0,
                            'lotNumber': ( item.lotNumber )? item.lotNumber : "" ,
                            'lotDate': ( item.lotDate )? item.lotDate : "",
                            'cards' : []
                        });

                    receivedQuantitySum(idx);
                };

                const addReceivedItemCard = (idx, item) => {
                    let receivedItem = fields.value[idx].value.receiveds.find(function(element, index) {
                        let receivedQuantity = element.receivedQuantity * fields.value[idx].value.quantity.quantityNum;
                        let cardQuantitySum = element.cardQuantity ?? 0;

                        return (receivedQuantity - cardQuantitySum ) > 0;
                    });
                    if(receivedItem){
                        let receivedItemIndex = fields.value[idx].value.receiveds.findIndex(function(elem){
                            return elem === receivedItem;
                        });

                        if(! fields.value[idx].value.receiveds[receivedItemIndex].cards){
                            fields.value[idx].value.receiveds[receivedItemIndex].cards = [];
                        }

                        fields.value[idx].value.receiveds[receivedItemIndex].cards.push({
                            'cardId': item.barcode,
                            'cardQuantity': item.cardQuantity,
                        })
                        
                        fields.value[idx].value.receiveds[receivedItemIndex].cardQuantity = (fields.value[idx].value.receiveds[receivedItemIndex].cards) ? 
                            fields.value[idx].value.receiveds[receivedItemIndex].cards.reduce(function(sum, elem) {
                            return sum + parseInt(elem.cardQuantity);
                        }, 0) : 0; 

                        readingCards.push(item.barcode);
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: '紐づけ可能な入荷データがありませんでした',
                        });
                        return false;
                    }
                };

                const addItemByBarcode = (items) => {
                    selectInHospitalItems.value = [];
                    if (
                        (items.type !== 'jancode' && items.type !== 'card' && items.type !== 'gs1-128')
                        || items.length === 0 || items.item.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: '商品が見つかりませんでした',
                        });
                        return false;
                    }

                    let inHospitalitems = fields
                        .value
                        .map((x, xKey) => {
                            let itemdata = items.item.find(
                                (y) => (x.value.inHospitalItemId === y.inHospitalItemId)
                            );
                            if (itemdata) {
                                return {
                                    id: xKey,
                                    item: itemdata
                                }
                            }
                        })
                        .filter(x => x);

                    if (inHospitalitems.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: '商品が見つかりませんでした',
                        });
                        return false;
                    }

                    if( items.type === 'gs1-128' || items.type === 'jancode') {
                        if (inHospitalitems.length === 1) {
                            addItem(inHospitalitems[0].id, inHospitalitems[0].item);
                        } else {
                            selectInHospitalItems.value = inHospitalitems;
                            openModal
                                .value
                                .open();
                        }
                    }
                    
                    if( items.type === 'card' ) {
                        if(order.receivedDivisionId !== items.item[0].divisionId){
                            Swal.fire({
                                icon: 'info',
                                title: '入荷先の部署と異なるカードです',
                            });
                            return false;
                        }

                        if (readingCards.find(elem => elem === items.item[0].barcode))
                        {
                            Swal.fire({
                                icon: 'info',
                                title: 'すでに読み込まれたカード情報です',
                            });
                            return false;
                        }
                        if (inHospitalitems.length === 1) {
                            addReceivedItemCard(inHospitalitems[0].id, items.item[0]);
                        }
                    }
                };
                const addReceived = (idx) => {
                    if (isReceived(idx))
                    {
                        return null;
                    }
                    isChange.value = true;
                    if (fields.value[idx].value.orderItemReceivedStatus == "3") {
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
                        .push({
                            'receivedQuantity': 0,
                            'lotNumber': "",
                            'lotDate': ""
                        });
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
                        .receiveds = (result) ?
                        result : [];
                };

                
                const deleteReceivedCard = (idx, ridx , cidx) => {
                    let index = readingCards.findIndex(elem => elem === fields
                        .value[idx]
                        .value
                        .receiveds[ridx]
                        .cards[cidx].cardId);

                    delete readingCards[index];

                    let result = fields
                        .value[idx]
                        .value
                        .receiveds[ridx]
                        .cards
                        .filter((value, index) => {
                            if (index === cidx) {
                                return;
                            }
                            return value;
                        })
                        .filter(e => e);
                    fields
                        .value[idx]
                        .value
                        .receiveds[ridx].cards = (result) ? result : [];
                    
                    fields.value[idx].value.receiveds[ridx].cardQuantity = (fields.value[idx].value.receiveds[ridx].cards) ? 
                        fields.value[idx].value.receiveds[ridx].cards.reduce(function(sum, elem) {
                        return sum + parseInt(elem.cardQuantity);
                    }, 0) : 0; 
                };

                const checkCardValid = () => {
                    let flag = true;
                    fields.value.forEach(function(element){
                        if(!!element.value.receiveds){
                            element.value.receiveds.forEach(function(received){
                                flag = (received.receivedQuantity * element.value.quantity.quantityNum) - ( received.cardQuantity ?? 0 ) >= 0;
                            })
                        }
                    })

                    return flag;
                }

                const reflect = () => {
                    for(val of fields.value){
                        if(val.value.receiveds && val.value.receiveds.length > 0){ //入力済みは対象外
                            continue;
                        }
                        if (val.value.lotManagement == "1") { //ロット管理必須のものは対象外
                            continue;
                        }
                        if ( val.value.receivedFlag ){
                            continue;
                        }
                        val.value.receiveds = [{"receivedQuantity": val.value.orderQuantity - val.value.receivedQuantity, "lotNumber": "", "lotDate": "",}];
                        val.value.sumReceivedQuantity = val.value.sumReceivedQuantity + val.value.orderQuantity - val.value.receivedQuantity;
                        isChange.value = true;
                    }
                }

                return {
                    selectInHospitalItems,
                    openModal,
                    receivedQuantitySum,
                    addItem,
                    addItemByBarcode,
                    deleteReceived,
                    deleteReceivedCard,
                    addReceived,
                    isRequired,
                    onRegister,
                    isChange,
                    numberFormat,
                    order,
                    fields,
                    breadcrumbs,
                    loading,
                    start,
                    complete,
                    reflect,
                }
            },
            watch: {}
        })
        .mount('#top');
</script>