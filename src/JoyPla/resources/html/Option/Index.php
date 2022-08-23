<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto bg-white">
        <h1 class="text-2xl mb-2">オプション</h1>
        <hr>
		<div class=" whitespace-pre-line p-4 text-base bg-gray-100 border border-gray-400 ">
			<v-text title="テナント種別" class="flex w-full gap-6">
				<span v-if="option.tenantKind == '1'">シングル</span>
				<span v-else="option.tenantKind != '1'">マルチ</span>
			</v-text>
			<v-text title="入庫先設定" class="flex w-full gap-6">
				<span v-if="option.receivingTarget == '1'">大倉庫</span>
				<span v-else="option.receivingTarget != '1'">発注部署</span>
			</v-text>
			<v-text title="登録可能ユーザー数" class="flex w-full gap-6">
				<span>{{option.registerableNum}}人まで</span>
			</v-text>
			<v-text title="消費計算方法" class="flex w-full gap-6">
				<span v-if="option.billingUnitPrice == '1'">単価フィールドを使用する</span>
				<span v-else="option.billingUnitPrice != '1'">購買価格を使用する</span>
			</v-text>
			<v-text title="払出計算方法" class="flex w-full gap-6">
				<span v-if="option.payoutUnitPrice == '1'">単価フィールドを使用する</span>
				<span v-else="option.payoutUnitPrice != '1'">購買価格を使用する</span>
			</v-text>
			<v-text title="棚卸計算方法" class="flex w-full gap-6">
				<span v-if="option.invUnitPrice == '1'">単価フィールドを使用する</span>
				<span v-else="option.invUnitPrice != '1'">購買価格を使用する</span>
			</v-text>
		</div>
      </div>
    </div>
  </div>
</div>
<script>
const PHPData = <?php echo json_encode($viewModel, true) ?>;
var JoyPlaApp = Vue.createApp({
    components: {
      'v-loading' : vLoading,
      'card-button' : cardButton,
      'v-breadcrumbs': vBreadcrumbs,
      'v-button-default': vButtonDefault,
      'v-button-primary': vButtonPrimary,
      'v-button-danger': vButtonDanger,
      'header-navi' : headerNavi,
      'v-open-modal': vOpenModal,
      'v-input' : vInput ,
      'item-view': itemView,
      'v-pagination' : vPagination,
      'v-select' : vSelect,
      'v-text' : vText,
    },
    setup(){
      const { ref , onMounted } = Vue;
      const { useForm } = VeeValidate;

	  const option = PHPData;

      const loading = ref(false);
      const start = () => {
          loading.value = true;
      }

      const complete = () => {
          loading.value = false;
      }

      const breadcrumbs = [
          {
            text: 'オプション',
            disabled: true, 
          }
        ];

      return {
		loading,
		option,
        breadcrumbs,
      }
  },
  watch: {
    'values.currentPage': function(val) {
      this.listGet();
      window.scrollTo(0, 0);
    }
  }
}).mount('#top');
</script>