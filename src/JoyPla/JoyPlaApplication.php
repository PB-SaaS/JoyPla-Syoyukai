<?php

namespace JoyPla;

use ApiResponse;
use App\Lib\ApiSpiral;
use Auth;
use framework\Application;
use framework\Facades\Gate;
use framework\Http\Request;
use framework\SpiralConnecter\SpiralConnecter;
use JoyPla\Application\LoggingObject\Spiralv2LogginObject;
use JoyPla\Enterprise\CommonModels\GatePermissionModel;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use Logger;
use LoggingConfig;

class JoyPlaApplication extends Application
{
    private ?Auth $auth = null;

    public function __construct()
    {
        config_path('JoyPla/Config/app');
        $this->auth = self::initAuth();
        $this->boot();
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public static function initAuth()
    {
        return new Auth('NJ_HUserDB', [
            'id',
            'registrationTime',
            'updateTime',
            'authKey',
            'hospitalId',
            'divisionId',
            'userPermission',
            'loginId',
            'loginPassword',
            'name',
            'nameKana',
            'mailAddress',
            'remarks',
            'termsAgreement',
            'tenantId',
            'agreementDate',
            'hospitalAuthKey',
            'userCheck',
            'affiliationHId'
        ]);
    }

    public function boot()
    {
        Request::setPathKey('path');

        /** logger 設定 */
        ApiResponse::$logger = new Logger(
            new Spiralv2LogginObject(
                LoggingConfig::SPIRALV2_API_KEY,
                LoggingConfig::LOGGING_APP_TITLE,
                LoggingConfig::JOYPLA_API_LOGGING_DB_TITLE,
                LoggingConfig::LOG_LEVEL
            )
        );

        $auth = $this->auth;

        $tenant = ModelRepository::getTenantInstance()
            ->where('tenantId', $auth->tenantId)
            ->get();
        $tenant = $tenant->first();

        $auth = $auth->collectMerge($tenant, 'tenantId');

        Gate::setAuth($auth);

        Gate::define('auth_check', function (Auth $auth) {
            return $auth->id != '';
        });

        Gate::define('is_admin', function (Auth $auth) {
            if ($auth->userPermission == '1') {
                return true;
            }
            return false;
        });

        Gate::define('is_user', function (Auth $auth) {
            if ($auth->userPermission == '2') {
                return true;
            }
            return false;
        });

        Gate::define('is_approver', function (Auth $auth) {
            if ($auth->userPermission == '3') {
                return true;
            }
            return false;
        });

        Gate::define('is_use_direct_delivery', function(){
            if(defined('IS_USE_DIRECT_DELIVERY') && IS_USE_DIRECT_DELIVERY){
                return true;
            }
            return false;
        });

        Gate::define('register_of_consumption_slips', function (Auth $auth) {
            //消費伝票登録
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('bulkregister_of_consumption_slips', function (
            Auth $auth
        ) {
            //消費伝票一括登録
            if ($auth->userPermission == '1') {
                //管理者
                return new GatePermissionModel(true, false);
            }
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(false, false);
        });

        Gate::define('list_of_consumption_slips', function (Auth $auth) {
            //消費伝票一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('update_of_consumption_slips', function (
            Auth $auth
        ) {
            //
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('cancellation_of_consumption_slips', function (
            Auth $auth
        ) {
            //消費伝票取り消し
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('register_of_unordered_slips', function (Auth $auth) {
            //未発注伝票登録
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_unordered_slips', function (Auth $auth) {
            //未発注伝票一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('deletion_of_unordered_slips', function (Auth $auth) {
            //未発注伝票削除
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('revision_of_unordered_slips', function (Auth $auth) {
            //未発注伝票修正
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('decision_of_order_slips', function (Auth $auth) {
            //発注確定
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('delete_of_order_slips', function (Auth $auth) {
            //発注削除
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_order_slips', function (Auth $auth) {
            //発注伝票一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('update_of_consumption_slips', function (
            Auth $auth
        ) {
            //
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('cancellation_of_order_slips', function (Auth $auth) {
            //発注伝票キャンセル
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('revision_of_order_slips', function (Auth $auth) {
            //発注伝票訂正
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('receipt', function (Auth $auth) {
            //入庫
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('late_receipt', function (Auth $auth) {
            //あとから入庫
            if ($auth->userPermission != '1') {
                //管理者以外
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('fixed_quantity_order_slips', function (Auth $auth) {
            //定数発注
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_acceptance_inspection_slips', function (
            Auth $auth
        ) {
            //検収書一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('register_return_slips', function (Auth $auth) {
            //返品登録
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_return_slips', function (Auth $auth) {
            //返品伝票一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('register_of_stocktaking_slips', function (Auth $auth) {
            //棚卸登録
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_stocktaking_history', function (Auth $auth) {
            //棚卸履歴一覧
            return new GatePermissionModel(true, false);
        });

        Gate::define('monthly_reports', function (Auth $auth) {
            //月次レポート
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('track_records', function (Auth $auth) {
            //実績
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('approval_of_application_for_use', function (Auth $auth) {
            //使用申請承認
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('adjustment_of_inventory', function (Auth $auth) {
            //在庫調整
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('adjustment_of_inventory_log', function (Auth $auth) {
            //在庫調整ログ
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('adjustment_of_lot', function (Auth $auth) {
            //ロット調整
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('adjustment_of_lot_log', function (Auth $auth) {
            //ロット調整実行ログ
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });
        Gate::define('register_of_items', function (Auth $auth) {
            //商品登録
            if ($auth->tenantKind != '1') {
                return new GatePermissionModel(false, false);
            }

            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }

            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }

            return new GatePermissionModel(true, false);
        });

        Gate::define('register_of_item_and_price_and_inHospitalItem', function (
            Auth $auth
        ) {
            //商品・金額・院内商品登録
            if ($auth->tenantKind != '1') {
                return new GatePermissionModel(false, false);
            }

            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }

            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }

            return new GatePermissionModel(true, false);
        });

        Gate::define('register_of_price_and_inHospitalItem', function (
            Auth $auth
        ) {
            //金額・院内商品登録
            if ($auth->tenantKind != '1') {
                return new GatePermissionModel(false, false);
            }

            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }

            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }

            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_users', function (Auth $auth) {
            //ユーザー一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_divisions', function (Auth $auth) {
            //部署一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('register_of_distributors', function (Auth $auth) {
            //卸業者一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('show_of_picking_history', function (Auth $auth) {
            //ユーザー情報一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('contract_confirm', function (Auth $auth) {
            //契約情報
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            if ($auth->userPermission == '3') {
                //承認者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('register_of_item_requests', function (Auth $auth) {
            //個別請求登録
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_item_request_history', function (Auth $auth) {
            //請求履歴一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('delete_of_item_request_history', function (Auth $auth) {
            //請求履歴削除
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('update_of_item_request_history', function (Auth $auth) {
            //請求内容更新
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('totalization_of_item_requests', function (Auth $auth) {
            //請求商品一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('item_request_bulk', function (Auth $auth) {
            //請求商品一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(false, false);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('register_of_payouts', function (Auth $auth) {
            //払出登録
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });
        
        Gate::define('list_of_payout_slips', function (Auth $auth) {
            //払出登録
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_accountant_slips', function (Auth $auth) {
            //会計一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('register_of_accountant', function (Auth $auth) {
            //会計一覧
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_itemList_slips', function (Auth $auth) {
            //商品リスト
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('register_of_itemList', function (Auth $auth) {
            //商品リスト-詳細
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('list_of_stocktakingList_slips', function (Auth $auth) {
            //棚卸商品管理表
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

        Gate::define('register_of_stocktakingList', function (Auth $auth) {
            //棚卸商品管理表-詳細
            if ($auth->userPermission == '2') {
                //担当者
                return new GatePermissionModel(true, true);
            }
            return new GatePermissionModel(true, false);
        });

    }
}
