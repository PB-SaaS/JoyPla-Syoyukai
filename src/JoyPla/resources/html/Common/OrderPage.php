<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto bg-white">
        <div class="index container mx-auto md:flex md:gap-10">
          <div class="flex-1">
            <div class="bg-scooter-500 text-scooter-50 py-1 font-bold col-span-4 px-3">発注</div>
            <div class="md:grid md:grid-cols-2 md:gap-10 gap-4 my-2">
              <?php if(gate('register_of_unordered_slips')->can()): ?>
              <card-button main-color="bg-scooter-500" text-color="text-scooter-50" sub-color="bg-scooter-600" label-text="個別発注" path="/order/register">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" class="stroke-scooter-700" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>  
              </card-button>
              <?php endif ?>
              <?php if(gate('fixed_quantity_order_slips')->can()): ?>
              <card-button main-color="bg-scooter-500" text-color="text-scooter-50" sub-color="bg-scooter-600" label-text="定数発注" path="/order/fixedQuantityOrder">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" class="stroke-scooter-700" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
              </card-button>
              <?php endif ?>
              <?php if(gate('list_of_unordered_slips')->can()): ?>
              <card-button main-color="bg-scooter-500" text-color="text-scooter-50" sub-color="bg-scooter-600" label-text="未発注書一覧" path="/order/unapproved/show">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" class="stroke-scooter-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </card-button>
              <?php endif ?>
              <?php if(gate('list_of_order_slips')->can()): ?>
              <card-button main-color="bg-scooter-500" text-color="text-scooter-50" sub-color="bg-scooter-600" label-text="発注書一覧" path="/order/show">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" class="stroke-scooter-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </card-button>
              <?php endif ?>
            </div>
          </div>
          <div class="flex-1">
            <div class="bg-scooter-500 text-scooter-50 py-1 font-bold col-span-4 px-3">入庫</div>
            <div class="md:grid md:grid-cols-2 md:gap-10 gap-4 my-2">
              <?php if(gate('receipt')->can()): ?>
              <card-button main-color="bg-scooter-500" text-color="text-scooter-50" sub-color="bg-scooter-600" label-text="個別入荷" path="/received/register">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" class="stroke-scooter-700" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>  
              </card-button>
              <?php endif ?>
              <?php if(gate('receipt')->can()): ?>
              <card-button main-color="bg-scooter-500" text-color="text-scooter-50" sub-color="bg-scooter-600" label-text="入荷照合" path="/received/order/list">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" class="stroke-scooter-700" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>
              </card-button>
              <?php endif ?>
              <?php if(gate('list_of_acceptance_inspection_slips')->can()): ?>
              <card-button main-color="bg-scooter-500" text-color="text-scooter-50" sub-color="bg-scooter-600" label-text="検収書一覧" path="/received/show">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" class="stroke-scooter-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </card-button>
              <?php endif ?>
              <?php if(gate('list_of_return_slips')->can()): ?>
              <card-button main-color="bg-scooter-500" text-color="text-scooter-50" sub-color="bg-scooter-600" label-text="返品書一覧" path="/return/show">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" class="stroke-scooter-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </card-button>
              <?php endif ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> 
<script>
var JoyPlaApp = Vue.createApp({
    setup() {
        const {ref , onCreated , onMounted} = Vue;
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
        
        onMounted( () => {
          sleepComplate()
        });

        return {loading, start, complete}
    },
    components: {
      'v-loading' : vLoading,
      'v-breadcrumbs': vBreadcrumbs,
      'card-button' : cardButton,
      'header-navi' : headerNavi
    },
    data(){
      return { 
        breadcrumbs: [
            {
              text: '発注メニュー',
              disabled: true,
            }
        ],
      }
    },
}) .mount('#top');
</script>