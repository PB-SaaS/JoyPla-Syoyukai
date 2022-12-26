<div id="top" v-cloak>
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto mb-96">
                <h1 class="text-2xl mb-2">請求内容</h1>
                <hr>
                <div class="p-4 text-base bg-gray-100 border border-gray-400 flex flex-col md:flex-row md:gap-6 gap-4 mb-6">
                    <?php if (gate('update_of_item_request_history')->can()) : ?>
                        <v-button-primary type="button" :disabled="! isChange" class="md:w-1/6 w-full" @click.native="onUpdate">内容を更新</v-button-primary>
                    <?php endif ?>
                    <?php if (gate('delete_of_item_request_history')->can()) : ?>
                        <v-button-danger type="button" class="md:w-1/6 w-full" @click.native="deleteSlip( itemRequest.requestHId )">
                            削除
                        </v-button-danger>
                    <?php endif ?>
                </div>
                <div class="p-4 text-base bg-gray-100 border border-gray-400">
                    <v-text title="請求日時" class="lg:flex w-full gap-6">{{ itemRequest.registrationTime }}</v-text>
                    <v-text title="請求番号" class="lg:flex w-full gap-6">{{ itemRequest.requestHId }}</v-text>
                    <v-text title="請求元部署" class="lg:flex w-full gap-6">{{ itemRequest.sourceDivision.divisionName }}</v-text>
                    <v-text title="請求先部署" class="lg:flex w-full gap-6">{{ itemRequest.targetDivision.divisionName }}</v-text>
                    <v-text title="請求担当者" class="lg:flex w-full gap-6">{{ itemRequest.requestUserName }}</v-text>
                    <?php if (gate('update_of_item_request_history')->can()) : ?>
                        <v-select class="lg:flex w-full gap-6" @change="isChange = true" change-class-name="inputChange" :options="[{ label: '個別請求', value: 1 },{ label: '消費請求', value: 2 }]" name="requestType" :rules="{required: true}" title="請求種別" label="請求種別"></v-select>
                    <?php else : ?>
                        <v-text title="請求種別" class="flex w-full gap-6">{{ itemRequest.requestTypeToString }}</v-text>
                    <?php endif ?>
                </div>
                <hr>
                <div class="p-4 text-lg font-bold">
                    <div class="flex w-full gap-6">
                        <div class="flex-initial lg:w-1/6 w-1/3">合計金額</div>
                        <div class="flex-auto">&yen; {{ numberFormat(totalAmount()) }}</div>
                    </div>
                </div>
                <hr>
                <div class="p-4 text-base">
                    <div class="lg:flex w-full gap-6">
                        <div class="flex-initial lg:w-1/6 w-full text-lg font-bold">商品情報</div>
                        <div class="flex-auto">
                            <div class="w-full mt-3" v-for="(item, idx) in fields" :key="item.key">
                                <div class="lg:flex ">
                                    <div class="lg:flex-1 flex lg:w-3/4">
                                        <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="item.value.itemImage"></item-view>
                                        <div class="flex-1 pl-4 lg:flex gap-6 break-all">
                                            <div class="flex-auto lg:w-4/5 w-full">
                                                <h3 class="text-xl font-bold font-heading">{{ item.value.item.makerName }}</h3>
                                                <p class="text-md font-bold font-heading">{{ item.value.item.itemName }}</p>
                                                <p class="text-md text-gray-500">{{ item.value.item.itemCode }}</p>
                                                <p class="text-md text-gray-500">{{ item.value.item.itemStandard }}</p>
                                                <p class="text-md text-gray-500">{{ item.value.item.itemJANCode }}</p>
                                                <?php if (gate('update_of_item_request_history')->can()) : ?>
                                                    <p class="text-base text-gray-900 lg:w-1/2">
                                                        <v-input-number @change="checkQuantity(idx); isChange = true" change-class-name="inputChange" :rules=" { between: [1 , 99999] }" :name="`requestItems[${idx}].requestQuantity`" label="請求数（入数）" :min="0" :unit="item.value.quantityUnit" :step="1" title="請求数（入数）"></v-input-number>
                                                    </p>
                                                <?php else : ?>
                                                    <div class="md:flex gap-6 ">
                                                        <div class="font-bold w-32">請求数</div>
                                                        <div>{{ numberFormat(item.value.requestQuantity) }} {{ item.value.quantity.quantityUnit }}</div>
                                                    </div>
                                                <?php endif ?>
                                                <div>
                                                    <span class="text-blue-700 text-lg mr-4">&yen; {{ numberFormat(requestPrice(idx) ) }}</span>
                                                    <span class="text-sm text-gray-900">( &yen; {{ numberFormat(item.value.unitPrice) }} / {{ item.value.quantity.quantityUnit }} )</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (gate('update_of_item_request_history')->can()) : ?>
                                    <div class="py-4">
                                        <v-button-danger class="w-full mx-auto lg:w-auto" type="button" @click.native="itemDelete(idx)">削除</v-button-danger>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        </div>
    </div>
