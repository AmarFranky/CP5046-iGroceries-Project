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

	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;

	$error = "" ;

	if ( $action === "update" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_File.php" ) ;
		$profile_pic_onoff = Util_Format_Sanatize( Util_Format_GetVar( "profile_pic_onoff" ), "n" ) ;

		LIST( $error, $filename ) = Util_Upload_File( "profile", $opid ) ;
		if ( !$error )
		{
			if ( $opid )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
				Ops_update_OpValue( $dbh, $opid, "pic", $profile_pic_onoff ) ;
			}
		}
	}
	else if ( ( $action === "clear" ) && $opid )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		Ops_update_OpValue( $dbh, $opid, "pic_form_display", 0 ) ;

		$dir_files = glob( $CONF["CONF_ROOT"]."/profile_$opid.*", GLOB_NOSORT ) ;
		$total_dir_files = count( $dir_files ) ;
		if ( $total_dir_files )
		{
			for ( $c = 0; $c < $total_dir_files; ++$c )
			{
				if ( $dir_files[$c] && is_file( $dir_files[$c] ) ) { @unlink( $dir_files[$c] ) ; }
			}
		}
	}

	$operators = Ops_get_AllOps( $dbh ) ;
	$opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ;
	$opvars = Ops_get_OpVars( $dbh, $opid ) ;

	$profile_pic_onoff = ( $opid && ( isset( $opinfo["pic"] ) && ( $opinfo["pic"] == 1 ) ) ) ? 1 : 0 ;
	$profile_pic_uploaded = ( $opid && ( Util_Upload_GetLogo( "profile", 0 ) != Util_Upload_GetLogo( "profile", $opid ) ) ) ? 1 : 0 ;
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
	var global_profile_pic_onoff = parseInt( <?php echo $profile_pic_onoff ?> ) ;
	var global_pic_edit = parseInt( <?php echo isset( $opvars["pic_edit"] ) ? $opvars["pic_edit"] : 0 ?> ) ;
	var global_form_display = parseInt( <?php echo $opinfo["pic_form_display"] ?> ) ;

	$(document).ready(function()
	{
		$("html").css({'background': '#272727'}) ; $("body").css({'background': '#D6E4F2'}) ;
		init_menu() ;
		toggle_menu_setup( "ops" ) ;

		<?php if ( $action && $error ): ?>
		do_alert_div( "..", 0, "<?php echo $error ?>" ) ;
		<?php elseif ( $action ): ?>
		do_alert(1, "Success" ) ;
		<?php endif ; ?>

	});

	function switch_op()
	{
		var opid = $('#select_ops').val() ;
		location.href = "interface_op_pics.php?opid="+opid ;
	}

	function confirm_clear()
	{
		if ( confirm( "Really clear this operator profile picture and use Global Default?" ) )
		{
			location.href = "interface_op_pics.php?action=clear&opid=<?php echo $opid ?>" ;
		}
	}

	function update_profile_pic_onoff( thevalue )
	{
		if ( global_profile_pic_onoff != thevalue )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions_.php",
				data: "action=update_profile_pic_onoff&opid=<?php echo $opid ?>&value="+thevalue+"&"+unixtime(),
				success: function(data){
					location.href = "interface_op_pics.php?opid=<?php echo $opid ?>&action=success" ;
				}
			});
		}
	}

	function confirm_pic_edit( thevalue )
	{
		if ( global_pic_edit != thevalue )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions_.php",
				data: "action=update_pic_edit&opid=<?php echo $opid ?>&value="+thevalue+"&flag=<?php echo ( isset( $opvars["pic_edit"] ) ) ? 1 : 0 ; ?>&"+unixtime(),
				success: function(data){
					global_pic_edit = thevalue ;
					do_alert( 1, "Success" ) ;
				}
			});
		}
	}

	function confirm_pic_form_display( thevalue )
	{
		if ( global_form_display != thevalue )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions_.php",
				data: "action=update_pic_form_display&opid=<?php echo $opid ?>&value="+thevalue+"&flag=<?php echo $opinfo["pic_form_display"] ?>&"+unixtime(),
				success: function(data){
					global_form_display = thevalue ;
					do_alert( 1, "Success" ) ;

					$('#iframe_example').attr('src', "iframe_profile_pics.php?opid=<?php echo $opid ?>&value="+thevalue ).ready(function() {
						//
					});
				}
			});
		}
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" style="margin-left: 0px;" onClick="location.href='ops.php?jump=main'" id="menu_ops_main">Chat Operators</div>
			<div class="op_submenu" onClick="location.href='ops.php?jump=assign'" id="menu_ops_assign">Assign Operator to Department</div>
			<div class="op_submenu_focus">Profile Picture</div>
			<div class="op_submenu" onClick="location.href='ops_reports.php'" id="menu_ops_report">Online Activity</div>
			<div class="op_submenu" onClick="location.href='ops.php?jump=monitor'" id="menu_ops_monitor">Status Monitor</div>
			<div class="op_submenu" onClick="location.href='ops.php?jump=online'" id="menu_ops_online"><img src="../pics/icons/bulb.png" width="12" height="12" border="0" alt=""> Go ONLINE!</div>
			<div style="clear: both"></div>
		</div>

		<form method="POST" action="interface_op_pics.php" enctype="multipart/form-data">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="opid" value="<?php echo $opid ?>">
		<input type="hidden" name="MAX_FILE_SIZE" value="250000">

		<div style="margin-top: 25px;" id="div_op_pics">
			Upload operator profile picture.  Various profile pictures can be downloaded at the <a href="http://www.phplivesupport.com/r.php?r=avatars" target="_blank">avatars icons page</a>.

			<?php if ( count( $operators ) > 0 ): ?>
			<div style="margin-top: 25px;">
				<div style="">
					<select id="select_ops" style="font-size: 16px;" onChange="switch_op()"><option value="0">Global Default</option>
					<?php
						for ( $c = 0; $c < count( $operators ); ++$c )
						{
							$operator = $operators[$c] ;
							$selected = "" ;
							if ( $opid == $operator["opID"] )
							{
								$selected = "selected" ;
								$op_name = $operator["name"] ;
							}
							$p_onoff = ( $operator["pic"] == 1 ) ? "(on)" : "(off)" ;
							print "<option value=\"$operator[opID]\" $selected>$operator[name] $p_onoff</option>" ;
						}
					?>
					</select>
				</div>
				<div style="margin-top: 15px;">
					<table cellspacing=0 cellpadding=0 border=0 width="100%">
					<tr>
						<td valign="top" width="150">
							<div id="div_profile">
								<img src="<?php print Util_Upload_GetLogo( "profile", $opid ) ?>" width="55" height="55" border=0 style="border: 1px solid #DFDFDF;" class="round"> &nbsp; &nbsp;

								<?php if ( $profile_pic_uploaded ): ?>
								<div style="margin-top: 5px;"><img src="../pics/icons/reset.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="confirm_clear()">clear picture and use Global Default</a></div>
								<?php elseif ( $opid ): ?>
								<div style="margin-top: 5px;"><img src="../pics/icons/vcard.png" width="16" height="16" border="0" alt=""> currently using <a href="interface_op_pics.php">Global Default</a></div>
								<?php endif ; ?>
							</div>

							<div id="div_alert" style="display: none; margin-top: 15px; margin-bottom: 25px;"></div>
							<div style="margin-top: 15px;"><img src="../pics/icons/info.png" width="16" height="16" border="0" alt=""> Profile picture should be <b>55 pixels width</b> and <b>55 pixels height</b>.  The image will be automatcially scaled to fit the dimensions.</div>
							<div style="margin-top: 15px;">
								<div><input type="file" name="profile" size="30"></div>
								<div style="margin-top: 5px;"><input type="submit" value="Upload Profile Picture" style="margin-top: 10px;" class="btn" id="btn_upload"></div>
							</div>
						</td>
						<td valign="top" style="padding-left: 15px;">
							<?php if ( $opid ): ?>
							<div>Profile picture setting for operator <span id="span_dept_name" style="font-size: 18px; font-weight: bold;" class="edit_title"><?php echo ( isset( $opinfo["name"] ) ) ? $opinfo["name"] : "Global Default" ; ?></span></div>
							<div class="info_info" style="margin-top: 5px;">
								<div id="div_info_title">Display the operator's profile picture to the visitor during a chat session.</div>
								<div style="margin-top: 5px; text-shadow: none;">
									<div class="info_good" style="float: left; width: 80px; padding: 3px; cursor: pointer;" onclick="$('#profile_pic_on').prop('checked', true);update_profile_pic_onoff(1);"><input type="radio" name="profile_pic_onoff" id="profile_pic_on" value="1" <?php echo ( $profile_pic_onoff ) ? "checked" : "" ?> > Display</div>
									<div class="info_error" style="float: left; margin-left: 10px; padding: 3px; cursor: pointer;" onclick="$('#profile_pic_pic_off').prop('checked', true);update_profile_pic_onoff(0);"><input type="radio" name="profile_pic_onoff" id="profile_pic_off" value="0" <?php echo ( !$profile_pic_onoff ) ? "checked" : "" ?> > Off.  Operator's profile picture will not be displayed to the visitor.</div>
									<div style="clear: both;"></div>
								</div>
							</div>
							<div class="info_info" style="margin-top: 15px;">
								Allow the operator to upload their own profile picture?
								<div style="margin-top: 5px; text-shadow: none;">
									<div class="info_good" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="$('#pic_edit_on').prop('checked', true);confirm_pic_edit(1);"><input type="radio" name="pic_edit" id="pic_edit_on" value="1" <?php echo ( isset( $opvars["pic_edit"] ) && $opvars["pic_edit"] ) ? "checked" : "" ; ?>> Yes</div>
									<div class="info_error" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="$('#pic_edit_off').prop('checked', true);confirm_pic_edit(0);"><input type="radio" name="pic_edit" id="pic_edit_off" value="0" <?php echo ( isset( $opvars["pic_edit"] ) && $opvars["pic_edit"] ) ? "" : "checked" ; ?>> No</div>
									<div style="clear: both;"></div>
								</div>
							</div>
							<?php if ( $opid && $opinfo["pic"] ): ?>
							<div class="info_info" style="margin-top: 15px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td style="">
										<div class="title">Chat Request Profile Picture</div>
										<div style="">If an operator is online, display the operator profile picture on the chat request window.</div>
										<div style="margin-top: 5px;">
											<div class="info_good" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="$('#pic_form_display_on').prop('checked', true);confirm_pic_form_display(1);"><input type="radio" name="pic_form_display" id="pic_form_display_on" value="1" <?php echo ( $opinfo["pic_form_display"] ) ? "checked" : "" ; ?>> Display</div>
											<div class="info_error" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="$('#pic_form_display_off').prop('checked', true);confirm_pic_form_display(0);"><input type="radio" name="pic_form_display" id="pic_form_display_off" value="0" <?php echo ( $opinfo["pic_form_display"] ) ? "" : "checked" ; ?>>Do not display.</div>
											<div style="clear: both;"></div>
										</div>
										<div style="margin-top: 25px;"><img src="../pics/icons/info.png" width="16" height="16" border="0" alt=""> If there are more then 3 operators online, random 3 online operator profile pictures will be displayed.</div>
									</td>
									<td style="padding-left: 15px;">
										<div class="info_action" style="border-bottom: 0px; border-bottom-left-radius: 0px 0px; -moz-border-radius-bottomleft: 0px 0px; border-bottom-right-radius: 0px 0px; -moz-border-radius-bottomright: 0px 0px;"><img src="../pics/icons/arrow_down.png" width="15" height="16" border="0" alt=""> (example) Chat request window.</div>
										<div style=""><iframe src="iframe_profile_pics.php?opid=<?php echo $opid ?>&value=<?php echo $opinfo["pic_form_display"] ?>&preview=1" style="width: 380px; height: 290px; border: 0px; overflow: hidden; -webkit-box-shadow: -0px 7px 29px rgba(0, 0, 0, 0.34); -moz-box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34);" border=0 frameborder=0 class="round" id="iframe_example"></iframe></div>
									</td>
								</tr>
								</table>
							</div>
							<?php endif ; ?>

							<?php endif ; ?>
						</td>
					</tr>
					</table>
				</div>
			</div>
			<?php else: ?>
			<div style="margin-top: 25px;"><span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Add an <a href="ops.php" style="color: #FFFFFF;">Operator</a> to continue.</span></div>
			<?php endif ; ?>
		</div>
		</form>

<?php include_once( "./inc_footer.php" ) ?>
