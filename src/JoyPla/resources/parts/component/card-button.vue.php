<script>
Vue.component('card-button', {
  props: {
    'labelText': String,
    'labelSubText' : String,
    'mainColor': String,
    'subColor': String,
    'textColor': String,
    'url': String,
  },
  computed: {
  },
  template: `
      <div class="w-100 md:my-0 my-5 cursor-pointer relative">
        <div v-bind:class="[mainColor,textColor]" class="px-3 pt-6 pb-4" >
          <div class="relative leading-3">
            <span class="text-xl font-bold">{{ labelText }}</span><br>
            <span class="text-sm">{{ labelSubText }}</span>
            <div class="absolute w-14 -top-1 right-0">
              <slot></slot>
            </div>
          </div>
        </div>
        <div v-bind:class="[subColor,textColor]" class="text-center text-sm">More Info</div>
        <a class="absolute top-0 left-0 w-100 h-100 block" :href="url" style="width:100%; height:100%"></a>
      </div>
    ` 
});
</script>
