<?php echo $name; ?> 様

JoyPla からお知らせです。
発注書が送信されておりますので、下記の通りお知らせします。

[発注先]
卸業者名 <?php echo $distributor_name; ?> 
〒<?php echo $distributor_postal_code; ?>  
<?php echo $distributor_prefectures; ?> <?php echo $distributor_address; ?> 
<?php
echo '発注方法: ' . orderMethod($order_method);

function orderMethod($index)
{
    if ($index == '1') {
        return 'JoyPla';
    }
    if ($index == '2') {
        return 'メール';
    }
    if ($index == '3') {
        return 'FAX';
    }
    if ($index == '4') {
        return '電話';
    }
    if ($index == '5') {
        return '業者システム';
    }
    return 'その他';
}
?> 

[医療機関]
施設名 <?php echo $hospital_name; ?> 
〒 <?php echo $postal_code; ?> <?php echo $prefectures; ?> <?php echo $address; ?> 
部署名 <?php echo $division_name; ?> 

[発注内容]

発注日時 <?php echo $order_date; ?>  

発注番号 <?php echo $order_number; ?>  

発注品目 <?php echo $item_num; ?> (品目) 

合計金額 <?php echo $total_price; ?> 

システムログインはこちら
<?php echo $login_url; ?>

※このメールへの返信は受け付けていません。
