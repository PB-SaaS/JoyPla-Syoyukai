/**
 * ëŒè€ÇÃÉtÉHÅ[ÉÄÇÕ name = searchForm Ç≈Ç†ÇÈÇ±Ç∆
 */
Vue.component('pagination', {
    props:{
        current_page: Number,
        total_rec: Number,
        limit: Number,
        show_nav: Number,
    },
    computed:{
        pages: function() {
            let max = Math.ceil(this.total_rec / this.limit);
            let start = this.current_page - Math.floor(this.show_nav / 2);
            if(start < 1)
            {
                start = 1;
            }

            let end = this.current_page + Math.floor(this.show_nav / 2);
            
            if(end > max)
            {
                end = max;
            }
            let arr = Array.from((function*(){for(let i = start; i <= end; i++) yield i})());
            if(arr.indexOf(1) === -1){ arr.push(1) };
            if(arr.indexOf(max) === -1){ arr.push(max) };
            return arr.sort((a, b) => a - b);
        },
        toObject: function() {
            let pages = this.pages;
            let max = Math.ceil(this.total_rec / this.limit);
            let arr = new Array();
            let attr = { 'startOf': 0 , 'endOf': 0 };
            let three_dot = false;
            for (let i = 1, p = 0; i <= max; ++i) 
            {
                if(pages[p] === i)
                {
                    if(three_dot)
                    {
                        attr.endOf = i;
                        arr.push({
                            'text':'...',
                            'current':false,
                            'startOf': attr.startOf,
                            'endOf': attr.endOf,
                            'disabled' : true,
                        });
                        three_dot = false;
                        attr.startOf = 0;
                        attr.endOf = 0;
                    }
                    arr.push({
                        'text':pages[p],
                        'current':(this.current_page == pages[p]),
                        'disabled' : false,
                    });
                    ++p;
                }
                else if(!three_dot)
                {
                    three_dot = true;
                    attr.startOf = i;
                }
            }
            return arr;
        }
    },
    methods:{
        page_submit: function(link){
            var q = document.createElement('input');
            q.type = 'hidden';
            q.name = 'page';
            q.value = link;
            document.searchForm.appendChild(q);
            document.searchForm.submit();
        }
    },
    template: `
        <ul class="uk-pagination">
            <template v-if="toObject.length" >
                <li v-for="li in toObject" :class="{ 'uk-disabled' : li.disabled , 'uk-active' : li.current }">
                    <template v-if="li.current">
                        <span>{{ li.text }}</span>
                    </template>
                    <template v-else>
                        <a v-on:click="page_submit(li.text)" href="javascript:void(0)">{{ li.text }}</a>
                    </template>
                </li>
            </template>
            <template v-else >
                <li>
                    <span>1</span>
                </li>
            </template>
        </ul>
        `
});