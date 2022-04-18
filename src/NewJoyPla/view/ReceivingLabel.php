<?php
$original_design = <<<EOM
	<div class="printarea uk-margin-remove">
		<span>%JoyPla:distributorName%</span><br>
		<span>メーカー名：%JoyPla:itemMaker%</span><br>
		<span>商品名：%JoyPla:itemName%</span><br>
		<span>規格：%JoyPla:itemStandard%</span><br>
		<span>商品コード：%JoyPla:itemCode%</span>
		<span>入数：%JoyPla:quantity%%JoyPla:quantityUnit%</span><br>
		<span>%JoyPla:lotNumber% %JoyPla:lotDate%</span>
		<span>%JoyPla:nowTime%</span><br>
		<div class="uk-text-center" id="barcode_%JoyPla:num%">%JoyPla:barcodeId%</div>
	</div>
EOM;
if($hospital_data->labelDesign1 != ''){
    $original_design = htmlspecialchars_decode($hospital_data->labelDesign1);
}
?>
<style>
@media print{
    body{
        zoom: 1 !important; /* Equal to scaleX(0.7) scaleY(0.7) */
    }
}
html {
    color: #000;
}
#createLabel{
    font-size:0;
}
.a4area{
    break-after: always;
}
.printarea{
    vertical-align: top;
}
.printarea{
    font-size:12px;
    border: 1px solid gray;
    padding: 5px;
}
.font-size-16{
    font-size:16px;
}
</style>
<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
        <div class="uk-container uk-container-expand">
            <h1 class="no_print">
                入庫ラベル発行
            </h1>
            <div class="no_print">
            <form class="uk-form-stacked uk-child-width-1-2 uk-width-1-1 uk-width-1-2@l" uk-grid :action="form_action" method="post" name="setting_form" id="app">
                <input type="hidden" :value="receiving_id" name="receivingId">
                <input type="hidden" value="" name="print_counts">
                <div class="uk-width-1-1">
                    <div class="uk-margin">
                        <table class="uk-table uk-table-divider"> 
                            <tr>
                                <th>id</th>
                                <th>検収番号</th>
                                <th>商品情報</th>
                                <th>印刷枚数</th>
                            </tr>
                            <tr v-for="( elm , index ) in print_counts">
                                <td>{{ index + 1 }}</td>
                                <td>{{ elm.receivingHId }}</td>
                                <td>
                                    <div uk-grid margin="0">
                                        <div class="uk-width-1-4 uk-text-muted">メーカー</div>
                                        <div class="uk-width-3-4">{{ get_item_object(elm.receivingNumber).makerName }}</div>
                                        <div class="uk-width-1-4 uk-text-muted">商品名</div>
                                        <div class="uk-width-3-4">{{ get_item_object(elm.receivingNumber).itemName }}</div>
                                        <div class="uk-width-1-4 uk-text-muted">製品コード</div>
                                        <div class="uk-width-3-4">{{ get_item_object(elm.receivingNumber).itemCode }}</div>
                                        <div class="uk-width-1-4 uk-text-muted">規格</div>
                                        <div class="uk-width-3-4">{{ get_item_object(elm.receivingNumber).itemStandard }}</div>
                                        <div class="uk-width-1-4 uk-text-muted">JANコード</div>
                                        <div class="uk-width-3-4">{{ get_item_object(elm.receivingNumber).itemJANCode }}</div>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" v-model="elm.count" class="uk-input">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div>
                    <div class="uk-margin">
                        <label class="uk-form-label">印刷タイプ</label>
                        <div class="uk-form-controls">
                            <select name="printType" class="uk-select" v-model="label_setting.printType">
                                <option value="1">ラベルプリンター</option>
                                <option value="2">A4印刷</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label">バーコードの高さ</label>
                        <div class="uk-form-controls">
                            <input type="number" name="barcodeHeight" v-model="label_setting.barcodeHeight" class="uk-input uk-width-2-3"><span class="uk-text-bottom">px</span>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="uk-margin">
                        <label class="uk-form-label">ラベルの幅</label>
                        <div class="uk-form-controls">
                            <input type="number" name="labelwidth" v-model="label_setting.labelwidth" class="uk-input uk-width-2-3"><span class="uk-text-bottom">mm</span>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label">ラベルの最小高さ</label>
                        <div class="uk-form-controls">
                            <input type="number" name="labelmheight" v-model="label_setting.labelmheight" class="uk-input uk-width-2-3"><span class="uk-text-bottom">mm</span>
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-1 uk-text-center">
                    <button v-on:click="setting" type="button" class="uk-button uk-button-small ">反映</button>
                </div>

            </form>
            </div>
            <div class="uk-margin-bottom no_print" uk-grid>
                <div class="uk-width-1-2@m">
                    <button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
                </div>
            </div>
            <div id="createLabel">
            <?php 
            $num = 1 ;
            $nowTime = date('Y年m月d日 H時i分s秒');
            foreach($receiving_items as $item){
                $barcodeId = '';
                $quantity = '';
                if($item->totalReturnCount == null){
                    $item->totalReturnCount  = 0;
                }
                
                $quantityVal = $item->quantity;
                
                if($quantityVal >= 10000 ){
                    $quantity = '9999';
                } else if($quantityVal < 1 ){
                    $quantity = str_pad(1 , 4, "0", STR_PAD_LEFT);
                } else {
                    $quantity = str_pad($quantityVal , 4, "0", STR_PAD_LEFT);
                }
                
                $max = 0;
                $max = $item->print_count;
                for($rnum = 1 ; $rnum <= $max; $rnum++){
                    $receiving_num = str_replace('rec_', '', $item->receivingNumber);
                    $barcodeId = "20".$receiving_num; // 検収書から生成できるバーコードは20にする。
                    
                    if(($num - 1 )% 2 == 0){
                        echo "<div>";
                    }

                    $officialFlag = '';
                    if($item->officialFlag == '1'){
                        $officialFlag = '償還';
                    }

                    $design = $original_design;
                    $design = str_replace('%JoyPla:nowTime%',			$nowTime, 									$design);//バーコードの値
                    $design = str_replace('%JoyPla:barcodeId%',			$barcodeId, 								$design);//バーコードの値
                    $design = str_replace('%JoyPla:num%',				$num, 										$design);//枚目
                    $design = str_replace('%JoyPla:inHPId%',			$item->inHospitalItemId, 					$design);//院内商品ID
                    $design = str_replace('%JoyPla:itemName%',			$item->itemName,                    		$design);//商品名
                    $design = str_replace('%JoyPla:itemCode%',			$item->itemCode, 		                    $design);//製品コードb
                    $design = str_replace('%JoyPla:itemStandard%',		$item->itemStandard,	                    $design);//商品規格
                    $design = str_replace('%JoyPla:itemJANCode%',		$item->itemJANCode, 	                    $design);//JANコードb
                    $design = str_replace('%JoyPla:itemUnit%',			$item->itemUnit, 		                    $design);//個数単位
                    $design = str_replace('%JoyPla:quantity%',			(int)$quantity, 		                    $design);//入り数
                    $design = str_replace('%JoyPla:catalogNo%',			$item->catalogNo, 		                    $design);//カタログ名
                    $design = str_replace('%JoyPla:labelId%',			$item->labelId, 		                    $design);//ラベルID
                    //$design = str_replace('%JoyPla:printCount%',		$item->printCount,					    	$design);//印刷数
                    $design = str_replace('%JoyPla:distributorName%',	$item->distributorName,				       	$design);//卸業者名
                    $design = str_replace('%JoyPla:itemMaker%',			$item->makerName, 		                    $design);//メーカー名
                    $design = str_replace('%JoyPla:quantityUnit%',		$item->quantityUnit,	                    $design);//入数単位
                    //$design = str_replace('%JoyPla:sourceDivisionName%',$item->sourceDivisionName,				    $design);//払い出し元部署
                    //$design = str_replace('%JoyPla:sourceRackName%',	$item->sourceRackName, 					    $design);//払い出し元部署棚
                    $design = str_replace('%JoyPla:divisionName%',		$item->divisionName,						$design);//払い出し先部署 
                    //$design = str_replace('%JoyPla:rackName%',			$item->rackName, 							$design);//払い出し先部署棚
                    //$design = str_replace('%JoyPla:constantByDiv%',		$item->constantByDiv, 					    $design);//払い出し先部署定数
                    $design = str_replace('%JoyPla:officialFlag%',		$officialFlag,								$design);//償還フラグ
                    $design = str_replace('%JoyPla:officialFlag:id%',   $item->officialFlag,					    $design);//償還フラグ id
                    $design = str_replace('%JoyPla:lotNumber%',			$item->lotNumber, 		                    $design);//ロット
                    $design = str_replace('%JoyPla:lotDate%',			$item->lotDate, 		                    $design);//使用期限

                    echo $design;

                    if(($num)% 2 == 0){
                        echo "</div>";
                    }
                    $num ++ ;
                    
                }
            }
            ?>
            </div>
        </div>
    </div>
