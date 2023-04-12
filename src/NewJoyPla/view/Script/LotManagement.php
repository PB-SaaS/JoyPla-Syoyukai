<script>
$(function(){
	let nowDate = new Date(<?php echo date('Y'); ?>,  <?php echo date(
    'm'
); ?> - 1, <?php echo date('d'); ?>, "00", "00", "00");
	let week2 = new Date(<?php echo date(
     'Y',
     strtotime('+2 week')
 ); ?>,  <?php echo date('m', strtotime('+2 week')); ?> - 1, <?php echo date(
     'd',
     strtotime('+2 week')
 ); ?>, "00", "00", "00");
	let month3 = new Date(<?php echo date(
     'Y',
     strtotime('+3 month')
 ); ?>,  <?php echo date('m', strtotime('+3 month')); ?> - 1, <?php echo date(
     'd',
     strtotime('+3 month')
 ); ?>, "00", "00", "00");

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
	const divisitonData = <?php echo json_encode($division); ?>;
	$(function(){
		makeDivisionSelect();
	})

	function createCustomDropdown(element, data , name , defaultValue = '' , callback) {
		const dropdown = document.createElement('div');
		dropdown.className = 'uk-inline uk-width-1-1';
		element.appendChild(dropdown);

		const input = document.createElement('input');
		input.className = 'uk-input';
		input.type = 'text';
		input.placeholder = '部署を選択してください';
		input.readOnly = true;
		input.name = name;
		dropdown.appendChild(input);

		const dropdownList = document.createElement('div');
		dropdownList.className = 'uk-dropdown uk-width-1-1 uk-padding-remove';
		dropdownList.setAttribute('uk-dropdown', 'mode: click; pos: bottom-justify');
		dropdown.appendChild(dropdownList);

		const searchInput = document.createElement('input');
		searchInput.type = 'text';
		searchInput.placeholder = '部署を検索...';
		searchInput.className = 'uk-input';
		dropdownList.appendChild(searchInput);

		const list = document.createElement('ul');
		list.className = 'uk-nav uk-dropdown-nav';
		dropdownList.appendChild(list);

		data.forEach(function (item) {
			const listItem = document.createElement('li');
			listItem.className = "uk-padding-small uk-link hover-bg-gray-200 uk-transition-toggle";
			listItem.addEventListener('click', function (event) {
				event.preventDefault();
				input.value = item.text;
				callback ? callback(item.id) : null;
			});
			list.appendChild(listItem);

			if(defaultValue === item.id){
				input.value = item.text;
				callback ? callback(item.id) : null;
			}

			const listItemLink = document.createElement('a');
			listItemLink.className = "uk-text-emphasis";
			listItemLink.href = '#';
			listItemLink.textContent = item.text;
			listItem.appendChild(listItemLink);
		});

		searchInput.addEventListener('input', function () {
			const filter = searchInput.value.toUpperCase();
			const listItems = list.getElementsByTagName('li');

			for (let i = 0; i < listItems.length; i++) {
				const txtValue = listItems[i].textContent;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					listItems[i].style.display = 'block';
				} else {
					listItems[i].style.display = 'none';
				}
			}
		});
	}

	function makeDivisionSelect(){
		const divisionDiv = document.querySelector("div#division_id_div");
		const defaultValue = document.querySelector("input#search_division_id").value;
		const divisionOptions = [{
			id: '',
			text: '----- 部署を選択してください -----'
		}];
		
		Object.keys(divisitonData).forEach(function (key) {
			divisionOptions.push({
				id: divisitonData[key].divisionId,
				text: divisitonData[key].divisionName,
			});
		});
		createCustomDropdown(divisionDiv, divisionOptions , '' , defaultValue , function(value){ $('#search_division_id').val(value) });
	}

</script>