<div id="top" v-cloak="v-cloak">
    <header-navi :sidemenu="false"></header-navi>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto py-4">
                <h1 class="text-2xl mb-2">所属病院選択</h1>
                <div class="mb-2 lg:w-1/3">
                    <v-select 
                    name="affiliationId" 
                    label="所属施設選択" 
                    title="所属施設選択"
                    @change="changeTenant"
                    :options="affiliations"
                    />
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
            const { useForm } = VeeValidate;
            const { handleSubmit , control, meta , validate , values , isSubmitting  } = useForm({
                initialValues: {          
                    affiliationId: '',
                },
                validateOnMount : false
            });


            const postChangeAffiliation = async () => {
                let params = new URLSearchParams();
                params.append("path", "/api/user/change/affiliation");
                params.append("_method", 'post');
                params.append("_csrf", _CSRF);
                params.append("affiliationId", values.affiliationId);

                return await axios.post(_APIURL,params);
            }

            const changeTenant = async(e) => {
                await postChangeAffiliation()
                location.reload();
            }

            let affiliations = ref([]); 

            const getAffiliations = async () => {
                let params = new URLSearchParams();
                params.append("path", "/api/user/affiliation");
                params.append("_method", 'get');
                params.append("_csrf", _CSRF);
                let res = await axios.post(_APIURL,params);
                affiliations.value = res.data.data;
            }
            onMounted(() => {
                getAffiliations();
            })
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
                changeTenant,
                affiliations
            }
        },
        components: {
            'v-checkbox': vCheckbox,
            'v-loading': vLoading,
            'v-select': vSelect,
            'v-button-primary' : vButtonPrimary,
            'header-navi': headerNavi
        }
    })
    .mount('#top');
</script>