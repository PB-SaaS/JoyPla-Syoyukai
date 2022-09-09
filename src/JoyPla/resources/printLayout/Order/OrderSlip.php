<div id="top" v-cloak>
    <template v-if="orderItems.length > 0">
        <template v-for="(orderItemArray , key) in orderItems">
                <div class="paper A4 ">
                    <!-- here -->
                    <div class="p-6 relative">
                        <header class="text-center text-2xl">発注書</header>
                        <main class="mt-6">
                            <template v-if="key === 0">
                                <div class="flex">
                                    <div class="flex-auto w-1/2">
                                        <h4 class="text-xl font-bold">{{  order.distributor.distributorName}} 御中</h4>
                                        <div class="text-xxs divide-y divide-solid my-4 border-solid border-b">
                                            <div class="flex ">
                                                <div class="flex-auto w-1/3">発注日</div>
                                                <div class="flex-auto w-2/3">{{ order.orderDate }}</div>
                                            </div>
                                            <div class="flex ">
                                                <div class="flex-auto w-1/3">発注番号</div>
                                                <div class="flex-auto w-2/3">{{ order.orderId }}</div>
                                            </div>
                                        </div>
                                        <div class="text-xxs divide-y divide-solid my-4 border-solid border-b">
                                            <div class="flex ">
                                                <div class="flex-auto w-full">
                                                    {{ order.hospital.hospitalName }}<br>
                                                    〒{{ order.hospital.postalCode }}<br>
                                                    {{ order.hospital.pref }} {{ order.hospital.address }}
                                                </div>
                                            </div>
                                            <div class="flex ">
                                                <div class="flex-auto w-1/3">電話番号</div>
                                                <div class="flex-auto w-2/3">{{ order.hospital.phoneNumber }}</div>
                                            </div>
                                            <div class="flex ">
                                                <div class="flex-auto w-1/3">担当者</div>
                                                <div class="flex-auto w-2/3">{{ order.orderUserName }}</div>
                                            </div>
                                            <div class="flex ">
                                                <div class="flex-auto w-1/3">発注元部署</div>
                                                <div class="flex-auto w-2/3">{{ order.division.divisionName }}</div>
                                            </div>
                                            <div class="flex ">
                                                <div class="flex-auto w-1/3">入庫先部署</div>
                                                <div class="flex-auto w-2/3">{{ order.receivedDivisionName }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-auto w-1/2">
                                        <div>
                                            <img class="ml-auto h-6" src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png"/>
                                        </div>
                                        <div class="productsLabel mt-6">
                                            <img :src="barcode_src" alt="" class="w-2/3 ml-auto pb-4"/>
                                            <!-- <img :src="qr_src" alt="" class="w-1/4 ml-auto pb-4"/> -->
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex font-bold">
                                        <div class="flex-auto w-1/5">合計金額</div>
                                        <div class="flex-auto w-4/5">&yen; {{ numberFormat(order.totalAmount) }}</div>
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
                                            <th class="border border-slate-600 w-20 p-1">卸業者<br>管理コード</th>
                                            <th class="border border-slate-600 w-auto p-1">価格</th>
                                            <th class="border border-slate-600 w-auto p-1">入数</th>
                                            <th class="border border-slate-600 w-auto p-1">発注数</th>
                                            <th class="border border-slate-600 w-auto p-1">価格</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-xxs">
                                        <tr class="bg-white" v-for="( orderItem ) in orderItemArray">
                                            <td class="border border-slate-600 p-1">{{ orderItem.id  }}</td>
                                            <td class="border border-slate-600 p-1">
                                                <p class="truncate w-64">
                                                    {{ orderItem.item.itemName }} 
                                                    {{ orderItem.item.itemCode }} 
                                                    {{ orderItem.item.itemStandard }} 
                                                </p>
                                            </td>
                                            <td class="border border-slate-600 p-1"> 
                                                <img class="mx-auto" :src="orderItem.janCodeImg"/>
                                            </td>
                                            <td class="border border-slate-600 p-1">
                                                <p class="truncat">{{ orderItem.distributorManagerCode }}</p>
                                            </td>
                                            <td class="border border-slate-600 p-1">
                                                <p class="truncate">&yen; {{ numberFormat(orderItem.price) }}</p>
                                            </td>
                                            <td class="border border-slate-600 p-1">
                                                <p class="truncate">{{ numberFormat(orderItem.quantity.quantityNum) }} {{ orderItem.quantity.quantityUnit }}</p>
                                            </td>
                                            <td class="border border-slate-600 p-1">
                                                <p class="truncate">{{ numberFormat(orderItem.orderQuantity) }}{{ orderItem.quantity.itemUnit }}</p>
                                            </td>
                                            <td class="border border-slate-600 p-1">&yen; {{ numberFormat(orderItem.orderPrice) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <template v-if="key === ( orderItems.length - 1 )">
                                <div class="flex-col gap-4 mt-4 py-2 bg-gray-100 border-gray-400 border text-xxs">
                                    <div class="flex-auto">備考</div>
                                    <div class="flex-auto"><p class=" whitespace-pre-wrap">{{ order.orderComment }}</p></div>
                                </div>
                                <div class="flex-col gap-4 mt-4 py-2 bg-gray-100 border-gray-400 border text-xxs">
                                    <div class="flex-auto">卸業者備考</div>
                                    <div class="flex-auto"><p class=" whitespace-pre-wrap">{{ order.distributorComment }}</p></div>
                                </div>
                            </template>
                        </main>
                    </div>
                </div>
        </template>
    </template>
</div>

<script>
    const order = <?php echo json_encode($order,true) ?>;
    let orderItems = <?php echo json_encode($orderItems,true) ?>;
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
            order: order,
            orderItems: orderItems,
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
                        text:        this.order.orderId,    // Text to encode
                        scale:       3,               // 3x scaling factor
                        height:      5,              // Bar height, in millimeters
                        includetext: true,            // Show human-readable text
                        textxalign:  'center',        // Always good to set this
                });
                this.barcode_src = canvas.toDataURL();

                bwipjs.toCanvas(canvas, {
                        bcid:        'qrcode',
                        text:        'https://'+ location.host +_ROOT + '&path=/order/' + this.order.orderId ,    // Text to encode
                    }); 

                this.qr_src = canvas.toDataURL();
                
                this.orderItems.forEach((x , xkey) => {
                    x.forEach( async (y , ykey)=>{
                        let canvas = await document.createElement("canvas");
                         try {
                            bwipjs.toCanvas(canvas, {
                                bcid:        'ean13',       // Barcode type
                                text:        y.item.itemJANCode,    // Text to encode
                                scale:       3,               // 3x scaling factor
                                height:      5,              // Bar height, in millimeters
                                includetext: true,            // Show human-readable text
                                textxalign:  'center',        // Always good to set this
                            }); 
                        } catch (e) {
                            bwipjs.toCanvas(canvas, {
                                bcid:        'code128',       // Barcode type
                                text:        y.item.itemJANCode,    // Text to encode
                                scale:       3,               // 3x scaling factor
                                height:      5,              // Bar height, in millimeters
                                includetext: true,            // Show human-readable text
                                textxalign:  'center',        // Always good to set this
                            }); 
                        }
                        this.orderItems[xkey][ykey].janCodeImg = canvas.toDataURL();
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