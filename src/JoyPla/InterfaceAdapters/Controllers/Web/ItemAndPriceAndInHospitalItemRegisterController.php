<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\HospitalUser;
use App\SpiralDb\Distributor;
use App\SpiralDb\Item;
use Auth;
use Csrf;
use Collection;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Http\Request;
use framework\Routing\Router;
use framework\SpiralConnecter\SpiralDB;
use framework\Library\SiValidator;
use framework\Library\SpiralDbUniqueRule;

/* 
SiValidator::defineRule("janTenantUnique", function($value, $param){
    SpiralDbUniqueRule::unique("NJ_itemDB","itemJANCode", function($spiralDb){
        $spiralDb -> where("tenantId", $this->request->user()->tenantId);
        return $spiralDb;    
    });
});
 */

class ItemAndPriceAndInHospitalItemRegisterController extends Controller
{
    private $formName = "itemAndPriceAndInHospitalItemRegistForm";

    private array $input = [
        "itemName",
        "category",
        "smallCategory",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "catalogNo",
        "serialNo",
        "lotManagement",
        "officialFlag",
        "officialprice",
        "officialpriceOld",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "minPrice",
        "distributorId",
        "distributorMCode",
        "price",
        "unitPrice",
        "medicineCategory",
        "homeCategory",
        "measuringInst",
        "notice",
        "janTenantId",
    ];

    private array $rules = [
        "itemName" => ['required','maxword:128'],
        "category" => [],
        "smallCategory" => ['maxword:128'],
        "itemCode" => ['maxword:128'],
        "itemStandard" => ['maxword:128'],
        "itemJANCode" => ['required', 'digits:13',],
        "makerName" => ['maxword:128'],
        "catalogNo" => ['maxword:128'],
        "serialNo" => ['maxword:128'],
        "lotManagement" => [],
        "officialFlag" => [],
        "officialprice" => ['min:0'],
        "officialpriceOld" => ['min:0'],
        "quantity" => ['required', 'min:1'],
        "quantityUnit" => ['required'],
        "itemUnit" => ['required'],
        "minPrice" => ['min:0'],
        "distributorId" => ['required'],
        "distributorMCode" => ['maxword:128'],
        "price" => ['required', 'min:0'],
        "unitPrice" => ['min:0'],
        "medicineCategory" => ['maxword:512'],
        "homeCategory" => ['maxword:512'],
        "measuringInst" => ['maxword:128'],
        "notice" => ['maxword:512'],
        "janTenantId" => ["spiralDbUnique"],
    ];

    private array $labels = [
        "itemName" => "商品名",
        "category" => "分類",
        "smallCategory" => "小分類",
        "itemCode" => "製品コード",
        "itemStandard" => "規格",
        "itemJANCode" => "JANコード",
        "makerName" => "メーカー名",
        "catalogNo" => "カタログNo",
        "serialNo" => "シリアルNo",
        "lotManagement" => "ロット管理フラグ",
        "officialFlag" => "償還価格フラグ",
        "officialprice" => "償還価格",
        "officialpriceOld" => "旧償還価格",
        "quantity" => "入数",
        "quantityUnit" => "入数単位",
        "itemUnit" => "個数単位",
        "minPrice" => "定価",
        "distributorId" => "卸業者",
        "distributorMCode" => "卸業者管理コード",
        "price" => "購買価格",
        "unitPrice" => "単価",
        "medicineCategory" => "保険請求分類（医科）",
        "homeCategory" => "保険請求分類（在宅）",
        "measuringInst" => "測定機器名",
        "notice" => "特記事項",
        "janTenantId" => "JANコード",
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function register($vars) {
        $user = ($this->request->user());
        $input = $this->request->only($this->input);
        $itemJANCode = $input["itemJANCode"];
        $input["janTenantId"] = $itemJANCode . $this->request->user()->tenantId;
        if($this->request->confirm){
            $validate = SiValidator::make(
                $input,
                $this->rules,
                $this->labels
            );
        }

        $distributor = SpiralDB::title("NJ_distributorDB") 
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->value([
                'distributorId',
                'distributorName',
            ])->get()->toArray();

        if($user->userPermission == "1" || $user->userPermission == "3")
        {
            $body = view('html/Product/ItemAndPriceAndInHospitalItemRegist/input' , [
                'distributor' => $distributor,
                'input' => $this->request->all(),
                'validate' => $validate,
                'csrf' => Csrf::generate(),
            ]);
            echo view('html/Common/Template', compact('body'), false)->render();
        }else
        {
            $body = view('html/Common/Error', [
                'message' => "登録する権限がありません。",
            ]);
            echo view('html/Common/Template', compact('body'), false)->render();
        }
        
    }

    public function confirm(array $vars){
        Csrf::validate($this->request->_csrf,true);

        $user = ($this->request->user());
        $input = $this->request->only($this->input);
        $itemJANCode = $input["itemJANCode"];
        $input["janTenantId"] = $itemJANCode . $this->request->user()->tenantId;
        $this->request->session()->put($this->formName, $input);
        
        $validate = SiValidator::make(
            $input,
            $this->rules,
            $this->labels
        );

        if( $validate->isError() ){
            $this->request->set('confirm' , true);
            Router::redirect('/product/ItemAndPriceAndInHospitalRegist/input',$this->request);
            exit;
        }

        $distributor = SpiralDB::title("NJ_distributorDB") -> where('hospitalId', $this->request->user()->hospitalId)
            ->where('distributorId', $this->request->get('distributorId'))
            ->value([
                'distributorName',
            ])->get()->toArray();
        $category = [
                    1 => "医療材料",
                    2 => "薬剤",
                    3 => "試薬",
                    4 => "日用品",
                    99 => "その他",
                    ];

        if($user->userPermission == "1" || $user->userPermission == "3")
        {
            $body = view('html/Product/ItemAndPriceAndInHospitalItemRegist/confirm' , [
                'distributor' => $distributor,
                'category' -> $category,
                'input' => $this->request->all(),
                'session' => $this->request->session()->get($this->formName),
                'validate' => $validate,
                'csrf' => Csrf::generate()
            ]);
            echo view("html/Common/Template", compact("body"), false);
        }else
        {
            $body = view('html/Common/Error', [
                'message' => "登録する権限がありません。",
            ]);
            echo view('html/Common/Template', compact('body'), false)->render();
        }
    }

    public function thanks(array $vars)
    {
        Csrf::validate($this->request->_csrf,true);
        $input = $this->request->session()->get($this->formName , []);
        $this->request->merge($input);

        $validate = SiValidator::make(
            $input,
            $this->rules,
            $this->labels
        );
        
        if( $this->request->formBack || $validate->isError()){
            $this->request->set('confirm' , true);
            Router::redirect('/product/ItemAndPriceAndInHospitalRegist/input',$this->request);
            exit;
        }

        $this->request->session()->forget($this->formName);

        $body = view('html/Product/ItemAndPriceAndInHospitalItemRegist/thanks', []);
        echo view("html/Common/Template", compact("body"), false);
    }

}