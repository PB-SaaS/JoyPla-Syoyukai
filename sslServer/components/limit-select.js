/**
 * �Ώۂ̃t�H�[���� name = searchForm �ł��邱��
 */
Vue.component('limit-select', {
    props:{
        limit: Number,
        select: Array,
        attr: Object,
    },
    computed:{
    },
    methods:{
    },
    template: `
        <div uk-grid :class="attr">
            <div class="uk-width-2-3">
                <select name="limit" class="uk-select">
                    <option v-for="s in select" v-bind:selected="s === limit" v-bind:value="s">{{ s }}��</option>
                </select>
            </div>
            <div class="uk-width-1-3">
                <input type="submit" name="smp-table-submit-button" class="uk-button uk-button-default" value="�\��">
            </div>
        </div>
        `
});