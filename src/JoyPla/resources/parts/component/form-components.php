const vText = {
  setup(props) {
    console.log(props);
  },
  props: {
    title : {
        type: String, 
        required: false,
        default: ""
    },
    isRequired: {
        type: String, 
        required: false,
        default: false
    },
  },
  template: `
  <fieldset>
    <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal" v-if="title != ''">
        {{ title }} <span v-if="isRequired" class="bg-red-400 text-white text-md font-medium inline-flex items-center px-2.5 rounded">必須</span>
    </div>
    <div class="flex-auto">
        <slot></slot>
    </div>
  </fieldset>
  ` 
}

const vInput = {
  components: {
    'v-text' : vText
  },
  setup(props) {
    // a simple `name` field with basic required validator
    const { ref } = Vue;
    const { value, errorMessage , meta , validate } = VeeValidate.useField(
      Vue.toRef(props, 'name'),
      Vue.toRef(props, 'rules') ,
       {label : props.label });

    const valid = meta.valid;
    const validated = meta.validated;

    const isRequired = () => {
      return props.rules.required;
    }

    const changeClass = ref({});
    return {
      changeClass,
      isRequired,
      value,
      meta,
      successClassName: ['text-gray-700', 'border-gray-300'],
      errorClassName: ['text-red-500', 'border-red-500'],
      errorMessage,
    };
  },
  props: {
    label: {
        type: String, 
        required: false,
        default: "", 
    },
    type: { 
        type: String, 
        required: true 
    },
    name: { 
        type: String, 
        required: true 
    },
    placeholder: { 
        type: String, 
        required: false 
    },
    rules: {
        type: Object, 
        required: false, 
        default: {}
    },
    title: {
        type: String, 
        required: false,
        default : ""
    },
    changeClassName: {
      type: String,
      required: false,
      default : ""
    }
  },
  watch: {
    value(){
      this.changeClass = {
        [this.changeClassName] : true
      };
    }
  },
  template: `
    <v-text :title="title" :isRequired="isRequired()">
      <input
            :type="type"
            :placeholder="placeholder"
            v-model="value"
            class="appearance-none w-full py-2 px-3 leading-tight h-full text-left flex-initial bg-white border"
            :class="[ ( ! meta.valid && meta.validated == true) ? errorClassName : successClassName , changeClass]"
        />
        <span class="text-red-500">{{ errorMessage }}</span>
    </v-text>
     `
};
const vInputNumber = {
  components: {
    'v-text' : vText
  },
  setup(props) {
    // a simple `name` field with basic required validator
    const { ref , onMounted } = Vue;
    const {  value, errorMessage , resetField , handleChange} = VeeValidate.useField(
      Vue.toRef(props, 'name'),
      Vue.toRef(props, 'rules') , 
      { label : props.label });

    const updateValue = (e) => {
      let num = ( ! e.target.value )? 0 : parseInt(e.target.value) ;
      value.value = num;
    };

    const increment = () => {
      let num = parseInt(value.value) + props.step;
      if( num > props.max ){ return }
      value.value = num;
    };
    const decrement = () => {
      let num = parseInt(value.value) - props.step;
      if( num < props.min ){ return }
      value.value = num;
    };
    const isRequired = () => {
      return props.rules.required;
    }
    const changeClass = ref({});

    const incrementDom = ref(null);
    const decrementDom = ref(null);

    onMounted(() => {

      decrementDom.value.addEventListener('pointerdown', () => {
        decrement();
        let count = 0;
        let timer;
        const longPushSecond = 1;
        const intervalId = setInterval(function(){
          count++;
          if((count / 10) > longPushSecond){
            decrement();
          }
        }, 100);

        document.addEventListener('pointerup', () => {   
          count = 0;     
          clearInterval(intervalId)
        }, { once: true })
      });

      incrementDom.value.addEventListener('pointerdown', () => {
        increment();
        let count = 0;
        let timer;
        const longPushSecond = 1;
        const intervalId = setInterval(function(){
          count++;
          if((count / 10) > longPushSecond){
            increment();
          }
        }, 100);

        document.addEventListener('pointerup', () => {   
          count = 0;     
          clearInterval(intervalId)
        }, { once: true })
      })
    });
    return {
      incrementDom,
      decrementDom,
      changeClass,
      isRequired,
      updateValue,
      increment,
      decrement,
      value,
      errorMessage,
    };
  },
  props: {
    label: {
        type: String, 
        required: false,
        default: "", 
    },
    rules: {
        type: Object, 
        required: false, 
        default: {}
    },
    name: {
        type: String, 
        required: true 
    },
    max: { 
        type: Number, 
        required: false 
    },
    min: { 
        type: Number, 
        required: false 
    },
    step: { 
        type: Number, 
        required: false ,
        default : 1
    },
    unit: { 
        type: String, 
        required: false ,
        default : ""
    },
    title: {
        type: String, 
        required: false,
        default : ""
    },
    changeClassName: {
      type: String,
      required: false,
      default : ""
    }
  },
  computed: {
  },
  data(){
    return {
      'classobj' : this.getClass()
    };
  },
  emits: ['update:current'],
  methods: {
    getClass()
    {
      return  {
            'text-red-500' : !! this.errorMessage,
            'border-red-500' : !! this.errorMessage,
            'border-gray-300' : !this.errorMessage,
            'text-gray-700' : !this.errorMessage,
          };
    }
  },
  watch: {
    value(newValue) {
      this.changeClass = {
        [this.changeClassName] : true
      };
      if(this.$attrs.onChange){
        this.$attrs.onChange();
      };
      this.$emit('update:current', newValue);
    },
    errorMessage() {
      this.classobj = this.getClass()
    }

  },
  template: `
    <v-text :title="title" :isRequired="isRequired()">
      <div class="flex">
        <div class="flex-none">
          <button 
            ref="decrementDom"
            style="touch-action: none; user-select: none;"
            oncontextmenu="return false;"
            type="button" 
            class="bg-white hover:border-gray-400 text-gray-700 py-2 px-4 border border-gray-300 h-full w-full" 
            >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
            </svg>
          </button>
        </div>
        <div class="flex-initial w-full flex border bg-white" :class="[classobj , changeClass]">
          <input
              :class="changeClass"
              type="number"
              :name="name"
              v-model="value"
              class="appearance-none w-5/6 py-2 pl-3 leading-tight h-full text-right flex-initial bg-white"
          />
          <div class="px-2 flex items-center justify-center max-w-xs bg-white whitespace-pre"
              :class="changeClass">
            {{ unit }}
          </div>
        </div>
        <div class="flex-none">
          <button 
            ref="incrementDom"
            style="touch-action: none; user-select: none;"
            oncontextmenu="return false;"
            type="button" 
            class="bg-white hover:border-gray-400 text-gray-700 py-2 px-4 border border-gray-300 h-full w-full" 
            >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
          </button>
        </div>
      </div>
      <span class="text-red-500">{{ errorMessage }}</span>
    </v-text>
    ` 
};

