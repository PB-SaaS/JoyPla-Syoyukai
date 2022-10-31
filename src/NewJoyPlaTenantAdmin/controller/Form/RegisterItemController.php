<?php

namespace App\Controller;

use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Model\Item;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class RegisterItemController extends Controller
{
    public function __construct()
    {
    }

    public function thank()
    {
        global $SPIRAL;
        try {
            $session = $SPIRAL->getSession(true, 3600);
            $content = '商品情報の登録が完了しました。';

            if (!($session->containsKey('tenantId'))) {
                Item::where('itemId', $SPIRAL->getContextByFieldTitle('itemId'))->delete();
                throw new Exception('ページの有効期限が切れました。再度お試し下さい。', 112);
            }

            $user_tenantId = $session->get('tenantId');
            $tenantId = $SPIRAL->getParam('tenantId');

            if (!($session->get('tenantId')) || $tenantId == '') {
                throw new Exception('Not found.', 404);
            }

            if ($tenantId !== $user_tenantId) {
                Item::where('itemId', $SPIRAL->getContextByFieldTitle('itemId'))->update([
                    'tenantId' => $user_tenantId,
                    'updateTime' => 'now'
                ]);
            }
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/FormError', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false)->render();
        } finally {
            $session->remove('tenantId');
            $SPIRAL->finishSession();
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
                'content' => $content
            ], false);
        }
    }
}
