<style>
    .id{
      min-width: 24px;
      max-width: 24px;
    }
    
    .shouhin-data .itemName{
      font-size: 14px;
    }
    .shouhin-data .makerName{
      font-size: 12px;
      
    }
    .shouhin-data .tana{
      font-size: 8px;
    }
    .shouhin-data .itemCode{
      font-size: 8px;
      
    }
    .shouhin-data .constant{
      font-size: 8px;
      
    }
    .shouhin-data .quantity{
      font-size: 8px;
      
    }
    .shouhin-data .price{
      font-size: 8px;
      
    }
    .shouhin-data .JANCode{
      font-size: 8px;
      
    }
    .shouhin-data .officialFlag{
      font-size: 8px;
      
    }
    .itemCount{
      position: relative;
    }
    .itemCount->after {
       content: attr(data-format); /* ここが重要!! */
       width: 10%;
       height: 20px;
       position: absolute;
       bottom: 4px;
    }
    .itemCountInput{
      width: 90%;
    }
    .uk-table th, .uk-table td{
      word-break: break-word;
      padding: 12px 8px;
      vertical-align: middle;
    }
    .uk-table tfoot tr{
      border-bottom: #e5e5e5 1px solid;
      border-top: #e5e5e5 1px solid;
    }
    
    table.uk-table {
      counter-reset: rowCount;
    }

    table.uk-table > tbody > tr {
      counter-increment: rowCount;
    }

    table.uk-table > tbody > tr > td:first-child::before {
      content: counter(rowCount);
    }
</style>
<div class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><span>棚卸内容入力</span></li>
            </ul>
            <h2 class="page_title">棚卸内容入力</h2>
            <hr>
            <div class="uk-width-1-3@m">
                <div class="uk-margin">
                    <select class="uk-select" name="busyo" v-model="divisionId" v-bind:disabled="division_disabled">
                        <option value="">----- 部署選択 -----</option>
                    <?php
                        foreach($division->data as $data)
                        {
                            if($data->divisionType === '1')
                            {
                                echo '<option value="'.$data->divisionId.'">'.$data->divisionName.'(大倉庫)</option>';
                                echo '<option value="" disabled>--------------------</option>';
                            }
                        }
                        foreach($division->data as $data)
                        {
                            if($data->divisionType === '2')
                            {
                                echo '<option value="'.$data->divisionId.'">'.$data->divisionName.'</option>';
                            }
                        }
                    ?>
                    </select>
                </div>
            </div>
            <div class="uk-margin-bottom">
                <div class="" uk-margin>
                    <button class="uk-button uk-button-default " onclick="inventory.sanshouClick()">商品マスタを開く</button>
                    <button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
                    <button class="uk-button uk-button-primary" onclick="inventory.sendInventory()">棚卸入力</button>
                </div>
            </div>

            <div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
                <form action='#' method="post" onsubmit="inventory.barcodeSearch(); return false">
                    <input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." autofocus="true" name="barcode" autocomplete="off">
                    <button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
                </form>
            </div>

            <div class="uk-margin uk-text-right">
                <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #modal-gs1128">GS1-128で照合</button>
            </div>

            <div class="shouhin-table uk-width-expand uk-overflow-auto">
                <table class="uk-table uk-table-striped uk-text-nowrap">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>メーカー</th>
                            <th>商品名</th>
                            <th>製品コード</th>
                            <th>規格</th>
                            <th>入数</th>
                            <th>棚卸数量</th>
                            <th>価格</th>
                            <th>単価</th>
                            <th>JANコード</th>
                            <th>卸業者</th>
                            <th>ロット番号</th>
                            <th>使用期限</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                        </tr>
                        <tr>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                        </tr>
                        <tr>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                            <td>&emsp;</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- This is a button toggling the modal with the default close button -->
<!-- This is the modal with the default close button -->
<div id="modal-gs1128" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <form onsubmit="inventory.gs1_128($('#GS1-128').val());return false;" action="#">
            <button class="uk-modal-close" type="button" uk-close></button>
            <h2 class="uk-modal-title">GS1-128 読取</h2>
            <input type="text" class="uk-input" placeholder="GS1-128" id="GS1-128" autofocus="true">
            <p class="uk-text-right">
                <button class="uk-button uk-button-primary" type="button" onclick="inventory.gs1_128($('#GS1-128').val());">反映</button>
            </p>
        </form>
    </div>