const vTextarea = {
  components: {
    'v-text' : vText
  },
  setup(props) {
    // a simple `name` field with basic required validator
    const { ref } = Vue;
    const { value, errorMessage , meta , validate } = VeeValidate.useField(
      Vue.toRef(props, 'name'),
      Vue.toRef(props, 'rules') ,
       {label : props.label });

    const valid = meta.valid;
    const validated = meta.validated;

    const isRequired = () => {
      return props.rules.required;
    }

    const changeClass = ref({});

    const textcount = () => {
      let len = 0;
      for (let i = 0; i < value.value.length; i++) {
        (value.value[i].match(/[ -~]/)) ? len += 1 : len += 2;
      }
      return len ;
    }

    return {
      textcount,
      changeClass,
      isRequired,
      value,
      meta,
      successClassName: ['text-gray-700', 'border-gray-300'],
      errorClassName: ['text-red-500', 'border-red-500'],
      errorMessage,
    };
  },
  props: {
    label: {
        type: String, 
        required: false,
        default: "", 
    },
    type: { 
        type: String, 
        required: true 
    },
    name: { 
        type: String, 
        required: true 
    },
    placeholder: { 
        type: String, 
        required: false 
    },
    rules: {
        type: Object, 
        required: false, 
        default: {}
    },
    title: {
        type: String, 
        required: false,
        default : ""
    },
    changeClassName: {
      type: String,
      required: false,
      default : ""
    }
  },
  watch: {
    value(){
      this.changeClass = {
        [this.changeClassName] : true
      };
    }
  },
  template: `
    <v-text :title="title" :isRequired="isRequired()">
      <div class="relative">
        <textarea
            :type="type"
            :placeholder="placeholder"
            v-model="value"
            class="appearance-none w-full py-2 px-3 leading-tight h-32 text-left flex-initial bg-white border"
            :class="[ ( ! meta.valid && meta.validated == true) ? errorClassName : successClassName , changeClass]"
        ></textarea>
        <span class="absolute bottom-4 right-6">{{ textcount() }}文字</span>
      </div>
        <span class="text-red-500">{{ errorMessage }}</span>
    </v-text>
     `
};

