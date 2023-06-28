<?php echo $hospitalName . "\n" ?>
<?php echo $name ?> 様

JoyPla からお知らせです。
<?php echo $requestUserName ?>より請求書が作成されましたので、下記の通りお知らせします。

<?php
foreach ($histories as $history) {
    echo "請求番号: " . $history["requestHId"] ." \n";
    echo "請求元部署名: " . $history["sourceDivisionName"] ." \n";
    echo "請求先部署名: " . $history["targetDivisionName"] ." \n";
    echo "品目数: " . $history["itemCount"] ." \n";
    echo " \n";
}
?>

下記URLよりログインしてご確認ください
<?php echo $url ?>

※このメールへの返信は受け付けていません。