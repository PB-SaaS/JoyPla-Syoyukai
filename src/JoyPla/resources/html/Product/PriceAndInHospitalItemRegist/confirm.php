<div class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom" id="mainPage">

    <h1>金額情報登録 - 確認</h1>

    <p class="header_text">登録内容をご確認の上、登録ボタンをクリックしてください。<br><p class="uk-alert-danger uk-alert">内容を修正する場合は、戻るボタンをクリックしてください。</p></p>

    <form method="post">
        <input type="hidden" name="confirm" value="true">
        <input type="hidden" name="_method" value="post">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="path" value="registPriceAndInHPForm/thanks">
        <div class="smp_tmpl">
            <dl class="cf">
                <dt class="title">
                    メーカー
                </dt>
                <dd class="data ">
                    <?php echo $input["makerName"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    商品名
                </dt>
                <dd class="data ">
                    <?php echo $input["itemName"] ; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    製品コード
                </dt>
                <dd class="data ">
                    <?php echo $input["itemCode"] ; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    規格
                </dt>
                <dd class="data ">
                    <?php echo $input["itemStandard"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    JANコード
                </dt>
                <dd class="data ">
                    <?php echo $input["itemJANCode"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    カタログNo
                </dt><dd class="data ">
                    <?php echo $input["catalogNo"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    シリアルNo
                </dt><dd class="data ">
                    <?php echo $input["serialNo"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    卸業者
                </dt>
                <dd class="data ">
                    <?php echo $distributor[$input["distributorId"]]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    卸業者管理コード
                </dt>
                <dd class="data ">
                    <?php echo $input["distributorMCode"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    入数
                </dt>
                <dd class="data integer">
                    <?php echo $input["quantity"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    入数単位
                </dt>
                <dd class="data ">
                    <?php echo $input["quantityUnit"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    個数単位
                </dt>
                <dd class="data ">
                    <?php echo $input["itemUnit"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    購買価格
                </dt>
                <dd class="data real">
                    <?php $input["price"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    単価
                </dt>
                <dd class="data real">
                    <?php $input["unitPrice"]; ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    保険請求分類（医科）
                </dt>
                <dd class="data ">
                    <?php echo nl2br($input["medicineCategory"]); ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    保険請求分類（在宅）
                </dt>
                <dd class="data ">
                    <?php echo nl2br($input["homeCategory"]); ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    測定機器名
                </dt>
                <dd class="data ">
                    <?php echo $input["measuringInst"] ?><br>
                </dd>
            </dl>
            <dl class="cf">
                <dt class="title">
                    特記事項
                </dt>
                <dd class="data ">
                    <?php echo nl2br($input["notice"]); ?><br>
                </dd>
            </dl>
        </div>
        <input class="submit" type="submit" name="formBack" value="戻る">
        <input class="submit" type="submit" name="submit" value="登録">
    </form>
</div>