const vSelect = {
  components: {
    'v-text' : vText,
  },
  setup(props) {
    // a simple `name` field with basic required validator]
    const { ref , onMounted } = Vue;
    const { value, errorMessage , meta , validate } = VeeValidate.useField(
    Vue.toRef(props, 'name'),
    Vue.toRef(props, 'rules') ,
    {label : props.label });

    const isRequired = () => {
      return props.rules.required;
    }

    const changeClass = ref({});
    return {
      changeClass,
      isRequired,
      value,
      meta,
      successClassName: ['text-gray-700', 'border-gray-300'],
      errorClassName: ['text-red-500', 'border-red-500'],
      errorMessage,
    };
  },
  props: {
    options: { 
        type: Array, 
        required: true 
    },
    name: { 
        type: String, 
        required: true 
    },
    rules: {
        type: Object, 
        required: false, 
        default: {}
    },
    label : {
        type: String, 
        required: false,
        default: ""
    },
    title : {
        type: String, 
        required: false,
        default: ""
    },
    changeClassName: {
      type: String,
      required: false,
      default : ""
    }
  },
  mounted() {
  },
  methods: {
  },
  watch: {
    options() {
      if(this.options.length === 1)
      {
        this.value = this.options[0].value;
      }
    },
    value(newValue) {
      this.changeClass = {
        [this.changeClassName] : true
      };
      if(this.$attrs.onChange){
        this.$attrs.onChange();
      };
    },
  },
  template: `
  <v-text :title="title" :isRequired="isRequired()">
    <div class="relative">
      <select :name="name"
        v-model="value"
        :class="[( ! meta.valid && meta.validated == true) ? errorClassName : successClassName , changeClass]"
        class="appearance-none border w-full py-2 px-3 leading-tight" >
        <template v-for="(option, index) in options">
          <option :value="option.value" :selected="option.value == value">
            {{ option.label }}
          </option>
        </template>
      </select>
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
        </svg>
      </div>
    </div>
    <span class="text-red-500">{{ errorMessage }}</span>
  </v-text>
  ` 
};
const vCheckbox = {
  setup(props) {
    const { toRef } = Vue;
    const { useField } = VeeValidate;
    // Must use `toRef` to make the checkboxes names reactive
    const { checked, handleChange , value, errorMessage } = useField(
      toRef(props, 'name'),
      toRef(props, 'rules'), 
      {
        label : props.label,
        type: 'checkbox',
        checkedValue: props.value,
      });

    return {
      checked, // readonly
      handleChange,
      errorMessage,
    };
  },
  props: {
    modelValue: {
      type: null,
    },
    // Field's own value
    value: {
      type: null,
    },
    name: {
      type: String,
    },
    rules: {
      type: Object,
      default: {},
    },
    label : {
        type: String, 
        required: false,
        default: ""
    },
    title : {
        type: String, 
        required: false,
        default: ""
    }
  },
  template: `
    <label>
      <input
        type="checkbox"
        @input="handleChange(value)"
        class="form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain mr-2 cursor-pointer"
        :value="value"
        :name="name"
      />{{ title }}
    </label><br>
    <span class="text-red-500">{{ errorMessage }}</span>
    ` 
};
const vButtonPrimary = {
  props: {
    type: { 
        type: String, 
        required: true 
    },
  },
  data() {
    return {
      values: []
    };
  },
  methods: {
  },
  template: `<button :type="type" class="
  disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none
  hover:border-sushi-700 text-sushi-50 py-2 px-4 border border-sushi-200 bg-sushi-500 hover:bg-sushi-400"><slot></slot></button>`
};
const vButtonDefault = {
  props: {
    type: { 
        type: String, 
        required: true 
    },
  },
  data() {
    return {
      values: []
    };
  },
  methods: {
  },
  template: `<button :type="type" class="
  disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none
  bg-white hover:border-gray-400 text-gray-700 py-2 px-4 border border-gray-300"><slot></slot></button>`
};

