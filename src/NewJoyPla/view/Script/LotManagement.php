<script>
$(function(){
	let nowDate = new Date(<?php echo date("Y") ?>,  <?php echo date("m") ?> - 1, <?php echo date("d") ?>, "00", "00", "00");
	let week2 = new Date(<?php echo date("Y",strtotime("+2 week")) ?>,  <?php echo date("m",strtotime("+2 week")) ?> - 1, <?php echo date("d",strtotime("+2 week")) ?>, "00", "00", "00");
	let month3 = new Date(<?php echo date("Y",strtotime("+3 month")) ?>,  <?php echo date("m",strtotime("+3 month")) ?> - 1, <?php echo date("d",strtotime("+3 month")) ?>, "00", "00", "00");

	for(let i = 0; i < $("#tablearea .stockQuantity").length ; i++){
		str = $("#tablearea .stockQuantity")[i].innerText;
		if(str == ""){
			continue;
		}
		if(str < 0)
		{
			$("#tablearea .stockQuantity")[i].classList.add( "uk-text-danger" );
			$("#tablearea .stockQuantity")[i].classList.add( "uk-text-bold" );
			$("#tablearea .stockQuantityUnit")[i].classList.add( "uk-text-danger" );
			$("#tablearea .stockQuantityUnit")[i].classList.add( "uk-text-bold" );
		}
	}
	for(let i = 0; i < $("#tablearea .lotDate").length ; i++){
		str = $("#tablearea .lotDate")[i].innerText;
		if(str == ""){
			continue;
		}
		result = str.split("年");
		result2 = result[1].split("月");
		result3 = result2[1].split("日");
		
		Y = result[0];
		m = result2[0];
		d = result3[0];
		
		lotDate = new Date(Y,  parseInt(m) - 1, d, "00", "00", "00");
		if(nowDate.getTime() <= lotDate.getTime() && month3.getTime() >= lotDate.getTime()){
			$("#tablearea .lotDate")[i].style = "color: pink";
			$("#tablearea .lotDate")[i].classList.add( "uk-text-bold" );
		}
		if(nowDate.getTime() <= lotDate.getTime() && week2.getTime() >= lotDate.getTime()){
			$("#tablearea .lotDate")[i].classList.add( "uk-text-warning" );
			$("#tablearea .lotDate")[i].classList.add( "uk-text-bold" );
		}
		if(nowDate.getTime() > lotDate.getTime()){
			$("#tablearea .lotDate")[i].classList.add( "uk-text-danger" );
			$("#tablearea .lotDate")[i].classList.add( "uk-text-bold" );
		}
	}
});
</script>

<script>
const divison_data = <?php echo json_encode($division); ?>;
$(function(){
	makeDivisionSelect();
})

function makeDivisionSelect(){
   	let selectval = $("#search_division_id").val();
	//html = "<input type="number" class="uk-input" style="width:72px" step="10">";
    html = document.createElement("div");
    select = document.createElement("select");
    select.className = "uk-select";
    select.onchange  = function () {  
    	onchangeSelect(this.value);
    };
    
    option = document.createElement("option");
    option.value = "";
    option.text = " --- 部署を選択してください ---";
	select.appendChild(option);
    
	Object.keys(divison_data).forEach(function (key) {
	    option = document.createElement("option");
	    option.value = divison_data[key]['divisionId'];
	    option.text = divison_data[key]['divisionName'];
	    //input.step = listObject[object.recordId].irisu;
	    //<span class="uk-text-bottom">個</span>
	    
	    if(divison_data[key]['divisionId'] == selectval){
	    	option.selected = "selected";
	    }
		select.appendChild(option);
	});

	html.appendChild(select);
	
	$("#division_id_div").append(html);
}

function onchangeSelect(val){
   $("#search_division_id").val(val);
}
</script>