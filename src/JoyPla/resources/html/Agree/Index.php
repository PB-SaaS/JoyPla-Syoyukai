<div id="top" v-cloak="v-cloak">
    <v-loading :show="loading"></v-loading>
    <header-navi :sidemenu="false"></header-navi>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto py-4">
                <h1 class="text-2xl mb-2">利用規約同意</h1>
                <form :action="_ROOT" method="post" @submit="onSubmit">
                    <div class="p-4 text-base bg-gray-100 border border-gray-400 my-2">
                        <div class="p-4 text-base">
                            <div class="md:h-[70vh] h-72">
                                <embed src="https://i02.smp.ne.jp/u/joypla/new/pdf/joypla-spdkiyaku20.pdf" type="application/pdf" width="100%" height="100%">
                            </div> 
                        </div>
                        <v-text title="" class="w-full gap-6 text-center">
                            <v-checkbox value="1" name="agree" :rules="{ required: true }" label="利用規約同意" title="同意します"></v-checkbox>
                        </v-text>
                    </div>
                    <input type="hidden" :value="_CSRF" name="_token">
                    <input type="hidden" value="post" name="_method">
                    <input type="hidden" value="/agree/send" name="path">
                    <div class="flex justify-center gap-6">
                        <v-button-primary type="submit">送信</v-button-primary>
                    </div>
                </form>
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
                    'agree' : "",
                },
                validateOnMount : false
            });

            const onSubmit = async (event) => {
                const { valid, errors } = await validate();
                if(!valid){
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: '入力エラーがございます。ご確認ください',
                    })
                    event.preventDefault();
                }
            };
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
                onSubmit ,
            }
        },
        components: {
            'v-checkbox': vCheckbox,
            'v-loading': vLoading,
            'v-text': vText,
            'v-button-primary' : vButtonPrimary,
            'header-navi': headerNavi
        }
    })
    .mount('#top');
</script>