const vButtonDanger = {
  props: {
    type: { 
        type: String, 
        required: true 
    },
  },
  data() {
    return {
      values: []
    };
  },
  methods: {
  },
  template: `<button :type="type" class="
  disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none
  hover:border-red-700 text-white py-2 px-4 border border-red-200 bg-red-500 hover:bg-red-400"><slot></slot></button>`
};

const vAlert = {
  setup(props,{emit}){
    const { onMounted } = Vue;
    onMounted(() => {
      MicroModal.init({
          disableScroll: true
      });
    });
    const open = () => {
      MicroModal.show(props.id);
    };
    const close = () => {
      MicroModal.close(props.id);
    };

    const ok = () => {
      close();
      emit('ok')
    };

    return {
      open,
      ok,
    }
  },
  components: {
    'v-button-default': vButtonDefault,
    'v-button-primary': vButtonPrimary,
  },
  props: {
    id: { 
        type: String, 
        required: true 
    },
    headtext: { 
        type: String, 
        required: false,
        default: "" 
    },
    message: { 
        type: String, 
        required: true 
    },
  },
  template : `
  <div class="modal micromodal-slide" :id="id">
    <div class="modal__overlay z-10">
      <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-alert-title">
        <header class="modal__header">
          <h2 class="modal__title" id="modal-alert-title">
            {{ headtext }}
          </h2>
          <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content whitespace-pre" id="modal-alert-content">
          {{ message }}
        </main>
        <footer class="modal__footer text-right">
          <v-button-primary type="button"  @click.native="ok">OK</v-button-primary>
        </footer>
      </div>
    </div>
  </div>
    `,
};
const vConfirm = {
  setup(props,{emit}){
    const { onMounted } = Vue;
    onMounted(() => {
      MicroModal.init({
          disableScroll: true
      });
    });
    const open = () => {
      MicroModal.show(props.id);
    };
    const close = () => {
      MicroModal.close(props.id);
    };

    const cancel = () => {
      close();
      emit('cancel')
    };

    const ok = () => {
      close();
      emit('ok')
    };

    return {
      open,
      cancel,
      ok,
    }
  },
  components: {
    'v-button-default': vButtonDefault,
    'v-button-primary': vButtonPrimary,
  },
  props: {
    id: { 
        type: String, 
        required: true 
    },
    headtext: { 
        type: String, 
        required: false,
        default: "" 
    },
    message: { 
        type: String, 
        required: true 
    },
  },
  template : `
  <div class="modal micromodal-slide" :id="id">
    <div class="modal__overlay z-10">
      <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-confirm-title">
        <header class="modal__header">
          <h2 class="modal__title" id="modal-confirm-title">
            {{ headtext }}
          </h2>
          <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content whitespace-pre" id="modal-confirm-content">
          {{ message }}
        </main>
        <footer class="modal__footer text-right flex gap-4 justify-end">
          <v-button-default type="button" @click.native="cancel" >キャンセル</v-button-default>
          <v-button-primary type="button" @click.native="ok">OK</v-button-primary>
        </footer>
      </div>
    </div>
  </div>
    `,
};

const vOpenModal = {
  setup(props,{emit}){
    const { onMounted } = Vue;
    onMounted(() => {
      MicroModal.init({
          disableScroll: true 
      });
    });

    const open = () => {
      MicroModal.show(props.id);
    };

    const close = () => {
      MicroModal.close(props.id);
    };

    return {
      open,
      close
    }
  },
  props: {
    id: { 
        type: String, 
        required: true 
    },
    headtext: { 
        type: String, 
        required: false,
        default: "" 
    },
  },
  template : `
  <div class="modal micromodal-slide" :id="id">
    <div class="modal__overlay z-10">
      <div class="bg-white py-7 px-4 lg:px-7 rounded overflow-y-auto box-border lg:w-2/3 w-full max-w-none" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="flex flex-col h-full">
          <header class="modal__header">
            <h2 class="modal__title" id="modal-title">
              {{ headtext }}
            </h2>
            <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
          </header>
          <main class="h-full" id="modal-content">
            <slot></slot>
          </main>
        </div>  
      </div>
    </div>
  </div>
    `,
  methods :{
    clickEvent: function(){
      this.$emit('from-child')
     },
    stopEvent: function(){
      event.stopPropagation()
    }    
  }
};

