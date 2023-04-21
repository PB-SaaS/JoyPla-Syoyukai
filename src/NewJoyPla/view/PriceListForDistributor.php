<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once 'NewJoyPla/lib/Define.php';
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/lib/Func.php';
include_once 'NewJoyPla/api/GetDistributor.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase(
    $SPIRAL,
    $spiralApiCommunicator,
    $spiralApiRequest
);
$userInfo = new App\Lib\UserInfo($SPIRAL);

$getDistributor = new App\Api\GetDistributor($spiralDataBase, $userInfo);

$distributorData = $getDistributor->getDistributor();

$myPageID = '';
if (isset($_POST['MyPageID']) && $_POST['MyPageID'] != '') {
    $myPageID = $_POST['MyPageID'];
}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <title>JoyPla 金額情報一覧</title>
	<?php include_once 'NewJoyPla/src/Head.php'; ?>
</head>
<body>
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
		            <li><a href="%url/rel:mpg:top%&path=product">商品メニュー</a></li>
				    <li><span>金額情報一覧</span></li>
				</ul>
                        <div class="no_print uk-margin">
				  <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
				  <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click()">
				</div>
                        <h2 class="page_title uk-margin-remove">金額情報一覧</h2>
                        <hr>
		    	<div class="uk-margin" id="tablearea">
					%sf:usr:search16%
		    	</div>
			</div>
		</div>
	</div>
</body>

	<script>
   const distributorData = <?php echo json_encode($distributorData['data']); ?>;
   $(function(){
      makeDistributorSelect();
   })

   function makeDistributorSelect(){
		const distributorDiv = document.querySelector("div#distributorIdDiv");
		const defaultValue = document.querySelector("input#distributorId").value;
		const distributorOptions = [{
			id: '',
			text: '----- 部署を選択してください -----'
		}];
		
		Object.keys(distributorData).forEach(function (key) {
			distributorOptions.push({
				id: distributorData[key].distributorId,
				text: distributorData[key].distributorName,
			});
		});
		createCustomDropdown(distributorDiv, distributorOptions , '' , defaultValue , function(value){ $('#distributorId').val(value) });
	}

	function createCustomDropdown(element, data , name , defaultValue = '' , callback) {
		const dropdown = document.createElement('div');
		dropdown.className = 'uk-inline uk-width-1-1';
		element.appendChild(dropdown);

		const input = document.createElement('input');
		input.className = 'uk-input';
		input.type = 'text';
		input.placeholder = '卸業者を選択してください';
		input.readOnly = true;
		input.name = name;
		dropdown.appendChild(input);

		const dropdownList = document.createElement('div');
		dropdownList.className = 'uk-dropdown uk-width-1-1 uk-padding-remove';
		dropdownList.setAttribute('uk-dropdown', 'mode: click; pos: bottom-justify');
		dropdown.appendChild(dropdownList);

		const searchInput = document.createElement('input');
		searchInput.type = 'text';
		searchInput.placeholder = '卸業者を検索...';
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
	
	</script>
</html>
