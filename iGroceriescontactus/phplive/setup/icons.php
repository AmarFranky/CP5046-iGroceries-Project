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

	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_File.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/addons/API/Util_Addons.php" ) ;
	if ( is_file( "$CONF[CONF_ROOT]/addons.php" ) ) { include_once( "$CONF[CONF_ROOT]/addons.php" ) ; }

	$deptinfo = Array() ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$option = Util_Format_Sanatize( Util_Format_GetVar( "option" ), "ln" ) ;
	$jump = Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ;
	$error = Util_Format_Sanatize( Util_Format_GetVar( "error" ), "ln" ) ;

	$online = ( isset( $VALS['ONLINE'] ) && $VALS['ONLINE'] ) ? unserialize( $VALS['ONLINE'] ) : Array() ;
	$offline = ( isset( $VALS['OFFLINE'] ) && $VALS['OFFLINE'] ) ? unserialize( $VALS['OFFLINE'] ) : Array() ;

	$svg_icons = ( isset( $VALS_ADDONS ) && isset( $VALS_ADDONS["svg_icons"] ) ) ? unserialize( base64_decode( $VALS_ADDONS["svg_icons"] ) ) : Array() ;
	
	$svg_icons_online = ( isset( $svg_icons[$deptid] ) && isset( $svg_icons[$deptid]["online"] ) && preg_match( "/^<svg /i", $svg_icons[$deptid]["online"][5] ) ) ? $svg_icons[$deptid]["online"] : Array() ;
	$svg_icons_offline = ( isset( $svg_icons[$deptid] ) && isset( $svg_icons[$deptid]["offline"] ) && preg_match( "/^<svg /i", $svg_icons[$deptid]["offline"][5] ) ) ? $svg_icons[$deptid]["offline"] : Array() ;
	
	$text_icons_online = ( isset( $svg_icons[$deptid] ) && isset( $svg_icons[$deptid]["online"] ) && preg_match( "/^<span /i", $svg_icons[$deptid]["online"][5] ) ) ? $svg_icons[$deptid]["online"] : Array() ;
	$text_icons_offline = ( isset( $svg_icons[$deptid] ) && isset( $svg_icons[$deptid]["offline"] ) && preg_match( "/^<span /i", $svg_icons[$deptid]["offline"][5] ) ) ? $svg_icons[$deptid]["offline"] : Array() ;

	if ( $action === "upload" )
	{
		$icon = isset( $_FILES['icon_online']['name'] ) ? "icon_online" : "icon_offline" ;
		LIST( $error, $filename ) = Util_Upload_File( $icon, $deptid ) ;
		if ( isset( $dbh ) && isset( $dbh['con'] ) ) { database_mysql_close( $dbh ) ; }
		HEADER( "location: icons.php?action=success&error=$error" ) ;
		exit ;
	}
	else if ( $action === "update_offline" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
		$url = Util_Format_Sanatize( Util_Format_GetVar( "url" ), "url" ) ;

		$departments = Depts_get_AllDepts( $dbh ) ;
		$dept_groups = Depts_get_AllDeptGroups( $dbh ) ;

		$dept_hash = Array() ;
		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			$dept_hash[$department["deptID"]] = 1 ; 
		}
		for ( $c = 0; $c < count( $dept_groups ); ++$c )
		{
			$department = $dept_groups[$c] ;
			$dept_hash[$department["groupID"]] = 1 ; 
		}

		foreach ( $offline as $key => $value )
		{
			if ( $key && !isset( $dept_hash[$key] ) )
				unset( $offline[$key] ) ;
		}
		$offline[$deptid] = ( $option == "redirect" ) ? $url : $option ;
		Util_Vals_WriteToFile( "OFFLINE", serialize( $offline ) ) ;
		$jump = "settings" ;
	}
	else if ( $action === "update_online" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

		$departments = Depts_get_AllDepts( $dbh ) ;
		$dept_groups = Depts_get_AllDeptGroups( $dbh ) ;

		$dept_hash = Array() ;
		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			$dept_hash[$department["deptID"]] = 1 ; 
		}
		for ( $c = 0; $c < count( $dept_groups ); ++$c )
		{
			$department = $dept_groups[$c] ;
			$dept_hash[$department["groupID"]] = 1 ; 
		}

		foreach ( $online as $key => $value )
		{
			if ( $key && !isset( $dept_hash[$key] ) )
				unset( $online[$key] ) ;
		}
		$online[$deptid] = ( $option == "redirect" ) ? $url : $option ;
		Util_Vals_WriteToFile( "ONLINE", serialize( $online ) ) ;
		$jump = "settings" ;
	}
	else if ( $action === "reset" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

		if ( $option == "online" )
		{
			foreach ( $online as $key => $value )
			{
				if ( $key && ( $key == $deptid ) )
					unset( $online[$key] ) ;
			} Util_Vals_WriteToFile( "ONLINE", serialize( $online ) ) ;
		}
		else if ( $option == "offline" )
		{
			foreach ( $offline as $key => $value )
			{
				if ( $key && ( $key == $deptid ) )
					unset( $offline[$key] ) ;
			} Util_Vals_WriteToFile( "OFFLINE", serialize( $offline ) ) ;
		}
		else if ( ( $option == "icon_online" ) || ( $option == "icon_offline" ) )
		{
			if ( $deptid )
			{
				$dir_files = glob( $CONF["CONF_ROOT"]."/$option"."_$deptid.*", GLOB_NOSORT ) ;
				$total_dir_files = count( $dir_files ) ;
				if ( $total_dir_files )
				{
					for ( $c = 0; $c < $total_dir_files; ++$c )
					{
						if ( $dir_files[$c] && is_file( $dir_files[$c] ) ) { @unlink( $dir_files[$c] ) ; }
					}
				}
			}
		}
	}

	if ( !isset( $departments ) ) { $departments = Depts_get_AllDepts( $dbh ) ; }
	if ( $deptid ) { $deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ; }

	$online_option = "embed" ;
	if ( isset( $online[$deptid] ) ) { $online_option = $online[$deptid] ; }
	else
	{
		if ( isset( $online[0] ) ) { $online_option = $online[0] ; }
	}

	$offline_option = "embed" ; $redirect_url = "" ;
	if ( isset( $offline[$deptid] ) )
	{
		if ( !preg_match( "/^(icon|hide|embed|tab)$/", $offline[$deptid] ) ) { $offline_option = "redirect" ; $redirect_url = $offline[$deptid] ; }
		else{ $offline_option = $offline[$deptid] ; }
	}
	else
	{
		if ( isset( $offline[0] ) )
		{
			if ( !preg_match( "/^(icon|hide|embed|tab)$/", $offline[0] ) ) { $offline_option = "redirect" ; $redirect_url = $offline[0] ; }
			else{ $offline_option = $offline[0] ; }
		}
	}
	$mobile_newwin = ( isset( $VALS["MOBILE_NEWWIN"] ) && is_numeric( $VALS["MOBILE_NEWWIN"] ) ) ? intval( $VALS["MOBILE_NEWWIN"] ) : 0 ;
	if ( !isset( $dept_groups ) ) { $dept_groups = Depts_get_AllDeptGroups( $dbh ) ; }
	$dept_groups_hash = Array() ;
	for ( $c = 0; $c < count( $dept_groups ); ++$c )
	{
		$dept_group = $dept_groups[$c] ;
		$dept_groups_hash[$dept_group["groupID"]] = $dept_group["name"] ;
	}

	$global_default_online = Util_Upload_GetChatIcon( "icon_online", 0 ) ;
	$global_default_offline = Util_Upload_GetChatIcon( "icon_offline", 0 ) ;

	$online_image = $global_default_online ;
	$offline_image = $global_default_offline ;
	if ( $deptid )
	{
		$online_image = Util_Upload_GetChatIcon( "icon_online", $deptid ) ;
		$offline_image = Util_Upload_GetChatIcon( "icon_offline", $deptid ) ;
	}

	$alttext_array = ( isset( $VALS["alttext"] ) ) ? unserialize( $VALS["alttext"] ) : Array() ;
	$alttext_array_dept = Array() ; $alttext_using_global = 0 ;
	if ( isset( $alttext_array[$deptid] ) )
		$alttext_array_dept = $alttext_array[$deptid] ;
	else if ( $deptid && isset( $alttext_array[0] ) )
	{
		$alttext_using_global = 1 ;
		$alttext_array_dept = $alttext_array[0] ;
	} array_walk( $alttext_array_dept, "base64_decode_array" ) ;
	function base64_decode_array( &$value, $index )
	{
		$value = base64_decode( $value ) ;
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
<script data-cfasync="false" type="text/javascript" src="../addons/svg/js/spectrum.js?<?php echo filemtime ( "../addons/svg/js/spectrum.js" ) ; ?>"></script>
<script data-cfasync="false" type="text/javascript" src="../addons/svg/js/svg.js?<?php echo filemtime ( "../addons/svg/js/svg.js" ) ; ?>"></script>
<link rel="Stylesheet" href="../addons/svg/css/spectrum.css?<?php echo filemtime ( "../addons/svg/css/spectrum.css" ) ; ?>">

<script data-cfasync="false" type="text/javascript">
<!--
	"use strict" ;
	var deptid = <?php echo $deptid ?> ;
	var global_mobilenewwin = <?php echo $mobile_newwin ?> ;
	var global_jump = "<?php echo $jump ?>" ;

	$(document).ready(function()
	{
		$("html").css({'background': '#272727'}) ; $("body").css({'background': '#D6E4F2'}) ;
		init_menu() ;
		toggle_menu_setup( "icons" ) ;

		show_div('<?php echo $jump ?>') ;

		<?php if ( $action === "ob" ): ?>
			toggle_ob() ;
		<?php elseif ( $action && !$error ): ?>
			do_alert( 1, "Success" ) ;
			if ( "<?php echo $action ?>" == "update_ob_clean" ) { toggle_ob() ; }
		<?php endif ; ?>

		<?php if ( $action && $error ): ?>do_alert( 0, "<?php echo $error ?>" ) ;<?php endif ; ?>
		<?php if ( $deptid ): ?>$('#div_notice_html').show() ;<?php endif ; ?>
		if ( [].filter ) { svg_init() ; }

		<?php if ( ( $action != "upload" ) ): ?>
			<?php if ( isset( $svg_icons_online[0] ) ): ?>
				toggle_type('svg', 'online') ;
				toggle_status('svg', 'online') ;
			<?php elseif ( isset( $text_icons_online[0] ) ): ?>
				toggle_type('text', 'online') ;
				toggle_status('text', 'online') ;
			<?php else: ?>
				toggle_type('image', 'online') ;
				toggle_status('image', 'online') ;
			<?php endif ; ?>

			<?php if ( isset( $svg_icons_offline[0] ) ): ?>
				toggle_type('svg', 'offline') ;
				toggle_status('svg', 'offline') ;
			<?php elseif ( isset( $text_icons_offline[0] ) ): ?>
				toggle_type('text', 'offline') ;
				toggle_status('text', 'offline') ;
			<?php else: ?>
				toggle_type('image', 'offline') ;
				toggle_status('image', 'offline') ;
			<?php endif ; ?>
		<?php endif ; ?>

		<?php if ( !isset( $text_icons_online[0] ) && !isset( $svg_icons_online[0] ) ): ?>
			$('#image_online_status_image').prop( "checked", true ) ;
		<?php elseif ( isset( $text_icons_online[0] ) ): ?>
			$('#image_online_status_text').prop( "checked", true ) ;
		<?php elseif ( isset( $svg_icons_online[0] ) ): ?>
			$('#image_online_status_svg').prop( "checked", true ) ;
		<?php endif ; ?>

		<?php if ( !isset( $text_icons_offline[0] ) && !isset( $svg_icons_offline[0] ) ): ?>
			$('#image_offline_status_image').prop( "checked", true ) ;
		<?php elseif ( isset( $text_icons_offline[0] ) ): ?>
			$('#image_offline_status_text').prop( "checked", true ) ;
		<?php elseif ( isset( $svg_icons_offline[0] ) ): ?>
			$('#image_offline_status_svg').prop( "checked", true ) ;
		<?php endif ; ?>

		<?php if ( $jump == "settings" ): ?>
		show_div_behavior( 'online', 'options' ) ; show_div_behavior( 'offline', 'options' ) ;
		<?php else: ?>
		show_div_behavior( 'online', 'icon' ) ; show_div_behavior( 'offline', 'icon' ) ;
		<?php endif ; ?>

	});

	function switch_dept( theobject )
	{
		location.href = "icons.php?deptid="+theobject.value+"&jump="+global_jump+"&"+unixtime() ;
	}

	function switch_dept_alttext( theobject )
	{
		location.href = "icons.php?deptid="+theobject.value+"&jump=alttext&"+unixtime() ;
	}

	function show_div( thediv )
	{
		$('#div_alert').hide() ;

		// for situations the department is changed when viewing settings
		if ( thediv == "settings" ) { thediv = "chaticons" ; }
		if ( thediv )
		{
			var divs = Array( "chaticons", "alttext", "iconsettings" ) ;
			for ( var c = 0; c < divs.length; ++c )
			{
				$('#div_'+divs[c]).hide() ;
				$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
			}

			$('#div_'+thediv).show() ;
			$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
		}
	}

	function show_div_behavior( theicon, thediv )
	{
		$('#div_alert').hide() ;

		var divs = Array( "icon", "options" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#'+theicon+'_'+divs[c]).hide() ;
			$('#menu_'+theicon+'_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('#'+theicon+'_'+thediv).show() ;
		$('#menu_'+theicon+'_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;

		if ( thediv == "options" )
		{
			global_jump = "settings" ;
			$('#'+theicon+'_svg_radio').hide() ;
			$('#div_'+theicon+'_icon_image').hide() ;
			$('#div_'+theicon+'_icon_svg').hide() ;
			$('#div_'+theicon+'_icon_text').hide() ;
		}
		else
		{
			global_jump = "" ;
			$('#'+theicon+'_svg_radio').show() ;

			if ( $('#'+theicon+'_icon_type_image').is(':checked') )
				toggle_type( 'image', theicon ) ;
			else if ( $('#'+theicon+'_icon_type_svg').is(':checked') )
				toggle_type( 'svg', theicon ) ;
			else
				toggle_type( 'text', theicon ) ;
		}
	}

	function check_url()
	{
		var url = $('#offline_url').val().trim() ;
		var url_ok = ( url.match( /(http:\/\/)|(https:\/\/)/i ) ) ? 1 : 0 ;

		if ( !url )
			return "Please provide the webpage URL." ;
		else if ( !url_ok )
			return "URL should begin with http:// or https:// protocol." ;
		else
			return false ;
	}

	function open_url()
	{
		var unique = unixtime() ;
		var url = $('#offline_url').val().trim() ;
		var error = check_url() ;

		if ( error )
			do_alert( 0, error ) ;
		else
			window.open(url, unique, 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1') ;
	}

	function update_online()
	{
		var unique = unixtime() ;
		var option = $("input[name='online_option']:checked").val() ;

		location.href = "./icons.php?action=update_online&deptid=<?php echo $deptid ?>&option="+option+"&"+unique ;
	}

	function update_offline()
	{
		var unique = unixtime() ;
		var option = $("input[name='offline_option']:checked").val() ;
		var error = check_url() ;

		if ( error && ( option == "redirect" ) )
			do_alert( 0, error ) ;
		else
		{
			var url = encodeURIComponent( $('#offline_url').val().trim().replace( /http/ig, "hphp" ) ) ;
			location.href = "./icons.php?action=update_offline&deptid=<?php echo $deptid ?>&option="+option+"&url="+url+"&"+unique ;
		}
	}

	function reset_doit( theicon, thedeptid )
	{
		if ( confirm( "Reset to Global Default settings?" ) )
			location.href = "./icons.php?action=reset&jump=settings&option="+theicon+"&deptid="+thedeptid ;
	}

	function reset_icon( theicon, thedeptid )
	{
		if ( confirm( "Reset to Global Default icon?" ) )
			location.href = "./icons.php?action=reset&option="+theicon+"&deptid="+thedeptid ;
	}

	function confirm_change( theflag )
	{
		if ( global_mobilenewwin != theflag )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "action=update_mobile_newwin&value="+theflag+"&"+unixtime(),
				success: function(data){
					global_mobilenewwin = theflag ;
					do_alert( 1, "Success" ) ;
				}
			});
		}
	}

	function toggle_type( thetype, theicon )
	{
		if ( !$('#'+theicon+'_icon_type_'+thetype).prop('checked') )
			$('#'+theicon+'_icon_type_'+thetype).prop('checked', true) ;

		if ( thetype == "image" )
		{
			$('#div_'+theicon+'_icon_svg').hide() ;
			$('#div_'+theicon+'_icon_text').hide() ;
			$('#div_'+theicon+'_icon_image').show() ;
		}
		else if ( thetype == "svg" )
		{
			if ( [].filter )
			{
				$('#div_'+theicon+'_icon_image').hide() ;
				$('#div_'+theicon+'_icon_text').hide() ;
				$('#div_'+theicon+'_icon_svg').show() ;
			}
			else
			{
				do_alert( 0, "This browser does not support SVG images.  Please use a modern browser." ) ;
			}
		}
		else
		{
			$('#div_'+theicon+'_icon_image').hide() ;
			$('#div_'+theicon+'_icon_svg').hide() ;
			$('#div_'+theicon+'_icon_text').show() ;
		}
	}

	function toggle_status( thetype, theicon )
	{
		$('#svg_'+theicon+'_status_'+thetype).prop('checked', true) ;
		$('#text_'+theicon+'_status_'+thetype).prop('checked', true) ;
	}

	function update_alttext( thereset )
	{
		var json_data = new Object ;
		var unique = unixtime( ) ;

		var alt_query = "" ;
		if ( !thereset )
		{
			$("#table_alttext").find('*').each( function(){
				var div_name = this.id ;
				if ( div_name.indexOf("alt_") == 0 )
					alt_query += div_name+"="+encodeURIComponent( $(this).val().trim() )+"&" ;
			} );
		}
		else
		{
			if ( !confirm( "Reset to Global Default values?" ) )
				return false ;
			alt_query = "reset=1" ;
		}

		$('#btn_alttext').attr( "disabled", true ) ;

		$.ajax({
		type: "POST",
		url: "../ajax/setup_actions_.php",
		data: "action=update_alttext&deptid=<?php echo $deptid ?>&"+alt_query+"&"+unique,
		success: function(data){
			eval( data ) ;
			$('#btn_alttext').attr( "disabled", false ) ;

			if ( json_data.status )
			{
				location.href = "icons.php?action=success&deptid=<?php echo $deptid ?>&jump=alttext" ;
			}
			else
				do_alert( 0, json_data.error ) ;

		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Connection error.  Refresh the page and try again." ) ;
		} });
	}

	function display_text(e, theicon )
	{
		if ( noquotes(e) )
		{
			var text = $('#input_text_'+theicon).val().trim() ;
			$('#span_text_text_'+theicon).html( text ) ;

			if ( !$('#span_'+theicon+'_text_cancel').is(":visible") )
				$('#span_'+theicon+'_text_cancel').show() ;
		}
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu_focus" style="margin-left: 0px;" onClick="show_div('chaticons')" id="menu_chaticons">Chat Icons</div>
			<div class="op_submenu" onClick="show_div('alttext')" id="menu_alttext">Alt Text</div>
			<div class="op_submenu" onClick="show_div('iconsettings')" id="menu_iconsettings">Mobile Behavior</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;" id="div_chaticons">
			<div>
				<?php if ( count( $departments ) > 1 ): ?>
				<form method="POST" action="" id="form_theform">
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
				</form>
				<?php else: ?>
				&nbsp;
				<?php endif ; ?>
			</div>
			<div id="div_notice_html" style="display: none; margin-top: 25px;"><span class="info_warning">The chat icon for this department will display when utilizing the <a href="code.php?deptid=<?php echo $deptid ?>">Department Specific HTML Code</a>.</span></div>

			<div id="div_alert" style="display: none; margin-top: 25px;"></div>

			<div style="margin-top: 25px;">
			
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td>
						<div style="padding-bottom: 15px;">
							<div class="op_submenu" onClick="show_div_behavior('online', 'icon')" id="menu_online_icon">Online Icon</div>
							<div class="op_submenu" onClick="show_div_behavior('online', 'options')" id="menu_online_options">Behavior</div>
							<div style="clear: both"></div>
						</div>
					</td>
					<td>
						<div style="padding-bottom: 15px;">
							<div class="op_submenu" onClick="show_div_behavior('offline', 'icon')" id="menu_offline_icon">Offline Icon</div>
							<div class="op_submenu" onClick="show_div_behavior('offline', 'options')" id="menu_offline_options">Behavior</div>
							<div style="clear: both"></div>
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%" style="padding-right: 10px;">
						<form method="POST" action="icons.php" enctype="multipart/form-data" id="form_online" name="form_online">
						<input type="hidden" name="action" value="upload">
						<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
						<input type="hidden" name="MAX_FILE_SIZE" value="200000">
						<div class="edit_title">
							<?php
								if ( isset( $deptinfo["name"] ) )
									print $deptinfo["name"] ;
								else if ( isset( $dept_groups_hash[$deptid] ) )
									print $dept_groups_hash[$deptid] ;
								else
									print "Global Default" ;
							?>
							<span class="info_good">ONLINE</span> Chat Icon
						</div>
						<div id="online_svg_radio" style="margin-top: 25px;">
							<span class="info_misc" style="cursor: pointer;" onclick="toggle_type('image', 'online')"><input type="radio" id="online_icon_type_image" name="online_icon_type" value="image" checked> Image</span>
							<span class="info_misc" style="margin-left: 5px; cursor: pointer;" onclick="toggle_type('svg', 'online')"><input type="radio" id="online_icon_type_svg" name="online_icon_type" value="svg" > SVG</span>
							<span class="info_misc" style="margin-left: 5px; cursor: pointer;" onclick="toggle_type('text', 'online')"><input type="radio" id="online_icon_type_text" name="online_icon_type" value="text" > Text</span>
						</div>
						<div id="div_online_icon_image" style="display: none; margin-top: 35px;">
							<div style="text-shadow: none;">
								<div class="info_good" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('online', 'image', 1)"><input type="radio" name="image_online_status" id="image_online_status_image" value="1"> Enable.  Use Image</div>
								<div class="info_neutral" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('online', 'svg', 1)"><input type="radio" name="image_online_status" id="image_online_status_svg" value="0"> Disable.  Use SVG</div>
								<div class="info_neutral" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('online', 'text', 1)"><input type="radio" name="image_online_status" id="image_online_status_text" value="0"> Disable.  Use Text</div>
								<div style="clear: both;"></div>
							</div>
							<div style="margin-top: 15px;">
								<div><input type="file" name="icon_online" size="30"></div>
								<div style="margin-top: 5px;"><input type="submit" value="Upload Image" style="margin-top: 10px;" class="btn"></div>
							</div>

							<div style="margin-top: 15px;"><img src="<?php print $online_image ?>" border="0" alt=""></div>
							<?php if ( $deptid && preg_match( "/_$deptid\.[a-z]/i", $online_image ) ): ?>
							<div style="margin-top: 15px;">&bull; reset to use <a href="JavaScript:void(0)" onClick="reset_icon( 'icon_online', <?php echo $deptid ?> )">Global Default</a></div>
							<?php endif ; ?>

							<div style="margin-top: 45px;"><img src="../pics/icons/chats_active.png" width="16" height="16" border="0" alt=""> Download different chat icons at the <a href="https://www.phplivesupport.com/r.php?r=icons" target="_blank">chat icons download page</a>.</div>
						</div>
						<?php $icon_svg = "online" ; include( "../addons/svg/inc_icons_svg.php" ) ; ?>
						<?php $icon_text = "online" ; include( "./inc_icons_text.php" ) ; ?>
						<div id="online_options" style="display: none; margin-top: 25px; line-height: 160%;" class="info_info">
							<table cellspacing=1 cellpadding=5 border=0 width="100%">
							<tr>
								<td colspan=2><div style="font-size: 14px; font-weight: bold;">When the <span class="info_good">ONLINE</span> chat icon is clicked:</div></td>
							</tr>
							<tr>
								<td width="25" align="center"><input type="radio" name="online_option" value="popup" <?php echo ( $online_option == "popup" ) ? "checked" : "" ; ?>></td>
								<td>Open the chat request window in a new <span class="info_blue_dark" style="padding: 2px;">popup</span> window when the online icon is clicked.</td>
							</tr>
							<tr>
								<td width="25" align="center"><input type="radio" name="online_option" value="tab" <?php echo ( $online_option == "tab" ) ? "checked" : "" ; ?>></td>
								<td>Open the chat request window in a new <span class="info_blue_dark" style="padding: 2px;">tabbed</span> window when the online icon is clicked.</td>
							</tr>
							<tr>
								<td width="25" align="center"><input type="radio" name="online_option" value="embed" <?php echo ( $online_option == "embed" ) ? "checked" : "" ; ?>></td>
								<td>Open the chat request as an <span class="info_blue_dark" style="padding: 2px;">embed</span> window on the webpage when the online icon is clicked.</td>
							</tr>
							<tr>
								<td></td>
								<td><div style="padding-top: 5px;"><button type="button" onClick="update_online()" class="btn">Update</button>
								&nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="$('#form_online').get(0).reset(); show_div_behavior('online', 'icon');">cancel</a> &nbsp; 
								<?php
									if ( $deptid && !isset( $online[$deptid] ) ):
										print " &bull; currently using Global Default settings" ;
									elseif ( $deptid ):
										print " &bull; reset to use <a href=\"JavaScript:void(0)\" onClick=\"reset_doit( 'online', $deptid )\">Global Default</a>" ;
									endif ;
								?>
								</div></td>
							</tr>
							</table>
						</div>
						</form>
					</td>
					<td valign="top" width="50%" style="padding-left: 10px;">
						<form method="POST" action="icons.php" enctype="multipart/form-data" id="form_offline" name="form_offline">
						<input type="hidden" name="action" value="upload">
						<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
						<input type="hidden" name="MAX_FILE_SIZE" value="200000">
						<div class="edit_title">
							<?php
								if ( isset( $deptinfo["name"] ) )
									print $deptinfo["name"] ;
								else if ( isset( $dept_groups_hash[$deptid] ) )
									print $dept_groups_hash[$deptid] ;
								else
									print "Global Default" ;
							?>
							<span class="info_error">OFFLINE</span> Chat Icon
						</div>
						<div id="offline_svg_radio" style="margin-top: 25px;">
							<span class="info_misc" style="cursor: pointer;" onclick="toggle_type('image', 'offline')"><input type="radio" id="offline_icon_type_image" name="offline_icon_type" value="image" checked> Image</span>
							<span class="info_misc" style="margin-left: 5px; cursor: pointer;" onclick="toggle_type('svg', 'offline')"><input type="radio" id="offline_icon_type_svg" name="offline_icon_type" value="svg" > SVG</span>
							<span class="info_misc" style="margin-left: 5px; cursor: pointer;" onclick="toggle_type('text', 'offline')"><input type="radio" id="offline_icon_type_text" name="offline_icon_type" value="text" > Text</span>
						</div>
						<div id="div_offline_icon_image" style="display: none; margin-top: 35px;">
							<div style="text-shadow: none;">
								<div class="info_good" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('offline', 'image', 1)"><input type="radio" name="image_offline_status" id="image_offline_status_image" value="1"> Enable.  Use Image</div>
								<div class="info_neutral" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('offline', 'svg', 1)"><input type="radio" name="image_offline_status" id="image_offline_status_svg" value="0"> Disable.  Use SVG</div>
								<div class="info_neutral" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('offline', 'text', 1)"><input type="radio" name="image_offline_status" id="image_offline_status_text" value="0"> Disable.  Use Text</div>
								<div style="clear: both;"></div>
							</div>
							<div style="margin-top: 15px;">
								<div><input type="file" name="icon_offline" size="30"></div>
								<div style="margin-top: 5px;"><input type="submit" value="Upload Image" style="margin-top: 10px;" class="btn"></div>
							</div>

							<div style="margin-top: 15px;"><img src="<?php print $offline_image ?>" border="0" alt=""></div>
							<?php if ( $deptid && preg_match( "/_$deptid\.[a-z]/i", $offline_image ) ): ?>
							<div style="margin-top: 15px;">&bull; reset to use <a href="JavaScript:void(0)" onClick="reset_icon( 'icon_offline', <?php echo $deptid ?> )">Global Default</a></div>
							<?php endif ; ?>

							<div style="margin-top: 45px;"><img src="../pics/icons/chats_active.png" width="16" height="16" border="0" alt=""> Download different chat icons at the <a href="https://www.phplivesupport.com/r.php?r=icons" target="_blank">chat icons download page</a>.</div>
						</div>
						<?php $icon_svg = "offline" ; include( "../addons/svg/inc_icons_svg.php" ) ; ?>
						<?php $icon_text = "offline" ; include( "./inc_icons_text.php" ) ; ?>
						<div id="offline_options" style="display: none; margin-top: 25px; line-height: 160%;" class="info_info">
							<table cellspacing=1 cellpadding=5 border=0 width="100%">
							<tr>
								<td colspan=2><div style="font-size: 14px; font-weight: bold;">When the <span class="info_error">OFFLINE</span> chat icon is clicked:</div></td>
							</tr>
							<tr>
								<td width="25" align="center"><input type="radio" name="offline_option" value="icon" <?php echo ( $offline_option == "icon" ) ? "checked" : "" ; ?>></td>
								<td>Display the offline chat icon and open the leave a message window in a new <span class="info_blue_dark" style="padding: 2px;">popup</span> window when the offline icon is clicked.</td>
							</tr>
							<tr>
								<td width="25" align="center"><input type="radio" name="offline_option" value="tab" <?php echo ( $offline_option == "tab" ) ? "checked" : "" ; ?>></td>
								<td>Display the offline chat icon and open the leave a message window in a new <span class="info_blue_dark" style="padding: 2px;">tabbed</span> window when the offline icon is clicked.</td>
							</tr>
							<tr>
								<td width="25" align="center"><input type="radio" name="offline_option" value="embed" <?php echo ( $offline_option == "embed" ) ? "checked" : "" ; ?>></td>
								<td>Display the offline chat icon and <span class="info_blue_dark" style="padding: 2px;">embed</span> the leave a message window on the webpage when the offline icon is clicked.</td>
							</tr>
							<tr>
								<td width="25" align="center"><input type="radio" name="offline_option" id="option_redirect" value="redirect" <?php echo ( $offline_option == "redirect" ) ? "checked" : "" ; ?> onClick="$('#offline_url').focus()"></td>
								<td>
									Display the offline chat icon and redirect the visitor to a webpage when the offline icon is clicked. Provide the redirect URL below:
									<div style="margin-top: 5px;">
										<input type="text" class="input" style="width: 80%;" maxlength="255" name="offline_url" id="offline_url" value="<?php echo $redirect_url ?>" onFocus="$('#option_redirect').prop('checked', true)" onKeyPress="return noquotestags(event)"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="open_url()">visit</a></span>
									</div>
								</td>
							</tr>
							<tr>
								<td width="25" align="center"><input type="radio" name="offline_option" value="hide" <?php echo ( $offline_option == "hide" ) ? "checked" : "" ; ?>></td>
								<td>
									Do not display the offline chat icon.
								</td>
							</tr>
							<tr>
								<td></td>
								<td><div style="padding-top: 5px;"><button type="button" onClick="update_offline()" class="btn">Update</button>
								&nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="$('#form_offline').get(0).reset(); show_div_behavior('offline', 'icon');">cancel</a> &nbsp; 
								<?php
									if ( $deptid && !isset( $offline[$deptid] ) ):
										print " &bull; currently using Global Default settings" ;
									elseif ( $deptid ):
										print " &bull; reset to use <a href=\"JavaScript:void(0)\" onClick=\"reset_doit( 'offline', $deptid )\">Global Default</a>" ;
									endif ;
								?>
								</div></td>
							</tr>
							</table>
						</div>
						</form>
					</td>
				</tr>
				</table>

			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="div_alttext">
			Add an alt text to various images (chat icons, chat invite, embed window).  Alt texts are text that is displayed when the image cannot be loaded or when the mouse is over the image.  Alt text will not be displayed for Text chat icon.

			<?php if ( count( $departments ) > 1 ): ?>
			<div style="margin-top: 15px;">
				<form method="POST" action="" id="form_theform">
				Department <select name="deptid_alttext" id="deptid_alttext" style="font-size: 16px;" onChange="switch_dept_alttext( this )">
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
				</form>
			</div>
			<?php endif ; ?>

			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=5 border=0 width="100%" id="table_alttext">
				<tr>
					<td valign="bottom" width="25%">
						<div><span class="info_good">Online</span> Online chat icon</div>
						<div style="margin-top: 5px;"><input type="text" class="input" style="width: 50%" id="alt_online" maxlength="100" value="<?php echo isset( $alttext_array_dept["online"] ) ? $alttext_array_dept["online"] : "" ; ?>" onKeyPress="return noquotestagscomma(event)" autocomplete="off"></div>
					</td>
					<td valign="bottom" width="25%">
						<div><span class="info_error">Offline</span> Offline chat icon</div>
						<div style="margin-top: 5px;"><input type="text" class="input" style="width: 50%" id="alt_offline" maxlength="100" value="<?php echo isset( $alttext_array_dept["offline"] ) ? $alttext_array_dept["offline"] : "" ; ?>" onKeyPress="return noquotestagscomma(event)" autocomplete="off"></div>
					</td>
					<td valign="bottom" width="25%">
						<div><a href="code_invite.php">Automatic Chat Invite</a> image</div>
						<div style="margin-top: 5px;"><input type="text" class="input" style="width: 50%" id="alt_invite" maxlength="100" value="<?php echo isset( $alttext_array_dept["invite"] ) ? $alttext_array_dept["invite"] : "" ; ?>" onKeyPress="return noquotestagscomma(event)" autocomplete="off"></div>
					</td>
					<td valign="bottom" width="25%">
						<div><a href="code_invite.php">Automatic</a> and <a href="../addons/proaction/proaction.php">ProAction</a> invite close <img src="../themes/initiate/close_box.png" width="14" height="14" border="0" alt=""></div>
						<div style="margin-top: 5px;"><input type="text" class="input" style="width: 50%" id="alt_close" maxlength="100" value="<?php echo isset( $alttext_array_dept["close"] ) ? $alttext_array_dept["close"] : "" ; ?>" onKeyPress="return noquotestagscomma(event)" autocomplete="off"></div>
					</td>
				</tr>
				<tr>
					<td valign="bottom" width="25%">
						<div><img src="../themes/whiteout/win_min.png" width="16" height="16" border="0" alt=""> embed chat minimize</div>
						<div style="margin-top: 5px;"><input type="text" class="input" style="width: 50%" id="alt_emminimize" maxlength="100" value="<?php echo isset( $alttext_array_dept["emminimize"] ) ? $alttext_array_dept["emminimize"] : "" ; ?>" onKeyPress="return noquotestagscomma(event)" autocomplete="off"></div>
					</td>
					<td valign="bottom" width="25%">
						<div><img src="../themes/whiteout/win_max.png" width="16" height="16" border="0" alt=""> embed chat maximize</div>
						<div style="margin-top: 5px;"><input type="text" class="input" style="width: 50%" id="alt_emmaximize" maxlength="100" value="<?php echo isset( $alttext_array_dept["emmaximize"] ) ? $alttext_array_dept["emmaximize"] : "" ; ?>" onKeyPress="return noquotestagscomma(event)" autocomplete="off"></div>
					</td>
					<td valign="bottom" width="25%">
						<div><img src="../themes/whiteout/win_pop.png" width="16" height="16" border="0" alt=""> embed chat popout</div>
						<div style="margin-top: 5px;"><input type="text" class="input" style="width: 50%" id="alt_empopout" maxlength="100" value="<?php echo isset( $alttext_array_dept["empopout"] ) ? $alttext_array_dept["empopout"] : "" ; ?>" onKeyPress="return noquotestagscomma(event)" autocomplete="off"></div>
					</td>
					<td valign="bottom" width="25%">
						<div><img src="../themes/whiteout/win_close.png" width="16" height="16" border="0" alt=""> embed chat close</div>
						<div style="margin-top: 5px;"><input type="text" class="input" style="width: 50%" id="alt_emclose" maxlength="100" value="<?php echo isset( $alttext_array_dept["emclose"] ) ? $alttext_array_dept["emclose"] : "" ; ?>" onKeyPress="return noquotestagscomma(event)" autocomplete="off"></div>
					</td>
				</tr>
				</table>
			</div>

			<div style="padding-top: 25px;">
				<button type="button" onClick="update_alttext(0)" class="btn" id="btn_alttext">Update</button> &nbsp;
				<?php if ( $deptid && $alttext_using_global ): ?>
					&bull; currently using <a href="icons.php?jump=alttext">Global Default</a> values
				<?php elseif ( $deptid ): ?>
					&bull; reset to use <a href="JavaScript:void(0)" onClick="update_alttext(1)">Global Default</a> values
				<?php endif ; ?>
			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="div_iconsettings">
			<b>Mobile behavior:</b> This setting overrides the <a href="JavaScript:void(0)" onClick="show_div_behavior( 'online', 'options' ) ; show_div_behavior( 'offline', 'options' ) ; show_div('chaticons');">Online/Offline behavior</a>.
			<div style="margin-top: 15px;"><img src="../pics/icons/alert.png" width="16" height="16" border="0" alt=""> If the webpage does not contain a <a href="https://www.phplivesupport.com/r.php?r=viewport" target="_blank">viewport</a>, the chat window will automatically open in a new window on mobile devices.  The automatic behavior is to ensure the chat window is displayed correctly based on the mobile device screen size.</div>

			<div style="margin-top: 25px;">
				<div class="info_neutral" style="cursor: pointer;" onclick="$('#mobile_newwin_0').prop('checked', true);confirm_change(0);"><input type="radio" name="mobile_newwin" id="mobile_newwin_0" value="0" <?php echo ( $mobile_newwin === 0 ) ? "checked" : "" ; ?>> Use Online/Offline behavior</div>
				<div class="info_neutral" style="margin-top: 15px; cursor: pointer;" onclick="$('#mobile_newwin_1').prop('checked', true);confirm_change(1);"><input type="radio" name="mobile_newwin" id="mobile_newwin_1" value="1" <?php echo ( $mobile_newwin === 1 ) ? "checked" : "" ; ?>> Always open the chat request in a new window for mobile visitors (not including iPad visitors).</div>
				<div class="info_neutral" style="margin-top: 15px; cursor: pointer;" onclick="$('#mobile_newwin_2').prop('checked', true);confirm_change(2);"><input type="radio" name="mobile_newwin" id="mobile_newwin_2" value="2" <?php echo ( $mobile_newwin === 2 ) ? "checked" : "" ; ?>> Always open the chat request in a new window for mobile visitors (including iPad visitors).</div>
			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ?>