</div>

<script>
class InventoryRegister {
    constructor()
    {
        this.canAjax = true;
        this.gs1128_object = {};
        this.listObject = {};
        this.dataKey = ['id','maker','shouhinName','code','kikaku','irisu','count','kakaku','unitPrice','jan','oroshi'];
        this.index = 1;
        this.useUnitPrice = parseInt(<?php echo json_encode($useUnitPrice); ?>);
    }

    sanshouClick(){
    // 参照マスタを別ウィンドウで開く
        window.open('%url/rel:mpgt:page_175973%', '_blank','scrollbars=yes,width=1220,height=600');
    }

    delTr(index , elm){
        elm.parentElement.parentElement.remove();
        delete this.listObject[index];
    }

    chkNotLotRow(itemKey) {
        let index = null;
        $(document).find('.item_' + itemKey).each(function() {
            let lotNum = $(this).parents('tr').find('.lot_' + itemKey).val();
            let lotDate = $(this).parents('tr').find('.lotDate_' + itemKey).val();
            if (!lotNum && !lotDate) {
                let target = $(this).parents('tr');
                index = $(document).find('.shouhin-table table tbody tr').index(target);
                return false;
            }
        });
        return index;
    }

    addTr(object, type, count) {
console.log(object);
        let tmp = this;
        if ( type === 1 ) { //商品マスタ
            let chk = tmp.chkNotLotRow(object.recordId);
            if ( chk !== null ) { return; }
        }
        if ( type === 2 ) { //バーコード検索
            let chk = tmp.chkNotLotRow(object.recordId);
            if ( chk !== null ) {
                let target = $('.shouhin-table table tbody tr').eq(chk).find('.item_' + object.recordId);
                let setCount = parseInt(target.val()) + count;
                target.val(setCount).css({'color':'rgb(68, 68, 68)', 'background-color':'rgb(255, 204, 153)'});
                $(window).scrollTop($(target).offset().top - 100);
                return;
            }
        }
      
        tmp.listObject[tmp.index] = object;
        tmp.listObject[tmp.index].no = tmp.index;
        tmp.listObject[tmp.index].countNum = count;
//        if (object.lotFlag === 'はい') { 
        if (object.lotFlagBool === '1') { 
            tmp.listObject[tmp.index].lotFlag = 1;
        } else {
            tmp.listObject[tmp.index].lotFlag = 0;
        }
        let trElm = document.createElement('tr'); 
        trElm.id = 'tr_' + tmp.index;
        let tdElm = '';
        let html = '';
        let input = '';
        let span = '';
        let text = '';
        for (let i = 0 ; i < tmp.dataKey.length; i++) {
            tdElm = document.createElement('td');
            html = document.createTextNode('');
        
            if (tmp.dataKey[i] === 'id') {
            //html = document.createTextNode(listObject[object.recordId].row);
            } else if (tmp.dataKey[i] === 'count') {
                html = document.createElement('div');
                input = document.createElement('input');
                input.type = 'number';
                input.step = '1';
                input.className = 'uk-input item_' + object.recordId;
                input.name = 'count';
                input.style = 'width:96px';
                input.min = 0;
                input.value = count;
                if (count > 0) { 
                    input.style.backgroundColor = 'rgb(255, 204, 153)';
                    input.style.color = 'rgb(68, 68, 68)';
                }
                input.onchange  = function () {
                      changeForInputNumber(this);
                  };
                span = document.createElement('span');
                span.innerText = tmp.listObject[tmp.index].unit;
                span.className = 'uk-text-bottom';
                html.appendChild(input);
                html.appendChild(span);
            } else {
                text = '';
                if (tmp.dataKey[i] === 'kakaku') {
                    text += '￥';
                    text += price_text(tmp.listObject[tmp.index][tmp.dataKey[i]]);
                    text += '/'+ tmp.listObject[tmp.index].itemUnit;
                } else if (tmp.dataKey[i] === 'unitPrice') {
                    text += '￥';
                    text += (tmp.useUnitPrice)
                      ? price_text(tmp.listObject[tmp.index][tmp.dataKey[i]]) 
                      : price_text( String(parseInt(tmp.listObject[tmp.index]['kakaku']) / parseInt(tmp.listObject[tmp.index]['irisu'])) );
                } else if(tmp.dataKey[i] === 'teisu' || tmp.dataKey[i] === 'irisu') {
                    text += tmp.listObject[tmp.index][tmp.dataKey[i]];
                    text += tmp.listObject[tmp.index].unit;
                } else {
                   text += tmp.listObject[tmp.index][tmp.dataKey[i]];
                }
                html = document.createTextNode(text);
            }
        
            tdElm.appendChild(html);
            trElm.appendChild(tdElm);
        }

        tdElm = document.createElement('td');
        input = document.createElement('input');
        input.className = 'uk-input lot_' + object.recordId;
        input.name = 'lot';
        input.type = 'text';
        input.maxLength = 20;
        input.style.width = '184px';
        if ( type === 4 && object.lot ) {
            input.value = object.lot;
            input.style.backgroundColor = 'rgb(255, 204, 153)';
            input.style.color = 'rgb(68, 68, 68)';
        }
        input.onchange  = function () {  
              $(this).css({'background-color':'rgb(255, 204, 153)'});
          };
        tdElm.appendChild(input); 
        trElm.appendChild(tdElm); 
      
        tdElm = document.createElement('td');
        input = document.createElement('input');
        input.className = 'uk-input lotDate_' + object.recordId;
        input.name = 'lotDate';
        input.type = 'date';
        input.style.width = '184px';
        if ( type === 4 && object.lotDate ) {
            input.value = object.lotDate;
            input.style.backgroundColor = 'rgb(255, 204, 153)';
            input.style.color = 'rgb(68, 68, 68)';
        }
        input.onchange  = function () {  
              $(this).css({'background-color':'rgb(255, 204, 153)'});
          };
        tdElm.appendChild(input); 
        trElm.appendChild(tdElm);
      
        if ( type === 4 ) {
            delete object['lot'];
            delete object['lotDate'];
        }
      
        tdElm = document.createElement('td');
        input = document.createElement('input');
        input.type = 'button';
        input.value = '削除';
        input.className = 'uk-button uk-button-danger uk-button-small';
        input.onclick = function() {
              tmp.delTr(object.no, this);
          }
        tdElm.appendChild(input);
        trElm.appendChild(tdElm);
      
        tdElm = document.createElement('td');
        input = document.createElement('input');
        input.type = 'button';
        input.value = '追加';
        input.className = 'uk-button uk-button-default uk-button-small';
        input.onclick = function(){
            let copy = { ...object };
            tmp.addTr(copy, 3, 1);
        }
        tdElm.appendChild(input);
        trElm.appendChild(tdElm);
      
        if ( type === 3 ) { //追加ボタン
            var from = event.target;
            $('.shouhin-table table tbody').find(from).parents('tr').after(trElm);
        } else {
            $('.shouhin-table table tbody').append(trElm);
        }
      
        if ( count > 0 ) { $(window).scrollTop($(trElm).offset().top - 100); }
        tmp.index++;
    }

