<div class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><span>金額情報一覧</span></li>
            </ul>
            <div class="no_print uk-margin">
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="price_list.export()">
            </div>
            <h2 class="page_title uk-margin-remove">金額情報一覧</h2>
            <hr>
            <div class="uk-margin" id="tablearea">
                %sf:usr:search36%
            </div>
        </div>
    </div>
</div>

<script>
class PriceList
{
    constructor()
    {
        this.makeDistributorSelect();
        this.onchangeSelect();
    }

    export()
    {
        $("#exportButton").click();
    }

    makeDistributorSelect()
    {
        let selectval = $("#distributorId").val();
        let html = document.createElement("div");
        let select = document.createElement("select");
        select.className = "uk-select";
        select.id = "distributor";
      
        let option = document.createElement("option");
        option.value = "";
        option.text = " --- 卸業者を選択してください ---";
        select.appendChild(option);
      
        for (var key in distributor) {
            option = document.createElement("option");
            option.value = distributor[key][1];
            option.text = distributor[key][0];
            if (distributor[key][1] == selectval) {
                option.selected = "selected";
            }
            select.appendChild(option);
        };
  
        html.appendChild(select);
    
        $("#distributorIdDiv").append(html);
    }
   
    onchangeSelect()
    {
        $(document).on('change', '#distributor', function() {
            let val = $(this).val();
            $("#distributorId").val(val);
        });
    }
}

let distributor = JSON.parse('<?php echo json_encode($distributor); ?>');

let price_list = new PriceList();

</script>