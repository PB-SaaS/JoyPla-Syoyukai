<?php echo $name ?> 様

JoyPla からお知らせです。

未発注書が作成されましたので、下記の通りお知らせします。

<?php
foreach($history as $h)
{
    echo "[発注番号]: " . $h["orderNumber"] ." \n";
    echo "[金額]: ￥" . number_format_jp($h["totalAmount"]) ." \n";
    echo "[担当者]: " . $h["ordererUserName"] ." \n";
    echo " \n";
}
?>

下記URLよりログインしてご確認ください 
<?php echo $url ?>  

※このメールへの返信は受け付けていません。 