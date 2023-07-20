<?php echo $name; ?> 様

JoyPla からお知らせです。

未発注書が作成されましたので、下記の通りお知らせします。

<?php
$order_methods = [
    '1' => 'JoyPla',
    '2' => 'メール',
    '3' => 'FAX',
    '4' => '電話',
    '5' => '業者システム',
];

echo '施設名: ' . $hospitalName . " \n";
echo '担当者: ' . $ordererUserName . " \n";
echo " \n";
echo " \n";
foreach ($history as $h) {
    echo '発注番号: ' . $h['orderNumber'] . " \n";
    echo '部署名: ' . $h['divisionName'] . " \n";
    echo '卸業者名: ' . $h['distributorName'] . " \n";
    echo '発注方法: ' .
        (isset($order_methods[$h['orderMethod']])
            ? $order_methods[$h['orderMethod']]
            : 'その他') .
        " \n";
    echo '金額: ￥' . $h['totalAmount'] . " \n";
    echo " \n";
}
?>

下記URLよりログインしてご確認ください 
<?php echo $url; ?>  

※このメールへの返信は受け付けていません。 