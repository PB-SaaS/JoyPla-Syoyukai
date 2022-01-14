/**
 * ‘ÎÛ‚ÌƒtƒH[ƒ€‚Í name = searchForm ‚Å‚ ‚é‚±‚Æ
 */
Vue.component('table-offset', {
    props:{
        current_page: Number,
        total_rec: Number,
        limit: Number,
        show_nav: Number,
    },
    computed:{
    },
    methods:{
    },
    template: `
        <div class="uk-width-1-3@m">
            <span class="smp-offset-start">
                <span v-if="total_rec > 0">{{ (limit * (current_page - 1)) + 1 }}</span>
                <span v-else>0</span>
                - 
            </span>
            <span class="smp-offset-end">
                <span v-if="((limit * current_page) > total_rec)">{{ total_rec }}</span>
                <span v-else>{{ limit * current_page }}</span>
            </span>
            Œ / 
            <span class="smp-count">{{ total_rec }}</span>Œ
        </div>
        `
});