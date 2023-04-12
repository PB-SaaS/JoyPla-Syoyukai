<script>
    
	const divisitonData = <?php echo json_encode($division); ?>;
	$(function(){
		makeDivisionSelect();
	})

	function makeDivisionSelect(){
		const divisionDiv = document.querySelector("div#divisionIdDiv");
		const defaultValue = document.querySelector("input#divisionId").value;
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
		createCustomDropdown(divisionDiv, divisionOptions , '' , defaultValue , function(value){ $('#divisionId').val(value) });
	}

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

	function onchangeSelect(val){
		$('#divisionId').val(val);
	}
	
</script>