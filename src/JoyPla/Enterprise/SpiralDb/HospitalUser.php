<?php
namespace App\SpiralDb;

use Auth;
use SpiralORM;

class HospitalUser extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_HUserDB";
    public static $guarded = ["id"];
    public static $primary_key = "loginId";
    public static $mail_field_title = "mailAddress";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "authKey",
        "hospitalId",
        "divisionId",
        "userPermission",
        "loginId",
        "loginPassword",
        "name",
        "nameKana",
        "mailAddress",
        "remarks",
        "termsAgreement",
        "tenantId",
        "agreementDate",
        "hospitalAuthKey",
        "userCheck"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

    
    public static function isAdmin(Auth $auth){
        if($auth->userPermission == '1')
        {
            return true;
        }
        return false;
    }

    public static function isUser(Auth $auth){
        if($auth->userPermission  == '2')
        {
            return true;
        }
        return false;
    }
    
    
    public static function isApprover(Auth $auth){
        if($auth->userPermission == '3')
        {
            return true;
        }
        return false;
    }
} 
