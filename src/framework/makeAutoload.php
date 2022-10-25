<?php

            $outputtext = "<?php".PHP_EOL; 
        
            foreach(scanDirctory("src/framework/Exception") as $file){
                $file = str_replace("src/", "", $file);
                $outputtext .= "require_once('$file');".PHP_EOL; 
            }
            foreach(scanDirctory("src/framework/Facades") as $file){
                $file = str_replace("src/", "", $file);
                $outputtext .= "require_once('$file');".PHP_EOL; 
            }
            foreach(scanDirctory("src/framework/Http") as $file){
                $file = str_replace("src/", "", $file);
                $outputtext .= "require_once('$file');".PHP_EOL; 
            }
            foreach(scanDirctory("src/framework/Routing") as $file){
                $file = str_replace("src/", "", $file);
                $outputtext .= "require_once('$file');".PHP_EOL; 
            }
            foreach(scanDirctory("src/framework/Core") as $file){
                $file = str_replace("src/", "", $file);
                $outputtext .= "require_once('$file');".PHP_EOL; 
            }
            foreach(scanDirctory("src/framework/Library") as $file){
                $file = str_replace("src/", "", $file);
                $outputtext .= "require_once('$file');".PHP_EOL; 
            }
            
            $outputtext .= "require_once('framework/SpiralConnecter/SpiralConnecterInterface.php');".PHP_EOL;
            $outputtext .= "require_once('framework/SpiralConnecter/SpiralConnecter.php');".PHP_EOL;
            $outputtext .= "require_once('framework/SpiralConnecter/SpiralDB.php');".PHP_EOL;
            $outputtext .= "require_once('framework/SpiralConnecter/SpiralManager.php');".PHP_EOL;
            $outputtext .= "require_once('framework/SpiralConnecter/SpiralExpressManager.php');".PHP_EOL;
            $outputtext .= "require_once('framework/SpiralConnecter/Paginator.php');".PHP_EOL;
            $outputtext .= "require_once('framework/SpiralConnecter/SpiralApiConnecter.php');".PHP_EOL;
            
            $outputtext .= "require_once('framework/Application.php');".PHP_EOL;
            file_put_contents('src/framework/Bootstrap/autoload.php', $outputtext);
            
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
        