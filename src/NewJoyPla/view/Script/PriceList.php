
<script>
class PriceList
{
    constructor()
    {
        this.makeDistributorSelect();
        this.onchangeSelect();
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