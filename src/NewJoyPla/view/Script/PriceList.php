
<script>
class PriceList
{
    constructor()
    {
        this.makeDistributorSelect();
        this.onchangeSelect();
        this.switchLink();
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

    switchLink()
    {
        let userPermission = %val:usr:userPermission:id%;
        let tenantKind = <?php echo $tenantKind; ?>;
        if((userPermission === 1 || userPermission === 2) && tenantKind === 1){
            $("a.smp-cell-id").css("display", "inline");
        }else{
            $("a.smp-cell-id").css("display", "none");
        }
    }
}

let distributor = JSON.parse('<?php echo json_encode($distributor); ?>');

let price_list = new PriceList();

</script>