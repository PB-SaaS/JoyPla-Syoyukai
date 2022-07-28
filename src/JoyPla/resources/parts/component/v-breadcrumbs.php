const vBreadcrumbs = {
  props: {
    items: {
        default: [],
        type: Array
    },
  },
  computed: {
  },
  template: `
    <div class="bg-gray-100 p-2 mb-4 border-b-2 border-slate-200 border-solid">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="%url/rel:mpgt:Root%&" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-gray-700" v-if="items.length > 0">
                    Top
                </a>
                <span class="text-sm font-medium text-gray-700 " v-if="items.length === 0">Top</span>
            </li>
            <template v-for="(l,k) in items">
                <li>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                        <a :href="l.href" class="ml-1 text-sm font-medium text-gray-400 hover:text-gray-700 "  v-if="!l.disabled">{{ l.text }}</a>
                        <span class="ml-1 text-sm font-medium text-gray-700 "  v-if="l.disabled">{{ l.text }}</span>
                    </div>
                </li>
            </template>
        </ol>
    </div>
    ` 
};
