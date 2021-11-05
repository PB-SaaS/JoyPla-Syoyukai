<script>
	class CardList 
	{
		  cardPrint()
		  {
		  	let ids = [];
		  	$('tbody input[type=checkbox]:checked').each(function(index, element){ 
		  		ids.push(element.value);
		  	});
				UIkit.modal.alert('カードを発行します').then(function(){
					if(ids.length == 0){
						UIkit.modal.alert('発行するカード情報にチェックを入れてください');
						return false;
					}
			  		$('input[name=card_ids]').val(JSON.stringify(ids));
			  		$('form[name=cardCreate]').submit();
				});
		  }
	}
	card_list = new CardList();
</script>