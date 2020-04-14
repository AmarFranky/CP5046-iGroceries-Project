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
	$jump = Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;

	if ( $action === "update" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/update.php" ) ;

		$timestamp = Util_Format_Sanatize( Util_Format_GetVar( "timestamp" ), "n" ) ;
		$copy_all = Util_Format_Sanatize( Util_Format_GetVar( "copy_all" ), "n" ) ;
		$message = preg_replace( "/<script(.*?)<\/script>/i", "", Util_Format_Sanatize( Util_Format_GetVar( "message" ), "" ) ) ;
		$message_busy = preg_replace( "/<script(.*?)<\/script>/i", "", Util_Format_Sanatize( Util_Format_GetVar( "message_busy" ), "" ) ) ;

		$table_name = "msg_greet" ;

		if ( !$message )
			$error = "Blank input is invalid.  Message has been reset." ;
		else
		{
			if ( $copy_all )
			{
				for( $c = 0; $c < count( $departments ); ++$c )
				{
					Depts_update_DeptValue( $dbh, $departments[$c]["deptID"], $table_name, $message ) ;
					Depts_update_DeptVarsValue( $dbh, $departments[$c]["deptID"], "timestamp", $timestamp ) ;
				}
			}
			else
			{
				Depts_update_DeptValue( $dbh, $deptid, $table_name, $message ) ;
				Depts_update_DeptVarsValue( $dbh, $deptid, "timestamp", $timestamp ) ;
			}
		}
	}

	$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	$deptvars = Depts_get_DeptVars( $dbh, $deptid ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($deptinfo["lang"], "ln").".php" ) ;
	$deptname = $deptinfo["name"] ;

	$message = $deptinfo["msg_greet"] ;
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

		<?php if ( ( $action === "update" ) && !$error ): ?>
		do_alert( 1, "Success" ) ;
		<?php elseif ( $error ): ?>
		do_alert( 0, "<?php echo $error ?>" ) ;
		<?php endif ; ?>

		show_div_trans( "<?php echo ( $jump == "settings" ) ? "settings" : "msg" ; ?>" ) ;
		toggle_timestamp( <?php echo isset( $deptvars["timestamp"] ) ? $deptvars["timestamp"] : 1 ; ?> ) ;
	});

	function do_submit_settings()
	{
		var emailt = $('#emailt').val() ;

		if ( emailt && !check_email( emailt ) )
			do_alert( 0, "Email format is invalid. (example: you@domain.com)" ) ;
		else
			$('#form_settings').submit() ;
	}

	function show_div_trans( thediv )
	{
		$('#jump').val( thediv ) ;
		if ( thediv == "msg" )
		{
			$('#menu2_settings').removeClass("menu_dept_focus").addClass("menu_dept") ;
			$('#menu2_msg').removeClass("menu_dept").addClass("menu_dept_focus") ;
			$('#div_settings').hide() ; $('#div_msg').show() ;
		}
		else
		{
			$('#menu2_msg').removeClass("menu_dept_focus").addClass("menu_dept") ;
			$('#menu2_settings').removeClass("menu_dept").addClass("menu_dept_focus") ;
			$('#div_msg').hide() ; $('#div_settings').show() ;
		}
	}

	function toggle_timestamp( thevalue )
	{
		if ( thevalue )
		{
			$('#img_timestamp').attr('src', '../pics/screens/timestamp.gif') ;
		}
		else
		{
			$('#img_timestamp').attr('src', '../pics/screens/timestamp_off.gif') ;
		}
	}
//-->
</script>
</head>
<body style="overflow: hidden;">

