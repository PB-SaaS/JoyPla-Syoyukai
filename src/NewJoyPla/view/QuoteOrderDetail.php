<style>
    .shouhin-table table.uk-table {
      counter-reset: rowCount;
    }

    .shouhin-table table.uk-table > tbody > tr {
      counter-increment: rowCount;
    }

    .shouhin-table table.uk-table > tbody > tr > td:first-child::before {
      content: counter(rowCount);
    }
  </style>

<div class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="%url/rel:mpgt:Price%&Action=QuoteList&table_cache=true">見積依頼一覧</a></li>
                <li><span>見積依頼詳細</span></li>
            </ul>
            <div class="no_print uk-margin" uk-margin>
                <input class="print_hidden uk-button uk-button-default uk-margin-small-top" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
            </div>
            <hr>
            <div class="uk-margin-auto uk-width-2-3@m">
                <article class="uk-article">
                    <h1 class="uk-article-title">%val:usr:requestTitle%</h1>
                    <p class="uk-article-meta">
                        ステータス: %val:usr:requestStatus% <br>
                        依頼者 %val:usr:hospitalName% %val:usr:requestUName% <br>
                        卸業者：%val:usr:distributorName%
                    </p>
                    <p class="">
                        見積期限：%val:usr:quotePeriod% <br> <br>
                        %val:usr:requestDetail:br%
                    </p>
                    <div class="uk-grid-small uk-child-width-auto" uk-grid>
                        <div class="uk-width-1-2">
                            %val:usr:registrationTime%
                        </div>
                    </div>
                </article>
            </div>

        <?php if ($tenantKind == "1" && $userInfo->isAdmin()): ?>
            <div class="uk-margin">
                <p>見積商品一覧</p>
                <div>%sf:usr:search19:mstfilter:table%</div>
            </div>
        <?php endif; ?>

            <hr>
            <div class="uk-margin">
                <div class="no_print uk-margin" uk-margin>
                    <button class="uk-button uk-button-primary uk-margin-small-right" type="button" uk-toggle="target: #modal-RQItems">金額見積の対象商品を追加</button>
                </div>
                <p>見積金額一覧</p>
                <hr>
                <div class="" id="tablearea">
                    %sf:usr:search37:table:mstfilter%
                </div>
            </div>
        </div>
    </div>
</div>
<!-- This is a button toggling the modal with the default close button -->

<!-- This is the modal with the default close button -->
<div id="modal-RQItems" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-width-1-1 uk-padding" uk-height-viewport="offset-bottom: 20">
        <h2 class="uk-modal-title">金額見積の対象商品を追加</h2>
        <button class="uk-modal-close-outside" type="button" uk-close></button>
        <button class="uk-button uk-button-default" type="button" onclick="sanshouClick()">商品マスタを開く</button>
        <button class="uk-button uk-button-primary" type="submit" onclick="regRequestItemList()">追加確定</button>
        <form onsubmit="return false" action="#" method="post">
            <div class="shouhin-table uk-width-expand uk-modal-body uk-overflow-auto">
                <table class="uk-table uk-table-striped uk-table-striped uk-table-condensed uk-text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>メーカー</th>
                            <th>商品名</th>
                            <th>製品コード</th>
                            <th>規格</th>
                            <th>JANコード</th>
                            <th>入数</th>
                            <th>個数単位</th>
                            <th>特記事項</th>
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
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>
    </div>
