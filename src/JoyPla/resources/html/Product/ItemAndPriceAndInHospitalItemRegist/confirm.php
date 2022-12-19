<div class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom" id="mainPage">

    <h1>商品情報登録 - 確認</h1>

    <p class="header_text">変更内容をご確認の上、変更ボタンをクリックしてください。<br><p class="uk-alert-danger uk-alert">内容を修正する場合は、戻るボタンをクリックしてください。</p></p>

    <div>
        <form method="post" x-bind:action="_ROOT">
            <input type="hidden" name="_method" value="post">
            <input type="hidden" name="_csrf" value="<?= $csrf ?>">
            <input type="hidden" name="path" value="/product/ItemAndPriceAndInHospitalRegist/thanks">
            <div class="smp_tmpl">
                <dl class="cf">
                    <dt class="title">
                        商品名 
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->itemName->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        分類
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->category->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        小分類
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->smallCategory->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        製品コード
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->itemCode->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        規格
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->itemStandard->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        JANコード 
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->itemJANCode->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        メーカー名
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->makerName->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        カタログNo
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->catalogNo->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        シリアルNo
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->serialNo->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        ロット管理フラグ
                    </dt><dd class="data multi2">
                        <span class="msg"><?= ($errors)? $errors->lotManagement->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        償還価格フラグ
                    </dt><dd class="data multi2">
                        <span class="msg"><?= ($errors)? $errors->officialFlag->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        償還価格
                    </dt><dd class="data real">
                        <span class="msg"><?= ($errors)? $errors->officialprice->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        旧償還価格
                    </dt><dd class="data real">
                        <span class="msg"><?= ($errors)? $errors->officialpriceOld->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        入数 
                    </dt><dd class="data integer">
                        <span class="msg"><?= ($errors)? $errors->quantity->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        入数単位 
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->quantityUnit->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        個数単位 
                    </dt><dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->itemUnit->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        定価
                    </dt><dd class="data real">
                        <span class="msg"><?= ($errors)? $errors->minPrice->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        卸業者
                    </dt>
                    <dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->distributorId->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        卸業者管理コード
                    </dt>
                    <dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->distributorMCode->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        購買価格
                    </dt>
                    <dd class="data real">
                        <span class="msg"><?= ($errors)? $errors->price->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        単価
                    </dt>
                    <dd class="data real">
                        <span class="msg"><?= ($errors)? $errors->unitPrice->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        保険請求分類（医科）
                    </dt>
                    <dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->medicineCategory->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        保険請求分類（在宅）
                    </dt>
                    <dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->homeCategory->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        測定機器名
                    </dt>
                    <dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->measuringInst->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        特記事項
                    </dt>
                    <dd class="data ">
                        <span class="msg"><?= ($errors)? $errors->notice->message() : "" ?></span>
                    </dd>
                </dl>
            </div>
            <input class="submit" type="submit" name="SMPFORM_BACK" value="戻る">
            <input class="submit" type="submit" name="submit" value="登録">
        </form>
    </div>
</div>
