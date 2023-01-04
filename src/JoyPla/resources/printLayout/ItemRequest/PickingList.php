<div id="top" v-cloak>
    <template v-if="dtotalizations.length > 0">
        <div class="paper A4 ">
            <!-- here -->
            <div class="p-6 relative">

                <header class="text-center text-2xl">ピッキングリスト</header>
                <main class="mt-6">
                    <div class="flex">
                        <div class="flex-auto w-full">
                            <div>
                                <img class="ml-auto h-6" src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex font-bold">
                            <div class="my-2 w-full text-right">
                                <p class="text-xs">発行日 {{ pickingDate }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <table class="border-collapse border border-slate-500 w-full">
                            <thead class="whitespace-nowrap font-medium text-gray-700 text-center border text-xs">
                                <tr class="bg-gray-100">
                                    <th scope="col" class="border border-slate-600 w-10 p-1">
                                        No
                                    </th>
                                    <th scope="col" class="border border-slate-600 w-36 p-1">
                                        請求先部署名
                                    </th>
                                    <th scope="col" class="border border-slate-600 w-36 p-1">
                                        商品情報
                                    </th>
                                    <th scope="col" class="border border-slate-600 w-20 p-1">
                                        棚名
                                    </th>
                                    <th scope="col" class="border border-slate-600 w-36 p-1">
                                        請求元部署名
                                    </th>
                                    <th scope="col" class="border border-slate-600 w-20 p-1">
                                        請求数
                                    </th>
                                    <th scope="col" class="border border-slate-600 w-48 p-1">
                                        バーコード
                                    </th>
                                    <th scope="col" class="border border-slate-600 w-20 p-1">
                                        在庫数
                                    </th>
                                    <th scope="col" class="border border-slate-600 w-20 p-1">
                                        必要数
                                    </th>
                                    <th scope="col" class="border border-slate-600 w-10 p-1">

                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-xxs text-gray-900 font-light">
                                <tr class="border-b bg-white" v-for="(totalization, idx) in dtotalizations" :key="totalization.key">
                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="whitespace-nowrap border border-slate-600 p-1">
                                        {{ totalization.no }}
                                    </td>
                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="border border-slate-600 p-1 break-all">
                                        {{ totalization.targetDivisionName }}
                                    </td>
                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="break-words border border-slate-600 p-1">
                                        <p class="text-md font-bold">{{ totalization.makerName }}</p>
                                        <p class="text-md font-bold">{{ totalization.itemName }}</p>
                                        <p class="text-md text-gray-500">{{ totalization.itemCode }}</p>
                                        <p class="text-md text-gray-500">{{ totalization.itemStandard }}</p>
                                        <p class="text-md text-gray-500">{{ totalization.itemJANCode }}</p>
                                    </td>
                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="break-words border border-slate-600 p-1">
                                        {{ totalization.rackName }}
                                    </td>
                                    <td class="break-all border border-slate-600 p-1">
                                        {{ totalization.sourceDivisionName }}
                                    </td>
                                    <td class="border border-slate-600 p-1">
                                        {{ totalization.requestQuantity }} {{ totalization.quantityUnit }}
                                    </td>
                                    <td class="border border-slate-600 py-1 px-3">
                                        <div class="productsLabel mt-6">
                                            <img class="mx-auto" :src="totalization.barcode" />
                                        </div>
                                    </td>
                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="border border-slate-600 p-1">
                                        {{ totalization.stockQuantity }} {{ totalization.quantityUnit }}
                                    </td>
                                    <td v-if="totalization.firstRow === true" :rowspan="totalization.rowspan" class="border border-slate-600 p-1">
                                        {{ totalization.totalRequestQuantity }} {{ totalization.quantityUnit }}
                                    </td>
                                    <td class="border border-slate-600 p-1 text-center">
                                        <input class="float-none form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 my-1 align-top bg-no-repeat bg-center bg-contain cursor-pointer" type="checkbox" value="">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </main>
            </div>
        </div>
    </template>
</div>

<script>
    //    const totalization = <?php echo json_encode($totalization, true) ?>;
    //    const count = <?php echo json_encode($count, true) ?>;
    var JoyPlaApp = Vue.createApp({
        components: {},
        setup() {
            const {
                ref,
                toRef,
                toRefs,
                reactive,
                onMounted
            } = Vue;

            const date = new Date();
            const yyyy = date.getFullYear();
            const mm = ("0" + (date.getMonth() + 1)).slice(-2);
            const dd = ("0" + date.getDate()).slice(-2);
            const pickingDate = yyyy + '年' + mm + '月' + dd + '日';

            const numberFormat = (value) => {
                if (!value) {
                    return 0;
                }
                return new Intl.NumberFormat('ja-JP').format(value);
            };

            //let totalizations = [];
            const list = sessionStorage.getItem('pickingList');
            const totalizations = JSON.parse(list);

            const setList = () => {
                //const list = sessionStorage.getItem('pickingList');
                //let totalizations = JSON.parse(list);
                //sessionStorage.removeItem('list');
            }

            //setList();

            /*
                        const totalPayoutQuantity = (idx) => {
                            let num = 0;
                            totalizations.value.forEach((v, id) => {
                                if (v.recordId === totalizations.value.recordId) {
                                    num += parseInt(v.payoutQuantity);
                                }
                            });
                            return num;
                        }
            */
            return {
                numberFormat,
                pickingDate,
                //setList,
                totalizations
            }

        },

        data() {
            return {
                barcode: "",
                dtotalizations: []
            };
        },
        async created() {
            //const list = sessionStorage.getItem('pickingList');
            //const totalizations = JSON.parse(list);
            //console.log(totalizations);
            if (!this.totalizations) {
                location.href = _ROOT;
            }
            await this.createBarCode();
        },
        methods: {
            async createBarCode() {
                //console.log(this.totalizations);

                let canvas = await document.createElement("canvas");
                try {

                    this.totalizations.forEach(async (x, xkey) => {
                        if (x.default === true) {
                            let canvas = await document.createElement("canvas");
                            try {
                                bwipjs.toCanvas(canvas, {
                                    bcid: 'code128', // Barcode type
                                    text: 'STK' + x.recordId + '+' + x.sourceDivisionId, // Text to encode
                                    scale: 3, // 3x scaling factor
                                    height: 5, // Bar height, in millimeters
                                    includetext: true, // Show human-readable text
                                    textxalign: 'center', // Always good to set this
                                });
                            } catch (e) {
                                bwipjs.toCanvas(canvas, {
                                    bcid: 'code39', // Barcode type
                                    text: 'STK' + x.recordId + '+' + x.sourceDivisionId, // Text to encode
                                    scale: 3, // 3x scaling factor
                                    height: 5, // Bar height, in millimeters
                                    includetext: true, // Show human-readable text
                                    textxalign: 'center', // Always good to set this
                                });
                            }
                            this.totalizations[xkey].barcode = canvas.toDataURL();
                            this.dtotalizations.push(this.totalizations[xkey]);

                        }
                    });
                } catch (e) {
                    console.error(e);
                    return;
                }
            },
        }

    }).mount('#top');
</script>