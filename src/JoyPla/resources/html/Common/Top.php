<div id="top" v-cloak="v-cloak">
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto flex flex-col md:gap-10">
                <div class="md:grid md:grid-cols-2 md:gap-10">
                    <v-barcode-slip-search></v-barcode-slip-search>
                </div>
                <div class="md:grid md:grid-cols-4 md:gap-10">
                    <card-button main-color="bg-cornflower-blue-500" text-color="text-cornflower-blue-50"
                        sub-color="bg-cornflower-blue-600" label-text="消費" label-sub-text="Consumption"
                        path="/consumption">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-cornflower-blue-700" stroke-linejoin="round"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-scooter-500" text-color="text-scooter-50" sub-color="bg-scooter-600"
                        label-text="発注" label-sub-text="Order" path="/order">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-scooter-700" stroke-linejoin="round"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-sushi-500" text-color="text-sushi-50" sub-color="bg-sushi-600"
                        label-text="請求" label-sub-text="ItemRequest" path="itemrequest">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-sushi-700" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-chelsea-cucumber-500" text-color="text-chelsea-cucumber-50"
                        sub-color="bg-chelsea-cucumber-600" label-text="払出" label-sub-text="Paypout" path="payout">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-chelsea-cucumber-700" stroke-linejoin="round"
                                d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                        </svg>
                    </card-button>
                </div>
                <div class="md:grid md:grid-cols-4 md:gap-10">
                    <card-button main-color="bg-buttercup-500" text-color="text-buttercup-50"
                        sub-color="bg-buttercup-600" label-text="在庫" label-sub-text="stock" path="stock">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-buttercup-700" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-carrot-orange-500" text-color="text-carrot-orange-50"
                        sub-color="bg-carrot-orange-600" label-text="カード" label-sub-text="card" path="card">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-carrot-orange-700" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-outrageous-orange-500" text-color="text-outrageous-orange-50"
                        sub-color="bg-outrageous-orange-600" label-text="実績" label-sub-text="TrackRecord"
                        path="trackrecord">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-outrageous-orange-700" stroke-linejoin="round"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-pomegranate-500" text-color="text-pomegranate-50"
                        sub-color="bg-pomegranate-600" label-text="月次レポート" label-sub-text="MonthlyReport"
                        path="monthlyreport">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-pomegranate-700" stroke-linejoin="round"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </card-button>
                </div>
                <div class="md:grid md:grid-cols-4 md:gap-10">
                    <card-button main-color="bg-lilac-bush-500" text-color="text-lilac-bush-50"
                        sub-color="bg-lilac-bush-600" label-text="商品" label-sub-text="Product" path="/product">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-lilac-bush-700" stroke-linejoin="round"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-amethyst-500" text-color="text-amethyst-50" sub-color="bg-amethyst-600"
                        label-text="見積" label-sub-text="Estimate" path="/estimate">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-amethyst-700" stroke-linejoin="round"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-flush-orange-500" text-color="text-flush-orange-50"
                        sub-color="bg-flush-orange-600" label-text="貸出" label-sub-text="Borrowing" path="/lending">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-flush-orange-700" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-pine-green-500" text-color="text-pine-green-50"
                        sub-color="bg-pine-green-600" label-text="棚卸" label-sub-text="Stocktaking" path="stocktaking">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-pine-green-700" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </card-button>
                </div>
                <div class="md:grid md:grid-cols-4 md:gap-10">
                    <card-button main-color="bg-ochre-500" text-color="text-ochre-50"
                        sub-color="bg-ochre-600" label-text="会計" label-sub-text="Accountant"
                        path="accountant">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-ochre-700" stroke-linejoin="round"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-san-juan-500" text-color="text-san-juan-50" sub-color="bg-san-juan-600"
                        label-text="ユーザー" label-sub-text="User" path="/user">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-san-juan-700" stroke-linejoin="round"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </card-button>
                    <card-button main-color="bg-stack-500" text-color="text-stack-50" sub-color="bg-stack-600"
                        label-text="オプション" label-sub-text="Option" path="/option">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" class="stroke-stack-700" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" class="stroke-stack-700" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </card-button>
                </div>
                <div id="news-area" class="mx-auto md:w-2/3 w-full my-6">
                    <div class="bg-eastern-blue-700 text-white">
                        <p class="my-2 font-bold text-lg mx-4">最新のお知らせ</p>
                    </div>
                    <div class="hover:bg-sushi-50" v-for="(notification) in notifications">
                        <div class="border-b-2 border-solid border-gray-100 w-full">
                            <div class="flex gap-4 items-center p-2 ">
                                <div v-if="notification.type == '2'"
                                    class="flex-initia text-center text-xs font-semibold inline-block py-1 px-2 rounded text-white bg-eastern-blue-900 w-24">
                                    重要なお知らせ
                                </div>
                                <div v-else-if="notification.type == '3'"
                                    class="flex-initial text-center text-xs font-semibold inline-block py-1 px-2 rounded text-white bg-eastern-blue-900 w-24">
                                    メンテナンス
                                </div>
                                <div v-else
                                    class="flex-initial text-center text-xs font-semibold inline-block py-1 px-2 rounded text-white bg-eastern-blue-900 w-24">
                                    お知らせ
                                </div>
                                <div class="flex-initial w-32">{{ (notification.registrationTime).split(' ')[0] }}</div>
                                <div class="flex-1">
                                    <a :href="_ROOT+'&path=/notification/'+notification.notificationId"
                                        class="no-underline hover:underline">{{ notification.title }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a :href="_ROOT + '&path=/notification'" type="button"
                            class="text-white bg-eastern-blue-700 hover:bg-eastern-blue-800 focus:outline-none focus:ring-4 rounded-full text-sm px-5 py-2.5 text-center mr-2 my-2">過去のお知らせを表示</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var JoyPlaApp = Vue
        .createApp({
            setup() {
                const {
                    ref,
                    onCreated,
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

                const sleepComplate = () => {
                    window.setTimeout(function() {
                        complete();
                    }, 500);
                }
                start();

                const searchValue = ref("");

                const notifications = ref([]);

                const {
                    meta,
                    validate,
                    values,
                    setFieldValue,
                    resetForm
                } = useForm({
                    initialValues: {
                        perPage: 3,
                        currentPage: 1,
                    },
                });
                const listGet = () => {
                    let params = new URLSearchParams();
                    params.append("path", "/api/notification/show");
                    params.append("search", JSON.stringify(encodeURIToObject(values)));
                    params.append("_csrf", _CSRF);

                    start();

                    axios.post(_APIURL, params)
                        .then((response) => {
                            notifications.value = response.data.data;
                            totalCount.value = parseInt(response.data.count);
                        })
                        .catch((error) => {
                            complete();
                        })
                        .then(() => {
                            complete();
                        });
                };

                onMounted(() => {
                    sleepComplate()
                    listGet()
                });

                return {
                    loading,
                    start,
                    complete,
                    searchValue,
                    notifications
                }
            },
            components: {
                'v-barcode-slip-search': vBarcodeSlipSearch,
                'v-loading': vLoading,
                'v-breadcrumbs': vBreadcrumbs,
                'card-button': cardButton,
                'header-navi': headerNavi
            },
            data() {
                return {
                    breadcrumbs: []
                }
            }
        })
        .mount('#top');
</script>