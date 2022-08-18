<div id="top" v-cloak="v-cloak">
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto">
                <h1 class="text-2xl mb-2">棚卸情報インポート</h1>
                <hr>
                <div class="mb-2 lg:w-1/3">
                    <v-select-division 
                        name="divisionId" 
                        label="棚卸部署" 
                        :rules="{ required : true }"
                        title="棚卸部署指定" 
                        :is-only-my-division="<?php var_export(gate('register_of_stocktaking_slips')->isOnlyMyDivision()) ?>"
                    />
                </div>
                <div class="flex mb-2 lg:w-1/3">
                    <div class="mb-3 w-full">
                        <label for="formFile" class="form-label inline-block mb-2 text-gray-700">棚卸情報ファイル選択</label>
                        <input
                            @change="loadCsvFile"
                            accept=".csv,.tsv,.txt"
                            class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                            type="file"
                            id="formFile">
                    </div>
                </div>
                <div class="my-4 grid grid-cols-3 gap-4 lg:w-1/3 items-center">
                    <v-button-primary type="button" @click.native="onSubmit">インポート実行</v-button-primary>
                </div>
                <p class="text-red-500 font-bold" v-for="msg in messages">{{ msg }}</p>
                <div class="flex flex-col">
                    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-4 inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="overflow-hidden">
                                <table class="min-w-full text-left">
                                    <thead class="border-b bg-gray-800">
                                        <tr>
                                            <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                                                #
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                                                院内商品ID
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                                                ロット番号
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                                                消費期限
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-white px-2 py-4 ">
                                                棚卸数量
                                            </th>
                                        </tr>
                                    </thead class="border-b">
                                    <tbody>
                                        <tr class="bg-white border-b" v-for="(item, index) in fields" :key="item.key">
                                            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium  ">{{ (index+1) }}</td>
                                            <td class="text-sm  font-light px-2 py-4">
                                                <v-input 
                                                :name="`importList[${index}].data[0]`" 
                                                label="院内商品ID" 
                                                :rules="{ required : true , one_of : getInHospitalItemIds() }" 
                                                type="text" 
                                                title="院内商品ID"></v-input>
                                            </td>
                                            <td class="text-sm  font-light px-2 py-4">
                                                <v-input 
                                                :name="`importList[${index}].data[1]`" 
                                                label="ロット番号" 
                                                :rules="{ required : isRequired(item.value.data[0]) ,lotnumber: true , twoFieldRequired : [ '消費期限', `@importList[${index}].data[2]`]  }" 
                                                type="text" 
                                                title="ロット番号"></v-input>
                                            </td>
                                            <td class="text-sm  font-light px-2 py-4">
                                                <v-input 
                                                :name="`importList[${index}].data[2]`" 
                                                label="消費期限" 
                                                :rules="{ required : isRequired(item.value.data[0]) ,lotdate: true , twoFieldRequired : [ '消費期限', `@importList[${index}].data[1]`]  }" 
                                                type="text" 
                                                title="消費期限"></v-input>
                                            </td>
                                            <td class="text-sm  font-light px-2 py-4">
                                                <v-input-number 
                                                :rules="{ between: [0 , 99999999] }" 
                                                :name="`importList[${index}].data[3]`"
                                                label="棚卸数量" 
                                                :min="0" 
                                                :step="1" 
                                                title="棚卸数量" 
                                                ></v-input-number>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var JoyPlaApp = Vue
        .createApp({
            components: {
                'v-loading': vLoading,
                'v-breadcrumbs': vBreadcrumbs,
                'v-button-default': vButtonDefault,
                'v-button-primary': vButtonPrimary,
                'v-button-danger': vButtonDanger,
                'header-navi': headerNavi,
                'v-input': vInput,
                'v-input-number': vInputNumber,
                'v-select-division' : vSelectDivision
            },
            setup() {
                const {ref, onMounted} = Vue;
                const {useFieldArray, useForm} = VeeValidate;

                const { handleSubmit , control, meta , validate , values , isSubmitting   } = useForm({
                    initialValues: {
                        divisionId: "",
                        importList : [],
                    },
                    validateOnMount : false
                });

                const { remove, push, fields , update , replace } = useFieldArray('importList' , control);

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

                const messages = ref([]);
                const filename = ref('');
                const headers = ['院内商品ID', 'ロット番号', '使用期限', '棚卸数量'];
                const loadCsvFile = (e) => {
                    messages.value = [];
                    let file = e
                        .target
                        .files[0];

                    filename.value = file.name;
                    let extension = file
                        .name
                        .split('.')
                        .pop();

                    if (!file.type.match("text/csv") && !file.type.match("application/vnd.ms-excel") && (extension != 'tsv' && extension != 'txt')) {
                        messages
                            .value
                            .push("CSV/TSV ファイルを選択してください");
                        return;
                    }

                    let extension_type = extension;
                    if ((file.type.match("text/csv") || file.type.match("application/vnd.ms-excel"))) {
                        extension_type = 'csv';
                    }

                    if ((extension == 'tsv' || extension == 'txt')) {
                        extension_type = 'tsv';
                    }

                    let reader = new FileReader();
                    reader.readAsArrayBuffer(file);
                    //reader.readAsText(file);
                    reader.onload = function (e) {
                        start();
                        // 8ビット符号なし整数値配列と見なす
                        var array = new Uint8Array(e.target.result);

                        // 文字コードを取得
                        switch (Encoding.detect(array)) {
                            case 'UTF16':
                                // 16ビット符号なし整数値配列と見なす
                                array = new Uint16Array(e.target.result);
                                break;
                            case 'UTF32':
                                // 32ビット符号なし整数値配列と見なす
                                array = new Uint32Array(e.target.result);
                                break;
                        }

                        // Unicodeの数値配列に変換
                        var unicodeArray = Encoding.convert(array, 'UNICODE');
                        // Unicodeの数値配列を文字列に変換
                        var text = Encoding.codeToString(unicodeArray);

                        let lines = text.split(/\r\n|\n/);
                        let linesArr = [];

                        for (let i = 0; i < lines.length; i++) {
                            if (lines[i] === "") {
                                lines.splice(i, 1);
                            }
                        }

                        if (5000 < lines.length) {
                            messages.value.push("読み込んだファイルは5000件を超えています");
                            return false;
                        }

                        for (let i = 0; i < lines.length; i++) {
                            if (lines[i] === "") {
                                continue;
                            }
                            linesArr[i] = {
                                'index': '',
                                'data': []
                            };
                            if (extension_type == 'csv') {
                                console.log(lines[i].split(/,(?=(?:[^"]*"[^"]*")*[^"]*$)/));
                                linesArr[i]['data'] = lines[i].split(/,(?=(?:[^"]*"[^"]*")*[^"]*$)/);
                                //linesArr[i] = lines[i].split(",");
                            }
                            if (extension_type == 'tsv') {
                                linesArr[i]['data'] = lines[i].split("\t");
                                //linesArr[i] = lines[i].split("\t");
                            }
                            if (headers.length !== linesArr[i]['data'].length) {
                                messages.value.push("カラム数がヘッダーと一致しません");
                                return false;
                            }
                            for (let j = 0; j < linesArr[i]['data'].length; j++) {
                                if (linesArr[i]['data'][j].match(/^"/)) {
                                    linesArr[i]['data'][j] = linesArr[i]['data'][j].replace(/^"/, "");
                                }
                                if (linesArr[i]['data'][j].match(/"$/)) {
                                    linesArr[i]['data'][j] = linesArr[i]['data'][j].replace(/"$/, "");
                                }
                            }
                            let percent = 100;
                            if ((lines.length - 1) > 0) {
                                percent = (1 / (lines.length - 1)) * 100;
                            }
                        }
                        complete();
                        replace(linesArr);
                    };
                };


                const inHospitalItems = ref([]);
                const getInHospitalItems = () => {
                    inHospitalItems.value = [];
                    let params = new URLSearchParams();
                    params.append("path", "/api/stocktaking/inHospitalItem");
                    params.append("_csrf", _CSRF);

                    start();

                    axios.post(_APIURL,params)
                        .then( (response) => {
                            inHospitalItems.value = response.data.data;
                            console.log(inHospitalItems.value);
                        }) 
                        .then(() => {
                            complete();
                        });
                };

                const breadcrumbs = [
                    {
                        text: '棚卸メニュー',
                        disabled: false,
                        href: _ROOT + '&path=/stocktaking'
                    }, {
                        text: '棚卸情報インポート',
                        disabled: true
                    }
                ];

                const numberFormat = (value) => {
                    if (! value ) { return 0; }
                    return new Intl.NumberFormat('ja-JP').format(value);
                };

                const findInHospitalItem = (inHospitalItemId) => {
                    return inHospitalItems.value.find(
                        (value) => inHospitalItemId == value.inHospitalItemId
                    );
                };

                const getInHospitalItemIds = () => {
                    let ids = [];
                    inHospitalItems.value.forEach(
                        (value) => ids.push(value.inHospitalItemId)
                    );
                    return ids;
                };

                const isRequired = (inHospitalItemId) => {
                    let inHospitalItem = findInHospitalItem(inHospitalItemId);
                    
                    return (inHospitalItem && inHospitalItem.lotManagement === '1');
                }


                onMounted(() => {
                    sleepComplate();
                    getInHospitalItems();
                });

                const createImportModel = ( values ) => {
                    let items = values.importList;
                    let importModels = [];
                    items.forEach(function(item, idx){
                        if(item.data[3] != 0)
                        {
                            importModels.push({
                                'recordId': item.data[0],
                                'lotNumber': item.data[1],
                                'lotDate': item.data[2],
                                'countNum': item.data[3],
                            });
                        }
                    });
                    return importModels;
                };

                const temporaryImport = handleSubmit(async (values) => {
                    try {
                        const importModels = createImportModel(values);
                        if( importModels.length === 0)
                        {
                            Swal.fire({
                                icon: 'error',
                                title: '登録する商品がありませんでした。',
                                text: '内容を確認の上、再送信をしてください。',
                            })
                            return false;
                        }
                        
                        let params = new URLSearchParams();
                        //params.append("path", "/api/received/register");
                        params.append("Action", "inventoryRegistApi");
                        params.append("_csrf", _CSRF);
                        params.append("divisionId", values.divisionId);
                        params.append("inventory", JSON.stringify(encodeURIToObject(importModels)));

                        start();
                        const res = await axios.post('%url/rel:mpgt:Inventory%',params);
                        complete();
                        if(res.data.code == 1) {
                            Swal.fire({
                                icon: 'error',
                                title: 'システムエラー',
                                text: res.data.message,
                            });
                            return false;
                        }

                        if(res.data.code != 0) {
                            throw new Error(res.data.message)
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'インポートが完了しました。',
                        }).then((result) => {
                            replace([]);
                            document.getElementById('formFile').value = "";
                        });
                        return true ;
                    } catch (error) {
                        Swal.fire({
                        icon: 'error',
                        title: 'システムエラー',
                        text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
                        });
                    }
                    
                });

                const onSubmit = async () =>{
                    const { valid, errors } = await validate();
                    if(!valid){
                        Swal.fire({
                            icon: 'error',
                            title: '入力エラー',
                            text: '入力エラーがございます。ご確認ください',
                        })
                    } 
                    else 
                    {
                        Swal.fire({
                            title: 'インポートを行います。',
                            text: "よろしいですか？",
                            icon: 'info',
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if(result.isConfirmed){
                                temporaryImport();
                            }
                        });
                    }
                };


                return {
                    onSubmit,
                    getInHospitalItemIds,
                    isRequired,
                    numberFormat,
                    messages,
                    fields,
                    loadCsvFile,
                    loading,
                    start,
                    complete,
                    breadcrumbs,
                    values,
                }
            }, 
            watch: {
            }
        })
        .mount('#top');
</script>