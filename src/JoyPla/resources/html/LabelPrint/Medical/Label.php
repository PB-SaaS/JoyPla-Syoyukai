<style>
html {
    color: #000;
}
#createLabel{
    font-size:0;
    padding: 5mm;
}
.a4area{
    break-after: always;
}
.printarea{
    vertical-align: top;
}
.printarea svg{
    display: inline-block !important;
}
.printarea{
    font-size:12px;
    border: 1px solid gray;
    padding: 5px;
}
.font-size-16{
    font-size:16px;
}

body {
    -webkit-print-color-adjust: exact;
    color-adjust: exact;
}

@page {
	margin: 5mm;
}
@media print{
    #createLabel{
        width: auto !important;
        border : 0px dotted #cccccc !important;
	    padding: 0;
    }
}
</style>
<div id="top" v-cloak>
    <div class="no_print">
        <header-navi></header-navi>
    </div>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto no_print">
                <h1 class="text-2xl my-2">医事ラベル発行</h1>
                <hr>
                <div class="my-4">
                    <table class="table-auto w-full text-sm">
                        <thead>
                            <tr>
                                <th class="border border-slate-100 font-medium p-4 pr-8 text-left">商品情報</th>
                                <th class="border border-slate-100 font-medium p-4 pr-8 text-left">ロット番号</th>
                                <th class="border border-slate-100 font-medium p-4 pr-8 text-left">使用期限</th>
                                <th class="border border-slate-100 font-medium p-4 pr-8 text-left w-0">枚数</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template  v-for="(inHospitalItem , idx) in values.items">
                                <template v-for="(order , pIdx) in inHospitalItem.order">
                                    <template v-for="(print , printIdx) in order.print">
                                        <tr>
                                            <td v-if="pIdx === 0 && printIdx === 0" class="border border-slate-100 p-4 pr-8 text-slate-500" :rowspan="calcRow(inHospitalItem.order)">
                                                <div>
                                                    <p>{{ inHospitalItem.itemName }}</p>
                                                    <p>{{ inHospitalItem.itemCode }}</p>
                                                    <p>{{ inHospitalItem.itemStandard }}</p>
                                                    <p>{{ inHospitalItem.itemJANCode }}</p>
                                                </div>
                                            </td>
                                            <!-- @TODO ロット番号と使用期限は任意項目のため空文字のときの表示を確認する。 -->
                                            <td v-if="printIdx === 0" class="border border-slate-100 p-4 pr-8 text-slate-500" :rowspan="order.print.length">{{ order.lotNumber }}</td>
                                            <td v-if="printIdx === 0" class="border border-slate-100 p-4 pr-8 text-slate-500" :rowspan="order.print.length">{{ order.lotDate }}</td>
                                            <td class="border border-slate-100 p-4 pr-8 text-slate-500">
                                                <v-input-number 
                                                :name="`items[${idx}].order[${pIdx}].print[${printIdx}].print`"
                                                :rules="{ between: [0 , 9999] }" 
                                                label="枚数" 
                                                :min="0"
                                                unit="枚" 
                                                :step="1" 
                                                title="枚数" 
                                                class=" w-[240px]"></v-input-number>
                                            </td>

                                        </tr>
                                    </template>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="flex gap-3">
                    <div class="md:w-1/4">
                        <v-select
                            name="setting.printType"
                            :options="printTypes"
                            label="印刷方法"
                            title="印刷方法"
                        ></v-select>
                    </div>
                    <div class="md:w-1/4">
                        <v-input prefix="mm" type="number" name="setting.pagePadding" title="印刷余白" label="印刷余白"></v-input>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="md:w-1/4">
                        <v-input prefix="mm" type="number" name="setting.labelwidth" title="ラベルの幅" label="ラベルの幅"></v-input>
                    </div>
                    <div class="md:w-1/4">
                        <v-input prefix="mm" type="number" name="setting.labelheight" title="ラベルの最小高さ" label="ラベルの最小高さ"></v-input>
                    </div>
                </div>
                <div class="text-center m-12">
                    <v-button-default @click.native="labelReload" type="button">反映</v-button-default>
                </div>
            </div>
            <div id="printWrapper" class="">
                <div id="createLabel" class="mx-auto ">
                    <?php
                        echo ($labelHtml);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const PHPData = <?php echo json_encode($inHospitalItems, true); ?>;
    
    // Debug用
    console.log(PHPData);

    var JoyPlaApp = Vue.createApp({
        components: {
            'v-select': vSelect,
            'v-button-default': vButtonDefault,
            'header-navi': headerNavi,
            'v-input-number': vInputNumber,
            'v-input': vInput,
        },
        setup() {
            const {ref , onCreated , onMounted} = Vue;
            const {useFieldArray, useForm} = VeeValidate;
            let label_setting = JSON.parse(localStorage.getItem("joypla_Medical_LabelCreate")) ?? [];
            const { handleSubmit , control, meta , validate , values , isSubmitting } = useForm({
                initialValues: {
                    setting: {
                        pagePadding: (label_setting['pagePadding'])? label_setting['pagePadding'] : 5,
                        labelwidth:  (label_setting['labelwidth'])? label_setting['labelwidth'] :85,
                        labelheight: (label_setting['labelheight'])? label_setting['labelheight'] : 50,
                        printType:  (label_setting['printType'])? label_setting['printType'] :1,
                    },
                    items : PHPData,
                },
                validateOnMount : false
            });

            const {
                remove,
                insert,
                fields,
                update,
                replace
            } = useFieldArray('items', control);
            

            const printTypes = [
                { label: "ラベルプリンター", value: "1" },
                { label: "A4印刷", value: "2" },
            ];
            
            onMounted( () => {
                const style = document.createElement('style');
                style.innerHTML = `
                #printWrapper {
                    background-color: #ccc;
                }
                #createLabel{
                    text-align: left;
                    border : 1px dotted #cccccc;
                    background-color: #fff;
                    padding:`+values.setting.pagePadding+`mm;
                }
                @media print{
                    #createLabel{
                        width: auto !important;
                        border : 0px dotted #cccccc !important;
                        padding: 0mm;
                    }
                }
                .printarea {
                    width:`+values.setting.labelwidth+`mm;
                    min-height:`+values.setting.labelheight+`mm;
                }
                @page {
                    margin: `+values.setting.pagePadding+`mm;
                }
                `;

                if(values.setting.printType == '2'){
                    style.innerHTML = style.innerHTML + `
                    #createLabel{
                        width: 210mm;
                    }
                    @media print{
                        #createLabel{
                            width: auto !important;
                            border : 0px dotted #cccccc !important;
                        }
                    }
                    .printarea {
                        width:`+values.setting.labelwidth+`mm;
                        min-height:`+values.setting.labelheight+`mm;
                        display: inline-block;
                        letter-spacing: normal;
                        page-break-after: auto !important;
                    }
                    `;
                } else {
                    style.innerHTML = style.innerHTML + `
                    #createLabel{
                        width: `+ ( parseInt(values.setting.labelwidth) + ( parseInt(values.setting.pagePadding) * 2 ) ) +`mm;
                    }
                    @media print{
                        #createLabel{
                            width: auto !important;
                            border : 0px dotted #cccccc !important;
                        }
                    }
                    .printarea {
                        width:`+values.setting.labelwidth+`mm;
                        min-height:`+values.setting.labelheight+`mm;
                        display: block;
                        letter-spacing: normal;
                        page-break-after: always;
                    }
                    `;
                }
                document.body.appendChild(style);
                
            });

            const createLabelModel = (values) => {
                let model = [];
                values.items.forEach(item => {
                    item.order.forEach(order => {
                        let obj = {};
                        obj.orderItemId = order.inHospitalItemId
                        obj.print = order.print
                        model.push(obj);
                    })
                });

                return model;
            }
            
            const labelReload = handleSubmit(async (values) => {
                const labelModel = createLabelModel(values);
                
                let params = {};

                params.path = "/medicalLabel";
                params._method = 'get';
                params._csrf = _CSRF;
                params.request = JSON.stringify(encodeURIToObject(labelModel));
                // 新しいフォーム要素を作成
                const form = document.createElement("form");
                
                // フォームの設定
                form.method = 'post';

                // @TODO このパスはAPI次第で変える必要がある。
                form.action = _ROOT+"&path=/label/medicalOrder/03654da0e6eff67";
                
                // パラメータをループしてフォームに追加
                for (let key in params) {
                    if (params.hasOwnProperty(key)) {
                        let value = params[key];
                    
                        // パラメータの入力フィールドを生成
                        let input = document.createElement("input");
                        input.type = "hidden";
                        input.name = key;
                        input.value = value;
                        
                        // 入力フィールドをフォームに追加
                        form.appendChild(input);
                    }
                }
                let setting = {
                    "pagePadding": values.setting.pagePadding,
                    "labelwidth":values.setting.labelwidth,
                    "labelheight":values.setting.labelheight,
                    "printType":values.setting.printType,
                };

                localStorage.setItem("joypla_Medical_LabelCreate", JSON.stringify(setting));
                
                // フォームをドキュメントに追加して送信
                document.body.appendChild(form);
                form.submit();
            });

            const calcRow = (orders) => {
                let printCount = 0;
                orders.forEach(function(order) {
                    printCount += order.print.length;
                });
                return printCount;
            }

            return {
                calcRow,
                values,
                printTypes,
                labelReload,
            }
        },
        watch: {}
    }).mount('#top');
</script>