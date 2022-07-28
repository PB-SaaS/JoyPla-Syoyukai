<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <h1 class="text-2xl mb-2">個別消費</h1>
        <hr>
        <div class="mb-2 lg:w-1/3">
          <label class="block text-gray-700 text-sm font-bold mb-1">
            消費日指定
          </label>
          <v-input
            name="date"
            v-model="text"
            type="date"
            format="yyyy/MM/dd"
            :error="false"
            errormsg=""
            ></v-input>
        </div>
        <div class="mb-2 lg:w-1/3">
          <label class="block text-gray-700 text-sm font-bold mb-1">
            消費部署指定
          </label>
          <v-select-division v-model="division.value" name="divisionId" />
        </div>
        <div class="my-4 grid grid-cols-3 gap-4 lg:w-1/3">
          <v-button-default type="button" class="col-span-2" @click.native="showContent = true">院内商品マスタから参照</v-button-default>
          <v-button-primary type="button" >消費登録</v-button-primary>
        </div>
        <div class="p-2 bg-gray-300">
          <fieldset class="relative">
            <v-input 
              type="text" 
              v-model="barcode" 
              name="barcode" 
              class=" pl-12" 
              :error="false"
              errormsg=""
              ></v-input>
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-2 text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div> 
          </fieldset>
        </div>
        <div class="mt-3">
          <div class="w-full lg:flex border border-sushi-600 bg-white mt-3">
            <div class="h-12 lg:h-auto lg:w-48 flex-initial bg-cover text-center overflow-hidden bg-sushi-300 grid place-content-center" >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </div>
            <div class="flex-1 p-4 relative">
              <div class="absolute top-2 right-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </div>
              <div class="flex-auto lg:flex justify-between leading-normal lg:space-y-0 space-y-4 lg:mt-0 mt-4 gap-6">
                <div class="flex-initial lg:w-2/4">
                メーカ名メーカ名メーカ名メーカ名メーカ名メーカ名<br>
                  商品名商品名商品名商品名商品名商品名商品名商品名<br>
                  規格規格規格規格規格規格規格規格規格規格規格規格<br>
                  コードコードコードコードコードコードコードコード<br>
                  JANJANJANJANJANJANJANJANJANJANJANJANJANJANJAN<br>
                </div>
                <div class="flex-initial lg:w-1/4">
                  <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">
                      ロット番号
                    </label>
                    <v-input
                      v-model="text"
                      type="text" 
                      name="lotNumber"
                      :error="false"
                      errormsg=""></v-input>
                  </div>
                  <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">
                      消費期限
                    </label>
                    <v-input
                      v-model="text"
                      type="date" 
                      name="lotDate"
                      :error="false"
                      errormsg=""></v-input>
                  </div>
                </div>
                <div class="flex-initial lg:w-1/4">
                  <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">
                      消費数（入数）
                    </label>
                    <v-input-number v-model.number="num" name="num" :min="0" :max="100" unit="枚" ></v-input-number>
                  </div>
                  <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">
                      消費数（個数）
                    </label>
                    <v-input-number v-model.number="num" name="num" :min="0" :max="100" unit="箱" :step="100" ></v-input-number>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script> 
var JoyPlaApp = Vue.createApp({
    components: {
      'v-breadcrumbs': vBreadcrumbs,
      'v-input': vInput,
      'v-select': vSelect,
      'v-select-division': vSelectDivision,
      'v-button-default': vButtonDefault,
      'v-button-primary': vButtonPrimary,
      'v-input-number': vInputNumber,
      'v-inhospitalitem-modal': vInHospitalItemModal,
      'header-navi' : headerNavi
    },
    data(){
      return {
        num: 0,
        barcode : "",
        text: "",
        showContent: false,
        division: {
          value : "",
        },
        breadcrumbs: [
          {
            text: '消費メニュー',
            disabled: false,
            href: '%url/rel:mpgt:Root%&path=/consumption',
          },
          {
            text: '個別消費',
            disabled: true, 
          },
      ]}
    }, 
    mounted(){
    },
    methods: {
      openModal: function(){
        this.showContent = true
      },    
      closeModal: function(){
        this.showContent = false
      },
      show : function() {
        this.$modal.show('modal-inHospitalItems');
      },
      hide: function() 
      {
        this.$modal.hide('modal-inHospitalItems');
      }
    }
}).mount('#top');
</script> 