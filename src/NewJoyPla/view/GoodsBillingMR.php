<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/GoodsBillingMonthlyReport.php';
include_once 'NewJoyPla/api/GetDivision.php';
include_once 'NewJoyPla/lib/Func.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
$userInfo = new App\Lib\UserInfo($SPIRAL);

$goodsBillingMonthlyReport = new App\Api\GoodsBillingMonthlyReport($spiralDataBase,$userInfo);

$startMonth = date('Y-m-01');
$endMonth = '';
$divisionId = '';
$page = 1;
$limit = 100;
$itemName = '';
$itemCode = '';
$itemStandard = '';

if(isset($_POST['startMonth'])){
	$startMonth = App\Lib\html($_POST['startMonth']);
}

if(isset($_POST['endMonth'])){
	$endMonth = App\Lib\html($_POST['endMonth']);
}

if($userInfo->getUserPermission() == '1'){
	if(isset($_POST['divisionId'])){
		$divisionId = App\Lib\html($_POST['divisionId']);
	}
} else {
	$divisionId = $userInfo->getDivisionId();
}

if(isset($_POST['page'])){
	$page = App\Lib\html($_POST['page']);
}

if(isset($_POST['limit'])){
	$limit = App\Lib\html($_POST['limit']);
}

if(isset($_POST['itemName'])){
	$itemName = App\Lib\html($_POST['itemName']);
}
if(isset($_POST['itemCode'])){
	$itemCode = App\Lib\html($_POST['itemCode']);
}
if(isset($_POST['itemStandard'])){
	$itemStandard = App\Lib\html($_POST['itemStandard']);
}


$result = $goodsBillingMonthlyReport->dataSelect($startMonth,$endMonth,$divisionId,$itemName,$itemCode,$itemStandard,$page,$limit);

$getDivision = new App\Api\GetDivision($spiralDataBase,$userInfo);

