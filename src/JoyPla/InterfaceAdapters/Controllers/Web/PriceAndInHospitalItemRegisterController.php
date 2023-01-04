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
use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\PriceAndInHospitalItemRepository;
use JoyPla\InterfaceAdapters\Presenters\Api\Item\ItemRegisterPresenter;
use JoyPla\Application\InputPorts\Api\Item\PriceAndInHospitalItemRegisterInputData;
use JoyPla\Application\InputPorts\Api\Item\PriceAndInHospitalItemRegisterInputPortInterface;
use JoyPla\Application\Interactors\Api\Item\PriceAndInHospitalItemRegisterInteractor;

class PriceAndInHospitalItemRegisterController extends Controller
{
    private $formName = "priceAndInHospitalItemRegistForm";
    private $itemData = "forRegistingPriceAndInHospitalItem";

    private array $input = [
        "lotManagement",
        "officialFlag",
        "officialprice",
        "officialpriceOld",
        "quantity",
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
    ];

    private array $rules = [
        "lotManagement" => [],
        "officialFlag" => [],
        "officialprice" => ['min:0'],
        "officialpriceOld" => ['min:0'],
        "quantity" => ['required', 'min:1'],
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
    ];

    private array $labels = [
        "lotManagement" =>"ロット管理フラグ",
        "officialFlag" =>"償還価格フラグ",
        "officialprice" =>"償還価格",
        "officialpriceOld" =>"旧償還価格",
        "quantity" =>"入数",
        "itemUnit" =>"個数単位",
        "minPrice" =>"定価",
        "distributorId" =>"卸業者",
        "distributorMCode" =>"卸業者管理コード",
        "price" =>"購買価格",
        "unitPrice" =>"単価",
        "medicineCategory" =>"保険請求分類（医科）",
        "homeCategory" =>"保険請求分類（在宅）",
        "measuringInst" =>"測定機器名",
        "notice" =>"特記事項",
    ];

/* 
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
 */

    public function register($vars) {
        $user = ($this->request->user());
        $input = $this->request->only($this->input);
        if($this->request->confirm){
            $validate = SiValidator::make(
                $input,
                $this->rules,
                $this->labels
            );
        }
        $sessionItemData = $this->request->session()->get($this->itemData);
        $itemId = ($sessionItemData['itemId']) ? $sessionItemData['itemId'] : $this->request->get('itemId');
        $data = SpiralDB::title('NJ_itemDB')->value(['itemId', 'tenantId', 'makerName', 'itemName', 'itemCode', 'itemStandard', 'itemJANCode', 'quantityUnit', 'catalogNo', 'serialNo', ])
            ->where('itemId', $itemId)->get();
        $item = $data->first()->toArray();
        $this->request->session()->put($this->itemData, $item);

        $distributor = SpiralDb::title('NJ_distributorDB')
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->value([
                'distributorId',
                'distributorName',
            ])->get()->toArray();

        if(($user->userPermission == "1" || $user->userPermission == "3")/*  && $item->tenantId === $this->request->user()->tenantId */)
        {
            $body = view('html/Product/PriceAndInHospitalItemRegist/input' , [
                'distributor' => $distributor,
                'input' => $this->request->all(),
                'item' => $item,
                'session' => $session, //一時的に
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
        $this->request->session()->put($this->formName, $input);
        
        $validate = SiValidator::make(
            $input,
            $this->rules,
            $this->labels
        );

        if( $validate->isError() ){
            $this->request->set('confirm' , true);
            Router::redirect('/product/PriceAndInHospitalRegist/input',$this->request);
            exit;
        }

        $distributor = SpiralDb::title('NJ_distributorDB')->where('hospitalId', $this->request->user()->hospitalId)
            ->where('distributorId', $this->request->get('distributorId'))
            ->value([
                'distributorName',
            ])->get()->toArray();

        if($user->userPermission == "1" || $user->userPermission == "3")
        {
            $body = view('html/Product/PriceAndInHospitalItemRegist/confirm' , [
                'distributor' => $distributor,
                'input' => $this->request->all(),
                'item' => $this->request->session()->get($this->itemData),
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
            Router::redirect('/product/PriceAndInHospitalRegist/input',$this->request);
            exit;
        }else{
            $hospitalId = $this->request->user()->hospitalId;
            $item = $this->request->session()->get($this->itemData);
            $input["quantityUnit"] = $item["quantityUnit"];
            $repository = new PriceAndInHospitalItemRepository();
            $result = $repository -> saveToArray($hospitalId, $item["itemId"], $input);
        }

        $this->request->session()->forget($this->formName);
        $this->request->session()->forget($this->itemData);

        $body = view('html/Product/PriceAndInHospitalItemRegist/thanks', ["result" => $result,]);
        echo view("html/Common/Template", compact("body"), false);
    }

}