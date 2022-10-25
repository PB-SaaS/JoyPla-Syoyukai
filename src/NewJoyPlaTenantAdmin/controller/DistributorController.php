<?php

namespace App\Controller;

use Controller;
use Csrf;
use ApiResponse;
use App\Lib\Auth;
use App\Model\Tenant;
use App\Model\Hospital;
use App\Model\Distributor;
use App\Model\DistributorUpsertDB;
use App\Model\DistributorAndHospitalDB;

use Validate\DistributorDB;

use stdClass;
use Exception;

class DistributorController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();

            $hospital = Hospital::where('tenantId', $auth->tenantId)->get();
            $select_hospital = [['text' => '----- 選択してください -----', 'value' => '']];
            foreach ($hospital->data->all() as $h) {
                $select_hospital[] = ['text' => $h->hospitalName, 'value' => $h->hospitalName];
            }

            $distributor = DistributorAndHospitalDB::where('tenantId', $auth->tenantId)->get();
            $select_distributor = [['text' => '----- 選択してください -----', 'value' => '']];
            foreach ($distributor->data->all() as $d) {
                $select_distributor[] = ['text' => $d->distributorName, 'value' => $d->distributorName];
            }
            $session = $SPIRAL->getSession(true, 3600);
            $session->remove('back_url');

            $error = $SPIRAL->getParam('errorMsg');

            $content = $this->view('NewJoyPlaTenantAdmin/view/Distributor/Index', [
                'error' => $error,
            ], false)->render();
        } catch (Exception $ex) {

            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ], false)->render();
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [
                'select_distributor' => $select_distributor,
                'select_hospital' => $select_hospital,
            ], false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [], false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n2' => 'uk-active uk-open',
                'n2_1' => 'uk-active'
            ], false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [], false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 卸業者管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ], false);
        }
    }


    public function bulkInsert()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('DistributorBlukInsert');

            $api_url = "%url/rel:mpgt:Distributor%";
            $error = $SPIRAL->getParam('errorMsg');

            $hospital = Hospital::where('tenantId', $auth->tenantId)->get();

            $content = $this->view('NewJoyPlaTenantAdmin/view/Distributor/BulkInsert', [
                'api_url' => $api_url,
                'hospital' => $hospital->data->all(),
                'csrf_token' => Csrf::generate(16)
            ], false)->render();
        } catch (Exception $ex) {

            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ], false)->render();
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [], false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [], false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n2' => 'uk-active uk-open',
                'n2_2' => 'uk-active',
            ], false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [], false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 卸業者一括登録・更新',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ], false);
        }
    }

    public function bulkUpsertApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            $auth = new Auth();

            $target = new DistributorDB();
            $rowData = $target->rowData;
            //$rowData = $this->requestUrldecode($SPIRAL->getParam('rowData'));
            //$rowData = $SPIRAL->getParam('rowData');
            $insert_data = [];
            foreach ($rowData as $rows) {
                $insert_data[] =
                    [
                        "distributorName" => $rows['data'][0],
                        "distributorId" => $rows['data'][1],
                        "distCommonId" => $rows['data'][2],
                        "postalCode" => $rows['data'][3],
                        "prefectures" => $rows['data'][4],
                        "address" => $rows['data'][5],
                        "phoneNumber" => $rows['data'][6],
                        "faxNumber" => $rows['data'][7],
                        "vendorFlag" => $rows['data'][8],
                        "hospitalId" => $SPIRAL->getParam('hospitalId'),
                    ];
            }

            $result = DistributorUpsertDB::insert($insert_data);
            $content = new ApiResponse($result->ids, count($insert_data), $result->code, $result->message, ['insert']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }

    public function bulkInsertValidateCheckApi()
    {
        global $SPIRAL;

        $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
        Csrf::validate($token, true);

        $target = new DistributorDB();
        $content =  json_encode(array_map(function ($t) {
            return $t->getValue();
        }, $target->getTryDbFieldList()->getFailedObjects()), JSON_UNESCAPED_UNICODE);
        return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
            'content'   => $content,
        ], false);
    }

    public function bulkInsertValidateCheck2Api()
    {
        global $SPIRAL;
        $auth = new Auth();

        $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
        Csrf::validate($token, true);

        $target = new DistributorDB();
        $rowData = $target->rowData;
        //$rowData = $this->requestUrldecode($SPIRAL->getParam('rowData'));
        //$rowData = $SPIRAL->getParam('rowData');
        $messages = [];

        $distributor = Distributor::where('hospitalId', $_POST['hospitalId']);

        foreach ($rowData as $row) {
            $distributor->orWhere('distributorId', $row['data'][1]);
        }

        $distributor = $distributor->get();

        foreach ($rowData as $row) {
            $check = false;
            if ($row['data'][1] === "") {
                $check = true;
            } else {
                foreach ($distributor->data->all() as $d) {
                    if ($d->distributorId === $row['data'][1]) {
                        $check = true;
                    }
                }
            }
            if (!$check) {
                $messages[] = ((int)$row['index'] + 1) . "行目の卸業者ID：存在しません";
            }
        }

        $content = json_encode($messages);
        return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
            'content'   => $content,
        ], false);
    }
}

/***
 * 実行
 */
$DistributorController = new DistributorController();

$action = $SPIRAL->getParam('Action'); {
    if ($action === "bulkInsert") {
        echo $DistributorController->bulkInsert()->render();
    } elseif ($action === "bulkInsertValidateCheckApi") {
        echo $DistributorController->bulkInsertValidateCheckApi()->render();
    } elseif ($action === "bulkInsertValidateCheck2Api") {
        echo $DistributorController->bulkInsertValidateCheck2Api()->render();
    } elseif ($action === "bulkUpsertApi") {
        echo $DistributorController->bulkUpsertApi()->render();
    } else {
        echo $DistributorController->index()->render();
    }
}
