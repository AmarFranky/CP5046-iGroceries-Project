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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Hash.php" ) ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_File.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$https = "" ;
	if ( isset( $_SERVER["HTTP_CF_VISITOR"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_CF_VISITOR"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTP_X_FORWARDED_PROTO"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_X_FORWARDED_PROTO"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTPS"] ) && preg_match( "/(on)/i", $_SERVER["HTTPS"] ) ) { $https = "s" ; }

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$jump = Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ; if ( !$jump ) { $jump = "logo" ; }
	$jump2 = Util_Format_Sanatize( Util_Format_GetVar( "jump2" ), "ln" ) ; if ( !$jump2 ) { $jump2 = "upload" ; }
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$lang = Util_Format_Sanatize( Util_Format_GetVar( "lang" ), "ln" ) ;

	if ( !isset( $CONF["screen"] ) ) { $CONF["screen"] = "same" ; }
	if ( !isset( $CONF["THEME"] ) ) { $CONF["THEME"] = "default" ; }
	if ( !isset( $CONF["lang"] ) ) { $CONF["lang"] = "english" ; } if ( !$lang ) { $lang = $CONF["lang"] ; }
	$dept_themes = ( isset( $VALS["THEMES"] ) && $VALS["THEMES"] ) ? unserialize( $VALS["THEMES"] ) : Array() ;
	if ( isset( $dept_themes[0] ) ) { $CONF["THEME"] = $dept_themes[0] ; }

	$error = "" ;

	$deptinfo = Array() ;
	if ( $deptid )
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	LIST( $your_ip, $null ) = Util_IP_GetIP( "" ) ;

	if ( $action === "update" )
	{
		if ( $jump == "logo" )
			LIST( $error, $filename ) = Util_Upload_File( "logo", $deptid ) ;
		else if ( $jump == "time" )
		{
			$timezone = Util_Format_Sanatize( Util_Format_GetVar( "timezone" ), "timezone" ) ;

			if ( $timezone != $CONF["TIMEZONE"] )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove.php" ) ;
				Chat_remove_ResetReports( $dbh ) ;

				$error = ( Util_Vals_WriteToConfFile( "TIMEZONE", $timezone ) ) ? "" : "Could not write to config file." ;
				if ( phpversion() >= "5.1.0" ){ date_default_timezone_set( $timezone ) ; }
			}
		}
	}
	else if ( $action === "screen" )
	{
		$screen = Util_Format_Sanatize( Util_Format_GetVar( "screen" ), "ln" ) ;

		if ( $CONF["screen"] != $screen )
		{
			$error = ( Util_Vals_WriteToConfFile( "screen", $screen ) ) ? "" : "Could not write to config file." ;
			$CONF["screen"] = $screen ;
		}
		$jump = "screen" ;
	}
	else if ( ( $action === "clear" ) && $deptid )
	{
		$dir_files = glob( $CONF["CONF_ROOT"]."/logo_$deptid.*", GLOB_NOSORT ) ;
		$total_dir_files = count( $dir_files ) ;
		if ( $total_dir_files )
		{
			for ( $c = 0; $c < $total_dir_files; ++$c )
			{
				if ( $dir_files[$c] && is_file( $dir_files[$c] ) ) { @unlink( $dir_files[$c] ) ; }
			}
		}
	}
	else if ( $action == "format" )
	{
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "n" ) ;

		$timeformat = ( $value != 24 ) ? 12 : 24 ;
		if ( Util_Vals_WriteToFile( "TIMEFORMAT", $timeformat ) )
		{
			$VARS_24H = ( $value != 24 ) ? 0 : 1 ;
			$VARS_TIMEFORMAT = ( !$VARS_24H ) ? "g:i:s a" : "G:i:s" ;
			$action = "success" ;
		}
		else
			$error = "Error updating time format." ;
	}

	$screen_same = ( $CONF["screen"] == "same" ) ? "checked" : "" ;
	$screen_separate = ( $screen_same == "checked" ) ? "" : "checked" ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$timezones = Util_Hash_Timezones() ;
	$vars = Util_Format_Get_Vars( $dbh ) ;
	$charset = ( isset( $vars["char_set"] ) && $vars["char_set"] ) ? unserialize( $vars["char_set"] ) : Array(0=>"UTF-8") ;
	$emlogos_hash = ( isset( $VALS["EMLOGOS"] ) ) ? unserialize( $VALS["EMLOGOS"] ) : Array() ;

	$login_url = $CONF['BASE_URL'] ;
	if ( !preg_match( "/\/\//", $login_url ) ) { $login_url = "//$PHPLIVE_HOST$login_url" ; }
	$dept_groups = Depts_get_AllDeptGroups( $dbh ) ;

	$global_default_logo = Util_Upload_GetLogo( "logo", 0 ) ;
	$logo = $global_default_logo ;
	if ( $deptid )
	{
		$logo = Util_Upload_GetLogo( "logo", $deptid ) ;
	}
	$is_using_global_default_logo = ( $logo == $global_default_logo ) ? 1 : 0 ;
	$embed_win_sizes = ( isset( $VALS["embed_win_sizes"] ) ) ? unserialize( $VALS["embed_win_sizes"] ) : Array() ;
	if ( isset( $embed_win_sizes[0] ) )
	{
		$VARS_CHAT_WIDTH_WIDGET = $embed_win_sizes[0]["width"] ;
		$VARS_CHAT_HEIGHT_WIDGET = $embed_win_sizes[0]["height"] ;
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
	var global_div_sub = "" ;
	var global_charset = "<?php echo $charset[0] ?>" ;
	var global_timeformat = <?php echo ( !isset( $VALS["TIMEFORMAT"] ) || ( isset( $VALS["TIMEFORMAT"] ) && ( $VALS["TIMEFORMAT"] != 24 ) ) ) ? 12 : 24 ; ?> ;
	var global_emlogo = <?php echo ( isset( $emlogos_hash[0] ) && $emlogos_hash[0] ) ? 1 : 0 ; ?> ;
	var global_autocorrect_v = <?php echo ( !isset( $VALS["AUTOCORRECT_V"] ) || ( isset( $VALS["AUTOCORRECT_V"] ) && $VALS["AUTOCORRECT_V"] ) ) ? 1 : 0 ; ?> ;
	var global_autocorrect_o = <?php echo ( !isset( $VALS["AUTOCORRECT_O"] ) || ( isset( $VALS["AUTOCORRECT_O"] ) && $VALS["AUTOCORRECT_O"] ) ) ? 1 : 0 ; ?> ;

	$(document).ready(function()
	{
		$("html").css({'background': '#272727'}) ; $("body").css({'background': '#D6E4F2'}) ;
		init_menu() ;
		toggle_menu_setup( "interface" ) ;

		show_div( "<?php echo $jump ?>" ) ;

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
		<?php if ( $action && $error ): ?>do_alert_div( "..", 0, "<?php echo $error ?>" ) ;<?php endif ; ?>
		<?php if ( $deptid && !$is_using_global_default_logo ): ?>$('#div_notice_html').show() ; $('#div_notice_html_settings').show() ;<?php endif ; ?>

		$('#urls_<?php echo $CONF["screen"] ?>').show() ;

		check_image_dim() ;
	});

	function show_div( thediv )
	{
		$('#div_alert').hide() ;
	
		var divs = Array( "logo", "charset", "time", "screen", "lang", "props" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#settings_'+divs[c]).hide() ;
			$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('input#jump').val( thediv ) ;
		$('#settings_'+thediv).show() ;
		$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;

		if ( thediv == "logo" )
			show_div_logo( "<?php echo $jump2 ?>" ) ;
		else if ( thediv == "props" )
			show_div_props( "autocorrect" ) ;
	}

	function show_div_logo( thediv )
	{
		var divs = Array( "upload", "settings" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#settings_logo_'+divs[c]).hide() ;
			$('#menu2_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu2') ;
		}

		global_div_sub = thediv ;

		if ( thediv == "settings" )
		{
			$('#iframe_widget_embed').attr( 'src', "../phplive.php?embed=1&d=<?php echo $deptid ?>&preview=1&"+unixtime() ).load(function ( ){
				//
			});
		}

		$('#settings_logo_'+thediv).show() ;
		$('#menu2_'+thediv).removeClass('op_submenu2').addClass('op_submenu_focus') ;
	}

	function show_div_props( thediv )
	{
		var divs = Array( "autocorrect" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#settings_props_'+divs[c]).hide() ;
			$('#menu2_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu2') ;
		}

		$('#settings_props_'+thediv).show() ;
		$('#menu2_'+thediv).removeClass('op_submenu2').addClass('op_submenu_focus') ;
	}

	function switch_dept( theobject )
	{
		location.href = "interface.php?deptid="+theobject.value+"&jump2="+global_div_sub ;
	}

	function update_timezone()
	{
		var timezone = $('#timezone').val() ;

		if ( timezone != "<?php echo $CONF["TIMEZONE"] ?>" )
		{
			if ( confirm( "This action will reset the chat reports data.  Are you sure?" ) )
				location.href = "interface.php?action=update&jump=time&timezone="+timezone ;
			else
				$('#timezone').val( "<?php echo $CONF["TIMEZONE"] ?>" ) ;
		}
		else
			do_alert( 1, "System timezone is already "+timezone+"." ) ;
	}

	function confirm_charset( thecharset )
	{
		if ( global_charset != thecharset )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "action=update_vars&varname=char_set&value="+thecharset+"&"+unixtime(),
				success: function(data){
					global_charset = thecharset ;
					do_alert( 1, "Success" ) ;
				}
			});
		}
	}

	function check_image_dim()
	{
		var img = new Image() ;
		img.onload = get_img_dim ;
		img.src = '<?php print Util_Upload_GetLogo( "logo", $deptid ) ?>' ;
	}

	function get_img_dim()
	{
		var img_width = this.width ;
		var img_height = this.height ;

		//$('#div_logo').css({'width': img_width, 'height': img_height}) ;
	}

	function confirm_clear()
	{
		if ( confirm( "Really remove this department logo and use Global Default?" ) )
		{
			location.href = "interface.php?action=clear&deptid=<?php echo $deptid ?>" ;
		}
	}

	function toggle_emlogo( thevalue )
	{
		if ( global_emlogo != thevalue )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "action=update_emlogo&deptid=<?php echo $deptid ?>&value="+thevalue+"&"+unixtime(),
				success: function(data){
					global_emlogo = thevalue ;

					$('#iframe_widget_embed').attr( 'src', "../phplive.php?embed=1&d=0&preview=1&d=<?php echo $deptid ?>&"+unixtime() ).load(function ( ){
						do_alert( 1, "Success" ) ;
					});
				}
			});
		}
	}

	function toggle_autocorrect( the_vo, thevalue )
	{
		if ( ( ( the_vo == "v" ) && ( global_autocorrect_v != thevalue ) ) || ( ( the_vo == "o" ) && ( global_autocorrect_o != thevalue ) ) )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "action=update_autocorrect&vo="+the_vo+"&value="+thevalue+"&"+unixtime(),
				success: function(data){
					if ( the_vo == "v" ) { global_autocorrect_v = thevalue ; }
					else { global_autocorrect_o = thevalue ; }
					do_alert( 1, "Success" ) ;
				}
			});
		}
	}

	function confirm_change( theformat )
	{
		if ( parseInt( global_timeformat ) != parseInt( theformat ) )
			location.href = "interface.php?action=format&jump=time&value="+theformat+"&"+unixtime() ;
	}

	function view_preview( theupdateflag )
	{
		var width = $('#embed_width').val().trim() ; $('#embed_width').val( width ) ;
		var height = $('#embed_height').val().trim() ; $('#embed_height').val( height ) ;

		if ( !parseInt( width ) || !parseInt( height ) )
			do_alert( 0, "Value must be a number." ) ;
		else if ( width < 320 )
			do_alert( 0, "Width must be greater then 320 pixels." ) ;
		else if ( width > 800 )
			do_alert( 0, "Width must be less then 800 pixels." ) ;
		else if ( height < 400 )
			do_alert( 0, "Height must be greater then 400 pixels." ) ;
		else if ( height > 800 )
			do_alert( 0, "Height must be less then 800 pixels." ) ;
		else
		{
			var changed = 0 ;

			if ( width != <?php echo ( isset( $embed_win_sizes[$deptid] ) ) ? $embed_win_sizes[$deptid]["width"] : $VARS_CHAT_WIDTH_WIDGET ; ?> )
				changed = 1 ;
			if ( height != <?php echo ( isset( $embed_win_sizes[$deptid] ) ) ? $embed_win_sizes[$deptid]["height"] : $VARS_CHAT_HEIGHT_WIDGET ; ?> )
				changed = 1 ;

			if ( changed )
			{
				$('#phplive_widget_embed_iframe').css({'width': width, 'height': height}).fadeOut("fast").fadeIn("fast") ;

				if ( !theupdateflag ) { $('#span_cancel').show() ; }
				else
				{
					$.ajax({
						type: "POST",
						url: "../ajax/setup_actions_.php",
						data: "action=embed_win&width="+width+"&height="+height+"&d=<?php echo $deptid ?>&"+unixtime(),
						success: function(data){
							location.href = "interface.php?jump2=settings&deptid=<?php echo $deptid ?>&action=success" ;
						}
					});
				}
			}
			else
			{
				do_alert( 0, "There are no changes." ) ;
			}
		}
	}

	function view_preview_cancel( theupdateflag )
	{
		var width ; var height ;

		if ( theupdateflag )
		{
			width = <?php echo $VARS_CHAT_WIDTH_WIDGET ; ?> ;
			height = <?php echo $VARS_CHAT_HEIGHT_WIDGET ; ?> ;
		}
		else
		{
			width = <?php echo ( isset( $embed_win_sizes[$deptid] ) ) ? $embed_win_sizes[$deptid]["width"] : $VARS_CHAT_WIDTH_WIDGET ; ?> ;
			height = <?php echo ( isset( $embed_win_sizes[$deptid] ) ) ? $embed_win_sizes[$deptid]["height"] : $VARS_CHAT_HEIGHT_WIDGET ; ?> ;
		}

		$('#embed_width').val( width ) ;
		$('#embed_height').val( height ) ;

		$('#phplive_widget_embed_iframe').css({'width': width, 'height': height}).fadeOut("fast").fadeIn("fast") ;
		$('#span_cancel').hide() ;

		if ( theupdateflag )
		{
			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions_.php",
				data: "action=embed_win&&d=<?php echo $deptid ?>&"+unixtime(),
				success: function(data){
					location.href = "interface.php?jump2=settings&deptid=<?php echo $deptid ?>&action=success" ;
				}
			});
		}
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="show_div('logo')" id="menu_logo" style="margin-left: 0px;">Logo</div>
			<div class="op_submenu" onClick="location.href='interface_themes.php'">Themes</div>
			<div class="op_submenu" onClick="show_div('charset')" id="menu_charset">Character Set</div>
			<?php if ( phpversion() >= "5.1.0" ): ?><div class="op_submenu" onClick="show_div('time')" id="menu_time">Timezone</div><?php endif; ?>
			<div class="op_submenu" onClick="location.href='interface_lang.php'">Language Text</div>
			<div class="op_submenu" onClick="location.href='interface_gdpr.php'" id="menu_gdpr">Privacy & GDPR</div>
			<div class="op_submenu" onClick="location.href='interface_chat_msg.php'">Chat End Msg</div>
			<div class="op_submenu" onClick="show_div('screen')" id="menu_screen">Login Screen</div>
			<div class="op_submenu" onClick="show_div('props')" id="menu_props">Properties</div>
			<div style="clear: both"></div>
		</div>

		<form method="POST" action="interface.php" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="jump" id="jump" value="">
		<input type="hidden" name="MAX_FILE_SIZE" value="200000">

		<div style="display: none; margin-top: 25px;" id="settings_logo">
			<div id="op_submenu_wrapper_logo">
				<div class="op_submenu2" style="margin-left: 0px;" onClick="show_div_logo('upload')" id="menu2_upload">Upload Logo</div>
				<div class="op_submenu2" onClick="show_div_logo('settings')" id="menu2_settings">Embed Chat Logo Display and Window Size</div>
				<div style="clear: both"></div>
			</div>

			<div style="margin-top: 25px;">
				<?php if ( count( $departments ) > 1 ): ?>
				Department <select name="deptid" id="deptid" style="font-size: 16px;" onChange="switch_dept( this )">
					<option value="0">Global Default</option>
					<?php
						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$department = $departments[$c] ;
							if ( $department["name"] != "Archive" )
							{
								$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
								print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
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
				<?php else: ?>
				<input type="hidden" name="deptid" id="deptid" value="0">
				<?php endif ; ?>
			</div>

			<div id="settings_logo_upload" style="display: none; margin-top: 25px;">
				<ul style="">
					<li id="div_notice_html" style="display: none;"> <span class="info_warning">The logo for this department will display when using the <a href="code.php?deptid=<?php echo $deptid ?>">Department Specific HTML Code</a>.</span></li>
					<li style="margin-top: 15px; line-height: 160%;"> For proper display of the logo, <span class="info_warning" style="padding: 2px;"><b>maximum</b> logo size should be <b><?php echo $VARS_CHAT_WIDTH_WIDGET - 20 ?>px (width)</b> and <b>150px (height)</b></span>.  If the logo is bigger then the recommended dimensions, it will be automatically resized to fit the window.</li>
				</ul>

				<table cellspacing=0 cellpadding=0 border=0 width="100%" class="edit_wrapper" style="margin-top: 15px;">
				<tr>
					<td valign="top">
						<div id="div_alert" style="display: none; margin-bottom: 25px;"></div>

						<?php if ( ( count( $departments ) == 1 ) && isset( $deptinfo["deptID"] ) ): ?>
						<div class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Because only one department is available, choose the <a href="interface.php" style="color: #FFFFFF;">"Global Default"</a> to upload your logo.</div>

						<?php else: ?>
						<div style="">
							<div><input type="file" name="logo" size="30"></div>
							<div style="margin-top: 5px;"><input type="submit" value="Upload Logo" style="margin-top: 10px;" class="btn"></div>
						</div>

						<div id="div_logo" style="margin-top: 25px;"><img src="<?php print $logo ?>" style="max-width: 520px; max-height: 150px; border: 0px;"></div>

						<?php if ( $deptid && !$is_using_global_default_logo ): ?>
							<div style="margin-top: 15px;"><img src="../pics/icons/delete.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="confirm_clear()">remove this logo and use Global Default</a></div>
						<?php elseif ( $deptid ): ?>
							<div style="margin-top: 15px;"><img src="../pics/icons/themes.png" width="16" height="16" border="0" alt=""> currently using <a href="interface.php">Global Default</a></div>
						<?php endif ; ?>

						<div style="margin-top: 25px;"><img src="../pics/icons/arrow_right.png" width="16" height="15" border="0" alt=""> <a href="JavaScript:void(0)" onClick="preview_theme('<?php echo ( isset( $dept_themes[$deptid] ) ) ? $dept_themes[$deptid] : $CONF["THEME"] ; ?>', <?php echo $VARS_CHAT_WIDTH ?>, <?php echo $VARS_CHAT_HEIGHT ?>, <?php echo $deptid ?> )">view how it looks (popup)</a></div>
						<div style="margin-top: 15px;"><img src="../pics/icons/arrow_right.png" width="16" height="15" border="0" alt=""> <a href="JavaScript:void(0)" onClick="show_div_logo('settings')">view how it looks (embed)</a></div>

						<?php endif ; ?>
					</td>
				</tr>
				</table>
			</div>
			<div id="settings_logo_settings" style="display: none; margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td valign="top" width="300">
						<div class="info_neutral">
							<div style="">
								<div style="text-align: justify;">
									The logo is always displayed for the popup window option.
									<div style="margin-top: 15px;" class="info_warning">Should the logo also be displayed for the embed chat window?</div>
								</div>
							</div>

							<div style="margin-top: 25px;">
								<div class="info_good" style="float: left; padding: 3px; cursor: pointer;" onclick="$('#emlogo_on').prop('checked', true);toggle_emlogo(1);"><input type="radio" name="emlogo" id="emlogo_on" value="1" <?php echo ( isset( $emlogos_hash[0] ) && $emlogos_hash[0] ) ? "checked" : "" ; ?>> Yes</div>
								<div class="info_error" style="float: left; margin-left: 10px; padding: 3px; cursor: pointer;" onclick="$('#emlogo_off').prop('checked', true);toggle_emlogo(0);"><input type="radio" name="emlogo" id="emlogo_off" value="0" <?php echo ( isset( $emlogos_hash[0] ) && $emlogos_hash[0] ) ? "" : "checked" ; ?>> No</div>
								<div style="clear: both;"></div>
							</div>
						</div>

						<div class="info_neutral" style="margin-top: 25px;">
							Adjust the embed chat window width and height.
							<div style="margin-top: 15px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td>Width &nbsp; </td>
									<td> <input type="text" class="input" size="4" maxlength="3" name="embed_width" id="embed_width" value="<?php echo isset( $embed_win_sizes[$deptid] ) ? $embed_win_sizes[$deptid]["width"] : $VARS_CHAT_WIDTH_WIDGET ; ?>" onKeyPress="return numbersonly(event)"> pixels</td>
								</tr>
								<tr><td><img src="../pics/space.gif" width="1" height="5" border=0></td></tr>
								<tr>
									<td>Height &nbsp; </td>
									<td> <input type="text" class="input" size="4" maxlength="3" name="embed_height" id="embed_height" value="<?php echo isset( $embed_win_sizes[$deptid] ) ? $embed_win_sizes[$deptid]["height"] : $VARS_CHAT_HEIGHT_WIDGET ; ?>" onKeyPress="return numbersonly(event)"> pixels</td>
								</tr>
								<tr><td><img src="../pics/space.gif" width="1" height="5" border=0></td></tr>
								<tr>
									<td>&nbsp;</td>
									<td>
										<div style="padding-top: 10px;"><span class=""><a href="JavaScript:void(0)" onClick="view_preview(0)">view how it will look</a> <img src="../pics/icons/arrow_right.png" width="16" height="15" border="0" alt=""> </span> &nbsp;&nbsp; <span style="display: none;" id="span_cancel"><a href="JavaScript:void(0)" onClick="view_preview_cancel(0)">cancel</a></span></div>
										<div style="margin-top: 25px;">
											<button type="button" class="btn" onClick="view_preview(1)">Update</button> &nbsp;
											<?php if ( isset( $embed_win_sizes[$deptid] ) && $deptid ): ?>
											<span id="span_embed_win_reset" style="">&bull; reset to use <a href="JavaScript:void(0)" onClick="view_preview_cancel(1)">Global Default</a></span>
											<?php elseif ( isset( $embed_win_sizes[$deptid] ) ): ?>
											<span id="span_embed_win_reset" style="">&bull; reset to use <a href="JavaScript:void(0)" onClick="view_preview_cancel(1)">original size</a></span>
											<?php endif ; ?>
										</div>
									</td>
								</tr>
								</table>
							</div>
						</div>
					</td>
					<td valign="top" style="padding-left: 25px;">
						<div id='phplive_widget_embed_iframe' style='width: <?php echo ( isset( $embed_win_sizes[$deptid] ) ) ? $embed_win_sizes[$deptid]["width"] : $VARS_CHAT_WIDTH_WIDGET ; ?>px; height: <?php echo ( isset( $embed_win_sizes[$deptid] ) ) ? $embed_win_sizes[$deptid]["height"] : $VARS_CHAT_HEIGHT_WIDGET ; ?>px; -moz-border-radius: 5px; border-radius: 5px;'>
							<iframe id='iframe_widget_embed' name='iframe_widget_embed' style='width: 100%; height: 100%; -moz-border-radius: 5px; border-radius: 5px; border: 0px; -webkit-box-shadow: -0px 7px 29px rgba(0, 0, 0, 0.34); -moz-box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34);' src='about:blank' scrolling='no' border=0 frameborder=0></iframe>
						</div>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 45px;">
				<span><b>Related Feature:</b> Chat window open as an embed window or a popup window can be set at <code><a href="./icons.php?jump=settings">Chat Icons > Behavior</a></code>.</span>
			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="settings_props">
			<div id="op_submenu_wrapper_logo">
				<div class="op_submenu2" style="margin-left: 0px;" onClick="show_div_props('autocorrect')" id="menu2_autocorrect">Autocorrect</div>
				<div style="clear: both"></div>
			</div>

			<div style="margin-top: 25px;">
				<div><span style="color: #0078D7; font-weight: bold;">Autocorrect</span> is a web browser feature that sometimes automatically corrects minor misspelled words during a chat session.</div>
				
				<div style="margin-top: 5px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> It is recommended to keep the setting to On unless it is causing some issues (example: international language being autocorrected to English words).</div>
				<div style="margin-top: 15px;"><span class="info_warning"><img src="../pics/icons/warning.gif" width="16" height="16" border="0" alt=""> [ Caution ] Switching "Off" the autocorrect might also disable the automatic spellcheck on some devices.</span></div>
				<div style="margin-top: 25px;">
					<table cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td>
							Visitor chat autocorrect:
						</td>
						<td style="padding-left: 15px;">
							<div style="">
								<div class="info_good" style="float: left; padding: 3px; width: 50px; cursor: pointer;" onclick="$('#autocorrect_v_1').prop('checked', true);toggle_autocorrect('v', 1);"><input type="radio" name="autocorrect_v" id="autocorrect_v_1" value="1" <?php echo ( !isset( $VALS["AUTOCORRECT_V"] ) || ( isset( $VALS["AUTOCORRECT_V"] ) && $VALS["AUTOCORRECT_V"] ) ) ? "checked" : "" ; ?>> On</div>
								<div class="info_error" style="float: left; margin-left: 10px; padding: 3px; width: 50px; cursor: pointer;" onclick="$('#autocorrect_v_0').prop('checked', true);toggle_autocorrect('v', 0);"><input type="radio" name="autocorrect_v" id="autocorrect_v_0" value="0" <?php echo ( isset( $VALS["AUTOCORRECT_V"] ) && !$VALS["AUTOCORRECT_V"] ) ? "checked" : "" ; ?>> Off</div>
								<div style="clear: both;"></div>
							</div>
						</td>
					</tr>
					<tr><td colspan=2>&nbsp;</td></tr>
					<tr>
						<td>
							Operator chat autocorrect:
						</td>
						<td style="padding-left: 15px;">
							<div style="">
								<div class="info_good" style="float: left; padding: 3px; width: 50px; cursor: pointer;" onclick="$('#autocorrect_o_1').prop('checked', true);toggle_autocorrect('o', 1);"><input type="radio" name="autocorrect_o" id="autocorrect_o_1" value="1" <?php echo ( !isset( $VALS["AUTOCORRECT_O"] ) || ( isset( $VALS["AUTOCORRECT_O"] ) && $VALS["AUTOCORRECT_O"] ) ) ? "checked" : "" ; ?>> On</div>
								<div class="info_error" style="float: left; margin-left: 10px; padding: 3px; width: 50px; cursor: pointer;" onclick="$('#autocorrect_o_0').prop('checked', true);toggle_autocorrect('o', 0);"><input type="radio" name="autocorrect_o" id="autocorrect_o_0" value="0" <?php echo ( isset( $VALS["AUTOCORRECT_O"] ) && !$VALS["AUTOCORRECT_O"] ) ? "checked" : "" ; ?>> Off</div>
								<div style="clear: both;"></div>
							</div>
						</td>
					</tr>
					</table>
				</div>
			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="settings_charset">
			If multi-language characters are not rendering properly on the operator chat window or while viewing transcripts, try updating the character set value.  UTF-8 is suggested.

			<div style="margin-top: 25px;">
				<div class="li_op round" style="cursor: pointer;" onclick="$('#charset_UTF-8').prop('checked', true); confirm_charset('UTF-8');"><input type="radio" name="charset" id="charset_UTF-8" value="UTF-8" <?php echo ( $charset[0] == "UTF-8" ) ? "checked" : "" ?>> UTF-8</div>
				<div class="li_op round" style="cursor: pointer;" onclick="$('#charset_ISO-8859-1').prop('checked', true); confirm_charset('ISO-8859-1');"><input type="radio" name="charset" id="charset_ISO-8859-1" value="ISO-8859-1" <?php echo ( $charset[0] == "ISO-8859-1" ) ? "checked" : "" ?>> ISO-8859-1</div>
				<div style="clear: both;"></div>
			</div>
		</div>

		<?php if ( phpversion() >= "5.1.0" ): ?>
		<div style="display: none; margin-top: 25px;" id="settings_time">

			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td valign="top">

					<div style="">
						<select id="timezone">
						<?php
							for ( $c = 0; $c < count( $timezones ); ++$c )
							{
								$selected = "" ;
								if ( $timezones[$c] == date_default_timezone_get() )
									$selected = "selected" ;

								print "<option value=\"$timezones[$c]\" $selected>$timezones[$c]</option>" ;
							}
						?>
						</select>
					</div>
					<div style="margin-top: 15px;">
						<div style="text-align: justify;" class="info_error">
							<table cellspacing=0 cellpadding=0 border=0>
							<tr>
								<td><img src="../pics/icons/alert.png" width="16" height="16" border="0" alt=""></td>
								<td style="padding-left: 5px;">Updating the timezone will reset (clear) the <a href="reports_chat.php" style="color: #FFFFFF;">chat reports data</a>.</td>
							</tr>
							</table>

							<div style="margin-top: 5px;">The reset is necessary because the past data timezone will conflict with the new timezone, resulting in invalid data and possible errors.  Be sure to print out the current reports before continuing.  The <a href="transcripts.php" style="color: #FFFFFF;">chat transcripts</a> will not be deleted but the created timestamp may be different from the original because of the timezone change.</div>
						</div>
					</div>

					<div style="margin-top: 25px;"><button type="button" onClick="update_timezone()" class="btn">Update Timezone</button></div>
				</td>
				<td width="25"><img src="../pics/space.gif" width="25" height="1" border=0></td>
				<td valign="top" width="500" class="info_info">
					<div>
						<div>System Time:</div>
						<div class="info_blue_dark" style="display: inline-block; margin-top: 15px; font-size: 18px; font-weight: bold; font-family: sans-serif;"><?php echo $CONF['TIMEZONE'] ?></div>
						<div style="margin-top: 15px; font-size: 32px; font-weight: bold; color: #3A89D1; font-family: sans-serif;"><?php echo date( "M j, Y ($VARS_TIMEFORMAT)", time() ) ; ?></div>
					</div>

					<div style="margin-top: 25px;">Updating the hour format will not reset any data.  It simply formats the hour display to 12 or 24.</div>
					<div style="margin-top: 15px;">
						<span class="info_neutral" style="margin-left: 5px; cursor: pointer;" onclick="$('#timeformat_12').prop('checked', true);confirm_change(12);"><input type="radio" id="timeformat_12" name="timeformat_12" value="12" <?php echo ( !$VARS_24H ) ? "checked" : "" ; ?>> 12h</span>
						<span class="info_neutral" style="margin-left: 5px; cursor: pointer;" onclick="$('#timeformat_24').prop('checked', true);confirm_change(24);"><input type="radio" id="timeformat_24" name="timeformat_12" value="24" <?php echo ( $VARS_24H ) ? "checked" : "" ; ?>> 24h</span>
					</div>
				</td>
			</tr>
			</table>

		</div>
		<?php endif; ?>

		<div style="display: none; margin-top: 25px;" id="settings_screen">
			Choose whether to display the operator login and the setup login screens on the same URL or separate URLs.
		
			<div style="margin-top: 25px;">
				<div class="li_op round" style="cursor: pointer;" onclick="$('#screen_one').prop('checked', true); location.href='interface.php?action=screen&screen=same';"><input type="radio" name="screen" id="screen_one" value="same" <?php echo $screen_same ?>> Same URL</div>
				<div class="li_op round" style="cursor: pointer;" onclick="$('#screen_two').prop('checked', true); location.href='interface.php?action=screen&screen=separate';"><input type="radio" name="screen" id="screen_two" value="separate" <?php echo $screen_separate ?>> Separate URLs</div>
				<div style="clear: both;"></div>
			</div>

			<div style="margin-top: 25px;">
				<div id="urls_same" style="display: none;" class="info_info">
					<div style=""><img src="../pics/icons/user_chat_big.png" width="24" height="24" border="0" alt=""> <img src="../pics/icons/settings_big.png" width="24" height="24" border="0" alt=""> Operator and Setup Admin Login URL</div>
					<div style="margin-top: 5px; font-size: 32px; font-weight: bold;"><a href="<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$login_url" : $login_url ; ?>" target="new" style="color: #1DA1F2;" class="nounder"><?php echo ( !preg_match( "/^(http)/", $login_url ) ) ? "http$https:$login_url" : $login_url ; ?></a></div>
				</div>
				<div id="urls_separate" style="display: none;">
					<div class="info_info">
						<div style="font-size: 14px; font-weight: bold;"><img src="../pics/icons/user_chat_big.png" width="24" height="24" border="0" alt=""> Operator Login URL</div>
						<div style="margin-top: 5px; font-size: 32px; font-weight: bold;"><a href="<?php echo ( !preg_match( "/^(http)/", $login_url ) ) ? "http$https:$login_url" : $login_url ; ?>" target="new" style="color: #1DA1F2;" class="nounder"><?php echo ( !preg_match( "/^(http)/", $login_url ) ) ? "http$https:$login_url" : $login_url ; ?></a></div>
					</div>

					<div class="info_info" style="margin-top: 25px;">
						<div style="font-size: 14px; font-weight: bold;"><img src="../pics/icons/settings_big.png" width="24" height="24" border="0" alt=""> Setup Admin Login URL</div>
						<div style="margin-top: 5px; font-size: 32px; font-weight: bold;"><a href="<?php echo ( !preg_match( "/^(http)/", $login_url ) ) ? "http$https:$login_url" : $login_url ; ?>/setup" target="new" style="color: #1DA1F2;" class="nounder"><?php echo ( !preg_match( "/^(http)/", $login_url ) ) ? "http$https:$login_url" : $login_url ; ?>/setup</a></div>
					</div>
				</div>
			</div>
		</div>
		</form>

<?php include_once( "./inc_footer.php" ) ?>