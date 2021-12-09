<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="app">
    <div class="uk-container uk-container-expand" uk-height-viewport="expand: true">
        <div class="uk-margin-auto uk-margin-remove-top uk-margin-bottom" id="mainPage">
            <!-- SMP_TEMPLATE_HEADER start -->
            <h1>金額・院内商品一括登録</h1>
            <div class="js-upload uk-placeholder uk-text-center">
                <div class="js-upload" uk-form-custom>
                    <input type="file" @change="loadCsvFile" accept=".csv,.tsv,.txt">
                    <span class="uk-link" tabindex="-1" v-if="filename !== ''">{{ filename }}</span>
                    <span class="uk-link" tabindex="-1" v-else> CSV/TSV ファイルを選択してください</span>
                </div>
            </div>
            <div>
                <select v-model="hospitalId" class="uk-select uk-margin-bottom uk-width-1-2@m" @change="result = false">
                    <option value="">----- 登録先病院を選択してください -----</option>
                    <?php
                        foreach($hospital as $h)
                        {
                            echo "<option value='".$h->hospitalId."'>".$h->hospitalName."</option>".PHP_EOL;
                        }
                    ?>
                </select>
            </div>
            <input type="button" v-on:click="validateCheck" value="バリデーションチェック" class="uk-button uk-button-primary" v-bind:disabled="lists.length == 0">
            
            <span class="uk-margin-small-right" uk-icon="icon: arrow-right; ratio: 2" v-if="result"></span>
            <input type="button" v-on:click="regist" value="登録・更新実行" class="uk-button uk-button-primary" v-if="result">
            <h4>読み取り結果</h4>
            <p>件数：{{ lists.length }} 件</p>
            <br>
            <span class="uk-text-warning">※院内商品と金額情報を同時に新規登録します</span>
            <p class="uk-text-danger" v-for="msg in message">{{ msg }}</p>
            <p class="uk-text" >{{ success_message }}</p>
            <table class="uk-table uk-table-divider">
                <thead>
                    <tr>
                        <th>id</th>
                        <th v-for="header in headers">{{ header }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(worker, index) in lists" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td v-for="(column, index) in worker.data" :key="index">{{ column }}</td>
                        <td><button type="button" class="uk-button uk-button-danger uk-button-small" v-on:click="deleteList(index)">削除</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr></tr>
                        
                </tfoot>
            </table>
        </div>
    
    </div>
        
</div>
            
<script>
    
var app = new Vue({
    el: '#app',
    data() {
        return {
            limit : 5000,
            result : false,
            message: "",
            success_message : "",
            filename: "",
            hospitalId: "",
            headers: [
                "商品ID",
                "卸業者ID",
                "不使用フラグ",
                "定価",
                "単価",
                "測定機器名",
                "入数",
                "入数単位",
                "個数単位",
                "購買価格",
                "特記事項",
                ],
            workers: [],
            lists: []
        };
    },
    methods: {
		deleteList: function(key) {
			this.lists.splice(key, 1);
		},
        loadCsvFile(e) {
            let vm = this;
            vm.result = false;
            vm.workers = [];
            vm.lists = [];
            vm.message = [];
            vm.success_message = "";
            let file = e.target.files[0];
            
            vm.filename = file.name;
            let extension = vm.filename.split('.').pop();
            
            if (! file.type.match("text/csv") && ! file.type.match("application/vnd.ms-excel") && ( extension != 'tsv' && extension != 'txt' ) ) {
                vm.message.push( "CSV/TSV ファイルを選択してください" );
                return;
            }
            
            let extension_type = extension;
            if ((file.type.match("text/csv") || file.type.match("application/vnd.ms-excel") )) {
                extension_type = 'csv';
            }
            
            if ( ( extension == 'tsv' || extension == 'txt' )) {
                extension_type = 'tsv';
            }
            
            let reader = new FileReader();
            reader.readAsArrayBuffer(file);
            progress_bar.start(100, 'ファイルを読み込んでいます...');
            //reader.readAsText(file);
            reader.onload = function (e) {
                // 8ビット符号なし整数値配列と見なす
                var array = new Uint8Array(e.target.result);
            
                // 文字コードを取得
                switch (Encoding.detect(array)) {
                case 'UTF16':
                    // 16ビット符号なし整数値配列と見なす
                    array = new Uint16Array(e.target.result);
                    break;
                case 'UTF32':
                    // 32ビット符号なし整数値配列と見なす
                    array = new Uint32Array(e.target.result);
                    break;
                }
            
                // Unicodeの数値配列に変換
                var unicodeArray = Encoding.convert(array, 'UNICODE');
                // Unicodeの数値配列を文字列に変換
                var text = Encoding.codeToString(unicodeArray);
                
                let lines = text.split(/\r\n|\n/);
                let linesArr = [];
                
                for (let i = 0; i < lines.length; i++) {
                    if(lines[i] === ""){
                        lines.splice(i,1);
                    }
                }
                
                if(vm.limit < lines.length)
                { 
                    vm.message.push("読み込んだファイルは"+vm.limit+"件を超えています");
                    progress_bar.progress(100, '進捗：'+100+'%');
                    return false;
                }
                
                for (let i = 0; i < lines.length; i++) {
                    if(lines[i] === ""){
                        continue;
                    }
                    linesArr[i] = {'index' : '' , 'data' : []};
                    if(extension_type == 'csv'){
                        console.log(lines[i].split(/,(?=(?:[^"]*"[^"]*")*[^"]*$)/));
                        linesArr[i]['data'] = lines[i].split(/,(?=(?:[^"]*"[^"]*")*[^"]*$)/);
                        //linesArr[i] = lines[i].split(",");
                    }
                    if(extension_type == 'tsv'){
                        linesArr[i]['data'] = lines[i].split("\t");
                        //linesArr[i] = lines[i].split("\t");   
                    }
                    if(vm.headers.length !== linesArr[i]['data'].length)
                    {
                        vm.message.push("カラム数がヘッダーと一致しません");
                        progress_bar.progress(100, '進捗：'+100+'%');
                        return false;
                    }
                    for(let j = 0 ; j < linesArr[i]['data'].length ; j++ ) {
                        if(linesArr[i]['data'][j].match(/^"/))
                        {
                            linesArr[i]['data'][j] = linesArr[i]['data'][j].replace(/^"/, "");
                        }
                        if(linesArr[i]['data'][j].match(/"$/))
                        {
                            linesArr[i]['data'][j] = linesArr[i]['data'][j].replace(/"$/, "");
                        }
                    }
    			    let percent = 100;
    			    if((lines.length - 1) > 0){
    			        percent = (1 / ( lines.length - 1) ) * 100;
    			    }
    			    let val = Math.ceil(progress_bar.getVal() + percent);
                    progress_bar.progress(val, '進捗：'+val+'%');
                }
                /*
                let temp = [];
                for(let i = 0; i < linesArr.length; i++){
                    if(i >= 5){ break; }
                    temp[i] = linesArr[i];
                }
                */
                vm.lists = linesArr;
                //vm.workers = temp;
            };
        },
        validateCheck: function() {
            let vm = this;
            vm.result = false;
            vm.message = [];
            vm.success_message = "";
            
            if(vm.hospitalId == "")
            {
                UIkit.modal.alert('病院を選択してください');
                return false;
            }
            
            progress_bar.start(100, 'バリデーションチェックを開始しています...');
            vm.lists.forEach(function(value,index)
            {
                vm.lists[index]['index'] = index; 
            });
            let chunk = vm.arrayChunk(vm.lists,1000);
            let i = 0 ;
            let ajaxCount = 0 ;
            for(i ; i < chunk.length ; i++ )
            {
                let forCount = i;
                let startRowNumber = JSON.parse(JSON.stringify(chunk[i]));
                startRowNumber = startRowNumber.shift()['index'];
                
                (function(i){
                    $.ajax({
                        async: true,
                        url: "<?php echo $api_url ?>",
                        type:'POST',
                        data:{
                            startRowNumber : startRowNumber,
                            rowData : JSON.stringify( vm.objectValueToURIencode(chunk[i]) ),
                            _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                            Action : "bulkInsertValidateCheckApi",
                        },
        				dataType: "json"
        			})
        			// Ajaxリクエストが成功した時発動
        			.done( (data) => {
        			    if(vm.message.length < 1000){
            			    for(let j = 0 ; j < data.length ; j++)
            			    {
            			        vm.message.push(data[j]);
            			    }
        			    }
        			})
        			// Ajaxリクエストが失敗した時発動
        			.fail( (data) => {
        			})
        			// Ajaxリクエストが成功・失敗どちらでも発動
        			.always( (data) => {
        			    let percent = 100;
        			    if((chunk.length - 1) > 0){
        			        percent = (1 / ( chunk.length - 1) ) * 100;
        			    }
        			    let val = Math.ceil(progress_bar.getVal() + percent);
                        progress_bar.progress(val, '進捗：'+val+'%');
        			});
        		})(forCount);
        		
                (function(i){
                    $.ajax({
                        async: true,
                        url: "<?php echo $api_url ?>",
                        type:'POST',
                        data:{
                            startRowNumber : startRowNumber,
                            hospitalId: vm.hospitalId,
                            rowData : JSON.stringify( vm.objectValueToURIencode(chunk[i]) ),
                            _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                            Action : "bulkInsertValidateCheck2Api",
                        },
        				dataType: "json"
        			})
        			// Ajaxリクエストが成功した時発動
        			.done( (data) => {
        			    if(vm.message.length < 1000){
            			    for(let j = 0 ; j < data.length ; j++)
            			    {
            			        vm.message.push(data[j]);
            			    }
        			    }
        			})
        			// Ajaxリクエストが失敗した時発動
        			.fail( (data) => {
        			})
        			// Ajaxリクエストが成功・失敗どちらでも発動
        			.always( (data) => {
        			    let percent = 100;
        			    if((chunk.length - 1) > 0){
        			        percent = (1 / ( chunk.length - 1) ) * 100;
        			    }
        			    let val = Math.ceil(progress_bar.getVal() + percent);
                        progress_bar.progress(val, '進捗：'+val+'%');
        			});
        		})(forCount);
            }
            $(document).ajaxStop(function() {
                if(vm.message.length == 0 )
                {
                    vm.success_message = "チェックが完了しました";
                    vm.result = true;
                }
            });
        },
        regist: function() {
            let vm = this;
            UIkit.modal.confirm("登録処理を行います。<br>よろしいでしょうか。").then(function () 
            {
                vm.success_message = "";
                vm.result = false;
                vm.message = [];
                
                if(vm.hospitalId == "")
                {
                    UIkit.modal.alert('病院を選択してください');
                    return false;
                }
                progress_bar.start(100, '登録を開始しています...');
                vm.lists.forEach(function(value,index)
                {
                    vm.lists[index]['index'] = index; 
                });
                let chunk = vm.arrayChunk(vm.lists,1000);
                let i = 0 ;
                let ajaxCount = 0 ;
                for(i ; i < chunk.length ; i++ )
                {
                    let forCount = i;
                    let startRowNumber = JSON.parse(JSON.stringify(chunk[i]));
                    startRowNumber = startRowNumber.shift()['index'];
                    
                    (function(i){
                        $.ajax({
                            async: true,
                            url: "<?php echo $api_url ?>",
                            type:'POST',
                            data:{
                                hospitalId: vm.hospitalId,
                                startRowNumber : startRowNumber,
                                rowData : JSON.stringify( vm.objectValueToURIencode(chunk[i]) ),
                                _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                                Action : "bulkInsertApi",
                            },
            				dataType: "json"
            			})
            			// Ajaxリクエストが成功した時発動
            			.done( (data) => {
            			    if(data.code !== "0")
            			    {
            			        vm.message.push('登録に失敗しました');
            			    }
            			})
            			// Ajaxリクエストが失敗した時発動
            			.fail( (data) => {
        			        vm.message.push('登録に失敗しました');
            			})
            			// Ajaxリクエストが成功・失敗どちらでも発動
            			.always( (data) => {
            			    let percent = 100;
            			    if((chunk.length - 1) > 0){
            			        percent = (1 / ( chunk.length - 1) ) * 100;
            			    }
            			    let val = Math.ceil(progress_bar.getVal() + percent);
                            progress_bar.progress(val, '進捗：'+val+'%');
            			});
            		})(forCount);
                }
                $(document).ajaxStop(function() {
                    if(vm.message.length == 0 )
                    {
                        vm.success_message = "登録が完了しました";
                        vm.result = false;
					    vm.lists.splice(0, vm.lists.length);
                    }
                });
            });
        },
        objectValueToURIencode: function(object){
			let result = {};
			let vm = this;
			
			if(object == null){
				return null;
			}
			Object.keys(object).forEach(function (key) {
				if( typeof object[key] == "object"){
					result[key] = vm.objectValueToURIencode(object[key]);
				} else {
					result[key] = encodeURI(object[key]);
				}
			});
			return result;
		},
        arrayChunk: function(arr, size = 1){
            return arr.reduce(
                (newarr, _, i) => (i % size ? newarr : [...newarr, arr.slice(i, i + size)]),
                []
            )
        },
    }
});
</script>