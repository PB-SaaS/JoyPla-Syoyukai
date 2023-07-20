<div id="top" v-cloak>
    <template v-if="stocktakingListRows.length > 0">
        <template v-for="(stocktakingListRowsArray , key) in printSplitter(stocktakingListRows)">
            <div class="paper A4 ">
                <!-- here -->
                <div class="p-6 relative">
                    <header class="text-center text-xl">棚卸商品管理表</header>
                    <main class="mt-6">
                        <template v-if="key === 0">
                            <div class="flex">
                                <div class="flex-auto w-1/2">
                                    <div class="text-xxs divide-y divide-solid my-4 border-solid border-b">
                                        <div class="flex ">
                                            <div class="flex-auto w-1/3">発行日</div>
                                            <div class="flex-auto w-2/3"><?php echo date('Y年m月d日'); ?></div>
                                        </div>
                                        <div class="flex ">
                                            <div class="flex-auto w-1/3">部署</div>
                                            <div class="flex-auto w-2/3">{{ stocktakingList.divisionName }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-auto w-1/2">
                                    <div>
                                        <img class="ml-auto h-6" src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div class="mt-4">
                            <table class="border-collapse border border-slate-500 w-full" id="stocktakingListRow">
                                <thead class="text-xs">
                                    <tr class="bg-gray-100">
                                        <th class="border border-slate-600 text-xxs w-5 p-1">No.</th>
                                        <th class="border border-slate-600 text-xxs w-48 p-1">商品情報</th>
                                        <th class="border border-slate-600 text-xxs w-20 p-1">卸業者</th>
                                        <th class="border border-slate-600 text-xxs w-36 p-1">バーコード</th>
                                        <th class="border border-slate-600 text-xxs w-10 p-1">単価</th>
                                        <th class="border border-slate-600 text-xxs w-20 p-1">入数</th>
                                        <th class="border border-slate-600 text-xxs w-10 p-1">在庫数</th>
                                        <th class="border border-slate-600 text-xxs w-14 p-1">棚卸必須</th>
                                        <th class="border border-slate-600 text-xxs w-auto p-1">備考</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xxs">
                                    <tr class="bg-white" v-for="( stocktakingListRow, index ) in stocktakingListRowsArray">
                                        <td class="border border-slate-600 p-1">{{key <= 1 ? (key * 10) + index + 1 : (key * 11) + index }}</td>
                                        <td class="border border-slate-600 p-1">
                                            <p class="w-44">
                                                {{ stocktakingListRow.itemName }}<br>
                                                {{ stocktakingListRow.makerName }}<br>
                                                {{ stocktakingListRow.itemCode }}<br>
                                                {{ stocktakingListRow.itemStandard }}<br>
                                                {{ stocktakingListRow.itemJANCode }}<br>
                                            </p>
                                        </td>
                                        <td class="border border-slate-600 p-1">
                                            <p class="truncat">{{ stocktakingListRow.distributorName }}</p>
                                        </td>
                                        <td class="border border-slate-600 py-1 px-3">
                                            <img class="mx-auto" :src="stocktakingListRow.janCodeImg" />
                                        </td>
                                        <td class="border border-slate-600 p-1">￥{{ stocktakingListRow.stocktakingUnitPrice }}</td>
                                        <td class="border border-slate-600 p-1">
                                            {{ stocktakingListRow.quantity }}{{ stocktakingListRow.quantityUnit }}/{{ stocktakingListRow.itemUnit }}
                                        </td>
                                        <td class="border border-slate-600 p-1">
                                            <p class="truncat" v-if="stocktakingListRow.stockQuantity">{{ stocktakingListRow.stockQuantity }}</p>
                                            <p class="truncat" v-else>0</p>
                                        </td>
                                        <td class="border border-slate-600 p-1">
                                            <span v-if="stocktakingListRow.mandatoryFlag=='1'">必須</span>
                                        </td>
                                        <td class="border border-slate-600 p-1 text-right"></td>
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
<style>
    #stocktakingListRow tr td{
        page-break-inside: avoid;
    }
    @media print{
        #stocktakingListRow thead{
            margin-top: 10px;
        }
    }
</style>
<script>
    const stocktakingList = <?php echo json_encode($stocktakingList, true); ?>;
    let stocktakingListRows = <?php echo json_encode($stocktakingListRows, true); ?>;
    var JoyPlaApp = Vue.createApp({
        components: {},
        setup() {
            const numberFormat = (value) => {
                if (!value) {
                    return 0;
                }
                return new Intl.NumberFormat('ja-JP').format(value);
            };
            const printSplitter = (array) => {
                let stocktakingLists = [];
                let length = array.length > 10 ? 1 + Math.ceil((array.length - 10) / 11) : 1;
                for(let i = 0; i < length; i++){
                    if(i === 0)
                    {
                        stocktakingLists.push(array.slice(0,10));
                    }else{
                        stocktakingLists.push(array.slice(10+((i-1)*11), 10+(i*11)));
                    }
                }
                return stocktakingLists;
            }
            return {
                numberFormat,
                printSplitter,
            }
        },
        data() {
            return {
                barcode_src: "",
                qr_src: "",
                stocktakingList: stocktakingList,
                stocktakingListRows: stocktakingListRows,
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
                        text: this.stocktakingList.stockListId, // Text to encode
                        scale: 3, // 3x scaling factor
                        height: 5, // Bar height, in millimeters
                        includetext: true, // Show human-readable text
                        textxalign: 'center', // Always good to set this
                    });
                    this.barcode_src = canvas.toDataURL();

                    bwipjs.toCanvas(canvas, {
                        bcid: 'qrcode',
                        text: 'https://' + location.host + _ROOT + '&path=/stocktakingList/' + this.stocktakingList.stockListId, // Text to encode
                    });

                    this.qr_src = canvas.toDataURL();
                    let labelBarcode = '';
                    let quantityNum = '';

                    this.stocktakingListRows.forEach(async(x, xkey) => {
                        let canvas = await document.createElement("canvas");
                        labelBarcode = '';
                        quantityNum = '';
                        if(x.quantity >= 10000 ){
                            quantityNum = '9999';
                        } else {
                            if(x.quantity < 1 ){
                                quantityNum = 1;
                            }else{
                                quantityNum = x.quantity;
                            }
                            quantityNum = quantityNum.padStart(4, "0");
                        }
                        labelBarcode = "01"+x.labelId+quantityNum;
                        bwipjs.toCanvas(canvas, {
                            bcid: 'code128', // Barcode type
                            text: labelBarcode, // Text to encode
                            scale: 3, // 3x scaling factor
                            height: 5, // Bar height, in millimeters
                            includetext: true, // Show human-readable text
                            textxalign: 'center', // Always good to set this
                        });
                        this.stocktakingListRows[xkey].janCodeImg = canvas.toDataURL();
                    });
                } catch (e) {
                    console.error(e);
                    return;
                }
            },
        }
    }).mount('#top');
</script>