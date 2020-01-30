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

	$error = "" ;

	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$page = Util_Format_Sanatize( Util_Format_GetVar( "page" ), "n" ) ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$option = Util_Format_Sanatize( Util_Format_GetVar( "option" ), "n" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$bgcolor = Util_Format_Sanatize( Util_Format_GetVar( "bgcolor" ), "ln" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;

	if ( $action === "update" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/update.php" ) ;

		$copy_all = Util_Format_Sanatize( Util_Format_GetVar( "copy_all" ), "n" ) ;

		$rname = Util_Format_Sanatize( Util_Format_GetVar( "rname" ), "n" ) ;
		$remail = Util_Format_Sanatize( Util_Format_GetVar( "remail" ), "n" ) ;
		$rquestion = Util_Format_Sanatize( Util_Format_GetVar( "rquestion" ), "n" ) ;
		$custom_field = preg_replace( "/'/", "", preg_replace( "/\"/", "", Util_Format_Sanatize( Util_Format_GetVar( "custom_field" ), "notags" ) ) ) ;
		$custom_field_required = Util_Format_Sanatize( Util_Format_GetVar( "custom_field_required" ), "n" ) ;
		$custom_field2 = preg_replace( "/'/", "", preg_replace( "/\"/", "", Util_Format_Sanatize( Util_Format_GetVar( "custom_field2" ), "notags" ) ) ) ;
		$custom_field2_required = Util_Format_Sanatize( Util_Format_GetVar( "custom_field2_required" ), "n" ) ;
		$custom_field3 = preg_replace( "/'/", "", preg_replace( "/\"/", "", Util_Format_Sanatize( Util_Format_GetVar( "custom_field3" ), "notags" ) ) ) ;
		$custom_field3_required = Util_Format_Sanatize( Util_Format_GetVar( "custom_field3_required" ), "n" ) ;
		$prechat = Util_Format_Sanatize( Util_Format_GetVar( "prechat" ), "n" ) ;

		$custom_array = ( $custom_field || $custom_field2 || $custom_field3 ) ? serialize( Array( "$custom_field", $custom_field_required, "$custom_field2", $custom_field2_required, "$custom_field3", $custom_field3_required ) ) : serialize( Array() ) ;

		if ( $copy_all )
		{
			for( $c = 0; $c < count( $departments ); ++$c )
			{
				Depts_update_DeptValues( $dbh, $departments[$c]["deptID"], "remail", $remail, "rquestion", $rquestion ) ;
				Depts_update_DeptValues( $dbh, $departments[$c]["deptID"], "rname", $rname, "custom", $custom_array ) ;
				Depts_update_DeptVarsValue( $dbh, $departments[$c]["deptID"], "prechat_form", $prechat ) ;
			}
		}
		else
		{
			Depts_update_DeptValues( $dbh, $deptid, "remail", $remail, "rquestion", $rquestion ) ;
			Depts_update_DeptValues( $dbh, $deptid, "rname", $rname, "custom", $custom_array ) ;
			Depts_update_DeptValue( $dbh, $deptid, "custom", $custom_array ) ;
			Depts_update_DeptVarsValue( $dbh, $deptid, "prechat_form", $prechat ) ;
		}
	}

	$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	$deptvars = Depts_get_DeptVars( $dbh, $deptid ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($deptinfo["lang"], "ln").".php" ) ;
	$deptname = $deptinfo["name"] ;

	$custom_field = ( $deptinfo["custom"] ) ? unserialize( $deptinfo["custom"] ) : Array() ;
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

		toggle_prechat( <?php echo isset( $deptvars['prechat_form'] ) ? 1 : 1 ; ?> ) ;
		toggle_prechat( <?php echo ( !isset( $deptvars['prechat_form'] ) || $deptvars['prechat_form'] ) ? 1 : 0 ?> ) ;

		<?php if ( ( $action === "update" ) && !$error ): ?>
		do_alert( 1, "Success" ) ;
		<?php elseif ( $error ): ?>
		do_alert( 0, "<?php echo $error ?>" ) ;
		<?php endif ; ?>
	});

	function do_submit_settings()
	{
		$('#form_settings').submit() ;
	}

	function toggle_prechat( theflag )
	{
		if ( theflag )
		{
			$('#div_prechat_skip').hide() ;
			$('#div_prechat').show() ;
		}
		else
		{
			$('#div_prechat').hide() ;
			$('#div_prechat_skip').show() ;
		}
	}
