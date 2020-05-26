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
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; exit ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$error = Util_Format_Sanatize( Util_Format_GetVar( "error" ), "ln" ) ;
	$page = Util_Format_Sanatize( Util_Format_GetVar( "page" ), "n" ) ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$option = Util_Format_Sanatize( Util_Format_GetVar( "option" ), "n" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$bgcolor = Util_Format_Sanatize( Util_Format_GetVar( "bgcolor" ), "ln" ) ;
	$jump = Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($deptinfo["lang"], "ln").".php" ) ;

	if ( $action === "update" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/put.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/update.php" ) ;

		$copy_all = Util_Format_Sanatize( Util_Format_GetVar( "copy_all" ), "n" ) ;
		$message = preg_replace( "/<script(.*?)<\/script>/i", "", Util_Format_Sanatize( Util_Format_GetVar( "message" ), "" ) ) ;
		$message_busy = preg_replace( "/<script(.*?)<\/script>/i", "", Util_Format_Sanatize( Util_Format_GetVar( "message_busy" ), "" ) ) ;
		$offline_form = Util_Format_Sanatize( Util_Format_GetVar( "offline_form" ), "n" ) ;
		$emailm_cc = Util_Format_Sanatize( Util_Format_GetVar( "emailm_cc" ), "e" ) ;
		$template_subject = preg_replace( "/<script(.*?)<\/script>/i", "", Util_Format_Sanatize( Util_Format_GetVar( "template_subject" ), "" ) ) ;
		$template_body = preg_replace( "/<script(.*?)<\/script>/i", "", Util_Format_Sanatize( Util_Format_GetVar( "template_body" ), "" ) ) ;
		$offline_template = "$template_subject-_-$template_body" ;
		$MSG_LEAVE_MESSAGE = Util_Format_Sanatize( Util_Format_GetVar( "TXT_MSG_LEAVE_MESSAGE" ), "noscripts" ) ;

		$table_name = "msg_offline" ;

		if ( !$message )
		{
			$error = urlencode( "Blank input is invalid.  Message has been reset." ) ;
			$action = "" ;
		}
		else
		{
			if ( $copy_all )
			{
				for( $c = 0; $c < count( $departments ); ++$c )
				{
					$lang_db_dept = Lang_get_Lang( $dbh, $departments[$c]["deptID"] ) ;
					$lang_vars = ( isset( $lang_db_dept["lang_vars"] ) ) ? unserialize( $lang_db_dept["lang_vars"] ) : Array() ;
					if ( ( isset( $LANG["MSG_LEAVE_MESSAGE"] ) && ( $LANG["MSG_LEAVE_MESSAGE"] != $MSG_LEAVE_MESSAGE ) ) || ( isset( $lang_vars["MSG_LEAVE_MESSAGE"] ) && ( isset( $lang_db_dept["deptID"] ) && ( $lang_vars["MSG_LEAVE_MESSAGE"] != $MSG_LEAVE_MESSAGE ) ) ) )
					{
						$lang_vars["MSG_LEAVE_MESSAGE"] = $MSG_LEAVE_MESSAGE ;
						Lang_put_Lang( $dbh, $departments[$c]["deptID"], serialize( $lang_vars ) ) ;
					}

					Depts_update_DeptValue( $dbh, $departments[$c]["deptID"], $table_name, $message ) ;
					Depts_update_DeptValues( $dbh, $departments[$c]["deptID"], "emailm_cc", $emailm_cc, "msg_busy", $message_busy ) ;
					Depts_update_DeptVarsValues( $dbh, $departments[$c]["deptID"], "offline_form", $offline_form, "offline_msg_template", $offline_template ) ;
				}
			}
			else
			{
				$lang_db_dept = Lang_get_Lang( $dbh, $deptid ) ;
				$lang_vars = ( isset( $lang_db_dept["lang_vars"] ) ) ? unserialize( $lang_db_dept["lang_vars"] ) : Array() ;
				if ( ( isset( $LANG["MSG_LEAVE_MESSAGE"] ) && ( $LANG["MSG_LEAVE_MESSAGE"] != $MSG_LEAVE_MESSAGE ) ) || ( isset( $lang_vars["MSG_LEAVE_MESSAGE"] ) && ( isset( $lang_db_dept["deptID"] ) && ( $lang_vars["MSG_LEAVE_MESSAGE"] != $MSG_LEAVE_MESSAGE ) ) ) )
				{
					$lang_vars["MSG_LEAVE_MESSAGE"] = $MSG_LEAVE_MESSAGE ;
					Lang_put_Lang( $dbh, $deptid, serialize( $lang_vars ) ) ;
				}

				Depts_update_DeptValue( $dbh, $deptid, $table_name, $message ) ;
				Depts_update_DeptValues( $dbh, $deptid, "emailm_cc", $emailm_cc, "msg_busy", $message_busy ) ;
				Depts_update_DeptVarsValues( $dbh, $deptid, "offline_form", $offline_form, "offline_msg_template", $offline_template ) ;
			}
			$action = "success" ;
		}
		HEADER( "location: iframe_edit_2.php?action=$action&bgcolor=$bgcolor&option=$option&deptid=$deptid&error=$error" ) ;
		exit ;
	}

	$deptname = $deptinfo["name"] ;
	$deptvars = Depts_get_DeptVars( $dbh, $deptid ) ;

	$lang_db = Lang_get_Lang( $dbh, $deptid ) ;
	if ( isset( $lang_db["deptID"] ) )
	{
		$db_lang_hash = unserialize( $lang_db["lang_vars"] ) ;
		$LANG = array_merge( $LANG, $db_lang_hash ) ;
	}

	$message = $deptinfo["msg_offline"] ;
	$offline_form = ( isset( $deptvars["offline_form"] ) ) ? $deptvars["offline_form"] : 1 ;

	include_once( "$CONF[DOCUMENT_ROOT]/examples/inc_default_vars.php" ) ;
	$template_subject = $DEFAULT_VAR_OFFLINE_TEMPLATE_SUBJECT ;
	$template_body = $DEFAULT_VAR_OFFLINE_TEMPLATE_BODY ;
	if ( isset( $deptvars["offline_msg_template"] ) && preg_match( "/-_-/", $deptvars["offline_msg_template"] ) )
	{	
		LIST( $template_subject, $template_body ) = explode( "-_-", $deptvars["offline_msg_template"] ) ;
	}
	$offline = ( isset( $VALS['OFFLINE'] ) && $VALS['OFFLINE'] ) ? unserialize( $VALS['OFFLINE'] ) : Array( ) ;
	if ( !isset( $offline[0] ) ) { $offline[0] = "embed" ; }
	if ( !isset( $offline[$deptid] ) ) { $offline[$deptid] = $offline[0] ; }
	$redirect_url = ( isset( $offline[$deptid] ) && !preg_match( "/^(icon|hide|embed|tab)$/", $offline[$deptid] ) ) ? $offline[$deptid] : "" ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script data-cfasync="false" type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>

