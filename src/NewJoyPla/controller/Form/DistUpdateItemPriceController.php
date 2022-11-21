<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

use function App\Lib\html;

class DistUpdateItemPriceController extends Controller
{
    public function __construct()
    {
    }

    public function input(): View
    {
        global $SPIRAL;
        try {
            $notUsedFlag = html($SPIRAL->getParam('notUsedFlag'));
            $itemId = html($SPIRAL->getParam('itemId'));
            $makerName = html($SPIRAL->getParam('makerName'));
            $itemName = html($SPIRAL->getParam('itemName'));
            $itemCode = html($SPIRAL->getParam('itemCode'));
            $itemStandard = html($SPIRAL->getParam('itemStandard'));
            $itemJANCode = html($SPIRAL->getParam('itemJANCode'));
            $quantity = html($SPIRAL->getParam('quantity'));
            $quantityUnit = html($SPIRAL->getParam('quantityUnit'));
            $itemUnit = html($SPIRAL->getParam('itemUnit'));
            $price = html($SPIRAL->getParam('price'));
            $notice = html($SPIRAL->getParam('notice'));

            $content = $this->view('NewJoyPla/view/Form/DistUpdateItemPrice/Input', [
                    'csrf_token' => Csrf::generate(16),
                    'SPIRAL' => $SPIRAL,
                    'notUsedFlag' => $notUsedFlag,
                    'itemId' => $itemId,
                    'makerName' => $makerName,
                    'itemName' => $itemName,
                    'itemCode' => $itemCode,
                    'itemStandard' => $itemStandard,
                    'itemJANCode' => $itemJANCode,
                    'quantity' => $quantity,
                    'quantityUnit' => $quantityUnit,
                    'itemUnit' => $itemUnit,
                    'price' => $price,
                    'notice' => $notice
                    ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 金額情報変更 - 入力',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ], false);
        }
    }


    public function confirm(): View
    {
        global $SPIRAL;
        try {
            $notUsedFlag = html($SPIRAL->getParam('notUsedFlag'));
            $itemId = html($SPIRAL->getParam('itemId'));
            $makerName = html($SPIRAL->getParam('makerName'));
            $itemName = html($SPIRAL->getParam('itemName'));
            $itemCode = html($SPIRAL->getParam('itemCode'));
            $itemStandard = html($SPIRAL->getParam('itemStandard'));
            $itemJANCode = html($SPIRAL->getParam('itemJANCode'));
            $quantity = html($SPIRAL->getParam('quantity'));
            $quantityUnit = html($SPIRAL->getParam('quantityUnit'));
            $itemUnit = html($SPIRAL->getParam('itemUnit'));
            $price = html($SPIRAL->getParam('price'));
            $notice = html($SPIRAL->getParam('notice'));

            $content = $this->view('NewJoyPla/view/Form/DistUpdateItemPrice/Confirm', [
                    'csrf_token' => Csrf::generate(16),
                    'SPIRAL' => $SPIRAL,
                    'notUsedFlag' => $notUsedFlag,
                    'itemId' => $itemId,
                    'makerName' => $makerName,
                    'itemName' => $itemName,
                    'itemCode' => $itemCode,
                    'itemStandard' => $itemStandard,
                    'itemJANCode' => $itemJANCode,
                    'quantity' => $quantity,
                    'quantityUnit' => $quantityUnit,
                    'itemUnit' => $itemUnit,
                    'price' => $price,
                    'notice' => $notice
                    ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 金額情報変更 - 確認',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ], false);
        }
    }
}
