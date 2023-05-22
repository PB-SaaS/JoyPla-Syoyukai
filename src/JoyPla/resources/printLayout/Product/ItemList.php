<div id="top" v-cloak>
    <template v-if="itemListRows.length > 0">
        <template v-for="(itemListRowsArray , key) in printSplitter(itemListRows)">
            <div class="paper A4 ">
                <!-- here -->
                <div class="p-6 relative">
                    <header class="text-center text-2xl">{{itemList.itemListName}}</header>
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
                                            <div class="flex-auto w-1/3">商品リスト番号</div>
                                            <div class="flex-auto w-2/3">{{ itemList.itemListId }}</div>
                                        </div>
                                        <div class="flex ">
                                            <div class="flex-auto w-1/3">部署</div>
                                            <div class="flex-auto w-2/3">{{ itemList.divisionName }}</div>
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
                            <table class="border-collapse border border-slate-500 w-full" id="itemListRow">
                                <thead class="text-xs">
                                    <tr class="bg-gray-100">
                                        <th class="border border-slate-600 w-10 p-1"></th>
                                        <th class="border border-slate-600 w-48 p-1">商品情報</th>
                                        <th class="border border-slate-600 w-20 p-1">卸業者</th>
                                        <th class="border border-slate-600 w-36 p-1">バーコード</th>
                                        <th class="border border-slate-600 w-auto p-1">備考</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xxs">
                                    <tr class="bg-white" v-for="( itemListRow, index ) in itemListRowsArray">
                                        <td class="border border-slate-600 p-1">{{ index + 1 }}</td>
                                        <td class="border border-slate-600 p-1">
                                            <p class="w-64">
                                                {{ itemListRow.itemName }}<br>
                                                {{ itemListRow.itemCode }}<br>
                                                {{ itemListRow.itemStandard }}<br>
                                                {{ itemListRow.quantity }} / {{ itemListRow.quantityUnit }} / {{ itemListRow.itemUnit }}<br>
                                            </p>
                                        </td>
                                        <td class="border border-slate-600 p-1">
                                            <p class="truncat">{{ itemListRow.distributorName }}</p>
                                        </td>
                                        <td class="border border-slate-600 py-1 px-3">
                                            <img class="mx-auto" :src="itemListRow.janCodeImg" />
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
    #itemListRow tr td{
        page-break-inside: avoid;
    }
    @media print{
        #itemListRow thead{
            margin-top: 10px;
        }
    }
</style>
<script>
    const itemList = <?php echo json_encode($itemList, true); ?>;
    let itemListRows = <?php echo json_encode($itemListRows, true); ?>;
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
                let itemLists = [];
                let length = array.length > 11 ? 1 + Math.ceil((array.length - 11) / 13) : 1;
                for(let i = 0; i < length; i++){
                    if(i === 0)
                    {
                        itemLists.push(array.slice(0,11));
                    }else{
                        itemLists.push(array.slice(11+((i-1)*13), 11+(i*13)));
                    }
                }
                return itemLists;
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
                itemList: itemList,
                itemListRows: itemListRows,
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
                        text: this.itemList.itemListId, // Text to encode
                        scale: 3, // 3x scaling factor
                        height: 5, // Bar height, in millimeters
                        includetext: true, // Show human-readable text
                        textxalign: 'center', // Always good to set this
                    });
                    this.barcode_src = canvas.toDataURL();

                    bwipjs.toCanvas(canvas, {
                        bcid: 'qrcode',
                        text: 'https://' + location.host + _ROOT + '&path=/itemList/' + this.itemList.itemListId, // Text to encode
                    });

                    this.qr_src = canvas.toDataURL();
                    let labelBarcode = '';
                    let quantityNum = '';

                    this.itemListRows.forEach(async(x, xkey) => {
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
                        this.itemListRows[xkey].janCodeImg = canvas.toDataURL();
                    });
                } catch (e) {
                    console.error(e);
                    return;
                }
            },
        }
    }).mount('#top');
</script>