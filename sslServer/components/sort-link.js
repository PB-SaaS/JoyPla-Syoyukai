/**
 * ëŒè€ÇÃÉtÉHÅ[ÉÄÇÕ name = searchForm Ç≈Ç†ÇÈÇ±Ç∆
 */
 Vue.component('sort-link', {
    props: {
        current_title: String,
        title: String,
        asc: String,
    },
    computed:{
    },
    
    methods:{
        sort_submit: function(){
            console.log(document.querySelector('input[name=sortTitle]'));
            if(document.querySelector('input[name=sortTitle]'))
            {
                var q = document.querySelector('input[name=sortTitle]');
                q.value = this.title;
            }
            else 
            {
                var q = document.createElement('input');
                q.type = 'hidden';
                q.name = 'sortTitle';
                q.value = this.title;
                document.searchForm.appendChild(q);
            }

            if(document.querySelector('input[name=sort]'))
            {
                var s = document.querySelector('input[name=sort]');
                s.value = 'asc';
                if(this.asc === 'true' && this.title === this.current_title)
                {
                    s.value = 'desc';
                }
            }
            else
            {
                var s = document.createElement('input');
                s.type = 'hidden';
                s.name = 'sort';
                s.value = 'asc';
                if(this.asc === 'true' && this.title === this.current_title)
                {
                    s.value = 'desc';
                }
                document.searchForm.appendChild(s);
            }
            document.searchForm.submit();
        }
    },
    template: `
        <a href="javascript:void(0)" v-on:click="sort_submit">
            <slot></slot>
            <template v-if="(asc === 'true' && title === current_title)">Å£</template>
            <template v-else-if="(asc === 'false' && title === current_title)">Å•</template>
        </a>
        `
});