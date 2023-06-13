<style>
    .paper.A4 > div {
        width: 210mm;
        height: auto;
        min-height: 295mm !important;
    }
</style>
<div id="top" v-cloak>
    <div class="paper A4 ">
        <!-- here -->
        <div class="p-6 relative">
            <header class="text-center text-2xl">出庫伝票</header>
            <main class="mt-6">
                <div class="flex">
                    <div class="flex-auto w-1/2">
                        <div class="text-xxs divide-y divide-solid my-4 border-solid border-b">
                            <div class="flex ">
                                <div class="flex-auto w-1/3">出庫日</div>
                                <div class="flex-auto w-2/3">{{ acceptance.acceptanceDate }}</div>
                            </div>
                            <div class="flex ">
                                <div class="flex-auto w-1/3">出庫番号</div>
                                <div class="flex-auto w-2/3">{{ acceptance.acceptanceId }}</div>
                            </div>
                            <div class="flex ">
                                <div class="flex-auto w-1/3">払出元部署</div>
                                <div class="flex-auto w-2/3">{{ acceptance._sourceDivision?.divisionName }}</div>
                            </div>
                            <div class="flex ">
                                <div class="flex-auto w-1/3">払出先部署</div>
                                <div class="flex-auto w-2/3">{{ acceptance._targetDivision?.divisionName }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-auto w-1/2">
                        <div>
                            <img class="ml-auto h-6" src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
                        </div>
                        <div class="productsLabel mt-6">
                            <!-- <img :src="barcode_src" alt="" class="w-2/3 ml-auto pb-4" /> -->
                            <!-- <img :src="qr_src" alt="" class="w-1/4 ml-auto pb-4"/> -->
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex font-bold">
                        <div class="flex-auto w-1/5">合計金額</div>
                        <div class="flex-auto w-4/5">&yen; {{ numberFormat(acceptance.acceptancePrice) }}</div>
                    </div>
                </div>
                <div class="mt-4">
                    <table class="border-collapse border border-slate-500 w-full">
                        <thead class="text-xs">
                            <tr class="bg-gray-100">
                                <th class="border border-slate-600 w-10 p-1"></th>
                                <th class="border border-slate-600 w-48 p-1">商品情報</th>
                                <th class="border border-slate-600 w-20 p-1">ロット番号</th>
                                <th class="border border-slate-600 w-auto p-1">使用期限</th>
                                <th class="border border-slate-600 w-auto p-1">出庫数</th>
                                <th class="border border-slate-600 w-auto p-1">合計金額</th>
                            </tr>
                        </thead>
                        <tbody class="text-xxs">
                            <template  v-for="( inHospitalItem , idx ) in acceptance._inHospitalItems">
                                <template v-for="( acceptanceItem , pidx ) in getAcceptanceItems(inHospitalItem.inHospitalItemId)">
                                    <tr class="bg-white">
                                        <td class="border border-slate-600 p-1" v-if="pidx === 0" :rowspan="getAcceptanceItems(inHospitalItem.inHospitalItemId)?.length">{{ idx + 1  }}</td>
                                        <td class="border border-slate-600 p-1" v-if="pidx === 0" :rowspan="getAcceptanceItems(inHospitalItem.inHospitalItemId)?.length">
                                            <p class="w-64">
                                                {{ inHospitalItem.makerName }}<br>
                                                {{ inHospitalItem.itemName }}<br>
                                                {{ inHospitalItem.itemCode }}<br>
                                                {{ inHospitalItem.itemStandard }}<br>
                                                {{ inHospitalItem.itemJANCode }}
                                            </p>
                                        </td>
                                        <td class="border border-slate-600 p-1 text-center">
                                            <p class="truncat">{{ acceptanceItem.lotNumber }}</p>
                                        </td>
                                        <td class="border border-slate-600 p-1 text-center">
                                            <p class="truncat">{{ acceptanceItem.lotDate }}</p>
                                        </td>
                                        <td class="border border-slate-600 p-1 text-center">
                                            <p class="truncate">{{ numberFormat(acceptanceItem.acceptanceCount) }} {{ acceptanceItem.quantityUnit }}</p>
                                        </td>
                                        <td class="border border-slate-600 p-1 text-center" v-if="pidx === 0" :rowspan="getAcceptanceItems(inHospitalItem.inHospitalItemId)?.length">
                                            <p class="truncate">&yen;{{ numberFormat(getAcceptancePrice(inHospitalItem.inHospitalItemId)) }}</p>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</div>

<script>
    const acceptance = <?php echo json_encode($acceptance, true); ?>;
    var JoyPlaApp = Vue.createApp({
        components: {},
        setup() {
            const numberFormat = (value) => {
                if (!value) {
                    return 0;
                }
                return new Intl.NumberFormat('ja-JP').format(value);
            };
            
            const getAcceptanceItems = (inHospitalItemId) => {
                return acceptance._items.filter(item => item.inHospitalItemId === inHospitalItemId);
            }
            const getAcceptancePrice = (inHospitalItemId) => {
                return getAcceptanceItems(inHospitalItemId).reduce((sum, item) => sum + parseInt(item.acceptancePrice), 0);
            }

            return {
                getAcceptancePrice,
                getAcceptanceItems,
                numberFormat,
            }
        },
        data() {
            return {
                barcode_src: "",
                qr_src: "",
                acceptance: acceptance,
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
                        bcid: 'code128', // Barcode type
                        text: acceptance.acceptanceId, // Text to encode
                        scale: 3, // 3x scaling factor
                        height: 5, // Bar height, in millimeters
                        includetext: true, // Show human-readable text
                        textxalign: 'center', // Always good to set this
                    });
                    this.barcode_src = canvas.toDataURL();

                    bwipjs.toCanvas(canvas, {
                        bcid: 'qrcode',
                        text: 'https://' + location.host + _ROOT + '&path=/acceptance/' + this.acceptance.acceptanceId, // Text to encode
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