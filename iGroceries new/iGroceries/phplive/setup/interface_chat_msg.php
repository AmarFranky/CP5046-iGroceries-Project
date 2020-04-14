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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$error = "" ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$copy_all = Util_Format_Sanatize( Util_Format_GetVar( "copy_all" ), "n" ) ;

	$text = Util_Format_Sanatize( Util_Format_GetVar( "text" ), "noscripts" ) ;
	$text = preg_replace( "/\"/", "'", $text ) ;
	$text = preg_replace( "/<html(.*?)>/i", "'", $text ) ; $text = preg_replace( "/<body(.*?)>/i", "'", $text ) ;

	$deptinfo = Array() ;
	$departments = Depts_get_AllDepts( $dbh ) ;

	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		if ( $department["deptID"] == $deptid )
		{
			$deptinfo = $department ;
			$deptvars = Depts_get_DeptVars( $dbh, $deptid ) ;
			break ;
		}
	}

	$chat_end_message = ( isset( $deptvars["end_chat_msg"] ) && $deptvars["end_chat_msg"] ) ? $deptvars["end_chat_msg"] : "" ;
	if ( $action == "update" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/update.php" ) ;

		if ( $copy_all )
		{
			for( $c = 0; $c < count( $departments ); ++$c )
			{
				if ( !Depts_update_DeptVarsValue( $dbh, $departments[$c]["deptID"], "end_chat_msg", $text ) )
				{
					$error = "Error in processing update.  Please try again." ;
					break ;
				}
			}
		}
		else if ( !Depts_update_DeptVarsValue( $dbh, $deptid, "end_chat_msg", $text ) )
			$error = "Error in processing update.  Please try again." ;

		if ( !$error )
		{
			$chat_end_message = $text ;
			$deptvars["end_chat_msg"] = $chat_end_message ;
		}
	}

	$chat_end_message_md5 = md5( $chat_end_message ) ;
	$deptvars_all = Depts_get_AllDeptsVars( $dbh ) ;
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
		location.href = "interface_chat_msg.php?deptid="+theobject.value+"&"+unixtime() ;
	}

	function view_preview( theflag )
	{
		var text = encodeURIComponent( $('#text').val() ) ;

		if ( !text )
		{
			do_alert( 0, "Blank message is invalid." ) ;
			setTimeout( function(){ $('#text').fadeTo( "fast", 0.1 ).fadeTo( "fast", 1 ).fadeTo( "fast", 0.1 ).fadeTo( "fast", 1 ).fadeTo( "fast", 0.1 ).fadeTo( "fast", 1 ) ; }, 3500 ) ;
			return false ;
		}
		else
		{
			$('#iframe_widget_embed').attr("src", "iframe_chat_msg.php?&preview=1&deptid=<?php echo $deptid ?>&text="+text+"&"+unixtime()) ;
		}
	}

	function do_reset()
	{
		$('#div_text').fadeTo( "fast", 0.1 ).fadeTo( "fast", 1 ) ;
		$('#form_txt').trigger("reset") ;

		$('#btn_reset').hide() ;
	}

	function do_update()
	{
		$('#form_txt').submit() ;
	}

	function do_delete( thedeptid )
	{
		if ( confirm( "Really clear the Chat End Message?" ) )
		{
			var unique = unixtime() ;
			var json_data ;

			$.ajax({
			type: "POST",
			url: "../ajax/setup_actions_.php",
			data: "action=delete_endmsg&deptid="+thedeptid+"&"+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					location.href = "interface_chat_msg.php?action=success&deptid="+thedeptid+"&"+unique ;
				}
				else
					do_alert( 0, json_data.error ) ;

			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Connection error.  Refresh the page and try again." ) ;
			} });
		}
	}

	function input_text_event( e )
	{
		var text = $('#text').val().trim() ;

		if ( ( phplive_md5( text ) != "<?php echo $chat_end_message_md5 ?>" ) )
		{
			if ( !$('#btn_reset').is(":visible") )
				$('#btn_reset').show() ;
		}
		else if ( $('#btn_reset').is(":visible") )
			$('#btn_reset').hide() ;
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
			<div class="op_submenu" onClick="location.href='interface_lang.php'" id="menu_lang">Language Text</div>
			<div class="op_submenu" onClick="location.href='interface_gdpr.php'" id="menu_gdpr">Privacy & GDPR</div>
			<div class="op_submenu_focus">Chat End Msg</div>
			<div class="op_submenu" onClick="location.href='interface.php?jump=screen'">Login Screen</div>
			<div class="op_submenu" onClick="location.href='interface.php?jump=props'">Properties</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			<form method="POST" action="interface_chat_msg.php" enctype="multipart/form-data" id="form_txt">
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
			<input type="hidden" name="pos" id="pos" value="">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td valign="top" width="<?php echo $VARS_CHAT_WIDTH_WIDGET ?>">
					<div style="text-align: justify;"><b>Chat End Message</b> will be displayed to the visitor when the chat has ended.  The message will be displayed immediately after the "chat has ended" notice.</div>

					<div id='phplive_survey' style='margin-top: 15px; width: <?php echo $VARS_CHAT_WIDTH_WIDGET ?>px; overflow: auto; -moz-border-radius: 5px; border-radius: 5px; -webkit-box-shadow: -0px 7px 29px rgba(0, 0, 0, 0.34); -moz-box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34);'>
						<iframe id='iframe_widget_embed' name='iframe_widget_embed' style='width: 100%; height: 240px; -moz-border-radius: 5px; border-radius: 5px; border: 0px;' src='iframe_chat_msg.php?deptid=<?php echo $deptid ?>' scrolling='yes' border=0 frameborder=0></iframe>
					</div>
				</td>
				<td valign="top" width="100%" style="padding-left: 25px;">
					<div style="margin-bottom: 15px;">
						Department <select name="deptid" id="deptid" style="font-size: 16px;" onChange="switch_dept( this )">
						<option value="0">- select department -</option>
						<?php
							for ( $c = 0; $c < count( $departments ); ++$c )
							{
								$department = $departments[$c] ;
								$temp_deptid = $department["deptID"] ;
								$enabled = ( isset( $deptvars_all[$temp_deptid]["end_chat_msg"] ) && $deptvars_all[$temp_deptid]["end_chat_msg"] ) ? "(enabled)" : "" ;
								$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
								print "<option value=\"$department[deptID]\" $selected>$department[name] $enabled</option>" ;
							}
						?>
						</select>
					</div>
					<div style="<?php echo ( !$deptid ) ? "display: none;" : "" ; ?>">

						<div style="margin-top: 15px;">
							<div style="margin-top: 15px; background: url( ../pics/icons/warning.png ) no-repeat; padding-left: 20px;"> Try to fit the content within the available box size on the left.  Visitors will be able to view the entire content without having to scroll.</div>
						</div>

						<div style="margin-top: 15px;">Message (HTML is ok.):</div>
						<div style="margin-top: 5px; background: url( ../pics/icons/warning.png ) no-repeat; padding-left: 20px;"> For URL links, include the <code>target='_blank'</code> in the <code>href</code> tag to open the link in a new window instead of inside the chat request window.</div>
						<div id="div_text">
							<textarea style="width: 95%; resize: vertical;" rows="8" class="input" name="text" id="text" onKeyup="input_text_event(event);" spellcheck="true"><?php echo $chat_end_message ; ?></textarea>
						</div>

						<div style="margin-top: 5px;"><span class=""><img src="../pics/icons/arrow_left.png" width="16" height="15" border="0" alt=""> <a href="JavaScript:void(0)" onClick="view_preview()">view how it will look</a></span></div>

						<div style="margin-top: 35px;">
							<?php if ( count( $departments ) > 1 ) : ?>
							<div style="margin-top: 25px;"><input type="checkbox" id="copy_all" name="copy_all" value=1> copy this update to all departments</div>
							<?php endif ; ?>

							<div style="margin-top: 15px;">
								<button type="button" class="btn" onClick="do_update()">Update Message</button> &nbsp; &nbsp;
								<?php if ( $deptid && isset( $deptvars["end_chat_msg"] ) && $deptvars["end_chat_msg"] ): ?>
									<button type="button" class="btn" onClick="do_reset()" id="btn_reset" style="display: none;">Reset</button> 
									&nbsp; or &nbsp; <a href="JavaScript:void(0)" onClick="do_delete(<?php echo $deptid ?>)">clear message</a>
								<?php endif ; ?>
							</div>
						</div>
					</div>
				</td>
			</tr>
			</table>
			</form>
		</div>

<?php include_once( "./inc_footer.php" ) ?>