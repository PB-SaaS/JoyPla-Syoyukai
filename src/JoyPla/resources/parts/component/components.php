const cardButton = {
    setup(props) {
        const openPage = () => {
            if(props.baseUrl)
            {
              location.href = props.baseUrl + "&path=" + props.path;
            } 
            else 
            {
              location.href = _ROOT + "&path=" + props.path;
            }
        }

        return {openPage}
    },
    props: {
        'labelText': String,
        'mainColor': String,
        'subColor': String,
        'textColor': String,
        'path': String,
        'baseUrl': String,
    },
    computed: {},
    template: `
      <div class="w-100 md:my-0 my-5 cursor-pointer relative" @click="openPage">
        <div v-bind:class="[mainColor,textColor]" class="px-3 pt-6 pb-4" >
          <div class="relative leading-3">
            <div class="h-10 flex items-center">
              <span class="text-xl font-bold">{{ labelText }}</span>
            </div>
            <div class="absolute w-14 -top-2 right-0">
              <slot></slot>
            </div>
          </div>
        </div>
        <div v-bind:class="[subColor,textColor]" class="text-center text-sm">More Info</div>
      </div>
    `
};

const list = {
    setup() {
        const {reactive} = Vue;
        const lists = reactive([
            {
                name: 'ダッシュボード',
                icon: 'TemplateIcon',
                link: '/'
            }, {
                name: 'EC',
                icon: 'ShoppingCartIcon',
                link: '/#',
                sublists: [
                    {
                        name: '商品一覧',
                        link: '/#'
                    }, {
                        name: '注文一覧',
                        link: '/#'
                    }
                ]
            }
        ]);
        const toggle = (name) => {
            const list = lists.find((list) => list.name === name);
            list.show = !list.show;
        };
        return {lists, toggle}
    },
    props: {},
    computed: {},
    template: `
    <ul class="text-gray-700">
      <li class="mb-1" v-for="list in lists" :key="list.name">
        <a
          v-if="!list.sublists"
          :href="list.link"
          class="
            flex
            items-center
            p-2
            rounded-sm
            hover:text-white hover:bg-blue-400
          "
        >
          <span>{{ list.name }}</span>
        </a>
        <div
          v-else
          class="
            flex
            items-center
            justify-between
            p-2
            cursor-pointer
            rounded-sm
            hover:bg-blue-400 hover:text-white
          "
          @click="toggle(list.name)"
        >
          <div class="flex items-center">
            <span>{{ list.name }}</span>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" 
            class="w-4 h-4 transform duration-300"
            :class="!list.show ? 'rotate-0' : '-rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
          </svg>
        </div>
        <ul class="mt-1" v-show="list.show">
          <li class="mb-1" v-for="list in list.sublists" :key="list.name">
            <a
              :href="list.link"
              class="block p-2 rounded-sm hover:bg-blue-400 hover:text-white"
            >
              <span class="pl-8">{{ list.name }}</span>
            </a>
          </li>
        </ul>
      </li>
    </ul>
    `
};
const sidebar = {
    components: {
        list: list
    },
    setup() {},
    props: {},
    computed: {},
    template: `
  <div class="p-4">
    <a :href="_ROOT" class="block w-36 mx-auto my-4">
        <img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png">
    </a>
    <list />
  </div>
    `
};

<?php
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

global $SPIRAL;

$hospital = ModelRepository::getHospitalInstance()
    ->where('hospitalId', $SPIRAL->getContextByFieldTitle('hospitalId'))
    ->get();
$hospital = $hospital->first();
$top_api_url = '%url/rel:mpgt:page_262241%';
$api_url = '%url/rel:mpgt:Notification%';

$permission = [
    1 => '管理者',
    2 => '担当者',
    3 => '承認者',
];

$permissionText =
    $permission[$SPIRAL->getContextByFieldTitle('userPermission')];
?>