    inventoryCheck(){
        let tmp = this;
        if ($('select[name="busyo"]').val()){ 
        } else {
            UIkit.modal.alert('部署を選択してください');
            return false ;
        }
      
        if (Object.keys(tmp.listObject).length === 0) {
            UIkit.modal.alert('商品を選択してください');
            return false ;
        }
      
        let chkLot = true;
        Object.keys(tmp.listObject).forEach(function (key) {
            if (tmp.listObject[key]['lotFlag']) {
                if ((!tmp.listObject[key]['lotNumber'] && tmp.listObject[key]['lotDate'])
                  || (tmp.listObject[key]['lotNumber'] && !tmp.listObject[key]['lotDate'])) {
                    chkLot = false;
                }
            }
        });
      
        if (!chkLot) {
            UIkit.modal.alert('ロット情報を入力してください');
            return false ;
        }
      
        chkLot = true;
        let regex = /^[0-9a-zA-Z]+$/;
        Object.keys(tmp.listObject).forEach(function (key) {
            if (tmp.listObject[key]['lotNumber']) {
                if ((!regex.test(tmp.listObject[key]['lotNumber'])) ||
                    (encodeURI(tmp.listObject[key]['lotNumber']).replace(/%../g, '*').length > 20)) {
                    chkLot = false;
                }
            }
        });
      
        if (!chkLot) {
            UIkit.modal.alert('ロット番号の入力を確認してください');
            return false ;
        }
      
        return true;
      
    }

