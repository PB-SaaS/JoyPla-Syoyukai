
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/lib/Func.php";

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("topicsDB",$SPIRAL->getCardId(),"topicId","authKey");

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKeyCrypt = $crypt->encrypt($card["data"][0][1], "JoyPla");
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla トピック詳細</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
</head>
<body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="%url/table:back%">トピック一覧</a></li>
				    <li><span>トピック詳細</span></li>
				</ul>
		    	<hr>
		    	<div class="uk-margin-auto uk-width-2-3@m">
		    		<article class="uk-article">

					    <h1 class="uk-article-title">%val:usr:topicTitle%</h1>
					
					    <p class="uk-article-meta">
					    	作成者 %val:usr:topicName% <br>
					    	病院：%val:usr:hospitalName%<br>
					    	卸業者：%val:usr:distributorName%
					    </p>
					
					    <p class="uk-text-lead">%val:usr:topicContent:br%</p>
					
						<div class="uk-grid-small uk-child-width-auto" uk-grid>
							<div class="uk-width-1-2">
					    		%val:usr:registrationTime%
							</div>
					        <div class="uk-width-1-2 uk-text-right">
					            %val:usr:commentCount% Comments
					        </div>
					    </div>
						
					</article>
		    		<hr>
			        %sf:usr:search89:mstfilter:table%
		    		<hr>
			        <form onsubmit="return false;">
			        	<legend class="uk-legend">コメント</legend>
			        	<div class="uk-margin">
					        <label class="uk-form-label" for="form-stacked-text">氏名</label>
					        <div class="uk-form-controls">
			        			<input type="text" class="uk-input" name="name" value="%val:@usr:name%" readonly>
					        </div>
					    </div>
			        	<div class="uk-margin">
					        <label class="uk-form-label" for="form-stacked-text">コメント</label>
					        <div class="uk-form-controls">
			        			<textarea class="uk-textarea" rows="15" name="comment" maxlength="2000"></textarea>
					        </div>
					        <span class="uk-text-meta">※2000文字以内で入力してください</span>
					    </div>
					    
			        	<div class="uk-margin">
					        <div class="uk-form-controls uk-text-center">
			        			<input type="submit" class="uk-button uk-button-primary" value="コメントを送信" onclick="regComment()">
					        </div>
					    </div>
			        </form>
				</div>
			</div>
		</div>
	</div>
	<script>
		let canAjax = true;
		$(function(){
			let length = $(".uk-comment-list.comment-table").children().length;
			if(length == 0 ){
				$(".uk-comment-list.comment-table").html("<li class='uk-text-center uk-text-bold'><p>コメントはありません</p></li>");
				$(".uk-pagination").hide();
			}
		});
		
		function regComment(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			let comment = $("textarea[name='comment']").val();
			let name = $("input[name='name']").val();
			
			if(comment == ""){
				$("textarea[name='comment']").addClass("uk-form-danger");
				UIkit.modal.alert("コメントが空欄です。");
				return false;
			}
			if(name == ""){
				$("input[name='name']").addClass("uk-form-danger");
				UIkit.modal.alert("氏名が空欄です。");
				return false;
			}
			loading();
			canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
			$.ajax({
				async: false,
                url:"%url/card:page_266362%",
                type:"POST",
                data:{
                	topicId : "%val:usr:topicId%",
                	authKey: "<?php echo $authKeyCrypt ?>",
                	commentData : JSON.stringify( objectValueToURIencode({"comment":comment,"name":name}) ),
                },
                dataType: "json"
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
            	
                if(! data.result){
            		UIkit.modal.alert("コメントに失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
            		return false;
                }
                UIkit.modal.alert("コメントが完了しました").then(function(){
					location.reload(true);
				});
                
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
        		UIkit.modal.alert("コメントに失敗しました").then(function(){
					canAjax = true; // 再びAjaxできるようにする
				});
        		return false;
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
            	loading_remove();
            });
		}
		
	</script>
</body>
</html>
