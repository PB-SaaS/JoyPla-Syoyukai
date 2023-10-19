<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;
 
use Exception;
use framework\Http\Request;
use framework\Http\Controller;
use framework\Http\View;
 
class MedicalLabelController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
 
    public function index(array $vars)
    {

        // throw new Exception("testItomasayuki");

        $payoutId ="05652f5f66c6165";
        $inHospitalItems= $this->mockHospitalItem();
        $labeldesign=$this->defaultDesign();

        $body = View::forge('labelPrint/medical/Label', [
            'payoutId' => $payoutId,
            'inHospitalItems' => $inHospitalItems,
            'totalPrintCount' => count($inHospitalItems),
            'labelHtml' => $this->convertKeyword($labeldesign , $inHospitalItems),
            // 'labelHtml' => $labeldesign,
        ], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    //  %%で囲われた変数名の中身が空文字の時にどうするか確認する。
    private function defaultDesign()
    {
        return <<<EOM
    <div class="printarea uk-margin-remove">
        <div class="uk-child-width-1-2 uk-grid" uk-grid="">
            <div class="uk-first-column">
                <b class="font-size-16">償還</b><br>
                <span>%itemName%</span><br>
                <span>%makerName%</span><br>
                <span>%catalogNo%</span><br>
                <span>%itemStandard%</span><br>
                <span>%medicineCategory%</span><br>
            </div>
            <div class="uk-text-right uk-padding-remove">
                <span>%printDate%</span><br>
                <b>入数単位:1%quantityUnit%</b><br>
                <b>償還価格:%officialprice%円</b><br>
            </div>
        </div>
    
        <br>
        <span>%distributorName%</span><br>

	</div>
EOM;
    }

    
    private function mockHospitalItem()
    {
        return [
            [
                "id" => "1",
                "registrationTime" => "2023年10月6日 09時10分53秒",
                "updateTime" => "2023年10月18日 13時30分30秒",
                "itemId" => "item_0000000002",
                "priceId" => "price_0000000001",
                "inHospitalItemId" => "00000001",
                "authKey" => "h4a23cg4ynf35326",
                "hospitalId" => "hospital_0000000001",
                "medicineCategory" => "アルコール毛細管体温計\n単回使用気管切開チューブ",
                "homeCategory" => "",
                "notUsedFlag" => "0",
                "notice" => "",
                "HPstock" => "3073",
                "unitPrice" => "1000",
                "measuringInst" => "",
                "makerName" => "エンブレム株式会社",
                "itemName" => "消毒用アルコール綿",
                "itemCode" => "",
                "itemStandard" => "M",
                "itemJANCode" => "4987603115095",
                "officialFlag" => "0",
                "officialpriceOld" => "",
                "officialprice" => "2000",
                "catalogNo" => "01231809",
                "serialNo" => "",
                "lotManagement" => "",
                "category" => "1",
                "distributorId" => "dis_1014796949",
                "distributorName" => "Cell医療薬品株式会社",
                "price" => "1000",
                "quantity" => "100",
                "itemUnit" => "枚",
                "quantityUnit" => "箱",
                "distributorMCode" => "",
                "inItemImage" => "",
                "labelId" => "00000001",
                "payout" => [
                    [
                        "payoutItemId" => "payout_0000000001",
                        "inHospitalItemId" => "00000001",
                        "lotNumber" => "rotto012",
                        "lotDate" => "2099年10月01日",
                        "print" => [
                            [
                                "count" => "2",
                                "print" => "1"
                            ]
                        ]
                    ]
                ],
                "target" => [
                    "divisionId" => "div_0833326238",
                    "divisionName" => "リハビリテーション科",
                    "inHospitalItemId" => "00000001",
                    "rackName" => "",
                    "constantByDiv" => "10"
                ],
                "source" => [
                    "divisionId" => "div_0053498963",
                    "divisionName" => "内科",
                    "inHospitalItemId" => "00000001",
                    "rackName" => "",
                    "constantByDiv" => "10"
                ]
                ],
                
        ];
    }
    
    private function convertKeyword(string $template, array $inputData){
        $html = '';
        foreach($inputData as $key => $input)
        {
            $design = $template;
            $design = str_replace('%itemName%',			$input['itemName'],                 $design);//商品名
            $design = str_replace('%makerName%',		$input['makerName'], 		        $design);//メーカー名
            $design = str_replace('%printDate%',		$this->getToday(), 					$design);//印刷日
            $design = str_replace('%quantityUnit%',		$input['quantityUnit'],	            $design);//入数単位
            $design = str_replace('%officialprice%',	number_format_jp((float)$input['officialprice']),  $design);//償還価格
            $design = str_replace('%catalogNo%',		$input['catalogNo'], 		        $design);//カタログNo
            $design = str_replace('%itemStandard%',		$input['itemStandard'], 		    $design);//規格
            $replacedMedicineCategory = str_replace("\n", '<br>', $input['medicineCategory']);
            $design = str_replace('%medicineCategory%',	$replacedMedicineCategory, 		$design);//(特定保険材料名称「保険請求分類(医科)」)
            $design = str_replace('%distributorName%',		$input['distributorName'], 		        $design);//卸業者            
            $html .= $design;
        }   
        return $html;
    }

    private function getToday($timezone = 'UTC') {
        date_default_timezone_set($timezone);
        return date('y/m/d');
    }

    
}