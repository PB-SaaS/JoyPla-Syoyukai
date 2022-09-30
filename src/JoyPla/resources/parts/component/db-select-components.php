const vSelectDivision = {
    components: {
        'v-select': vSelect
    },
    props: {
        name: {
            type: String,
            required: true,
            default: 'divisionId'
        },
        label: {
            type: String,
            required: false,
            default: ""
        },
        rules: {
            type: Object,
            required: false,
            default: {}
        },
        title: {
            type: String,
            required: false,
            default: ""
        },
        isOnlyMyDivision: {
            type: Boolean,
            required: false,
            default: false
        },
    },
    watch: {},
    data() {
        return {
            options: [
                {
                    label: "----- 部署を選択してください -----",
                    value: ""
                }
            ]
        }
    },
    mounted() {
        this.load();
    },
    methods: {
        load() {
            let self = this;
            let params = new URLSearchParams();
            params.append("path", "/api/division/show");
            params.append("isOnlyMyDivision", this.isOnlyMyDivision);
            params.append("_csrf", _CSRF);
            axios
                .post(_APIURL, params)
                .then((response) => {
                    let options = self.options;
                    if(response.data.data.length === 1)
                    {
                      options = [];
                    } 
                    response.data.data.forEach(function (x, i) {
                        options.push({label: x.divisionName, value: x.divisionId});
                    });
                    self.options = options;
                })
                .catch((error) => {
                    console.log(error);
                });
        }
    },
    template: `
  <v-select :options="options" :name="name" :rules="rules" :title="title" :label="label" :disabled="disabled"></v-select>
    `
};

const vMultipleSelectDivision = {
    components: {
        'v-multiple-select': vMultipleSelect
    },
    setup(props) {
        const {ref, onMounted} = Vue;

        const options = ref([]);
        const load = () => {
            let self = this;
            let params = new URLSearchParams();
            params.append("path", "/api/division/show");
            params.append("_csrf", _CSRF);
            axios
                .post(_APIURL, params)
                .then((response) => {
                    response
                        .data
                        .data
                        .forEach(function (x, i) {
                            options
                                .value
                                .push({label: x.divisionName, value: x.divisionId});
                        });
                })
                .catch((error) => {
                    console.log(error);
                });
        };

        onMounted(() => {
            load();
        });

        return {options}
    },
    props: {
        name: {
            type: String,
            required: true,
            default: 'divisionIds'
        },
        title: {
            type: String,
            required: true,
            default: ''
        }
    },
    template: `
    <v-multiple-select
      :name="name"
      :title="title"
      :options="options"
      id="multipleDivision"
      />
      `
};

const vMultipleSelectDistributor = {
    components: {
        'v-multiple-select': vMultipleSelect
    },
    setup(props) {
        const {ref, onMounted} = Vue;

        const options = ref([]);
        const load = () => {
            let self = this;
            let params = new URLSearchParams();
            params.append("path", "/api/distributor/show");
            params.append("_csrf", _CSRF);
            axios
                .post(_APIURL, params)
                .then((response) => {
                    response
                        .data
                        .data
                        .forEach(function (x, i) {
                            options
                                .value
                                .push({label: x.distributorName, value: x.distributorId});
                        });
                })
                .catch((error) => {
                    console.log(error);
                });
        };

        onMounted(() => {
            load();
        });

        return {options}
    },
    props: {
        name: {
            type: String,
            required: true,
            default: 'distributorIds'
        },
        title: {
            type: String,
            required: true,
            default: ''
        }
    },
    template: `
    <v-multiple-select
      :name="name"
      :title="title"
      :options="options"
      id="multipleDistributor"
      />
      `
};

