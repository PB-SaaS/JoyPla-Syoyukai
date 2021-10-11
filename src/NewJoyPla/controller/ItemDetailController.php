<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Tenant;
use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class ItemDetailController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;

        $title = 'JoyPla 商品情報詳細';

        try {
            
            $user_info = new UserInfo($SPIRAL);

            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            $cardId = $SPIRAL->getCardId();
            if($cardId == null)
            {   
                throw new Exception("ページが存在しません",404);
            }

            $tenantKind = '';
            $tenantId = $user_info->getTenantId();
            if ($tenantId)
            {
                $tenant = Tenant::where('tenantId',$tenantId)->get();
                $tenant = $tenant->data->all()[0];
                $tenantKind = $tenant->tenantKind;
            }

            $content = $this->view('NewJoyPla/view/ProductInformationDetail', [
                'user_info' => $user_info,
                'csrf_token' => Csrf::generate(16),
                'tenantKind' => $tenantKind
            ] , false);

        } catch ( Exception $ex ) {
            $title = 'JoyPla エラー';
            $header = $this->view('NewJoyPla/src/Header', [], false);
            
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => $title,
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
}

/***
 * 実行
 */
$ItemDetailController = new ItemDetailController();

$action = $SPIRAL->getParam('Action');

{
    echo $ItemDetailController->index()->render();
}