<div id="top" v-cloak>
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto">
                <div class="bg-sushi-500 text-sushi-50 py-1 font-bold col-span-4 px-3">請求</div>
                <div class="md:grid md:grid-cols-4 md:gap-10 gap-4 my-2">
                    <?php if (gate('register_of_item_requests')->can()) : ?>
                        <card-button main-color="bg-sushi-500" text-color="text-sushi-50" sub-color="bg-sushi-600" label-text="個別請求" path="/itemrequest/register">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" class="stroke-sushi-300" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                        </card-button>
                    <?php endif ?>
                    <?php if (gate('list_of_item_request_history')->can()) : ?>
                        <card-button main-color="bg-sushi-500" text-color="text-sushi-50" sub-color="bg-sushi-600" label-text="請求履歴一覧" path="/itemrequest/history">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" class="stroke-sushi-300" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </card-button>
                    <?php endif ?>
                    <?php if (gate('totalization_of_item_requests')->can()) : ?>
                        <card-button main-color="bg-sushi-500" text-color="text-sushi-50" sub-color="bg-sushi-600" label-text="請求商品一覧" path="/itemrequest/totalization">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" class="stroke-sushi-300" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </card-button>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var JoyPlaApp = Vue.createApp({
        setup() {
            const {
                ref,
                onCreated,
                onMounted
            } = Vue;
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

            return {
                loading,
                start,
                complete
            }
        },
        components: {
            'v-loading': vLoading,
            'card-button': cardButton,
            'v-breadcrumbs': vBreadcrumbs,
            'header-navi': headerNavi
        },
        data() {
            return {
                breadcrumbs: [{
                    text: '請求メニュー',
                    disabled: true,
                }, ],
            }
        },
    }).mount('#top');
</script>