<style>
    .scroll-hint-icon {
        top: 8%;
    }
</style>
<div id="top" v-cloak>
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="h-full px-1">
        <div class="flex-auto">
            <div class="index mx-auto">
                <h1 class="text-2xl mb-2">請求商品一覧</h1>
                <hr>
                <div class="w-full flex border-b-2 border-gray-200 py-4">
                    <div class="flex-auto w-1/2">
                        <v-select name="perPage" :options="perPageOptions" @change="changeParPage"></v-select>
                    </div>
                    <div class="flex-auto w-1/2">
                        <div class="ml-auto h-10 w-20 cursor-pointer" data-micromodal-trigger="openModal">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="h-8 w-8 mx-auto" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" @click="onOpenModal">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <p class="text-sm text-center">絞り込み</p>
                        </div>
                    </div>
                </div>
                <div>
                    {{ (totalCount == 0)? 0 : ( parseInt(values.perPage) * ( values.currentPage - 1 ) ) + 1 }}件 - {{ (( parseInt(values.perPage) * values.currentPage ) < totalCount ) ? parseInt(values.perPage) * values.currentPage : totalCount }}件 / 全 {{ totalCount }}件
                </div>


                <div v-if="(totalCount !== 0)">
                    <div class="p-2 bg-gray-300">
                        <v-barcode-search @additem="addItemByBarcode"></v-barcode-search>
                    </div>
                    <div class=" my-2">
                        <div class="max-h-full h-full grid place-content-center w-full lg:flex border border-sushi-600 bg-white mt-3">
                            <div class="flex-1 p-4 relative text-center">バーコードを読み取ってください</div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <div id="totalizationTable" class="flex flex-col">
                        <div class="sm:-mx-6 lg:-mx-8">
                            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-center">
                                        <thead class="whitespace-nowrap">
                                            <tr>
                                                <th scope="col" class="px-6 py-4" colspan="7">
                                                    <div class="lg:flex lg:flex-row gap-4">
                                                        <div class="my-4 w-1/5 lg:w-1/6">
                                                            <form :action="_ROOT" method="POST" name="print" @submit="onPrintSubmit" target="_blank">
                                                                <input type="hidden" :value="_CSRF" name="_token">
                                                                <input type="hidden" value="post" name="_method">
                                                                <input type="hidden" value="/itemrequest/pickingList" name="path">
                                                                <input type="hidden" name="search" :value="pickingListSearch">
                                                                <v-button-default class="w-full" type="submit" :disabled="(totalCount === 0)">印刷</v-button-default>
                                                            </form>
                                                        </div>
                                                        <div class=" my-4 w-1/5 lg:w-1/6">
                                                            <v-button-primary type="button" class="w-full" @click.native="onSubmit" :disabled="(totalCount === 0)">払出登録</v-button-primary>
                                                        </div>
                                                        <div class=" my-4 w-1/5 lg:w-1/6">
                                                            <v-button-primary type="button" class="w-full" @click.native="onAcceptanceSubmit" :disabled="(totalCount === 0)">出庫登録</v-button-primary>
                                                        </div>
                                                        <div class="my-4 items-center self-center">
                                                            <?php /*<v-switch id="labelCreate" v-model="labelCreate" :message="(labelCreate)? 'ラベル発行をする' : 'ラベル発行をしない'"></v-switch>*/?>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-2 py-4 text-center">
                                                    <v-button-default class="w-auto px-2" type="button" @click.native="reflectToPayout">請求数を反映</v-button-default>
                                                </th>
                                                <th scope="col" class="px-6 py-4" colspan="6"></th>
                                            </tr>
                                        </thead>
                                        <thead class="bg-gray-50 whitespace-nowrap text-sm font-medium text-gray-700 text-center border">
                                            <tr>
                                                <th scope="col" class="px-2 py-4 border w-10 ">
                                                    No
                                                </th>
                                                <th scope="col" class="px-6 py-4 border">
                                                    請求先部署名
                                                </th>
                                                <th scope="col" class="min-w-[225px] px-6 py-4 border">
                                                    商品情報
                                                </th>
                                                <th scope="col" class="px-6 py-4 border">

                                                </th>
                                                <th scope="col" class="px-6 py-4 border">
                                                    請求元部署名
                                                </th>
                                                <th scope="col" class="px-6 py-4 border">
                                                    請求数
                                                </th>
                                                <th scope="col" class="px-6 py-4 border">
                                                    在庫数
                                                </th>
                                                <th scope="col" class="min-w-[225px] px-6 py-4 border">
                                                    払出数
                                                </th>
                                                <th scope="col" class="min-w-[225px] px-6 py-4 border">
                                                    ロット番号
                                                </th>
                                                <th scope="col" class="min-w-[225px] px-6 py-4 border">
                                                    使用期限
                                                </th>
                                                <th scope="col" class="min-w-[225px] px-6 py-4 border">
                                                    カード情報
                                                </th>
                                                <th scope="col" class="px-6 py-4 border">
                                                    必要数
                                                </th>
                                                <th scope="col" class="px-6 py-4 border">
                                                    合計払出数
                                                </th>
                                                <th scope="col" class="px-6 py-4">

                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-sm text-gray-900 font-light" v-if="(totalCount !== 0)">
                                            <template v-for=" (totalization, idx) in values.totalizations" :key="totalization.key">
                                                <tr class="border-b">
                                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="whitespace-nowrap font-medium px-3 py-4 border">
                                                        {{ totalization.no }}
                                                    </td>
                                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="break-all text-left px-3 py-4 border">
                                                        {{ totalization.targetDivisionName }}
                                                    </td>
                                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="break-words text-left px-3 py-4 border">
                                                        <p class="text-md font-bold">{{ totalization.makerName }}</p>
                                                        <p class="text-md font-bold">{{ totalization.itemName }}</p>
                                                        <p class="text-md text-gray-500">{{ totalization.itemCode }}</p>
                                                        <p class="text-md text-gray-500">{{ totalization.itemStandard }}</p>
                                                        <p class="text-md text-gray-500">{{ totalization.itemJANCode }}</p>
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 border">
                                                        <input class="float-none form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 my-1 align-top bg-no-repeat bg-center bg-contain cursor-pointer" type="checkbox" :name="`values.totalizations[${idx}].payoutCheck`" :checked="totalization.payoutCheck" @change="changeCheck(idx)">
                                                    </td>
                                                    <td class="break-all text-left px-3 py-4 border">
                                                        {{ totalization.sourceDivisionName }}
                                                    </td>
                                                    <td v-if="totalization.default === true" :rowspan="totalization.added" class="whitespace-nowrap px-3 py-4 border">
                                                        {{ totalization.requestQuantity }} {{ totalization.quantityUnit }}
                                                    </td>
                                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="whitespace-nowrap px-3 py-4 border">
                                                        {{ totalization.stockQuantity }} {{ totalization.quantityUnit }}
                                                    </td>
                                                    <td class="px-3 py-4 border text-left">
                                                        <v-input-number @change="checkPayoutQuantity(idx)" :rules="{ between: [0 , 99999] }" :name="`totalizations[${idx}].payoutQuantity`" :min="0" :unit="totalization.quantityUnit" label="払出数" :step="1"></v-input-number>
                                                    </td>
                                                    <td class="px-3 py-4 border text-left">
                                                        <span v-if="isRequired(idx)" class="mb-2 bg-red-400 text-white text-md font-medium inline-flex items-center px-2.5 rounded">必須</span>
                                                        <v-input :name="`totalizations[${idx}].lotNumber`" label="ロット番号" :rules="{ lotnumber: true , twoFieldRequired : [ '消費期限', `@totalizations[${idx}].lotDate`]}" type="text"></v-input>
                                                    </td>
                                                    <td class="px-3 py-4 border text-left">
                                                        <span v-if="isRequired(idx)" class="mb-2 bg-red-400 text-white text-md font-medium inline-flex items-center px-2.5 rounded">必須</span>
                                                        <v-input :name="`totalizations[${idx}].lotDate`" label="消費期限" :rules="{ twoFieldRequired : [ 'ロット番号' , `@totalizations[${idx}].lotNumber`] }" type="date"></v-input>
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 border text-left">
                                                        <v-input-v2 :name="`totalizations[${idx}].card`" type="text" label="カード情報" :readonly="true"></v-input>
                                                    </td>
                                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="whitespace-nowrap px-3 py-4 border">
                                                        {{ totalization.totalRequestQuantity }} {{ totalization.quantityUnit }}
                                                    </td>
                                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="whitespace-nowrap px-3 py-4 border">
                                                        {{ totalPayoutQuantity(idx) }} {{ totalization.quantityUnit }}
                                                    </td>
                                                    <td v-if="totalization.default === true" class="whitespace-nowrap px-3 py-4 border">
                                                        <v-button-primary type="button" @click.native="copyItem(idx)">追加</v-button-primary>
                                                    </td>
                                                    <td v-if="totalization.default === false" class="whitespace-nowrap px-3 py-4 border">
                                                        <v-button-danger type="button" @click.native="deleteItem(idx)">削除</v-button-danger>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <v-pagination :show-pages="showPages" v-model:current-page="values.currentPage" :total-count="totalCount" :per-page="parseInt(values.perPage)"></v-pagination>
            </div>
        </div>
    </div>
    <v-open-modal ref="openModal" headtext="絞り込み" id="openModal">
        <div class="flex flex-col">
            <div class="w-full overflow-y-auto" style="max-height: 85vh;">
                <div class="flex flex-wrap">
                    <div class="w-full px-3 my-6 md:mb-0">
                        <div class="my-4">
                            <v-input name="makerName" type="text" label="メーカー名" title="メーカー名"></v-input>
                        </div>
                        <div class="my-4">
                            <v-input name="itemName" type="text" label="商品名" title="商品名"></v-input>
                        </div>
                        <div class="my-4">
                            <v-input name="itemCode" type="text" label="製品コード" title="製品コード"></v-input>
                        </div>
                        <div class="my-4">
                            <v-input name="itemStandard" type="text" label="規格" title="規格"></v-input>
                        </div>
                        <div class="my-4">
                            <v-input name="itemJANCode" type="text" label="JANコード" title="JANコード"></v-input>
                        </div>
                        <div class="my-4">
                            <v-multiple-select-division-v2 id="sourceDivisionIds" name="sourceDivisionIds" title="請求元部署名" />
                            </v-multiple-select-division-v2>
                        </div>
                        <div class="my-4">
                            <v-multiple-select-division-v2 id="targetDivisionIds" name="targetDivisionIds" title="請求先部署名" :is-only-my-division="<?php var_export(
                                gate(
                                    'list_of_item_request_history'
                                )->isOnlyMyDivision()
                            ); ?>" />
                            </v-multiple-select-division-v2>
                        </div>
                        <div class="mx-auto lg:w-2/3 mb-4 text-center flex items-center gap-6 justify-center">
                            <v-button-default type="button" @click.native="searchClear">クリア</v-button-default>
                            <v-button-primary type="button" @click.native="searchExec">絞り込み</v-button-primary>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </v-open-modal>
    <v-open-modal ref="openSelectModal" headtext="請求商品選択" id="openSelectModal">
        <div class="flex flex-col" style="max-height: 68vh;">
            <div class="overflow-y-scroll my-6">
                <div class="w-full mb-8 xl:mb-0">
                    <div class="hidden lg:flex w-full sticky top-0 bg-white py-4 flex-wrap">
                        <div class="w-full lg:w-5/6">
                            <h4 class="font-bold font-heading text-gray-500 text-center">請求商品情報</h4>
                        </div>
                        <div class="w-full lg:w-1/6">
                            <h4 class="font-bold font-heading text-gray-500 text-center">反映</h4>
                        </div>
                    </div>
                    <div class="lg:pt-0 pt-4">
                        <div class="flex flex-wrap items-center mb-3" v-for="(elem, index) in selectRequestItems">
                            <div class="w-full lg:w-5/6 lg:px-4 px-0 mb-6 lg:mb-0">
                                <div class="flex gap-4">
                                    <div class="break-words items-center w-2/3 box-border">
                                        <p class="text-md font-bold">{{ elem.makerName }}</p>
                                        <p class="text-md font-bold">{{ elem.itemName }}</p>
                                        <p class="text-md text-gray-500">{{ elem.itemCode }}</p>
                                        <p class="text-md text-gray-500">{{ elem.itemStandard }}</p>
                                        <p class="text-md text-gray-500">{{ elem.itemJANCode }}</p>
                                    </div>
                                    <div class="break-words items-start w-1/3">
                                        <p class="text-md pb-2">
                                            <span class="block text-sm text-gray-500">ロット番号：</span>{{ elem.lotNumber }}
                                        </p>
                                        <p class="text-md pb-2">
                                            <span class=" block text-sm text-gray-500">使用期限：</span>{{ elem.lotDate }}
                                        </p>
                                        <p class="text-md">
                                            <span class=" block text-sm text-gray-500">カード情報：</span>{{ elem.card }}
                                        </p>
                                    </div>
                                </div>
                                <div class="break-words w-full pt-2">
                                    <p class="text-md">
                                        <span class="text-sm font-bold">請求元部署：</span>{{ elem.sourceDivisionName }}<br>
                                        <span class="text-sm font-bold">請求先部署：</span>{{ elem.targetDivisionName }}<br>
                                    </p>
                                </div>
                            </div>
                            <div class="w-full lg:block lg:w-1/6 px-4 py-4">
                                <v-button-default type="button" class="w-full" v-on:click.native="reflectSelect(elem)">反映</v-button-default>
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
        components: {
            'v-loading': vLoading,
            'card-button': cardButton,
            'v-breadcrumbs': vBreadcrumbs,
            'v-button-default': vButtonDefault,
            'v-button-primary': vButtonPrimary,
            'v-button-danger': vButtonDanger,
            'header-navi': headerNavi,
            'v-open-modal': vOpenModal,
            'v-input': vInput,
            'v-input-v2': vInputv2,
            'item-view': itemView,
            'v-pagination': vPagination,
            'v-select': vSelect,
            'v-select-division': vSelectDivision,
            'v-checkbox': vCheckbox,
            'v-multiple-select-division-v2': vMultipleSelectDivisionV2,
            'v-input-number': vInputNumber,
            'v-barcode-search': vBarcodeSearch,
            'v-switch' : vSwitch,
        },
        setup() {
            const {
                ref,
                toRef,
                toRefs,
                onMounted,
                reactive,
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

            start();

            //const labelCreate = ref(localStorage.joypla_payoutLabelCreate === 'true');
            const labelCreate = ref(false);

            const getCache = () => {
                let url = window.location.href;
                name = 'isCache';
                var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                    results = regex.exec(url);
                if (!results) return null;
                if (!results[2]) return '';
                return decodeURIComponent(results[2].replace(/\+/g, " "));
            }

            const pagetitle = "totalization";

            const getParam = (name) => {
                let url = window.location.href;
                name = name.replace(/[\[\]]/g, "\\$&");

                if (getCache() === "true") {
                    let obj = sessionStorage.getItem(pagetitle);
                    if (obj === null) {
                        return ""
                    }
                    return (JSON.parse(obj))[name];
                }

                var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                    results = regex.exec(url);
                if (!results) return null;
                if (!results[2]) return '';
                return decodeURIComponent(results[2].replace(/\+/g, " "));
            }

            const setParam = (values) => {
                sessionStorage.setItem(pagetitle, JSON.stringify(values));
                const url = new URL(window.location);
                url.searchParams.set('itemName', values.itemName);
                url.searchParams.set('makerName', values.makerName);
                url.searchParams.set('itemCode', values.itemCode);
                url.searchParams.set('itemStandard', values.itemStandard);
                url.searchParams.set('itemJANCode', values.itemJANCode);
                url.searchParams.set('perPage', values.perPage);
                url.searchParams.set('currentPage', values.currentPage);
                url.searchParams.set('sourcerDivisionIds', values.sourcerDivisionIds);
                url.searchParams.set('targetDivisionIds', values.targetDivisionIds);
                history.pushState({}, '', url);
            }

            const {
                meta,
                control,
                validate,
                values,
                setFieldValue,
                resetForm,
                handleSubmit,
                isSubmitting
            } = useForm({
                initialValues: {
                    totalizations: [],
                    itemName: (getParam("itemName")) ? getParam("itemName") : "",
                    makerName: (getParam("makerName")) ? getParam("makerName") : "",
                    itemCode: (getParam("itemCode")) ? getParam("itemCode") : "",
                    itemStandard: (getParam("itemStandard")) ? getParam("itemStandard") : "",
                    itemJANCode: (getParam("itemJANCode")) ? getParam("itemJANCode") : "",
                    perPage: (Number.isInteger(getParam("perPage"))) ? getParam("perPage") : "100",
                    currentPage: (Number.isInteger(parseInt(getParam("currentPage")))) ? parseInt(getParam("currentPage")) : 1,
                    sourceDivisionIds: (getParam("sourceDivisionIds")) ? (Array.isArray(getParam("sourceDivisionIds")) ? getParam("sourceDivisionIds") : (getParam("sourceDivisionIds")).split(',')) : [],
                    targetDivisionIds: (getParam("targetDivisionIds")) ? (Array.isArray(getParam("targetDivisionIds")) ? getParam("targetDivisionIds") : (getParam("targetDivisionIds")).split(',')) : []
                },
                validateOnMount: false
            });

            const {
                remove,
                push,
                fields,
                update,
                replace
            } = useFieldArray('totalizations', control);

            const breadcrumbs = [{
                    text: '請求メニュー',
                    disabled: false,
                    href: _ROOT + '&path=/itemrequest',
                },
                {
                    text: '請求商品一覧',
                    disabled: true,
                }
            ];

            const openModal = ref();

            const numberFormat = (value) => {
                if (!value) {
                    return 0;
                }
                return new Intl.NumberFormat('ja-JP').format(value);
            };

            const isRequired = (idx) => {
                return (values.totalizations[idx].lotManagement);
            };

            const totalPayoutQuantity = (idx) => {
                let num = 0;
                values.totalizations.forEach((v, id) => {
                    if (v.recordId === values.totalizations[idx].recordId) {
                        num += parseInt(v.payoutQuantity);
                    }
                });
                values.totalizations.forEach((v, id) => {
                    if (v.recordId === values.totalizations[idx].recordId) {
                        v.totalPayoutQuantity = num;
                    }
                });
                return num;
            };

            const checkPayoutQuantity = (idx) => {
                if (values.totalizations[idx]) {
                    if (!values.totalizations[idx].payoutQuantity || values.totalizations[idx].payoutQuantity < 0) {
                        values.totalizations[idx].payoutQuantity = 0;
                    }
                    if (values.totalizations[idx].payoutQuantity > 0) {
                        values.totalizations[idx].payoutCheck = true;
                    } else {
                        values.totalizations[idx].payoutCheck = false;
                    }
                    if (values.totalizations[idx].card !== '') {
                        values.totalizations[idx].payoutQuantity = values.totalizations[idx].cardQuantity;
                    }
                }
            }

            const changeCheck = (idx) => {
                const item = values.totalizations[idx];
                item.payoutCheck = !item.payoutCheck;
            }

            const onOpenModal = () => {
                openModal.value.open();
            }

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                showCloseButton: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            const showPages = ref(5);

            const totalCount = ref(0);

            const perPageOptions = [{
                label: "10件表示",
                value: "10"
            }, {
                label: "50件表示",
                value: "50"
            }, {
                label: "100件表示",
                value: "100"
            }, {
                label: "500件表示",
                value: "500"
            }, {
                label: "1000件表示",
                value: "1000"
            }];


            const searchCount = ref(0);

            const reflectToPayout = () => {
                if (values.totalizations.length === 0) {
                    return;
                }

                values.totalizations.forEach((v, idx) => {
                    if (v.payoutQuantity === 0) {
                        values.totalizations[idx].payoutQuantity = v.requestQuantity;
                    }
                });
            }

            const makeItems = (data) => {
                values.totalizations = [];
                data.forEach((elm, index) => {
                    elm.totalRequests.forEach((v, idx) => {
                        let item = new Object();
                        item.firstRow = false;
                        if (idx === 0) {
                            item.firstRow = true;
                        }
                        item.recordId = v.recordId;
                        item.inHospitalItemId = v.inHospitalItemId;
                        item.targetDivisionName = v.targetDivision.divisionName;
                        item.targetDivisionId = v.targetDivision.divisionId;
                        item.sourceDivisionName = v.sourceDivision.divisionName;
                        item.sourceDivisionId = v.sourceDivision.divisionId;
                        item.requestQuantity = parseInt(v.requestQuantity);
                        item.quantityUnit = elm.quantity.quantityUnit;
                        item.lotManagement = elm.lotManagement;
                        item.totalRequestQuantity = parseInt(elm.requestQuantity);
                        item.stockQuantity = parseInt(elm.stockQuantity);
                        item.makerName = elm.item.makerName;
                        item.itemName = elm.item.itemName;
                        item.itemCode = elm.item.itemCode;
                        item.itemStandard = elm.item.itemStandard;
                        item.itemJANCode = elm.item.itemJANCode;
                        item.cardQuantity = 0;
                        item.payoutQuantity = 0;
                        item.default = true;
                        item.added = 1;
                        item.lotNumber = '';
                        item.lotDate = '';
                        item.card = '';
                        item.payoutCheck = false;
                        if (item.firstRow === true) {
                            item.no = index + 1;
                            item.rowspan = elm.countTotalRequests;
                        }
                        push(item);
                    });
                });
            };

            const copyItem = (idx) => {
                if (values.totalizations[idx].added > 9) {
                    Swal.fire({
                        icon: 'info',
                        title: '追加可能件数を超えています。',
                    });
                    return;
                }

                let item = Object.assign({}, values.totalizations[idx]);
                item.payoutQuantity = 0;
                item.firstRow = false;
                item.default = false;
                item.lotNumber = '';
                item.lotDate = '';
                item.card = '';
                item.payoutCheck = false;
                values.totalizations.forEach((elm, index) => {
                    if (elm.recordId === item.recordId &&
                        elm.firstRow === true) {
                        elm.rowspan += 1;
                    }
                    if (elm.recordId === item.recordId &&
                        elm.sourceDivisionId === item.sourceDivisionId &&
                        elm.default === true) {
                        elm.added += 1;
                    }
                });
                values.totalizations.splice(idx + 1, 0, item);
            };

            const deleteItem = (idx) => {
                if (values.totalizations[idx].default === true) {
                    return;
                }

                values.totalizations.forEach((elm, index) => {
                    if (elm.recordId === values.totalizations[idx].recordId &&
                        elm.firstRow === true) {
                        elm.rowspan -= 1;
                    }
                    if (elm.recordId === values.totalizations[idx].recordId &&
                        elm.sourceDivisionId === values.totalizations[idx].sourceDivisionId &&
                        elm.default === true) {
                        elm.added = elm.added -= 1;
                    }
                });

                values.totalizations.splice(idx, 1);
            };

            const listGet = () => {
                const search = Object.assign({}, values);
                delete search.totalizations;
                let params = new URLSearchParams();
                params.append("path", "/api/itemrequest/totalization");
                params.append("search", JSON.stringify(encodeURIToObject(search)));
                params.append("_csrf", _CSRF);

                setParam(values);

                start();
                axios.post(_APIURL, params)
                    .then((response) => {
                        if (response.data.count > 0) {
                            makeItems(response.data.data);
                        }
                        totalCount.value = parseInt(response.data.count);
                    })
                    .catch((error) => {
                        complete();
                        if (searchCount.value > 0) {
                            Toast.fire({
                                icon: 'error',
                                title: '検索に失敗しました。再度お試しください。'
                            })
                        }
                        searchCount.value++;
                    })
                    .then(() => {
                        complete();
                        if (searchCount.value > 0) {
                            Toast.fire({
                                icon: 'success',
                                title: '検索が完了しました。'
                            })
                        }
                        searchCount.value++;
                    });
            };
            
            onMounted(() => {
                listGet();
                Toast.fire({
                    icon: 'warning',
                    title: '表示内容を変更する際に、\r\nご入力内容は保持されません。',
                    width: '400px'
                })
            });

            const changeParPage = () => {
                values.currentPage = 1;
                values.totalizations = [];
                listGet();
            };

            const searchExec = () => {
                values.currentPage = 1;
                values.totalizations = [];
                listGet();
            };

            const searchClear = () => {
                values.currentPage = 1;
                values.totalizations = [];
                values.itemName = '';
                values.makerName = '';
                values.itemCode = '';
                values.itemStandard = '';
                values.itemJANCode = '';
                values.sourceDivisionIds = [];
                values.targetDivisionIds = [];
                listGet();
            };

            const pickingListSearch = ref([]);
            const onPrintSubmit = () => {
                const search = Object.assign({}, values);
                delete search.totalizations;
                pickingListSearch.value = JSON.stringify(encodeURIToObject(search));
            };

            const checkLot = () => {
                let check = true;
                values.totalizations.forEach(function(item, idx) {
                    if (item.payoutCheck === true && item.payoutQuantity > 0) {
                        if (item.lotManagement === true) {
                            if (item.lotDate === '' || item.lotNumber === '') {
                                check = false;
                            }
                        }
                    }
                });
                return check;
            }

            const checkPayout = () => {
                let check = true;
                values.totalizations.forEach(function(item, idx) {
                    if (item.payoutCheck === true && item.payoutQuantity > 0) {
                        if (item.totalPayoutQuantity > item.totalRequestQuantity) {
                            check = false;
                        }
                        if (item.totalPayoutQuantity > item.stockQuantity) {
                            check = false;
                        }
                    }
                });
                return check;
            }

            const createPayoutModel = (values) => {
                let items = values.totalizations;
                let payouts = [];
                items.forEach(function(item, idx) {
                    if (item.payoutCheck === true && item.payoutQuantity > 0) {
                        payouts.push({
                            'recordId': item.recordId,
                            'inHospitalItemId': item.inHospitalItemId,
                            'targetDivisionId': item.sourceDivisionId, //請求画面のtargetと、払出のtargetは逆
                            'sourceDivisionId': item.targetDivisionId,
                            'payoutQuantity': item.payoutQuantity,
                            'lotNumber': item.lotNumber,
                            'lotDate': item.lotDate,
                            'card': item.card ?? ''
                        });
                    }
                });
                return payouts;
            };

            const createAcceptanceModel = (values) => {
                let items = values.totalizations;
                let payouts = [];
                items.forEach(function(item, idx) {
                    if (item.payoutCheck === true && item.payoutQuantity > 0) {
                        payouts.push({
                            'inHospitalItemId': item.inHospitalItemId,
                            'acceptanceQuantity': parseInt(item.payoutQuantity),
                            'targetDivisionId' : item.sourceDivisionId, //請求画面のtargetと、払出のtargetは逆
                            'sourceDivisionId' : item.targetDivisionId,
                            'lotNumber' : item.lotNumber,
                            'lotDate' : item.lotDate,
                            'card' : item.cardId ?? '',
                        })
                    }
                });
                return payouts;
            };
            

            const onSubmit = async () => {
                const {
                    valid,
                    errors
                } = await validate();

                const validLot = checkLot();
                const validPayout = checkPayout();

                if (!valid) {
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: '入力エラーがございます。ご確認ください。',
                    })
                } else if (!validLot) {
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: 'ロット情報を正しく入力してください。'
                    })
                } else if (!validPayout) {
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: '請求数・在庫数をご確認の上、払出数を正しく入力してください。'
                    })
                } else {
                    Swal.fire({
                        title: '確認',
                        text: "払出登録を行います。よろしいですか？",
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

            
            const acceptanceRegister = handleSubmit(async (values) => {
                try {
                    const acceptanceModels = createAcceptanceModel(values);
                    if (acceptanceModels.length === 0) {
                        Swal.fire({
                        icon: 'error',
                        title: '登録する商品がありませんでした。',
                        text: '内容を確認の上、再送信をしてください。',
                        })
                        return false;
                    }
                    
                    let params = new URLSearchParams();
                    params.append("path", "/api/acceptance/register");
                    params.append("_method", 'post');
                    params.append("_csrf", _CSRF);
                    params.append("isOnlyAcceptance", 'true');
                    params.append("acceptanceItems", JSON.stringify(encodeURIToObject(acceptanceModels)));

                    const res = await axios.post(_APIURL, params);

                    if (res.data.code != 200) {
                        throw new Error(res.data.message)
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '登録が完了しました。',
                    }).then((result) => {
                        if(labelCreate.value && res.data.data[0]){
                            const url = _ROOT + '&path=/label/acceptance/' + res.data.data[0];
                            window.open(url, '_blank')
                        }
                        listGet();
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

            const onAcceptanceSubmit = async () => {
                const {
                    valid,
                    errors
                } = await validate();

                const validLot = checkLot();
                const validPayout = checkPayout();

                if (!valid) {
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: '入力エラーがございます。ご確認ください。',
                    })
                } else if (!validLot) {
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: 'ロット情報を正しく入力してください。'
                    })
                } else if (!validPayout) {
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: '請求数・在庫数をご確認の上、払出数を正しく入力してください。'
                    })
                } else {
                    Swal.fire({
                        title: '確認',
                        text: "出庫登録を行います。よろしいですか？",
                        icon: 'info',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            acceptanceRegister();
                        }
                    })
                }
            };

            const register = handleSubmit(async (values) => {
                try {
                    const payoutModels = createPayoutModel(values);
                    if (payoutModels.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: '登録する商品がありませんでした。',
                            text: '内容を確認の上、再送信をしてください。',
                        })
                        return false;
                    }

                    let params = new URLSearchParams();
                    params.append("path", "/api/payout/register");
                    params.append("_method", 'post');
                    params.append("_csrf", _CSRF);
                    params.append("payoutType", 2);
                    params.append("payoutItems", JSON.stringify(encodeURIToObject(payoutModels)));
                    const res = await axios.post(_APIURL, params);

                    if (res.data.code != 200) {
                        if (res.data.code == 998) {
                            throw new Error(res.data.code)
                        } else {
                            throw new Error(res.data.message)
                        }
                    }
                    Swal.fire({
                        icon: 'success',
                        title: '登録が完了しました。',
                    }).then((result) => {
                        if(labelCreate.value && res.data.data[0]){
                            const url = _ROOT + '&path=/label/payout/' + res.data.data[0];
                            window.open(url, '_blank')
                        }
                        listGet();
                    });
                    return true;
                } catch (error) {
                    if (error.message == 998) {
                        Swal.fire({
                            icon: 'error',
                            title: '登録できませんでした。',
                            text: '在庫管理されていない商品が含まれています。'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'システムエラー',
                            text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。'
                        });
                    }
                }

            });

            const reflectSelect = (elem) => {
                if (elem.type === 'gs1-128') {
                    if (values.totalizations[elem.index].lotNumber !== '' || values.totalizations[elem.index].lotDate !== '') {
                        copyItem(elem.index);
                        values.totalizations[elem.index + 1].lotNumber = elem.refLotNumber;
                        values.totalizations[elem.index + 1].lotDate = elem.refLotDate;
                        MicroModal.close("openSelectModal");
                        return false;
                    }
                    values.totalizations[elem.index].lotNumber = elem.refLotNumber;
                    values.totalizations[elem.index].lotDate = elem.refLotDate;
                    MicroModal.close("openSelectModal");
                    return false;
                }

                if (elem.type === 'card') {
                    if ((elem.totalPayoutQuantity + elem.cardQuantity) > elem.totalRequestQuantity) {
                        Swal.fire({
                            icon: 'error',
                            title: '請求必要数を超過するため反映できません。',
                        });
                        return false;
                    }
                    if (values.totalizations[elem.index].payoutQuantity > 0) {
                        copyItem(elem.index);
                        values.totalizations[elem.index + 1].payoutQuantity = elem.cardQuantity;
                        values.totalizations[elem.index + 1].cardQuantity = elem.cardQuantity;
                        values.totalizations[elem.index + 1].card = elem.refCard;
                        values.totalizations[elem.index + 1].lotNumber = values.totalizations[elem.index].lotNumber;
                        values.totalizations[elem.index + 1].lotDate = values.totalizations[elem.index].lotDate;
                        MicroModal.close("openSelectModal");
                        return false;
                    }
                    values.totalizations[elem.index].payoutQuantity = elem.cardQuantity;
                    values.totalizations[elem.index].cardQuantity = elem.cardQuantity;
                    values.totalizations[elem.index].card = elem.refCard;
                    MicroModal.close("openSelectModal");
                    return false;
                }
            }
            const openSelectModal = ref();
            const selectRequestItems = ref([]);
            const addItemByBarcode = (items) => {
                selectRequestItems.value = [];
                if (!items.item || items.item.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: '商品が見つかりませんでした',
                    });
                    return false;
                }

                if (items.type != "gs1-128" && items.type != "card" && items.type != "pickingList") {
                    Swal.fire({
                        icon: 'error',
                        title: 'エラー',
                        text: 'GS1-128・カード・ピッキングリスト以外の\r\nバーコードは読むことができません',
                    });
                }

                if (items.type == "pickingList") {
                    let recordId = '';
                    let sourceDivisionId = '';
                    items.item.forEach((x, id) => {
                        recordId = x.recordId;
                        sourceDivisionId = x.sourceDivisionId;
                    });

                    let hit = 0;
                    values.totalizations.forEach((v, idx) => {
                        if ((recordId === v.recordId) && (sourceDivisionId === v.sourceDivisionId)) {
                            hit++;
                        }
                        if ((v.payoutQuantity === 0) && (recordId === v.recordId) && (sourceDivisionId === v.sourceDivisionId)) {
                            values.totalizations[idx].payoutQuantity = v.requestQuantity;
                        }
                    });
                    if (hit === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: '商品が見つかりませんでした。',
                        });
                        return false;
                    }
                    return false;
                }

                if (items.type == "gs1-128") {
                    let target = [];
                    let targetRequestItems = [];
                    let lotNumber = '';
                    let lotDate = '';
                    let inHospitalItemId = '';
                    items.item.forEach((x, id) => {
                        lotNumber = x.lotNumber;
                        lotDate = x.lotDate;
                        inHospitalItemId = x.inHospitalItemId;
                    });

                    values.totalizations.forEach((v, idx) => {
                        if (inHospitalItemId === v.inHospitalItemId) {
                            target.push(idx);
                            let tmp = Object.assign({}, v);
                            tmp.index = idx;
                            tmp.type = items.type;
                            tmp.refLotNumber = lotNumber;
                            tmp.refLotDate = lotDate;
                            targetRequestItems.push(tmp);
                        }
                    });

                    if (target.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: '商品が見つかりませんでした。',
                        });
                        return false;
                    }

                    if (target.length === 1) {
                        if (values.totalizations[target[0]].lotNumber !== '' || values.totalizations[target[0]].lotDate !== '') {
                            copyItem(target[0]);
                            values.totalizations[target[0] + 1].lotNumber = lotNumber;
                            values.totalizations[target[0] + 1].lotDate = lotDate;
                            return;
                        }
                        values.totalizations[target[0]].lotNumber = lotNumber;
                        values.totalizations[target[0]].lotDate = lotDate;
                    }

                    if (target.length > 1) {
                        selectRequestItems.value = targetRequestItems;
                        openSelectModal.value.open();
                    }
                }

                if (items.type == "card") {
                    let exist = false
                    let target = [];
                    let targetRequestItems = [];
                    let card = '';
                    let cardQuantity = 0;
                    let divisionId = '';
                    let inHospitalItemId = '';

                    items.item.forEach((x, id) => {
                        values.totalizations.forEach((item, idx) => {
                            if ((x.barcode) === item.card) {
                                exist = true;
                            }
                        });
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
                        /*
                        if (x.lotNumber !== '' && x.lotDate !== '') {
                            Swal.fire({
                                icon: 'info',
                                title: 'ロット情報が登録済みのカードです。',
                            });
                            return false;
                        }
                        */
                        card = x.barcode;
                        cardQuantity = parseInt(x.cardQuantity);
                        divisionId = x.divisionId;
                        inHospitalItemId = x.inHospitalItemId;
                    });

                    values.totalizations.forEach((v, idx) => {
                        if ((inHospitalItemId === v.inHospitalItemId) && (divisionId === v.sourceDivisionId)) {
                            target.push(idx);
                            let tmp = Object.assign({}, v);
                            tmp.index = idx;
                            tmp.type = items.type;
                            tmp.cardQuantity = cardQuantity;
                            tmp.refCard = card;
                            targetRequestItems.push(tmp);
                        }
                    });

                    if (target.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: '商品が見つかりませんでした。',
                        });
                        return false;
                    }

                    if (target.length === 1) {
                        if (values.totalizations[target[0]].requestQuantity < cardQuantity) {
                            Swal.fire({
                                icon: 'error',
                                title: 'エラー',
                                text: '請求数を超過するカードは使用できません。',
                            });
                            return false;
                        }
                        if ((values.totalizations[target[0]].totalPayoutQuantity + cardQuantity) > values.totalizations[target[0]].totalRequestQuantity) {
                            Swal.fire({
                                icon: 'error',
                                title: '請求必要数を超過するため反映できません。',
                            });
                            return false;
                        }
                        if (values.totalizations[target[0]].payoutQuantity > 0) {
                            copyItem(target[0]);
                            values.totalizations[target[0] + 1].card = card;
                            values.totalizations[target[0] + 1].payoutQuantity = cardQuantity;
                            values.totalizations[target[0] + 1].cardQuantity = cardQuantity;
                            return;
                        }
                        values.totalizations[target[0]].card = card;
                        values.totalizations[target[0]].payoutQuantity = cardQuantity;
                        values.totalizations[target[0]].cardQuantity = cardQuantity;
                    }

                    if (target.length > 1) {
                        selectRequestItems.value = targetRequestItems;
                        openSelectModal.value.open();
                    }

                }

            }

            return {
                addItemByBarcode,
                selectRequestItems,
                openSelectModal,
                onPrintSubmit,
                pickingListSearch,
                reflectSelect,
                isSubmitting,
                onSubmit,
                loading,
                start,
                complete,
                searchClear,
                searchExec,
                changeParPage,
                perPageOptions,
                listGet,
                totalCount,
                showPages,
                values,
                onOpenModal,
                openModal,
                breadcrumbs,
                numberFormat,
                fields,
                validate,
                makeItems,
                isRequired,
                totalPayoutQuantity,
                checkPayoutQuantity,
                copyItem,
                deleteItem,
                reflectToPayout,
                changeCheck,
                labelCreate,
                onAcceptanceSubmit
            }
        },
        watch: {
            isSubmitting() {
                this.loading = this.isSubmitting;
            },
            labelCreate(bool) {
                localStorage.joypla_payoutLabelCreate = bool;
            },
            fields: {
                async handler(val, oldVal) {
                    await this.validate();
                },
                deep: true
            },
            'values.currentPage': function(val) {
                this.values.totalizations = [];
                this.listGet();
                window.scrollTo(0, 0);
            }
        }
    }).mount('#top');
</script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        new ScrollHint('#totalizationTable', {
            scrollHintIconAppendClass: 'scroll-hint-icon-white', // white-icon will appear
            applyToParents: true,
            i18n: {
                scrollable: 'スクロールできます'
            }
        });
    });
</script>