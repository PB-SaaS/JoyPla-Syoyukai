<?php
$outputtext = "<?php".PHP_EOL; 

foreach(scanDirctory('../src/Library/gs1128-decoder') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

//file_put_contents("../src/NewJoyPla/require.php", $outputtext);
file_put_contents($argv[1], $outputtext);

function scanDirctory($dir) {
    $files = glob(rtrim($dir, '/') . '/*');
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