</div>
<script>

    let register_data = {
        print_counts : <?php echo json_encode($print_counts) ?>,
        receiving_items : <?php echo json_encode($receiving_items) ?>,
        form_action: "<?php echo $form_action ?>",
        receiving_id: "<?php echo $receiving_id ?>",
        count : "<?php echo  $num ?>",
    };
    var app = new Vue({
        el: '#app',
        data: {
            print_counts: register_data.print_counts,
            receiving_items: register_data.receiving_items,
            form_action: register_data.form_action,
            receiving_id: register_data.receiving_id,
            count: register_data.count,
            label_setting: {
                    "barcodeHeight": 50,
                    "labelwidth": 85,
                    "labelmheight": 50,
                    "printType": 1,
                }
        },
        created(){
            document.querySelector('input[name=print_counts]').value = JSON.stringify(this.print_counts);
            let storage_item = localStorage.getItem("joypla_LabelCreate");
            if(storage_item){
                this.label_setting = JSON.parse(localStorage.getItem("joypla_LabelCreate"));
            }
            
            const style = document.createElement('style');
            style.innerHTML = `
            .printarea {
                width:`+this.label_setting.labelwidth+`mm;
                min-height:`+this.label_setting.labelmheight+`mm;
            }
            `;

            if(this.label_setting.printType == '2'){
                style.innerHTML = `
                .printarea {
                    width:`+this.label_setting.labelwidth+`mm;
                    min-height:`+this.label_setting.labelmheight+`mm;
                    display: inline-block;
					page-break-after: auto !important;
                }
                `;
            }
            document.body.appendChild(style);

            //let count = $('#createLabel').children().length;
            let num;
            for(let i = 1 ; i < this.count ; i++){
                if($('#barcode_' + i).length > 0){
                    num = $('#barcode_' + i).text();
                    $('#barcode_' + i).html('<svg id="barcode_area_'+i+'"></svg>');
                    this.generate_barcode('barcode_area_' + i,num);
                    //$('td#barcode_' + i +' div').barcode(num, "ean13",{barWidth: 2 ,barHeight: 40 , output: 'css'});
                }
            }
            
            if(document.getElementsByName('printType')[0].value == 2){
                var pointHeight = 0;
                $("#createLabel > div ").each(function(){
                    $(".printarea",this).each(function(){
                        if(pointHeight < $(this).height() ){
                            pointHeight = $(this).height();
                        }
                    });
                    $(".printarea",this).css({ height:pointHeight+"px" });
                    pointHeight = 0;
                });
            }
        },
        methods: {
            generate_barcode(idname,value)
            {
                let height = this.label_setting.barcodeHeight;
                
                JsBarcode("#"+idname,value,{format: "ITF", width: 1.8, height: height,fontSize: 14});
            },
            get_item_object : function(receiving_number)
            {
                if(!receiving_number){ return {} }
                return this.receiving_items.find(s => s.receivingNumber === receiving_number);
            },
            setting: function()
            {
                document.querySelector('input[name=print_counts]').value = JSON.stringify(this.print_counts);
                localStorage.setItem("joypla_LabelCreate", JSON.stringify(this.label_setting));
                document.setting_form.submit();
            }
        }
    });
</script>