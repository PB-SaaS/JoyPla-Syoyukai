<?php
require_once "NewJoyPla/core/Monad.php";
require_once "NewJoyPla/core/Collection.php";
require_once "NewJoyPla/core/Model.php";
require_once "NewJoyPla/core/View.php";
require_once "NewJoyPla/core/Controller.php";
require_once "NewJoyPla/core/ApiController.php";
require_once "NewJoyPla/core/Response.php";
require_once "NewJoyPla/core/ApiResponse.php";
require_once "NewJoyPla/core/ListResponse.php";
require_once "NewJoyPla/core/Form.php";
require_once "NewJoyPla/core/Field.php";
require_once "NewJoyPla/core/FieldSet.php";
require_once "NewJoyPla/core/Util.php";
require_once "NewJoyPla/core/Csrf.php";

require_once "NewJoyPla/lib/ApiSpiral.php";
require_once "NewJoyPla/lib/Logger.php";
require_once "NewJoyPla/lib/LoggingSpiralv2.php";
require_once 'NewJoyPla/lib/UserInfo.php';
require_once "NewJoyPla/lib/SpiralDataBase.php";
require_once "NewJoyPla/lib/SpiralSendMail.php";
require_once "NewJoyPla/lib/SpiralTable.php";
require_once "NewJoyPla/lib/SpiralArea.php";
require_once "NewJoyPla/lib/SpiralDBFilter.php";
require_once "NewJoyPla/lib/Define.php";
require_once "NewJoyPla/lib/Func.php";

require_once "NewJoyPla/model/Base.php";


function number_format_jp($num)
{
    if(empty($num)) { return 0; }
    return preg_replace("/\.?0+$/","",number_format($num,2));
}

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