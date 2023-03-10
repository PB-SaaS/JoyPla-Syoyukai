<?php echo $distributor_name; ?> 
<?php echo $name; ?> 様

JoyPla からお知らせです。
発注書が送信されておりますので、下記の通りお知らせします。

[医療機関]
施設名 <?php echo $hospital_name; ?> 
〒 <?php echo $postal_code; ?> <?php echo $prefectures; ?> <?php echo $address; ?> 

[発注内容]
===========================================================
<?php foreach ($orders as $key => $order) { ?>
<?php if ($key > 100) {
    continue;
} ?>
部署名 <?php echo $order['division']['divisionName']; ?> 

発注番号 <?php echo $order['orderId']; ?>  

発注品目 <?php echo $order['itemCount']; ?> (品目) 

合計金額 <?php echo '￥' . number_format_jp((float) $order['totalAmount']); ?> 

下記URLより発注伝票を確認できます
<?php echo $slip_url . $order['orderId']; ?> 
===========================================================
<?php } ?>

システムログインはこちら
<?php echo $login_url; ?>

※このメールへの返信は受け付けていません。
<?php if ($useMedicode): ?>
※Medicode-web連携は毎時00分,15分,30分,45分に実行されます。
<?php endif; ?>
