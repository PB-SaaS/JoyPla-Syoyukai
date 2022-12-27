<div id="top" v-cloak>
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto">
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
                    {{ (totalCount == 0)? 0 : ( parseInt(values.perPage) * ( values.currentPage - 1 ) ) + 1 }}件 - {{ (( parseInt(values.perPage) * values.currentPage )  < totalCount ) ?  parseInt(values.perPage) * values.currentPage : totalCount  }}件 / 全 {{ totalCount }}件
                </div>

                <div class="lg:flex lg:flex-row gap-4">
                    <div class="my-4 w-1/5 lg:w-1/6">
                        <v-button-default class="w-full" type="button">印刷</v-button-default>
                    </div>
                    <div class="my-4 w-1/5 lg:w-1/6">
                        <v-button-default class="w-full" type="button">払出登録</v-button-default>
                    </div>
                </div>

                <div class="p-2 bg-gray-300">
                    <v-barcode-search @additem="addItemByBarcode"></v-barcode-search>
                </div>
                <div class="my-2">
                    <div class="max-h-full h-full grid place-content-center w-full lg:flex border border-sushi-600 bg-white mt-3">
                        <div class="flex-1 p-4 relative text-center">バーコードを読み取ってください</div>
                    </div>
                </div>

                <div>

                    <div class="flex flex-col">
                        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full count-table">
                                        <thead class="whitespace-nowrap">
                                            <tr>
                                                <th scope="col" class="px-6 py-4" colspan="7"></th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-2 py-4 text-center">
                                                    <v-button-default class="w-auto" type="button">請求数を反映</v-button-default>
                                                </th>
                                                <th scope="col" class="px-6 py-4" colspan="6"></th>
                                            </tr>
                                        </thead>
                                        <thead class="border whitespace-nowrap">
                                            <tr>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    No
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    請求先部署名
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    商品情報
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">

                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    請求元部署名
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    請求数
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    在庫数
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    払出数
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    ロット番号
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    使用期限
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    カード情報
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    必要数
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center border">
                                                    合計払出数
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-center">

                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-b hover:bg-sushi-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border"></td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border">
                                                    Cell
                                                </td>
                                            </tr>
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
                            <v-input name="yearMonth" type="month" label="請求年月" title="請求年月"></v-input>
                        </div>
                        <div class="my-4">
                            <fieldset>
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">請求種別</div>
                                <div class="flex gap-2">
                                    <v-checkbox value="1" name="requestType" label="請求種別" title="個別請求"></v-checkbox>
                                    <v-checkbox value="2" name="requestType" label="請求種別" title="消費請求"></v-checkbox>
                                </div>
                            </fieldset>
                        </div>
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
                            <v-multiple-select-division-v2 id="sourceDivisionIds" name="sourceDivisionIds" title="請求元部署名" :is-only-my-division="<?php var_export(gate('list_of_item_request_history')->isOnlyMyDivision()); ?>" />
                            </v-multiple-select-division-v2>
                        </div>
                        <div class="my-4">
                            <v-multiple-select-division-v2 id="targetDivisionIds" name="targetDivisionIds" title="請求先部署名" />
                            </v-multiple-select-division-v2>
                        </div>
                        <!--
                        <div class="my-4">
                            <v-select-division name="sourceDivisionIds" label="請求元部署" title="請求元部署名" :is-only-my-division="<?php var_export(gate('list_of_item_request_history')->isOnlyMyDivision()); ?>" />
                        </div>
                        <div class="my-4">
                            <v-select-division name="targetDivisionIds" label="請求先部署" title="請求先部署名" />
                        </div>
                                    -->
                        <div class="mx-auto lg:w-2/3 mb-4 text-center flex items-center gap-6 justify-center">
                            <v-button-default type="button" @click.native="searchClear">クリア</v-button-default>
                            <v-button-primary type="button" @click.native="searchExec">絞り込み</v-button-primary>
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
            'item-view': itemView,
            'v-pagination': vPagination,
            'v-select': vSelect,
            'v-select-division': vSelectDivision,
            'v-checkbox': vCheckbox,
            'v-multiple-select-division-v2': vMultipleSelectDivisionV2,
            'v-input-number': vInputNumber,
            'v-barcode-search': vBarcodeSearch
        },
        setup() {
            const {
                ref,
                onMounted
            } = Vue;
            const {
                useForm
            } = VeeValidate;

            const loading = ref(false);
            const start = () => {
                loading.value = true;
            }

            const complete = () => {
                loading.value = false;
            }

            //start();


            const getCache = () => {
                let url = window.location.href;
                name = 'isCache';
                var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                    results = regex.exec(url);
                if (!results) return null;
                if (!results[2]) return '';
                return decodeURIComponent(results[2].replace(/\+/g, " "));
            }

            const pagetitle = "itemrequesthistory";

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
                url.searchParams.set('yearMonth', values.yearMonth);
                url.searchParams.set('perPage', values.perPage);
                url.searchParams.set('currentPage', values.currentPage);
                url.searchParams.set('sourcerDivisionIds', values.sourcerDivisionIds);
                url.searchParams.set('targetDivisionIds', values.targetDivisionIds);
                url.searchParams.set('requestType', values.requestType);
                history.pushState({}, '', url);
            }

            const {
                meta,
                validate,
                values,
                setFieldValue,
                resetForm
            } = useForm({
                initialValues: {
                    itemName: (getParam("itemName")) ? getParam("itemName") : "",
                    makerName: (getParam("makerName")) ? getParam("makerName") : "",
                    itemCode: (getParam("itemCode")) ? getParam("itemCode") : "",
                    itemStandard: (getParam("itemStandard")) ? getParam("itemStandard") : "",
                    itemJANCode: (getParam("itemJANCode")) ? getParam("itemJANCode") : "",
                    yearMonth: (getParam("yearMonth")) ? getParam("yearMonth") : "",
                    perPage: (Number.isInteger(getParam("perPage"))) ? getParam("perPage") : "10",
                    currentPage: (Number.isInteger(parseInt(getParam("currentPage")))) ? parseInt(getParam("currentPage")) : 1,
                    sourceDivisionIds: (getParam("sourceDivisionIds")) ? (Array.isArray(getParam("sourceDivisionIds")) ? getParam("sourceDivisionIds") : (getParam("sourceDivisionIds")).split(',')) : [],
                    targetDivisionIds: (getParam("targetDivisionIds")) ? (Array.isArray(getParam("targetDivisionIds")) ? getParam("targetDivisionIds") : (getParam("targetDivisionIds")).split(',')) : [],
                    requestType: (getParam("requestType")) ? (Array.isArray(getParam("requestType")) ? getParam("requestType") : (getParam("requestType")).split(',')) : []
                },
            });
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

            const itemRequests = ref([]);

            const perPageOptions = [{
                label: "10件表示",
                value: "10"
            }, {
                label: "50件表示",
                value: "50"
            }, {
                label: "100件表示",
                value: "100"
            }];


            const searchCount = ref(0);

            const listGet = () => {
                let params = new URLSearchParams();
                params.append("path", "/api/itemrequest/history");
                params.append("search", JSON.stringify(encodeURIToObject(values)));
                params.append("_csrf", _CSRF);

                setParam(values);

                //start();

                axios.post(_APIURL, params)
                    .then((response) => {
                        console.log(response);
                        itemRequests.value = response.data.data;
                        totalCount.value = parseInt(response.data.count);
                    })
                    .catch((error) => {
                        console.log(error);
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
                                title: '検索が完了しました'
                            })
                        }
                        searchCount.value++;
                    });
            };

            const changeParPage = () => {
                values.currentPage = 1;
                listGet();
            };

            const add = (elem) => {
                context.emit('additem', elem);
                Toast.fire({
                    icon: 'success',
                    title: '反映しました'
                })
            };

            onMounted(() => {
                //listGet();
            });

            const searchExec = () => {
                values.currentPage = 1;
                listGet();
            };
            const searchClear = () => {
                values.currentPage = 1;
                resetForm({
                    itemName: "",
                    makerName: "",
                    itemCode: "",
                    itemStandard: "",
                    itemJANCode: "",
                    yearMonth: "",
                    sourceDivisionIds: [],
                    targetDivisionIds: [],
                    requestType: [],
                    currentPage: 1,
                    perPage: values.perPage,
                });
                listGet();
            };


            const openPrint = (url) => {
                location.href = _ROOT + "&path=/itemrequest/" + url + "/print";
            }

            const registerPayout = (requestHId) => {
                Swal.fire({
                    title: '請求内容を削除',
                    text: "削除後は元に戻せません。\r\nよろしいですか？",
                    icon: 'warning',
                    confirmButtonText: '削除します',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        start();

                        let params = new URLSearchParams();
                        params.append("path", "/api/itemrequest/" + requestHId + "/delete");
                        params.append("_method", 'delete');
                        params.append("_csrf", _CSRF);

                        const res = await axios.post(_APIURL, params);
                        console.log(res);
                        complete();
                        if (res.data.code != 200) {
                            throw new Error(res.data.message)
                        }
                        Swal.fire({
                            icon: 'success',
                            title: '請求内容の削除が完了しました。',
                        }).then((result) => {
                            location.reload();
                        });
                        return true;
                    }
                }).catch((error) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'システムエラー',
                        text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
                    });
                });
            }

            return {
                registerPayout,
                loading,
                start,
                complete,
                openPrint,
                searchClear,
                searchExec,
                changeParPage,
                perPageOptions,
                listGet,
                totalCount,
                showPages,
                values,
                itemRequests,
                onOpenModal,
                openModal,
                breadcrumbs,
                numberFormat,
            }
        },
        watch: {
            'values.currentPage': function(val) {
                this.listGet();
                window.scrollTo(0, 0);
            },
            values: {
                async handler(val, oldVal) {
                    console.log(JSON.stringify(this.values));
                },
                deep: true
            }
        }
    }).mount('#top');
</script>