const headerNavi = {
    components: {
        sidebar: sidebar
    },
    setup() {
        const {ref , onMounted , onBeforeUnmount} = Vue;
        const innerWidth = ref(window.innerWidth);
        const show = ref(false);

        const hospitalName = "<?php echo html($hospital->hospitalName); ?>";
        const userName = "%val:usr:name%";
        const notificationView = ref(false);
        const userModalView = ref(false);
        const supportView = ref(false);
        const notifications = ref([]);
        const count = ref(0);
        const badge = ref(false);

        const notification = async () =>
        {
            let params = new URLSearchParams();
            params.append("_csrf", _CSRF);

            const res = await axios.post('<?php echo $api_url; ?>',params);
            //if(res.data.code != 200) {
            if(res.data.code != 0) {
              throw new Error(res.data.message)
            }
            
            notifications.value = res.data.data;
            count.value = res.data.count;
            badge.value = (res.data.count > 0);

            return true ;
        }
        
        const userModal = ref(null); // 対象の要素
        const supportModal = ref(null); // 対象の要素
        const notificationModal = ref(null); // 対象の要素

        const clickOutside = (e) => {
          // [対象の要素]が[クリックされた要素]を含まない場合
          if (e.target instanceof Node && !notificationModal.value?.contains(e.target)) {
            notificationView.value = false;
          }
          // [対象の要素]が[クリックされた要素]を含まない場合
          if (e.target instanceof Node && !userModal.value?.contains(e.target)) {
            userModalView.value = false;
          }

          if (e.target instanceof Node && !supportModal.value?.contains(e.target)) {
            supportView.value = false;
          }
        }

        onBeforeUnmount(() => {
          removeEventListener('click', clickOutside)
        })
        
        onMounted(() => {
          notification();
          addEventListener('click', clickOutside);
        });
        
        return {
          notificationModal,
          notificationView,
          userModal,
          userModalView,
          supportModal,
          supportView,
          show,
          userName,
          hospitalName,
          notifications,
          count,
          badge
        }
    },
    props: {
        sidemenu: {
            type: Boolean,
            required: false,
            default: true
        }
    },
    computed: {},
    template: `
    <!-- 
    <div class="relative" v-if="sidemenu === true">
      <div
        class="fixed top-0 w-64 h-screen bg-white z-20 transform"
        :class="{ '-translate-x-full': !show }"
      >
        <sidebar />
      </div>
      <div
        class="fixed inset-0 bg-gray-900 opacity-50 z-10"
        @click="show = !show"
        v-show="show"
      ></div>
    </div>
    <div id="header-navi">
        <nav class="bg-white px-2 sm:px-4 py-2 border-b-2 border-sushi-500 w-full z-10">
            <div class="flex flex-wrap justify-between items-center mx-auto h-11">
                <div class="flex">
                    <div v-if="sidemenu === true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" @click="show = !show">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </div>
                    <a :href="_ROOT" class="items-center block w-28 mx-4">
                        <img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png">
                    </a>
                </div>
            </div>
        </nav>
    </div>
  -->
<nav id="nav" class="flex relative" style="border-bottom: solid 2px #98CB00;">
	<div class="flex-wrap flex items-center"> 
		<a :href="_ROOT" class="text-2xl text-[#333333] decoration-0 flex justify-center items-center gap-1 box-border min-h-[80px] px-[15px]">
			<img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
		</a>
	</div>
	<div class=" mr-[20px] ml-auto flex" style="flex-wrap: nowrap;">
		<p class="sm:block hidden my-auto mr-[20px] text-right">
			{{ hospitalName }}<br>
			{{ userName }} 様
		</p>
		<ul class="flex m-0 p-0 list-none" ref="supportModal" >
			<li class="">
				<a href="#" title="ヘルプ" v-on:click="supportView = !supportView" class="flex justify-center items-center gap-x-1 box-border px-[15px] text-sm decoration-0 min-h-[80px]">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-[30px] w-[30px] text-[#999]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </a>
				<div class="md:right-[20px] md:left-auto right-0 left-0 top-[80px] md:w-[400px] block p-0 mt-[15px] absolute z-50 box-border text-[#666] bg-white" style="box-shadow: 0 5px 12px rgb(0 0 0 / 15%);" v-if="supportView">
            <div class="max-h-[450px] flow-root md:p-[40px] p-[30px]">
              <ul class="text-[0.875rem] p-0 list-none mb-0 text-[#999]" style='font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";'>
                <li>
                    <a href="https://support.joypla.jp/" class="flex items-center" target="support">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-[30px] w-[30px] mr-[10px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>サポートサイト（外部サイト）</span>
                    </a>
                </li> 
                <li class="mt-6">
                    <a href="https://reg34.smp.ne.jp/regist/is?SMPFORM=meoj-lirdmf-2830358d2ea157fb8a38fdead8ace8c9" class="flex items-center" target="support">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-[30px] w-[30px] mr-[10px] " fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      <span>サポート依頼（外部サイト）</span>
                    </a>
                </li>
              </ul>
				    </div>
          </div>
			</li>
			<li class="inline-block max-w-full align-middle" ref="notificationModal">
				<a href="#" title="お知らせ" v-on:click="notificationView = !notificationView" class="flex justify-center items-center gap-x-1 box-border px-[15px] text-sm decoration-0 min-h-[80px] relative">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-[30px] w-[30px] text-[#999]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <div v-show="badge">
            <span class=" box-border min-w-[18px] h-[18px] rounded-[500px] py-0 px-[5px] align-middle bg-[#1e87f0] text-white text-[11px] inline-flex justify-center items-center " style="line-height: 0; position: absolute;top: 13px;right: 0px;" v-text='count'></span>
          </div>
        </a>
				<div class="md:right-[20px] md:left-auto right-0 left-0 top-[80px] md:w-[400px] block p-0 mt-[15px] absolute z-50 box-border text-[#666] bg-white" style="box-shadow: 0 5px 12px rgb(0 0 0 / 15%);" v-if="notificationView">
            <div class="max-h-[450px] flow-root md:p-[40px] p-[30px]" style="overflow-y: scroll; padding: 15px">
				        <ul class="p-0 list-none" v-if="notifications.length > 0 ">
				            <li v-for="notification in notifications">
                      <article style="padding:10px">
								        <header class="mb-[20px] mt-[20px] flow-root">
                          <div class="mb-0 items-center ml-[-30px] flex flex-wrap m-0 p-0 list-none">
                              <div class="w-auto ml-[30px]">
                                  <img :src="notification.icon" width="40" height="40" alt="">
                              </div>
                              <div class="flex-1 min-w-[1px] box-border w-full max-w-full pl-[30px] m-0 flow-root break-words">
                                <p v-html="notification.message"></p>
                              </div>
                              <div class="">
                                  <h4 class="m-0 text-[1.25rem] ml-[30px]" style="line-height: 1.4">
                                    <a :href="notification.link">移動</a>
                                  </h4>
                              </div>
                          </div>
								      </header>
								    </article>
                  </li>
					    	</ul>
                <p v-else>最新の通知はありません</p>
				    </div>
          </div>
			</li>
			<li ref="userModal" class="">
				<a href="#" v-on:click="userModalView = !userModalView" class="flex justify-center items-center gap-x-1 box-border px-[15px] text-sm decoration-0 min-h-[80px]">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-[30px] w-[30px] text-[#999]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
          </svg>
        </a>
				<div class="md:right-[20px] md:left-auto right-0 left-0 md:w-[400px] block p-0 mt-[15px] absolute z-50 box-border text-[#666] bg-white" style="box-shadow: 0 5px 12px rgb(0 0 0 / 15%);" v-if="userModalView">
            <div class="max-h-[450px] flow-root md:p-[40px] p-[30px]">
				        <div class="items-center ml-[-30px] flex flex-wrap m-0 p-0 list-none">
				            <div class="flex-1 min-w-[1px] box-border w-full max-w-full pl-[30px] m-0 flow-root break-words">
				                <h3 class="mb-0 text-[1.5rem]" style="line-height: 1.4">
                        {{ hospitalName }}<br>
                        </h3>
                        <span class="text-white py-0 p-[15px] inline-block px-[10px] text-[0.875rem] align-middle whitespace-nowrap rounded-sm tra" style="line-height: 1.5; background-color: #7AAE36;  text-transform: uppercase"><?php echo $permissionText; ?></span>
                        <p class="mt-[20px]" >{{ userName }} 様</p>
				            </div>
				        </div>
				        <ul class="text-[0.875rem] p-0 list-none mt-[20px] mb-0">
                  <li>
                    <a :href="_ROOT" class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-[30px] w-[30px] mr-[10px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                      </svg>
                      <div>TOPへ戻る</div>
                    </a>
                  </li>
                  <li class="mt-[20px]">
                    <a href="#" onclick="document.userInfoChange_nav.submit();" class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-[30px] w-[30px] mr-[10px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                      ユーザー情報変更
                    </a>
                  </li>
                  <li class="mt-[20px]">
                    <a href="%form:act:logout%"  class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-[30px] w-[30px] mr-[10px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                      </svg>
                        ログアウト
                      </a>
                  </li>
                </ul>
				    </div>
          </div> 
			</li>
		</ul>
      <form method="post" action="%url/rel:mpgt:page_262241%" name="userInfoChange_nav">
        <input type="hidden" name="Action" value="userInfoChange">
      </form>
	    <form method="post" action="%url/rel:mpgt:page_262241%" name="contactUs">
			<input type="hidden" name="Action" value="contactUs">
		</form>
	</div>
</nav>
    `
};

