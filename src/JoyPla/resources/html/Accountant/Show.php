<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <h1 class="text-2xl mb-2">会計伝票の詳細</h1>
        <hr>
        <div class="p-4 text-base bg-gray-100 border border-gray-400 flex flex-col md:flex-row md:gap-6 gap-4 mb-6">
          <?php if (true): ?>
          <v-button-danger type="button" class="md:w-1/6 w-full" >
            会計伝票を削除
          </v-button-danger>
          <?php endif; ?>
        </div>
        <div class="p-4 text-base bg-gray-100 border border-gray-400">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">会計日</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">会計番号</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">会計タイプ</div>
          </div>
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">会計部署</div>
          </div>
        </div>
        <hr>
        <div class="p-4 text-lg font-bold">
          <div class="flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-1/3">合計金額</div>
          </div>
        </div>
        <hr>
        <div class="p-4 text-base">
          <div class="lg:flex w-full gap-6">
            <div class="flex-initial lg:w-1/6 w-full text-lg font-bold">商品情報</div>
            <div class="flex-auto">
              
            </div>
          </div>
        </div>
        <hr>
      </div>
    </div>
  </div>
</div>
<script>

const accountantId = '<?php echo $accountantId; ?>';

var JoyPlaApp = Vue.createApp({ 
    components: {
      'v-loading' : vLoading,
      'header-navi' : headerNavi,
      'v-breadcrumbs': vBreadcrumbs,
      'item-view' : itemView,
      'v-button-default' : vButtonDefault,
      'v-button-danger' : vButtonDanger
    },
    setup(){
      const {ref , onCreated , onMounted} = Vue;
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
      
      onMounted( () => {
        sleepComplate()
        console.log(getSlipData(accountantId));
      });

      const breadcrumbs = [
        {
          text: '会計メニュー',
          disabled: false,
          href: _ROOT+'&path=/accountant',
        },
        {
          text: '会計一覧',
          disabled: false,
          href: _ROOT+'&path=/accountant/index&isCache=true',
        },
        {
          text: '会計伝票の詳細',
          disabled: true,
        }
      ];

      const getSlipData = async ( accountantId ) => 
      {
        start();
        
        let params = new URLSearchParams();
        params.append("path", "/api/accountant/"+accountantId);
        params.append("_method", 'get');
        params.append("_csrf", _CSRF);

        const res = await axios.post(_APIURL,params);
        
        complete();

        if(res.data.code != 200) {
          throw new Error(res.data.message)
        }
        
        return res.data.data ;
      }
      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };

      return {
        numberFormat,
        breadcrumbs,
        loading, 
        start, 
        complete
      }
  },
  watch: {
  }
}).mount('#top');
</script> 