<div id="iframe_body" style="height: 440px; overflow: hidden; <?php echo ( $bgcolor ) ? "background: #$bgcolor;" : "" ?>">
	<form action="iframe_edit_1.php" id="form_settings" method="POST" accept-charset="<?php echo $LANG["CHARSET"] ?>">
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
	<input type="hidden" name="option" value="<?php echo $option ?>">
	<input type="hidden" name="bgcolor" value="<?php echo $bgcolor ?>">
	<input type="hidden" name="jump" id="jump" value="">
	<div id="">
		<div class="menu_dept" onClick="show_div_trans('msg')" id="menu2_msg">Connecting Message</div>
		<div class="menu_dept" onClick="show_div_trans('settings')" id="menu2_settings">Chat Response Timestamp</div>
		<div style="clear: both"></div>
	</div>
	<div style="margin-top: 15px;">
		<div id="div_msg" style="display: none;">
			<div class="info_info">
				<div style="text-align: justify;">
					<div style="font-weight: bold; font-size: 14px;">Chat Connecting Message</div>
					<div style="margin-top: 5px;">The following message will be displayed to the visitor while being connected to an operator.</div>
					<div style="margin-top: 10px;"><b>%%visitor%%</b> = visitor's name</div>
				</div>
				<div style="margin-top: 5px; padding-bottom: 15px;"><input type="text" class="input" style="width: 95%" id="message" name="message" maxlength="455" value="<?php echo preg_replace( "/\"/", "&quot;", $message ) ?>"></div>
			</div>

			<?php if ( count( $departments ) > 1 ) : ?>
			<div style="margin-top: 15px;"><input type="checkbox" id="copy_all" name="copy_all" value=1> copy this update (<b>Connecting Message</b> and <b>Chat Response Timestamp</b>) to all departments</div>
			<?php endif ; ?>

			<div style="margin-top: 15px;"><input type="button" value="Update" class="btn" onClick="do_submit_settings()"> &nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="parent.do_options( <?php echo $option ?>, <?php echo $deptid ?> );">cancel</a></div>
		</div>
		<div id="div_settings" style="display: none;">
			<div class="info_info">
				<div style="">
					<div style="font-weight: bold; font-size: 14px;">Chat Response Timestamp</div>
					<div style="margin-top: 5px;">During the chat session, display the chat response timestamp next to the name?</div>
				</div>
				<div class="info_neutral" style="margin-top: 10px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> This setting only effects the visitor chat session.  Timestamp will always be displayed for the operator.</div>

				<div style="margin-top: 15px; padding-bottom: 15px;">
					<table cellspacing=0 cellpadding=2 border=0>
					<tr>
						<td valign="top">
							<div style=""><img src="../pics/screens/timestamp.gif" width="210" height="60" border="0" alt="" style="border: 1px solid #E8E9EB;" class="round" id="img_timestamp"></div>
						</td>
						<td>
							<div style="padding-left: 15px;">
								<div class="info_good" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onClick="$('#timestamp_on').prop('checked',true);toggle_timestamp(1);"><input type="radio" name="timestamp" id="timestamp_on" value=1 <?php echo ( !isset( $deptvars["timestamp"] ) || $deptvars["timestamp"] ) ? "checked" : "" ; ?>> On</div>
								<div class="info_error" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onClick="$('#timestamp_off').prop('checked',true);toggle_timestamp(0);"><input type="radio" name="timestamp" id="timestamp_off" value=0 <?php echo ( !isset( $deptvars["timestamp"] ) || $deptvars["timestamp"] ) ? "" : "checked" ; ?>> Off</div>
								<div style="clear: both;"></div>
							</div>
						</td>
					</tr>
					</table>
				</div>
			</div>

			<?php if ( count( $departments ) > 1 ) : ?>
			<div style="margin-top: 15px;"><input type="checkbox" id="copy_all" name="copy_all" value=1> copy this update (<b>Connecting Message</b> and <b>Chat Response Timestamp</b>) to all departments</div>
			<?php endif ; ?>

			<div style="margin-top: 15px;"><input type="button" value="Update" class="btn" onClick="do_submit_settings()"> &nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="parent.do_options( <?php echo $option ?>, <?php echo $deptid ?> );">cancel</a></div>
		</div>
	</div>
	</form>
</div>

</body>
</html>