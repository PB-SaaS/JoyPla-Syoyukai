<?php
include_once 'NewJoyPla/autoload.php';

use App\Lib\UserInfo;
use App\Model\Hospital;
use App\Model\Distributor;
use App\Model\DistributorUser;

$nav = new StdClass;
$nav->user_info = $user_info;
if(!$user_info){
	$nav->user_info = new UserInfo($SPIRAL);
}
if($nav->user_info->isHospitalUser())
{
	$nav->hospital = Hospital::where('hospitalId',$nav->user_info->getHospitalId())->plain()->get();
	$nav->hospital = $nav->hospital->data->get(0);
}
if($nav->user_info->isDistributorUser())
{
	$user = DistributorUser::where('loginId', $nav->user_info->getLoginId())->plain()->value('distributorId')->get();
	$user = $user->data->get(0);
	$nav->distributor = Distributor::where('distributorId',$user->distributorId)->plain()->get();
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
				<a href="https://support.joypla.jp/" target="support" title="ヘルプ">
					<svg xmlns="http://www.w3.org/2000/svg" style="width:30px; height:30px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
						<path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
				</a>
			</li>
			<li ref="notificationModal">
				<a href="#" title="お知らせ" v-on:click="notificationView = !notificationView" style="position: relative">
					<svg xmlns="http://www.w3.org/2000/svg" style="width:30px; height:30px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
						<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
					</svg>
					<div v-show="badge">
						<span class="uk-badge" style="position: absolute;top: 13px;right: 0px;" v-text='count'></span>
					</div>
				</a>
				<div class="md:right-[20px] md:left-auto right-0 left-0 top-[80px] md:w-[400px] block p-0 mt-[15px] absolute z-50 box-border text-[#666] bg-white" style="box-shadow: 0 5px 12px rgb(0 0 0 / 15%);" v-if="notificationView">
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
			</li>
			<li ref="userModal">
				<a  href="#" v-on:click="userModalView = !userModalView">
				<svg xmlns="http://www.w3.org/2000/svg" style="width:30px; height:30px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
					<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
				</svg>
				</a>
				<div  class="md:right-[20px] md:left-auto right-0 left-0 md:w-[400px] block p-0 mt-[15px] absolute z-50 box-border text-[#666] bg-white" style="box-shadow: 0 5px 12px rgb(0 0 0 / 15%);" v-if="userModalView">
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
	                        <li>
								<a href="%url/rel:mpg:top%" >
								<svg xmlns="http://www.w3.org/2000/svg" style="height:30px; width:30px ; margin-right: 10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
									<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
								</svg>
								TOPへ戻る
								</a>
							</li>
	                        <?php if($nav->user_info->isDistributorUser()): ?>
	                        <li>
								<a href="#" onclick="document.accountSelect_nav.submit();">
								<svg xmlns="http://www.w3.org/2000/svg" style="height:30px; width:30px;  margin-right: 10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
									<path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
								</svg>
								卸業者アカウント切り替え
								</a>
							</li>
	                        <?php endif ?>
	                        <li>
								<a href="#" onclick="document.userInfoChange_nav.submit();">
								<svg xmlns="http://www.w3.org/2000/svg" style="height:30px; width:30px; margin-right: 10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
									<path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
								</svg>
								ユーザー情報変更
								</a>
							</li>
	                        <li>
								<a href="#" onclick="document.contactUs.submit()">
								<svg xmlns="http://www.w3.org/2000/svg" style="height:30px; width:30px;  margin-right: 10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
									<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
								</svg>
								お問合せ</a>
							</li>
							<li>
								<a href="%form:act:logout%">
								<svg xmlns="http://www.w3.org/2000/svg" style="height:30px; width:30px;  margin-right: 10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
									<path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
								</svg>
								ログアウト</a>
							</li>
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
		notificationView: false,
		userModalView: false,
	},
	mounted(){
		  this.notification();
          addEventListener('click', this.clickOutside);
    },
	beforeMount(){ 
          removeEventListener('click', this.clickOutside)
    },
	methods: {
		clickOutside: function(e){
          // [対象の要素]が[クリックされた要素]を含まない場合
          if (e.target instanceof Node && !this.$refs.notificationModal?.contains(e.target)) {
            this.notificationView = false;
          }
          // [対象の要素]が[クリックされた要素]を含まない場合
          if (e.target instanceof Node && !this.$refs.userModal?.contains(e.target)) {
            this.userModalView = false;
          }
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