<div class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom" id="mainPage">

    <h1>金額情報登録 - 入力</h1>

    <?php if($errors && $errors->isError()): ?>
    <p class="header_emesg">入力内容に不備があります。入力された値をご確認ください</p>
    <?php endif ?>
    <p class="header_rmesg">必要事項をご入力の上、確認ボタンを押してください。</p>

    <div>
        <form method="post">
            <input type="hidden" name="_method" value="post">
            <input type="hidden" name="_csrf" value="<?= $csrf ?>">
            <input type="hidden" name="path" value="registPriceAndInHPForm/confirm">
            <input type="hidden" name="hospitalId" value="<?php echo $hospitalId; ?>">
            <input type="hidden" name="tenantId" value="<?php echo $tenantId; ?>">
            <input type="hidden" name="topPageLink" value="<?php echo $topPageLink; ?>">
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
                        <span class="need">必須</span>
                    </dt>
                    <dd class="data ">
                        <select name="distributorId" id="distributorId" class="uk-select ">
                            <option value="">
                                --- 選択してください ---
                            </option>
                            <?php
                            foreach($distributor as $key){
                                $selected = ($input["distributorId"] === $key["distributorId"]) ? "selected" : "";
                                echo "<option value='" . $key["distributorId"] . "' " . $selected . ">" . $key["distributorName"] . "</option>\n";
                            }
                            ?>
                        </select>
                        <br>
                        <span class="msg"><?= ($errors)? $errors->distributorId->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        卸業者管理コード
                    </dt>
                    <dd class="data ">
                        <input class="input " type="text" name="distributorMCode" value="<?php echo $input["distributorMCode"]; ?>" maxlength="128">
                        <br>
                        <span class="msg"><?= ($errors)? $errors->distributorMCode->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        入数 <span class="need">必須</span>
                    </dt><dd class="data integer">
                        <input class="input " type="number" name="quantity" value="<?php echo $input["quantity"]; ?>" maxlength="10" style="text-align: right;" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->quantity->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        入数単位
                    </dt><dd class="data ">
                        <?php echo $input["quantityUnit"]; ?><br>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        個数単位 <span class="need">必須</span>
                    </dt><dd class="data ">
                        <input class="input " type="number" name="itemUnit" value="<?php echo $input["itemUnit"]; ?>" maxlength="32" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->itemUnit->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        購買価格
                        <span class="need">必須</span>
                    </dt>
                    <dd class="data real">
                        <input class="input " type="number" name="price" value="<?php echo $input["price"]; ?>" maxlength="20" style="text-align: right;">
                        <br>
                        <span class="msg"><?= ($errors)? $errors->price->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        単価
                    </dt>
                    <dd class="data real">
                        <input class="input " type="number" name="unitPrice" value="<?php echo $input["unitPrice"]; ?>" maxlength="20" style="text-align: right;">
                        <br>
                        <button type="button" onclick="getUnitPrice()" class="uk-button uk-button-default" title="購買価格÷入数の値を自動で入力します">単価を自動計算</button>
                        <script>
                            function getUnitPrice() {
                                let price = $('input[name=price]')[0].value;
                                let quantity = $('input[name=quantity]')[0].value;

                                let unitPrice = 0;
                                if (price == "" || price == 0) {
                                    unitPrice = 0
                                }
                                if (quantity == "" || quantity == 0) {
                                    unitPrice = 0
                                }
                                unitPrice = (price / quantity);

                                $('input[name=unitPrice]')[0].value = unitPrice;
                            }
                        </script>
                        <br>
                        <span class="msg"><?= ($errors)? $errors->unitPrice->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        保険請求分類（医科）
                    </dt>
                    <dd class="data ">
                        <textarea class="" name="medicineCategory" rows="4" wrap="soft"><?php echo $input["medicineCategory"]; ?></textarea><br>
                        <span class="msg"><?= ($errors)? $errors->medicineCategory->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        保険請求分類（在宅）
                    </dt>
                    <dd class="data ">
                        <textarea class="" name="homeCategory" rows="4" wrap="soft"><?php echo $input["homeCategory"]; ?></textarea><br>
                        <span class="msg"><?= ($errors)? $errors->homeCategory->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        測定機器名
                    </dt>
                    <dd class="data ">
                        <input class="input " type="text" name="measuringInst" value="<?php echo $input["measuringInst"]; ?>" maxlength="128"><br>
                        <span class="msg"><?= ($errors)? $errors->measuringInst->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        特記事項
                    </dt>
                    <dd class="data ">
                        <textarea class="" name="notice" rows="4" wrap="soft"><?php echo $input["notice"]; ?></textarea><br>
                        <span class="msg"><?= ($errors)? $errors->notice->message() : "" ?></span>
                    </dd>
                </dl>
            </div>
            <input class="submit" type="submit" name="submit" value="確認">
        </form>
    </div>
</div>