<script data-cfasync="false" type="text/javascript">
<!--
	var winname = unixtime() ;
	var option = <?php echo $option ?> ; // used to communicate with depts.php to toggle iframe

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;
		$("body, html").css({'background-color': '#<?php echo $bgcolor ?>'}) ;

		<?php if ( $action === "success" ): ?>
		do_alert( 1, "Success" ) ;
		<?php elseif ( $error ): ?>
		do_alert( 0, "<?php echo $error ?>" ) ;
		<?php endif ; ?>

		show_div( "<?php echo ( $jump == "template" ) ? "template" : "email" ; ?>" ) ;
	});

	function do_submit_settings()
	{
		var emailm_cc = $('#emailm_cc').val().replace(/\s/g,'') ;
		$('#emailm_cc').val(emailm_cc) ;

		if ( emailm_cc && !check_email( emailm_cc ) )
			do_alert( 0, "Email format is invalid. (example: you@domain.com)" ) ;
		else if ( emailm_cc && ( "<?php echo $deptinfo["email"] ?>" == emailm_cc ) )
			do_alert( 0, "Email address must be different then the department email." ) ;
		else
			$('#form_settings').submit() ;
	}

	function toggle_offlineform( theobject )
	{
		if ( theobject.value == 1 )
			$('#div_cc').show() ;
		else
			$('#div_cc').hide() ;
	}

	function show_div( thediv )
	{
		$('#jump').val( thediv ) ;
		if ( thediv == "email" )
		{
			$('#menu2_trans_template').removeClass("menu_dept_focus").addClass("menu_dept") ;
			$('#menu2_trans_email').removeClass("menu_dept").addClass("menu_dept_focus") ;
			$('#div_template').hide() ; $('#div_email').show() ;
		}
		else
		{
			$('#menu2_trans_email').removeClass("menu_dept_focus").addClass("menu_dept") ;
			$('#menu2_trans_template').removeClass("menu_dept").addClass("menu_dept_focus") ;
			$('#div_email').hide() ; $('#div_template').show() ;
		}
	}
