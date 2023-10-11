<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <div class="bg-lilac-bush-500 text-lilac-bush-50 py-1 font-bold col-span-4 px-3">商品</div>
        <div class="md:grid md:grid-cols-4 md:gap-10 gap-4 my-2">
          <card-button main-color="bg-lilac-bush-500" text-color="text-lilac-bush-50" sub-color="bg-lilac-bush-600" label-text="商品一覧" path="" base-url="%url/rel:mpgt:Product%&Action=Item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" class="stroke-lilac-bush-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>  
          </card-button>
          <card-button main-color="bg-lilac-bush-500" text-color="text-lilac-bush-50" sub-color="bg-lilac-bush-600" label-text="金額一覧" path="" base-url="%url/rel:mpgt:Price%&Action=Price">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" class="stroke-lilac-bush-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <card-button main-color="bg-lilac-bush-500" text-color="text-lilac-bush-50" sub-color="bg-lilac-bush-600" label-text="院内商品一覧" path="" base-url="%url/rel:mpgt:Product%&Action=InHospitalItem">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" class="stroke-lilac-bush-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <?php if(gate('register_of_items')->can()): ?>
          <card-button main-color="bg-lilac-bush-500" text-color="text-lilac-bush-50" sub-color="bg-lilac-bush-600" label-text="商品登録" path="" base-url="%url/rel:mpgt:page_262241%&Action=itemReg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" class="stroke-lilac-bush-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <card-button main-color="bg-lilac-bush-500" text-color="text-lilac-bush-50" sub-color="bg-lilac-bush-600" label-text="商品・金額・院内商品情報登録" path="/product/ItemAndPriceAndInHospitalRegist/input">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" class="stroke-lilac-bush-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <?php endif ?>
          <card-button main-color="bg-lilac-bush-500" text-color="text-lilac-bush-50" sub-color="bg-lilac-bush-600" label-text="商品リスト" path="/product/itemList/index">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" class="stroke-lilac-bush-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <card-button main-color="bg-lilac-bush-500" text-color="text-lilac-bush-50" sub-color="bg-lilac-bush-600" label-text="入荷ラベル・償還ラベル発行" path="/product/label/index">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" class="stroke-lilac-bush-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
      'card-button' : cardButton,
      'v-breadcrumbs': vBreadcrumbs,
      'header-navi' : headerNavi
    },
    data(){
      return {
        breadcrumbs: [
          {
            text: '商品メニュー',
            disabled: true,
          },
        ],
      }
    }, 
}).mount('#top');
</script> 