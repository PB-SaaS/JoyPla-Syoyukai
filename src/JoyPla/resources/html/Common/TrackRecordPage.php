<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <div class="bg-outrageous-orange-500 text-outrageous-orange-50 py-1 font-bold col-span-4 px-3">実績</div>
        <div class="md:grid md:grid-cols-4 md:gap-10 gap-4 my-2">
          <card-button main-color="bg-outrageous-orange-500" text-color="text-outrageous-orange-50" sub-color="bg-outrageous-orange-600" label-text="消費履歴詳細一覧" path="" base-url="%url/rel:mpgt:ConsumeHistList%">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" class="stroke-outrageous-orange-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <card-button main-color="bg-outrageous-orange-500" text-color="text-outrageous-orange-50" sub-color="bg-outrageous-orange-600" label-text="発注履歴詳細一覧" path="" base-url="%url/rel:mpgt:OrderHistList%">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" class="stroke-outrageous-orange-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <card-button main-color="bg-outrageous-orange-500" text-color="text-outrageous-orange-50" sub-color="bg-outrageous-orange-600" label-text="入荷履歴詳細一覧" path="" base-url="%url/rel:mpgt:ReceiveHistList%">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" class="stroke-outrageous-orange-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <card-button main-color="bg-outrageous-orange-500" text-color="text-outrageous-orange-50" sub-color="bg-outrageous-orange-600" label-text="払出履歴詳細一覧" path="" base-url="%url/rel:mpgt:PayoutHistList%">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" class="stroke-outrageous-orange-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <card-button main-color="bg-outrageous-orange-500" text-color="text-outrageous-orange-50" sub-color="bg-outrageous-orange-600" label-text="棚卸実績" path="" base-url="%url/rel:mpgt:Inventory%&Action=inventoryMovement">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" class="stroke-outrageous-orange-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <card-button main-color="bg-outrageous-orange-500" text-color="text-outrageous-orange-50" sub-color="bg-outrageous-orange-600" label-text="請求履歴詳細一覧" path="" base-url="%url/rel:mpgt:ItemReqHistList%">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" class="stroke-outrageous-orange-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <card-button main-color="bg-outrageous-orange-500" text-color="text-outrageous-orange-50" sub-color="bg-outrageous-orange-600" label-text="払出予定商品実績" path="" base-url="%url/rel:mpgt:PayoutHistList%&Action=pickingItemHistory">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" class="stroke-outrageous-orange-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
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
          text: '実績メニュー',
          disabled: true,
        }, ],
      }
    },
  }).mount('#top');
</script>