</div>
<script>
    // 参照ボタンクリック
    function sanshouClick() {
        // 参照マスタを別ウィンドウで開く
        window.open('%url/rel:mpgt:page_169089%', '_blank', 'scrollbars=yes,width=1220,height=600');
    }
    let listObject = {};
    let num = 0;
    let dataKey = ['id', 'makerName', 'itemName', 'itemCode', 'itemStandard', 'itemJANCode', 'quantity', 'itemUnit', 'notice'];

    function delTr(object, elm) {
        elm.parentElement.parentElement.remove()
        delete listObject[object.num];
        console.log(listObject);
    }

    function addTr(object) {
        num++;
        object.num = num;
        listObject[object.num] = object;
        //listObject[object.num].quantity = 0;
        listObject[object.num].notice = "";
        //listObject[object.num].itemUnit = "";

        let trElm = document.createElement("tr");
        let tdElm = '';
        for (let i = 0; i < dataKey.length; i++) {
            tdElm = document.createElement("td");
            html = document.createTextNode('');

            if (dataKey[i] === 'id') {
                //html = document.createTextNode(listObject[object.recordId].row);
            } else if (dataKey[i] === 'itemUnit') {
                //html = '<input type="number" class="uk-input" style="width:72px" step="10">';
                html = document.createElement("div");
                input = document.createElement("input");
                input.type = 'text';
                input.className = 'uk-input';
                input.id = 'itemUnit_' + num;
                input.style = 'width:96px';
                input.value = listObject[num][dataKey[i]];

                input.onchange = function() {
                    listObject[object.num].itemUnit = this.value;
                    $('#itemUnit_' + object.num).css({
                        "color": "rgb(68, 68, 68)",
                        "background-color": "rgb(255, 204, 153)",
                        "width": "96px"
                    });
                };
                html.appendChild(input);
            } else if (dataKey[i] === 'notice') {

                //html = '<input type="number" class="uk-input" style="width:72px" step="10">';
                html = document.createElement("div");
                textarea = document.createElement("textarea");
                textarea.className = 'uk-textarea uk-width-medium';
                textarea.id = 'notice_' + num;
                textarea.maxLength = "256";

                textarea.onchange = function() {
                    listObject[object.num].notice = this.value;
                    $('#notice_' + object.num).css({
                        "color": "rgb(68, 68, 68)",
                        "background-color": "rgb(255, 204, 153)"
                    });
                };

                html.appendChild(textarea);

            } else if (dataKey[i] === 'quantity') {
                //html = '<input type="number" class="uk-input" style="width:72px" step="10">';
                html = document.createElement("div");
                input = document.createElement("input");
                input.type = 'number';
                input.step = "1"
                input.className = 'uk-input';
                input.id = 'q_' + num;
                input.style = 'width:96px';
                input.min = 0;
                input.value = listObject[num][dataKey[i]];

                input.onchange = function() {
                    changeForInputNumber(this);
                    listObject[object.num].quantity = this.value;
                    $('#q_' + object.num).css({
                        "color": "rgb(68, 68, 68)",
                        "background-color": "rgb(255, 204, 153)",
                        "width": "96px"
                    });
                };
                span = document.createElement("span");
                span.innerText = listObject[num].quantityUnit;
                span.className = 'uk-text-bottom';
                html.appendChild(input);
                html.appendChild(span);
            } else {
                text = '';
                text += listObject[num][dataKey[i]];
                html = document.createTextNode(text);
            }

            tdElm.appendChild(html);
            trElm.appendChild(tdElm);
        }
        tdElm = document.createElement("td");

        input = document.createElement("input");
        input.type = 'button';
        input.value = '削除';
        input.className = 'uk-button uk-button-danger uk-button-small';
        input.onclick = function() {
            delTr(object, this);
        }

        tdElm.appendChild(input);
        trElm.appendChild(tdElm);
        $(".shouhin-table table tbody").append(trElm);
    }

    let canAjax = true;

    function regRequestItemList() {
        UIkit.modal.confirm("見積商品の登録を行います").then(function() {

            if (!canAjax) {
                console.log('通信中');
                return;
            }
            if (!itemsCheck()) {
                loading_remove();
                return false;
            }

            canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
            loading();
            $.ajax({
                    async: false,
                    url: '%url/card:page_169096%',
                    type: 'POST',
                    data: {
                        items: JSON.stringify(objectValueToURIencode(listObject))
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done((data) => {
                    if (!data.result) {
                        UIkit.modal.alert("見積商品の登録に失敗しました").then(function() {
                            UIkit.modal($('#modal-RQItems')).show();
                        });
                        return false;
                    }
                    UIkit.modal.alert('見積商品の登録が完了しました').then(function() {
                        location.reload();
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail((data) => {
                    console.log(data.responseText);
                    UIkit.modal.alert("見積商品の登録に失敗しました").then(function() {
                        UIkit.modal($('#modal-RQItems')).show();
                    });
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always((data) => {
                    canAjax = true; // 再びAjaxできるようにする
                    loading_remove();
                });

        }, function() {
            UIkit.modal($('#modal-RQItems')).show();
        });
    }

    function itemsCheck() {
        let checkflg = false;
        Object.keys(listObject).forEach(function(key) {
            checkflg = true;
        });

        if (checkflg) {} else {
            UIkit.modal.alert('見積商品がありません').then(function() {
                UIkit.modal($('#modal-RQItems')).show();
            });
            return false;
        }

        checkflg = true;
        Object.keys(listObject).forEach(function(key) {
            if (Math.floor(listObject[key].quantity) <= 0) {
                checkflg = false;
            }
        });

        if (checkflg) {} else {
            UIkit.modal.alert('入数を0以上で入力してください').then(function() {
                UIkit.modal($('#modal-RQItems')).show();
            });
            return false;
        }

        checkflg = true;
        Object.keys(listObject).forEach(function(key) {
            console.log(listObject[key].itemUnit);
            if (listObject[key].itemUnit == '') {
                checkflg = false;
            }
        });

        if (checkflg) {} else {
            UIkit.modal.alert('個数単位を入力してください').then(function() {
                UIkit.modal($('#modal-RQItems')).show();
            });
            return false;
        }

        return true;

    }
</script>