const vLoading = {
  props: {
    text: {
        default: 'ロード中...',
        type: String
    },
    show: {
        default: true,
        type: Boolean
    }
  },
  computed: {
  },
  template: `
  <teleport to="body">
    <div v-if="show">
      <div class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-sushi-50 opacity-75 flex flex-col items-center justify-center">
        <div class="flex justify-center m-8">
          <div class="animate-ping h-8 w-8 bg-sushi-600 rounded-full"></div>
          <div class="animate-ping h-8 w-8 bg-sushi-600 rounded-full mx-8"></div>
          <div class="animate-ping h-8 w-8 bg-sushi-600 rounded-full"></div>
        </div>
        <span>{{ text }}</span>
      </div>
    </div>
</teleport>
    ` 
};