const vTab = {
  setup(props,{ emit }){
    const setTab = (tab) => {
      emit('update:currentTab', tab);
    };

    return {
      setTab
    }
  },
  props: {
    currentTab: { 
        type: String,
        required: true 
    }, //現在のページ
    tabs: { 
        type: Array, 
        required: true 
    },
  },
  computed: {
  },
  data(){
    return{
      noActiveClass : {
        'inline-block': true,
        'px-4': true,
        'py-2': true,
        'rounded-t-lg': true,
        'border-b-2': true,
        'border-transparent' : true,
        'hover:text-gray-600' : true ,
        'hover:border-gray-300' : true
      },
      activeClass : {
        'inline-block': true,
        'px-4': true,
        'py-2': true,
        'text-blue-600' : true,
        'border-blue-600' : true,
        'rounded-t-lg': true,
        'border-b-2': true,
      },
      disabledClass : {
        'inline-block': true,
        'px-4': true,
        'py-2': true,
        'text-gray-400': true,
        'rounded-t-lg': true,
        'cursor-not-allowed' : true
      }
    }
  },
  template : `
  <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200">
      <ul class="flex flex-wrap -mb-px">
          <li class="mr-2" v-for="(elem , key ) in tabs">
              <a href="#" :class="activeClass" v-if="elem.value === currentTab">{{ elem.label }}</a>
              <a href="#" :class="disabledClass" v-else-if="elem.disabled">{{ elem.label }}</a>
              <a href="#" :class="noActiveClass" v-else v-on:click.prevent="setTab(elem.value)">{{ elem.label }}</a>
          </li>
      </ul>
  </div>
    `,
  mounted() {
  },
};

