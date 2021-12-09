<?php
include_once 'NewJoyPla/autoload.php';

use App\Lib\UserInfo;
use App\Model\Hospital;
use App\Model\Distributor;

$nav = new StdClass;

$nav->user_info = new UserInfo($SPIRAL);

if($nav->user_info->isHospitalUser())
{
	$nav->hospital = Hospital::where('hospitalId',$nav->user_info->getHospitalId())->get();
	$nav->hospital = $nav->hospital->data->get(0);
}
if($nav->user_info->isDistributorUser())
{
	$nav->distributor = Distributor::where('distributorId',$nav->user_info->getDistributorId())->get();
	$nav->distributor = $nav->distributor->data->get(0);
	$nav->hospital = Hospital::where('hospitalId',$nav->distributor->hospitalId)->get();
	$nav->hospital = $nav->hospital->data->get(0);
}

$top_api_url = "%url/rel:mpgt:page_262241%";
$api_url = "%url/rel:mpgt:Notification%";
if(! $nav->user_info->isHospitalUser())
{
	$top_api_url = "%url/rel:mpgt:oroshiTopPage%";
	$api_url = "%url/rel:mpgt:NotificationDist%";
}

?>

<nav id="nav" class="uk-navbar-container no_print" uk-navbar="mode: click" uk-navbar>
	<div class="uk-navbar-left"> 
		<a href="%url/rel:mpg:top%" class="uk-navbar-item uk-logo">
			<img src="https://i02.smp.ne.jp/u/joypla/images/logo_png.png" />
		</a>
	</div>

	<div class="uk-navbar-right uk-margin-right" style="flex-wrap: nowrap;">
		<p class="uk-margin-auto-vertical uk-visible@s uk-margin-right uk-text-right">
			<?php
			if($nav->user_info->isHospitalUser())
			{
				echo $nav->hospital->hospitalName.'<br>';
			}
			if($nav->user_info->isDistributorUser())
			{
				echo $nav->distributor->distributorName.'<br>';
				echo '<span class="uk-text-small">担当施設：'.$nav->hospital->hospitalName. '</span><br>';
			}
			?>
			<?php echo $nav->user_info->getName();?> 様
		</p>
		<ul class="uk-navbar-nav">
			<li>
				<a href="https://support.joypla.jp/" target="support" uk-icon="icon: question; ratio: 1.5" title="ヘルプ"></a>
			</li>
			<li class="uk-inline">
				<a href="#" uk-icon="icon: bell; ratio: 1.5" title="お知らせ" uk-navbar="mode: click" uk-toggle="animation: uk-animation-fade"></a>
				<div class="uk-navbar-dropdown uk-card uk-navbar-dropdown-width-2 uk-padding-remove">
                   <div class="uk-card-body uk-height-max-large" style="overflow-y: scroll; padding: 15px">
				        <ul class="uk-list" v-if="notifications.length > 0 ">
				            <li v-for="notification in notifications">
				                <article class="uk-comment" style="padding:10px">
								    <header class="uk-comment-header uk-margin-top uk-margin-bottom">
								        <div class="uk-grid-medium uk-flex-middle" uk-grid>
								            <div class="uk-width-auto">
								                <img class="uk-comment-avatar" :src="notification.icon" width="40" height="40" alt="">
								            </div>
										    <div class="uk-comment-body uk-width-expand">
				                                <p v-html="notification.message"></p>
										    </div>
								            <div class="">
								                <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-heading" :href="notification.link">移動</a></h4>
								                <ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-remove-top">
								                </ul>
								            </div>
								        </div>
								    </header>
								</article>
				            </li>
						</ul>
			            <p v-else>最新の通知はありません</p>
				    </div>
		        </div>
		        <div v-show="badge">
					<span class="uk-badge" style="position: absolute;top: 13px;right: 0px;" v-text='count'></span>
		        </div>
			</li>
			<li>
				<a uk-icon="icon: menu; ratio: 1.5" uk-navbar="mode: click" uk-toggle="animation: uk-animation-fade"></a>
				<div class="uk-navbar-dropdown uk-card uk-navbar-dropdown-width-2 uk-padding-remove">
                   <div class="uk-card-body">
				        <div class="uk-grid-small uk-flex-middle" uk-grid>
				            <div class="uk-width-expand">
				                <h3 class="uk-card-title uk-margin-remove-bottom">
								<?php
								if($nav->user_info->isHospitalUser())
								{
									echo $nav->hospital->hospitalName.'<br>';
								}
								if($nav->user_info->isDistributorUser())
								{
									echo $nav->distributor->distributorName.'<br>';
									echo '<span class="uk-text-small">担当施設：'.$nav->hospital->hospitalName. '</span><br>';
								}
								?>
				                </h3>
								<span class="uk-label uk-padding-small uk-label-success uk-padding-remove-vertical"><?php echo $nav->user_info->getUserPermissionName()?></span>
								<p><?php echo $nav->user_info->getName();?> 様</p>
				            </div>
				        </div>
				        <ul class="uk-list uk-navbar-dropdown-nav uk-list-large">
	                        <li><a href="%url/rel:mpg:top%" ><span class="uk-margin-small-right" uk-icon="icon:home; ratio:1.5"></span>TOPへ戻る</a></li>
	                        <?php if($nav->user_info->isDistributorUser()): ?>
	                        <li><a href="#" onclick="document.accountSelect_nav.submit();"><span class="uk-margin-small-right" uk-icon="icon:pencil; ratio:1.5"></span>卸業者アカウント切り替え</a></li>
	                        <?php endif ?>
	                        <li><a href="#" onclick="document.userInfoChange_nav.submit();"><span class="uk-margin-small-right" uk-icon="icon:pencil; ratio:1.5"></span>ユーザー情報変更</a></li>
	                        <li><a href="#" onclick="document.contactUs.submit()"><span class="uk-margin-small-right" uk-icon="icon:mail; ratio:1.5"></span>お問合せ</a></li>
	                        <li><a href="%form:act:logout%" ><span class="uk-margin-small-right" uk-icon="icon:sign-out; ratio:1.5"></span>ログアウト</a></li>
                    	</ul>
				    </div>
		        </div>
			</li>
		</ul>
		
		<?php if($nav->user_info->isHospitalUser()) : ?>
		<form method="post" action="<?php echo $top_api_url ?>" name="userInfoChange_nav">
			<input type="hidden" name="Action" value="userInfoChange">
		</form>
		<?php endif ?>
		<?php if($nav->user_info->isDistributorUser()) : ?>
		<form method="post" action="<?php echo $top_api_url ?>" name="userInfoChange_nav">
			<input type="hidden" name="Action" value="userInfoChange">
		</form>
		<form method="post" action="<?php echo $top_api_url ?>" name="accountSelect_nav">
			<input type="hidden" name="Action" value="accountSelect">
		</form>
		<?php endif ?>
		
	    <form method="post" action="<?php echo $top_api_url ?>" name="contactUs">
			<input type="hidden" name="Action" value="contactUs">
		</form>
	</div>
</nav>
<script>
var nav = new Vue({
	el: '#nav',
	data: {
		badge: false,
		count: 0,
		notifications: {},
	},
	methods: {
		window:onload = function() {  
		   nav.notification();
		},
		notification: function ()
		{
			$.ajax({
				async: true,
				url : "<?php echo $api_url ?>",
                type:'POST',
                data:{
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
            	nav.notifications = data.data;
            	nav.count = data.count;
            	nav.badge = (data.count > 0);
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
            	//console.log(data);
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
            	//console.log(data);
            });
		}
	}
});
</script>