//-->
</script>
</head>
<body style="overflow: hidden;">

<div id="iframe_body" style="height: 440px; overflow: auto; <?php echo ( $bgcolor ) ? "background: #$bgcolor;" : "" ?>">
	<form action="iframe_edit_6.php" id="form_settings" method="POST" accept-charset="<?php echo $LANG["CHARSET"] ?>">
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
	<input type="hidden" name="option" value="<?php echo $option ?>">
	<input type="hidden" name="bgcolor" value="<?php echo $bgcolor ?>">
	<div style="padding-bottom: 15px; text-align: justify;">
		<div style="margin-bottom: 15px;">
			<div class="li_op round" style="cursor: pointer;" onclick="$('#prechat_1').prop('checked', true);toggle_prechat(1);"><input type="radio" name="prechat" id="prechat_1" value="1" onClick="toggle_prechat(1)" <?php echo ( !isset( $deptvars['prechat_form'] ) || $deptvars['prechat_form'] ) ? "checked" : "" ; ?> > Display the Pre-Chat Form</div>
			<div class="li_op round" style="cursor: pointer;" onclick="$('#prechat_0').prop('checked', true);toggle_prechat(0);"><input type="radio" name="prechat" id="prechat_0" value="0" onClick="toggle_prechat(0)" <?php echo ( isset( $deptvars['prechat_form'] ) && !$deptvars['prechat_form'] ) ? "checked" : "" ; ?> > Hide the Pre-Chat Form</div>
			<div style="clear: both;"></div>
		</div>
		<div>
			<div id="div_prechat" style="display: none;" class="info_info">
				<div style="float: left; min-height: 215px; width: 360px;" class="info_info">
					<div style="margin-top: 5px;">Should the <span class="txt_blue" style="font-weight: bold;">Name</span> be required before starting a chat session?  Select "Yes" for required.  Select "No" for optional.</div>
					<div style="margin-top: 5px;">
						<div class="li_op" style="cursor: pointer;" onclick="$('#rname_1').prop('checked', true);"><input type="radio" name="rname" id="rname_1" value="1" checked> Yes, required.</div>
						<div class="li_op round" style="cursor: pointer;" onclick="$('#rname_0').prop('checked', true);"><input type="radio" name="rname" id="rname_0" value="0"> Optional</div>
						<div style="clear: both;"></div>
					</div>

					<div style="margin-top: 10px;"><span class="txt_blue" style="font-weight: bold;">Email</span> address required to chat?  Select "Yes" for required.  Select "No" to <b>hide the email field</b>.</div>
					<div style="margin-top: 5px;">
						<div class="li_op round" style="cursor: pointer;" onclick="$('#remail_1').prop('checked', true);"><input type="radio" name="remail" id="remail_1" value="1" checked> Yes, required.</div>
						<div class="li_op round" style="cursor: pointer;" onclick="$('#remail_0').prop('checked', true);"><input type="radio" name="remail" id="remail_0" value="0"> Hide field.</div>
						<div style="clear: both;"></div>
					</div>

					<div style="margin-top: 10px;"><span class="txt_blue" style="font-weight: bold;">Question</span> required to chat?  Select "Yes" for required.  Select "No" to <b>hide the question field</b></div>
					<div style="margin-top: 5px;">
						<div class="li_op round" style="cursor: pointer;" onclick="$('#rquestion_1').prop('checked', true);"><input type="radio" name="rquestion" id="rquestion_1" value="1" checked> Yes, required.</div>
						<div class="li_op round" style="cursor: pointer;" onclick="$('#rquestion_0').prop('checked', true);"><input type="radio" name="rquestion" id="rquestion_0" value="0"> Hide field.</div>
						<div style="clear: both;"></div>
					</div>
				</div>
				<div style="float: left; margin-left: 2px; min-height: 215px; width: 360px;" class="info_info">
					<div>Add additional <span style="font-size: 16px; font-weight: bold;">Custom Fields</span> on the chat request window.</div>
					<div style="margin-top: 10px; text-shadow: none;" class="info_neutral">Additional field could be "Login", "Phone", "Order Number", etc.</div>
					<div style="margin-top: 10px;">
						<table cellspacing=0 cellpadding=2 border=0>
						<tr>
							<td><input type="text" class="input" size="20" maxlength="70" id="custom_field" name="custom_field" value="<?php echo isset( $custom_field[0] ) ? $custom_field[0] : "" ; ?>" onKeyPress="return noquotestags(event)"></td>
							<td style="padding-left: 10px;"><select name="custom_field_required" class="select"><option value=1>required to chat</option><option value=0 <?php echo ( isset( $custom_field[1] ) && !$custom_field[1] ) ? "selected" : "" ; ?>>optional</option></select></td>
						</tr>
						<tr>
							<td><input type="text" class="input" size="20" maxlength="70" id="custom_field2" name="custom_field2" value="<?php echo isset( $custom_field[2] ) ? $custom_field[2] : "" ; ?>" onKeyPress="return noquotestags(event)"></td>
							<td style="padding-left: 10px;"><select name="custom_field2_required" class="select"><option value=1>required to chat</option><option value=0 <?php echo ( isset( $custom_field[3] ) && !$custom_field[3] ) ? "selected" : "" ; ?>>optional</option></select></td>
						</tr>
						<tr>
							<td><input type="text" class="input" size="20" maxlength="70" id="custom_field3" name="custom_field3" value="<?php echo isset( $custom_field[4] ) ? $custom_field[4] : "" ; ?>" onKeyPress="return noquotestags(event)"></td>
							<td style="padding-left: 10px;"><select name="custom_field3_required" class="select"><option value=1>required to chat</option><option value=0 <?php echo ( isset( $custom_field[5] ) && !$custom_field[5] ) ? "selected" : "" ; ?>>optional</option></select></td>
						</tr>
						</table>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div id="div_prechat_skip" style="display: none;" class="info_info">
				Do not display the pre-chat form. To request a chat session, the visitor only needs to click the "Start Chat" button.
				<div style="margin-top: 5px;" class="info_warning">
					<table cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td><img src="../pics/icons/info.png" width="16" height="16" border="0" alt=""></td>
						<td style="padding-left: 5px;">Keep in mind, the <a href="interface_lang.php" target="_parent">pre-chat greeting message and the sub text will be displayed</a> to the visitor prior to starting a chat session.  If wanting to skip the pre-chat form entirely and start the chat session immediately, the feature is located at the <a href="code_autostart.php" target="_parent">Automatic Start Chat</a> area.</div></td>
					</tr>
					</table>
			</div>
		</div>
	</div>
	<script data-cfasync="false" type="text/javascript">
	<!--
		$( "input#rname_"+<?php echo $deptinfo["rname"] ?> ).prop( "checked", true ) ;
		$( "input#remail_"+<?php echo $deptinfo["remail"] ?> ).prop( "checked", true ) ;
		$( "input#rquestion_"+<?php echo $deptinfo["rquestion"] ?> ).prop( "checked", true ) ;
	//-->
	</script>

	<?php if ( count( $departments ) > 1 ) : ?>
	<div style=""><input type="checkbox" id="copy_all" name="copy_all" value=1> copy this update to all departments</div>
	<?php endif ; ?>

	<div style="margin-top: 15px;"><input type="button" value="Update" class="btn" onClick="do_submit_settings()"> &nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="parent.do_options( <?php echo $option ?>, <?php echo $deptid ?> );">cancel</a> &nbsp; &nbsp; <span class="info_neutral">The pre-chat greeting message ("Welcome to our Live Chat") and subtext can be updated at the <a href="interface_lang.php" target="_parent">Language Text</a> area.</span></div>
	</form>
</div>

</body>
</html>