const vPagination = {
  props: {
    showPages: { 
        type: Number, 
        required: true 
    }, //ページネーションを何件表示するか
    currentPage: { 
        type: Number, 
        required: true 
    }, //現在のページ
    totalCount: { 
        type: Number, 
        required: true 
    },//総件数
    perPage: { 
        type: Number, 
        required: true 
    }, //1ページあたりの表示件数
  },
  data(){
    return {
      perPageEdited: Number,
      totalCountEdited: Number,
      totalPages: Number,
      currentPageEdited: Number, //現在のページ
    };
  },
  computed: {
    //ページ番号を計算する
    numFix() {
      var vm = this;
      return function (num) {
        var ajust = 1 + (vm.showPages - 1) / 2;
        var result = num;
        //前ページがマイナスになる場合は1からはじめる
        if (vm.currentPageEdited > vm.showPages / 2) {
          var result = num + vm.currentPageEdited - ajust;
        }
        //後ページが最大ページを超える場合は最大ページを超えないようにする
        if (vm.currentPageEdited + vm.showPages / 2 > vm.totalPages) {
          var result = vm.totalPages - vm.showPages + num;
        } //総ページ数が表示ページ数に満たない場合、連番そのまま
        if (vm.totalPages <= vm.showPages) {
          var result = num;
        }
        return result;
      };
    },
    //総記事数が表示ページ数以下の場合に調整する
    showPagesFix() {
      var vm = this;
      if (vm.totalPages < vm.showPages) {
        return vm.totalPages;
      } else {
        return vm.showPages;
      }
    },
  },
  mounted() {
    this.perPageEdited = this.perPage;
    this.totalCountEdited = this.totalCount;
    this.currentPageEdited = this.currentPage;
    this.totalPages = Math.ceil( this.totalCountEdited / this.perPageEdited );
  },
  watch: {
    perPage(val) {
      this.perPageEdited = this.perPage;
      this.totalPages = Math.ceil( this.totalCountEdited / this.perPageEdited );
    },
    currentPageEdited(val) {
      //親コンポーネントに現在のページを送る
      this.$emit('update:currentPage', val);
    },
    //ページネーションを複数設置したときの対応
    currentPage(val) {
      this.currentPageEdited = this.currentPage;
    },
    totalCount(val) {
      this.totalCountEdited = this.totalCount;
      this.totalPages = Math.ceil( this.totalCountEdited / this.perPageEdited );
    }
  },
  emits: ['update:currentPage'],
  methods: {
    //何ページ目を表示するか
    setPage(page) {
      var vm = this;
      //マイナスにならないようにする
      if (page <= 0) {
        this.currentPageEdited = 1;
      }
      //最大ページを超えないようにする
      else if (page > vm.totalPages) {
        this.currentPageEdited = this.totalPages;
      } else {
        this.currentPageEdited = page;
      }
    },
  },
  template : `
  <div class="flex justify-center py-2" v-if="totalPages">
    <nav aria-label="Page navigation">
      <ul class="flex list-style-none gap-3">
        <li v-if="numFix(1) != 1">
          <a
            class="page-link relative block py-1.5 px-3  border-0 bg-transparent outline-none transition-all duration-300 rounded text-gray-800 hover:text-gray-800 hover:bg-gray-200 focus:shadow-none"
              href="#"
              @click.prevent="setPage(1)" 
            >1</a>
        </li>
        <li v-if="numFix(1) > 2">
          <span class="page-link relative block py-1.5 px-3  border-0 bg-transparent outline-none transition-all duration-300 rounded text-gray-800 hover:text-gray-800 hover:bg-gray-200 focus:shadow-none">...</span>
        </li>
        <li
          class="page-item"
          v-for="num in showPagesFix"
          :key="num"
          :class="{'active' : numFix(num) == currentPageEdited}"
        >
          <template v-if="numFix(num) == currentPageEdited">
            <span class="page-link relative block py-1.5 px-3  border-0 bg-blue-600 outline-none transition-all duration-300 rounded text-white hover:text-white hover:bg-blue-600 shadow-md focus:shadow-md">{{ numFix(num) }}</span>
          </template>
          <a
          class="page-link relative block py-1.5 px-3  border-0 bg-transparent outline-none transition-all duration-300 rounded text-gray-800 hover:text-gray-800 hover:bg-gray-200 focus:shadow-none"
            href="#"
            @click.prevent="setPage(numFix(num))"
            v-else
          >{{ numFix(num) }}</a>
        </li>
        <li v-if=" numFix(showPagesFix) < ( totalPages - 1 )">
          <span class="page-link relative block py-1.5 px-3  border-0 bg-transparent outline-none transition-all duration-300 rounded text-gray-800 hover:text-gray-800 hover:bg-gray-200 focus:shadow-none">...</span>
        </li>
        <li v-if=" numFix(showPagesFix) != totalPages">
          <a
            class="page-link relative block py-1.5 px-3  border-0 bg-transparent outline-none transition-all duration-300 rounded text-gray-800 hover:text-gray-800 hover:bg-gray-200 focus:shadow-none"
              href="#"
              @click.prevent="setPage(numFix(totalPages))" 
            >{{ totalPages }}</a>
        </li>
      </ul>
    </nav>
  </div>
    `,
};