//-->
</script>
</head>
<body style="overflow: hidden;">

<div id="iframe_body" style="height: 440px; overflow: hidden; <?php echo ( $bgcolor ) ? "background: #$bgcolor;" : "" ?>">
	<form action="iframe_edit_2.php" id="form_settings" method="POST" accept-charset="<?php echo $LANG["CHARSET"] ?>">
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
	<input type="hidden" name="option" value="<?php echo $option ?>">
	<input type="hidden" name="bgcolor" value="<?php echo $bgcolor ?>">
	<input type="hidden" name="jump" id="jump" value="">
	<div id="">
		<div class="menu_dept_focus" onClick="show_div('email')" id="menu2_trans_email">Offline Message to Display</div>
		<div class="menu_dept" onClick="show_div('template')" id="menu2_trans_template">"Leave a message" Email Template</div>
		<div style="clear: both"></div>
	</div>
	<div style="margin-top: 15px;">
		<div id="div_email" style="display: none; padding-bottom: 15px;" class="info_info">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td valign="top" style="padding-right: 25px;">
					<big><b>Offline Header Text</b></big>
					<div style="margin-top: 5px;"><input type="text" class="input" style="width: 90%;" maxlength="165" name="TXT_MSG_LEAVE_MESSAGE" id="TXT_MSG_LEAVE_MESSAGE" value="<?php echo Util_Format_ConvertQuotes( rawurldecode( $LANG["MSG_LEAVE_MESSAGE"] ) ) ?>" placeholder="Please leave a message."></div>
				</td>
				<td valign="top" width="60%">
					<div>
						<div><span style="font-weight: bold;">Standard Offline Subtext:</span><br>Offline message when department is offline. (HTML is ok)</div>
						<div style="margin-top: 5px; padding-bottom: 15px;"><input type="text" class="input" style="width: 95%" id="message" name="message" maxlength="955" value="<?php echo preg_replace( "/\"/", "&quot;", $message ) ?>"></div>
					</div>

					<div>
						<div><span style="font-weight: bold;">"Busy" Offline Subtext:</span><br>Offline message when department operators are online but the chat request was not accepted. (HTML is ok)</div>
						<div style="margin-top: 5px; padding-bottom: 15px;"><input type="text" class="input" style="width: 95%" id="message_busy" name="message_busy" maxlength="955" value="<?php echo preg_replace( "/\"/", "&quot;", $deptinfo["msg_busy"] ) ?>"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan=2>
					<table cellspacing=0 cellpadding=0 border=0 width="100%">
					<tr>
						<td>
							<?php if ( $redirect_url ): ?>
							<div style="margin-top: 5px;" class="info_error"><b>Note:</b> The "Leave a message" form will not be displayed because the <a href="icons.php?deptid=<?php echo $deptid ?>&jump=settings" style="color: #FFFFFF;" target="_parent">Chat Icon offline setting</a> is set to a URL.  Instead, the URL will be displayed.</div>
							<?php else: ?>
							<select name="offline_form" style="width: 98%;" onChange="toggle_offlineform(this)">
								<option value="1" <?php echo ( $offline_form ) ? "selected" : "" ; ?>>When Offline or Busy: Allow visitors to "leave a message" by filling out the email form.</option>
								<option value="0" <?php echo ( !$offline_form ) ? "selected" : "" ; ?>>When Offline or Busy: Do not display the "leave a message" email form.  Only display the offline header and subtext messages.</option>
							</select>
							<?php endif ; ?>
						</td>
					</tr>
					<tr>
						<td style="padding-top: 5px;">
							<div id="div_cc" style="<?php echo ( !$offline_form ) ? "display: none;" : "" ?>"><img src="../pics/icons/email.png" width="16" height="16" border="0" alt=""> Email the "Leave a message" offline messages to the <a href="JavaScript:void(0)" onClick="parent.blink_td_email(<?php echo $deptid ?>)">department email address</a> and also send a copy to: <input type="text" class="input" size="20" maxlength="160" style="padding: 5px;" name="emailm_cc" id="emailm_cc" value="<?php echo $deptinfo["emailm_cc"] ?>"></div>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</div>
		<div id="div_template" style="display: none; padding-bottom: 15px;" class="info_info">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td valign="top" width="300" nowrap>
					Subject: <input type="text" class="input" id="template_subject" name="template_subject" size="35" maxlength="255" value="<?php echo $template_subject ?>">
					<div style="margin-top: 5px;">
						<textarea type="text" cols="50" rows="9" id="template_body" name="template_body"><?php echo $template_body ?></textarea>
					</div>
				</td>
				<td valign="top" width="100%" style="padding-left: 15px;">
					Pre-populated Variables
					<div style="margin-top: 5px;">
						<div><span style="font-weight: bold; color: #427EEC;">%%visitor_subject%%</span> = email subject provided by the visitor</div>
						<div style="margin-top: 3px;"><span style="font-weight: bold; color: #427EEC;">%%visitor_message%%</span> = email message provided by the visitor</div>
						<div style="margin-top: 3px;"><span style="font-weight: bold; color: #427EEC;">%%department_name%%</span> = department name</div>
						<div style="margin-top: 3px;"><span style="font-weight: bold; color: #427EEC;">%%custom_variables%%</span> = <a href="JavaScript:void(0)" onClick="parent.do_options( 6, <?php echo $deptid ?>, '<?php echo $bgcolor ?>' )">custom variables</a> (if provided)</div>
						<div style="margin-top: 3px;"><span style="font-weight: bold; color: #427EEC;">%%visitor%%</span> = visitor name</div>
						<div style="margin-top: 3px;"><span style="font-weight: bold; color: #427EEC;">%%visitor_email%%</span> = visitor email</div>
						<div style="margin-top: 3px;"><span style="font-weight: bold; color: #427EEC;">%%stat_total_footprints%%</span> = visitor total footprints (number)</div>
						<div style="margin-top: 3px;"><span style="font-weight: bold; color: #427EEC;">%%stat_ip%%</span> = visitor IP address</div>
						<div style="margin-top: 3px;"><span style="font-weight: bold; color: #427EEC;">%%stat_visitor_id%%</span> = unique ID assigned to the visitor</div>
						<div style="margin-top: 3px;"><span style="font-weight: bold; color: #427EEC;">%%stat_onpage_url%%</span> = URL the visitor was on when sending the message</div>
					</div>
				</td>
			</tr>
			</table>
		</div>
	</div>

	<?php if ( count( $departments ) > 1 ) : ?>
	<div style="margin-top: 15px;"><input type="checkbox" id="copy_all" name="copy_all" value=1> copy this update (<b>Offline Message to Display</b> and <b>"Leave a message" Email Template</b>) to all departments</div>
	<?php endif ; ?>

	<div style="margin-top: 15px;"><input type="button" value="Update" class="btn" onClick="do_submit_settings()"> &nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="parent.do_options( <?php echo $option ?>, <?php echo $deptid ?> );">cancel</a></div>

	</form>
</div>

</body>
</html>