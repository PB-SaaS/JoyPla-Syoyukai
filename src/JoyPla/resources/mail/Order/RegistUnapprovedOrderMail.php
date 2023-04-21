<?php echo $name; ?> 様

JoyPla からお知らせです。

未発注書が作成されましたので、下記の通りお知らせします。

<?php
echo '施設名: ' . $hospitalName . " \n";
echo '担当者: ' . $ordererUserName . " \n";
echo " \n";
echo " \n";
foreach ($history as $h) {
    echo '発注番号: ' . $h['orderNumber'] . " \n";
    echo '部署名: ' . $h['divisionName'] . " \n";
    echo '卸業者名: ' . $h['distributorName'] . " \n";
    echo '発注方法: ' . orderMethod($h['orderMethod']) . " \n";
    echo '金額: ￥' . $h['totalAmount'] . " \n";
    echo " \n";
}

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

下記URLよりログインしてご確認ください 
<?php echo $url; ?>  

※このメールへの返信は受け付けていません。 