const vInHospitalItemModal = {
    props: {
      unitPriceUse: {
          type: Boolean,
          required: true,
          default: '1'
      },
    },
    setup(props, context) {
        const {ref, toRef, toRefs, reactive, onMounted} = Vue;
        const {useForm} = VeeValidate;

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

        const {meta, validate, values, setFieldValue} = useForm({
            initialValues: {
                itemName: "",
                makerName: "",
                itemCode: "",
                itemStandard: "",
                itemJANCode: "",
                distributorIds: [],
                perPage: "10",
                currentPage: 1
            }
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        const showPages = ref(5);
        const totalCount = ref(0);
        const inHospitalItems = ref([]);
        const currentTab = ref('list');
        const tabs = [
            {
                label: 'リスト',
                value: 'list',
                disabled: false
            }, {
                label: '検索',
                value: 'search',
                disabled: false
            }
        ];
        const perPageOptions = [
            {
                label: "10件表示",
                value: "10"
            }, {
                label: "50件表示",
                value: "50"
            }, {
                label: "100件表示",
                value: "100"
            }
        ];

        const numberFormat = (value) => {
            if (!value) {
                return 0;
            }
            return value
                .toString()
                .replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g, '$1,');
        };

        const currentPageEdited = ref(1);
        
        const searchExec = () => {
            currentPageEdited.value = 1;
            listGet();
        };

        const searchCount = ref(0);


        const listGet = () => {
            let params = new URLSearchParams();
            params.append("path", "/api/inHospitalItem/show");
            params.append("search", JSON.stringify(encodeURIToObject(values)));
            params.append("_csrf", _CSRF);

            start();

            axios
                .post(_APIURL, params)
                .then((response) => {
                    if(props.unitPriceUse !== '1')
                    {
                      response.data.data.forEach((x , id)=>{
                        response.data.data[id].unitPrice = (x.price / x.quantity);
                      });
                    }
                    inHospitalItems.value = response.data.data;
                    totalCount.value = parseInt(response.data.count);
                    currentTab.value = 'list';
                })
                .catch((error) => {
                    complete();
                    if (searchCount.value > 0) {
                        Toast.fire({icon: 'error', title: '検索に失敗しました。再度お試しください。'})
                    }
                    searchCount.value++;
                })
                .then(() => {
                    complete();
                    if (searchCount.value > 0) {
                        Toast.fire({icon: 'success', title: '検索が完了しました'})
                    }
                    searchCount.value++;
                });
        };

        const getCurrentTab = (tab) => {
            currentTab.value = tab;
        };

        const changeParPage = () => {
            currentPageEdited.value = 1;
            listGet();
        };

        const searchClear = () => {
            setFieldValue('itemName', '');
            setFieldValue('makerName', '');
            setFieldValue('itemCode', '');
            setFieldValue('itemStandard', '');
            setFieldValue('itemJANCode', '');
            setFieldValue('distributorIds', []);
            listGet();
        };
        const add = (elem) => {
            context.emit('additem', elem);
            Toast.fire({icon: 'success', title: '反映しました'})
        };

        onMounted(() => {
            listGet();
        });

        return {
            loading,
            start,
            complete,
            currentPageEdited,
            values,
            searchExec,
            showPages,
            totalCount,
            inHospitalItems,
            currentTab,
            tabs,
            perPageOptions,
            listGet,
            getCurrentTab,
            changeParPage,
            searchClear,
            add,
            numberFormat
        }
    },
    emits: ['additem'],
    components: {
        'v-loading': vLoading,
        'v-tab': vTab,
        'v-input': vInput,
        'v-select': vSelect,
        'v-select-division': vSelectDivision,
        'v-multiple-select-distributorId': vMultipleSelectDistributor,
        'v-pagination': vPagination,
        'v-open-modal': vOpenModal,
        'v-button-default': vButtonDefault,
        'v-button-primary': vButtonPrimary,
        'v-input-number': vInputNumber,
        'item-view': itemView,
        'blowing': blowing
    },
    watch: {
        currentPageEdited() {
            this.values.currentPage = this.currentPageEdited;
            this.listGet();
            document
                .getElementById('inHospitalItemsList')
                .scrollTop = 0;
        }
    },
    methods: {},
    template: `
  <v-loading :show="loading"></v-loading>
  <teleport to="body">
    <v-open-modal id="inHospitalItemModal" headtext="商品検索" @show="listGet">
      <div class="flex flex-col" style="max-height: 68vh;">
        <div>
          <v-tab ref="tab"
              :tabs="tabs"
              v-model:currentTab="currentTab"
              @currentTab="getCurrentTab"></v-tab>
        </div>
        <template v-if="currentTab === 'search'">
          <div class="w-full overflow-y-auto" style="max-height: 85vh;">
            <div class="flex flex-wrap">
              <div class="w-full px-3 my-6 md:mb-0">
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="text"
                  name="makerName"
                  title="メーカー"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="text"
                  name="itemName"
                  title="商品名"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="text"
                  name="itemCode"
                  title="製品コード"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="text"
                  name="itemStandard"
                  title="規格"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="text"
                  name="itemJANCode" 
                  title="JANコード"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-multiple-select-distributorId
                    name="distributorIds"
                    title="卸業者名"
                    ></v-multiple-select-distributorId>
                </div>
                <v-input type="hidden" name="perPage"></v-input>
                <div class="mx-auto lg:w-2/3 mb-4 text-center flex items-center gap-6 justify-center">
                  <v-button-default type="button" v-on:click.native="searchClear">クリア</v-button-default>
                  <v-button-primary type="button" v-on:click.native="searchExec">検索</v-button-primary>
                </div>
              </div>
            </div>
          </div>
        </template>
        <template v-else-if="currentTab === 'list'">
          <div class="flex">
            <div class="flex-none lg:w-1/4 w-full my-2">
              <v-select
                name="perPage"
                :options="perPageOptions"
                @change="changeParPage"
              ></v-select>
            </div>
          </div>
          <div>
            {{ (totalCount == 0)? 0 : ( parseInt(values.perPage) * ( values.currentPage - 1 ) ) + 1 }}件 - {{ (( parseInt(values.perPage) * values.currentPage )  < totalCount ) ?  parseInt(values.perPage) * values.currentPage : totalCount  }}件 / 全 {{ totalCount }}件
          </div>
          <div class="max-h-full overflow-y-auto" id="inHospitalItemsList">
            <div>
              <div class="w-full mb-8 xl:mb-0">
                <div class="hidden lg:flex w-full sticky top-0 bg-white py-4 flex-wrap">
                  <div class="w-full lg:w-5/6">
                    <h4 class="font-bold font-heading text-gray-500 text-center">商品情報</h4>
                  </div>
                  <div class="w-full lg:w-1/6">
                    <h4 class="font-bold font-heading text-gray-500 text-center">反映</h4>
                  </div>
                </div>
                <div class="lg:pt-0 pt-4"> 
                  <div class="flex flex-wrap items-center mb-3" v-for="(elem, index) in inHospitalItems">
                    <div class="w-full lg:w-5/6 lg:px-4 px-0 mb-6 lg:mb-0">
                      <div class="flex flex-wrap items-center gap-4">
                        <div class="flex-none">
                          <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="elem.inItemImage"></item-view>
                        </div>
                        <div class="break-words flex-1 box-border w-44">
                          <h3 class="text-xl font-bold font-heading">{{ elem.makerName }}</h3>
                          <p class="text-md font-bold font-heading">{{ elem.itemName }}</p>
                          <p class="text-md font-bold font-heading">{{ elem.itemJANCode }}</p>
                          <p class="text-gray-500">{{ elem.itemCode }}<br>{{ elem.itemStandard }}</p>
                          <p class="text-gray-500">{{ elem.quantity }}{{ elem.quantityUnit }} 入り</p>
                          <p>
                            <span class="text-xl text-orange-600 font-bold font-heading">&yen; {{ numberFormat(elem.price) }}</span>
                            <span class="text-gray-400"> ( &yen; {{ 
                              numberFormat( elem.unitPrice ) 
                            }}/{{ elem.quantityUnit }} )</span>
                            <blowing :message="elem.priceNotice" title="金額管理備考" v-if="elem.priceNotice != ''" class="mx-4"></blowing>
                          </p>
                          <p class="text-gray-800">{{ elem.distributorName }}</p>
                        </div>
                      </div>
                    </div>
                    <div class="w-full lg:block lg:w-1/6 px-4 py-4">
                      <v-button-default type="button" class="w-full" v-on:click.native="add(elem)">反映</v-button-default>
                    </div>
                    <div class="py-2 px-4 w-full">
                      <div class="border-t border-gray-200"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div>
            <v-pagination 
                  :showPages="showPages"
                  v-model:currentPage="currentPageEdited"
                  :totalCount="totalCount"
                  :perPage="parseInt(values.perPage)"
                ></v-pagination>
          </div> 
        </template>
        <div class="hidden">
          <v-input type="hidden" name="makerName"></v-input>
          <v-input type="hidden" name="itemName"></v-input>
          <v-input type="hidden" name="itemCode"></v-input>
          <v-input type="hidden" name="itemStandard"></v-input>
          <v-input type="hidden" name="itemJANCode"></v-input>
          <v-input type="hidden" name="distributorIds"></v-input>
          <v-input type="hidden" name="currentPage"></v-input>
          <v-input type="hidden" name="perPage"></v-input>
        </div>
      </div>
      </v-open-modal>
  </teleport>
    `
};

const vOrderItemModal = {
    setup(props, context) {
        const {ref, toRef, toRefs, reactive, onMounted} = Vue;
        const {useForm} = VeeValidate;

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
            listGet();
        });

        const {meta, validate, values, setFieldValue} = useForm({
            initialValues: {
                orderDate: "",
                itemName: "",
                makerName: "",
                itemCode: "",
                itemStandard: "",
                itemJANCode: "",
                distributorIds: [],
                divisionIds: [],
                perPage: "10",
                currentPage: 1
            }
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        const showPages = ref(5);
        const totalCount = ref(0);
        const orders = ref([]);
        const currentTab = ref('list');
        const tabs = [
            {
                label: 'リスト',
                value: 'list',
                disabled: false
            }, {
                label: '検索',
                value: 'search',
                disabled: false
            }
        ];
        const perPageOptions = [
            {
                label: "10件表示",
                value: "10"
            }, {
                label: "50件表示",
                value: "50"
            }, {
                label: "100件表示",
                value: "100"
            }
        ];

        const numberFormat = (value) => {
            if (!value) {
                return 0;
            }
            return value
                .toString()
                .replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g, '$1,');
        };

        const searchExec = () => {
            currentPageEdited.value = 1;
            listGet();
        };

        const searchCount = ref(0);

        const listGet = () => {
            let params = new URLSearchParams();
            params.append("path", "/api/order/unreceivedShow");
            params.append("search", JSON.stringify(encodeURIToObject(values)));
            params.append("_csrf", _CSRF);

            start();

            axios
                .post(_APIURL, params)
                .then((response) => {
                    orders.value = response.data.data;
                    totalCount.value = parseInt(response.data.count);
                    currentTab.value = 'list';
                })
                .catch((error) => {
                    complete();
                    if (searchCount.value > 0) {
                        Toast.fire({icon: 'error', title: '検索に失敗しました。再度お試しください。'})
                    }
                    searchCount.value++;
                })
                .then(() => {
                    complete();
                    if (searchCount.value > 0) {
                        Toast.fire({icon: 'success', title: '検索が完了しました'})
                    }
                    searchCount.value++;
                });
        };

        const getCurrentTab = (tab) => {
            currentTab.value = tab;
        };

        const currentPageEdited = ref(1);

        const changeParPage = () => {
            currentPageEdited.value = 1;
            listGet();
        };

        const searchClear = () => {
            setFieldValue('orderDate', '');
            setFieldValue('itemName', '');
            setFieldValue('makerName', '');
            setFieldValue('itemCode', '');
            setFieldValue('itemStandard', '');
            setFieldValue('itemJANCode', '');
            setFieldValue('distributorIds', []);
            setFieldValue('divisionIds', []);
            listGet();
        };
        const add = (list , idx) => {
            context.emit('additem', list , idx);
            Toast.fire({icon: 'success', title: '反映しました'})
        };

        return {
            loading,
            start,
            complete,
            currentPageEdited,
            values,
            searchExec,
            showPages,
            totalCount,
            orders,
            currentTab,
            tabs,
            perPageOptions,
            listGet,
            getCurrentTab,
            changeParPage,
            searchClear,
            add,
            numberFormat
        }
    },
    props: {
      isOnlyMyDivision: {
            type: Boolean,
            required: false,
            default: false
        }
    },
    emits: ['additem'],
    components: {
        'v-loading': vLoading,
        'v-tab': vTab,
        'v-input': vInput,
        'v-select': vSelect,
        'v-select-division': vSelectDivision,
        'v-multiple-select-distributor': vMultipleSelectDistributor,
        'v-multiple-select-division': vMultipleSelectDivision,
        'v-pagination': vPagination,
        'v-open-modal': vOpenModal,
        'v-button-default': vButtonDefault,
        'v-button-primary': vButtonPrimary,
        'v-input-number': vInputNumber,
        'item-view': itemView,
        'blowing': blowing,
        'v-text' : vText,
    },
    watch: {
        currentPageEdited() {
            this.values.currentPage = this.currentPageEdited;
            this.listGet();
            document
                .getElementById('ordersList')
                .scrollTop = 0;
        }
    },
    methods: {},
    template: `
  <v-loading :show="loading"></v-loading>
  <teleport to="body">
    <v-open-modal id="orderItemModal" headtext="商品検索" @show="listGet">
      <div class="flex flex-col" style="max-height: 68vh;">
        <div>
          <v-tab ref="tab"
              :tabs="tabs"
              v-model:currentTab="currentTab"
              @currentTab="getCurrentTab"></v-tab>
        </div>
        <template v-if="currentTab === 'search'">
          <div class="w-full overflow-y-auto" style="max-height: 85vh;">
            <div class="flex flex-wrap">
              <div class="w-full px-3 my-6 md:mb-0">
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="month"
                  name="orderDate"
                  title="発注日"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="text"
                  name="makerName"
                  title="メーカー"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="text"
                  name="itemName"
                  title="商品名"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="text"
                  name="itemCode"
                  title="製品コード"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="text"
                  name="itemStandard"
                  title="規格"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-input 
                  @keypress.enter="searchExec"
                  type="text"
                  name="itemJANCode" 
                  title="JANコード"></v-input>
                </div>
                <div class="mx-auto lg:w-2/3 mb-4">
                  <v-multiple-select-distributor
                    name="distributorIds"
                    title="卸業者名"
                    ></v-multiple-select-distributor>
                </div>
                <div class="mx-auto lg:w-2/3 mb-4" v-if="!isOnlyMyDivision">
                  <v-multiple-select-division
                    name="divisionIds"
                    title="部署名"
                    ></v-multiple-select-division>
                </div>
                <v-input type="hidden" name="perPage"></v-input>
                <div class="mx-auto lg:w-2/3 mb-4 text-center flex items-center gap-6 justify-center">
                  <v-button-default type="button" v-on:click.native="searchClear">クリア</v-button-default>
                  <v-button-primary type="button" v-on:click.native="searchExec">検索</v-button-primary>
                </div>
              </div>
            </div>
          </div>
        </template>
        <template v-else-if="currentTab === 'list'">
          <div class="flex">
            <div class="flex-none lg:w-1/4 w-full my-2">
              <v-select
                name="perPage"
                :options="perPageOptions"
                @change="changeParPage"
              ></v-select>
            </div>
          </div>
          <div>
            {{ (totalCount == 0)? 0 : ( parseInt(values.perPage) * ( values.currentPage - 1 ) ) + 1 }}件 - {{ (( parseInt(values.perPage) * values.currentPage )  < totalCount ) ?  parseInt(values.perPage) * values.currentPage : totalCount  }}件 / 全 {{ totalCount }}件
          </div>
          <div class="max-h-full overflow-y-auto" id="ordersList">
            <div>
              <div class="w-full mb-8 xl:mb-0">
                <div class="hidden lg:flex w-full sticky top-0 bg-white py-4 flex-wrap">
                  <div class="w-full lg:w-5/6">
                    <h4 class="font-bold font-heading text-gray-500 text-center">商品情報</h4>
                  </div>
                  <div class="w-full lg:w-1/6">
                    <h4 class="font-bold font-heading text-gray-500 text-center">反映</h4>
                  </div>
                </div>
                <div class="lg:pt-0 pt-4"> 
                  <template v-for="(order, index) in orders">
                    <template v-for="(orderItem , idx ) in order.orderItems">
                      <div class="p-4 text-base lg:mx-4 mx-0 bg-gray-100 border border-gray-400 my-2">
                        <div class="flex gap-2">
                          <div class="flex-none">発注番号</div>
                          <div class="flex-1">{{ order.orderId }}</div>
                        </div>
                        <div class="flex gap-2">
                          <div class="flex-none">発注日</div>
                          <div class="flex-1">{{ order.orderDate }}</div>
                        </div>
                        <div class="flex gap-2">
                          <div class="flex-none">発注元部署</div>
                          <div class="flex-1">{{ order.division.divisionName }}</div>
                        </div>
                      </div>
                      <div class="flex flex-wrap items-center mb-3">
                        <div class="w-full lg:w-5/6 lg:px-4 px-0 mb-6 lg:mb-0">
                          <div class="flex flex-wrap items-center gap-4">
                            <div class="flex-none">
                              <item-view class="md:h-44 md:w-44 h-32 w-32" :base64="orderItem.itemImage"></item-view>
                            </div>
                            <div class="break-words flex-1 box-border w-44">
                              <h3 class="text-xl font-bold font-heading">{{ orderItem.item.makerName }}</h3>
                              <p class="text-md font-bold font-heading">{{ orderItem.item.itemName }}</p>
                              <p class="text-md font-bold font-heading">{{ orderItem.item.itemJANCode }}</p>
                              <p class="text-gray-500">{{ orderItem.item.itemCode }}<br>{{ orderItem.item.itemStandard }}</p>
                              <p class="text-gray-500">{{ orderItem.quantity.quantityNum }}{{ orderItem.quantity.quantityUnit }} 入り</p>
                              <p>
                                <span class="text-xl text-orange-600 font-bold font-heading">
                                  {{ numberFormat( orderItem.orderQuantity ) }}{{ orderItem.quantity.itemUnit }} 発注中
                                </span>
                                <span class="text-xl text-orange-600 font-bold font-heading" v-if="(orderItem.orderQuantity - orderItem.receivedQuantity) > 0">
                                  {{ numberFormat( ( orderItem.orderQuantity - orderItem.receivedQuantity) ) }}{{ orderItem.quantity.itemUnit }} 未入荷
                                </span>
                              </p>
                              <p class="text-gray-800">{{ orderItem.distributor.distributorName }}</p>
                            </div>
                          </div>
                        </div>
                        <div class="w-full lg:block lg:w-1/6 px-4 py-4">
                          <v-button-default type="button" class="w-full" v-on:click.native="add(order , idx)">反映</v-button-default>
                        </div>
                        <div class="py-2 px-4 w-full">
                          <div class="border-t border-gray-200"></div>
                        </div>
                      </div>
                    </template>
                  </template>
                </div>
              </div>
            </div>
          </div>
          <div>
            <v-pagination 
                  :showPages="showPages"
                  v-model:currentPage="currentPageEdited"
                  :totalCount="totalCount"
                  :perPage="parseInt(values.perPage)"
                ></v-pagination>
          </div> 
        </template>
        <div class="hidden">
          <v-input type="hidden" name="makerName"></v-input>
          <v-input type="hidden" name="itemName"></v-input>
          <v-input type="hidden" name="itemCode"></v-input>
          <v-input type="hidden" name="itemStandard"></v-input>
          <v-input type="hidden" name="itemJANCode"></v-input>
          <v-input type="hidden" name="distributorIds"></v-input>
          <v-input type="hidden" name="divisionIds"></v-input>
          <v-input type="hidden" name="orderDate"></v-input>
          <v-input type="hidden" name="currentPage"></v-input>
          <v-input type="hidden" name="perPage"></v-input>
        </div>
      </div>
      </v-open-modal>
  </teleport>
    `
};

