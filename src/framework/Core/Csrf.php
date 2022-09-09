<?php

class Csrf
{
    public static function generate($length = 16)
    {
        $string = null;
        if(class_exists('Spiral')){
            global $SPIRAL;
            $session   = $SPIRAL->getSession(false,3600);
            $string = $session->get('csrf');
        }

        if($string == null)
        {
            while (($len = strlen($string)) < $length) {
                $size = $length - $len;
                $bytes = random_bytes($size);
                $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
            }
        }
        
        if(class_exists('Spiral')){
            global $SPIRAL;
            $session   = $SPIRAL->getSession(false,3600);
            $session->put('csrf', $string);
        }

        return $string;
    } 

    public static function validate($token, $throw = false)
    {
        $success = true;
        if(class_exists('Spiral')){
            global $SPIRAL;
            $session   = $SPIRAL->getSession(false,3600);
            $success = $session->get('csrf') === $token;
            if( !$success && $throw )
            {
                throw new Exception('CSRF validation failed.', 300);
            }
        }

        return $success;
    }
}