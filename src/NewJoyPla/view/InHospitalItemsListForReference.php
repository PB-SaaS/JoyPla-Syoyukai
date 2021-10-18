
<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
    <div class="uk-container uk-container-expand">
    	<h2 class="page_title"><?php echo $page_title ?></h2>
    	<hr>
    	<div class="" id="tablearea">
    		<?php echo $keyword ?>
    	</div>
    </div>
	</div>
</div>
<script>

class InHospitalItemsListForReference {
	
	search(inHospitalItemId)
	{
		  $.ajax({
          async: false,
          url: "<?php echo $api_url ?>",
          type:'POST',
          data:{
              _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
              Action : "searchApi",
              inHospitalItemId : inHospitalItemId,
          },
          dataType: 'json'
      })
      // Ajaxリクエストが成功した時発動
      .done( (data) => {
          if(data.code != 0){
              UIkit.modal.alert('データの取得に失敗しました');
              return false;
          }
          window.opener.addTr(data.data, 1, 0);
      })
      // Ajaxリクエストが失敗した時発動
      .fail( (data) => {
          UIkit.modal.alert('データの取得に失敗しました');
      })
      // Ajaxリクエストが成功・失敗どちらでも発動
      .always( (data) => {
          loading_remove();
      });
	}
}

let in_hp_items_list_reference = new InHospitalItemsListForReference();

function hanei(elm){
	let shouhinTr = $(elm.parentNode.parentNode);
	shouhinObject = JSON.parse($('.json',shouhinTr).text().replace(/\r?\n/g, '').trim());
	window.opener.addTr(shouhinObject, 1, 0);
}
</script>