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

	$vars_rtype = Array( 1=>"Defined Order", 2=>"Round-robin", 3=>"Simultaneous" ) ;

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$ao = Util_Format_Sanatize( Util_Format_GetVar( "ao" ), "n" ) ;
	$ftab = Util_Format_Sanatize( Util_Format_GetVar( "ftab" ), "ln" ) ;
	$dept_themes = ( isset( $VALS["THEMES"] ) && $VALS["THEMES"] ) ? unserialize( $VALS["THEMES"] ) : Array() ;
	$error = "" ;

	if ( $action === "submit" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/put.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
		$name = Util_Format_ConvertQuotes( Util_Format_Sanatize( Util_Format_GetVar( "name" ), "ln" ) ) ;
		$email = Util_Format_Sanatize( Util_Format_GetVar( "email" ), "e" ) ;
		$visible = Util_Format_Sanatize( Util_Format_GetVar( "visible" ), "n" ) ;
		$rtype = Util_Format_Sanatize( Util_Format_GetVar( "rtype" ), "n" ) ;
		$rtime = Util_Format_Sanatize( Util_Format_GetVar( "rtime" ), "n" ) ;
		$rloop = Util_Format_Sanatize( Util_Format_GetVar( "rloop" ), "n" ) ;
		$vupload = Util_Format_Sanatize( Util_Format_GetVar( "vupload" ), "a" ) ;
		$ctimer = Util_Format_Sanatize( Util_Format_GetVar( "ctimer" ), "n" ) ;
		$smtp_md5 = Util_Format_Sanatize( Util_Format_GetVar( "smtp" ), "ln" ) ;
		$tshare = Util_Format_Sanatize( Util_Format_GetVar( "tshare" ), "n" ) ;
		$traffic = Util_Format_Sanatize( Util_Format_GetVar( "traffic" ), "n" ) ;
		$texpire = Util_Format_Sanatize( Util_Format_GetVar( "texpire" ), "n" ) ;
		$lang = Util_Format_Sanatize( Util_Format_GetVar( "lang" ), "ln" ) ;
		$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;

		if ( !is_file( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) )
			$lang = "english" ;
		include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;

		$department_pre = Depts_get_DeptInfoByName( $dbh, $name ) ;
		if ( ( isset( $department_pre["deptID"] ) && !$deptid ) || ( isset( $department_pre["deptID"] ) && ( $department_pre["deptID"] != $deptid ) ) ) { $error = "$name name is already in use." ; }
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

			$smtp_temp = Depts_get_SMTPByMd5( $dbh, $smtp_md5 ) ;
			$smtp = ( $smtp_temp ) ? $smtp_temp : "" ;

			if ( $name != "Archive" )
			{
				$queue = 0 ;
				if ( isset( $department_pre["deptID"] ) )
					$queue = $department_pre["queue"] ;

				$vupload_val = "" ;
				if ( !count( $vupload ) ) { $vupload_val = "0," ; }
				else
				{
					for ( $c = 0; $c < count( $vupload ); ++$c )
					{
						if ( $vupload[$c] == 1 ) { $vupload_val = "1," ; break ; }
						$vupload_val .= $vupload[$c]."," ;
					}
				} if ( $vupload_val ) { $vupload_val = substr_replace( $vupload_val, "", -1 ) ; }
				if ( !$deptid = Depts_put_Department( $dbh, $deptid, $name, $email, $visible, $queue, $rtype, $rtime, $rloop, 6, strtoupper( $vupload_val ), $ctimer, $smtp, $tshare, $texpire, $lang ) ) { $error = "DB Error: $dbh[error]" ; }
			}
			
			if ( !$error )
			{
				$departments = Depts_get_AllDepts( $dbh ) ;
				if ( count( $departments ) == 1 )
				{
					if ( !isset( $CONF["lang"] ) || ( isset( $CONF["lang"] ) && ( $CONF["lang"] != $lang ) ) ) { $error = ( Util_Vals_WriteToConfFile( "lang", $lang ) ) ? "" : "Could not write to config file. [e1]" ; }
					if ( !$error && ( !isset( $CONF["THEME"] ) || ( isset( $CONF["THEME"] ) && ( $CONF["THEME"] != $theme ) ) ) ) { $error = ( Util_Vals_WriteToConfFile( "THEME", $theme ) ) ? "" : "Could not write to vals file. [e1]" ; }
				}
				if ( $theme )
				{
					$update_vals = 0 ;
					if ( ( $deptid && isset( $dept_themes[$deptid] ) && ( $dept_themes[$deptid] == $theme ) ) || ( isset( $CONF["THEME"] ) && ( $CONF["THEME"] == $theme ) ) ) {
						if ( isset( $dept_themes[$deptid] ) ) { unset( $dept_themes[$deptid] ) ; $update_vals = 1 ; }
					}
					else { $dept_themes[$deptid] = $theme ; }
					if ( count( $dept_themes ) || $update_vals ) { $error = ( Util_Vals_WriteToFile( "THEMES", serialize( $dept_themes ) ) ) ? "" : "Could not write to vals file. [e2]" ; }
				}

				if ( !$error )
				{
					if ( isset( $dbh ) && $dbh['con'] ) { database_mysql_close( $dbh ) ; }
					HEADER( "location: depts.php?action=success" ) ;
					exit ;
				}
			}
		}
	}
	else if ( $action === "delete" )
	{
		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;

		if ( $deptid && !Depts_get_IsDeptInGroup( $dbh, $deptid ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/remove.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

			Depts_remove_Dept( $dbh, $deptid ) ;

			$update_vals = 0 ;
			if ( isset( $dept_themes[$deptid] ) ) { unset( $dept_themes[$deptid] ) ; $update_vals = 1 ; }
			if ( count( $dept_themes ) || $update_vals ) { $error = ( Util_Vals_WriteToFile( "THEMES", serialize( $dept_themes ) ) ) ? "" : "Could not write to vals file. [e3]" ; }

			$departments = Depts_get_AllDepts( $dbh ) ;
			if ( count( $departments ) == 1 )
			{
				$department = $departments[0] ;
				if ( isset( $department["lang"] ) && $department["lang"] && ( $CONF["lang"] != $department["lang"] ) )
				{
					$lang = $department["lang"] ;
					$error = ( Util_Vals_WriteToConfFile( "lang", $lang ) ) ? "" : "Could not write to config file. [e2]" ;
					$CONF["lang"] = $lang ;
				}
				if ( isset( $dept_themes[$department["deptID"]] ) && ( $dept_themes[$department["deptID"]] != $CONF["THEME"] ) )
				{
					$error = ( Util_Vals_WriteToConfFile( "THEME", $dept_themes[$department["deptID"]] ) ) ? "" : "Could not write to vals file. [e5]" ;
					$CONF["THEME"] = $dept_themes[$department["deptID"]] ;
				}
			}
			else if ( !count( $departments ) )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/remove.php" ) ;
				Lang_remove_Lang( $dbh, 0 ) ;
			}

			$dir_files = glob( $CONF["CONF_ROOT"]."/logo_*", GLOB_NOSORT ) ;
			$total_dir_files = count( $dir_files ) ;
			if ( $total_dir_files )
			{
				for ( $c = 0; $c < $total_dir_files; ++$c )
				{
					if ( $dir_files[$c] && is_file( $dir_files[$c] ) && preg_match( "/logo_$deptid\./", $dir_files[$c] ) )
					{
						@unlink( $dir_files[$c] ) ;
					}
				}
			}

			// need to fetch again to get remaining departments
			$departments = Depts_get_AllDepts( $dbh ) ;
			if ( count( $departments ) == 1 )
			{
				$department = $departments[0] ;
				$temp_deptid = $department["deptID"] ;

				$default_logo = $dept_logo = "" ;
				if ( $total_dir_files )
				{
					for ( $c = 0; $c < $total_dir_files; ++$c )
					{
						if ( $dir_files[$c] && is_file( $dir_files[$c] ) )
						{
							if ( preg_match( "/logo_0\./", $dir_files[$c] ) )
								$default_logo = $dir_files[$c] ;
							else if ( preg_match( "/logo_$temp_deptid\./", $dir_files[$c] ) )
								$dept_logo = $dir_files[$c] ;
						}
					}
				}
				if ( $dept_logo )
				{
					if ( $default_logo ) { @unlink( $default_logo ) ; }
				}
			}
		}
		else
			$error = "Department is assigned to a Department Group.  Department cannot be deleted." ;
	}
	else if ( $action === "update_lang" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
		$prev_lang = Util_Format_Sanatize( Util_Format_GetVar( "prev_lang" ), "ln" ) ;
		$lang = Util_Format_Sanatize( Util_Format_GetVar( "lang" ), "ln" ) ;

		if ( is_file( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) )
		{
			$error = ( Util_Vals_WriteToConfFile( "lang", $lang ) ) ? "" : "Could not write to config file. [e3]" ;
			if ( !$error )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/update.php" ) ;

				$CONF["lang"] = $lang ;
				Depts_update_DeptLangs( $dbh, $prev_lang, $lang ) ;
			}
		}
		else { $error = "Invalid language." ; }
	}
	else if ( $action === "update_theme" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
		$prev_theme = Util_Format_Sanatize( Util_Format_GetVar( "prev_theme" ), "ln" ) ;
		$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;

		if ( is_dir( "$CONF[DOCUMENT_ROOT]/themes/$theme/" ) )
		{
			$error = ( Util_Vals_WriteToConfFile( "THEME", $theme ) ) ? "" : "Could not write to config file. [e7]" ;
			if ( !$error )
			{
				$CONF["THEME"] = $theme ;

				$update_vals = 0 ;
				foreach ( $dept_themes as $the_deptid => $theme )
				{
					if ( $theme == $prev_theme ) { unset( $dept_themes[$the_deptid] ) ; $update_vals = 1 ; }
				}
				if ( count( $dept_themes ) || $update_vals ) { $error = ( Util_Vals_WriteToFile( "THEMES", serialize( $dept_themes ) ) ) ? "" : "Could not write to vals file. [e6]" ; }
			}
		}
		else { $error = "Invalid theme." ; }
	}

	if ( !isset( $departments ) )
		$departments = Depts_get_AllDepts( $dbh ) ;

	// filter for departments with SMTP
	$departments_smtp = $smtp_temp = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		if ( $department["smtp"] && !isset( $smtp_temp[$department["smtp"]] ) )
		{
			$departments_smtp[$department["deptID"]] = $department["smtp"] ;
			$smtp_temp[$department["smtp"]] = true ;
		}
	}

	$auto_offline = ( isset( $VALS["AUTO_OFFLINE"] ) && $VALS["AUTO_OFFLINE"] ) ? unserialize( $VALS["AUTO_OFFLINE"] ) : Array() ;
	$themes_js = "" ;
	foreach ( $dept_themes as $key => $value )
		$themes_js .= "themes[$key] = '$value' ; " ;

	$dept_groups_hash = Array() ; $dept_groups_js = "" ;
	$dept_groups = Depts_get_AllDeptGroups( $dbh ) ;
	for ( $c = 0; $c < count( $dept_groups ); ++$c )
	{
		$dept_group = $dept_groups[$c] ;
		$deptids = explode( ",", $dept_group["deptids"] ) ;
		for ( $c2 = 0; $c2 < count( $deptids ); ++$c2 )
		{
			$deptid_temp = $deptids[$c2] ;
			if ( $deptid_temp && !isset( $dept_groups_hash[$deptid_temp] ) ) { $dept_groups_hash[$deptid_temp] = 1 ; $dept_groups_js .= "dept_groups[$deptid_temp] = 1 ; " ; }
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

<script data-cfasync="false" type="text/javascript">
<!--
	var global_deptid ;
	var global_option ;
	var global_div_list_height ;
	var global_div_form_height ;
	var themes = new Object ;
	var max_menus = 8 ;

	var dept_groups = new Object ; <?php echo $dept_groups_js ?>

	$(document).ready(function()
	{
		$("html").css({'background': '#272727'}) ; $("body").css({'background': '#D6E4F2'}) ;

		init_menu() ;
		toggle_menu_setup( "depts" ) ;

		init_divs() ;

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;
		<?php elseif ( $action && $error ): ?>do_alert( 0, "<?php echo $error ?>" ) ;
		<?php endif ; ?>

		eval( "<?php echo $themes_js ?>" ) ;

		<?php if ( $ao ): ?>
		$('*[id*=menu_8_]').each(function() {
			$(this).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
		}) ;
		<?php endif ; ?>

		<?php if ( $ftab == "vis" ): ?>
			$(".div_class_visible").fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
		<?php elseif ( $ftab == "loop" ): ?>
			$('.div_class_loop').fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
		<?php elseif ( $ftab == "msg" ): ?>
			$('*[id*=menu_2_]').each(function() {
				$(this).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
			}) ;
		<?php elseif ( $ftab == "queue" ): ?>
			$('*[id*=menu_5_]').each(function() {
				$(this).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
			}) ;
		<?php elseif ( $ftab == "route" ): ?>
			$('.div_class_route').fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
		<?php elseif ( $ftab == "req" ): ?>
			$('*[id*=menu_6_]').each(function() {
				$(this).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
			}) ;
		<?php elseif ( $ftab == "lang" ): ?>
			$('.div_class_lang').fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
			$('#div_tab_lang_primary').fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
		<?php endif ; ?>

	});
	$(window).resize(function() { });

	function init_divs()
	{
		global_div_list_height = $('#div_list').outerHeight() ;
		global_div_form_height = $('#div_form').outerHeight() ;
	}

	function do_submit()
	{
		var name = $( "input#name" ).val() ;
		var email = $( "input#email" ).val() ;

		if ( name == "" )
			do_alert( 0, "Please provide the department name." ) ;
		else if ( !check_email( email ) )
			do_alert( 0, "Please provide a valid email address." ) ;
		else if ( !check_visible() )
		{
			do_alert( 0, "The one available department must be \"Visible for Selection\"." ) ;
			setTimeout( function(){ $('#td_option_visible').fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ; }, 3500 ) ;
		}
		else
			$('#theform').submit() ;
	}

	function do_options( theoption, thedeptid, thebgcolor )
	{
		var unique = unixtime() ;
		global_option = theoption ;
		global_deptid = thedeptid ;

		for ( var c = 1; c <= max_menus; ++c )
		{
			if ( c != theoption )
				$('#menu_'+c+"_"+thedeptid).removeClass('menu_dept_focus').addClass('menu_dept') ;
		}

		if ( $('#iframe_edit_'+thedeptid).is(':visible') && ( document.getElementById('iframe_edit_'+thedeptid).contentWindow.option == theoption ) )
		{
			$('#iframe_'+thedeptid).fadeOut("fast") ;

			$('#menu_'+theoption+"_"+thedeptid).removeClass('menu_dept_focus').addClass('menu_dept') ;
		}
		else
		{
			$('#iframe_edit_'+thedeptid).attr('src', 'iframe_edit_'+theoption+'.php?bgcolor='+thebgcolor+'&option='+theoption+'&deptid='+thedeptid+'&'+unique ) ;

			$('#iframe_'+thedeptid).fadeIn("fast") ;
			$('#menu_'+theoption+"_"+thedeptid).removeClass('menu_dept').addClass('menu_dept_focus') ;
		}
	}

	function do_edit( thedeptid, thename, theemail, thertype, thertime, therloop, thevupload, thectimer, thetexpire, thevisible, thequeue, thetshare, thelang, thesmtp_md5 )
	{
		global_deptid = thedeptid ;

		$( "input#deptid" ).val( thedeptid ) ;
		$( "input#name" ).val( thename ) ;
		$( "input#email" ).val( theemail ) ;
		$( "select#rtime" ).val( thertime ) ;
		$( "select#texpire" ).val( thetexpire ) ;
		$( "select#smtp" ).val( thesmtp_md5 ) ;
		$( "input#rtype_"+thertype ).prop( "checked", true ) ;
		$( "select#rloop" ).val( therloop ) ;
		$( '#ctimer_'+thectimer ).prop('checked', true) ;
		$( "input#visible_"+thevisible ).prop( "checked", true ) ;
		$( "input#tshare_"+thetshare ).prop( "checked", true ) ;

		if ( thelang ) { $( "select#lang" ).val( thelang ) ; }
		else { $( "select#lang" ).val( "<?php echo $CONF["lang"] ?>" ) ; }
		if ( typeof( themes[thedeptid] ) != "undefined" ) { $( "select#theme" ).val( themes[thedeptid] ) ; }

		show_form( thedeptid ) ;

		do_upload_checked( thevupload ) ;
		$('#div_dept_online').show() ;
	}

	function check_visible()
	{
		var deptid = parseInt( $( "input#deptid" ).val() ) ;
		var visible = $('input:radio[name=visible]:checked').val() ;
		var total_depts = <?php echo count( $departments ) ; ?> ;
 
		if ( !parseInt( visible ) )
		{
			if ( ( deptid && ( total_depts == 1 ) ) || ( !deptid && !total_depts ) )
				return false ;
		} return true ;
	}

	function do_reset_()
	{
		$('html, body').animate({
			scrollTop: 0
		}, 500);
	}

	function do_delete( thedeptid, thename )
	{
		var pos = $('#div_tr_'+thedeptid).position() ;
		var width = $('#div_tr_'+thedeptid).outerWidth() - 18 ;
		var height = $('#div_tr_'+thedeptid).outerHeight() + 75 ;

		global_deptid = thedeptid ;

		if ( $('#div_notice_delete').is(':visible') )
			$('#div_notice_delete').fadeOut( "fast", function() { show_div_delete(thename, pos, width, height) ; }) ;
		else
			show_div_delete(thename, pos, width, height) ;
	}

	function do_delete_doit()
	{
		if ( confirm( "Are you sure?  All department data will be permanently deleted." ) )
			location.href = "depts.php?action=delete&deptid="+global_deptid ;
	}

	function show_div_delete( thename, thepos, thewidth, theheight )
	{
		$('#span_name').html( thename ) ; 
		$('#div_notice_delete').css({'top': thepos.top, 'left': thepos.left, 'width': thewidth, 'height': theheight}).fadeIn("fast") ;

		if ( typeof( dept_groups[global_deptid] ) != "undefined" )
		{
			$('#div_button_confirm_delete').hide() ;
			$('#div_button_error_dept_group_assigned').show() ;
		}
		else
		{
			$('#div_button_error_dept_group_assigned').hide() ;
			$('#div_button_confirm_delete').show() ;
		}
	}

	function new_canned( thedeptid )
	{
		document.getElementById('iframe_edit_'+thedeptid).contentWindow.toggle_new(1) ;
	}

	function update_theme( thetheme )
	{
		location.href = 'depts.php?action=update_theme&prev_theme=<?php echo isset( $CONF["THEME"] ) ? $CONF["THEME"] : "" ; ?>&theme='+thetheme ;
	}

	function update_lang( thelang )
	{
		location.href = 'depts.php?action=update_lang&deptid=0&prev_lang=<?php echo isset( $CONF["lang"] ) ? $CONF["lang"] : "" ; ?>&lang='+thelang ;
	}

	function show_form( thedeptid )
	{
		if ( typeof( global_option ) != "undefined" )
		{
			if ( $('#iframe_edit_'+global_deptid).is(':visible') && ( document.getElementById('iframe_edit_'+global_deptid).contentWindow.option == global_option ) )
			do_options( global_option, global_deptid, "" ) ;
		}

		$(window).scrollTop(0) ;
		if ( !thedeptid )
		{
			$('#span_link_html_code').html( '<a href="code.php">department specific HTML Code</a>' ) ;
		}
		else
		{
			$('#span_link_html_code').html( '<a href="code.php?deptid='+thedeptid+'">department specific HTML Code</a>' ) ;
		}

		$('#div_smtps').show() ;

		$('#div_error_dept_group_assigned').hide() ;
		$('#div_btn_add').hide() ;
		$('#div_list').hide() ;
		$('#div_form').show() ;
	}

	function do_reset()
	{
		global_deptid = 0 ;
		$('#deptid').val(0) ;
		$('#lang').val('<?php echo $CONF["lang"] ?>') ;
		$('#theform').each(function(){
			this.reset();
		});

		$(window).scrollTop(0) ;
		$('#div_form').hide() ;
		$('#div_btn_add').show() ;
		$('#div_list').show() ;
		$('#div_dept_online').hide() ;
	}

	function iframe_scroll( thedeptid, thescrollto )
	{
		document.getElementById('iframe_edit_'+thedeptid).contentWindow.scrollTo( 0, thescrollto ) ;
	}

	function toggle_upload( thevalue )
	{
		var total_checked = 0 ;
		$('#theform').find('*').each( function () {
			var div_name = this.id ;
			if ( div_name.indexOf( "upload_" ) == 0 )
			{
				if ( this.checked ) { ++total_checked ; }
			}
		}) ;

		if ( $('#upload_'+thevalue).is(':checked') )
		{
			$('#upload_'+thevalue).prop('checked', false) ;
			if ( thevalue != 1 ) { $('#upload_1').prop('checked', false) ; }
			else if ( thevalue == 1 ) { check_all(0) ; }
		}
		else
		{
			++total_checked ;
			$('#upload_'+thevalue).prop('checked', true) ;
			if ( thevalue == 0 ) { check_all(0) ; }
			else if ( thevalue == 1 ) { check_all(1) ; }
			else if ( total_checked == 8 ) { $('#upload_1').prop('checked', true) ; }
			else { $('#upload_0').prop('checked', false) ; }
		}
	}

	function check_all( theflag )
	{
		if ( theflag )
		{
			$('#theform').find('*').each( function () {
				var div_name = this.id ;
				if ( div_name.indexOf( "upload_" ) == 0 )
				{
					if ( div_name == "upload_0" )
						this.checked = false ;
					else
						this.checked = true ;
				}
			}) ;
		}
		else
		{
			$('#theform').find('*').each( function () {
				var div_name = this.id ;
				if ( div_name.indexOf( "upload_" ) == 0 )
				{
					if ( div_name != "upload_0" )
						this.checked = false ;
				}
			}) ;
		}
	}

	function do_upload_checked( thevalue )
	{
		var uploads = thevalue.split( "," ) ;

		if ( uploads.length >= 8 ) { check_all(1) ; }
		else
		{
			for ( var c = 0; c < uploads.length; ++c )
			{
				var value = uploads[c] ;

				if ( value )
				{
					if ( value == 1 ) { check_all(1) ; break ; }
					else { $('#upload_'+value).prop('checked', true) ; }
				}
			}
		}
	}

	function blink_td_email( thedeptid )
	{
		$('#div_td_email_'+thedeptid).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
	}

	function send_test_email( thedeptid, theemail )
	{
		var unique = unixtime() ;

		if ( confirm( "Send a test email to "+theemail+"?" ) )
		{
			document.getElementById('iframe_edit_'+thedeptid).contentWindow.disable_button() ;

			$.ajax({
			type: "POST",
			url: "../ajax/setup_actions_.php",
			data: "action=send_test_email&deptid="+thedeptid+"&unique="+unique,
			success: function(data){
				document.getElementById('iframe_edit_'+thedeptid).contentWindow.send_complete( data ) ;
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Could not connect to server.  Please refresh the page and try again." ) ;
			} });
		}
	}

	function check_dept_group_assigned()
	{
		if ( typeof( dept_groups[global_deptid] ) != "undefined" )
		{
			setTimeout( function(){ $('#visible_1').prop('checked', true) ; }, 200 ) ;
			$('#div_error_dept_group_assigned').show().fadeTo('fast', .1).fadeTo('fast', 1).fadeTo('fast', .1).fadeTo('fast', 1).fadeTo('fast', .1).fadeTo('fast', 1) ;
		}
	}

	function mimic_edit()
	{
		$('#div_notice_delete').hide() ;
		$('#dept_edit_'+global_deptid).trigger( "click" ) ;
	}

	function preview_lang( thelang, thewidth, theheight, thedeptid )
	{
		var unique = unixtime() ;
		var thetarget = "_blank" ;

		if ( ( typeof( mapp ) != "undefined" ) && mapp )
			thetarget = "_system" ;

		var win_preview = window.open( "../phplive.php?d="+thedeptid+"&lang="+thelang+"&preview=1&"+unique, "theme_preview", 'scrollbars=no,resizable=yes,menubar=no,location=no,screenX=50,screenY=100,width='+thewidth+',height='+theheight, thetarget, "location=yes" ) ;
		win_preview.focus() ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu_focus" style="margin-left: 0px;">Chat Departments</div>
			<div class="op_submenu" onClick="location.href='dept_groups.php'">Department Groups</div>
			<div style="clear: both"></div>
		</div>

		<div id="div_btn_add" style="margin-top: 25px;">
			<table cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td><div class="edit_focus" onClick="show_form(0)"><img src="../pics/icons/add.png" width="16" height="16" border="0" alt=""> Add Chat Department</div></td>
				<td style="padding-left: 55px;">
					<?php if ( count( $departments ) > 1 ): ?>
						<div style="text-shadow: none;">
							<div id="div_tab_lang_primary" class="edit_title">Primary Language</div>
							<div>Primary language for <a href="code.php">All Departments HTML Code</a>:</div>
							<div style="margin-top: 5px;">
								<div id="primary_lang_select" class="info_clear">
									<select name="lang_pr" id="lang_pr">
									<?php
										$dir_langs = opendir( "$CONF[DOCUMENT_ROOT]/lang_packs/" ) ;

										$langs = Array() ;
										while ( $this_lang = readdir( $dir_langs ) )
											$langs[] = $this_lang ;
										closedir( $dir_langs ) ;

										sort( $langs, SORT_STRING ) ;
										for ( $c = 0; $c < count( $langs ); ++$c )
										{
											$this_lang = preg_replace( "/.php/", "", $langs[$c] ) ;

											$selected = $selected_string = "" ;
											if ( $CONF["lang"] == $this_lang )
											{
												$selected = "selected" ;
												$selected_string = " (primary)" ;
											}

											if ( preg_match( "/[a-z]/i", $this_lang ) && !preg_match( "/index/i", $this_lang ) )
												print "<option value=\"$this_lang\" $selected>".ucfirst( $this_lang )."$selected_string</option>" ;
										}
									?>
									</select> &nbsp; &nbsp;
									<button type="button" onClick="update_lang($('#lang_pr').val())" class="btn">Update</button>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</td>
				<td style="padding-left: 55px;">
					<?php if ( count( $departments ) > 1 ): ?>
						<div style="text-shadow: none;">
							<div class="edit_title">Primary Theme</div>
							<div>Primary theme for <a href="code.php">All Departments HTML Code</a>:</div>
							<div style="margin-top: 5px;">
								<div id="primary_theme_select" style="" class="info_clear">
									<select name="theme_pr" id="theme_pr">
									<?php
										$dir_themes = opendir( "$CONF[DOCUMENT_ROOT]/themes/" ) ;

										$themes = Array() ;
										while ( $this_theme = readdir( $dir_themes ) )
											$themes[] = $this_theme ;
										closedir( $dir_themes ) ;

										sort( $themes, SORT_STRING ) ;
										for ( $c = 0; $c < count( $themes ); ++$c )
										{
											$this_theme = $themes[$c] ;

											$selected = $selected_string = "" ;
											if ( $CONF["THEME"] == $this_theme )
											{
												$selected = "selected" ;
												$selected_string = " (primary)" ;
											}

											if ( preg_match( "/[a-z]/i", $this_theme ) && ( $this_theme != "initiate" ) )
												print "<option value=\"$this_theme\" $selected>$this_theme$selected_string</option>" ;
										}
									?>
									</select> &bull; <a href="JavaScript:void(0)" onClick="preview_theme_embed($('#theme_pr').val(), 0)">preview</a> &nbsp; &nbsp;
									<button type="button" onClick="update_theme($('#theme_pr').val())" class="btn">Update</button>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</td>
			</tr>
			</table>
		</div>
		<div style="display: none; margin-top: 15px; text-align: right;"><span class="info_neutral" style="cursor: pointer;"><img src="../pics/icons/group.png" width="16" height="16" border="0" alt=""> configure fallback department</span></div>
		<div id="div_list" style="margin-top: 15px;">
			<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_departments">
			<?php
				for ( $c = 0; $c < count( $departments ); ++$c )
				{
					$department = $departments[$c] ;

					$name = $department["name"] ;
					$rtype = $vars_rtype[$department["rtype"]] ;
					$rtime = "$department[rtime] sec" ;
					$visible = ( $department["visible"] ) ? "<img src=\"../pics/icons/check.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"visible for selection\" title=\"visible for selection\">" : "<img src=\"../pics/icons/privacy_on.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"not visible for selection\" title=\"not visible for selection\">" ;

					$queue_string = ( $department["queue"] && ( $department["rtype"] != 3 ) ) ? "<span class=\"info_good\" style=\"padding: 2px; text-shadow: none;\">On</span>" : "<span class=\"info_error\" style=\"padding: 2px; text-shadow: none;\">Off</span>" ;
					$vupload_icon = ( $VARS_INI_UPLOAD && $department["vupload"] ) ? "<img src=\"../pics/icons/attach.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"visitors can upload files during chat\" title=\"visitors can upload files during chat\" style=\"cursor: help;\">" : "" ;

					$lang = ucfirst( $department["lang"] ) ;
					$theme = ( isset( $dept_themes[$department["deptID"]] ) ) ? $dept_themes[$department["deptID"]] : $CONF["THEME"] ;
					$span_class = ( isset( $auto_offline[$department["deptID"]] ) ) ? "info_good" : "info_error" ;
					$auto_off_string = ( isset( $auto_offline[$department["deptID"]] ) ) ? "<span class=\"info_good\" style=\"padding: 2px; text-shadow: none;\">On</span>" : "<span class=\"info_error\" style=\"padding: 2px; text-shadow: none;\">Off</span>" ;

					$smtp_md5 = ( $department["smtp"] ) ? md5( $department["smtp"] ) : 0 ;

					$bg_color = ( ($c+1) % 2 ) ? "FFFFFF" : "EDEDED" ;

					$edit_delete = "<div id=\"dept_edit_$department[deptID]\" style=\"cursor: pointer;\" onClick=\"do_edit( $department[deptID], '$name', '$department[email]', '$department[rtype]', '$department[rtime]', '$department[rloop]', '$department[vupload]', '$department[ctimer]', '$department[texpire]', '$department[visible]', '$department[queue]', '$department[tshare]', '$department[lang]', '$smtp_md5' )\"><img src=\"../pics/btn_edit.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div><div onClick=\"do_delete($department[deptID], '$name')\" style=\"margin-top: 10px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;
					$options = "
						<div class=\"menu_dept\" id=\"menu_6_$department[deptID]\" onClick=\"do_options( 6, $department[deptID], '$bg_color' );\">Pre-Chat</div>
						<div class=\"menu_dept\" id=\"menu_5_$department[deptID]\" onClick=\"do_options( 5, $department[deptID], '$bg_color' );\">Queue <span id=\"span_queue_$department[deptID]\" style=\"text-shadow: none;\">$queue_string</span></div>
						<div class=\"menu_dept\" id=\"menu_1_$department[deptID]\" onClick=\"do_options( 1, $department[deptID], '$bg_color' );\">Chatting</div>
						<div class=\"menu_dept\" id=\"menu_4_$department[deptID]\" onClick=\"do_options( 4, $department[deptID], '$bg_color' );\">Visitor Email Transcript</div>
						<div class=\"menu_dept\" id=\"menu_2_$department[deptID]\" onClick=\"do_options( 2, $department[deptID], '$bg_color' );\">Offline Message</div>
						<div class=\"menu_dept\" id=\"menu_3_$department[deptID]\" onClick=\"do_options( 3, $department[deptID], '$bg_color' );\">Canned Responses</div>
						<div class=\"menu_dept\" id=\"menu_8_$department[deptID]\" onClick=\"do_options( 8, $department[deptID], '$bg_color' );\">Offline Hours <span id=\"span_class_$department[deptID]\" style=\"text-shadow: none;\">$auto_off_string</span></div>
						<div class=\"menu_dept\" id=\"menu_7_$department[deptID]\" style=\"margin: 0px;\" onClick=\"do_options( 7, $department[deptID], '$bg_color' );\">SMTP</div>
						<div style=\"clear: both;\"></div>
					" ;

					$td1 = "td_dept_td_blank" ;
					$td2 = "td_dept_td" ;

					print "
					<tr id=\"div_tr_$department[deptID]\" style=\"background: #$bg_color\">
						<td class=\"$td1\" nowrap>$edit_delete</td>
						<td class=\"$td1\">
							<div><b>$name</b></div>
							<div style=\"margin-top: 5px;\">$vupload_icon</div>
						</td>
						<td class=\"$td1\"><div id=\"div_td_email_$department[deptID]\"><div class=\"txt_grey\" style=\"margin-bottom: 5px;\">Department Email</div>$department[email]</div></td>
						<td class=\"$td1 div_class_route\"><div class=\"txt_grey\" style=\"margin-bottom: 5px;\">Routing Type</div>$rtype</td>
						<td class=\"$td1 div_class_rtype\"><div class=\"txt_grey\" style=\"margin-bottom: 5px;\">Routing Time</div>$rtime</td>
						<td class=\"$td1 div_class_loop\"><div class=\"txt_grey\" style=\"margin-bottom: 5px;\">Loop</div>$department[rloop]</td>
						<td class=\"$td1 div_class_visible\" align=\"center\"><div class=\"txt_grey\" style=\"margin-bottom: 5px;\">Visible</div>$visible</td>
						<td class=\"$td1 div_class_lang\"><div class=\"txt_grey\" style=\"margin-bottom: 5px;\">Language</div>$lang</td>
						<td class=\"$td1\"><div class=\"txt_grey\" style=\"margin-bottom: 5px;\">Theme</div><a href=\"JavaScript:void(0)\" onClick=\"preview_theme_embed('$theme', $department[deptID])\">$theme</a></td>
					</tr>
					<tr style=\"background: #$bg_color\">
						<td class=\"$td2\" valign=\"top\" align=\"right\" style=\"padding-top: 0px;\">
							<div class=\"txt_grey\" style=\"margin-top: 16px;\">Dept ID: $department[deptID]</div>
						</td>
						<td class=\"$td2\" colspan=\"8\" valign=\"top\" style=\"padding-top: 0px;\">
							<div style=\"padding-top: 15px; padding-bottom: 0px; border-bottom: 0px;\" class=\"info_neutral\">
								Department Options for <span class=\"title info_action\">$name</span>
								<div style=\"margin-top: 15px;\">$options</div>
							</div>
							<div id=\"iframe_$department[deptID]\" style=\"display: none; width: 100%\"><iframe id=\"iframe_edit_$department[deptID]\" name=\"iframe_edit_$department[deptID]\" style=\"width: 100%; height: 450px; border: 0px; margin-top: 15px;\" src=\"\" scrolling=\"auto\" frameBorder=\"0\"></iframe></div>
						</td>
					</tr>
					" ;
				}
				if ( $c == 0 )
					print "<tr><td colspan=9 class=\"td_dept_td\">Blank results.</td></tr>" ;
			?>
			</table>
		</div>

		<div id="div_form" style="display: none;" id="a_edit">
			<form method="POST" action="depts.php" id="theform">
			<input type="hidden" name="action" value="submit">
			<input type="hidden" name="deptid" id="deptid" value="0">
			<input type="hidden" name="tshare" value="">

			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td colspan=2 style="padding-bottom: 25px;" align="left"><span class="info_neutral"><img src="../pics/icons/arrow_left.png" width="16" height="15" border="0" alt=""> <a href="JavaScript:void(0)" onClick="do_reset()">back</a></span></td>
				</tr>
				<tr>
					<td nowrap class="tab_form_title">Department Name</td>
					<td style="padding-left: 10px;"><input type="text" class="input" name="name" id="name" size="30" maxlength="40" value="" onKeyPress="return noquotes(event)"> &nbsp; * example: Customer Support, Sales, Billing Department</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title">Department Email</td>
					<td style="padding-left: 10px;"><input type="text" class="input" name="email" id="email" size="30" maxlength="160" value="" onKeyPress="return justemails(event)"> &nbsp; * if the visitor leaves a message on the offline form, the message is sent to this email address</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title">Chat Routing Type</td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=5 border=0>
						<tr>
							<td nowrap><div class="info_neutral" onClick="$('#rtype_1').prop('checked', true);" style="cursor: pointer;"><input type="radio" name="rtype" id="rtype_1" value="1"> <span style="font-weight: bold; color: #5D5D5D;">Defined Order:</span></div></td>
							<td>The chat request is routed to each operator based on the defined order set at <a href="ops.php?jump=assign">Assign Operator to Department</a> area.</td>
						</tr>
						<tr>
							<td nowrap><div class="info_neutral" onClick="$('#rtype_2').prop('checked', true);" style="cursor: pointer;"><input type="radio" name="rtype" id="rtype_2" value="2" checked> <span style="font-weight: bold; color: #5D5D5D;">Round-Robin:</span></div></td>
							<td>The chat request is routed to each operator based on the operator who <b>has not accepted a chat in the longest time</b>.  The operator that has not accepted a chat in the longest time will be the first to receive the chat request.</td>
						</tr>
						<tr>
							<td><div class="info_neutral" onClick="$('#rtype_3').prop('checked', true);" style="cursor: pointer;"><input type="radio" name="rtype" id="rtype_3" value="3"> <span style="font-weight: bold; color: #5D5D5D;">Simultaneous:</span></div></td>
							<td>All operators will receive the chat request at the same time.</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title">Chat Routing Time</td>
					<td style="padding-left: 10px;">
						<div>If an operator does not accept the chat request within <select name="rtime" id="rtime" ><option value="15">15 seconds</option><option value="30">30 seconds</option><option value="45" selected>45 seconds</option><option value="60">1 minute</option><option value="90">1 min 30 sec</option><option value="120">2 minutes</option><option value="150">2 min 30 sec</option><option value="180">3 minutes</option><option value="240">4 minutes</option><option value="300">5 minutes</option></select>, route the chat request to the next available online operator.</div>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title">Chat Routing Loop</td>
					<td style="padding-left: 10px;">Route the chat request to all online operators <select name="rloop" id="rloop" >
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						</select> times (Simultaneous routing type will always route only once)</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title">Share Transcripts</td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td>
								Should the chat transcripts be shared between all operators that are assigned to this department?  If set to "No, keep private", the chat transcripts will only be visible to the chat operator that accepted the chat.
							</td>
							<td style="padding-left: 15px;" nowrap>
								<span class="info_good" style="background: #7BD869; border: 1px solid #65CB55; color: #FFFFFF; cursor: pointer;" onclick="$('#tshare_1').prop('checked', true);"><input type="radio" name="tshare" id="tshare_1" value="1"> Yes, share</span>
								<span class="info_error" style="background: #FD7D7F; border: 1px solid #E16F71; color: #FFFFFF; cursor: pointer;" onclick="$('#tshare_0').prop('checked', true);"><input type="radio" name="tshare" id="tshare_0" value="0" checked> No, keep private</span>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title">Chat Timer Display</td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td>During a chat session (for both the visitor and the operator), display a clock like incrementing timer that shows the duration of the chat session.  The timer will be in the format of <b>mm:ss</b> on the chat window.</td>
							<td style="padding-left: 15px;" nowrap>
								<span class="info_good" style="background: #7BD869; border: 1px solid #65CB55; color: #FFFFFF; cursor: pointer;" onclick="$('#ctimer_1').prop('checked', true);"><input type="radio" name="ctimer" id="ctimer_1" value="1" checked> Display</span>
								<span class="info_error" style="background: #FD7D7F; border: 1px solid #E16F71; color: #FFFFFF; cursor: pointer;" onclick="$('#ctimer_0').prop('checked', true);"><input type="radio" name="ctimer" id="ctimer_0" value="0"> Don't display</span>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title"><img src="../pics/icons/attach.png" width="16" height="16" border="0" alt=""> Visitor File Upload</td>
					<td style="padding-left: 10px;">
						<?php if ( $VARS_INI_UPLOAD ): ?>
						Allow visitors to upload files during a chat session?
						<div style="margin-top: 10px;">
							<span class="info_neutral" style="cursor: pointer;" onclick="toggle_upload(0)"><input type="checkbox" name="vupload[]" value="0" id="upload_0" onclick="toggle_upload(0)"> No</span>
							<span class="info_neutral" style="cursor: pointer;" onclick="toggle_upload(1)"><input type="checkbox" name="vupload[]" value="1" id="upload_1" onclick="toggle_upload(1)"> All</span>
							<span class="info_neutral" style="cursor: pointer;" onclick="toggle_upload('GIF')"><input type="checkbox" name="vupload[]" value="GIF" id="upload_GIF" onclick="toggle_upload('GIF')"> GIF</span>
							<span class="info_neutral" style="cursor: pointer;" onclick="toggle_upload('PNG')"><input type="checkbox" name="vupload[]" value="PNG" id="upload_PNG" onclick="toggle_upload('PNG')"> PNG</span>
							<span class="info_neutral" style="cursor: pointer;" onclick="toggle_upload('JPG')"><input type="checkbox" name="vupload[]" value="JPG" id="upload_JPG" onclick="toggle_upload('JPG')"> JPG, JPEG</span>
							<span class="info_neutral" style="cursor: pointer;" onclick="toggle_upload('PDF')"><input type="checkbox" name="vupload[]" value="PDF" id="upload_PDF" onclick="toggle_upload('PDF')"> PDF</span>
							<span class="info_neutral" style="cursor: pointer;" onclick="toggle_upload('ZIP')"><input type="checkbox" name="vupload[]" value="ZIP" id="upload_ZIP" onclick="toggle_upload('ZIP')"> ZIP</span>
							<span class="info_neutral" style="cursor: pointer;" onclick="toggle_upload('TAR')"><input type="checkbox" name="vupload[]" value="TAR" id="upload_TAR" onclick="toggle_upload('TAR')"> TAR</span>
							<span class="info_neutral" style="cursor: pointer;" onclick="toggle_upload('TXT')"><input type="checkbox" name="vupload[]" value="TXT" id="upload_TXT" onclick="toggle_upload('TXT')"> TXT</span>
							<span class="info_neutral" style="cursor: pointer;" onclick="toggle_upload('CONF')"><input type="checkbox" name="vupload[]" value="CONF" id="upload_CONF" onclick="toggle_upload('CONF')"> CONF</span>
						</div>

						<div style="margin-top: 20px;"><span class="info_warning"><img src="../pics/icons/info.png" width="14" height="14" border="0" alt=""> The file upload setting for the chat operators can be set for each operator at the <a href="ops.php">Operators</a> area.</span></div>
						<?php else: ?>
						<img src="../pics/icons/alert.png" width="16" height="16" border="0" alt=""> File upload is not enabled for this server ('<a href="http://php.net/manual/en/ini.core.php#ini.file-uploads" target="_blank">file_uploads</a>' directive).  Please contact the server admin for more information.
						<?php endif ; ?>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title">Delete Transcripts</td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td>Automatically delete <a href="transcripts.php">chat transcripts</a> that were created over &nbsp;</td>
							<td>
								<select id="texpire" name="texpire"><option value=0>do not delete</option>
								<?php
									for( $c = 1; $c <= 60; ++$c )
									{
										print "<option value=\"$c\">$c</option>" ;
									}
								?>
								</select> months ago.
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<?php if ( count( $departments_smtp ) > 0 ): ?>
			<div id="div_smtps" style="display: none; margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title">SMTP Settings</td>
					<td style="padding-left: 10px;">
						<a href="../addons/smtp/smtp.php">Use SMTP setting</a>: 
						<select name="smtp" id="smtp">
							<option value="0"></option>
							<?php
								foreach ( $departments_smtp as $deptid => $smtp )
								{
									$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $smtp ) ) ;
									if ( $smtp_array )
									{
										$smtp_md5 = md5( $smtp ) ;
										if ( isset( $smtp_array["api"] ) && $smtp_array["api"] )
											print "<option value=\"$smtp_md5\">API: $smtp_array[api] ($smtp_array[login]$smtp_array[domain])</option>" ;
										else
											print "<option value=\"$smtp_md5\">$smtp_array[host] (login: $smtp_array[login])</option>" ;
									}
								}
							?>
						</select> If SMTP is not selected, the system will utilize the standard <a href="https://www.php.net/manual/en/function.mail.php" target="php_mailfunction">PHP mail()</a> function using the server mail settings to send out emails.
					</td>
				</tr>
				</table>
			</div>
			<?php endif ; ?>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title">Language</td>
					<td style="padding-left: 10px;">
						Visitor chat window language ("Start Chat", "Name", "Email", "Question", "Email Transcript", "Select Department", etc) <select name="lang" id="lang">
						<?php
							$dir_langs = opendir( "$CONF[DOCUMENT_ROOT]/lang_packs/" ) ;

							$langs = Array() ;
							while ( $this_lang = readdir( $dir_langs ) )
								$langs[] = $this_lang ;
							closedir( $dir_langs ) ;

							sort( $langs, SORT_STRING ) ;
							for ( $c = 0; $c < count( $langs ); ++$c )
							{
								$this_lang = preg_replace( "/.php/", "", $langs[$c] ) ;

								$selected = "" ;
								if ( $CONF["lang"] == $this_lang )
									$selected = "selected" ;

								if ( preg_match( "/[a-z]/i", $this_lang ) && !preg_match( "/index/i", $this_lang ) )
									print "<option value=\"$this_lang\" $selected> ".ucfirst( $this_lang )."</option>" ;
							}
						?>
						</select>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title">Chat Theme</td>
					<td style="padding-left: 10px;">
						Visitor chat window theme <select name="theme" id="theme">
						<?php
							$dir_themes = opendir( "$CONF[DOCUMENT_ROOT]/themes/" ) ;

							$themes = Array() ;
							while ( $this_theme = readdir( $dir_themes ) )
								$themes[] = $this_theme ;
							closedir( $dir_themes ) ;

							sort( $themes, SORT_STRING ) ;
							for ( $c = 0; $c < count( $themes ); ++$c )
							{
								$this_theme = $themes[$c] ;

								$selected = "" ;
								if ( $CONF["THEME"] == $this_theme )
									$selected = "selected" ;

								if ( preg_match( "/[a-z]/i", $this_theme ) && ( $this_theme != "initiate" ) )
									print "<option value=\"$this_theme\" $selected>$this_theme</option>" ;
							}
						?>
						</select> &bull; <a href="JavaScript:void(0)" onClick="preview_theme_embed($('#theme').val(), 0)">preview</a> &nbsp;
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="tab_form_title" id="td_option_visible">Visible for Selection</td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td>
								When the visitor requests chat, choose whether to display this department on the department selection dropdown menu. If "Not visible", the only way to reach this department is if an operator transfers the chat session to this department or by using the <span id="span_link_html_code"></span>.
								<div class="info_error" style="display: none; margin-top: 5px;" id="div_error_dept_group_assigned"><img src="../pics/icons/alert.png" width="16" height="16" border="0" alt=""> Department is assigned to a <a href="dept_groups.php" style="color: #FFFFFF;">Department Group</a>.  Department must be visible.</div>
							</td>
							<td style="padding-left: 15px;" nowrap>
								<span class="info_good" style="background: #7BD869; border: 1px solid #65CB55; color: #FFFFFF; cursor: pointer;" onclick="$('#visible_1').prop('checked', true);$('#div_error_dept_group_assigned').hide();"><input type="radio" name="visible" id="visible_1" value="1" checked> Visible</span>
								<span class="info_error" style="background: #FD7D7F; border: 1px solid #E16F71; color: #FFFFFF; cursor: pointer;" onclick="$('#visible_0').prop('checked', true);check_dept_group_assigned();"><input type="radio" name="visible" id="visible_0" value="0"> Not visible</span>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title" style="background: #D6E4F2; border: 0px; padding-left: 0px; text-align: left; font-weight: normal;"><span class="info_neutral"><img src="../pics/icons/arrow_left.png" width="16" height="15" border="0" alt=""> <a href="JavaScript:void(0)" onClick="do_reset()">back</a></span></div></td>
					<td style="padding-left: 10px;">
						<div id="div_dept_online" style="display: none; margin-top: 15px;" class="info_warning"><table cellspacing=0 cellpadding=0 border=0><tr><td><img src="../pics/icons/warning.png" width="16" height="16" border="0" alt=""></td><td style="padding-left: 5px;">If an operator is Online <img src="../pics/icons/bulb.png" width="16" height="16" border="0" alt="">, they must logout and login again for some of the above changes to take effect.</td></tr></table></div>
						<div style="margin-top: 15px;"><button type="button" onClick="do_submit()" class="btn">Submit</button> &nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="do_reset()">cancel</a></div>
					</td>
				</tr>
				</table>
			</div>

			</form>
		</div>

		<div id="div_notice_delete" style="display: none; position: absolute; text-align: justify;" class="info_error">
			<div style="padding: 10px;">
				<div class="edit_title">Really delete this department (<span id="span_name"></span>)?</div>
				<div style="margin-top: 5px;">To retain the department chat transcripts and the chat reports, it is recommended to <a href="JavaScript:void(0)" onClick="mimic_edit()" style="color: #FFFFFF;">edit the department</a> and set the "Visible for Selection" to "No" rather then permanently deleting the department and the data.</div>

				<div style="display: none; margin-top: 15px;" id="div_button_confirm_delete"><button type="button" onClick="do_delete_doit()" class="btn">Delete</button> &nbsp; &nbsp; &nbsp; <a href="JavaScript:void(0)" style="color: #FFFFFF" onClick="$('#div_notice_delete').fadeOut('fast')">cancel</a></div>
				<div style="display: none; margin-top: 15px;" id="div_button_error_dept_group_assigned">
					<div class="info_box"><img src="../pics/icons/alert.png" width="16" height="16" border="0" alt=""> Department is assigned to a <a href="dept_groups.php">Department Group</a>.  Department cannot be deleted.</div>
					<div style="margin-top: 15px;"><a href="JavaScript:void(0)" style="color: #FFFFFF" onClick="$('#div_notice_delete').fadeOut('fast')">cancel</a></div>
				</div>
			</div>
		</div>

<span style="color: #0000FF; text-decoration: underline; line-height: 0px !important; cursor: pointer; position: fixed; bottom: 0px; right: 15px; z-index: 20000000;" id="phplive_btn_615" onclick="phplive_launch_chat_0()"></span>
<script data-cfasync="false" type="text/javascript">

var phplive_v = new Object ;
var st_embed_launch ;
var phplive_stop_chat_icon = 1 ;
var phplive_theme = "" ;
var phplive_embed_win_width = "<?php echo $VARS_CHAT_WIDTH_WIDGET ; ?>" ;
var phplive_embed_win_height = "<?php echo $VARS_CHAT_HEIGHT_WIDGET ; ?>" ;

function preview_theme_embed( thetheme, thedeptid )
{
	phplive_v["deptid"] = thedeptid ;
	phplive_theme = thetheme ;

	if ( $('#phplive_iframe_chat_embed_wrapper').is(":visible") )
	{
		phplive_embed_window_close( ) ;
		if ( typeof( st_embed_launch ) != "undefined" ) { clearTimeout( st_embed_launch ) ; }
		st_embed_launch = setTimeout( function(){ phplive_launch_chat_0() ; }, 1200 ) ;
	}
	else { phplive_launch_chat_0() ; }
}

(function() {
var phplive_href = encodeURIComponent( location.href ) ;
var phplive_e_615 = document.createElement("script") ;
phplive_e_615.type = "text/javascript" ;
phplive_e_615.async = true ;
phplive_e_615.src = "<?php echo $CONF["BASE_URL"] ?>/js/phplive_v2.js.php?v=0|615|0|&r="+phplive_href ;
document.getElementById("phplive_btn_615").appendChild( phplive_e_615 ) ;
})() ;

</script>

<?php include_once( "./inc_footer.php" ) ?>
