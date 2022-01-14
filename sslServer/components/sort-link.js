/**
 * �Ώۂ̃t�H�[���� name = searchForm �ł��邱��
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
        sort_submit: function(link){
            
            var q = document.createElement('input');
            q.type = 'hidden';
            q.name = 'sortTitle';
            q.value = this.title;

            var s = document.createElement('input');
            s.type = 'hidden';
            s.name = 'sort';
            s.value = 'asc';
            if(this.asc === 'true' && this.title === this.current_title)
            {
                s.value = 'desc';
            }
            document.searchForm.appendChild(q);
            document.searchForm.appendChild(s);
            document.searchForm.submit();
        }
    },
    template: `
        <a href="javascript:void(0)" v-on:click="sort_submit">
            <slot></slot>
            <template v-if="(asc === 'true' && title === current_title)">��</template>
            <template v-else-if="(asc === 'false' && title === current_title)">��</template>
        </a>
        `
});