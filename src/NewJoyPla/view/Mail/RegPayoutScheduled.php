<?php echo $name ?> 様

JoyPla からお知らせです。
払出予定商品の登録がされましたので、下記の通りお知らせします。

[払出予定商品情報]
<?php foreach($items as $item): ?> 
払出予定商品登録部署（払出先部署）: <?php echo $item['targetDivisionName'] ?> 
払出予定日: <?php echo explode(" ", $item['payoutPlanTime'])[0]; ?> 
メーカー: <?php echo $item['makerName'] ?> 
商品名: <?php echo $item['itemName'] ?> 
製品コード: <?php echo $item['itemCode'] ?> 
規格: <?php echo $item['itemStandard'] ?> 
JANコード: <?php echo $item['itemJANCode'] ?> 
払出数: <?php echo $item['payoutQuantity'] ?> <?php echo $item['quantityUnit'] ?> 
カード番号: <?php echo $item['cardId'] ?> 

<?php endforeach ?>

下記URLよりログインしてご確認ください 
<?php echo $login_url ?> 
 
※このメールへの返信は受け付けていません。 