const itemView = {
    setup() {
        const signatures = {
            JVBERi0: "application/pdf",
            R0lGODdh: "image/gif",
            R0lGODlh: "image/gif",
            iVBORw0KGgo: "image/png",
            "/9j/": "image/jpg"
        };

        const detectMimeType = (b64) => {
            if (!b64) {
                return false;
            }
            for (var s in signatures) {
                if (b64.indexOf(s) === 0) {
                    return 'data:' + signatures[s] + ';base64,' + b64;
                }
            }
            return false;
        }

        return {detectMimeType}
    },
    props: {
        base64: String
    },
    template: `
    <div class="flex-initial bg-cover text-center overflow-hidden bg-gray-100 place-content-center" >
      <template v-if="detectMimeType(base64)">
        <img class="object-contain h-full mx-auto" :src="detectMimeType(base64)" />
      </template>
      <template v-else>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-full w-10 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
      </template>
    </div>
    `
};

const blowing = {
    component: {
        'v-alert': vAlert
    },
    setup(props) {
        const info = () => {
            Swal.fire({
                title: props.title,
                html: '<p class="text-left whitespace-pre">' + props.message + '</p>',
                icon: 'info',
                confirmButtonText: 'OK'
            })
        }
        return {info}
    },
    props: {
        message: String,
        title: String
    },
    template: `
  <span>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block cursor-pointer" v-on:click="info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  </span>
    `

};
