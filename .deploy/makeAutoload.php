<?php
$outputtext = "<?php".PHP_EOL; 
foreach(scanDirctory('../src/JoyPla/Domain/Traits') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/NewJoyPla/Domain/Entity') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/NewJoyPla/Domain/ValueObject') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}
foreach(scanDirctory('../src/NewJoyPla/Application') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/NewJoyPla/lib') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/NewJoyPla/core') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/NewJoyPla/model') as $file){
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