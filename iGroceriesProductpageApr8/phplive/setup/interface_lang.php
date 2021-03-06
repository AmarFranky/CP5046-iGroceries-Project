<?php
	/***************************************/
	//
	//
	// PHP Live! Support
	//
	// https://www.phplivesupport.com
	//
	/***************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; exit ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$copy_all = Util_Format_Sanatize( Util_Format_GetVar( "copy_all" ), "n" ) ;
	$error = "" ;

	if ( !isset( $CONF["lang"] ) ) { $CONF["lang"] = "english" ; }

	$departments = Depts_get_AllDepts( $dbh ) ; $departments_visible = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		if ( $department["visible"] ) { $departments_visible[] = $department ; }
	}

	// set the $deptid based on visible or not visible availability
	if ( !$deptid )
	{
		if ( count( $departments_visible ) == 1 ) { $deptid = $departments_visible[0]["deptID"] ; }
		else if ( !count( $departments_visible ) && count( $departments ) ) { $deptid = $departments[0]["deptID"] ; }
	}

	if ( $deptid )
	{
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
		if ( isset( $deptinfo["lang"] ) && $deptinfo["lang"] ) { $CONF["lang"] = $deptinfo["lang"] ; }
	}

	$dept_groups = Depts_get_AllDeptGroups( $dbh ) ; $dept_groups_hash = Array() ;
	for ( $c = 0; $c < count( $dept_groups ); ++$c )
	{
		$dept_group = $dept_groups[$c] ;
		if ( ( $deptid > $VARS_GID_MIN ) && ( $deptid == $dept_group["groupID"] ) )
		{
			$CONF["lang"] = $dept_group["lang"] ;
		}
		$dept_groups_hash[$dept_group["groupID"]] = $dept_group["name"] ;
	}

	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize( $CONF["lang"], "ln" ).".php" ) ;

	if ( $action === "update" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/put.php" ) ;

		$db_lang_hash_postvar = Array() ;
		foreach ( $_POST as $post_name => $post_val )
		{
			if ( preg_match( "/^TXT_/", $post_name ) )
			{
				$post_name = preg_replace( "/^TXT_/", "", $post_name ) ;
				$db_lang_hash_postvar[$post_name] = rawurlencode( Util_Format_Sanatize( $post_val, "noscripts" ) ) ;
			}
		}

		$lang_db = Lang_get_Lang( $dbh, $deptid ) ; $db_lang_hash_temp = Array() ;
		if ( isset( $lang_db["deptID"] ) )
			$db_lang_hash_temp = unserialize( $lang_db["lang_vars"] ) ;
		$db_lang_hash = array_merge( $db_lang_hash_temp, $db_lang_hash_postvar ) ;

		if ( $copy_all )
		{
			for( $c = 0; $c < count( $departments ); ++$c )
			{
				$thisdeptid = $departments[$c]["deptID"] ;
				if ( !Lang_put_Lang( $dbh, $thisdeptid, serialize( $db_lang_hash ) ) )
				{
					$error = "Error in processing update.  Please try again. [e]" ;
					break ;
				}
			}
			for ( $c = 0; $c < count( $dept_groups ); ++$c )
			{
				$thisdeptid = $dept_groups[$c]["groupID"] ;
				if ( !Lang_put_Lang( $dbh, $thisdeptid, serialize( $db_lang_hash ) ) )
				{
					$error = "Error in processing update.  Please try again. [e]" ;
					break ;
				}
			}
			if ( !$error )
			{
				Lang_put_Lang( $dbh, 0, serialize( $db_lang_hash ) ) ;
				$lang_db = Array() ; $lang_db["deptID"] = $deptid ;
				$LANG = array_merge( $LANG, $db_lang_hash ) ;
			}
		}
		else if ( !Lang_put_Lang( $dbh, $deptid, serialize( $db_lang_hash ) ) )
			$error = "Error saving values." ;
		else
		{
			if ( !$deptid && ( count( $departments ) == 1 ) )
				Lang_put_Lang( $dbh, $departments[0]["deptID"], serialize( $db_lang_hash ) ) ;

			$lang_db = Array() ; $lang_db["deptID"] = $deptid ;
			$LANG = array_merge( $LANG, $db_lang_hash ) ;
		}
	}
	else if ( $action === "revert" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/remove.php" ) ;

		if ( $copy_all )
		{
			for( $c = 0; $c < count( $departments ); ++$c )
			{
				$thisdeptid = $departments[$c]["deptID"] ;
				Lang_remove_Lang( $dbh, $thisdeptid ) ;
			}
			for ( $c = 0; $c < count( $dept_groups ); ++$c )
			{
				$thisdeptid = $dept_groups[$c]["groupID"] ;
				Lang_remove_Lang( $dbh, $thisdeptid ) ;
			}
			Lang_remove_Lang( $dbh, 0 ) ;
		}
		else
		{
			Lang_remove_Lang( $dbh, $deptid ) ;
			if ( !$deptid && ( count( $departments ) == 1 ) )
				Lang_remove_Lang( $dbh, $departments[0]["deptID"] ) ;
		}
	}
	else
	{
		$lang_db = Lang_get_Lang( $dbh, $deptid ) ;
		if ( isset( $lang_db["deptID"] ) )
		{
			$db_lang_hash = unserialize( $lang_db["lang_vars"] ) ;
			$LANG = array_merge( $LANG, $db_lang_hash ) ;
		}
	}
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script data-cfasync="false" type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="../js/jquery_md5.js?<?php echo $VERSION ?>"></script>

<script data-cfasync="false" type="text/javascript">
<!--
	$(document).ready(function()
	{
		$("html").css({'background': '#272727'}) ; $("body").css({'background': '#D6E4F2'}) ;
		init_menu() ;
		toggle_menu_setup( "interface" ) ;

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
	});

	function switch_dept( theobject )
	{
		var unique = unixtime() ;
		location.href = "interface_lang.php?deptid="+theobject.value+"&"+unique ;
	}

	function close_view() { } // dummy function needed for preview close
	function view_preview( theflag )
	{
		if ( theflag )
		{
			//
		}
		else
		{
			var texts = new Object ; var div_name ;
			$('#td_text_values').find('*').each( function(){
				div_name = this.id ;
				if ( div_name.indexOf("TXT_") == 0 )
				{
					var temp_text = $('#'+div_name).val().trim().replace( /<script[^>]*>.*?<\/script>/gi,'' ) ;
					if ( div_name != "TXT_CHAT_WELCOME_SUBTEXT" )
						temp_text = strip_tags( temp_text ) ;

					$('#'+div_name).val( temp_text ) ;

					div_name = div_name.replace( /^TXT_/, "" ) ;
					texts[div_name] = temp_text ;
				}
			} );

			document.getElementById('iframe_widget_embed').contentWindow.preview_text( texts ) ;
			$('#phplive_widget_embed_iframe').fadeOut("fast").fadeIn("fast") ;
		}
	}

	function do_update()
	{
		$('#form_txt').submit() ;
	}

	function do_reset()
	{
		$('#form_txt').trigger("reset") ;
		view_preview(0) ;
		view_preview(1) ;
	}

	function do_revert()
	{
		var copy_all = ( $( "#copy_all" ).prop( "checked" ) ) ? 1 : 0 ;

		if ( confirm( "Clear all text and revert to default?" ) )
		{
			var unique = unixtime() ;
			location.href = "interface_lang.php?action=revert&deptid=<?php echo $deptid ?>&copy_all="+copy_all+"&"+unique ;
		}
	}

	function strip_tags( thetext )
	{
		var tmp = document.createElement("DIV") ;
		tmp.innerHTML = thetext ;
		return tmp.textContent || tmp.innerText || "" ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='interface.php?jump=logo'" style="margin-left: 0px;">Logo</div>
			<div class="op_submenu" onClick="location.href='interface_themes.php'">Themes</div>
			<div class="op_submenu" onClick="location.href='interface.php?jump=charset'">Character Set</div>
			<?php if ( phpversion() >= "5.1.0" ): ?><div class="op_submenu" onClick="location.href='interface.php?jump=time'">Timezone</div><?php endif; ?>
			<div class="op_submenu_focus" id="menu_lang">Language Text</div>
			<div class="op_submenu" onClick="location.href='interface_gdpr.php'" id="menu_gdpr">Privacy & GDPR</div>
			<div class="op_submenu" onClick="location.href='interface_chat_msg.php'">Chat End Msg</div>
			<div class="op_submenu" onClick="location.href='interface.php?jump=screen'">Login Screen</div>
			<div class="op_submenu" onClick="location.href='interface.php?jump=props'">Properties</div>
			<div style="clear: both"></div>
		</div>

		<form method="POST" action="interface_lang.php" enctype="multipart/form-data" id="form_txt" autocomplete="off">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="jump" id="jump" value="lang">
		<div style="margin-top: 25px;">
			<table cellspacing=0 cellpadding=2 border=0 width="100%">
			<tr>
				<?php if ( count( $departments ) ): ?>
				<td valign="top" width="<?php echo $VARS_CHAT_WIDTH_WIDGET ?>">
					<div id='phplive_widget_embed_iframe' style='width: <?php echo $VARS_CHAT_WIDTH_WIDGET ?>px; height: 550px; -moz-border-radius: 5px; border-radius: 5px; -webkit-box-shadow: -0px 7px 29px rgba(0, 0, 0, 0.34); -moz-box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34);'>
						<iframe id='iframe_widget_embed' name='iframe_widget_embed' style='width: 100%; height: 100%; -moz-border-radius: 5px; border-radius: 5px; border: 0px;' src='../phplive.php?preview=2&embed=1&d=<?php echo $deptid ?>' scrolling='no' border=0 frameborder=0></iframe>
					</div>
				</td>
				<?php endif ; ?>
				<td valign="top" width="100%" style="padding-left: 25px;" id="td_text_values">
					<div style="margin-bottom: 15px;">Update the chat request window text, replacing the default <a href="depts.php?deptid=<?php echo $deptid ?>&ftab=lang">department</a> language text values.</div>
					<?php if ( ( count( $departments ) > 1 ) || count( $dept_groups ) ): ?>
						<div style="margin-bottom: 25px;">
							<select name="deptid" id="deptid" style="font-size: 16px;" onChange="switch_dept( this )">
							<?php if ( count( $departments_visible ) > 1 ): ?>
							<option value="0">All Departments</option>
							<?php endif ; ?>
							<?php
								if ( count( $departments ) > 1 )
								{
									for ( $c = 0; $c < count( $departments ); ++$c )
									{
										$department = $departments[$c] ;

										if ( $department["name"] != "Archive" )
										{
											$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
											print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
										}
									}
								}
								if ( count( $dept_groups ) )
								{
									for ( $c = 0; $c < count( $dept_groups ); ++$c )
									{
										$dept_group = $dept_groups[$c] ;
										$selected = ( $deptid == $dept_group["groupID"] ) ? "selected" : "" ;
										print "<option value=\"$dept_group[groupID]\" $selected>$dept_group[name] [Department Group]</option>" ;
									}
								}
							?>
							</select>

							<div style="margin-top: 5px;">Department is based on <a href="code.php">chat icon HTML Code</a></div>
						</div>
					<?php endif ; ?>

					<?php if ( count( $departments ) ): ?>
						<div class="info_neutral" style="padding: 15px;">
							<div>
								<div style=""><img src="../pics/icons/arrow_down.png" width="15" height="16" border="0" alt=""> Embed Chat Title <img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> This text is displayed when the embed chat is minimized</div>
								<div><input type="text" class="input" style="width: 90%;" maxlength="20" name="TXT_TXT_LIVECHAT" id="TXT_TXT_LIVECHAT" onFocus="view_preview(1)" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["TXT_LIVECHAT"] ) ) ?>" placeholder="Live Chat"></div>
							</div>

							<div style="margin-top: 15px;">
								<img src="../pics/icons/arrow_down.png" width="15" height="16" border="0" alt=""> Welcome Greeting
								<div><input type="text" class="input" style="width: 90%;" name="TXT_CHAT_WELCOME" id="TXT_CHAT_WELCOME" onFocus="view_preview(1)" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["CHAT_WELCOME"] ) ) ?>" placeholder="Welcome to our Live Chat"></div>
							</div>

							<div style="margin-top: 15px;">
								<img src="../pics/icons/arrow_down.png" width="15" height="16" border="0" alt=""> Welcome Greeting Sub Text (HTML is ok)<br>
								<div><textarea class="input" style="width: 90%; height: 50px; resize: vertical;" name="TXT_CHAT_WELCOME_SUBTEXT" id="TXT_CHAT_WELCOME_SUBTEXT" onFocus="view_preview(1)" placeholder="To better assist you, please provide the following information."><?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["CHAT_WELCOME_SUBTEXT"] ) ) ?></textarea></div>
							</div>

							<div style="<?php echo ( ( $deptid < 100000000 ) && ( $deptid || ( count( $departments ) == 1 ) ) ) ? "display: none;" : "" ; ?> margin-top: 15px;"><input type="text" class="input" style="width: 90%;" maxlength="165" name="TXT_TXT_DEPARTMENT" id="TXT_TXT_DEPARTMENT" onFocus="view_preview(1)" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["TXT_DEPARTMENT"] ) ) ?>" placeholder="Department"></div>

							<div style="<?php echo ( ( $deptid < 100000000 ) && ( $deptid || ( count( $departments ) == 1 ) ) ) ? "display: none;" : "" ; ?> margin-top: 15px;"><input type="text" class="input" style="width: 90%;" maxlength="55" name="TXT_CHAT_SELECT_DEPT" id="TXT_CHAT_SELECT_DEPT" onFocus="view_preview(1)" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["CHAT_SELECT_DEPT"] ) ) ?>" placeholder="- select department -"></div>

							<div style="margin-top: 15px;">
								<span class=""><img src="../pics/icons/arrow_left.png" width="16" height="15" border="0" alt=""> <a href="JavaScript:void(0)" onClick="view_preview(0)">view how it will look</a></span>
							</div>
						</div>

						<div style="margin-top: 25px;">
							<div>Other texts that can be updated.</div>
							<div style="margin-top: 15px;">
								<input type="text" class="input" style="width: 13%;" maxlength="255" name="TXT_TXT_SUBMIT" id="TXT_TXT_SUBMIT" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["TXT_SUBMIT"] ) ) ?>" placeholder="Submit">

								&nbsp; &nbsp; <input type="text" class="input" style="width: 13%;" maxlength="255" name="TXT_CHAT_BTN_START_CHAT" id="TXT_CHAT_BTN_START_CHAT" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["CHAT_BTN_START_CHAT"] ) ) ?>" placeholder="Start Chat">
								&nbsp; &nbsp; <input type="text" class="input" style="width: 13%;" maxlength="255" name="TXT_CHAT_BTN_EMAIL" id="TXT_CHAT_BTN_EMAIL" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["CHAT_BTN_EMAIL"] ) ) ?>" placeholder="Send Email">
								&nbsp; &nbsp; <input type="text" class="input" style="width: 13%;" maxlength="255" name="TXT_TXT_ONLINE" id="TXT_TXT_ONLINE" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["TXT_ONLINE"] ) ) ?>" placeholder="Online">
								&nbsp; &nbsp; <input type="text" class="input" style="width: 13%;" maxlength="255" name="TXT_TXT_OFFLINE" id="TXT_TXT_OFFLINE" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["TXT_OFFLINE"] ) ) ?>" placeholder="Offline">
								<?php if ( ( $deptid < 100000000 ) && ( $deptid || ( count( $departments ) == 1 ) ) ): ?>
									<div style="margin-top: 15px;">
										<input type="text" class="input" style="width: 13%;" maxlength="255" name="TXT_TXT_NAME" id="TXT_TXT_NAME" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["TXT_NAME"] ) ) ?>" placeholder="Name">
										&nbsp; &nbsp; <input type="text" class="input" style="width: 13%;" maxlength="255" name="TXT_TXT_EMAIL" id="TXT_TXT_EMAIL" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["TXT_EMAIL"] ) ) ?>" placeholder="Email">
										&nbsp; &nbsp; <input type="text" class="input" style="width: 13%;" maxlength="255" name="TXT_TXT_QUESTION" id="TXT_TXT_QUESTION" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["TXT_QUESTION"] ) ) ?>" placeholder="Question">
										&nbsp; &nbsp; <input type="text" class="input" style="width: 13%;" maxlength="255" name="TXT_TXT_CONNECTING" id="TXT_TXT_CONNECTING" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["TXT_CONNECTING"] ) ) ?>" placeholder="Connecting">
										&nbsp; &nbsp; <input type="text" class="input" style="width: 13%;" maxlength="255" name="TXT_TXT_OPTIONAL" id="TXT_TXT_OPTIONAL" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["TXT_OPTIONAL"] ) ) ?>" placeholder="optional">
									</div>
									<div style="margin-top: 15px;">
										Chat Rating Survey Text<br>
										<div><input type="text" class="input" style="width: 90%;" maxlength="165" name="TXT_CHAT_NOTIFY_RATE" id="TXT_CHAT_NOTIFY_RATE" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["CHAT_NOTIFY_RATE"] ) ) ?>" placeholder="How would you rate the support?"></div>
									</div>
								<?php endif ; ?>
							</div>
						</div>

						<div style="margin-top: 25px; border-top: 1px solid #D6DDE4;">
							<?php if ( count( $departments ) > 1 ) : ?>
							<div style="margin-top: 25px;"><input type="checkbox" id="copy_all" name="copy_all" value=1> copy this update to all departments</div>
							<?php endif ; ?>
							<div style="margin-top: 25px;">
								<div style=""><button type="button" class="btn" onClick="do_update()">Update Text</button> &nbsp; &nbsp; <button type="button" class="btn" onClick="do_reset()">Reset</button> &nbsp; &nbsp; <?php if ( isset( $lang_db["deptID"] ) ): ?>or <a href="JavaScript:void(0)" onClick="do_revert()">revert to default language <?php echo "(".ucwords( $CONF["lang"] ).")" ; ?> text values</a><?php endif ; ?></div>
							</div>
						</div>

						<div style="margin-top: 45px;">
							<table cellspacing=0 cellpadding=0 border=0>
							<tr>
								<td><img src="../pics/icons/info.png" width="14" height="14" border="0" alt=""></td>
								<td style="padding-left: 5px;">The Offline "Leave a Message" text can be updated for each department at the "<a href="depts.php?ftab=msg">Offline Message</a>" department option.</td>
							</tr>
							</table>
						</div>
					<?php else: ?>
					<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Add a <a href="depts.php" style="color: #FFFFFF;">Department</a> to continue.</span>
					<?php endif ; ?>
				</td>
			</tr>
			</table>
		</div>
		</form>

<?php include_once( "./inc_footer.php" ) ?>
