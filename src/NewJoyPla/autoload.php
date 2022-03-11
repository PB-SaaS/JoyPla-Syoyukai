<?php
require_once "NewJoyPla/classes/Monad.php";
require_once "NewJoyPla/classes/Collection.php";
require_once "NewJoyPla/classes/Model.php";
require_once "NewJoyPla/classes/View.php";
require_once "NewJoyPla/classes/Controller.php";
require_once "NewJoyPla/classes/ApiController.php";
require_once "NewJoyPla/classes/Response.php";
require_once "NewJoyPla/classes/ApiResponse.php";
require_once "NewJoyPla/classes/ListResponse.php";
require_once "NewJoyPla/classes/Form.php";
require_once "NewJoyPla/classes/Field.php";
require_once "NewJoyPla/classes/FieldSet.php";
require_once "NewJoyPla/classes/Util.php";
require_once "NewJoyPla/classes/Csrf.php";

require_once "NewJoyPla/lib/ApiSpiral.php";
require_once 'NewJoyPla/lib/UserInfo.php';
require_once "NewJoyPla/lib/SpiralDataBase.php";
require_once "NewJoyPla/lib/SpiralSendMail.php";
require_once "NewJoyPla/lib/SpiralTable.php";
require_once "NewJoyPla/lib/SpiralDBFilter.php";
require_once "NewJoyPla/lib/Define.php";
require_once "NewJoyPla/lib/Func.php";

require_once "NewJoyPla/model/Base.php";


/** 初期実行しなければならない処理 */


use App\Lib\UserInfo;

class AutoLoad {
    
    public function __construct()
    {
        global $SPIRAL;
        $user_info = new UserInfo($SPIRAL);
        if($user_info->getTermsAgreement() != '2' && $user_info->isDistributorUser())
        {
            require_once "NewJoyPla/autoload.php";
            require_once "NewJoyPla/controller/Distributor/AgreePageController.php";
            exit;
        }
        else if($user_info->getTermsAgreement() != '2' && $user_info->isHospitalUser())
        {
            require_once "NewJoyPla/autoload.php";
            require_once "NewJoyPla/controller/AgreePageController.php";
            exit;
        }
        else if($user_info->getAffiliationId() == "" && $user_info->isDistributorUser())
        {
            $autoload_action = 'accountSelect';
            require_once 'NewJoyPla/autoload.php';
            require_once 'NewJoyPla/controller/Distributor/TopPageController.php';
            exit;
        }
    }
}
global $SPIRAL;
$autoloadCheckSkip = $SPIRAL->getParam('autoloadCheckSkip');
if($autoloadCheckSkip === null)
{
    $AutoLoad = new AutoLoad();
}