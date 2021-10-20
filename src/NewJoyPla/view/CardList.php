<div class="animsition" uk-height-viewport="expand: true">
  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
	    <div class="uk-container uk-container-expand">
	    	<ul class="uk-breadcrumb no_print">
			    <li><a href="%url/rel:mpg:top%">TOP</a></li>
			    <li><span>カード一覧</span></li>
			</ul>
					<div class="no_print uk-margin">
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="$('#exportButton').click();">
								<input type="button" onclick="card_list.cardPrint()" class="uk-button uk-button-primary" value="カード発行">
		    			<form action="<?php echo $api_url ?>" target="_blank" method="post" name='cardCreate'>
		    			  <input type="hidden" name="Action" value="cardLabelPrint">
								<input type="hidden" name="card_ids" value="">
		    			</form>
          </div>
	    	<div class="uk-width-1-1" uk-grid>
	    		<div class="uk-width-3-4@l uk-width-2-3">
	    			<h2>カード一覧</h2>
				</div>
	    	</div>
	    	<hr>
	    	<div class="uk-margin">
	    	    %sf:usr:search32:mstfilter%
			</div>
		</div>
	</div>
</div>
<script>
	class CardList 
	{
		  cardPrint()
		  {
		  	let ids = [];
		  	$('tbody input[type=checkbox]').each(function(index, element){ 
		  		ids.push(element.value);
		  	});
				UIkit.modal.alert('カードを発行します').then(function(){
		  		$('input[name=card_ids]').val(JSON.stringify(ids));
		  		$('form[name=cardCreate]').submit();
				});
		  }
	}
	card_list = new CardList();
</script>