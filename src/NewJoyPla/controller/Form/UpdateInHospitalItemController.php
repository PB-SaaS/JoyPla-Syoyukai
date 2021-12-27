<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\DistributorUser;
use App\Model\Distributor;
use App\Model\HospitalUser;
use App\Model\Price;
use App\Model\PriceView;
use App\Model\Inventory;
use App\Model\InventoryView;
use App\Model\InventoryEnd;
use App\Model\InventoryHistory;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class UpdateInHospitalItemController extends Controller
{
    public function __construct()
    {
    }
    
    
    public function input(): View
    {
        global $SPIRAL;
        try {
            
            $user_login_id = $SPIRAL->getParam('user_login_id');
            $user_auth_key = $SPIRAL->getParam('user_auth_key');
            $itemId = $SPIRAL->getParam('itemId');
            
            if($user_login_id == '' || $user_auth_key == '' || $itemId == '')
            {
                throw new Exception("ページが存在しません",404);
            }
            
            $user_info = HospitalUser::where('loginId',$user_login_id)->where('authKey',$user_auth_key)->get();
            $user_info = $user_info->data->get(0);
            
            $priceId = $SPIRAL->getParam('priceId');
            
            $price = PriceView::where('requestFlg','1')->where('notUsedFlag','1','!=')->where('itemId',$itemId)->where('hospitalId',$user_info->hospitalId)->get();
            $price = $price->data->all();
            
            $content = $this->view('NewJoyPla/view/Form/UpdateInHospitalItem/Input', [
                    'csrf_token' => Csrf::generate(16),
                    'price_data' => $price,
                    'current_price' => $SPIRAL->getParam('priceId'),
                    ] , false);
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 院内商品情報変更 - 入力',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function confirm(): View
    {
        global $SPIRAL;
        try {
            
            $user_login_id = $SPIRAL->getParam('user_login_id');
            $user_auth_key = $SPIRAL->getParam('user_auth_key');
            $itemId = $SPIRAL->getParam('itemId');
            $priceId = $SPIRAL->getParam('priceId');
            
            if($user_login_id == '' || $user_auth_key == '' || $itemId == '' || $priceId == '')
            {
                throw new Exception("ページが存在しません",404);
            }
            
            $user_info = HospitalUser::where('loginId',$user_login_id)->where('authKey',$user_auth_key)->get();
            $user_info = $user_info->data->get(0);
            
            $price = PriceView::where('requestFlg','1')->where('priceId',$priceId)->where('itemId',$itemId)->where('hospitalId',$user_info->hospitalId)->get();
            $price = $price->data->get(0);
            /*
            $ticket = $this->random(8);

            $priceCh = $SPIRAL->getParam('oldPrice') !== $price->price ? true : false;
            $unitPriceCh = $SPIRAL->getParam('oldUnitPrice') !== $price->unitPrice ? true : false;
            
            $cache = $SPIRAL->getCache();
            $cache->set('ticket', $ticket);
            $cache->set('priceFlag', $priceCh);
            $cache->set('unitPriceFlag', $unitPriceCh);
            */
            $content = $this->view('NewJoyPla/view/Form/UpdateInHospitalItem/Confirm', [
                    'csrf_token' => Csrf::generate(16),
                    'price' => $price,
                    //'ticket' => $ticket,
                    'oldPrice' => $SPIRAL->getParam('oldPrice') ,
                    'oldUnitPrice' => $SPIRAL->getParam('oldUnitPrice'),
                    ] , false);
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 院内商品情報変更 - 確認',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function thank(): View
    {
        global $SPIRAL;
        try {
            /*
            $cacheTicket = '';
            $SMPFORM = $SPIRAL->getParam('SMPFORM');
            $postTicket = $SPIRAL->getParam('ticket');
            $cache = $SPIRAL->getCache();
            $cacheTicket = $cache->get('ticket');
            $priceFlag = $cache->get('priceFlag');
            $unitPriceFlag = $cache->get('unitPriceFlag');
            $cache->delete('ticket');
            */
            $user_login_id = $SPIRAL->getParam('user_login_id');
            $user_auth_key = $SPIRAL->getParam('user_auth_key');
            
            if($user_login_id == '' || $user_auth_key == '')
            {
                throw new Exception("ページが存在しません",404);
            }
            
            $user_info = HospitalUser::where('loginId',$user_login_id)->where('authKey',$user_auth_key)->get();
            $user_info = $user_info->data->get(0);
            
            /*
            if ($SMPFORM && $cacheTicket && $postTicket)
            {
                if (($cacheTicket === $postTicket) && ($priceFlag || $unitPriceFlag))
                {
                *//*
                    $price = $priceFlag ? (int)$SPIRAL->getContextByFieldTitle ('price') : 0;
                    $unitPrice = $unitPriceFlag ? (int)$SPIRAL->getContextByFieldTitle ('unitPrice') : 0;
                    
                    
                    $updateData = [];
                    if ($price > 0) { $updateData['price'] = $price; }
                    if ($unitPrice > 0) { $updateData['unitPrice'] = $unitPrice; }
       */
                    $inHospitalItemId = $SPIRAL->getContextByFieldTitle ('inHospitalItemId');
                    $price = $SPIRAL->getContextByFieldTitle('price');
                    $unitPrice = $SPIRAL->getContextByFieldTitle('unitPrice');
                    
                    
                    $result = InventoryView::where('inHospitalItemId',$inHospitalItemId)->where('inventoryStatus','1')->where('hospitalId',$user_info->hospitalId)->update([
                            'price' => $price,
                            'unitPrice' => $unitPrice
                    ]);
                    
                    if ((int)$result->count > 0) {
                        $inventory = InventoryView::where('inventoryStatus','1')->where('hospitalId',$user_info->hospitalId)->get();
                        $inventory = $inventory->data->all();
                        
                        $Inventory_end = InventoryEnd::where('hospitalId',$user_info->hospitalId);
                        $Inventory_history = InventoryHistory::where('hospitalId',$user_info->hospitalId);
                        
                        $inventory_end_calc = [];
                        $inventory_history_calc = [];
                        
                        foreach($inventory as $item)
                        {
                            $Inventory_end->orWhere( 'inventoryEndId' , $item->inventoryEndId );
                            $Inventory_history->orWhere( 'inventoryHId' , $item->inventoryHId );
                            
                            if( ! array_key_exists( $item->inventoryEndId ,$inventory_end_calc) )
                            {
                                $inventory_end_calc[$item->inventoryEndId] = 0;
                            }
                            
                            if( ! array_key_exists( $item->inventoryHId ,$inventory_history_calc) )
                            {
                                $inventory_history_calc[$item->inventoryHId] = 0;
                            }
                            
                            $inventory_end_calc[$item->inventoryEndId] += (float)$item->inventryAmount;
                            $inventory_history_calc[$item->inventoryHId] += (float)$item->inventryAmount;
                        }
                        
                        $end_update = [];
                        foreach($inventory_end_calc as $id => $count)
                        {
                            $end_update[] = [
                                'inventoryEndId' => $id,
                                'totalAmount' => $count,
                                ];
                        }
                        
                        $history_update = [];
                        foreach($inventory_history_calc as $id => $count)
                        {
                            $history_update[] = [
                                'inventoryHId' => $id,
                                'totalAmount' => $count,
                                ];
                        }
                        
                        InventoryEnd::bulkUpdate('inventoryEndId',$end_update);
                        InventoryHistory::bulkUpdate('inventoryHId',$history_update);
                    }
                //}
            //}
            
            $content = $this->view('NewJoyPla/view/Form/UpdateInHospitalItem/Thank', [
                    'csrf_token' => Csrf::generate(16),
                    'price' => $price,
                    //'ticket' => $ticket,
                    'oldPrice' => $SPIRAL->getParam('oldPrice') ,
                    'oldUnitPrice' => $SPIRAL->getParam('oldUnitPrice'),
                    ] , false);
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 院内商品情報変更 - 完了',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
        

    }
    
    private function random($length = 8) {
        $str_list = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str_list_length = count($str_list);
        $str = "";
        for($i = 0; $i < $length; ++$i) {
            $str .= $str_list[random_int(0, $str_list_length - 1)];
        }
        return $str;
    }
}