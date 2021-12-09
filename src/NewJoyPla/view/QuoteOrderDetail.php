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
        <?php if ($tenant_kind == "1" && ( $user_info->isAdmin() || $user_info->isApprover() )): ?>
            <div class="uk-margin">
                <p>見積商品一覧</p>
                <div>%sf:usr:search39:mstfilter:table%</div>
            </div>
        <?php elseif ($tenant_kind == "1" && $user_info->isUser()): ?>
            <div class="uk-margin">
                <p>見積商品一覧</p>
                <div>%sf:usr:_tantouMitsumori:mstfilter:table%</div>
            </div>
        <?php endif; ?>

            <hr>
            <div class="uk-margin">
                <p>見積金額一覧</p>
                <div class="no_print uk-margin" uk-margin>
                    <button class="uk-button uk-button-primary uk-margin-small-right" type="button" uk-toggle="target: #modal-RQItems">金額見積の対象商品を追加</button>
                </div>
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
        <button class="uk-button uk-button-default" type="button" v-on:click="sanshouClick">商品マスタを開く</button>
        <button class="uk-button uk-button-primary" type="submit" v-on:click="regRequestItemList()">追加確定</button>
        <form onsubmit="return false" action="#" method="post">
            <div class="shouhin-table uk-width-expand uk-modal-body uk-overflow-auto">
                <table class="uk-table uk-table-striped uk-table-striped uk-table-condensed uk-text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>メーカー</th>
                            <th>分類</th>
                            <th>商品名</th>
                            <th>製品コード</th>
                            <th>規格</th>
                            <th>JANコード</th>
                            <th>ロット管理フラグ</th>
                            <th>入数</th>
                            <th>個数単位</th>
                            <th>特記事項</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(list, key) in lists" :id="'tr_' + key">
                            <td>{{list.text}}</td>
                            <td>{{list.maker}}</td>
                            <td>{{list.category}}</td>
                            <td>{{list.itemName}}</td>
                            <td>{{list.itemCode}}</td>
                            <td>{{list.itemStandard}}</td>
                            <td>{{list.itemJANCode}}</td>
                            <td>{{list.lotFlag}}</td>
                            <td>
                                <input type="number" step="1" min="0" class="uk-input" style="width: 96px;" v-bind:style="list.countStyle" v-model="list.quantity" v-on:change="addCountStyle(key)">
                                <span class="uk-text-bottom">{{list.quantityUnit}}</span>
                            </td>
                            <td>
                                <input type="text" class="uk-input" style="width: 96px;" v-bind:style="list.unitStyle" v-model="list.itemUnit" v-on:change="addUnitStyle(key)">
                            </td>
                            <td>
                                <textarea class="uk-textarea uk-width-medium" v-bind:style="list.noticeStyle" v-model="list.notice" v-on:change="addNoticeStyle(key)" maxlength="256"></textarea>
                            </td>
                            <td uk-margin class="uk-text-center">
                                <input type="button" class="uk-button uk-button-danger uk-button-small" value="削除" v-on:click="deleteList(key)">
                            </td>
                        </tr>
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
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>
    </div>
</div>

<script>

var app = new Vue({
    el: '#modal-RQItems',
    data: {
        lists: []
    },
    methods: {
        addList: function(object) {
            object.countStyle = {};
            object.notice = "";
            app.lists.push(object);
            
        },
        regRequestItemList: function() {
            UIkit.modal.confirm("見積商品の登録を行います").then(function() {
                if(!app.check()){
                    return false;
                }
                loading();
                $.ajax({
                    async: false,
                    url: "<?php echo $api_url ?>",
                    type:'POST',
                    data: {
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : 'regRequestItems',
                        items : JSON.stringify( objectValueToURIencode(app.lists) )
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if (data.code != 0) {
                        UIkit.modal.alert("見積商品の登録に失敗しました").then(function() {
                            UIkit.modal($('#modal-RQItems')).show();
                        });
                        return false;
                    }
                    UIkit.modal.alert("見積商品の登録が完了しました").then(function(){
                        app.lists.splice(0, app.lists.length);
                        location.reload();
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert("見積商品の登録に失敗しました").then(function() {
                        UIkit.modal($('#modal-RQItems')).show();
                    });
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    loading_remove();
                });
            }, function() {
                UIkit.modal($('#modal-RQItems')).show();
            });
        },
        deleteList: function(key) {
            this.lists.splice(key, 1);
        },
        sanshouClick: function() {
            window.open('%url/rel:mpgt:page_177993%', '_blank','scrollbars=yes,width=1220,height=600');
        },
        check: function(){
            
            if (app.lists.length === 0) {
                UIkit.modal.alert('見積商品がありません').then(function() {
                    UIkit.modal($('#modal-RQItems')).show();
                });
                return false ;
            }
 
            let checkflg = true;
            app.lists.forEach(function(elem, index) {
                if (Math.floor(app.lists[index].quantity) <= 0) {
                    let changeObject = app.lists[index];
                    changeObject.countStyle.border = 'red 2px solid';
                    app.$set(app.lists, index, changeObject);
                    checkflg = false;
                }
            });
            if (!checkflg) {
                UIkit.modal.alert('入数を1以上で入力してください').then(function() {
                    UIkit.modal($('#modal-RQItems')).show();
                });
                return false;
            }

            checkflg = true;
            app.lists.forEach(function(elem, index) {
                if (app.lists[index].itemUnit == '') {
                    let changeObject = app.lists[index];
                    changeObject.unitStyle.border = 'red 2px solid';
                    app.$set(app.lists, index, changeObject);
                    checkflg = false;
                }
            });
            if (!checkflg) {
                UIkit.modal.alert('個数単位を入力してください').then(function() {
                    UIkit.modal($('#modal-RQItems')).show();
                });
                return false;
            }
            
            checkflg = true;
            app.lists.forEach(function(elem, index) {
                if (app.lists[index].itemUnit.bytes() > 32)
                {
                    let changeObject = app.lists[index];
                    changeObject.unitStyle.border = 'red 2px solid';
                    app.$set(app.lists, index, changeObject);
                    checkflg = false;
                }
            });
            
            if (!checkflg) {
                UIkit.modal.alert('個数単位は半角32文字,全角16文字以内で入力してください').then(function() {
                    UIkit.modal($('#modal-RQItems')).show();
                });
                return false;
            }
            
            
            checkflg = true;
            app.lists.forEach(function(elem, index) {
                if (app.lists[index].notice.bytes() > 512)
                {
                    let changeObject = app.lists[index];
                    changeObject.noticeStyle.border = 'red 2px solid';
                    app.$set(app.lists, index, changeObject);
                    checkflg = false;
                }
            });
            
            if (!checkflg) {
                UIkit.modal.alert('特記事項は半角512文字,全角256文字以内で入力してください').then(function() {
                    UIkit.modal($('#modal-RQItems')).show();
                });
                return false;
            }
            return true;
        },
        addCountStyle: function(index){
            let changeObject = app.lists[index];
            changeObject.countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
            app.$set(app.lists, index, changeObject);
        },
        addUnitStyle: function(index){
            let changeObject = app.lists[index];
            changeObject.unitStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
            app.$set(app.lists, index, changeObject);
        },
        addNoticeStyle: function(index){
            let changeObject = app.lists[index];
            changeObject.noticeStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
            app.$set(app.lists, index, changeObject);
        }
    }
});

function addTr(object, type, count)
{
    app.addList(object);
}
</script>