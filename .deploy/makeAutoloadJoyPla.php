<?php
$outputtext = "<?php".PHP_EOL; 
$outputtext .= "require_once('JoyPla/config.php');".PHP_EOL; 

foreach(scanDirctory('../src/JoyPla/Domain/Traits') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/JoyPla/Domain/Entity') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/JoyPla/Domain/ValueObject') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}
foreach(scanDirctory('../src/JoyPla/Application') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}
foreach(scanDirctory('../src/JoyPla/Enterprise/Traits') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/JoyPla/Enterprise/Models') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}
foreach(scanDirctory('../src/JoyPla/Enterprise/ValueObject') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/JoyPla/InterfaceAdapters') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/JoyPla/lib') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/JoyPla/core') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/JoyPla/Enterprise/SpiralDb') as $file){
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