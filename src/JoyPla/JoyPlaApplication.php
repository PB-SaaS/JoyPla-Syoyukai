<?php

namespace JoyPla;

use App\SpiralDb\HospitalUser;
use App\SpiralDb\Tenant;
use Auth;
use framework\Application;
use framework\Facades\Gate;
use JoyPla\Enterprise\CommonModels\GatePermissionModel;

class JoyPlaApplication extends Application
{
    public function __construct()
    {
        $this->boot();
    }

    public function boot()
    {
        $auth = new Auth(HospitalUser::class);

        $tenant = Tenant::where('tenantId', $auth->tenantId)->get();
        $tenant = $tenant->data->get(0);
        
        $auth = $auth->collectMerge($tenant , 'tenantId');

        Gate::setAuth($auth);
 
        Gate::define('auth_check',function(Auth $auth){
            return $auth->id != "";
        });

        Gate::define('register_of_consumption_slips',function(Auth $auth){
            //消費伝票登録
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            if($auth->userPermission == '3')//承認者
            {
                return new GatePermissionModel(false,false);
            }
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('list_of_consumption_slips',function(Auth $auth){
            //消費伝票一覧
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('cancellation_of_consumption_slips',function(Auth $auth){
            //消費伝票取り消し
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            if($auth->userPermission == '3')//承認者
            {
                return new GatePermissionModel(false,false);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('register_of_unordered_slips',function(Auth $auth){
            //未発注伝票登録
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            if($auth->userPermission == '3')//承認者
            {
                return new GatePermissionModel(false,false);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('list_of_unordered_slips',function(Auth $auth){
            //未発注伝票一覧
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('deletion_of_unordered_slips',function(Auth $auth){
            //未発注伝票削除
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            if($auth->userPermission == '3')//承認者
            {
                return new GatePermissionModel(false,false);
            }
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('revision_of_unordered_slips',function(Auth $auth){
            //未発注伝票修正
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            if($auth->userPermission == '3')//承認者
            {
                return new GatePermissionModel(false,false);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('decision_of_order_slips',function(Auth $auth){
            //発注確定
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('list_of_order_slips',function(Auth $auth){
            //発注伝票一覧
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('cancellation_of_order_slips',function(Auth $auth){
            //発注伝票キャンセル
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            if($auth->userPermission == '3')//承認者
            {
                return new GatePermissionModel(false,false);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('revision_of_order_slips',function(Auth $auth){
            //発注伝票訂正
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(false , true);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('receipt',function(Auth $auth){
            //入庫
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            if($auth->userPermission == '3')//承認者
            {
                return new GatePermissionModel(false,false);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('fixed_quantity_order_slips',function(Auth $auth){
            //定数発注
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            if($auth->userPermission == '3')//承認者
            {
                return new GatePermissionModel(false,false);
            }
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('list_of_acceptance_inspection_slips',function(Auth $auth){
            //検収書一覧
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('register_return_slips',function(Auth $auth){
            //返品登録
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            if($auth->userPermission == '3')//承認者
            {
                return new GatePermissionModel(false,false);
            }
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('list_of_return_slips',function(Auth $auth){
            //返品伝票一覧
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(true , true);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('register_of_stocktaking_slips',function(Auth $auth){
            //棚卸登録
            if($auth->userPermission  == '2')//承認者
            {
                return new GatePermissionModel(true , true);
            }
            if($auth->userPermission  == '3')//承認者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('list_of_stocktaking_history',function(Auth $auth){
            //棚卸履歴一覧
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('monthly_reports',function(Auth $auth){
            //月次レポート
            if($auth->userPermission  == '2')//承認者
            {
                return new GatePermissionModel(true , true);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('track_records',function(Auth $auth){
            //実績
            if($auth->userPermission  == '2')//承認者
            {
                return new GatePermissionModel(true , true);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('approval_of_application_for_use',function(Auth $auth){
            //使用申請承認
            if($auth->userPermission  == '2')//承認者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('adjustment_of_inventory',function(Auth $auth){
            //在庫調整
            if($auth->userPermission  == '2')//承認者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('adjustment_of_inventory_log',function(Auth $auth){
            //在庫調整ログ
            if($auth->userPermission  == '2')//承認者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });

        Gate::define('adjustment_of_lot',function(Auth $auth){
            //ロット調整
            if($auth->userPermission  == '2')//承認者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('adjustment_of_lot_log',function(Auth $auth){
            //ロット調整実行ログ
            if($auth->userPermission  == '2')//承認者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });
        Gate::define('register_of_items',function(Auth $auth){
            //商品登録
            if($auth->tenantKind != "1")
            {
                return new GatePermissionModel(false , false);
            }

            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('list_of_users',function(Auth $auth){
            //ユーザー情報一覧
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('list_of_divisions',function(Auth $auth){
            //ユーザー情報一覧
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });
        
        Gate::define('register_of_distributors',function(Auth $auth){
            //ユーザー情報一覧
            if($auth->userPermission  == '2')//担当者
            {
                return new GatePermissionModel(false , false);
            }
            return new GatePermissionModel(true , false);
        });
        
        
        
    }
}