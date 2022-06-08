<?php
$outputtext = "<?php".PHP_EOL; 
foreach(scanDirctory('../src/NewJoyPlaTenantAdmin/Domain/Traits') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/NewJoyPlaTenantAdmin/Domain/Entity') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/NewJoyPlaTenantAdmin/Domain/ValueObject') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}
foreach(scanDirctory('../src/NewJoyPlaTenantAdmin/Application') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/NewJoyPlaTenantAdmin/lib') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/NewJoyPlaTenantAdmin/core') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

foreach(scanDirctory('../src/NewJoyPlaTenantAdmin/model') as $file){
    $file = str_replace("../src/", "", $file);
    $outputtext .= "require_once('$file');".PHP_EOL; 
}

//file_put_contents("../src/NewJoyPlaTenantAdmin/require.php", $outputtext);
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