const vBarcodeSearch = {
    setup(props, {emit}) {
        const {ref} = Vue;
        const {useForm} = VeeValidate;
        const {
            handleSubmit,
            control,
            meta,
            validate,
            values,
            isSubmitting,
            resetForm
        } = useForm({
            initialValues: {
                barcode: ""
            },
            validateOnMount: false
        });

        const loading = ref(false);
        const start = () => {
            loading.value = true;
        }

        const complete = () => {
            loading.value = false;
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        const getInHospitalItem = () => {
            if (!values.barcode) {
                return false;
            }
            let params = new URLSearchParams();
            params.append("path", "/api/barcode/search");
            params.append("barcode", values.barcode);
            params.append("_csrf", _CSRF);
            start();

            axios
                .post(_APIURL, params)
                .then((response) => {
                    complete();
                    emit('additem', response.data.data);
                    resetForm({barcode: ""});
                })
                .catch((error) => {
                    complete();
                    resetForm({barcode: ""});
                    Toast.fire({icon: 'error', title: '検索に失敗しました。再度お試しください。'})
                })
            };

        return {
          getInHospitalItem, 
          values,
          loading}
    },
    components: {
        'v-button-primary': vButtonPrimary,
        'v-input': vInput,
        'v-text' : vText,
        'v-loading' : vLoading
    },
    props: {},
    methods: {
        onEnter: function () {
            this.getInHospitalItem();
        }
    },
    template: `
    <v-loading :show="loading"></v-loading>
    <fieldset class="relative flex gap-2">
      <div class="pointer-events-none flex items-center px-2 text-gray-700 flex-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div> 
      <v-input 
      name="barcode" 
      label="バーコード" 
      type="text" 
      title=""
      class="flex-1 w-5/6"
      autocomplete="false"
      @keypress.enter.native="onEnter"></v-input>
      <v-button-primary type="button" class="flex-none" @click.native="onEnter">検索</v-button-primary>
    </fieldset>
  `
};

const vBarcodeSlipSearch = {
    setup(props, {emit}) {
        const {ref} = Vue;
        const {useForm} = VeeValidate;
        const {
            handleSubmit,
            control,
            meta,
            validate,
            values,
            isSubmitting,
            resetForm
        } = useForm({
            initialValues: {
                barcode: ""
            },
            validateOnMount: false
        });

        const loading = ref(false);
        const start = () => {
            loading.value = true;
        }

        const complete = () => {
            loading.value = false;
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        const search = () =>{
            if (!values.barcode) {
                return false;
            }
            let params = new URLSearchParams();
            params.append("searchValue", values.barcode);
            params.append("_csrf", _CSRF);
            start();

            axios
                .post('%url/rel:mpgt:barcodeSearchAPI%', params)
                .then((response) => {
                    complete();
                    if( !response.data.urls )
                    {
                      Swal.fire({
                          icon: 'warning',
                          title: '情報がありませんでした',
                      });
                      return false;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: '検索完了',
                    }).then((result) => {
                        location.href=response.data.urls[0];
                    });
                })
                .catch((error) => {
                    complete();
                    resetForm({barcode: ""});
                })
            };

        return {
          search, 
          values,
          loading}
    },
    components: {
        'v-button-primary': vButtonPrimary,
        'v-input': vInput,
        'v-text' : vText,
        'v-loading' : vLoading
    },
    props: {},
    methods: {
        onEnter: function () {
            this.search();
        }
    },
    template: `
    <v-loading :show="loading"></v-loading>
    <fieldset class="relative flex gap-2">
      <div class="pointer-events-none flex items-center px-2 text-gray-700 flex-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div> 
      <v-input 
      name="barcode" 
      label="バーコード" 
      type="text" 
      title=""
      class="flex-1 w-5/6"
      autocomplete="false"
      @keypress.enter.native="onEnter"></v-input>
      <v-button-primary type="button" class="flex-none" @click.native="onEnter">検索</v-button-primary>
    </fieldset>
  `
};

const vBarcodeSearchForOrderData = {
    setup(props, {emit}) {
        const {ref} = Vue;
        const {useForm} = VeeValidate;
        const {
            handleSubmit,
            control,
            meta,
            validate,
            values,
            isSubmitting,
            resetForm
        } = useForm({
            initialValues: {
                barcode: ""
            },
            validateOnMount: false
        });

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
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        const getOrderItem = () => {
            if (!values.barcode) {
                return false;
            }
            let params = new URLSearchParams();
            params.append("path", "/api/barcode/order/search");
            params.append("barcode", values.barcode);
            params.append("_csrf", _CSRF);
            start(); 

            axios
                .post(_APIURL, params)
                .then((response) => {
                    complete();
                    emit('additem', response.data.data);
                    resetForm({barcode: ""});
                })
                .catch((error) => {
                    complete();
                    resetForm({barcode: ""});
                    Toast.fire({icon: 'error', title: '検索に失敗しました。再度お試しください。'})
                })
            };

        return {getOrderItem, values , loading}
    },
    components: {
        'v-button-primary': vButtonPrimary,
        'v-input': vInput,
        'v-text' : vText,
        'v-loading' : vLoading
    },
    props: {},
    methods: {
        onEnter: function () {
            this.getOrderItem();
        }
    },
    template: `
    <v-loading :show="loading"></v-loading>
    <fieldset class="relative flex gap-2">
      <div class="pointer-events-none flex items-center px-2 text-gray-700 flex-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div> 
      <v-input 
      name="barcode" 
      label="バーコード" 
      type="text" 
      title=""
      class="flex-1 w-5/6"
      autocomplete="false"
      @keypress.enter.native="onEnter"></v-input>
      <v-button-primary type="button" class="flex-none" @click.native="onEnter">検索</v-button-primary>
    </fieldset>
  `
};
