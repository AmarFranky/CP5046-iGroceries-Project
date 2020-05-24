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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$error = "" ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;

	$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;

	if ( $action == "update_texpire" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/update.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove.php" ) ;
		$texpire = Util_Format_Sanatize( Util_Format_GetVar( "texpire" ), "n" ) ;

		if ( isset( $deptinfo["texpire"] ) )
		{
			Depts_update_DeptValue( $dbh, $deptid, "texpire", $texpire ) ;
			Chat_remove_ExpiredTranscript( $dbh, $deptid, $texpire ) ;
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Department does not exist.\" };" ;

		if ( isset( $dbh ) && isset( $dbh['con'] ) ) { database_mysql_close( $dbh ) ; }
		print $json_data ; exit ;
	}

	$departments = Depts_get_AllDepts( $dbh ) ;
	$texpire = isset( $deptinfo["texpire"] ) ?  $deptinfo["texpire"] : 0 ;
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
<script data-cfasync="false" type="text/javascript" src="../js/global.js?<?php echo filemtime ( "../js/global.js" ) ; ?>"></script>
<script data-cfasync="false" type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>

<script data-cfasync="false" type="text/javascript">
<!--
	var global_texpire = <?php echo $texpire ?> ;

	$(document).ready(function()
	{
		$("html").css({'background': '#272727'}) ; $("body").css({'background': '#D6E4F2'}) ;
		init_menu() ;
		toggle_menu_setup( "trans" ) ;

		<?php if ( $action && !$error ): ?>do_alert(1, "Success") ;<?php endif ; ?>

	});

	function switch_dept( theobject )
	{
		location.href = "transcripts_settings.php?deptid="+theobject.value+"&"+unixtime() ;
	}

	function do_update()
	{
		var texpire = parseInt( $('#texpire').val() ) ;

		if ( texpire != global_texpire )
		{
			$('#texpire').attr('disabled', true) ;

			if ( !texpire ) { $('#span_expired_ago').hide() ; }
			else { $('#span_texpire').html( texpire ) ; $('#span_expired_ago').show() ; }

			$('#div_confirm').show() ;
		}
		else
			do_alert( 0, "Value has not changed." ) ;
	}

	function do_cancel()
	{
		$('#texpire').attr('disabled', false).val(global_texpire) ;
		$('#div_confirm').hide() ;
	}

	function do_update_doit()
	{
		var texpire = parseInt( $('#texpire').val() ) ;

		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "transcripts_settings.php",
		data: "action=update_texpire&texpire="+texpire+"&deptid=<?php echo $deptid ?>&"+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				location.href = "transcripts_settings.php?action=sucess&deptid=<?php echo $deptid ?>" ;
			}
			else
				do_alert( 0, json_data.error ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			alert( "Could not connect to server.  Try refreshing this page." ) ;
		} });
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu_focus" style="margin-left: 0px;" onClick="location.href='transcripts.php'" id="menu_trans_list">Transcripts</div>
			<div class="op_submenu" onClick="location.href='transcripts_tags.php'" id="menu_trans_tags">Tags</div>
			<!-- <div class="op_submenu" onClick="show_div('encr')" id="menu_trans_encr">Encryption</div> -->
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			<div class="op_submenu2" style="margin-left: 0px;" onClick="location.href='transcripts.php'">View Transcripts</div>
			<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/export_transcripts/export_transcripts.php" ) ): ?><div class="op_submenu2" onClick="location.href='../addons/export_transcripts/export_transcripts.php'">Export Transcripts</div><?php endif ; ?>
			<div class="op_submenu_focus" >Automatic Delete</div>
			<div style="clear: both"></div>
		</div>

		<div id="transcripts_list" style="margin-top: 25px;">

			<div style="">
				<div style="margin-bottom: 15px;">
					<select name="deptid" id="deptid" style="font-size: 16px;" OnChange="switch_dept( this )">
					<option value="0">- select department -</option>
					<?php
						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$department = $departments[$c] ;
							$this_texpire = ( $department["texpire"] ) ? $department["texpire"]." months" : "do not delete" ;
							$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
							print "<option value=\"$department[deptID]\" $selected>$department[name] ($this_texpire)</option>" ;
						}
					?>
					</select>
				</div>

				<?php
					if ( $deptid ):
				?>
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td>Set the system to automatically delete department chat transcripts created over &nbsp; </td>
					<td>
						<select id="texpire" onChange="do_update()">
						<option value="0">do not delete</option>
						<?php
							for( $c = 1; $c <= 60; ++$c )
							{
								$selected = ( $c == $texpire ) ? "selected" : "" ;
								print "<option value=\"$c\" $selected>$c</option>" ;
							}
						?>
						</select> months ago.
					</td>
					<td></td>
				</tr>
				</table>

				<div id="div_confirm" style="display: none; margin-top: 15px;" class="info_error"><span id="span_expired_ago">Transcripts created over <span class="info_box"><span id="span_texpire"></span> months ago</span> will be deleted immediately.  This action is permanent.  </span>Are you sure?  &nbsp; <button type="button" class="btn" onClick="do_update_doit()">Yes. Update.</button> &nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="do_cancel();" style="color: #FFFFFF;">cancel</a></div>

				<?php else: ?>
				<img src="../pics/icons/arrow_top.png" width="15" height="16" border="0" alt=""> Select a department to update the automatic transcript delete setting.
				<?php endif ; ?>
			</div>

		</div>

<?php include_once( "./inc_footer.php" ) ?>
