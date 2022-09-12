<?php

namespace framework\SpiralConnecter {

    use App\Lib\ApiSpiral;

    class SpiralDB {

        protected string $title = "";
        protected array $fields = [];

        private static string $token = '';
        private static string $secret = '';


        private static ?SpiralConnecterInterface $connecter = null;

        public static function setConnecter(SpiralConnecterInterface $connecter)
        {
            self::$connecter = $connecter;
        }

        public static function setToken(string $token , string $secret)
        {
            self::$token = $token;
            self::$secret = $secret;
        }

        public static function filter($title)
        {
            return (new SpiralFilterManager(self::$connecter))->setTitle($title);
        }

        public static function mail($title)
        {
            return (new SpiralExpressManager(self::$connecter))->setTitle($title);
        }

        public static function title($title)
        {
            return (new SpiralManager(self::$connecter))->setTitle($title);
        }

        public static function getConnection()
        {
            if(class_exists('Spiral') && class_exists('SpiralApiRequest'))
            {
                global $SPIRAL;
                return new SpiralConnecter($SPIRAL);
            } 

            return new SpiralApiConnecter(self::$token , self::$secret);
        }
    }


    class XSpiralApiHeaderObject 
    {
        private string $func = '';
        private string $method = '';
        private string $action = '';

        public function __construct($func , $method , $action = 'request')
        {
            $this->func = $func;
            $this->method = $method;
            $this->action = $action;
        }

        public function __toString()
        {
            return "$this->func/$this->method/$this->action";
        }

        public function func(){
            return $this->func;
        }

        public function method(){
            return $this->method;
        }

        public function action(){
            return $this->action;
        }
    }

    abstract class SpiralModel {

        public function init()
        {
            return (new SpiralManager(SpiralDB::getConnection()))->setTitle($this->title)->fields($this->fields);
        }
    }
}