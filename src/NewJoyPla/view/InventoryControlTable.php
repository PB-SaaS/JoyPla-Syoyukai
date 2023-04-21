<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print uk-margin-top">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                	<li><a href="%url/rel:mpg:top%&path=stock">在庫メニュー</a></li>
                <li><span>在庫管理表</span></li>
            </ul>
            <div class="no_print uk-margin" uk-margin>
                <input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
                <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click()">
            </div>
            <h2 class="page_title uk-margin">在庫管理表</h2>
            <hr>
            <div class="" id="tablearea">
                %sf:usr:search60:mstfilter%
            </div>
        </div>
    </div>
</div>

<div id="modal-label" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">払出ラベル発行</h2>
        </div>
        <form action="%url/rel:mpgt:createLabel%" target="_blank" method="post" class="uk-form-horizontal" name="createLabelForm">
            <div class="uk-modal-body">
                <div class="uk-margin">
                    <div class="uk-form-label">卸業者名</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span class="modal-distributorName"></span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <label class="uk-form-label">払出元部署</label>
                    <div class="uk-form-controls sourceDivision">
						<input name="sourceDivision" type="hidden">
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">部署名</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span class="modal-divisionName"></span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">部署棚名</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span class="modal-divisionRackNumber"></span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">部署別定数</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span class="modal-constantByDiv"></span><span class="modal-quantityUnit"></span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <label class="uk-form-label">入数指定</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" type="number" step="1" value="" min="0" name="quantity">
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <label class="uk-form-label">個数（印刷枚数）</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" type="number" step="1" value="" min="0" name="printCount">
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">JANコード</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span class="modal-itemJANCode"></span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">メーカー名</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span class="modal-makerName"></span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">カタログNo</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span class="modal-catalogNo"></span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">商品名</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span class="modal-itemName"></span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">製品コード</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span class="modal-itemCode"></span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">規格</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span class="modal-itemStandard"></span>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-default uk-modal-close" type="button">閉じる</button>
                <input class="print_hidden uk-button uk-button-primary" type="button" value="ラベル発行" onclick="return createLabel()">
                <input type="hidden" value="" name="itemsData" id="itemsData">
                <input type="hidden" value="" name="inHospitalItemId">
                <input type="hidden" value="payout" name="pattern">
            </div>
        </form>
    </div>
</div>

   <script>
	const divisitonData = <?php echo json_encode($division); ?>;
	$(function(){
		$('p.stock_barcode').each(function(i, o){
			let num = $(o).text();
			$(o).html("<svg id='barcode_"+i+"'></svg>");
			generateBarcode("barcode_"+i,num);
		});
		makeDivisionSelect();
		makeSourceDivisionSelect();
	})
	
	function makeSourceDivisionSelect() {
		const sourceDivisionDiv = document.querySelector("div.sourceDivision");
		const divisionOptions = [{
			id: '',
			text: '----- 部署を選択してください -----'
		}];;
		
		Object.keys(divisitonData).forEach(function (key) {
		    divisionOptions.push({
				id: divisitonData[key].divisionId,
				text: divisitonData[key].divisionName,
			});
		});
		createCustomDropdown(sourceDivisionDiv, divisionOptions , 'sourceDivisionLabel' , '' , function(value , label){ $('input[name="sourceDivision"]').val(value) });
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

	
	function makeDivisionSelect() {
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

	let itemsToJs = {};

	function createModalOpen(elm,inHospitalItemId){
		let Tr = $(elm.parentNode.parentNode);
		let objectForLabel = JSON.parse($('.json',Tr).text().replace(/\r?\n/g, '').trim());
		itemsToJs = objectForLabel;
		
		$('input[name="printCount"]').removeClass('uk-form-danger');
		$('input[name="quantity"]').removeClass('uk-form-danger');

		$('input[name="inHospitalItemId"]').val(inHospitalItemId);

		$('span.modal-distributorName').text(itemsToJs[inHospitalItemId].distributorName);
		$('span.modal-divisionName').text(itemsToJs[inHospitalItemId].divisionName);
		$('span.modal-divisionRackNumber').text(itemsToJs[inHospitalItemId].rackName);

		$('input[name="quantity"]').val(itemsToJs[inHospitalItemId].quantity);
		$('input[name="printCount"]').val(1);
		
		$('span.modal-constantByDiv').text(itemsToJs[inHospitalItemId].constantByDiv);
		$('span.modal-quantityUnit').text(itemsToJs[inHospitalItemId].unit);
		
		$('span.modal-itemJANCode').text(itemsToJs[inHospitalItemId].jan);
		$('span.modal-makerName').text(itemsToJs[inHospitalItemId].maker);
		$('span.modal-itemName').text(itemsToJs[inHospitalItemId].itemName);
		$('span.modal-itemCode').text(itemsToJs[inHospitalItemId].code);
		$('span.modal-itemStandard').text(itemsToJs[inHospitalItemId].itemStandard);
		$('span.modal-catalogNo').text(itemsToJs[inHospitalItemId].catalogNo);
		
		UIkit.modal($('#modal-label')).show();
	}

	function createLabel(){
		if(!setVal()){
			UIkit.modal.alert('入力値に不正があります。<br>ご確認ください').then(function () {
				UIkit.modal($('#modal-label')).show();
			});
			
			return false;
		}
		let inHospitalItemId = $('input[name="inHospitalItemId"]').val();
		itemsToJs[inHospitalItemId].printCount = $('input[name="printCount"]').val();
		itemsToJs[inHospitalItemId].quantity = $('input[name="quantity"]').val();
		
		$("#itemsData").val(JSON.stringify( objectValueToURIencode(itemsToJs) ));

		sourceDivision = $('input[name="sourceDivision"]').val();
		flag = true;
		if(sourceDivision == ''){
			UIkit.modal.confirm("払出元部署が選択されていません。<br>元部署印字なしで出力しますか").then(function () {
				document.createLabelForm.submit();
				flag = true;
			},function(){
				UIkit.modal($('#modal-label')).show();
				flag = false;
			});
		} else {
			document.createLabelForm.submit();
		}
		return flag;
	}
	
	function setVal(){
		$('input[name="printCount"]').removeClass('uk-form-danger');
		$('input[name="quantity"]').removeClass('uk-form-danger');
		
		let flg = true;
		if($('input[name="printCount"]').val() == '' || $('input[name="printCount"]').val() < 1 ){
			$('input[name="printCount"]').addClass('uk-form-danger');
			flg = false;
		}
		if($('input[name="quantity"]').val() == '' || $('input[name="quantity"]').val() < 1 ){
			$('input[name="quantity"]').addClass('uk-form-danger');
			flg = false;
		}
		return flg;
	}
   </script>