</div>
<script>
    const PHPData = <?php echo json_encode($viewModel, true) ?>;
    console.log(PHPData);
    var JoyPlaApp = Vue.createApp({
        components: {
            'v-text': vText,
            'v-select': vSelect,
            'v-button-danger': vButtonDanger,
            'v-button-primary': vButtonPrimary,
            'v-checkbox': vCheckbox,
            'v-loading': vLoading,
            'header-navi': headerNavi,
            'v-breadcrumbs': vBreadcrumbs,
            'item-view': itemView,
            'v-input-number': vInputNumber,
            'v-textarea': vTextarea
        },
        setup() {
            const {
                ref,
                onCreated,
                onMounted
            } = Vue;

            const {
                useFieldArray,
                useForm
            } = VeeValidate;

            const itemRequest = PHPData.itemRequest;

            const {
                handleSubmit,
                control,
                meta,
                validate,
                values,
                isSubmitting
            } = useForm({
                initialValues: {
                    'requestHId': itemRequest.requestHId,
                    'requestType': itemRequest.requestType,
                    'requestItems': itemRequest.requestItems.map(x => {
                        x.rowrequestQuantity = parseInt(x.requestQuantity);
                        x.requestQuantity = parseInt(x.requestQuantity);
                        return x;
                    }),
                    'totalAmount': itemRequest.totalAmount
                },
                validateOnMount: false
            });

            const {
                remove,
                push,
                fields,
                update,
                replace
            } = useFieldArray('requestItems', control);

            console.log(fields);

            const loading = ref(false);

            const start = () => {
                loading.value = true;
            }

            const complete = () => {
                loading.value = false;
            }

            const sleepComplate = () => {
                window.setTimeout(function() {
                    complete();
                }, 500);
            }

            start();

            onMounted(() => {
                sleepComplate()
            });

            const breadcrumbs = [{
                    text: '請求メニュー',
                    disabled: false,
                    href: _ROOT + '&path=/itemrequest',
                },
                {
                    text: '請求履歴一覧',
                    disabled: false,
                    href: _ROOT + '&path=/itemrequest/history&isCache=true',
                },
                {
                    text: '請求内容',
                    disabled: true,
                }
            ];

            const numberFormat = (value) => {
                if (!value) {
                    return 0;
                }
                return new Intl.NumberFormat('ja-JP').format(value);
            };

            const isChange = ref(false);


            const requestPrice = (idx) => {
                return values.requestItems[idx].unitPrice * values.requestItems[idx].requestQuantity;
            };

            const totalAmount = () => {
                let num = 0;
                values.requestItems.forEach((v, idx) => {
                    num += requestPrice(idx);
                });
                return num;
            };

            const checkQuantity = (idx) => {
                if (values.requestItems[idx]) {
                    if (!values.requestItems[idx].requestQuantity || values.requestItems[idx].requestQuantity < 0) {
                        values.requestItems[idx].requestQuantity = 1;
                    }
                }
            }

            const createUpdateModel = () => {
                return values.requestItems.map(x => {
                    return {
                        'requestItemItemId': x.itemId,
                        'requestQuantity': x.requestQuantity,
                    };
                });
            }

            const onUpdate = async () => {
                const {
                    valid,
                    errors
                } = await validate();
                if (!valid) {
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: '入力エラーがございます。ご確認ください',
                    })
                } else {
                    Swal.fire({
                        title: '請求内容の更新',
                        text: "請求内容の更新を行います。\r\nよろしいですか？",
                        icon: 'warning',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            start();
                            let params = new URLSearchParams();
                            const updateModel = createUpdateModel();
                            params.append("path", "/api/itemrequest/history/" + values.requestHId + "/update");
                            params.append("requestType", values.requestType);
                            params.append("updateModel", JSON.stringify(encodeURIToObject(updateModel)));
                            params.append("_method", 'patch');
                            params.append("_csrf", _CSRF);

                            const res = await axios.post(_APIURL, params);
                            complete();
                            if (res.data.code != 200) {
                                throw new Error(res.data.message)
                            }

                            Swal.fire({
                                icon: 'success',
                                title: '請求内容の更新が完了しました。',
                            }).then((result) => {
                                location.reload();
                            });
                            return true;
                        }
                    }).catch((error) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'システムエラー',
                            text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
                        });
                    });
                };
            };

            const itemDelete = (idx) => {
                Swal.fire({
                    title: '商品削除の確認',
                    text: "商品を削除します。よろしいですか？",
                    icon: 'info',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        start();
                        let params = new URLSearchParams();
                        params.append("path", "/api/itemrequest/" + values.requestHId + "/" + values.requestItems[idx].requestId + "/delete");
                        params.append("_method", 'delete');
                        params.append("_csrf", _CSRF);
                        console.log(params);
                        const res = await axios.post(_APIURL, params);
                        console.log(res);
                        complete();
                        if (res.data.code != 200) {
                            throw new Error(res.data.message)
                        }

                        let addComment = "";
                        if (res.data.data.isItemRequestDeleted) {
                            addComment = "\r\n商品情報がなくなりましたので請求履歴も削除しました。";
                        }

                        Swal.fire({
                            icon: 'success',
                            title: '商品の削除が完了しました。',
                            text: addComment,
                        }).then((result) => {
                            if (res.data.data.isrequestItemDeleted) {
                                location.href = _ROOT + "&path=/itemrequest/history&isCache=true"
                            } else {
                                location.reload();
                            }

                        });
                        return true;
                    }
                }).catch((error) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'システムエラー',
                        text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
                    });
                });
            }

            const deleteSlip = (requestHId) => {
                Swal.fire({
                    title: '請求履歴を削除',
                    text: "削除後は元に戻せません。\r\nよろしいですか？",
                    icon: 'warning',
                    confirmButtonText: '削除します',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        start();

                        let params = new URLSearchParams();
                        params.append("path", "/api/itemrequest/" + requestHId + "/delete");
                        params.append("_method", 'delete');
                        params.append("_csrf", _CSRF);

                        const res = await axios.post(_APIURL, params);
                        console.log(res);
                        complete();
                        if (res.data.code != 200) {
                            throw new Error(res.data.message)
                        }

                        Swal.fire({
                            icon: 'success',
                            title: '請求履歴の削除が完了しました。',
                        }).then((result) => {
                            location.href = _ROOT + "&path=/itemrequest/history&isCache=true"
                        });
                        return true;
                    }
                }).catch((error) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'システムエラー',
                        text: 'システムエラーが発生しました。\r\nしばらく経ってから再度送信してください。',
                    });
                });
            }

            return {
                deleteSlip,
                onUpdate,
                itemDelete,
                isChange,
                numberFormat,
                itemRequest,
                fields,
                breadcrumbs,
                loading,
                start,
                complete,
                totalAmount,
                requestPrice,
                checkQuantity
            }
        },
        watch: {
            values: {
                async handler(val, oldVal) {
                    console.log(JSON.stringify(this.values));
                },
                deep: true
            }
        }
    }).mount('#top');
</script>