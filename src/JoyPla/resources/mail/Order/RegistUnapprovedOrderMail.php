<?php echo $name ?> 様

JoyPla からお知らせです。

未発注書が作成されましたので、下記の通りお知らせします。

<?php

echo "[施設名]: " . $hospitalName ." \n";
echo "[担当者]: " . $ordererUserName ." \n";
echo " \n";
echo " \n";
foreach($history as $h)
{
    echo "[発注番号]: " . $h["orderNumber"] ." \n";
    echo "[部署名]: " . $h["divisionName"] ." \n";
    echo "[卸業者名]: " . $h["distributorName"] ." \n";
    echo "[金額]: ￥" . number_format($h["totalAmount"]) ." \n";
    echo " \n";
}
?>

下記URLよりログインしてご確認ください 
<?php echo $url ?>  

※このメールへの返信は受け付けていません。 