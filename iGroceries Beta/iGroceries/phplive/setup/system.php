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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_DB.php" ) ;

	// permission checking
	$perm_web = is_writable( "$CONF[CONF_ROOT]" ) ;
	$perm_conf = is_writeable( "$CONF[CONF_ROOT]/config.php" ) ;
	$perm_chats = is_writeable( $CONF["CHAT_IO_DIR"] ) ;
	$perm_initiate = is_writeable( $CONF["TYPE_IO_DIR"] ) ;
	$perm_patches = is_writeable( "$CONF[CONF_ROOT]/patches" ) ;
	$disabled_functions = ini_get( "disable_functions" ) ;
	$ini_open_basedir = ini_get("open_basedir") ;
	$ini_safe_mode = ini_get("safe_mode") ;
	$safe_mode = preg_match( "/on/i", $ini_safe_mode ) ? 1 : 0 ;

	$pv = phpversion() ;

	$query = "SELECT created FROM p_admins WHERE adminID = 1 LIMIT 1" ;
	database_mysql_query( $dbh, $query ) ;
	$super_admin = database_mysql_fetchrow( $dbh ) ;

	$created = date( "M j, Y", $super_admin["created"] ) ;
	$diff = time() - $super_admin["created"] ; $days_running = round( $diff/(60*60*24) ) ;

	$tables = Util_DB_GetTableNames( $dbh ) ; $db_error = 0 ;
	for( $c = 0; $c < count( $tables ); ++$c )
	{
		$analyze = Util_DB_AnalyzeTable( $dbh, $tables[$c] ) ;
		$stats = Util_DB_TableStats( $dbh, $tables[$c] ) ;

		$name = $stats["Name"] ;
		$type = $analyze["Msg_type"] ;
		if ( preg_match( "/^p_/", $name ) )
		{
			if ( isset( $analyze["Msg_text"] ) && !preg_match( "/(Table is already up to date)|(ok)/i", $analyze["Msg_text"] ) )
			{
				$db_error = 1 ; break 1 ;
			}
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
	$(document).ready(function()
	{
		$("html").css({'background': '#272727'}) ; $("body").css({'background': '#D6E4F2'}) ;
		init_menu() ;
		toggle_menu_setup( "settings" ) ;
		fetch_admins() ;
	});

	function generate_admin()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$('#btn_generate').attr( "disabled", true ) ;

		$.ajax({
		type: "POST",
		url: "../ajax/setup_actions.php",
		data: "action=generate_setup_admin&"+unique,
		success: function(data){
			eval( data ) ;

			$('#btn_generate').attr( "disabled", false ) ;
			if ( json_data.status )
			{
				do_alert( 1, "Account Created" ) ;
				fetch_admins() ;
			}
			else
				do_alert( 0, json_data.error ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			$('#btn_generate').attr( "disabled", false ) ;
			alert( "Could not connect to server.  Try refreshing this page." ) ;
		} });
	}

	function fetch_admins()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "../ajax/setup_actions.php",
		data: "action=fetch_setup_admins&"+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				var admin_string = "<table cellspacing=1 cellpadding=0 border=0 width='100%'><tr><td width=\"14\" class=\"td_dept_td\">&nbsp;</td><td class=\"td_dept_td\"><b>Login Info</b></td><td class=\"td_dept_td\"><b>Created</b></td><td class=\"td_dept_td\"><b>Accessed</b></td></tr>" ;
				for ( var c = 0; c < json_data.admins.length; ++c )
				{
					var admin = json_data.admins[c] ;
					var password = admin["password"] ;
					var delete_option = "<img src=\"../pics/icons/delete.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"delete\" title=\"delete\" style=\"cursor: pointer;\" onClick=\"delete_admin("+admin["adminid"]+")\">" ;

					admin_string += "<tr style='background: #FFFFFF;'><td class=\"td_dept_td\">"+delete_option+"</td><td class=\"td_dept_td\" nowrap><b>Login:</b> "+admin["login"]+"<div style=\"margin-top: 5px;\"><b>Password:</b> "+password+"</div></td><td class=\"td_dept_td\" nowrap>"+admin["created"]+"</td><td class=\"td_dept_td\" nowrap>"+admin["lastactive"]+"</td></tr>" ;
				}
				admin_string += "</table>" ;
				
				$('#div_admins').html( admin_string ) ;
			}
			else
			{
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			alert( "Could not connect to server.  Try refreshing this page." ) ;
		} });
	}

	function delete_admin( theadminid )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( confirm( "Really delete this Admin?" ) )
		{
			$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "action=delete_setup_admin&adminid="+theadminid+"&"+unique,
			success: function(data){
				eval( data ) ;

				$('#btn_generate').attr( "disabled", false ) ;
				if ( json_data.status )
				{
					do_alert( 1, "Account Deleted" ) ;
					fetch_admins() ;
				}
				else
					do_alert( 0, json_data.error ) ;
			},
			error:function (xhr, ajaxOptions, thrownError){
				alert( "Could not connect to server.  Try refreshing this page." ) ;
			} });
		}
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" style="margin-left: 0px;" onClick="location.href='settings.php?jump=eips'" id="menu_eips">Excluded IPs</div>
			<div class="op_submenu" onClick="location.href='settings.php?jump=sips'" id="menu_sips">Blocked IPs</div>
			<?php if ( $admininfo["adminID"] == 1 ): ?>
			<div class="op_submenu" onClick="location.href='settings.php?jump=cookie'" id="menu_cookie">Cookies</div>
			<div class="op_submenu" onClick="location.href='settings.php?jump=upload'" id="menu_upload">File Upload</div>
				<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/mapp/settings.php" ) ): ?><div class="op_submenu" onClick="location.href='../mapp/settings.php'" id="menu_system"><img src="../pics/icons/mobile.png" width="12" height="12" border="0" alt=""> Mobile App</div><?php endif ; ?>
			<div class="op_submenu" onClick="location.href='settings.php?jump=profile'" id="menu_profile"><img src="../pics/icons/key.png" width="12" height="12" border="0" alt=""> Password</div>
			<?php endif ; ?>
			<div class="op_submenu_focus" id="menu_system">System</div>
			<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/account/index.php" ) ): ?><div class="op_submenu" onClick="location.href='../addons/account/index.php'" id="menu_account">Account</div><?php endif ; ?>
			<div style="clear: both"></div>
		</div>

		<?php if ( $db_error ): ?>
			<div class="info_error" style="margin-top: 25px;"><img src="../pics/icons/warning.png" width="16" height="16" border="0" alt=""> Database table has errors.  This will effect your system and some areas will not function properly.  <a href="db.php" style="color: #FFFFFF;">Please review the database informaion to fix the issue.</a></div>
		<?php endif ; ?>

		<div style="margin-top: 25px;">
			<form>
			<div style="float: left; width: 750px;">

				<div class="info_info">
					<table cellspacing=0 cellpadding=5 border=0 width="100%">
					<tr>
						<td nowrap><b>Software License Key:</b> <span class="info_blue_dark"><?php echo $KEY ?></span></td>
						<td width="100%" align="right" style="padding-left: 25px;"></td>
					</tr>
					<tr>
						<td colspan=2 style="padding-top: 25px;">
							<div class="info_blue" style="box-shadow: 0 8px 12px 0 rgba(0,0,0,.24),0 20px 40px 0 rgba(0,0,0,.24);">
								<table cellspacing=0 cellpadding=2 border=0>
								<tr>
									<td width="200">PHP Live! <span class="info_blue_dark">v.<?php echo $VERSION ?></span></td><td><img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="https://www.phplivesupport.com/r.php?plk=pi-24-ysj-m&r=vcheck&v=<?php echo base64_encode( $VERSION ) ?>&k=<?php echo base64_encode( $KEY ) ?>" target="new" style="color: #FFFFFF;">Click Here to check for new software version</a></td>
								</tr>
								</table>
							</div>
							<?php
								if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/geo_data/VERSION.php" ) ):
								include_once( "$CONF[DOCUMENT_ROOT]/addons/geo_data/VERSION.php" ) ;
							?>
							<div class="info_neutral" style="margin-top: 15px;">
								<table cellspacing=0 cellpadding=2 border=0>
								<tr>
									<td width="200"><a href="extras_geo.php">GeoIP addon</a> <span class="info_blue_dark">v.<?php echo $VERSION_GEO ?></span></td><td><img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck_geo&v=<?php echo base64_encode( $VERSION_GEO ) ?>" target="new">check for new version</a></td>
								</tr>
								</table>
							</div>
							<?php endif ; ?>
							<?php
								if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emarketing/API/VERSION.php" ) ):
								include_once( "$CONF[DOCUMENT_ROOT]/addons/emarketing/API/VERSION.php" ) ;
							?>
							<div class="info_neutral" style="margin-top: 15px;">
								<table cellspacing=0 cellpadding=2 border=0>
								<tr>
									<td width="200"><a href="../addons/emarketing/emarketing.php">Email Marketing addon</a> <span class="info_blue_dark">v.<?php echo $EMARKETING_VERSION ?></span></td><td><img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck_emarketing&v=<?php echo base64_encode( $EMARKETING_VERSION ) ?>" target="new">check for new version</a></td>
								</tr>
								</table>
							</div>
							<?php endif ; ?>
							<?php
								if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/export_transcripts/API/VERSION.php" ) ):
								include_once( "$CONF[DOCUMENT_ROOT]/addons/export_transcripts/API/VERSION.php" ) ;
							?>
							<div class="info_neutral" style="margin-top: 15px;">
								<table cellspacing=0 cellpadding=2 border=0>
								<tr>
									<td width="200"><a href="../addons/export_transcripts/export_transcripts.php">Export Transcripts addon</a> <span class="info_blue_dark">v.<?php echo $EXPORTT_VERSION ?></span></td><td><img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck_exportt&v=<?php echo base64_encode( $EXPORTT_VERSION ) ?>" target="new">check for new version</a></td>
								</tr>
								</table>
							</div>
							<?php endif ; ?>
							<?php
								if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/code_mapper/VERSION.php" ) ):
								include_once( "$CONF[DOCUMENT_ROOT]/addons/code_mapper/VERSION.php" ) ;
							?>
							<div class="info_neutral" style="margin-top: 15px;">
								<table cellspacing=0 cellpadding=2 border=0>
								<tr>
									<td width="200"><a href="../addons/code_mapper/code_mapper.php">Code Mapper addon</a> <span class="info_blue_dark">v.<?php echo $CODE_MAPPER_VERSION ?></span></td><td><img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck_codemapper&v=<?php echo base64_encode( $CODE_MAPPER_VERSION ) ?>" target="new">check for new version</a></td>
								</tr>
								</table>
							</div>
							<?php endif ; ?>
						</td>
					</tr>
					</table>
				</div>

				<?php if ( $admininfo["status"] != -1 ): ?>
				<div style="margin-top: 25px; min-height: 150px; max-height: 250px; text-align: justify;" class="info_info">
					<div style="float: left; width: 200px; margin-right: 45px;">
						<div class="edit_title">Temporary Admins</div>
						<div style="margin-top: 15px;">At this time, Temporary Setup Admins have access to all the setup options.</div>
						<div style="margin-top: 15px;"><button type="button" onClick="generate_admin()" id="btn_generate" class="btn">Create Temp Admin</button></div>

						<div style="margin-top: 15px;"><img src="../pics/icons/info.png" width="16" height="16" border="0" alt=""> Temporary Setup Admin accounts that have not been accessed in over 6 months will be automatically deleted.</div>
					</div>
					<div style="float: left; width: 450px;">
						<div style="margin-top: 15px; min-height: 145px; max-height: 245px; overflow-x: hidden; overflow-y: auto;" id="div_admins"></div>
					</div>
					<div style="clear: both;"></div>
				</div>
				<?php else: ?>
				<div style="margin-top: 25px; text-align: justify;" class="info_info">
					This account is a Temporary Admin.  Creation of additional Temporary Admin is not available for this account.
				</div>
				<?php endif ; ?>

			</div>
			<div style="float: left; border: 0px solid transparent; margin-left: 25px; text-align: right;">
				System Installed on:
				<div style="margin-top: 5px; margin-bottom: 20px; font-size: 16px;">
					<?php echo $created ?>
					<div style="margin-top: 5px; font-size: 12px;">(<?php echo ( $days_running ) ? $days_running : 1 ; ?> days)</div>
				</div>

				<div style="" class="info_neutral"><a href="db.php"><img src="../pics/icons/db.png" width="16" height="16" border="0" alt="" class="info_blue_dark"> View Database Stats</a></div>
				<div style="margin-top: 25px; width: 170px; overflow: auto;" class="info_neutral">
					Server <a href="http://www.php.net/" target="_blank">PHP version</a>: <?php echo $pv ?>
					<div style="margin-top: 15px;">
						<a href="http://php.net/manual/en/reserved.constants.php#constant.php-int-max" target="_blank">PHP_INT_MAX</a>
						<div style="font-weight: bold; margin-top: 10px;"><?php echo PHP_INT_MAX; ?></div>
					</div>
				</div>
			</div>
			<div style="clear:both;"></div>
			</form>

		</div>

<?php include_once( "./inc_footer.php" ) ?>