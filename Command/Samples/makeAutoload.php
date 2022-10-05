<?php
echo '
<?php
$outputtext = "<?php".PHP_EOL;

foreach('.json_encode($dir,true).' as $val){
    foreach(scanDirctory("src/'.$projectName.'/$val") as $file){
        $file = str_replace("src/", "", $file);
        $outputtext .= "require_once(\'$file\');".PHP_EOL;
    }
}

$outputtext .= "require_once(\''.$projectName.'/'.$projectName.'Application.php\');".PHP_EOL;

file_put_contents("src/'.$projectName.'/require.php", $outputtext);


function scanDirctory($dir) {
    $files = glob(rtrim($dir, "/") . "/*");
    $list = array();
    foreach ($files as $file) {
        if (is_file($file)) {
            $list[] = $file;
        }
        if (is_dir($file)) {
            $list = array_merge($list, scanDirctory($file));
        }
    }
    return $list;
}
';