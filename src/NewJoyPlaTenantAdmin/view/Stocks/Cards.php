<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">
    <div class="uk-container uk-container-expand">
    	<h1>カード情報管理</h1>
		<div class="uk-margin spiral_table_area" style="display:none">
			%sf:usr:search59%
		</div>
    </div>
</div>
<script>
let hospitalName = '';
$('#content .smp-search-form .smp-sf-row').each(function(index, element){
            
	if($('.smp-sf-head',this).text().trim(" ") === "病院名")
	{
		hospitalName = $('.smp-sf-body input',this)[0].value;
		if(hospitalName === '')
		{
			$('button[name=cardprint]').prop('disabled',true);
		} else {
			$('button[name=cardprint]').prop('disabled',false);
		}
	}
});

function cardPrint(){

	console.log($('input[id^="smp-table-check"][type="checkbox"]:checked'));
	let text = [];
	$('input[id^="smp-table-check"][type="checkbox"]:checked').each(function(index,elm){
		text.push(elm.value);
	});
	console.log(text);
};

</script>