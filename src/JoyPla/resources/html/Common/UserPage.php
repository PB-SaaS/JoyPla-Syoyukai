<div id="top" v-cloak>
  <v-loading :show="loading"></v-loading>
  <header-navi></header-navi>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <div id="content" class="flex h-full px-1">
    <div class="flex-auto">
      <div class="index container mx-auto">
        <div class="bg-san-juan-500 text-san-juan-50 py-1 font-bold col-span-4 px-3">ユーザー</div>
        <div class="md:grid md:grid-cols-4 md:gap-10 gap-4 my-2">
          <card-button main-color="bg-san-juan-500" text-color="text-san-juan-50" sub-color="bg-san-juan-600" label-text="ユーザー情報変更" path="" base-url="%url/rel:mpgt:page_262241%&Action=userInfoChange">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" class="stroke-san-juan-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <?php if (gate('list_of_users')->can()) : ?>
            <card-button main-color="bg-san-juan-500" text-color="text-san-juan-50" sub-color="bg-san-juan-600" label-text="ユーザー一覧" path="" base-url="%url/rel:mpgt:userManagement%">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" class="stroke-san-juan-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </card-button>
          <?php endif ?>
          <?php if (gate('list_of_divisions')->can()) : ?>
            <card-button main-color="bg-san-juan-500" text-color="text-san-juan-50" sub-color="bg-san-juan-600" label-text="部署一覧" path="" base-url="%url/rel:mpgt:userManagement%&Action=divisionList">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" class="stroke-san-juan-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </card-button>
          <?php endif ?>
          <card-button main-color="bg-san-juan-500" text-color="text-san-juan-50" sub-color="bg-san-juan-600" label-text="卸業者一覧" path="" base-url="%url/rel:mpgt:DistributorList%">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" class="stroke-san-juan-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </card-button>
          <?php if (gate('contract_confirm')->can()) : ?>
            <card-button main-color="bg-san-juan-500" text-color="text-san-juan-50" sub-color="bg-san-juan-600" label-text="契約情報" path="" base-url="%url/rel:mpgt:page_262241%&Action=contractConfirm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" class="stroke-san-juan-700" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </card-button>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  var JoyPlaApp = Vue.createApp({
    setup() {
      const {
        ref,
        onCreated,
        onMounted
      } = Vue;
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

      return {
        loading,
        start,
        complete
      }
    },
    components: {
      'v-loading': vLoading,
      'card-button': cardButton,
      'v-breadcrumbs': vBreadcrumbs,
      'header-navi': headerNavi
    },
    data() {
      return {
        breadcrumbs: [{
          text: 'ユーザーメニュー',
          disabled: true,
        }, ],
      }
    },
  }).mount('#top');
</script>