    sendInventory(){
        let tmp = this;
        if (!tmp.canAjax) { 
            console.log('通信中');
            return;
        }
        loading();
        tmp.setRegData();
        if (!tmp.inventoryCheck()) {
            loading_remove();
            return false;
        }
      
        let inventory = tmp.mergeLot();
        tmp.canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
        $.ajax({
            async: false,
            url: "<?php echo $api_url ?>",
            type:'POST',
            data: {
                _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                Action : "inventoryRegistAPI",
                inventory : JSON.stringify( objectValueToURIencode(inventory) ),
                divisionId : $('select[name="busyo"]').val()
            },
            dataType: 'json'
        })
        // Ajaxリクエストが成功した時発動
        .done( (data) => {
            if (!data.result) {
                UIkit.modal.alert("棚卸登録に失敗しました").then(function(){
                    tmp.canAjax = true; // 再びAjaxできるようにする
                });
                return false;
            }
            UIkit.modal.alert("棚卸登録が完了しました").then(function(){
                tmp.canAjax = true; // 再びAjaxできるようにする
                tmp.dataReset();
            });
        })
        // Ajaxリクエストが失敗した時発動
        .fail( (data) => {
            UIkit.modal.alert("棚卸登録に失敗しました").then(function(){
                tmp.canAjax = true; // 再びAjaxできるようにする
            });
        })
        // Ajaxリクエストが成功・失敗どちらでも発動
        .always( (data) => {
            loading_remove();
        });
    }

    dataReset(){
        ('table.uk-table tbody tr').remove();
        this.listObject = {};
        loading_remove();
    }

    gs1_128(gs1128){
        let tmp = this;
        this.gs1128_object = {};
        
        if (Object.keys(tmp.listObject).length === 0) {
            UIkit.modal.alert('先に商品を選択してください。');
            return false ;
        }
        
        if (gs1128.indexOf("]C1") !== 0) {
        //UIkit.modal.alert("GS1-128ではありません");
        //return ;
            return gs1_128("]C1"+gs1128);
        } else {
            gs1128 = gs1128.slice( 3 );
            let obj = check_gs1128(gs1128);
            let objkey = null;
            let setObj = {};
        
            if(!obj.hasOwnProperty("01")) {
                UIkit.modal.alert("商品情報が含まれておりませんでした。").then(function(){
                UIkit.modal($('#modal-gs1128')).show();
            });
            return;
        }
        
        let searchJan = addCheckDigit(obj["01"]);
        console.log(searchJan);
        Object.keys(tmp.listObject).forEach(function (key) {
            if (searchJan == tmp.listObject[key]["jan"]) {
                objkey = tmp.listObject[key]["recordId"];
                setObj = { ...tmp.listObject[key] };
            }
        });
        
        if (!objkey) {
            UIkit.modal.alert("対象の発注商品が見つかりませんでした。").then(function(){
                UIkit.modal($('#modal-gs1128')).show();
            });
            return;
        }

        let existflg = false;
        let objLot = (obj["10"] === void 0) ? "" : obj["10"]; //lotNumber
        let objLotDate = (obj["17"] === void 0) ? "" : changeDate(obj["17"]); //lotDate

        $(document).find('.lot_' + objkey).each(function() {
            let addRowLot = $(this).val();
            let addRowLotDate = $(this).parents('tr').find('.lotDate_' + objkey).val();
            let addRowNum = parseInt($(this).parents('tr').find('.item_' + objkey).val());

            if (addRowNum === 0 && !addRowLot && !addRowLotDate) {
                $(this).val(objLot).css({'color':'rgb(68, 68, 68)', 'background-color':'rgb(255, 204, 153)'});
                $(this).parents('tr').find('.lotDate_' + objkey).val(objLotDate).css({'color':'rgb(68, 68, 68)', 'background-color':'rgb(255, 204, 153)'});
                $(this).parents('tr').find('.item_' + objkey).val(parseInt(setObj.irisu)).css({'color':'rgb(68, 68, 68)', 'background-color':'rgb(255, 204, 153)'});
                $(window).scrollTop($(this).offset().top - 100);
                existflg = true;
               return false;
            }
            if ((addRowLot == objLot) && (addRowLotDate == objLotDate)) {
                let num = addRowNum + parseInt(setObj.irisu);
                $(this).parents('tr').find('.item_' + objkey).val(num).css({'color':'rgb(68, 68, 68)', 'background-color':'rgb(255, 204, 153)'});
                $(window).scrollTop($(this).offset().top - 100);
                existflg = true;
                return false;
            }
        });

        if (!existflg) {
            setObj.lot = objLot;
            setObj.lotDate = objLotDate;
            tmp.addTr(setObj, 4, parseInt(setObj.irisu));
        }

        $('.select_items').hide();
        $('.select_items select').val('');
        $('#GS1-128').val('');
        document.getElementById('GS1-128').focus();
      }
    }

