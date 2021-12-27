<script>
    
	const divisitonData = <?php echo json_encode($division); ?>;
	$(function(){
		makeDivitionSelect();
	})

	function makeDivitionSelect(){
   		let selectval = $('#divisionId').val();
		//html = '<input type="number" class="uk-input" style="width:72px" step="10">';
	    html = document.createElement("div");
	    select = document.createElement("select");
	    select.className = 'uk-select';
	    select.onchange  = function () {  
	    	onchangeSelect(this.value);
	    };
	    
	    option = document.createElement("option");
	    option.value = '';
	    option.text = ' ----- 部署を選択してください -----';
	    //input.step = listObject[object.recordId].irisu;
	    //<span class="uk-text-bottom">個</span>
		select.appendChild(option);
	    
		Object.keys(divisitonData).forEach(function (key) {
		    
		    option = document.createElement("option");
		    option.value = divisitonData[key].divisionId;
		    option.text = divisitonData[key].divisionName;
		    //input.step = listObject[object.recordId].irisu;
		    //<span class="uk-text-bottom">個</span>
		    
		    if(divisitonData[key].divisionId == selectval){
		    	option.selected = 'selected';
		    }
			select.appendChild(option);
		});
	
		html.appendChild(select);
		
		$('#divisionIdDiv').append(html);
	}
   
	function onchangeSelect(val){
		$('#divisionId').val(val);
	}
	
</script>