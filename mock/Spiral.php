<?php
class Spiral{

    public function __construct(){
    }

    public function finishSession(){
    }

    public function getArgs(){
    }
    
    public function getCache(int $_timeout = 900){
    }

    public function getCardId(){
    }

    public function getContextByFieldCode(string $_fieldCode = null){
        return $_fieldCode;
    }

    public function getContextByFieldTitle(string $_fieldTitle = null){
        return $_fieldTitle;
    }

    public function getCookieFilePath(){
    }

    public function getDataBase(string $_dbName = null){
    }

    public function getFacebook( array $_option){
    }

    public function getJsonParam(){
    }

    public function getParam(string $_name = null){
    }
    
    public function getParams(string $_name = null){
    }
    
    public function getPdfReport(){
    }
    
    public function getSpiralApiCommunicator(){
        return new \PbSpiralApiCommunicator();
    }
    
    public function getSpiralCrypt(){
    }
    
    public function getSpiralCryptOpenSsl(){
    }
    
    public function getTwitter(string $_accessToken = null,string $_accessTokenSecret = null){
    }
    
    public function getUserAgent(){
    }
    
    public function setApiToken(string $_token = null,string $_secret = null){
    }
    
    public function setApiTokenTitle(string $_title = null){
    }
    
    public function urlEncode(string $_string = null){
    }
    
}