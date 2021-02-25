<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
	<?php include_once 'NewJoyPla/src/Head.php'; ?>
    <title>JoyPla 一括院内商品情報登録</title>

     <script>
	 let canAjax = true;
     const column = [["商品ID:itemId","卸業者ID:distributorId","カタログNo:catalogNo","シリアルNo:serialNo","入数:quantity","個数単位:itemUnit","保険請求分類（医科）:medicineCategory","保険請求分類（在宅）:homeCategory","不使用フラグ:notUsedFlag","備考:notice","旧JoyPlaラベルId:labelId"]];
     let filesData = {};
		function exportCSV(records) {
			let data = records.map((record)=>record.join('\t')).join('\r\n');
			
			let bom  = new Uint8Array([0xEF, 0xBB, 0xBF]);
			let blob = new Blob([bom, data], {type: 'text/tab-separated-values'});
			let url = (window.URL || window.webkitURL).createObjectURL(blob);
			let link = document.createElement('a');
			link.download = 'header_inHospital_iteminsert.tsv';
			link.href = url;
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		};
     	// CSVをパース
		function parseCsv(csvStr, delimiter) {
		    var rowRegex = /(?:(?:"[^"]*")*[^\r\n"]*)+/g,
		        colRegex = new RegExp('(?:(?:"[^"]*")*[^' + delimiter + '"]*)+', 'g'),
		        rows = [],
		        row, cells, cell, rowMaches, colMaches;
		    //行を切り出す
		    while ((rowMaches = rowRegex.exec(csvStr)) !== null) {
		        if (rowMaches[0] !== '') {
		            cells = [];
		            row = rowMaches[0];
		            //セルを切り出す
		            while ((colMaches = colRegex.exec(row)) !== null) {
		                cell = colMaches[0].replace(/^\s+|\s+$/g, '');
		                if (cell.charAt(0) == '"' && cell.charAt(cell.length - 1) == '"') {
		                    cell = cell.slice(1, -1);
		                }
		                cell = cell.replace(/""/g, '"');
		                cells.push(cell);
		                colRegex.lastIndex++; //一歩前へ！
		            }
		            rows.push(cells);
		        }
		        rowRegex.lastIndex++; //一歩前へ！
		    }
		    return rows;
		}
		
		function checkfile(){
		    $('.resultarea').hide();
		    //FileReaderのインスタンスを作成する
		    var reader = new FileReader();
		  
		    //読み込んだファイルの中身を取得する
		    reader.readAsText( $('input[type="file"]')[0].files[0] );
		  
		    //ファイルの中身を取得後に処理を行う
		    reader.addEventListener( 'load', function() {
		       filesData = parseCsv(reader.result, "\t");
		       if(!headerCheck(filesData[0],column[0])){
				   return false;
			   }
		       
		       convertArrayForHeader(column);
		       filesData.shift();
		       convertArray(filesData);
		       $('.resultarea').show();
		    })
		}
		
		function headerCheck(head,columns){
			if(columns.length != head.length){
				UIkit.modal.alert("ヘッダーが一致しません");
				return false;
			}
			let flag = true;
			for(let i = 0 ; i < columns.length; i++){
				
				if(head[i] != columns[i]){
					flag = false;
				}
			}
			
			if(! flag){
				UIkit.modal.alert("ヘッダーが正しくありません");
				return false;
			}
			return true;
		}
		
		function insert(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			UIkit.modal.confirm("取り込みを開始します").then(function () {
				data = {};
				for(let i = 0 ; i < filesData.length ; i++){
					if(i >= 1000){
						break;
					}
					data[i] = filesData[i];
				}
        		

				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
					url:'%url/rel:mpgt:regInHPitemsBluk%',
					type:'POST',
					data:{
						insertData : JSON.stringify( objectValueToURIencode(data) )
					},
					dataType: 'json'
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
					if(! data.result){
						UIkit.modal.alert("取り込みに失敗しました<br>error:"+data.response.code+"<br>message:"+data.response.message).then(function(){
							location.reload();
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
					UIkit.modal.alert("取り込みが完了しました").then(function(){
						location.reload();
						canAjax = true; // 再びAjaxできるようにする
					});
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
					UIkit.modal.alert("取り込みに失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
				})
				// Ajaxリクエストが成功・失敗どちらでも発動
				.always( (data) => {
					loading_remove();
					
				});
			}, function () {
				UIkit.modal.alert('中止しました');
			});
		}
		function convertArrayForHeader(dataArray) {
			let insertElement = '';
			dataArray.forEach((element) => {
				insertElement += '<tr>';
				element.forEach((childElement) => {
					insertElement += `<th>${childElement}</th>`
				});
				insertElement += '</tr>';
			});
			$("#output_head").html(insertElement) ;
		}
		
		function convertArray(dataArray) {
			let insertElement = '';
			dataArray.forEach((element) => {
				insertElement += '<tr>';
				element.forEach((childElement) => {
					insertElement += `<td>${childElement}</td>`
				});
				insertElement += '</tr>';
			});
			$("#output_body").html(insertElement) ;
		}
     </script>
  </head>
  <body>
   
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
	    		<ul class="uk-breadcrumb">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>一括院内商品情報登録</span></li>
				</ul>
		    	<h2 class="page_title">一括院内商品情報登録</h2>
				<div class="uk-width-2-3@m uk-margin-auto">
					<form class="uk-form-horizontal uk-margin-large" name="myform">

					    <div class="uk-margin">
				        <label class="uk-form-label">登録用ヘッダー</label>
					        <div class="uk-form-controls">
						        <div uk-form-custom="target: true">
						            <button class="uk-button uk-button-secondary" type="button" onclick="exportCSV(column)">ダウンロード</button>
						        </div>
					        </div>
					        <hr>
					    </div>
					    <div class="uk-margin">
				        <label class="uk-form-label" for="file">ファイル選択</label>
					        <div class="uk-form-controls">
						        <div uk-form-custom="target: true">
						            <input type="file" accept=".tsv" name="fileinput">
						            <input class="uk-input uk-form-width-large" type="text" id="file" placeholder="TSVファイルを選択してください" disabled>
						        </div>
						        <button class="uk-button uk-button-secondary" type="button" onclick="checkfile()">解析</button><br>
					        	<span class="uk-text-danger">※一度に取り込める件数は1000件までです</span>
					        </div>
					        <hr>
					    </div>
					    
					    <div class="uk-margin uk-text-center">
					    	<input type="reset" value="リセット" class="uk-button uk-button-default">
					    </div>
					    
					</form>
				</div>
				<div class="uk-width-1-1 resultarea">
				    <h4>解析結果</h4>
					<input type="button" value="取込実行" class="uk-button uk-button-primary" onclick="insert()">
				    <div class="uk-overflow-auto">
				    <table class="uk-table uk-table-striped uk-text-nowrap uk-width-auto uk-margin-auto">
				     <tbody id="output_head"></tbody>
					 <tbody id="output_body"></tbody>
					</table>
					</div>
				</div>
    		</div>
		</div>
     </div>
  </body>
</html>