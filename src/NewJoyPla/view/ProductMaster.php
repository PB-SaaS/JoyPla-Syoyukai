
<div class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><span>商品マスタ</span></li>
            </ul>
            <div class="no_print uk-margin">
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="item_mst.export()">
            </div>
            <h2 class="page_title uk-margin-remove">商品マスタ</h2>
            <hr>
            <div class="uk-margin" id="tablearea">
                %sf:usr:search31%
            </div>
        </div>
    </div>
</div>
<script>
class ItemMst
{
    export()
    {
        $("#exportButton").click();
    }
}
let item_mst = new ItemMst();
</script>