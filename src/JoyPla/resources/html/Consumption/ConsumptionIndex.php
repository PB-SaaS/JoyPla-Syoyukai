<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <h1 class="text-2xl mb-2">消費伝票の詳細</h1>
        <hr>
        <div class="p-4 text-base bg-gray-100 border border-gray-400">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">消費日</div>
            <div class="flex-auto">{{ consumption.consumptionDate }}</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">消費番号</div>
            <div class="flex-auto">{{ consumption.consumptionId }}</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">消費タイプ</div>
            <div class="flex-auto">{{ ( consumption.consumptionStatus === 1)? "通常消費" : "貸出品" }}</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">消費部署</div>
            <div class="flex-auto">{{ consumption.division.divisionName }}</div>
          </div>
        </div>
        <hr>
        <div class="p-4 text-lg font-bold">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">合計金額</div>
            <div class="flex-auto">&yen; {{ numberFormat( consumption.totalAmount) }}</div>
          </div>
        </div>
        <hr>
        <div class="p-4 text-base">
          <div class="lg:flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-full text-lg font-bold">商品情報</div>
            <div class="flex-auto">
              <div class="w-full lg:flex mt-3" v-for="(consumptionItem) in consumption.consumptionItems">
                <div class="lg:flex-1 flex lg:w-3/4">
                  <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="consumptionItem.itemImage"></item-view>
                  <div class="flex-1 pl-4 lg:flex gap-6 break-all">
                    <div class="flex-auto lg:w-4/5 w-full">
                      <h3 class="text-xl font-bold font-heading">{{ consumptionItem.item.makerName }}</h3>
                      <p class="text-md font-bold font-heading">{{ consumptionItem.item.itemName }}</p>
                      <p class="text-md text-gray-500">{{ consumptionItem.item.itemCode }}</p>
                      <p class="text-md text-gray-500">{{ consumptionItem.item.itemStandard }}</p>
                      <p class="text-md text-gray-500">{{ consumptionItem.item.itemJANCode }}</p>
                      <p class="text-md text-gray-900" v-if="( consumptionItem.lot.lotNumber != '' && consumptionItem.lot.lotDate != '' )">
                      ロット情報：{{ consumptionItem.lot.lotNumber }} / {{ consumptionItem.lot.lotDate }}
                      </p>
                      <p class="text-base text-gray-900">
                      {{ numberFormat(consumptionItem.consumptionQuantity) }}{{ consumptionItem.quantity.quantityUnit }}
                      </p>
                      <p>
                        <span class="text-blue-700 text-lg mr-4">&yen; {{ numberFormat(consumptionItem.consumptionPrice) }}</span>
                        <span class="text-sm text-gray-900">( &yen; {{ numberFormat(consumptionItem.unitPrice) }} / {{ consumptionItem.quantity.quantityUnit }} )</span>
                      </p>
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
  </div>
</div>
<script>

const PHPData = <?php echo json_encode($viewModel, true) ?>;

var JoyPlaApp = Vue.createApp({ 
    components: {
      'v-loading' : vLoading,
      'header-navi' : headerNavi,
      'v-breadcrumbs': vBreadcrumbs,
      'item-view' : itemView
    },
    setup(){
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
      const breadcrumbs = [
          {
            text: '消費メニュー',
            disabled: false,
            href: '%url/rel:mpgt:Root%&path=/consumption',
          },
          {
            text: '消費一覧',
            disabled: false,
            href: '%url/rel:mpgt:Root%&path=/consumption/show&isCache=true',
          },
          {
            text: '消費伝票の詳細',
            disabled: true,
          }
        ];

      const consumption = PHPData.consumption;

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return value.toString().replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,' );
      };
      return {
        numberFormat,
        consumption,
        breadcrumbs,
        loading, 
        start, 
        complete
      }
  },
  watch: {
  }
}).mount('#top');
</script> 