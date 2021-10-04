
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

    .uk-button.goodsBillingButton{
        background: #EB8400;
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
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
        <div class="uk-container uk-container-expand uk-margin-top">
            <ul class="uk-breadcrumb">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><span>貸出内容入力</span></li>
            </ul>
            <h2 class="page_title">貸出内容入力</h2>
            <hr>
            <div class="uk-width-1-3@m">
                <div class="uk-margin">
                    <select class="uk-select" name="busyo">
                        <option value="">----- 部署選択 -----</option>
                        <?php
                        foreach($divisionData->data as $data)
                        {
                            if($data->divisionType === '1')
                            {
                                echo '<option value="'.$data->divisionId.'">'.$data->divisionName.'(大倉庫)</option>';
                                echo '<option value="" disabled>--------------------</option>';
                            }
                        }
                        foreach($divisionData->data as $data)
                        {
                            if($data->divisionType === '2')
                            {
                                echo '<option value="'.$data->divisionId.'">'.$data->divisionName.'</option>';
                            }
                        }
                        /*
                        if($userInfo->getUserPermission() == '1') {
                            echo '<option value="'.$divisionData['store'][0][1].'">'.$divisionData['store'][0][3].'(大倉庫)</option>';
                            echo '<option value="" disabled>--------------------</option>';
                            foreach($divisionData['division'] as $divisiton){
                                if($divisiton[5] == '1'){
                                    continue;
                                }
                            echo '<option value="'.$divisiton[1].'">'.$divisiton[3].'</option>';
                            }
                        } else if($userInfo->getDivisionId() == $divisionData['store'][0][1]) {
                            echo '<option value="'.$divisionData['store'][0][1].'">'.$divisionData['store'][0][3].'(大倉庫)</option>';
                        } else {
                            foreach($divisionData['division'] as $divisiton){
                                if($divisiton[5] == '1'){
                                    continue;
                                }
                            echo '<option value="'.$divisiton[1].'">'.$divisiton[3].'</option>';
                            }
                        }
                        */
                        ?>
                    </select>
                </div>
            </div>
            <div class="uk-margin-bottom">
                <div>
                    <div>
                        <button class="uk-button uk-button-default" onclick="borrowing_registar.sanshouClick()">商品マスタを開く</button>
                        <button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
                        <button class="uk-button uk-button-primary goodsBillingButton" onclick="borrowing_registar.borrowingRegist()">貸出リスト登録</button>
                        <?php if($user_info->isAdmin()): ?>
                        <button class="uk-button uk-button-primary unorderedSlipButton" onclick="borrowing_registar.usedReport()">使用済み報告</button>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
                <form action='#' method="post" onsubmit="borrowing_registar.barcodeSearch(); return false">
                    <input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." name="barcode" autocomplete="off"> 
                    <button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
                </form>	
            </div>
                <div class="uk-margin uk-text-right">
                    <button type="button" class="uk-button uk-button-primary"  uk-toggle="target: #modal-gs1128">GS1-128で照合</button>
                </div>
            
            <div class="shouhin-table uk-width-expand uk-overflow-auto">
                <table class="uk-table uk-table-striped uk-table-striped uk-table-condensed uk-text-nowrap">
                    <thead>
                        <tr>
                            <th class="uk-text-bottom">id</th>
                            <th class="uk-text-bottom">メーカー</th>
                            <th class="uk-text-bottom">商品名</th>
                            <th class="uk-text-bottom">製品コード</th>
                            <th class="uk-text-bottom">規格</th>
                            <th class="uk-text-bottom">入数</th>
                            <th class="uk-text-bottom">価格</th>
                            <th class="uk-text-bottom">単価</th>
                            <th class="uk-text-bottom">JANコード</th>
                            <th class="uk-text-bottom">卸業者</th>
                            <th class="uk-text-bottom">貸出数</th>
                            <th class="uk-text-bottom">ロット番号</th>
                            <th class="uk-text-bottom" style="width:146px">使用期限</th>
                            <th>
                                使用済み日<br>
                                <input type="date" class="uk-input uk-form-small" id="allUserdDate" style="width:184px" value='<?php echo date('Y-m-d') ?>'>
                                <div class="uk-text-right" style="width:184px">
                                    <button type="button" class="uk-button uk-button-primary uk-text-small uk-width-1-1" onclick='borrowing_registar.setUsedDate()'>一括反映</button>
                                </div>
                            </th>
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
            <form onsubmit="borrowing_registar.gs1_128($('#GS1-128').val());return false;" action="#">
                <button class="uk-modal-close" type="button" uk-close></button>
                <h2 class="uk-modal-title">GS1-128 読取</h2>
                <input type="text" class="uk-input" placeholder="GS1-128" id="GS1-128" autofocus="true">
                    <p class="uk-text-right">
                    <button class="uk-button uk-button-primary" type="button" onclick="borrowing_registar.gs1_128($('#GS1-128').val());">反映</button>
                </p>
            </form>
        </div>
    </div>
    <script>
    class BorrowingRegistar
    {
        constructor()
        {
            this.canAjax = true;
            this.gs1128_object = {};
            this.listObject = {};
            this.dataKey = ['id','maker','shouhinName','code','kikaku','irisu','kakaku','unitPrice','jan','oroshi','count'];
            this.index = 1;
        }
        // 参照ボタンクリック
        sanshouClick(){
            // 参照マスタを別ウィンドウで開く
            window.open('%url/rel:mpgt:page_175973%', '_blank','scrollbars=yes,width=1220,height=600');
        }
        
        delTr(index , elm){
            elm.parentElement.parentElement.remove();
            delete this.listObject[index];
        }

        addTr(object, type, count) {
            let tmp = this;
            
            tmp.listObject[tmp.index] = object;
            tmp.listObject[tmp.index].no = tmp.index;
            tmp.listObject[tmp.index].countNum = count;

            let trElm = document.createElement('tr'); 
            trElm.id = 'tr_' + tmp.index;
            let tdElm = '';
            let html = '';
            let input = '';
            let span = '';
            let text = '';
            for(let i = 0 ; i < tmp.dataKey.length; i++){
                tdElm = document.createElement('td');
                html = document.createTextNode('');
                
                if(tmp.dataKey[i] === 'id'){
                    //html = document.createTextNode(listObject[object.recordId].row);
                } else if(tmp.dataKey[i] === 'count'){
                    html = document.createElement('div');
                    span = document.createElement('span');
                    span.innerText = '1'+tmp.listObject[tmp.index].itemUnit;
                    html.appendChild(span);
                } else {
                    text = '';
                    if(tmp.dataKey[i] === 'kakaku'){
                        text += '￥';
                        text += price_text(tmp.listObject[tmp.index][tmp.dataKey[i]]);
                        text += '/'+ tmp.listObject[tmp.index].itemUnit;
                    } else if(tmp.dataKey[i] === 'unitPrice') {
                        text += '￥';
                        text += price_text(tmp.listObject[tmp.index][tmp.dataKey[i]]);
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
            input.className = 'uk-input usedDate_' + object.recordId;
            input.name = 'usedDate';
            input.type = 'date';
            input.value = '<?php echo date('Y-m-d') ?>';
            input.style.width = '184px';
            if ( type === 4 && object.usedDate ) {
                input.value = object.usedDate;
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
            input.type = 'button';
            input.value = '削除';
            input.className = 'uk-button uk-button-danger uk-button-small';
            input.onclick = function(){
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

        borrowingCheck(){
            let tmp = this;
            if($('select[name="busyo"]').val()){
            } else {
                UIkit.modal.alert('部署を選択してください');
                return false ;
            }
            
            if(Object.keys(tmp.listObject).length === 0){
                UIkit.modal.alert('商品を選択してください');
                return false ;
            }
            
            let chkLot = true;
            Object.keys(tmp.listObject).forEach(function (key) {
                if(tmp.listObject[key]['countNum'] > 0) {
                    if((!tmp.listObject[key]['lotNumber'] && tmp.listObject[key]['lotDate']) || (tmp.listObject[key]['lotNumber'] && !tmp.listObject[key]['lotDate'])) {
                    chkLot = false;
                    }
                }
            });
            
            if(!chkLot){
                UIkit.modal.alert('ロット情報を入力してください');
                return false ;
            }
            
            chkLot = true;
            let regex = /^[0-9a-zA-Z]+$/;
            Object.keys(tmp.listObject).forEach(function (key) {
                if(tmp.listObject[key]['lotNumber']) {
                    if((!regex.test(tmp.listObject[key]['lotNumber'])) ||
                        (encodeURI(tmp.listObject[key]['lotNumber']).replace(/%../g, '*').length > 20)) {
                    chkLot = false;
                    }
                }
            });
            
            if(!chkLot){
                UIkit.modal.alert('ロット番号の入力を確認してください');
                return false ;
            }
            return true;
        }
        
        <?php if($user_info->isAdmin()): ?>
        usedReportCheck(){
            let tmp = this;
            if($('select[name="busyo"]').val()){
            } else {
                UIkit.modal.alert('部署を選択してください');
                return false ;
            }
            
            if(Object.keys(tmp.listObject).length === 0){
                UIkit.modal.alert('商品を選択してください');
                return false ;
            }
            
            let chkLot = true;
            Object.keys(tmp.listObject).forEach(function (key) {
                if(tmp.listObject[key]['countNum'] > 0) {
                    if((!tmp.listObject[key]['lotNumber'] && tmp.listObject[key]['lotDate']) || (tmp.listObject[key]['lotNumber'] && !tmp.listObject[key]['lotDate'])) {
                    chkLot = false;
                    }
                }
            });
            
            if(!chkLot){
                UIkit.modal.alert('ロット情報を入力してください');
                return false ;
            }
            
            chkLot = true;
            let regex = /^[0-9a-zA-Z]+$/;
            Object.keys(tmp.listObject).forEach(function (key) {
                if(tmp.listObject[key]['lotNumber']) {
                    if((!regex.test(tmp.listObject[key]['lotNumber'])) ||
                        (encodeURI(tmp.listObject[key]['lotNumber']).replace(/%../g, '*').length > 20)) {
                    chkLot = false;
                    }
                }
            });
            
            if(!chkLot){
                UIkit.modal.alert('ロット番号の入力を確認してください');
                return false ;
            }

            let usedDate = true;
            Object.keys(tmp.listObject).forEach(function (key) {
                if(tmp.listObject[key]['countNum'] > 0) {
                    if(tmp.listObject[key]['usedDate'] == '' || ! tmp.listObject[key]['usedDate']) {
                        usedDate = false;
                    }
                }
            });
            
            if(!usedDate){
                UIkit.modal.alert('使用済み日時の入力を確認してください');
                return false ;
            }

            return true;
        }
        <?php endif ?>

        borrowingRegist(){
            let tmp = this;
            UIkit.modal.confirm('貸出商品登録を行いますか？').then(function () {
                tmp.setRegData();
                if(!tmp.canAjax) { 
                    console.log('通信中');
                    return;
                }
                if(! tmp.borrowingCheck()){
                    loading_remove();
                    return false;
                }
                tmp.canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
                loading();
                $.ajax({
                    async: false,
                    url: "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "borrowingRegistApi",
                        borrowing : JSON.stringify( objectValueToURIencode(tmp.listObject) ),
                        divisionId : $('select[name="busyo"]').val()
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert('貸出商品登録に失敗しました').then(function(){
                            tmp.canAjax = true; // 再びAjaxできるようにする
                        });
                        return false;
                    }
                    UIkit.modal.alert('貸出商品登録が完了しました').then(function () {
                        tmp.canAjax = true; // 再びAjaxできるようにする
                        tmp.dataReset();
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert('貸出商品登録に失敗しました').then(function(){
                        tmp.canAjax = true; // 再びAjaxできるようにする
                    });
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    loading_remove();
                });
            
            }, function () {
            });
        }
        usedReport(){
            let tmp = this;
            UIkit.modal.confirm('使用済み報告を行いますか？').then(function () {
                tmp.setRegData();
                if(!tmp.canAjax) { 
                    console.log('通信中');
                    return;
                }
                if(! tmp.usedReportCheck()){
                    loading_remove();
                    return false;
                }
                tmp.canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
                loading();
                $.ajax({
                    async: false,
                    url: "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "borrowingRegistrationToUsedReportApi",
                        borrowing : JSON.stringify( objectValueToURIencode(tmp.listObject) ),
                        divisionId : $('select[name="busyo"]').val()
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert('使用済み報告に失敗しました').then(function(){
                            tmp.canAjax = true; // 再びAjaxできるようにする
                        });
                        return false;
                    }
                    UIkit.modal.alert('使用済み報告が完了しました').then(function () {
                        tmp.canAjax = true; // 再びAjaxできるようにする
                        tmp.dataReset();
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert('使用済み報告に失敗しました').then(function(){
                        tmp.canAjax = true; // 再びAjaxできるようにする
                    });
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    loading_remove();
                });
            
            }, function () {
            });
        }
        barcodeSearch(){
            let tmp = this;
            if(!tmp.canAjax) { 
                console.log('通信中');
                return;
            }
            loading();
            tmp.canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
            $.ajax({
                async: false,
                url:'%url/rel:mpgt:labelBarcodeSAPI%',
                type:'POST',
                data:{
                    barcode : $('input[name="barcode"]').val(),
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
                let value = 0;
                if(data.code != 0 || data.data.length == 0){
                    UIkit.modal.alert("商品が見つかりませんでした").then(function(){
                        tmp.canAjax = true; // 再びAjaxできるようにする
                    });
                    return false;
                }
                data = data.data;
                tmp.addTr(data, 2, parseInt(data.count));
                tmp.canAjax = true; // 再びAjaxできるようにする
                $('input[name="barcode"]').val('');
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
                UIkit.modal.alert("商品が見つかりませんでした").then(function(){
                    tmp.canAjax = true; // 再びAjaxできるようにする
                });
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
                loading_remove();
            });
        }
        dataReset(){
            $('table.uk-table tbody tr').remove();
            this.listObject = {};
            loading_remove();
        }

        gs1_128(gs1128){
            let tmp = this;
            this.gs1128_object = {};
                
            if(Object.keys(tmp.listObject).length === 0){
                UIkit.modal.alert('先に商品を選択してください。');
                return false ;
            }
                
            if(gs1128.indexOf("]C1") !== 0){
                //UIkit.modal.alert("GS1-128ではありません");
                //return ;
                return tmp.gs1_128("]C1"+gs1128);
            } else {
                gs1128 = gs1128.slice( 3 );
                let obj = check_gs1128(gs1128);
                let objkey = null;
                let setObj = {};
                
                if(!obj.hasOwnProperty("01")){
                    UIkit.modal.alert("商品情報が含まれておりませんでした。").then(function(){
                        UIkit.modal($('#modal-gs1128')).show();
                    });
                    return;
                }
                
                let searchJan = addCheckDigit(obj["01"]);
                console.log(searchJan);
                Object.keys(tmp.listObject).forEach(function (key) {
                    if(searchJan == tmp.listObject[key]["jan"]){
                        objkey = tmp.listObject[key]["recordId"];
                        setObj = { ...tmp.listObject[key] };
                    }
                });
                
                if(!objkey){
                    UIkit.modal.alert('対象の商品が見つかりませんでした').then(function(){
                        UIkit.modal($('#modal-gs1128')).show();
                    });
                    return;
                }

                let existflg = false;
                let objLot = (obj["10"] === void 0) ? "" : obj["10"]; //lotNumber
                let objLotDate = (obj["17"] === void 0) ? "" : tmp.changeDate(obj["17"]); //lotDate

                $(document).find('.lot_' + objkey).each(function() {
                    let addRowLot = $(this).val();
                    let addRowLotDate = $(this).parents('tr').find('.lotDate_' + objkey).val();

                    if (!addRowLot && !addRowLotDate) {
                        $(this).val(objLot).css({'color':'rgb(68, 68, 68)', 'background':'rgb(255, 204, 153)'});
                        $(this).parents('tr').find('.lotDate_' + objkey).val(objLotDate).css({'color':'rgb(68, 68, 68)', 'background':'rgb(255, 204, 153)'});
                        $(this).parents('tr').find('.item_' + objkey).val(parseInt(setObj.irisu)).css({'color':'rgb(68, 68, 68)', 'background':'rgb(255, 204, 153)'});
                        $(window).scrollTop($(this).offset().top - 100);
                        existflg = true;
                        return false;
                    }
                    /*
                    if ((addRowLot == objLot) && (addRowLotDate == objLotDate)) {
                        let num = addRowNum + parseInt(setObj.irisu);
                        $(this).parents('tr').find('.item_' + objkey).val(num).css({'color':'rgb(68, 68, 68)', 'background':'rgb(255, 204, 153)'});
                        $(window).scrollTop($(this).offset().top - 100);
                        existflg = true;
                        return false;
                    }
                    */
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
        changeDate(text){
            if(text == null){
                return "";
            }
            if(text.length == "6"){
                text = 20 + text;
            }
            let date = text.slice(6, 8);
            if(parseInt(text.slice(6, 8)) == 0){
                date = '01';
            }
            return text.slice(0, 4) + "-" + text.slice(4, 6) + "-" + date;
        }
        setRegData() {
            let tmp = this;
            $(document).find('.shouhin-table table tbody tr').each(function() {
                let row = $.trim(($(this).attr('id')).replace('tr_', ''));
                let lot = $(this).find('input[name="lot"]').val();
                let lotDate = $(this).find('input[name="lotDate"]').val();
                let usedDate = $(this).find('input[name="usedDate"]').val();
                tmp.listObject[row].countNum = parseInt(1);
                tmp.listObject[row].lotNumber = lot;
                tmp.listObject[row].lotDate = lotDate;
                tmp.listObject[row].usedDate = usedDate;
            });
        }

        setUsedDate()
        {
            let val = $("#allUserdDate").val() ;

            if(val == '')
            {
                return UIkit.modal.alert("一括反映する日付を入力してください");
            }

            UIkit.modal.confirm("各商品の使用済み日に反映してよろしいですか").then(function () {
                let elm = $("input[name=\"usedDate\"]");
                for(let num = 0 ; num < elm.length ; num++)
                {
                    elm[num].value = val;
                    elm[num].style.backgroundColor = 'rgb(255, 204, 153)';
                    elm[num].style.color = 'rgb(68, 68, 68)';
                }
            }, function () {
            });
        }
    }

    let borrowing_registar = new BorrowingRegistar();
    
    $(document).on('change', 'input[type="number"]', function() {
        $(this).css({'color':'rgb(68, 68, 68)', 'background-color':'rgb(255, 204, 153)'});
        $(window).scrollTop($(this).offset().top - 100);
    });

    function addTr(object, type, count)
    {
        borrowing_registar.addTr(object, type, count);
    }
    
</script>