<?php echo $name; ?> 様

JoyPla からお知らせです。
発注書が送信されておりますので、下記の通りお知らせします。

[医療機関]
施設名 <?php echo $hospital_name; ?> 
〒 <?php echo $postal_code; ?> <?php echo $prefectures; ?> <?php echo $address; ?> 

[発注内容]
===========================================================
<?php foreach ($orders as $key => $order) {
    if ($key > 100) {
        continue;
    } ?>
卸業者名 <?php echo $order['distributor']['distributorName']; ?> 
〒<?php echo $order['distributor']['postalCode']; ?>  
<?php echo $order['distributor']['prefectures']; ?> <?php echo $order[
     'distributor'
 ]['address']; ?> 
<?php echo $order['distributor']['orderMethod']; ?> 

部署名 <?php echo $order['division']['divisionName']; ?> 

発注番号 <?php echo $order['orderId']; ?>  

発注品目 <?php echo $order['itemCount']; ?> (品目) 

合計金額 <?php echo '￥' . number_format_jp((float) $order['totalAmount']); ?> 
===========================================================
<?php
} ?>

システムログインはこちら
<?php echo $login_url; ?>

※このメールへの返信は受け付けていません。
