<?php
if($validate){
    $val = $validate->getResults();
    $is_error = $validate->isError();
}
$category = [1 => "医療材料", 2 => "薬剤", 3 => "試薬", 4 => "日用品", 99 => "その他",];
?>
<div id="top" v-cloak>
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto py-1">
            <div class="index container mx-auto mb-96">
                <h1 class="text-2xl mb-2">商品情報登録 - 確認</h1>
                <hr>
                <div>

                    <p class="header_text">変更内容をご確認の上、変更ボタンをクリックしてください。<br>
                        <p class="uk-alert-danger uk-alert">内容を修正する場合は、戻るボタンをクリックしてください。</p>
                    </p>

                    <div>
                        <div class="smp_tmpl">
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    商品名
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['itemName']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    分類
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($category[$val['category']['value']]) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    小分類
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['smallCategory']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    製品コード
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['itemCode']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    規格
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['itemStandard']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    JANコード 
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['itemJANCode']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    メーカー名
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['makerName']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    カタログNo
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['catalogNo']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    シリアルNo
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['serialNo']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    ロット管理フラグ
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo ($val['lotManagement']['value']) ? "はい" : "" ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    償還価格フラグ
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo ($val['officialFlag']['value']) ? "はい" : "" ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    償還価格
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['officialprice']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    旧償還価格
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['officialpriceOld']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    入数 
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['quantity']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    入数単位 
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['quantityUnit']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    個数単位 
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['itemUnit']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    定価
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['minPrice']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    卸業者
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($distributor[0]["distributorName"]) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    卸業者管理コード
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['distributorMCode']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    購買価格
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['price']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    単価
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['unitPrice']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    保険請求分類（医科）
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo nl2br(html($val['medicineCategory']['value'])) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    保険請求分類（在宅）
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo nl2br(html($val['homeCategory']['value'])) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    測定機器名
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo html($val['measuringInst']['value']) ?></span>
                                </div>
                            </div>
                            <div class="py-2 border-b border-gray-400 border-solid">
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    特記事項
                                </div>
                                <div class="flex-auto py-1">
                                    <span class="text-left text-xl"><?php echo nl2br(html($val['notice']['value'])) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-center py-1">
                            <form method="post" x-bind:action="_ROOT">
                                <input type="hidden" name="_method" value="post">
                                <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                                <input type="hidden" name="path" value="/product/ItemAndPriceAndInHospitalRegist/thanks">
                                <input class="bg-gray-400 hover:bg-gray-200 text-white font-bold py-2 px-4 rounded" type="submit" name="formBack" value="戻る">
                                <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit" value="登録">
                            </form>
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
        setup() {
            const {ref, onCreated, onMounted} = Vue;
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

            onMounted(() => {
                sleepComplate()
            });

            const { useForm } = VeeValidate;
            const { handleSubmit , control, meta , validate , values , isSubmitting  } = useForm({
                initialValues: {
                },
                validateOnMount : false
            });
/* 
            const onSubmit = async (event) => {
                const { valid, errors } = await validate();
                if(!valid){
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: '入力エラーがございます。ご確認ください',
                    })
                    event.preventDefault();
                }else{
                    document.getElementsByName("regForm")[0].submit();
                }
            };
 */
            const breadcrumbs = [
            {
                text: '商品メニュー',
                disabled: false,
                href: _ROOT + '&path=/product',
            },
            {
                text: '商品・金額・院内商品情報登録',
                disabled: true, 
            }
            ];

/* 
            const register = handleSubmit(async (values) => {
                try {
                    let params = new URLSearchParams();
                    params.append("path", "/api/ItemAndPriceAndInHospitalItem/register");
                    params.append("_method", 'post');
                    params.append("_csrf", _CSRF);

                    const res = await axios.post(_APIURL,params);
                    
                    if(res.data.code != 200) {
                    throw new Error(res.data.message)
                    }
                    
                    let frm = document.createElement("form");
                    frm.action = _ROOT;
                    frm.method = "post";
                    document.body.append(frm);
                    frm.addEventListener("data", (e) => {
                        let d = e.data;
                        d.set("_csrf", _CSRF);
                        d.set("_method", "post");
                        d.set("path", "/product/ItemAndPriceAndInHospitalRegist/thanks");
                    })
                    frm.submit();
                    
                    return true ;
                } catch (error) {
                    console.log(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'システムエラー',
                        text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
                    });
                }
                
            });
 */

/*
            async () =>{
                const { valid, errors } = await validate();
                if(!valid){
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: '入力エラーがございます。ご確認ください',
                    });
                }

                return handleSubmit((values, actions) => {
                    // Send data to API
                    alert(JSON.stringify(values, null, 2));
                });
            };*/

            return {
                loading, 
                start, 
                complete , 
                breadcrumbs, 
            }
        },
        components: {
            'v-checkbox': vCheckbox,
            'v-loading': vLoading,
            'v-text': vText,
            'v-input' : vInput,
            'v-textarea' : vTextarea,
            'v-select': vSelect,
            'v-checkbox': vCheckbox,
            'v-button-default' : vButtonDefault,
            'v-button-primary' : vButtonPrimary,
            'v-breadcrumbs': vBreadcrumbs,
            'header-navi': headerNavi
        }
    })
    .mount('#top');
</script>