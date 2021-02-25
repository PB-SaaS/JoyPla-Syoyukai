<!DOCTYPE html>
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/GetHospitalData.php';


$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
$userInfo = new App\Lib\UserInfo($SPIRAL);

$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$result = $getHospitalData->select();
?>
<html>
  <head>
    <title>JoyPla 契約情報確認</title>
	<?php include_once 'NewJoyPla/src/Head.php'; ?>
</head>
<body>
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>契約情報確認</span></li>
				</ul>
				
				
				
		    	<div class='uk-width-1-1' uk-grid>
		    		<div class="uk-width-5-6@m uk-width-2-3">
		    			<h2>契約情報確認</h2>
					</div>
		    	</div>
	    		<p class="uk-text-warning">契約情報の変更はお問合せよりご連絡ください。</p>
		    	<div class='uk-width-4-5@m uk-margin-auto uk-margin-remove-top' >
		    		<table class="uk-table uk-table-divider uk-table-responsive">
					        <tr>
					            <th>登録日時</th>
					            <td><?php echo $result['data'][0]['registrationTime'] ?></td>
					            <th>更新日時</th>
					            <td colspan="3"><?php echo $result['data'][0]['updateTime'] ?></td>
					        </tr>
					        <tr>
					            <th>契約病院名</th>
					            <td colspan="5"><?php echo $result['data'][0]['hospitalName'] ?></td>
					        </tr>
					        <tr>
					            <th>郵便番号</th>
					            <td colspan="5"><?php echo $result['data'][0]['postalCode'] ?></td>
					        </tr>
					        <tr>
					            <th>都道府県</th>
					            <td><?php echo $result['data'][0]['prefectures'] ?></td>
					            <th>住所</th>
					            <td colspan="3"><?php echo $result['data'][0]['address'] ?></td>
					        </tr>
					        <tr>
					            <th>電話番号</th>
					            <td><?php echo $result['data'][0]['phoneNumber'] ?></td>
					            <th>FAX番号</th>
					            <td colspan="3"><?php echo $result['data'][0]['faxNumber'] ?></td>
					        </tr>
					        <tr>
					            <th>責任者氏名</th>
					            <td><?php echo $result['data'][0]['name'] ?></td>
					            <th>責任者氏名（カナ）</th>
					            <td colspan="3"><?php echo $result['data'][0]['nameKana'] ?></td>
					        </tr>
					        <tr>
					            <th>メールアドレス</th>
					            <td><?php echo $result['data'][0]['mailAddress'] ?></td>
					            <th>責任者連絡先</th>
					            <td><?php echo $result['data'][0]['contactAddress'] ?></td>
					        </tr>
					</table>
		    	</div>
			</div>
		</div>
	</div>
</body>
</html>