const vMultipleSelect = {
  components: {
    'v-text' : vText,
    'v-open-modal' : vOpenModal,
    'v-button-default': vButtonDefault,
  },
  setup(props) {
    // a simple `name` field with basic required validator]
    const { ref , onMounted } = Vue;
    const { value, errorMessage , meta , validate } = VeeValidate.useField(
    Vue.toRef(props, 'name'),
    Vue.toRef(props, 'rules') ,
    {label : props.label });

    const valid = meta.valid;
    const validated = meta.validated;
    const refBadges = ref([]);

    const multiSelectModal = ref();

    const setBadges = (el) => {
      if (el) {
        refBadges.value.push(el)
      }
    };
    
    const isSelected = (v) =>
    {
      let result = null;
      value.value.forEach(function(i){
        if(i == v){
          result = i;
        }
      });
      return (result != null);
    };

    
    const findOptionOfValue = (v) =>
    {
      let result = "";
      props.options.forEach(function(i){
        if(i.value == v){
          result = i;
        }
      });
      return result;
    };

    const selectedRemove = (v) =>
    {
      value.value.some(function(d, i){
          if (d == v) value.value.splice(i,1);
      });
    };
    
    const propSelected = (v) =>
    {
      if(isSelected(v)){
        selectedRemove(v);
      } else {
        value.value.push(v);
      }
    };

    const open = () =>
    {
      multiSelectModal.value.open();
    };

    const close = () =>
    {
      multiSelectModal.value.close();
    }

    const isRequired = () => {
      return props.rules.required;
    }


    return {
      open,
      close,
      multiSelectModal,
      isRequired,
      propSelected,
      value,
      meta,
      findOptionOfValue,
      selectedRemove,
      successClassName: ['text-gray-700', 'border-gray-300'],
      errorClassName: ['text-red-500', 'border-red-500'],
      errorMessage,
      isSelected,
    };
  },
  props: {
    options: { 
        type: Array, 
        required: true 
    },
    name: { 
        type: String, 
        required: true 
    },
    label: {
        type: String, 
        required: false,
        default: "", 
    },
    rules: {
        type: Object, 
        required: false, 
        default: {}
    },
    title: {
        type: String, 
        required: false,
        default: "", 
    },
    id: {
        type: String, 
        required: false,
        default: "", 
    }
  },
  mounted() {
  },
  beforeDestroy() {
    window.removeEventListener('click', this._onBlurHandler);
  },
  methods: {
  },
  template: `
  <v-text :title="title" :isRequired="isRequired()">
    <div class="relative appearance-none border w-full py-2 px-3 mr-2 mb-2 text-gray-700 leading-tight border-gray-300 h-full cursor-pointer bg-white" @click="open">
      <span v-if="value.length == 0"> 選択されていません </span>
      <span v-else v-for="v in value" class="bg-gray-100 text-gray-800 text-md font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-2 mb-4">
        {{ findOptionOfValue(v).label }}
      </span>
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
        </svg>
      </div>
    </div>
    <v-open-modal ref="multiSelectModal" :id="id" headtext="複数選択">
      <div class="flex flex-col" style="max-height: 68vh;">
        <div class="overflow-y-scroll my-6">
          <template v-for="(option, index) in options" >
            <div v-on:click="propSelected(option.value)" class="px-4 py-2 cursor-pointer" :class="{ 'hover:bg-gray-200 ' : (! isSelected(option.value) ) , 'bg-sushi-300 ' : isSelected(option.value)  }">
              <template v-if="isSelected(option.value)">
                <div 
                  class="h-4 w-4 bg-sushi-500 border border-sushi-200 rounded-sm focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="stroke-sushi-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </div> 
              </template>
              <template v-else>
                <div
                  class="h-4 w-4 border border-gray-300 rounded-sm bg-white focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2">
                </div>
              </template>
              <label class="form-check-label inline-block text-gray-800" for="flexCheckDefault">
                {{ option.label }}
              </label>
            </div>
          </template>
        </div>
        <div class="mx-auto lg:w-2/3 mb-4 text-center flex items-center gap-6 justify-center">
          <v-button-default type="button" @click.native="close">閉じる</v-button-default>
        </div>
      </div>
    </v-open-modal>
  </v-text>
    ` 
};

const vSwitch = 
{
  setup(props){
    const { ref } = Vue;
    const isCheck = ref(props.modelValue); 

    return {
      isCheck,
    };
  },
  emits: ['update:modelValue'],
  props: {
    modelValue: Boolean,
    message : String,
    id: String
  },
  watch: {
    isCheck(val)
    {
      this.$emit('update:modelValue', val);
    }
  },
  template: `
  <label 
    :for="id"
    class="flex items-center cursor-pointer"
  >
    <!-- toggle -->
    <div class="relative">
      <!-- input -->
      <input :id="id" type="checkbox" class="sr-only" v-model="isCheck" />
      <!-- line -->
      <div class="block bg-gray-400 w-14 h-8 rounded-full" :class="{'bg-sushi-400': isCheck}"></div>
      <!-- dot -->
      <div class="absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition"
      :class="{'translate-x-full': isCheck}"></div>
    </div>
    <!-- label -->
    <div class="ml-3 text-gray-700 font-medium">
    {{ message }}
    </div>
  </label>
  `

};
