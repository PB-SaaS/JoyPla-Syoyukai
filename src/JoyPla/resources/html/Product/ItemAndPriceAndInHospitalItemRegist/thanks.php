<div id="top" v-cloak>
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto py-1">
            <div class="index container mx-auto mb-96">
                <h1 class="text-2xl mb-2">商品情報登録 - 完了</h1>

                <div class="smp_tmpl">
                    <div class="sub_text">
                        商品情報の登録が完了しました。<br>
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