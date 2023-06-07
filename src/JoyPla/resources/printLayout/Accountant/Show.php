<div id="top" v-cloak>
    <template v-if="accountantItems.length > 0">
        <template v-for="(accountantItemArray , key) in accountantItems">
            <div class="paper A4 ">
                <!-- here -->
                <div class="p-6 relative">
                    <header class="text-center text-2xl">会計伝票</header>
                    <main class="mt-6">
                        <template v-if="key === 0">
                            <div class="flex">
                                <div class="flex-auto w-1/2">
                                    <div class="text-xxs divide-y divide-solid my-4 border-solid border-b">
                                        <div class="flex ">
                                            <div class="flex-auto w-1/3">計上日</div>
                                            <div class="flex-auto w-2/3">{{ accountant.accountantDate }}</div>
                                        </div>
                                        <div class="flex ">
                                            <div class="flex-auto w-1/3">会計番号</div>
                                            <div class="flex-auto w-2/3">{{ accountant.accountantId }}</div>
                                        </div>
                                        <div class="flex ">
                                            <div class="flex-auto w-1/3">部署</div>
                                            <div class="flex-auto w-2/3">{{ accountant._division?.divisionName }}</div>
                                        </div>
                                        <div class="flex ">
                                            <div class="flex-auto w-1/3">卸業者</div>
                                            <div class="flex-auto w-2/3">{{ accountant._distributor?.distributorName }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-auto w-1/2">
                                    <div>
                                        <img class="ml-auto h-6" src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
                                    </div>
                                    <div class="productsLabel mt-6">
                                        <img :src="barcode_src" alt="" class="w-2/3 ml-auto pb-4" />
                                        <!-- <img :src="qr_src" alt="" class="w-1/4 ml-auto pb-4"/> -->
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="flex font-bold">
                                    <div class="flex-auto w-1/5">合計金額</div>
                                    <div class="flex-auto w-4/5">&yen; {{ numberFormat(accountant.totalAmount) }}</div>
                                </div>
                            </div>
                        </template>
                        <div class="mt-4">
                            <table class="border-collapse border border-slate-500 w-full">
                                <thead class="text-xs">
                                    <tr class="bg-gray-100">
                                        <th class="border border-slate-600 w-10 p-1"></th>
                                        <th class="border border-slate-600 w-48 p-1">商品情報</th>
                                        <th class="border border-slate-600 w-36 p-1">JAN</th>
                                        <th class="border border-slate-600 w-20 p-1">登録元</th>
                                        <th class="border border-slate-600 w-auto p-1">個数</th>
                                        <th class="border border-slate-600 w-auto p-1">価格</th>
                                        <th class="border border-slate-600 w-auto p-1">税率</th>
                                        <th class="border border-slate-600 w-auto p-1">小計</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xxs">
                                    <tr class="bg-white" v-for="( accountantItem ) in accountantItemArray">
                                        <td class="border border-slate-600 p-1">{{ accountantItem.index + 1  }}</td>
                                        <td class="border border-slate-600 p-1">
                                            <p class="w-64">
                                                {{ accountantItem.makerName }}<br>
                                                {{ accountantItem.itemName }}<br>
                                                {{ accountantItem.itemCode }}<br>
                                                {{ accountantItem.itemStandard }}
                                            </p>
                                        </td>
                                        <td class="border border-slate-600 py-1 px-3">
                                            <img class="mx-auto" :src="accountantItem.janCodeImg" />
                                        </td>
                                        <td class="border border-slate-600 p-1 text-center">
                                            <p class="truncat">{{ accountantItem.action }}</p>
                                        </td>
                                        <td class="border border-slate-600 p-1 text-center">
                                            <p class="truncate">{{ numberFormat(accountantItem.count) }} {{ accountantItem.unit }}</p>
                                        </td>
                                        <td class="border border-slate-600 p-1 text-center">
                                            <p class="truncate">&yen;{{ numberFormat(accountantItem.price) }}</p>
                                        </td>
                                        <td class="border border-slate-600 p-1 text-center">
                                            <p class="truncate">{{ numberFormat(accountantItem.taxrate) }}%</p>
                                        </td>
                                        <td class="border border-slate-600 p-1 text-center">&yen;{{ numberFormat(itemSubtotal(accountantItem)) }}</td>
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
    const accountant = <?php echo json_encode($accountant, true); ?>;
    let accountantItems = <?php echo json_encode($accountantItems, true); ?>;
    var JoyPlaApp = Vue.createApp({
        components: {},
        setup() {
            const numberFormat = (value) => {
                if (!value) {
                    return 0;
                }
                return new Intl.NumberFormat('ja-JP').format(value);
            };
            
            const itemSubtotal = (item) => {
                if(!item){
                    console.log(item);
                    return 0;
                }
                item = {
                    price : item.price ?? 0,
                    count : item.count ?? 0,
                    taxrate : item.taxrate ?? 0,
                }

                // 価格、数量、税率を整数に変換
                const priceInt = Math.round(item.price * 100);
                const countInt = Math.round(item.count * 100);
                const taxRateInt = Math.round(item.taxrate);

                // 小計と税額を計算
                const itemTotalInt = priceInt * countInt / 100;
                const taxAmountInt = itemTotalInt * taxRateInt / 100;

                // 小計と税額を加算して、結果を小数に戻して返す
                return (itemTotalInt + taxAmountInt) / 100 ?? 0;
            }
            return {
                itemSubtotal,
                numberFormat,
            }
        },
        data() {
            return {
                barcode_src: "",
                qr_src: "",
                accountant: accountant,
                accountantItems: accountantItems,
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
                        text: accountant.accountantId, // Text to encode
                        scale: 3, // 3x scaling factor
                        height: 5, // Bar height, in millimeters
                        includetext: true, // Show human-readable text
                        textxalign: 'center', // Always good to set this
                    });
                    this.barcode_src = canvas.toDataURL();

                    bwipjs.toCanvas(canvas, {
                        bcid: 'qrcode',
                        text: 'https://' + location.host + _ROOT + '&path=/accountant/' + this.accountant.accountantId, // Text to encode
                    });

                    this.qr_src = canvas.toDataURL();

                    this.accountantItems.forEach((x, xkey) => {
                        x.forEach(async (y, ykey) => {
                            let canvas = await document.createElement("canvas");
                            try {
                                bwipjs.toCanvas(canvas, {
                                    bcid: 'ean13', // Barcode type
                                    text: y.itemJANCode, // Text to encode
                                    scale: 3, // 3x scaling factor
                                    height: 5, // Bar height, in millimeters
                                    includetext: true, // Show human-readable text
                                    textxalign: 'center', // Always good to set this
                                });
                            } catch (e) {
                                bwipjs.toCanvas(canvas, {
                                    bcid: 'code128', // Barcode type
                                    text: y.itemJANCode, // Text to encode
                                    scale: 3, // 3x scaling factor
                                    height: 5, // Bar height, in millimeters
                                    includetext: true, // Show human-readable text
                                    textxalign: 'center', // Always good to set this
                                });
                            }
                            this.accountantItems[xkey][ykey].janCodeImg = canvas.toDataURL();
                        });
                    });
                } catch (e) {
                    console.error(e);
                    return;
                }
            },
        }
    }).mount('#top');
</script>