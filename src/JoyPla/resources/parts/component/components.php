const cardButton = {
  setup(props){
    const openPage = () => {
      location.href = _ROOT + "&path=" + props.path;
    }

    return {
      openPage,
    }
  },
  props: {
    'labelText': String,
    'mainColor': String,
    'subColor': String,
    'textColor': String,
    'path': String,
  },
  computed: {
  },
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
    setup(){
      const { reactive } = Vue;
      const lists = reactive([
        {
          name: 'ダッシュボード',
          icon: 'TemplateIcon',
          link: '/',
        },
        {
          name: 'EC',
          icon: 'ShoppingCartIcon',
          link: '/#',
          sublists: [
            {
              name: '商品一覧',
              link: '/#',
            },
            {
              name: '注文一覧',
              link: '/#',
            },
          ],
        },
      ]);
      const toggle = (name) => {
        const list = lists.find((list) => list.name === name);
        list.show = !list.show;
      };
      return { lists , toggle } 
    },
    props: {
    },
    computed: {
    },
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
      list : list
    },
    setup(){
    },
    props: {
    },
    computed: {
    },
    template: `
  <div class="p-4">
    <a href="%url/rel:mpgt:Root%" class="block w-36 mx-auto my-4">
        <img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png">
    </a>
    <list />
  </div>
    ` 
};

const headerNavi = {
    components: {
      sidebar : sidebar
    },
    setup(){
      const { ref } = Vue;
      const innerWidth = ref(window.innerWidth);
      const show = ref(false);
      return { show }
    },
    props: {
    },
    computed: {
    },
    template: `
    <div class="relative">
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
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" @click="show = !show">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </div>
                    <a href="%url/rel:mpgt:Root%" class="items-center block w-28 mx-4">
                        <img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png">
                    </a>
                </div>
            </div>
        </nav>
    </div>
    ` 
};

const itemView = { 
    setup(){
      const signatures = {
        JVBERi0: "application/pdf",
        R0lGODdh: "image/gif",
        R0lGODlh: "image/gif",
        iVBORw0KGgo: "image/png",
        "/9j/": "image/jpg"
      };

      const detectMimeType = (b64) => {
        if(! b64 ) { return false; } 
        for (var s in signatures) {
          if (b64.indexOf(s) === 0) {
            return 'data:'+signatures[s]+';base64,'+b64;
          }
        }
        return false;
      }

      return {
        detectMimeType
      }
    },
    props: {
      base64 : String
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
      'v-alert' : vAlert
    },
    setup(props){
      const info = () => {
        Swal.fire({
          title: props.title,
          html:
          '<p class="text-left whitespace-pre">'+props.message+'</p>',
          icon: 'info',
          confirmButtonText: 'OK'
        })
      }
      return {
        info
      }
    },
    props: {
      message: String,
      title: String,
    },
    template: `
  <span>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block cursor-pointer" v-on:click="info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  </span>
    `

};
