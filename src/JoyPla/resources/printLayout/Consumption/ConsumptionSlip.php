<div id="top" v-cloak>
    <template v-if="consumptionItems.length > 0">
        <template v-for="(consumptionItemArray , key) in consumptionItems">
                <div class="paper A4 ">
                    <!-- here -->
                    <div class="p-6 relative">
                    
                        <header class="text-center text-2xl">消費伝票</header>
                        <main class="mt-6">
                            <template v-if="key === 0">
                                <div class="flex">
                                    <div class="flex-auto w-1/2">
                                        <h4 class="text-xl font-bold">{{  consumption.hospital.hospitalName}}</h4>
                                        <div class="divide-y divide-solid my-4 border-solid border-b">
                                            <div class="flex py-2">
                                                <div class="flex-auto w-1/3">消費日</div>
                                                <div class="flex-auto w-2/3">{{ consumption.consumptionDate }}</div>
                                            </div>
                                            <div class="flex py-2">
                                                <div class="flex-auto w-1/3">消費番号</div>
                                                <div class="flex-auto w-2/3">{{ consumption.consumptionId }}</div>
                                            </div>
                                            <div class="flex py-2">
                                                <div class="flex-auto w-1/3">消費タイプ</div>
                                                <div class="flex-auto w-2/3">{{ consumption.consumptionStatusToString }}</div>
                                            </div>
                                            <div class="flex py-2">
                                                <div class="flex-auto w-1/3">消費部署</div>
                                                <div class="flex-auto w-2/3">{{ consumption.division.divisionName }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-auto w-1/2">
                                        <div>
                                            <img class="ml-auto h-6" src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png"/>
                                        </div>
                                        <div class="productsLabel mt-6">
                                            <img :src="barcode_src" alt="" class="w-2/3 ml-auto pb-4"/>
                                            <img :src="qr_src" alt="" class="w-1/4 ml-auto pb-4"/>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex font-bold">
                                        <div class="flex-auto w-1/5">合計金額</div>
                                        <div class="flex-auto w-4/5">&yen; {{ numberFormat(consumption.totalAmount) }}</div>
                                    </div>
                                </div>
                            </template>
                            <div class="mt-4">
                                <table class="border-collapse border border-slate-500 w-full">
                                    <thead class="text-xs">
                                        <tr class="bg-gray-100">
                                            <th class="border border-slate-600 w-10 p-1"></th>
                                            <th class="border border-slate-600 w-64 p-1">商品情報</th>
                                            <th class="border border-slate-600 w-20 p-1">ロット番号</th>
                                            <th class="border border-slate-600 w-20 p-1">消費期限</th>
                                            <th class="border border-slate-600 w-20 p-1">消費数</th>
                                            <th class="border border-slate-600 w-20 p-1">単価</th>
                                            <th class="border border-slate-600 w-auto p-1">消費金額</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-xxs">
                                        <tr class="bg-white" v-for="( consumptionItem ) in consumptionItemArray">
                                            <td class="border border-slate-600 p-1">{{ consumptionItem.id  }}</td>
                                            <td class="border border-slate-600 p-1">
                                                <p class="truncate w-64">
                                                    {{ consumptionItem.item.itemName }} 
                                                    {{ consumptionItem.item.itemCode }} 
                                                    {{ consumptionItem.item.itemStandard }} 
                                                </p>
                                            </td>
                                            <td class="border border-slate-600 p-1">
                                                <p class="truncate w-20">{{ consumptionItem.lot.lotNumber }}</p>
                                            </td>
                                            <td class="border border-slate-600 p-1">
                                                <p class="truncate w-20">{{ consumptionItem.lot.lotDate }}</p>
                                            </td>
                                            <td class="border border-slate-600 p-1">
                                                <p class="truncate w-20">{{ numberFormat(consumptionItem.consumptionQuantity) }}{{ consumptionItem.quantity.quantityUnit }}</p>
                                            </td>
                                            <td class="border border-slate-600 p-1">
                                                <p class="truncate w-20">&yen; {{ numberFormat(consumptionItem.unitPrice) }}</p>
                                            </td>
                                            <td class="border border-slate-600 p-1">&yen; {{ numberFormat(consumptionItem.consumptionPrice) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </main>
                    </div>
                </div>
        </template>
    </template>
</div>

<script>
    const consumption = <?php echo json_encode($consumption,true) ?>;
    const consumptionItems = <?php echo json_encode($consumptionItems,true) ?>;
var JoyPlaApp = Vue.createApp({
    components: {
    },
    setup(){
      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };
      return {
        numberFormat,
      }
    },
    data() {
        return {
            barcode_src: "",
            qr_src: "",
            consumption: consumption,
            consumptionItems: consumptionItems,
        };
    },
    async created() {
        await this.createBarCode();
    },
    methods: {
    async createBarCode() {
            let canvas = await document.createElement("canvas");
            try {
                bwipjs.toCanvas(canvas, {
                        bcid:        'code128',       // Barcode type
                        text:        this.consumption.consumptionId,    // Text to encode
                        scale:       3,               // 3x scaling factor
                        height:      5,              // Bar height, in millimeters
                        includetext: true,            // Show human-readable text
                        textxalign:  'center',        // Always good to set this
                });
                this.barcode_src = canvas.toDataURL();

                bwipjs.toCanvas(canvas, {
                        bcid:        'qrcode',
                        text:        'https://'+ location.host +_ROOT + '&path=/consumption/' + this.consumption.consumptionId ,    // Text to encode
                    }); 

                this.qr_src = canvas.toDataURL();
            } catch (e) {
                console.error(e);
                return;
            }
        },
    }
}).mount('#top'); 
</script>