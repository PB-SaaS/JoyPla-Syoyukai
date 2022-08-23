<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto mb-96">
        <span>{{ (notification.registrationTime).split(' ')[0] }}</span><br>
        <div v-if="notification.type == '2'" class="flex-initia text-center text-xs font-semibold inline-block py-1 px-2 rounded text-white bg-eastern-blue-900 w-24">
            重要なお知らせ
        </div>
        <div v-else-if="notification.type == '3'" class="flex-initial text-center text-xs font-semibold inline-block py-1 px-2 rounded text-white bg-eastern-blue-900 w-24">
            メンテナンス
        </div>
        <div v-else class="flex-initial text-center text-xs font-semibold inline-block py-1 px-2 rounded text-white bg-eastern-blue-900 w-24">
            お知らせ
        </div>
        <h1 class="text-2xl mb-2">{{notification.title}}</h1>
        <hr>
        <div class=" whitespace-pre-line p-4 text-base bg-gray-100 border border-gray-400 ">{{ notification.content }}</div>
      </div>
    </div>
  </div>
</div>
<script>

const PHPData = <?php echo json_encode($viewModel, true) ?>;

var JoyPlaApp = Vue.createApp({ 
    components: {
      'v-text' : vText,
      'v-select' : vSelect,
      'v-button-danger' : vButtonDanger,
      'v-button-primary' : vButtonPrimary, 
      'v-checkbox': vCheckbox,
      'v-loading' : vLoading,
      'header-navi' : headerNavi,
      'v-breadcrumbs': vBreadcrumbs,
      'item-view' : itemView,
      'v-input-number': vInputNumber
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
      });

      const notification = PHPData;

      const breadcrumbs = [
          {
            text: 'お知らせ',
            disabled: false,
            href: _ROOT + '&path=/notification',
          },
          {
            text: notification.title,
            disabled: true,
          }
        ];

      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };
      return {
        notification,
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