    changeDate(text) {
        if (text == null) { return ""; }
        if (text.length == "6") { text = 20 + text; }
        let date = text.slice(6, 8);
        if (parseInt(text.slice(6, 8)) == 0) { date = '01'; }
        return text.slice(0, 4) + "-" + text.slice(4, 6) + "-" + date;
    }

    setRegData() {
        let tmp = this;
        $(document).find('.shouhin-table table tbody tr').each(function() {
            let row = $.trim(($(this).attr('id')).replace('tr_', ''));
            let num = $(this).find('input[name="count"]').val();
            let lot = $(this).find('input[name="lot"]').val();
            let date = $(this).find('input[name="lotDate"]').val();
            tmp.listObject[row].countNum = parseInt(num);
            tmp.listObject[row].lotNumber = lot;
            tmp.listObject[row].lotDate = date;
        });
    }

    mergeLot(){
        let tmp = this;
        let regObj = {};
        Object.keys(tmp.listObject).forEach(function (key) {
            let itemId = tmp.listObject[key]['recordId'];
            if (regObj[itemId] === void 0) { regObj[itemId] = {}; }
            if (tmp.listObject[key].lotNumber && tmp.listObject[key].lotDate) {
                let lotKey = tmp.listObject[key].lotNumber + tmp.listObject[key].lotDate;
                let temp = Object.entries(regObj[itemId]);
                let chkLotDup = temp.findIndex(([id, data]) => data.lotNumber == tmp.listObject[key].lotNumber && data.lotDate == tmp.listObject[key].lotDate);
                if (chkLotDup === -1) {
                    regObj[itemId][lotKey] = { ...tmp.listObject[key] };
                } else {
                    regObj[itemId][lotKey]['countNum'] = regObj[itemId][lotKey]['countNum'] + tmp.listObject[key].countNum;
                }
            }
        
            if (!tmp.listObject[key].lotNumber && !tmp.listObject[key].lotDate) {
                if (regObj[itemId][0] === void 0) {
                    regObj[itemId][0] = {};
                    regObj[itemId][0] = { ...tmp.listObject[key] };
                } else {
                    regObj[itemId][0]['countNum'] = regObj[itemId][0]['countNum'] + tmp.listObject[key].countNum;
                }
            }
        });
        return regObj;
    }

}

let inventory = new InventoryRegister();

$(document).on('change', 'input[type="number"]', function() {
    $(this).css({'color':'rgb(68, 68, 68)', 'background-color':'rgb(255, 204, 153)'});
    $(window).scrollTop($(this).offset().top - 100);
});


</script>