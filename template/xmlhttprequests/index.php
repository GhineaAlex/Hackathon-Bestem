<?php
if ( ! defined ( 'ALLOW' ) ) exit ;

use engine\core as core ;
use engine\database as DB ;
use engine\user as __U ;
use engine\functions as __F ;

define ( 'IS_XML' , isset ( $_SERVER [ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower ( $_SERVER [ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' ) ;
if ( ! IS_XML ) exit ( 'No direct script access allowed!' ) ;
if ( ! isset ( $configs [ 1 ] ) ) exit ;


if ( $configs [ 1 ] === 'add_item' ) {

	if ( empty ( $_POST ) ) exit ;
	if ( self::$_user_type == 0 ) exit ( 'Denied' ) ;


	$result = __U::__add_item_cart ( __F::__protected_string_header ( 'appid' , INPUT_POST ) , __F::__protected_string_header ( 'item_id' , INPUT_POST ) , __F::__protected_string_header ( 'image' , INPUT_POST ) , __F::__protected_string_header ( 'name' , INPUT_POST ) ) ;


	if ( $result == 0 ) echo 'You have reached the maximum number from this item.' ;
	else if ( $result == 3 ) echo 'Insuficients funds.' ;
	else if ( $result == 1 ) {
		echo 'Item added.' ;
		$price = __U::__basket_price ( ) ;
?>
	<script>
		$ ( '#items_count' ) . html ( '<?= __U::__get_user_items_cart ( ) ?>' ) ;
		$ ( '#cart_price' ) . html ( '<?= $price ; ?>/<?= self::$_user_information [ 'coins' ] ; ?> Points' ) ;
	</script>
<?php
	}
}
else if ( $configs [ 1 ] === 'remove_item' ) {

	if ( empty ( $_POST ) ) exit ;
	if ( self::$_user_type == 0 ) exit ( 'Denied' ) ;

	__U::__remove_item_cart ( __F::__protected_string_header ( 'item_id' , INPUT_POST ) ) ;
	echo 'Removed item.' ;
	$price = __U::__basket_price ( ) ;
?>
	<script>
		$ ( '#items_count' ) . html ( '<?= __U::__get_user_items_cart ( ) ?>' ) ;
		$ ( '#cart_price' ) . html ( '<?= $price ; ?>/<?= self::$_user_information [ 'coins' ] ; ?> Points' ) ;
	</script>
<?php

}
else if ( $configs [ 1 ] === 'withdraw_items' ) {
	if ( self::$_user_type == 0 ) exit ( 'You need to login first.' ) ;
	if ( self::$_user_cart_count == 0 ) exit ;


	$result = DB::__db_query (
		core::$mysql_handle ,
		DB::$DB_FETCH ,
		DB::$DB_NONPROTECTED ,
		'SELECT `cart`, `trade_link`, `restricted_withdraw` FROM `accounts` WHERE `openid`=:openid' ,
		self::$_user_information [ 'openid' ]
	) ;

	$bot_in_trade = '<div id="withdraw_message_content" style="background: #61d334 ;"><i class="fa fa-times" aria-hidden="true" id="close_wi" style="position: absolute ;cursor: pointer ;top: 10px ;right: 10px ;"></i><h1 style="padding: 0 ;">Bot information</h1>You are already processing an withdraw. Wait for trade offer.</div><script>$ ( "#close_wi" ) . click ( function ( ) { $ ( "#withdraw_message_content" ) . fadeOut ( ) ; } ) ; </script>' ;

	$bot_in_trade_user = '<div id="withdraw_message_content"><i class="fa fa-times" aria-hidden="true" id="close_wi" style="position: absolute ;cursor: pointer ;top: 10px ;right: 10px ;"></i><h1 style="padding: 0 ;">Bot information</h1>Somebody else is already processing an withdraw.</div><script>$ ( "#close_wi" ) . click ( function ( ) { $ ( "#withdraw_message_content" ) . fadeOut ( ) ; } ) ; </script>' ;

	//$bot_trade = '<div id="withdraw_message_content" style="background: #61d334 ;"><i class="fa fa-times" aria-hidden="true" id="close_wi" style="position: absolute ;cursor: pointer ;top: 10px ;right: 10px ;"></i><h1 style="padding: 0 ;">Bot information</h1>Your request has been send to the bot. Wait for trade offer.</div><script>$ ( "#close_wi" ) . click ( function ( ) { $ ( "#withdraw_message_content" ) . fadeOut ( ) ; } ) ; </script>' ;
	$bot_trade = '<div id="withdraw_message_content" style="background: #61d334 ;"><i class="fa fa-times" aria-hidden="true" id="close_wi" style="position: absolute ;cursor: pointer ;top: 10px ;right: 10px ;"></i><h1 style="padding: 0 ;">Bot information</h1>The bot is currently offline. Your request has been saved and sent to the database.</div><script>$ ( "#close_wi" ) . click ( function ( ) { $ ( "#withdraw_message_content" ) . fadeOut ( ) ; } ) ; </script>' ;

	$trade_offer = '<div id="withdraw_message_content"><i class="fa fa-times" aria-hidden="true" id="close_wi" style="position: absolute ;cursor: pointer ;top: 10px ;right: 10px ;"></i><h1 style="padding: 0 ;">Bot information</h1>Add you trade offer link in your profile.</div><script>$ ( "#close_wi" ) . click ( function ( ) { $ ( "#withdraw_message_content" ) . fadeOut ( ) ; } ) ; </script>' ;

	if ( empty ( $result ) ) exit ;
	else {
		//if ( preg_match ( '/https:\/\/steamcommunity.com\/tradeoffer\/new\/\?partner=([0-9]+)&token=([0-9A-Za-z_-]{8})/' , $result [ 'trade_link' ] ) == false ) echo $trade_offer ;
		//else {
			//if ( $result [ 'restricted_withdraw' ] == 1 ) echo $bot_in_trade ;
			//else {
                if (strlen($result ['cart']) == 0) echo '<div id="withdraw_message_content" style="background: #61d334 ;"><i class="fa fa-times" aria-hidden="true" id="close_wi" style="position: absolute ;cursor: pointer ;top: 10px ;right: 10px ;"></i><h1 style="padding: 0 ;">Bot information</h1>The cart is empty.</div><script>$ ( "#close_wi" ) . click ( function ( ) { $ ( "#withdraw_message_content" ) . fadeOut ( ) ; } ) ; </script>';

				/*$bot = DB::__db_query (
					core::$mysql_handle ,
					DB::$DB_FETCH ,
					DB::$DB_PROTECTED ,
					'SELECT `trade_time`, `in_trade` FROM `bots_info` WHERE `bot_id`=\'1\''
				) ;

				if ( $bot [ 'in_trade' ] == 1 ) {
					if ( $bot [ 'trade_time' ] + 60 * 10 < time ( ) ) {
						__U::__extract_cart ( ) ;
						echo $bot_trade ;
					}
					else {
						echo $bot_in_trade_user ;
					}

				}
				else {*/
					__U::__extract_cart ( ) ;
					echo $bot_trade ;

				//}
			//}
		//}
	}
}
else if ( $configs [ 1 ] === 'cart_drop' ) {
	if ( self::$_user_type == 0 ) exit ( 'Denied' ) ;
	$cart = __U::__get_user_cart ( ) ;
	if ( $cart !== 'NULL' ) {
		if ( ! empty ( $cart ) ) {
?>
	<div class="block">
		<table>
<?php
			foreach ( $cart as $key => $value ) {
?>
			<tr id="i_<?php echo $value -> classid ; ?>">
				<td>
					<img style="height: 50px ;" src="<?php echo $value -> image ; ?>"/>
				</td>
				<td>
					<?php echo $value -> name ; ?><br><?php echo $value -> price ; ?> Points
				</td>
				<td>
					<img class="remove_item" item_id="<?php echo $value -> classid ; ?>" src="<?php echo TEMPLATE_URL ; ?>styles/images/remove.png" />
				</td>
			</tr>
<?php
			}
?>
		</table>
	</div>
			<script>
				$ ( '.remove_item' ) . click ( function ( ) {
					var item_id = $ ( this ) . attr ( 'item_id' ) ;
					$ ( '#i_' + item_id ) . remove ( ) ;
					$.post ( BOARD_AJAX + '/remove_item' , { 'item_id' : item_id } , function ( data ) {
						$ ( '#response' ) . fadeIn ( ) ;
						setInterval ( function ( ) { $ ( '#response' ) . fadeOut ( ) ; } , 5000 ) ;
					 	$ ( '#response' ) . html ( data ) ;
					} ) ;
				} ) ;
				$ ( '#withdraw_items' ) . click ( function ( ) {
					$.post ( BOARD_AJAX + '/withdraw_items' , { } , function ( data ) {
						$ ( '#withdraw_message' ) . html ( data ) ;
					} ) ;
				} ) ;
			</script>
			<div id="withdraw_items" style="cursor: pointer ;">Withdraw items</div>
<?php
		}
	}
	else {
		echo 'Empty basket.' ;
	}
}
else if ( $configs [ 1 ] === 'provider' ) {
	if ( self::$_user_type == 0 ) exit ( 'You need to login first.' ) ;
	if ( empty ( $_POST ) ) exit ;

	$provider = __F::__protected_string_header ( 'provider' , INPUT_POST ) ;
	if ( $provider == 'Adgate' ) {
		echo '<div id="status"></div><iframe id="frame_provider" class="iframe_tab" frameborder="0" allowfullscreen></iframe>

		<script>
			$(document).ready(function() {
				var status = $("#status");
			    var iframe = $("#frame_provider");

			    status.html("<i class=\"fa fa-spinner fa-spin fa-3x fa-fw\"></i>");

			    iframe.on("load", function() {
			        status.html("");
			    });

			    setTimeout(function() {
			        iframe.attr("src", "https://wall.adgaterewards.com/naeWqA/' . self::$_user_information [ 'ID' ] . '");
			    },
			    1000);
		    });
		</script>' ;
	}
	else if ( $provider == 'Adscend' ) {
		echo '<div id="status"></div><iframe id="frame_provider" class="iframe_tab" frameborder="0" allowfullscreen></iframe>

		<script>
			$(document).ready(function() {
				var status = $("#status");
			    var iframe = $("#frame_provider");

			    status.html("<i class=\"fa fa-spinner fa-spin fa-3x fa-fw\"></i>");

			    iframe.on("load", function() {
			        status.html("");
			    });

			    setTimeout(function() {
			        iframe.attr("src", "https://asmwall.com/adwall/publisher/111481/profile/10913?subid1=' . self::$_user_information [ 'ID' ] . '");
			    },
			    1000);
		    });
		</script>' ;
	}
	else if ( $provider == 'Persona-ly' ) {
		echo '<div id="status"></div><iframe id="frame_provider" class="iframe_tab" frameborder="0" allowfullscreen></iframe>

		<script>
			$(document).ready(function() {
				var status = $("#status");
			    var iframe = $("#frame_provider");

			    status.html("<i class=\"fa fa-spinner fa-spin fa-3x fa-fw\"></i>");

			    iframe.on("load", function() {
			        status.html("");
			    });

			    setTimeout(function() {
			        iframe.attr("src", "https://persona.ly/widget/?appid=13b3e23b20eb7240b38b35f525b32847&userid=' . self::$_user_information [ 'ID' ] . '");
			    },
			    1000);
		    });
		</script>' ;
	}
	else if ( $provider == 'OfferToro' ) {
		echo '<div id="status"></div><iframe id="frame_provider" class="iframe_tab" frameborder="0" allowfullscreen></iframe>

		<script>
			$(document).ready(function() {
				var status = $("#status");
			    var iframe = $("#frame_provider");

			    status.html("<i class=\"fa fa-spinner fa-spin fa-3x fa-fw\"></i>");

			    iframe.on("load", function() {
			        status.html("");
			    });

			    setTimeout(function() {
			        iframe.attr("src", "https://www.offertoro.com/ifr/show/5007/' . self::$_user_information [ 'ID' ] . '/3636");
			    },
			    1000);
		    });
		</script>' ;
	}
	else if ( $provider == 'Superrewards' ) {
		echo '<div id="status"></div><iframe id="frame_provider" class="iframe_tab" frameborder="0" allowfullscreen></iframe>

		<script>
			$(document).ready(function() {
				var status = $("#status");
			    var iframe = $("#frame_provider");

			    status.html("<i class=\"fa fa-spinner fa-spin fa-3x fa-fw\"></i>");

			    iframe.on("load", function() {
			        status.html("");
			    });

			    setTimeout(function() {
			        iframe.attr("src", "https://wall.superrewards.com/super/offers?h=thjpozojjmv.23114930455&uid=' . self::$_user_information [ 'ID' ] . '");
			    },
			    1000);
		    });
		</script>' ;
    }
	else if ( $provider == 'Minutestaff' ) {
		echo '<div id="status"></div><iframe id="frame_provider" class="iframe_tab" frameborder="0" allowfullscreen></iframe>

		<script>
			$(document).ready(function() {
				var status = $("#status");
			    var iframe = $("#frame_provider");

			    status.html("<i class=\"fa fa-spinner fa-spin fa-3x fa-fw\"></i>");

			    iframe.on("load", function() {
			        status.html("");
			    });

			    setTimeout(function() {
			        iframe.attr("src", "https://offerwall.minutecircuit.com/display.php?app_id=1052&site_code=9ad87515bc64c330&user_id=' . self::$_user_information [ 'ID' ] . '");
			    },
			    1000);
		    });
		</script>' ;
    }
    else if ( $provider == 'Smores.tv' ) {
		echo '<script>
			$(document).ready(function() {
			    setTimeout(function() {
			        window.location = "https://partner.smores.tv/click.php?aff=111481&camp=2950854&from=10913&prod=4&prod_channel=5&sub1=' . self::$_user_information [ 'ID' ] . '";
			    },
			    100);
		    });
		</script>' ;
	}
	else {
		echo '
			<div>
				<div class="inline-block item_contariner" style="height: 200px ;width: 30% ;padding: 20px ;margin: 30px ;border-radius: 0px ;border: 1px solid #9fa8ae ;vertical-align: middle ;">
					<span class="title" style="font-size: 30px ;">
						E-mail Bonus
					</span>
					<span class="title_content block">
						Add you email address and win:
						<h1>50 points</h1>
						' . ( ( strlen (  self::$_user_information [ 'email' ] ) > 0 ) ? '' : '
						<a class="button" href="' . URL . '/user/' . self::$_user_information [ 'openid' ] . '">Collect</a>' ) . '
					</span>
				</div>
				<div class="inline-block item_contariner" style="height: 200px ;width: 30% ;padding: 20px ;margin: 30px ;border-radius: 0px ;border: 1px solid #9fa8ae ;vertical-align: middle ;">
					<span class="title" style="font-size: 30px ;">
						Referral code
					</span>
					<span class="title_content block">
						Use a referral code from a friend and win:
						<h1>60 points</h1>
						' . ( ( self::$_user_information [ 'referral_to' ] != 0 ) ? '' : '
						<a class="button" href="' . URL . '/user/' . self::$_user_information [ 'openid' ] . '">Collect</a>' ) . '
					</span>
				</div>
			</div>

			<div>
				<div class="inline-block item_contariner" style="height: 200px ;width: 30% ;padding: 20px ;margin: 30px ;border-radius: 0px ;border: 1px solid #9fa8ae ;vertical-align: middle ;">
					<span class="title" style="font-size: 30px ;">
						Daily Reward
					</span>

					<span class="title_content block">
						Everyday you can click this button to win:
						<h1>5 points</h1>
						' . ( ( self::$_user_information [ 'next_reward' ] > time ( ) ) ? 'Next reward in: '. gmdate ( 'D, d M Y H:i:s T' , self::$_user_information [ 'next_reward' ] )  : '
						<a class="button" id="get_reward" style="cursor: pointer ;">Collect</a>' ) . '
					</span>
					<script>
						$ ( \'#get_reward\' ) . click ( function ( ) {
							$ . ajax ( {
				                url: BOARD_AJAX + \'/dailyreward\' ,
				                beforeSend: function ( ) { } ,
				                success: function ( html ) { location.reload ( ) ; }
				            } ) ;
			            } ) ;
					</script>
				</div>
				<div class="inline-block item_contariner" style="height: 200px ;width: 30% ;padding: 20px ;margin: 30px ;border-radius: 0px ;border: 1px solid #9fa8ae ;vertical-align: middle ;">
					<span class="title" style="font-size: 30px ;">
						Referral code - points
					</span>
					<span class="title_content block">
						Everytime someone uses your referral code, you get:
						<h1>10 points</h1>
					</span>
				</div>
			</div>
		' ;

	}

}
else if ( $configs [ 1 ] === 'support' ) {
	if ( empty ( $_POST ) ) exit ;

	$support = __F::__protected_string_header ( 'support' , INPUT_POST ) ;
	if ( $support == 'Login' ) {
		echo '<div style="text-align: left ;padding: 5px ;font-size: 20px ; color:#000000 ;"><h1>How can you login?</h1>It is pretty straight forward. You just have to use your Steam Account. Press the Sign in through Steam button on the left home bar.<br/>After you login, we will ask for the Steam Trade Offer link.<br/>You can get the Steam Trade Offer link here: <a href="http://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url">http://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url</a> <br/>You are now successfully logged in.</div>' ;
	}
	else if ( $support == 'Earn Points' ) {
		echo '<div style="text-align: left ;padding: 5px ;font-size: 20px ; color:#000000 ;"><h1>How can you earn points?</h1><ul><li>If you are logged in you can complete any offer from any provider that we have on site.</li><li>You will need to select a provider first and then follow the instructions that you are provided.</li><li>After you complete an offer, your number of points will be updated and you will be able to withdraw skins.</li></ul>As a tip, when you will complete offers like surveys or any other type of offer:<ul><li>Do NOT try to cheat.</li><li> Do NOT use fake or misleading information.</li><li>Do NOT try to avoid the system.</li></ul>These actions will result in a ban on your account and IP, also you won’t be able to complete offers with those providers.</div>' ;
	}
	else if ( $support == 'Missing Points' ) {
		echo '<div style="text-align: left ;padding: 5px ;font-size: 20px ; color:#000000 ;"><h1>Are you missing points?</h1>You did complete an offer, but you did not get the points? Every provider has a support and we will show below where you can find them.<br/>Also, you can contact us at: antsy.help@gmail.com for any missing points or any other issue<br/>Adgate<br/><img src="http://i.imgur.com/jd5g6Wo.png" style="width:700px ;" /><br/>Adscend<br/><img src="http://i.imgur.com/xgBs6u8.png" style="width:700px ;" /><br/>Persona-ly<br/><img src="http://i.imgur.com/UONJ4Kt.png" style="width:700px ;" /></div>' ;
	}
	else if ( $support == 'Missing Trade Offer' ) {
		echo '<div style="text-align: left ;padding: 5px ;font-size: 20px ; color:#000000 ;"><h1>Are you missing a Trade Offer?</h1>Contact us at: antsy.help@gmail.com<br/>Usually the trade bot doesn’t make mistakes, but if there is a busy period when more users tried to withdraw the same time, some errors might happen.</div>' ;
	}
	else if ( $support == 'Privacy Policy' ) {
		echo '<div style="text-align: left ;padding: 5px ;font-size: 20px ; color:#000000 ;"><h1>Privacy Policy</h1>The only information that we collect are:<ul><li>SteamID64</li><li>Steam Username</li><li>Steam Avatar Image</li><li>Steam Trade Offer Link</li></ul>We have NO access to your password, email or any kind of information like that.<br/>The only information that we might display is how many points you earned on a leaderboard.<br/>Providers are third party entities with whom we agreed to publish their offers.</div>' ;
	}
	else if ( $support == 'Terms of Service' ) {
		echo '<div style="text-align: left ;padding: 5px ;font-size: 20px ; color:#000000 ;"><h1>Terms of Service</h1>By using antsy.xyz you agree to the following terms of service:<br/>
These terms and conditions govern your use of this website.<br/>
By using this website, you accept these terms and conditions in full and without reservation.<br/>
If you disagree with these terms and conditions or any part of these terms and conditions, you must not use this website.<br/>
<h2>Acceptable use</h2>
<ul>
<li>You must not use this website in any way that causes, or may cause, damage to the website or impairment of the availability or accessibility of antsy.xyz or in any way which is unlawful, illegal, fraudulent or harmful, or in connection with any unlawful, illegal, fraudulent or harmful purpose or activity.</li>

<li>You must not use this website to copy, store, host, transmit, send, use, publish or distribute any material which consists of (or is linked to) any spyware, computer virus, Trojan horse, worm, keystroke logger, rootkit or other malicious computer software.</li>

<li>You must not use this website or any part of it to transmit or send unsolicited commercial communications.</li>

<li>You must not use this website for any purposes related to marketing without the express written consent of www.antsy.xyz</li>
<li>www.antsy.xyz may disable your user account without notice or explanation.</li>
<li>antsy.xyz does not make represntations or warranties in relation to this website or the information provided.</li>
</ul>
<h2>No warranties</h2>
www.antsy.xyz does not warrant that:
<ul>
<li>this website will be constantly available, or available at all; or</li>
<li>the information on this website is complete, true, accurate or non-misleading.</li>
</ul>
<h2>Limitations of liability</h2>
www.antsy.xyz will not be liable to you  in relation to the contents of, or use of, or otherwise in connection with.<br/>
<h2>Virtual Currency / Points</h2>
antsy.xyz uses virtual currency which is referred as “Points”. The “Points” do not come with a real monetary value, the “points” are not real money. You cannot redeem the “points” for real money..<br/>

<h2>Exceptions</h2>
Nothing in this website disclaimer will exclude or limit any warranty implied by law that it would be unlawful to exclude or limit; and nothing in this website disclaimer will exclude or limit the liability of antsy.xyz in any way.<br/>
<h2>Reasonableness</h2>
By using this website, you agree that the exclusions and limitations of liability set out in this website disclaimer are reasonable.<br/>
If you do not think they are reasonable, you must not use this website.<br/>

You accept that, as a limited liability entity, www.antsy.xyz has an interest in limiting the personal liability of its officers and employees.<br/>
You agree that you will not bring any claim personally against www.antsy.xyz’s employees in respect of any losses you suffer in connection with the website.<br/>
<h2>Unenforceable provisions</h2>
If any provision of this website disclaimer is, or is found to be, unenforceable under applicable law, that will not affect the enforceability of the other provisions of this website disclaimer.<br/>
<h2>Links</h2>
www.antsy.xyz has not reviewed all of the sites linked to its Internet web site and is not responsible for the contents of any such linked site.<br/>
Do NOT try to download any software stating to give you an any advantage in using this website, as this can be a reason for www.antsy.xyz to ban you from using this website.<br/>
<h2>Breaches of these terms and conditions</h2>
Without prejudice to www.antsy.xyz\'s other rights under these terms and conditions, if you breach these terms and conditions in any way,<br/>
www.antsy.xyz may take such action including suspending your access to the website, prohibiting you from accessing the website, blocking computers using your IP address from accessing www.antsy.xyz,<br/> contacting your internet service provider to request that they block your access to the website and/or bringing court proceedings against you.<br/>

<h2>Variation</h2>
www.antsy.xyz may revise these terms and conditions from time-to-time.<br/>
Revised terms and conditions will apply to the use of this website from the date of the publication of the revised terms and conditions on this website.<br/>
Please check this page regularly to ensure you are familiar with the current version.<br/>
<h2>Entire agreement</h2>
These terms and conditions, together with www.antsy’s Privacy Policy constitute the entire agreement between you and www.antsy.xyz in relation to your use of this website, and supersede all previous<br/> agreements in respect of your use of this website.</div>' ;
	}
	else if ( $support == 'Contact' ) {
		echo '<div style="text-align: left ;padding: 5px ;font-size: 20px ; color:#000000 ;"><h1>Contact</h1>antsy.help@gmail.com<br/><br/><br/>Some other questions:<br/><br/><ul><li>Why aren’t more offers available?<br/>Providers will use your IP to track your country and to offer specific tasks.<br/>We are trying to offer more providers with tasks for different countries.</li><li>Is there any bonus available?<br/>We will implement bonuses and other ways to gain coins. Just follow the website for more news.</li></div>' ;
	}
}
else if($configs [ 1 ] === 'address'){
	if ( self::$_user_type == 0 ) exit ( 'Denied' ) ;
	if ( empty ( $_POST ) ) exit ;


	$content = __F::__protected_string ( $_POST [ 'address' ] ) ;

			DB::__db_query (
				core::$mysql_handle ,
				DB::$DB_FETCH_NONE ,
				DB::$DB_PROTECTED ,
				'UPDATE `accounts` SET `address`=:addr WHERE `openid`=:sid' ,
				$content,
				self::$_user_information [ 'openid' ]
			) ;

}
else if ( $configs [ 1 ] === 'tradeurl' ) {
	if ( self::$_user_type == 0 ) exit ( 'Denied' ) ;
	if ( empty ( $_POST ) ) exit ;


	$content = strip_tags ( $_POST [ 'trade' ] ) ;
	if ( strlen ( $content ) == 0 ) echo 'Invalid trade url.' ;
	else {

		$result = __U::__add_trade_link ( $content ) ;
		if ( $result == false ) echo 'Invalid trade url.' ;
		else {
			echo 'Succes.' ;
			DB::__db_query (
				core::$mysql_handle ,
				DB::$DB_FETCH_NONE ,
				DB::$DB_PROTECTED ,
				'UPDATE `accounts` SET `tutorial_earn`=\'1\' WHERE `openid`=:sid' ,
				self::$_user_information [ 'openid' ]
			) ;
		}
	}

}
else if ( $configs [ 1 ] === 'referralurl' ) {
	if ( self::$_user_type == 0 ) exit ( 'Denied' ) ;
	if ( empty ( $_POST ) ) exit ;


	$content = $_POST [ 'url' ] ;
	if ( strlen ( $content ) == 0 ) echo 'Invalid code.' ;
	else {

		$result = __U::__add_referral ( $content ) ;
		if ( $result == false ) echo 'Invalid code.' ;
		else echo '<script>location.reload();</script>Succes.' ;
	}

}
else if ( $configs [ 1 ] === 'emailurl' ) {
	if ( self::$_user_type == 0 ) exit ( 'You need to login first.' ) ;
	if ( empty ( $_POST ) ) exit ;


	$content = $_POST [ 'url' ] ;
	if ( strlen ( $content ) == 0 ) echo 'Invalid email.' ;
	else {
		$result = __U::__add_email ( $content ) ;
		if ( $result == false ) echo 'Invalid email.' ;
		else echo '<script>location.reload();</script>Succes.' ;
	}

}
else if ( $configs [ 1 ] === 'referralcode' ) {
	if ( self::$_user_type == 0 ) exit ( 'You need to login first.' ) ;
	if ( empty ( $_POST ) ) exit ;

	$content = $_POST [ 'url' ] ;

	if ( ( strlen ( $content ) <= 0 || strlen ( $content ) > 8 ) ) echo 'Invalid referral code.' ;
	else {
		if ( strrpos ( $content , " " ) !== false  ) echo 'Invalid referral code.' ;
		else {
			$result = DB::__db_query (
				core::$mysql_handle ,
				DB::$DB_FETCH ,
				DB::$DB_PROTECTED ,
				'SELECT `ID` FROM `accounts` WHERE `referral_code`=:code' ,
				$content
			) ;

			if ( !empty ( $result ) ) echo 'This referral code belongs to someone.' ;
			else {
				DB::__db_query (
					core::$mysql_handle ,
					DB::$DB_FETCH_NONE ,
					DB::$DB_PROTECTED ,
					'UPDATE `accounts` SET `referral_code`=:REF WHERE `openid`=:sid' ,
					$content ,
					self::$_user_information [ 'openid' ]
				) ;
				echo '<script>location.reload();</script>Succes.' ;
			}
		}
	}
}
else if ( $configs [ 1 ] === 'popup' ) {
	if ( self::$_user_type == 0 ) exit ;

	DB::__db_query (
		core::$mysql_handle ,
		DB::$DB_FETCH_NONE ,
		DB::$DB_PROTECTED ,
		'UPDATE `accounts` SET `view_pop` = \'1\' WHERE `openid`=:sid' ,
		self::$_user_information [ 'openid' ]
	) ;

}
else if ( $configs [ 1 ] === 'dailyreward' ) {
	if ( self::$_user_information [ 'next_reward' ] > time ( ) ) exit ;

	DB::__db_query (
		core::$mysql_handle ,
		DB::$DB_FETCH_NONE ,
		DB::$DB_PROTECTED ,
		'UPDATE `accounts` SET `next_reward`=:next, `coins`=`coins`+\'5\' WHERE `openid`=:sid' ,
		time ( ) + 60 * 60 * 24 ,
		self::$_user_information [ 'openid' ]
	) ;

}

else if ( $configs [ 1 ] === 'paypal' ) {
		$value_coins = self::$_user_information['coins'] / 700;
    if ( self::$_user_type == 0 ) exit ( 'Denied' ) ;
    if ( empty ( $_POST ) ) exit ;
    if (strlen(self::$_user_information['email']) == 0) echo 'Email invalid.';
    if (empty(self::$_user_information['email'])) echo 'Email invalid.';
    $usd = __F::__protected_string ( $_POST [ 'usd' ] ) ;
    if (!is_float($usd) == 0) echo 'Invalid.';
    else {
			if($value_coins > 9.9 && $usd > 9.9){
				if ($value_coins > $usd) {
					$value = __U::__request_payment($usd, self::$_user_information['email'], $error_message);
					if ($value == false) {
							echo $error_message;
					}
					else {
						$coins_withdraw = $usd * 700;
						DB::__db_query (
							core::$mysql_handle ,
							DB::$DB_FETCH_NONE ,
							DB::$DB_PROTECTED ,
							'UPDATE `accounts` SET `coins`=`coins`- :coi WHERE `openid`=:sid' ,
							$coins_withdraw,
							self::$_user_information [ 'openid' ]
						) ;
						echo 'Payment was made.';
					}
			} else {
					echo 'Insufficient funds.';
			}
		}
		else {
			echo 'Insufficient funds.';
		}

    }

}
else if ( $configs [ 1 ] === 'get_trash' ) {
	$result = DB::__db_query (
		core::$mysql_handle ,
		DB::$DB_FETCH_ALL ,
		DB::$DB_PROTECTED ,
		'SELECT * FROM `flags`'
	) ;
	echo json_encode($result);
}
else if ( $configs [ 1 ] == 'post_trash' ) {

	if ( self::$_user_type == 0 ) exit ( 'Denied' ) ;
	if ( empty ( $_POST ) ) exit ;
	$lat = __F::__protected_string ( $_POST [ 'lat' ] ) ;
	$lng = __F::__protected_string ( $_POST [ 'lng' ] ) ;

	DB::__db_query (
		core::$mysql_handle ,
		DB::$DB_FETCH_NONE ,
		DB::$DB_PROTECTED ,
		'INSERT INTO `flags` (`lat`, `log`, `type`) VALUES (:lat, :log, \'1\')' ,
		$lat,
		$lng
	) ;
}