$divisionData = $getDivision->select();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
	<?php include_once 'NewJoyPla/src/Head.php'; ?>
    <title>JoyPla 月次レポート【消費】</title>
  </head>
  <body>
   
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
	    		<ul class="uk-breadcrumb">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>月次レポート【消費】</span></li>
				</ul>
		    	<div class="no_print uk-margin">
					<input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
					<input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="listDl();return false;">
				</div>
		    	<h2 class="page_title uk-margin-remove">月次レポート【消費】</h2>
		    	<hr>
				<div class="uk-width-1-1 uk-margin-auto">
					<form class="uk-form-stacked" name="myform" action="%url/rel:mpgt:goodsBillingMR%" method="post" onsubmit="return submitCheck()">
						<div class="uk-width-3-4@m uk-margin-auto">
							<h3>検索</h3>
							<div class="uk-form-controls uk-margin">
								<label class="uk-form-label">
							      日付
							    </label>
							    <div class="uk-child-width-1-2@m" uk-grid>
							    	<div>
							    		<div>
											<input type="date" class="uk-input uk-width-4-5" name="startMonth" value="<?php echo $startMonth ?>">
											<span class="uk-width-1-5'">から</span>
							    		</div>
							    	</div>
							    	<div>
							    		<div>
											<input type="date" class="uk-input uk-width-4-5" name="endMonth" value="<?php echo $endMonth ?>">
											<span class="uk-width-1-5'">まで</span>
							    		</div>
							    	</div>
							    </div>
							</div>
							<?php if($userInfo->getUserPermission() == '1'): ?>
							<div class="uk-form-controls uk-margin">
								<label class="uk-form-label">
							      部署
							    </label>
							    <div class="uk-child-width-1-1">
							    	<div>
							    		<select name="divisionId" class="uk-select">
							    			<option value="">----- 選択してください -----</option>
											<?php 
											$stringDom = '';
											if($divisionData['store']){
												$selected = '';
												if($divisionId == $divisionData['store'][0][1]){
													$selected = 'selected';
												}
												$stringDom .= "<option value=\"".$divisionData['store'][0][1]."\" ". $selected .">".$divisionData['store'][0][3]."</option>";
											}
											foreach($divisionData['division'] as $record){
												$selected = '';
												if($divisionId == $record[1]){
													$selected = 'selected';
												}
												$stringDom .= "<option value=\"".$record[1]."\" ".$selected." >".$record[3]."</option>";
											}
											echo $stringDom; 
											?>
							    		</select>
							    	</div>
							    </div>
							</div>
							<?php else: ?>
							<div class="uk-form-controls uk-margin">
								<label class="uk-form-label">
							      部署
							    </label>
							    <div class="uk-child-width-1-1">
							    	<div>
										<?php 
										if($divisionData['store'][0][1] == $divisionId){
											echo $divisionData['store'][0][3];
										} else { 
											foreach($divisionData['division'] as $record){
							    				if($record[1] != $divisionId){ continue; } 
							    				echo $record[3] ;
											}
							    		} ?>
							    	</div>
							    </div>
							</div>
							<?php endif ?>
							<div class="uk-form-controls uk-margin">
								<label class="uk-form-label">
							      商品名
							    </label>
							    <div class="uk-child-width-1-1">
							    	<div>
										<input type="text" class="uk-input" name="itemName" value="<?php echo $itemName ?>">
							    	</div>
							    </div>
							</div>
							
							<div class="uk-form-controls uk-margin">
								<label class="uk-form-label">
							      製品コード
							    </label>
							    <div class="uk-child-width-1-1">
							    	<div>
										<input type="text" class="uk-input" name="itemCode" value="<?php echo $itemCode ?>">
							    	</div>
							    </div>
							</div>
							
							<div class="uk-form-controls uk-margin">
								<label class="uk-form-label">
							      規格
							    </label>
							    <div class="uk-child-width-1-1">
							    	<div>
										<input type="text" class="uk-input" name="itemStandard" value="<?php echo $itemStandard ?>">
							    	</div>
							    </div>
							</div>
							
							<div class="uk-text-center">
								<input class="uk-margin-top uk-button uk-button-default" type="submit" value="検索">
							</div>
						</div>
						<div>
							<table class="uk-table uk-width-1-2@m uk-width-1-4@m uk-table-divider">
			    				<tbody>
				    				<tr class="uk-text-large">
				    					<td>合計金額</td>
				    					<td class="uk-text-right">￥<script>price(fixed('<?php echo $result['totalAmount'] ?>'))</script> -</td>
				    				</tr>
			    				</tbody>
			    			</table>
						</div>
						<?php \App\Lib\pager($page, $result['count'],$limit); ?>
						<div>
							<div class="uk-width-1-3@m">
								<span class="smp-offset-start"><?php echo($limit * ($page - 1) )+ 1 ?></span> - <span class="smp-offset-end"><?php echo($limit * $page > $result['count'])? $result['count'] : $limit * $page ?></span>件 / <span class="smp-count"><?php echo $result['count'] ?></span>件
							
							</div>
							<div class="uk-width-1-3@m" uk-grid>
								<div class="uk-width-2-3">
									<select name="limit" class=" uk-select">
										<option value="10" <?php echo($limit == '10')? 'selected' : '' ?>>10件</option>
										<option value="50" <?php echo($limit == '50')? 'selected' : '' ?>>50件</option>
										<option value="100" <?php echo($limit == '100')? 'selected' : '' ?>>100件</option></select></div>
								<div class="uk-width-1-3">
									<input type="submit" name="smp-table-submit-button" class="uk-button uk-button-default" value="表示">
								</div>
							</div>
							<div class="uk-overflow-auto">
							<table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap">
								<thead>
									<tr>
										<th>No</th>
										<th>院内商品ID</th>
										<th>メーカー</th>
										<th>商品名</th>
										<th>製品コード</th>
										<th>規格</th>
										<th>購買価格</th>
										<th>入数</th>
										<th>消費数</th>
										<th>合計金額</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									if($result['count'] > 0){
										foreach($result['data'] as $record){
											echo "<tr>";
											echo "<td>".$record['id'] ."</td>";
											echo "<td>".$record['inHospitalItemId']."</td>".PHP_EOL;
											echo "<td>".$record['makerName'] ."</td>";
											echo "<td>".$record['itemName'] ."</td>";
											echo "<td>".$record['itemCode'] ."</td>";
											echo "<td>".$record['itemStandard'] ."</td>";
											//echo "<td>￥<script>price(fixed('".$record['minPrice'] ."'))</script></td>";
											echo "<td>";
											foreach($record['price'] as $price){
												echo "￥<script>price(fixed('".$price."'))</script><br>".PHP_EOL;
											}
											echo "</td>".PHP_EOL;
											echo "<td>";
											foreach($record['quantity'] as $key => $quantity){
												echo $quantity . $record['quantityUnit'][$key]."<br>".PHP_EOL;
											}
											echo "</td>".PHP_EOL;
											echo "<td>";
											foreach($record['billingQuantity'] as $key => $payoutQuantity){
												echo $payoutQuantity . $record['quantityUnit'][$key]."<br>".PHP_EOL;
											}
											echo "</td>".PHP_EOL;
											echo "<td>";
											foreach($record['totalAmount'] as $totalAmount){
												echo "￥<script>price(fixed('".$totalAmount."'))</script><br>".PHP_EOL;
											}
											echo "</td>".PHP_EOL;
											echo "</tr>";
										}
									} ?>
								</tbody>
							</table>
							</div>
						</div>
						<input name="page" value="1" type="hidden">
						<?php \App\Lib\pager($page, $result['count'],$limit); ?>
					</form>
				</div>
    		</div>
		</div>
     </div>
     <script>
	function submitCheck(){
		if($('input[name="startMonth"]').val() == ""){
			UIkit.modal.alert('日付検索の開始日は必須です');
			return false;
		}
		return true;
	}
	
	function pageSubmit(page){
		$('input[name="page"]').val(page);
		document.myform.submit();
	}
     
	function exportCSV(records) {
		let remakeArray = new Array();

		k = 0;
		remakeArray[k] = records[0];
		for( let i = 1; i < records.length; i++ ) {
			for( let j = 0; j < records[i][6].length; j++ ){
				k = k + 1;
				remakeArray[k] = new Array();
				remakeArray[k][0] = records[i][0];
				remakeArray[k][1] = records[i][1];
				remakeArray[k][2] = records[i][2];
				remakeArray[k][3] = records[i][3];
				remakeArray[k][4] = records[i][4];
				remakeArray[k][5] = records[i][5];
				remakeArray[k][6] = records[i][6][j];
				remakeArray[k][7] = records[i][7][j];
				remakeArray[k][8] = records[i][8][j];
				remakeArray[k][9] = records[i][9][j];
				remakeArray[k][10] = records[i][10][j];
			}
		}
		let data = remakeArray.map((record)=>record.join('\t')).join('\r\n');
		
		let bom  = new Uint8Array([0xEF, 0xBB, 0xBF]);
		let blob = new Blob([bom, data], {type: 'text/tab-separated-values'});
		let url = (window.URL || window.webkitURL).createObjectURL(blob);
		let link = document.createElement('a');
		link.download = 'ConsumeMonthlyReport_<?php echo date('Ymd') ?>.tsv';
		link.href = url;
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link);
	};
	
	function listDl(){
		let records = <?php echo json_encode($result['data'])?>;
		let result = [];
		for(let i = 0 ; i < records.length ; i++){
			result[i] = [];
			Object.keys(records[i]).forEach(function (key) {
				result[i].push(records[i][key]);
			});
		}
		
		result.unshift(['id','inHospitalItemId','makerName','itemName','itemCode','itemStandard','price','quantity','quantityUnit','billingQuantity','totalAmount']);
	
		exportCSV(result);
	}
     </script>
  </body>
</html>
