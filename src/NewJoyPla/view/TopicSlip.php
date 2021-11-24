
<div class="animsition" uk-height-viewport="expand: true" id="app">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb no_print">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
			    <li><a href="<?php echo $link ?>">トピック一覧</a></li>
			    <li><span>トピック詳細</span></li>
			</ul>
	    	<hr>
	    	<div class="uk-margin-auto uk-width-2-3@m">
	    		<article class="uk-article">

				    <h1 class="uk-article-title">%val:usr:topicTitle%</h1>
				
				    <p class="uk-article-meta">
				    	作成者 %val:usr:topicName% <br>
				    	システム管理者：%val:usr:adminViewFlg:v%<br>
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
	    		<ul class="uk-comment-list comment-table">
	    			<li class='uk-text-center uk-text-bold' v-if="commentSort.length != count"><a href="#" @click="getComment">さらに読み込む</a></li>
	    			<li class='uk-text-center uk-text-bold' v-if="count == 0"><p>コメントはありません</p></li>
　					<li class="uk-margin-small" v-else v-for="(comment, index)  in commentSort">
　						<article class="uk-comment uk-comment-primary" style="position: relative;">
　							<header class="uk-comment-header uk-position-relative">
　								<div class="uk-grid-medium uk-flex-middle uk-grid">
　									<div class="uk-width-expand">
										<h4 class="uk-comment-title uk-margin-remove">投稿者：{{ comment.commentName }}</h4>
										<ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-remove-top">
						                    <li><a href="javascript:void(0)">{{ comment.registrationTime }}</a></li>
						                    <li><a href="javascript:void(0)" @click="commentDelete(comment.id)" class="uk-text-danger" v-if="comment.deletableFlag">削除</a></li>
						                </ul>
									</div>
								</div>
							</header>
							<div class="uk-comment-body" style="white-space:pre-wrap; word-wrap:break-word;">
								<p v-if="comment.deleteFlg == '1'">コメントは削除されました</p>
								<p v-else>{{ comment.comment }}</p>
							</div>
							<hr>
							<span class="uk-comment-meta uk-margin-remove">No.{{ count - (commentSort.length - ( index + 1 )) }}</span>
							<div v-if="comment.deleteFlg == '1'" style="opacity: 0.4;" class="uk-position-cover uk-overlay uk-overlay-primary uk-flex uk-flex-center uk-flex-middle"></div>
						</article>
					</li>
	    		</ul>
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
		        			<input type="submit" class="uk-button uk-button-primary" value="コメントを送信" @click="regComment">
				        </div>
				    </div>
		        </form>
			</div>
		</div>
	</div>
</div>
<script>
var app = new Vue({
	el: '#app',
	data: {
		comments: [],
		count: 0,
		page : 0,
		canAjax : true,
	},
	filters: {
    },
	created : function(){
		this.getComment();
	},
    computed: {
		commentSort() {
			return this.comments.sort((a, b) => {
				return a.id - b.id;
			});
		},
    },
    watch: {},
	methods: {
		regComment: function()
		{
			let comment = $("textarea[name='comment']").val();
			let name = $("input[name='name']").val();
			UIkit.modal.confirm('コメントを投稿します。<br>よろしいですか。').then(function(){
			
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
				$.ajax({
					async: false,
		            url:"<?php echo $api_url ?>",
		            type:"POST",
		            data:{
		            	commentData : JSON.stringify( objectValueToURIencode({"comment":comment,"name":name}) ),
		            	Action : 'comment',
		            	_csrf : "<?php echo $csrf_token ?>",
		            },
		            dataType: "json"
		        })
		        // Ajaxリクエストが成功した時発動
		        .done( (data) => {
		        	
		            if(! data.result){
		        		UIkit.modal.alert("コメントに失敗しました");
		        		return false;
		            }
					location.reload(true);
		            
		        })
		        // Ajaxリクエストが失敗した時発動
		        .fail( (data) => {
		    		UIkit.modal.alert("コメントに失敗しました");
		    		return false;
		        })
		        // Ajaxリクエストが成功・失敗どちらでも発動
		        .always( (data) => {
		        	loading_remove();
		        });
			});
		},
		commentDelete: function(commentId)
		{
			let vm = this;
			UIkit.modal.confirm('コメントを削除します。<br>よろしいですか。').then(function(){
			
				loading();
				if(!vm.canAjax)
				{
					return ;
				}
				vm.canAjax = false;
				
				$.ajax({
					async: false,
		            url:"<?php echo $api_url ?>",
		            type:"POST",
		            data:{
		            	id : commentId,
		            	Action : 'commentDeleteApi',
		            	_csrf : "<?php echo $csrf_token ?>",
		            },
		            dataType: "json"
		        })
		        // Ajaxリクエストが成功した時発動
		        .done( (data) => {
		            if(! data.result){
		        		UIkit.modal.alert("コメント削除に失敗しました");
		        		return false;
		            }
					location.reload(true);
		            
		        })
		        // Ajaxリクエストが失敗した時発動
		        .fail( (data) => {
		    		UIkit.modal.alert("コメント削除に失敗しました");
		    		return false;
		        })
		        // Ajaxリクエストが成功・失敗どちらでも発動
		        .always( (data) => {
					vm.canAjax = true;
		        	loading_remove();
		        });
			});
		},
		getComment: function()
		{
			let vm = this;
			if(!vm.canAjax)
			{
				return ;
			}
			vm.canAjax = false;
			loading();
			$.ajax({
				async: true,
	            url:"<?php echo $api_url ?>",
	            type:"POST",
	            data:{
	            	page : vm.page + 1,
	            	Action : 'getCommentApi',
	            	_csrf : "<?php echo $csrf_token ?>",
	            },
	            dataType: "json"
	        })
	        // Ajaxリクエストが成功した時発動
	        .done( (data) => {
	        	data.data.forEach( function( comment ) {
        			vm.comments.push(comment);
				});
	        	//
	        	vm.count = data.count;
	        	vm.page = vm.page + 1;
	        })
	        // Ajaxリクエストが失敗した時発動
	        .fail( (data) => {
	    		UIkit.modal.alert("取得に失敗しました");
	    		return false;
	        })
	        // Ajaxリクエストが成功・失敗どちらでも発動
	        .always( (data) => {
				vm.canAjax = true;
	        	loading_remove();
	        });
		}
	}
});
</script>
