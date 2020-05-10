<?php
	/***************************************/
	//
	//
	// PHP Live! Support
	//
	// https://www.phplivesupport.com
	//
	/***************************************/
	include_once( "./web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Vars/get.php" ) ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	use GeoIp2\Database\Reader ;

	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$deptid_orig = Util_Format_Sanatize( Util_Format_GetVar( "deptid_orig" ), "n" ) ;
	$gid = Util_Format_Sanatize( Util_Format_GetVar( "gid" ), "n" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
	$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;
	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "lns" ) ;
	$auto_pop = Util_Format_Sanatize( Util_Format_GetVar( "auto_pop" ), "n" ) ;
	$popout = Util_Format_Sanatize( Util_Format_GetVar( "popout" ), "n" ) ;
	$vname = Util_Format_Sanatize( Util_Format_GetVar( "vname" ), "ln" ) ; if ( ( preg_replace( "/ /", "", $vname ) == "" ) || ( $vname == "null" ) ) { $vname = "Visitor" ; }
	$vemail = Util_Format_Sanatize( Util_Format_GetVar( "vemail" ), "e" ) ; $vemail = ( !$vemail ) ? "null" : $vemail ;
	$vsubject = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "vsubject" ), "htmltags" ) ) ;
	$question = Util_Format_Sanatize( Util_Format_GetVar( "vquestion" ), "htmltags" ) ;
	$onpage = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "onpage" ), "url" ) ) ;  $onpage = ( $onpage ) ? $onpage : "" ;
	$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "title" ) ; $title = ( $title ) ? $title : "" ;
	$resolution = Util_Format_Sanatize( Util_Format_GetVar( "win_dim" ), "ln" ) ;
	$embed = Util_Format_Sanatize( Util_Format_GetVar( "embed" ), "n" ) ;
	$custom = Util_Format_Sanatize( Util_Format_GetVar( "custom" ), "htmltags" ) ;
	$token = Util_Format_Sanatize( Util_Format_GetVar( "token" ), "ln" ) ;
	$skp = Util_Format_Sanatize( Util_Format_GetVar( "skp" ), "n" ) ;
	$proid = Util_Format_Sanatize( Util_Format_GetVar( "proid" ), "ln" ) ;
	$api_key = Util_Format_Sanatize( Util_Format_GetVar( "api_key" ), "ln" ) ;
	$emarketid = Util_Format_Sanatize( Util_Format_GetVar( "emarketid" ), "n" ) ;
	if ( $skp ) { $vname = "Visitor" ; $vemail = "null" ; $question = "" ; }
	$custom_hash = Array() ;
	if ( $custom )
	{
		$custom_pairs = explode( "-cus-", $custom ) ;
		for ( $c = 0; $c < count( $custom_pairs ); ++$c )
		{
			if ( $custom_pairs[$c] )
			{
				LIST( $custom_var_name, $custom_var_val ) = explode( "-_-", $custom_pairs[$c] ) ;
				if ( $custom_var_name && ( !isset( $custom_hash[$custom_var_name] ) || !$custom_hash[$custom_var_name] ) )
					$custom_hash[$custom_var_name] = $custom_var_val ;
			}
		}
		$custom = "" ;
		foreach ( $custom_hash as $custom_var_name => $custom_var_val )
			$custom .= "$custom_var_name-_-$custom_var_val-cus-" ;
	} $custom = rawurldecode( $custom ) ;
	
	if ( is_file( "$CONF[CONF_ROOT]/addons.php" ) ) { include_once( "$CONF[CONF_ROOT]/addons.php" ) ; }

	$recaptchas = ( isset( $VALS_ADDONS ) && isset( $VALS_ADDONS["recaptcha"] ) ) ? unserialize( $VALS_ADDONS["recaptcha"] ) : Array() ;
	if ( isset( $recaptchas["active_string"] ) && $recaptchas["active_string"] && is_file( "$CONF[DOCUMENT_ROOT]/addons/recaptcha/config_recaptcha.php" ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/addons/recaptcha/config_recaptcha.php" ) ;
		if ( isset( $RECAPTCHA_SITE_KEY ) && isset( $RECAPTCHA_SECRET_KEY ) && ( $recaptchas["active_string"] == md5( $RECAPTCHA_SITE_KEY.$RECAPTCHA_SECRET_KEY ) ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/addons/recaptcha/inc_google_php.php" ) ;
			if( $response_data->success ) { }
			else
			{
				$deptid = $deptid_orig ;
				HEADER( "location: phplive.php?embed=$embed&popout=$popout&d=$deptid&token=$token&pg=".base64_encode($onpage)."&tl=".base64_encode( $title )."&custom=".rawurlencode( $custom )."&vquestion=".rawurlencode( $question )."&r=2&".$now ) ;
				exit ;
			}
		}
	}

	if ( isset( $VALS_ADDONS ) && isset( $VALS_ADDONS["proaction"] ) )
	{
		$proactions = unserialize( base64_decode( $VALS_ADDONS["proaction"] ) ) ;
		if ( isset( $proactions[$proid] ) ) { $custom .= "ProAction ID-_-$proid-cus-" ; }
	}

	$lang = ( isset( $CONF["lang"] ) ) ? $CONF["lang"] : "english" ; $dev = 0 ;
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $ip, $vis_token ) = Util_IP_GetIP( $token ) ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;
	if ( $geoip )
	{
		if ( ( phpversion() >= 5.4 ) && is_file( "$CONF[DOCUMENT_ROOT]/addons/geo_data/vendor/autoload.php" ) )
		{
			require "$CONF[DOCUMENT_ROOT]/addons/geo_data/vendor/autoload.php" ;
			$reader = new Reader( "$CONF[DOCUMENT_ROOT]/addons/geo_data/GeoLite2-City.mmdb" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/inc_geo.php" ) ;
		}
		$spam_country = isset( $VALS["CHAT_SPAM_COUNTRY"] ) ? unserialize( $VALS['CHAT_SPAM_COUNTRY'] ) : Array() ; $request_country = isset( $geo_country_code ) ? strtoupper( $geo_country_code ) : "" ;
		if ( $request_country && isset( $spam_country[$request_country] ) ) { $deptid = 0 ; $opid = 0 ; }
	}
	if ( $embed ) { $vis_token = Util_Format_Sanatize( Util_Format_GetVar( "vis_token" ), "ln" ) ; }

	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/get.php" ) ;

	/**************************************/
	/* fetch direct additonal check BEGIN */
	if ( $api_key && ( $api_key == $CONF['API_KEY'] ) )
	{
		if ( $opid )
		{
			$opinfo_next = Ops_get_OpInfoByID( $dbh, $opid ) ;
			if ( !isset( $opinfo_next["opID"] ) || !$opinfo_next["status"] ) { $opid = 0 ; unset( $opinfo_next ) ; }
			else
			{
				$deptid_found = 0 ;
				$op_depts = Ops_get_OpDepts( $dbh, $opinfo_next["opID"] ) ;
				for ( $c = 0; $c < count( $op_depts ); ++$c )
				{
					if ( $op_depts[$c]["deptID"] == $deptid ) { $deptid_found = 1 ; break ; }
				}
				if ( !$deptid_found )
				{
					for ( $c = 0; $c < count( $op_depts ); ++$c )
					{
						if ( $op_depts[$c]["status"] ) { $deptid = $op_depts[$c]["deptID"] ; $deptid_found = 1 ; break ; }
					}
				}
				if ( !$deptid_found )
				{
					// department not found.  set to zero for redirect
					$deptid = 0 ;
				} else { $deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ; $theme = "" ; }
			}
		}
		else if ( $deptid )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
			$theme = "" ;
			$ops_are_online = Ops_get_itr_AnyOpsOnline( $dbh, $deptid ) ;
			if ( $ops_are_online ) { $deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ; }
		}
	}
	else
	{
		// normal processing of chat request to a department
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	}
	/* fetch direct additonal check END */
	/************************************/

	$dept_themes = ( isset( $VALS["THEMES"] ) ) ? unserialize( $VALS["THEMES"] ) : Array() ;
	if ( !$theme && isset( $dept_themes[$deptid] ) && $deptid ) { $theme = $dept_themes[$deptid] ; }
	else if ( !$theme ) { $theme = $CONF["THEME"] ; }
	else if ( $theme && !is_file( "$CONF[DOCUMENT_ROOT]/themes/$theme/style.css" ) ) { $theme = $CONF["THEME"] ; }
	if ( !is_file( "$CONF[DOCUMENT_ROOT]/themes/$theme/style.css" ) ) { $theme = "default" ; }

	if ( !$deptid || !isset( $deptinfo["deptID"] ) )
	{
		if ( $deptid && !isset( $deptinfo["deptID"] ) )
		{
			// to limit infinite loop situation when department does not exist or was deleted
			LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;
			$query = "DELETE FROM p_requests WHERE deptID = $deptid" ;
			database_mysql_query( $dbh, $query ) ;
		}
		database_mysql_close( $dbh ) ;
		// If department does not exist or not found, route to chat request window
		if ( $gid ) { $deptid = $gid ; }
		HEADER( "location: phplive.php?embed=$embed&popout=$popout&d=$deptid&token=$token&theme=$theme&pg=".base64_encode($onpage)."&tl=".base64_encode( $title )."&custom=".rawurlencode( $custom )."&vquestion=".rawurlencode( $question )."&r=1&".$now ) ;
		exit ;
	} $opid_direct = $opid ; $opid = 0 ;

	if ( $deptinfo["smtp"] ) { $smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ; }
	if ( $deptinfo["lang"] ) { $CONF["lang"] = $deptinfo["lang"] ; }
	$lang = Util_Format_Sanatize( $CONF["lang"], "ln" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;

	$queueinfo = Array() ; $ops_d_string = "" ; $q_opids_js = "" ;
	$query = "SELECT queueID, ces, ops_d FROM p_queue WHERE md5_vis = '$vis_token' LIMIT 1" ;
	database_mysql_query( $dbh, $query ) ;
	$queueinfo = database_mysql_fetchrow( $dbh ) ;
	if ( !isset( $queueinfo["ces"] ) && $ces )
	{
		$query = "SELECT queueID, ces, ops_d FROM p_queue WHERE ces = '$ces' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;
		$queueinfo = database_mysql_fetchrow( $dbh ) ;
	}
	if ( isset( $queueinfo["ces"] ) )
	{
		$ces = $queueinfo["ces"] ;
		$ops_d_string = $queueinfo["ops_d"] ;
		$query = "SELECT * FROM p_req_log WHERE ces = '$ces' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;
		$requestinfo = database_mysql_fetchrow( $dbh ) ;
	}

	if ( !isset( $requestinfo["ces"] ) )
	{
		$requestinfo = Chat_get_itr_RequestGetInfo( $dbh, $embed, $ces, $vis_token ) ;
		if ( isset( $requestinfo["ces"] ) )
		{
			$ces = $requestinfo["ces"] ;
			$ops_d_string = $requestinfo["rstring"] ;
		}
		else
		{
			$query = "SELECT * FROM p_requests WHERE md5_vis = '$vis_token' OR md5_vis_ = '$vis_token' LIMIT 1" ;
			database_mysql_query( $dbh, $query ) ;
			$requestinfo = database_mysql_fetchrow( $dbh ) ;
			if ( isset( $requestinfo["ces"] ) )
			{
				$ces = $requestinfo["ces"] ;
				$ops_d_string = $requestinfo["rstring"] ;
			}
		}
	}
	if ( $ops_d_string )
	{
		$ops_d_array = explode( ",", $ops_d_string ) ;
		for ( $c = 0; $c < count( $ops_d_array ); ++$c )
		{
			if ( $ops_d_array[$c] )
			{
				$this_opid = $ops_d_array[$c] ;
				$q_opids_js .= "chats[ces][\"q_opids\"][$this_opid] = 1 ; " ;
			}
		}
	}

	$vses = $t_vses = 1 ; $connected = $created_embed = 0 ; $connected_trans = $text = "" ; $refer = "" ; $marketid = 0 ;
	if ( isset( $queueinfo["ces"] ) )
	{
		$vname = $requestinfo["vname"] ;
		$vemail = $requestinfo["vemail"] ;
		$question = $requestinfo["question"] ;
		$requestid = 1 ; // dummy id
	}
	else if ( isset( $requestinfo["requestID"] ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;

		$vname = $requestinfo["vname"] ;
		$vemail = $requestinfo["vemail"] ;
		$question = $requestinfo["question"] ;

		$requestid = $requestinfo["requestID"] ;
		$t_vses = $requestinfo["t_vses"] ;
		$vses = $t_vses + 1 ;
		Chat_update_RequestValue( $dbh, $requestid, "t_vses", $vses ) ;

		if ( $vses > $VARS_MAX_EMBED_SESSIONS ) { $vses = $vses - $VARS_MAX_EMBED_SESSIONS ; }
		function get_diff( $x, $y ) { return $x-$y ; }
		$diff = get_diff( $vses, $VARS_MAX_EMBED_SESSIONS ) ;
		while( $diff > 0 )
		{
			$vses = $diff ;
			$diff = get_diff( $diff, $VARS_MAX_EMBED_SESSIONS ) ;
		}

		if ( $requestinfo["status"] && is_file( "$CONF[CHAT_IO_DIR]/$ces.txt" ) )
		{
			$connected = 1 ;
			$created_embed = $requestinfo["created"] ;

			$rid = "0_$vses" ;
			$filename = $ces."-".$rid ;

			if ( is_file( "$CONF[CHAT_IO_DIR]/$filename.text" ) )
				@unlink( "$CONF[CHAT_IO_DIR]/$filename.text" ) ;

			$output = UtilChat_ExportChat( "$ces.txt" ) ;
			$text = "" ;
			if ( isset( $output[0] ) )
			{
				$text = addslashes( preg_replace( "/\"/", "&quot;", $output[0] ) ) ;
				$text = preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $text ) ;
			}
		}

		if ( !$requestinfo["status"] && $requestinfo["initiated"] )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;

			$opid = $requestinfo["opID"] ;
			// log DB table biggest, update first
			Chat_update_RequestLogValue( $dbh, $requestinfo["ces"], "status", 1 ) ;

			$text_joined = "<div class='ca'><b>Visitor</b> ".$LANG["CHAT_NOTIFY_JOINED"]."</div>" ;
			$filename = $requestinfo["ces"]."-".$requestinfo["opID"] ;
			UtilChat_AppendToChatfile( "$filename.text", base64_encode( $text_joined ) ) ;

			Footprints_update_FootprintUniqueValue( $dbh, $vis_token, "chatting", 1 ) ;
			Chat_update_RequestValue( $dbh, $requestinfo["requestID"], "status", 1 ) ;
			UtilChat_AppendToChatfile( $requestinfo["ces"].".txt", base64_encode( $text_joined ) ) ;
		}
	}
	else
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_ext.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_ext.php" ) ;

		$vname_orig = $vname ; $requestid = 0 ;
		$ces = Util_Functions_ext_GenerateCes( $dbh ) ;
		if ( !isset( $CONF["cookie"] ) || ( $CONF["cookie"] == "on" ) )
		{
			if ( $vname_orig && ( $vname_orig != "null" ) && ( $vname_orig != "Visitor" ) ) { Util_Format_SetCookie( "phplivevname", $vname_orig, $now+60*60*24*365, "/", "", $PHPLIVE_SECURE ) ; }
			if ( $vemail && ( $vemail != "null" ) ) { Util_Format_SetCookie( "phplivevemail", $vemail, $now+60*60*24*365, "/", "", $PHPLIVE_SECURE ) ; }
		}

		$referinfo = Footprints_get_IPRefer( $dbh, $vis_token ) ;
		$marketid = ( isset( $referinfo["marketID"] ) && $referinfo["marketID"] ) ? $referinfo["marketID"] : 0 ;
		$refer = ( isset( $referinfo["refer"] ) ) ? rawurlencode( $referinfo["refer"] ) : "" ;
	}

	$deptvars = Depts_get_DeptVars( $dbh, $deptid ) ;

	$stars_five = Util_Functions_Stars( ".", 5 ) ; $stars_four = Util_Functions_Stars( ".", 4 ) ; $stars_three = Util_Functions_Stars( ".", 3 ) ; $stars_two = Util_Functions_Stars( ".", 2 ) ; $stars_one = Util_Functions_Stars( ".", 1 ) ;

	if ( !isset( $VALS["PRINTER_ICON"] ) ) { $VALS["PRINTER_ICON"] = "on" ; }
	$print_option = ( !$mobile && ( $VALS["PRINTER_ICON"] == "on" ) ) ? "<span style='padding-right: 25px;'><img src='./themes/$theme/printer.png' width='16' height='16' border='0' alt='' onClick='do_print(ces, $deptinfo[deptID], 0, ".($VARS_CHAT_WIDTH+100).", ".($VARS_CHAT_HEIGHT+100).")' title='$LANG[CHAT_PRINT]' alt='$LANG[CHAT_PRINT]' style='cursor: pointer;'></span>" : "" ;
	$email_display = ( $vemail != "null" ) ? $vemail : "" ;
	$text_comment = isset( $LANG["TXT_COMMENT"] ) ? $LANG["TXT_COMMENT"] : "Comment" ;
	$text_rating = isset( $LANG["TXT_RATING"] ) ? $LANG["TXT_RATING"] : "Rating" ;
	$chat_end_message = ( isset( $deptvars["end_chat_msg"] ) && $deptvars["end_chat_msg"] ) ? "<div style='margin-top: 5px;'>".preg_replace( "/(\r\n)|(\n)|(\r)/", "", $deptvars["end_chat_msg"] )."</div>" : "" ;
	$new_response_image = ( file_exists( "$CONF[DOCUMENT_ROOT]/themes/$theme/new_response.png" ) ) ? "./themes/$theme/new_response.png" : "/themes/initiate/new_response.png" ;

	$qtexts = ( isset( $deptvars["qtexts"] ) ) ? unserialize( $deptvars["qtexts"] ) : Array("CHAT_QUEUE_EST" => "Estimated wait time is about",  "CHAT_QUEUE_EST_MIN" => "minutes", "CHAT_QUEUE_POS" => "Queue Position: ") ;
	$qlimit = ( isset( $deptvars["qlimit"] ) ) ? $deptvars["qlimit"] : 5 ;

	$dept_emo = ( isset( $VALS["EMOS"] ) ) ? unserialize( $VALS["EMOS"] ) : Array() ;
	$addon_emo = 0 ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) )
	{
		if ( !isset( $dept_emo[$deptid] ) || ( isset( $dept_emo[$deptid] ) && $dept_emo[$deptid] ) ) { $addon_emo = 1 ; }
		else if ( isset( $dept_emo[$deptid] ) && !$dept_emo[$deptid] ) { $addon_emo = 0 ; }
		else if ( !isset( $dept_emo[0] ) || ( isset( $dept_emo[0] ) && $dept_emo[0] ) ) { $addon_emo = 1 ; }
	}
	$autolinker_js_file = ( isset( $VARS_JS_AUTOLINK_FILE ) && ( ( $VARS_JS_AUTOLINK_FILE == "min" ) || ( $VARS_JS_AUTOLINK_FILE == "src" ) ) ) ? "autolinker_$VARS_JS_AUTOLINK_FILE.js" : "autolinker_min.js" ;

	$attach_icon = "$CONF[DOCUMENT_ROOT]/themes/$theme/attach.png" ;
	if ( is_file( $attach_icon ) ) { $attach_icon = "$CONF[BASE_URL]/themes/$theme/attach.png?$VERSION" ; }
	else { $attach_icon = "$CONF[BASE_URL]/pics/icons/attach.png" ; }
	$typing_icon = "$CONF[DOCUMENT_ROOT]/themes/$theme/typing.png" ;
	if ( is_file( $typing_icon ) ) { $typing_icon = "$CONF[BASE_URL]/themes/$theme/typing.png?$VERSION" ; }
	else { $typing_icon = "$CONF[BASE_URL]/pics/icons/typing.png" ; }

	$can_upload = ( $deptinfo["vupload"] ) ? 1 : 0 ;
	$addon_ws = 0 ; $WS_VERSION = "1.0" ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/ws/ws.php" ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/addons/ws/API/VERSION.php" ) ;
		$addon_ws = 1 ;
	}
	$LANG_DB = Lang_get_Lang( $dbh, $deptid ) ;
	if ( isset( $LANG_DB["deptID"] ) )
	{
		$db_lang_hash = unserialize( $LANG_DB["lang_vars"] ) ;
		$LANG = array_merge( $LANG, $db_lang_hash ) ;
	}
	if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emarketing/emarketing.php" ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/addons/emarketing/inc_save_response.php" ) ;
	} $salt = md5( md5( $CONF["SALT"] ).$ces ) ;
	$emlogos_hash = ( isset( $VALS["EMLOGOS"] ) ) ? unserialize( $VALS["EMLOGOS"] ) : Array() ;
?>
<?php include_once( "./inc_doctype.php" ) ?>
<?php if ( isset( $CONF["KEY"] ) && ( $CONF["KEY"] == md5($KEY."_key_phplive") ) ): ?><?php else: ?>
<!--
********************************************************************
* (c) PHP Live!
* www.phplivesupport.com
********************************************************************
-->
<?php endif ; ?>
<head>
<title> <?php echo urldecode( $LANG["CHAT_WELCOME"] ) ?> </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="noindex,nofollow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>">
<?php include_once( "./inc_meta_dev.php" ) ; ?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>

<link rel="Stylesheet" href="./themes/<?php echo $theme ?>/style.css?<?php echo filemtime ( "./themes/$theme/style.css" ) ; ?>">

</head>
<body style="-webkit-text-size-adjust: 100%; opacity:0.0; filter:alpha(opacity=00);">

<div id="chat_canvas" style="min-height: 100%; width: 100%;" onClick="clear_flash_console();">
	<?php include_once( "inc_embed_menu.php" ) ; ?>
	<div id="chat_canvas_content" style="padding: 10px;">
		<div id="chat_profile_pic" style="margin-bottom: 5px; padding: 10px; height: 55px; width: 92%;">
			<table cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td valign="top" width="55" id="td_chat_profile_pic_img" style="display: none; padding-right: 15px;"><div id="chat_profile_pic_img"><img src="pics/profile.png" width="55" height="55" border="0" alt="" class="profile_pic_img"></div></td>
				<td valign="top" style="white-space: nowrap;">
					<div style="white-space: nowrap;">
						<div style="font-weight: bold;" id="chat_profile_name"></div>
						<div style="margin-top: 5px;" style="" id="chat_department_name"><?php echo $deptinfo["name"] ?></div>
						<div id="chat_vtimer_wrapper" style="margin-top: 5px; <?php echo ( !$deptinfo["ctimer"] ) ? "display: none; " : "" ; ?>"><span style="font-weight: normal; display: inline-block; padding: 2px;" id="chat_vtimer">00:00</span></div>
					</div>
				</td>
				<?php if ( !$embed || ( $embed && isset( $emlogos_hash[0] ) && $emlogos_hash[0] ) ): ?>
				<td style="padding-left: 15px;" width="100%" align="right"><img src="<?php echo ( $gid ) ? Util_Upload_GetLogo( "logo", $gid ) : Util_Upload_GetLogo( "logo", $deptid ) ; ?>" border=0 style="max-width: 100%; max-height: 55px;"></td>
				<?php endif ; ?>
			</tr>
			</table>
		</div>
		<div id="chat_body" style="margin-top: 5px; overflow: auto; width: 92%; padding: 10px; height: 60px; word-break: break-word; word-wrap: break-word;" onClick="close_misc('all')"></div>
	</div>
</div>

<div id="chat_input_wrapper" style="position: fixed; bottom: 0px; left: 0px; padding-bottom: 15px; padding-top: 5px; width: 100%; z-Index: 131;">
	<div style="padding-left: 15px; padding-right: 15px;">
		<div id="chat_options" style="padding: 5px; height: 16px; display: inline-block; white-space: nowrap;">
			<span id="options_visitor" style="display: none;">
				<span id="chat_sound_wrapper" style="display: none; padding-right: 25px;"><img src="./themes/<?php echo $theme ?>/sound_on.png" width="16" height="16" border="0" alt="" onClick="toggle_chat_sound('<?php echo $theme ?>')" id="chat_sound" title="<?php echo $LANG["CHAT_SOUND"] ?>" alt="<?php echo $LANG["CHAT_SOUND"] ?>" style="cursor: pointer;"></span>
				<span id="options_expand" style="<?php echo ( $mobile || 1 ) ? "display: none; " : "" ; ?> padding-right: 20px;"><img src="./themes/<?php echo $theme ?>/slider_v.png" width="16" height="16" border="0" alt="" onClick="toggle_input_text()" id="chat_expand" title="" alt="" style="cursor: pointer;"></span>
				<span style="display: none; padding-right: 25px;" id="span_emoticons"><img src="<?php echo $CONF["BASE_URL"] ?>/addons/emoticons/smile.png" width="16" height="16" border="0" title="emoji" alt="emoji" style="cursor: pointer;" id="chat_emoticons" onClick="toggle_emo_box(0);close_misc('attach');close_misc('trans');close_misc('rating');"></span>
				<?php if ( $VARS_INI_UPLOAD && $can_upload && is_file( "$CONF[DOCUMENT_ROOT]/addons/file_attach/file_attach.php" ) ): ?><span style="padding-right: 25px;" id="chat_file_attach"><img src="<?php echo $attach_icon ?>" width="16" height="16" border="0" onClick="toggle_file_attach(0);close_misc('emo');close_misc('trans');close_misc('rating');" title="file attachment" alt="file attachment" style="cursor: pointer;"></span><?php endif ; ?>
				<span id="chat_email" style="display: none; padding-right: 25px;"><img src="./themes/<?php echo $theme ?>/email.png" width="16" height="16" border="0" onClick="toggle_send_trans(0);close_misc('emo');close_misc('attach');close_misc('rating');" alt="<?php echo $LANG["CHAT_BTN_EMAIL_TRANS"] ?>" title="<?php echo $LANG["CHAT_BTN_EMAIL_TRANS"] ?>" style="cursor: pointer;"></span>
				<?php echo $print_option ?>
			</span>
			<span id="chat_processing" style="display: none; padding-right: 25px;"><img src="./themes/<?php echo $theme ?>/loading_chat.gif" width="16" height="16" border="0" alt="loading..." title="loading..."></span>
			<span id="chat_vname" style="display: none; position: relative; top: -2px; padding-right: 15px;"></span>
			<span id="chat_vistyping_wrapper" style="display: none;"><span id="chat_vistyping" style="display: none;"><img src="<?php echo $typing_icon ?>" width="16" height="16" border="0" alt="<?php echo $LANG["TXT_TYPING"] ?>" title="<?php echo $LANG["TXT_TYPING"] ?>"></span></span>
			<span style=""><img src="./pics/space.gif" width="1" height="1" border="0" alt="" title=""></span>
		</div>
	</div>
	<div style="padding: 10px;" id="chat_input_text_wrapper">
		<table cellspacing=0 cellpadding=0 border=0 width="96%">
		<tr>
			<td><textarea id="input_text" rows="2" style="height: 45px; width: 100%; resize: none;" wrap="virtual" onKeyup="input_text_listen(event);" onKeydown="input_text_typing(event);clear_flash_console();" onFocus="clear_flash_console();" <?php echo ( isset( $VALS["AUTOCORRECT_V"] ) && !$VALS["AUTOCORRECT_V"] ) ? "autocomplete=\"off\" autocorrect=\"off\"" : "" ; ?> disabled><?php echo urldecode( $LANG["TXT_CONNECTING"] ) ?></textarea></td>
			<td valign="top" width="104">
				<div style="padding-left: 25px;">
					<button id="input_btn" type="button" class="input_button" style="<?php echo ( $mobile ) ? "" : "width: 104px; height: 45px; font-size: 14px; font-weight: bold;" ?>" OnClick="add_text_prepare(1)"><?php echo urldecode( $LANG["TXT_SUBMIT"] ) ?></button>
					<div id="sounds" style="width: 1px; height: 1px; overflow: hidden; opacity:0.0; filter:alpha(opacity=0);">
						<span id="div_sounds_new_text"></span>
						<audio id='div_sounds_audio_new_text'></audio>
					</div>
				</div>
			</td>
		</tr>
		</table>
	</div>
</div>
<div id="chat_survey_wrapper" class='cs' style="display: none; position: absolute; bottom: 0px; left: 0px; width: 100%; padding: 0px; margin: 0px; cursor: pointer; -webkit-box-shadow: -0px 7px 29px rgba(0, 0, 0, 0.34); -moz-box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); z-Index: 151;" onClick="toggle_rating(0);close_misc('attach');close_misc('trans');close_misc('emo');">
	<div class="ctitle" style="padding: 25px; padding-top: 20px; text-align: center;"><div class="info_box"><?php echo urldecode( $LANG["CHAT_NOTIFY_RATE"] ) ?></div></div>
</div>
<div id="chat_survey_rating_wrapper" class="info_content" style="display: none; position: absolute; top: 0px; left: 0px; padding: 2px; width: 300px; overflow: auto; -webkit-box-shadow: -0px 7px 29px rgba(0, 0, 0, 0.34); -moz-box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); z-Index: 500;">
	<div style="text-align: center; cursor: pointer;" class="info_error" onClick="close_misc('all')"><img src="<?php echo $CONF["BASE_URL"] ?>/themes/<?php echo $theme ?>/close_extra.png" width="16" height="16" border="0"> <?php echo ( isset( $LANG["CHAT_CLOSE"] ) ) ? urldecode( $LANG["CHAT_CLOSE"] ) : "Close" ; ?></div>
	<div id="chat_survey_rating" style="margin-top: 5px; padding: 10px;">
		<div style="padding: 4px;" id="div_chat_rate_title"><?php echo urldecode( $LANG["CHAT_NOTIFY_RATE"] ) ?></div>
		<div style="margin-top: 10px; padding-bottom: 15px;">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td valign="top" width="110">
					<div style="">
						<table cellspacing=0 cellpadding=4 border=0 style="">
						<tr><td><input type="radio" name="rating" id="rating_5" value=5 onClick="submit_survey(5, survey_texts)"></td><td style="padding-left: 2px; cursor: pointer;" onClick="submit_survey(5, survey_texts);$('#rating_5').prop('checked', true);"><?php echo $stars_five ?></td></tr>
						<tr><td><input type="radio" name="rating" id="rating_4" value=4 onClick="submit_survey(4, survey_texts)"></td><td style="padding-left: 2px; cursor: pointer;" onClick="submit_survey(4, survey_texts);$('#rating_4').prop('checked', true);"><?php echo $stars_four ?></td></tr>
						<tr><td><input type="radio" name="rating" id="rating_3" value=3 onClick="submit_survey(3, survey_texts)"></td><td style="padding-left: 2px; cursor: pointer;" onClick="submit_survey(3, survey_texts);$('#rating_3').prop('checked', true);"><?php echo $stars_three ?></td></tr>
						<tr><td><input type="radio" name="rating" id="rating_2" value=2 onClick="submit_survey(2, survey_texts)"></td><td style="padding-left: 2px; cursor: pointer;" onClick="submit_survey(2, survey_texts);$('#rating_2').prop('checked', true);"><?php echo $stars_two ?></td></tr>
						<tr><td><input type="radio" name="rating" id="rating_1" value=1 onClick="submit_survey(1, survey_texts)"></td><td style="padding-left: 2px; cursor: pointer;" onClick="submit_survey(1, survey_texts);$('#rating_1').prop('checked', true);"><?php echo $stars_one ?></td></tr>
						</table>
					</div>
				</td>
				<td valign="top" style="padding-left: 15px;">
					<?php echo $text_comment ?>:
					<div style="margin-top: 5px;"><textarea rows="3" style="width: 88%; resize: none;" id="vcomment" class="input_text vcomment"></textarea></div>
					<div style="margin-top: 5px;"><input type="button" id="btn_comment" value="<?php echo urldecode( $LANG["TXT_SUBMIT"] ) ?>" onClick="send_comment()" class="input_op_button"></div>
				</td>
			</tr>
			</table>
		</div>
	</div>
</div>

<div id="info_disconnect" style="position: absolute; top: 0px; right: 0px; text-align: center; z-Index: 130;" onClick="disconnect(1, undeefined, 1);"><img src="./themes/<?php echo $theme ?>/close_extra.png" width="14" height="14" border="0" alt=""> <span id="info_disconnect_text"><?php echo $LANG["TXT_DISCONNECT"] ?></span></div>

<div id="send_transcript_box" style="display: none; position: absolute; top: 0px; left: 0px; padding: 2px; width: 240px; height: 180px; overflow: auto; -webkit-box-shadow: -0px 7px 29px rgba(0, 0, 0, 0.34); -moz-box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); z-Index: 500;" class="info_content">
	<div style="text-align: center; cursor: pointer;" class="info_error" onClick="close_misc('all')"><img src="<?php echo $CONF["BASE_URL"] ?>/themes/<?php echo $theme ?>/close_extra.png" width="16" height="16" border="0"> <?php echo ( isset( $LANG["CHAT_CLOSE"] ) ) ? $LANG["CHAT_CLOSE"] : "Close" ; ?></div>
	<div style="margin-top: 5px; padding: 10px;">
		<div><?php echo urldecode( $LANG["TXT_EMAIL"] ) ?>:<br><input type='text' class='input_text' style='width: 85%;' maxlength='160' id='vemail' name='vemail' value='<?php echo $email_display ?>'></div>
		<div style="margin-top: 15px;">
			<input type='button' id='btn_email' value='<?php echo $LANG["CHAT_BTN_EMAIL_TRANS"] ?>' onClick='send_email(0)'>
			<?php if ( !$mobile && 0 ): ?>
				<div style="margin-top: 25px;"><a href="JavaScript:void(0)" id="link_download" onClick="send_email(1)"><?php echo isset( $LANG["TXT_DOWNLOAD"] ) ? $LANG["TXT_DOWNLOAD"] : "download" ; ?></a><a href="" download="chat_transcript_ID_<?php echo $ces ?>" id="link_download_doit" target="_blank" style="display: none;"></a></div>
			<?php endif ; ?>
		</div>
	</div>
</div>

<script data-cfasync="false" type="text/javascript" src="./js/global.js?<?php echo filemtime ( "./js/global.js" ) ; ?>"></script>
<script data-cfasync="false" type="text/javascript" src="./js/youtube-vimeo-url-parser.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="./js/framework.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="./js/jquery_md5.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="./js/modernizr.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="./js/paste_upload.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="./js/<?php echo $autolinker_js_file ?>?<?php echo $VERSION ?>"></script>
<?php if ( $addon_ws ): ?><script data-cfasync="false" type="text/javascript" src="./addons/ws/js/ws.js?<?php echo $WS_VERSION ?>"></script><?php endif ; ?>

<script data-cfasync="false" type="text/javascript">
<!--
	"use strict" ;
	var version = "<?php echo $VERSION ?>" ;
	var base_url = "." ;
	var base_url_full = "<?php echo $CONF["BASE_URL"] ?>" ;  var conf_extend = "<?php echo ( isset( $CONF_EXTEND ) && $CONF_EXTEND ) ? $CONF_EXTEND : "" ; ?>" ;
	var dev = <?php echo $dev ?> ;
	var phplive_proto = ( location.href.indexOf("https") == 0 ) ? 1 : 0 ; // to avoid JS proto error, use page proto for areas needing to access the JS objects
	if ( !phplive_proto && ( base_url_full.match( /http/i ) == null ) ) { base_url_full = "http:"+base_url_full ; }
	else if ( phplive_proto && ( base_url_full.match( /https/i ) == null ) ) { base_url_full = "https:"+base_url_full ; }
	var proto = phplive_proto ;

	var isop = 0 ; var isop_ = 11111111111 ; var isop__ = 0 ; var opc = 0 ; var opid_direct = <?php echo $opid_direct ?> ;
	var cname = "<?php echo $vname ?>" ; var cemail = "<?php echo $vemail ?>" ;
	var ces = "<?php echo $ces ?>" ;
	var st_typing, st_flash_console ;
	var si_title, si_typing, si_chat_body_resize, si_textarea, si_istyping_clear ;
	var deptid = <?php echo $deptinfo["deptID"] ?> ;
	var temail = <?php echo $deptinfo["temail"] ?> ;
	var rtype = <?php echo $deptinfo["rtype"] ?> ;
	var rtime = <?php echo $deptinfo["rtime"] ?> ; var rstring = "<?php echo $ops_d_string ?>" ;
	var rloop = <?php echo ( $deptinfo["rloop"] ) ? $deptinfo["rloop"] : 1 ; ?> ;
	var loop = <?php echo ( isset( $requestinfo["rloop"] ) ) ? $requestinfo["rloop"] : 1 ?> ;
	var queue = parseInt( <?php echo $deptinfo["queue"] ?> ) ;
	if ( opid_direct && ( rtype == 3 ) ) { rloop = 1 ; rtype = 2 ; }
	var queue_processed = 0 ;
	var inqueue = <?php echo isset( $queueinfo["ces"] ) ? 1 : 0 ; ?> ; var qlimit = <?php echo $qlimit ?> ;
	var chat_sound = 0 ; var console_blink_r = 0 ;
	var title_orig = document.title ;
	var si_counter = 0 ;
	var focused = 1 ;
	var widget = 0 ; var embed = <?php echo $embed ?> ;
	var time_format = <?php echo ( !isset( $VALS['TIMEFORMAT'] ) || ( $VALS['TIMEFORMAT'] != 24 ) ) ? 12 : 24 ; ?> ;
	var wp = 0 ;
	var mobile = ( <?php echo $mobile ?> ) ? is_mobile() : 0 ;
	var mapp = 0 ;
	var sound_new_text = "default" ;
	var sound_volume = 1 ;
	var salt = "<?php echo $salt ?>" ;
	var theme = "<?php echo $theme ?>" ; var lang = "<?php echo $lang ?>" ;
	var new_response_image = "<?php echo $new_response_image ?>" ;
	var vclick = 0 ;
	var height_chat_body ; // global for toggle_input_text()
	var timestamp = <?php echo isset( $deptvars["timestamp"] ) ? $deptvars["timestamp"] : 1 ; ?> ;

	// addons related
	var addon_emo = <?php echo $addon_emo ?> ;
	var addon_ws = 0 ; var ws_connection ;

	var loaded = 0 ;
	var newwin_print ;
	var popout = <?php echo ( isset( $VALS["POPOUT"] ) && ( $VALS["POPOUT"] == "on" ) ) ? 1 : 0 ?> ;
	var survey_texts = new Array("<?php echo urldecode( $LANG["CHAT_SURVEY_THANK"] ) ?>", "<?php echo urldecode( $LANG["CHAT_CLOSE"] ) ?>") ;
	var chat_end_message = "<?php echo $chat_end_message ?>" ;
	var phplive_mobile = 0 ; var phplive_ios = 0 ;
	var phplive_userAgent = navigator.userAgent || navigator.vendor || window.opera ;
	if ( phplive_userAgent.match( /iPad/i ) || phplive_userAgent.match( /iPhone/i ) || phplive_userAgent.match( /iPod/i ) )
	{
		phplive_ios = 1 ;
		if ( phplive_userAgent.match( /iPad/i ) ) { phplive_mobile = 0 ; }
		else { phplive_mobile = 1 ; }
	}
	else if ( phplive_userAgent.match( /Android/i ) ) { phplive_mobile = 2 ; }
	var is_chrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor) ;

	var phplive_browser = navigator.appVersion ; var phplive_mime_types = "" ;
	var phplive_display_width = screen.availWidth ; var phplive_display_height = screen.availHeight ; var phplive_display_color = screen.colorDepth ; var phplive_timezone = new Date().getTimezoneOffset() ;
	if ( navigator.mimeTypes.length > 0 ) { for (var x=0; x < navigator.mimeTypes.length; x++) { phplive_mime_types += navigator.mimeTypes[x].description ; } }
	var phplive_browser_token = phplive_md5( phplive_display_width+phplive_display_height+phplive_display_color+phplive_timezone+phplive_browser+phplive_mime_types ) ;
	var autolinker = new Autolinker( { newWindow: true, stripPrefix: false } ) ;

	var phplive_orientation_isportrait ;
	var win_st_resizing ;
	var st_init_divs ;
	var si_win_status ; var win_minimized = 0 ; var si_new_response ;

	var chats = new Object ;
	chats[ces] = new Object ;
	chats[ces]["requestid"] = <?php echo $requestid ?> ;
	chats[ces]["q_opids"] = new Object ; <?php echo $q_opids_js ?>
	chats[ces]["vname"] = cname ;
	chats[ces]["trans"] = "<xo><div class=\"ca\" id=\"div_connecting\"><?php echo ( $question ) ? "<div class=\'info_box\'><i>".preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", preg_replace( "/\"/", "&quot;", $question ) )."</i></div>" : "" ; ?><div style=\"margin-top: 10px;\"><?php echo addslashes( $deptinfo["msg_greet"] ) ?><div style=\"margin-top: 10px;\"><img src=\"themes/<?php echo $theme ?>/loading_bar.gif\" border=\"0\" alt=\"\"></div><div id=\"div_queue\" style=\"display: none; margin-top: 10px;\"></div></div></div></xo>".vars(null).vars_global().emos() ;
	chats[ces]["status"] = 0 ;
	chats[ces]["disconnected"] = 0 ;
	chats[ces]["tooslow"] = 0 ;
	chats[ces]["op2op"] = 0 ;
	chats[ces]["t_ses"] = <?php echo $vses ?> ;
	chats[ces]["deptid"] = <?php echo $deptid ?> ;
	chats[ces]["opid"] = 0 ;
	chats[ces]["opid_orig"] = 0 ;
	chats[ces]["oname"] = "" ;
	chats[ces]["mapp"] = 0 ;
	chats[ces]["ip"] = "<?php echo $ip ?>" ;
	chats[ces]["vis_token"] = "<?php echo $vis_token ?>" ;
	chats[ces]["chatting"] = 0 ;
	chats[ces]["survey"] = 0 ;
	chats[ces]["rate"] = 0 ;
	chats[ces]["timer"] = <?php echo ( isset( $requestinfo["ces"] ) ) ? $requestinfo["created"] : time() ?> ;
	chats[ces]["istyping"] = 0 ;
	chats[ces]["istyping_counter"] = 0 ;
	chats[ces]["disconnect_click"] = 0 ;
	chats[ces]["processed"] = unixtime() ;

	var audio_supported = HTML5_audio_support() ;
	var mp3_support = ( typeof( audio_supported["mp3"] ) != "undefined" ) ? 1 : 0 ;
	var phplive_session_support = ( typeof( Storage ) != "undefined" ) ? 1 : 0 ;

	// p_engine migration
	var stopped = 0 ;
	var reconnect = 0 ;
	var chatting_err_915, chatting_err_815 ;
	var dc_c_queueing = 0 ;
	var c_routing = 0, c_chatting = 0, c_requesting = 0, c_queueing = 0 ;
	var st_routing, st_chatting, st_requesting, st_init_chatting, st_network, st_connect, st_reconnect, st_queueing ;
	var VARS_JS_REQUESTING = <?php echo $VARS_JS_REQUESTING ?> ;
	var VARS_EXPIRED_QUEUE_IDLE = <?php echo $VARS_EXPIRED_QUEUE_IDLE ?> ;
	var VARS_JS_OP_CONSOLE_TIMEOUT = <?php echo $VARS_JS_OP_CONSOLE_TIMEOUT ?> ;
	var CHAT_ERROR_DC = "<?php echo isset( $LANG["CHAT_ERROR_DC"] ) ? Util_Format_ConvertQuotes( $LANG["CHAT_ERROR_DC"] ) : "Connection error.  Please refresh the page and try again." ; ?>" ;
	var cs = "" ;

	// [ START ] document ready previous code
	$.ajaxSetup({ cache: false }) ;

	if ( mobile ) { $('#embed_win_popout').hide() ; }
	else if ( popout ) { $('#embed_win_popout').show() ; }

	loaded = 1 ;
	init_disconnects() ;
	init_disconnect() ;

	if ( <?php echo $connected ?> )
	{
		chats[ces]["chatting"] = 1 ;
		chats[ces]["trans"] = init_timestamps( "<?php echo $text ?>" ) ;
		$('#chat_body').empty().html( chats[ces]["trans"] ) ;
	}
	else { $('#chat_body').empty().html( chats[ces]["trans"] ) ; }

	if ( addon_emo ) { $('#span_emoticons').show() ; }

	init_scrolling() ;
	init_typing() ;
	textarea_listen() ;

	if ( !phplive_ios && mp3_support )
	{
		if ( phplive_mobile && is_chrome )
		{
			// chrome browser on mobile does not support automatic sound play
		}
		else
		{
			chat_sound = 1 ;
			if ( phplive_session_support )
			{
				try {
					// initial check
					var temp = localStorage.getItem("visitor_sound") ;
					if ( temp ) { chat_sound = parseInt( temp ) ; }
				} catch (error) {}
			}
			print_chat_sound_image('<?php echo $theme ?>') ;
			$('#chat_sound_wrapper').show() ;
		}
	}
	else if ( !mp3_support )
	{
		var chat_sound = 0 ; var console_blink_r = 1 ;
	}

	if ( embed )
	{
		start_win_status_listener() ;
		start_new_response_listner() ;
	}

	if ( typeof( st_init_divs ) != "undefined" ) { clearTimeout( st_init_divs ) ; }
	st_init_divs = setTimeout( function(){ init_divs(0) ; }, 100 ) ;

	var height = $('#chat_input_text_wrapper').outerHeight() + 10 ;
	$('#chat_survey_wrapper').css({'height': height}) ;
	if ( mobile && <?php echo ( isset( $VARS_MISC_MOBILE_MAX_QUIRK ) && $VARS_MISC_MOBILE_MAX_QUIRK ) ? 1 : 0 ; ?> ) { $("body :input").each(function(){ $(this).css({'font-size': '16px'}) ; }); }
	<?php if ( $addon_ws ): ?>ws_init_connect( ces ) ; ws_init_message_receive() ; ws_init_close() ;<?php endif ; ?>
	$('#LANG_TXT_LIVECHAT').html( "<?php echo Util_Format_ConvertQuotes( urldecode( $LANG["TXT_LIVECHAT"] ) ) ?>" ) ;
	<?php if ( $VARS_INI_UPLOAD && $can_upload && is_file( "$CONF[DOCUMENT_ROOT]/addons/file_attach/file_attach.php" ) ): ?>
		try {
			document.getElementById("input_text").addEventListener("paste", paste_upload_init) ;
		} catch(err) { }
	<?php endif ; ?>
	if ( typeof( si_istyping_clear ) != "undefined" ) { clearInterval( si_istyping_clear ) ; }
	si_istyping_clear = setInterval(function() { init_clear_istyping_check() ; }, 1000) ;

	// p_engine migration
	if ( !widget )
		st_routing = setTimeout( "routing(0)" , 500 ) ; // slight delay (not really needed)
	init_chatting() ;
	// p_engine migration end

	$('body').animate({
		opacity: 1
	}, 500, function() {
		//
	});

	// [ END ] document ready previous code

	$(window).resize(function() {
		init_divs(1) ;
		// div refresh for memory buffer whiteout workaround on Android
		// else init_scrolling just in case (not really needed)
		if ( ( typeof( ces ) != "undefined" ) && ( mobile == 2 ) ) { $('#chat_body').empty().html( chats[ces]["trans"].emos().extract_youtube() ) ; init_scrolling() ; } else { init_scrolling() ; }
	});

	<?php if ( !$embed && !$dev ): ?>window.onbeforeunload = function() { return unload_disconnect( ces ) ; }<?php endif ; ?>

	$(window).focus(function() {
		input_focus() ;
	});
	$(window).blur(function() {
		focused = 0 ;
	});

	function unload_disconnect( theces )
	{
		return "<?php echo urldecode( $LANG["CHAT_CLOSE"] ) ?>?" ;
	}

	function init_disconnects()
	{
		// to fix div text not udating if covered by invisible layer image on parent (embed chat)
		var width = $('#info_disconnect').outerWidth() ;
		var width_embed = $('#info_disconnect_embed').outerWidth() ;
		var height = $('#info_disconnect').outerHeight() ;
		var height_embed = $('#info_disconnect_embed').outerHeight() ;

		if ( width_embed > width ) { $('#info_disconnect').css({'width': width_embed}) ; }
		if ( height_embed > height ) { $('#info_disconnect').css({'height': height_embed}) ; }

		$('#info_disconnect').addClass("info_disconnect") ;
		$('#info_disconnect_embed').addClass("info_disconnect") ;
	}

	function init_connect( thejson_data )
	{
		init_connect_doit( thejson_data ) ;
	}

	function init_connect_doit( thejson_data )
	{
		isop_ = parseInt( thejson_data.opid ) ;
		chats[ces]["requestid"] = parseInt( thejson_data.requestid ) ;
		chats[ces]["status"] = parseInt( thejson_data.status_request ) ;
		chats[ces]["oname"] = thejson_data.name ;
		chats[ces]["opid"] = thejson_data.opid ;
		chats[ces]["opid_orig"] = thejson_data.opid ;
		chats[ces]["mapp"] = thejson_data.mapp ;
		chats[ces]["rate"] = thejson_data.rate ;
		chats[ces]["timer"] = ( parseInt( chats[ces]["chatting"] ) ) ? chats[ces]["timer"] : unixtime() ;
		chats[ces]["trans"] = chats[ces]["trans"].replace( /<xo>(.*)<\/xo>/, "" ) ;
		chats[ces]["processed"] = unixtime() ;

		var transcript = chats[ces]["trans"] ;
		$('#chat_body').empty().html( transcript.emos().extract_youtube() ) ;
		init_scrolling() ;
		if ( !win_minimized || <?php echo $popout ?> ) { init_textarea() ; }

		$('#chat_profile_name').html( chats[ces]["oname"] ) ;
		if ( thejson_data.profile != "" )
		{
			$('#chat_profile_pic_img').html( "<img src='"+thejson_data.profile+"' width='55' height='55' border='0' alt='' class='profile_pic_img'>" ) ;
			$('#td_chat_profile_pic_img').fadeIn("fast") ;
		}
		$('#options_visitor').fadeIn("fast") ;

		init_timer() ;
		if ( <?php echo $deptinfo["temail"] ?> ) { $('#chat_email').show() ; }

		if ( addon_ws && !<?php echo ( isset( $requestinfo["status"] ) ) ? $requestinfo["status"] : 0 ?> )
		{
			var ws_text = "{ \"a\": \"init\", \"c\": \""+ces+"\" }" ;
			ws_init_message_send( ws_text ) ;
		}
		$('textarea#input_text').attr( "disabled", false ).val( "" ) ;
		$('#link_download').show() ;

		reset_chatting() ;
		chatting() ;
	}

	function init_chats()
	{
		chats[ces]["processed"] = unixtime() ;
	}

	function cleanup_disconnect( theces )
	{
		// visitor disconnects
		// - disconnected by operator located at global.js update_ces() through parsing
		if ( ( !chats[theces]["disconnected"] && chats[theces]["status"] ) || !chats[theces]["requestid"] || $('#div_queue').is(':visible') )
		{
			chats[theces]["disconnected"] = unixtime() ;

			var text = "<div class='cl'><?php echo urldecode( $LANG["CHAT_NOTIFY_VDISCONNECT"] ) ?></div>" ;
			if ( !chats[theces]["status"] )
			{
				// clear it out so the loading image is not shown
				$('#chat_body').empty() ;
				chats[theces]["trans"] = "" ;
			}

			add_text( theces, text ) ;
			init_textarea() ;
			stopit(0) ;

			window.onbeforeunload = null ;

			if ( ( chats[theces]["status"] || ( chats[theces]["status"] == 2 ) ) && !chats[theces]["survey"] )
			{
				chat_survey() ;
			}
			else if ( !chats[theces]["survey"] )
			{
				queue = 0 ; // skip queue and force leave a message
				leave_a_mesg(0, "") ;
			}
			if ( addon_ws )
			{
				ws_connection.close() ;
			}
		}
		else if ( vclick && ( typeof( theces ) != "undefined" ) && !chats[theces]["survey"] && !chats[ces]["disconnected"] )
		{
			leave_a_mesg(0, "") ;
		}
	}

	function disconnect_complete()
	{
		//
	}

	function leave_a_mesg( thestart_chat, theq_ops )
	{
		if ( thestart_chat && !chats[ces]["requestid"] )
		{
			init_chat_session() ;
		}
		else
		{
			var queue_it_up = 0 ;
			if ( queue && theq_ops )
			{
				var thisq_ops = theq_ops.split(",") ;
				for ( var c = 0; c < thisq_ops.length; ++c )
				{
					var this_opid = thisq_ops[c] ;
					if ( this_opid && typeof( chats[ces]["q_opids"][this_opid] ) == "undefined" ) { queue_it_up = 1 ; }
				}
			}

			if ( queue_it_up && !queue_processed && ( chats[ces]["status"] != 2 ) )
			{
				// 2 indicates leave a message on first decline
				if ( queue == 2 ) { queue_processed = 1 ; }
				inqueue = 1 ;

				// todo: check against queue log redundancy
				queueing() ;
			}
			else
			{
				<?php if ( $vsubject ): ?>var vsubject = encodeURIComponent( "<?php echo $vsubject ?>" ) ;<?php else: ?>var vsubject = "" ;<?php endif ; ?>

				window.onbeforeunload = null ;
				var url = base_url_full+"/phplive_m.php?ces=<?php echo $ces ?>&chat=1&deptid="+chats[ces]["deptid"]+"&gid=<?php echo $gid ?>&token="+phplive_browser_token+"&theme=<?php echo $theme ?>&embed=<?php echo $embed ?>&vname=<?php echo $vname ; ?>&vemail=<?php echo urlencode( $vemail ) ?>&vsubject="+vsubject+"&vquestion=<?php echo rawurlencode( $question ) ?>&onpage=<?php echo rawurlencode( Util_Format_URL( $onpage ) ) ?>&disconnect_click="+chats[ces]["disconnect_click"]+"&vclick="+vclick+"&custom=<?php echo rawurlencode( $custom ) ?>&emarketid=<?php echo ( isset( $emarketid ) ) ? $emarketid : 0 ; ?>&" ;

				if ( embed )
				{
					chats[ces]["disconnected"] = 1 ; // set it so it doesn't trigger too fast in other areas
					location.href = url ;
				}
				else
				{
					url = base_url_full+"/phplive_m.php?ces=<?php echo $ces ?>&chat=1&deptid="+chats[ces]["deptid"]+"&gid=<?php echo $gid ?>&token="+phplive_browser_token+"&theme=<?php echo $theme ?>&embed=<?php echo $embed ?>&vname=<?php echo $vname ; ?>&vemail=<?php echo urlencode( $vemail ) ?>&vsubject="+vsubject+"&vquestion=<?php echo rawurlencode( $question ) ?>&onpage=<?php echo rawurlencode( Util_Format_URL( $onpage ) ) ?>&disconnect_click="+chats[ces]["disconnect_click"]+"&vclick="+vclick+"&custom=<?php echo rawurlencode( $custom ) ?>&emarketid=<?php echo ( isset( $emarketid ) ) ? $emarketid : 0 ; ?>&" ;
					location.href = url ;
				}
			}
		}
	}

	function init_chat_session()
	{
		var json_data = new Object ;
		var unique = unixtime() ;

		$.ajax({
		type: "POST",
		url: "ajax/chat_actions_create.php",
		data: "action=create&token_ces=<?php echo md5( "$ces$CONF[SALT]" ) ?>&marketid=<?php echo $marketid ?>&refer=<?php echo $refer ?>&embed=<?php echo $embed ?>&deptid="+chats[ces]["deptid"]+"&gid=<?php echo $gid ?>&token=<?php echo $token ?>&ces=<?php echo $ces ?>&title=<?php echo rawurlencode( $title ) ?>&onpage=<?php echo rawurlencode( $onpage ) ?>&win_dim=<?php echo rawurlencode( $resolution ) ?>&custom=<?php echo rawurlencode( $custom ) ?>&vname=<?php echo rawurlencode( $vname ) ?>&vemail=<?php echo rawurlencode( $vemail ) ?>&auto_pop=<?php echo $auto_pop ?>&vquestion=<?php echo rawurlencode( $question ) ?>&rtype="+rtype+"&rstring="+rstring+"&iq="+inqueue+"&proto="+proto+"&"+unique,
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				$('#div_connecting').html("System error.  Close the chat window and please try again.<br><br>"+data).addClass("info_error") ;
				return false ;
			}

			if ( json_data.status )
			{
				chats[ces]["requestid"] = parseInt( json_data.requestid ) ;
				routing(opid_direct) ;
			}
			else
			{
				$('#div_connecting').html(json_data.error).addClass("info_error") ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			$('#div_connecting').html("Could not connect to server.  Close the chat window and please try again.").addClass("info_error") ;
		} });
	}

	function process_queue( theces, theqpos, theest, thecreated )
	{
		var est_string = "" ; var qpos_string = "" ;
		var now = unixtime() ;
		if ( theqpos > 0 )
		{
			var thisest = theest * theqpos ;
			if ( thisest < 1 ) { thisest = 1 ; }

			est_string = ( theest && <?php echo ( isset( $deptvars["qest"] ) && $deptvars["qest"] ) ? 1 : 0 ; ?> ) ? "<div><?php echo Util_Format_ConvertQuotes( $qtexts["CHAT_QUEUE_EST"] ) ?> "+thisest+" <?php echo Util_Format_ConvertQuotes( $qtexts["CHAT_QUEUE_EST_MIN"] ) ?></div>" : "" ;
			qpos_string = ( <?php echo ( isset( $deptvars["qpos"] ) && $deptvars["qpos"] ) ? 1 : 0 ; ?> ) ? "<div style=\"margin-top: 15px; font-size: 16px; font-weight: bold;\"><?php echo $qtexts["CHAT_QUEUE_POS"] ?> "+theqpos+"</div>" : "" ;

			if ( est_string || qpos_string )
			{
				$('#div_queue').html( est_string+qpos_string ).fadeIn("fast", function() {
					init_scrolling() ;
				});
			}
		}
		if ( theces == ces )
		{
			if ( <?php echo ( isset( $deptvars["qpos"] ) && $deptvars["qpos"] ) ? 1 : 0 ; ?> )
				qpos_string = "<?php echo Util_Format_ConvertQuotes( $qtexts["CHAT_QUEUE_POS"] ) ?> <?php echo urldecode( $LANG["TXT_CONNECTING"] ) ?>" ;
			else
				qpos_string = "<?php echo urldecode( $LANG["TXT_CONNECTING"] ) ?>" ;

			$('#div_queue').html( "<div style=\"margin-top: 15px; font-size: 16px; font-weight: bold;\">"+qpos_string+"</div>" ).fadeIn("fast", function() {
				init_scrolling() ;
			});
			init_chat_session() ;
		}
	}

	function send_email( thedownload )
	{
		var vemail = $('#vemail').val() ;
		var transcript = encodeURIComponent( $('#chat_body').html() ) ;

		if ( !vemail && !thedownload )
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_EMAIL"] ?>" ) ;
		else if ( !check_email( vemail ) && !thedownload )
			do_alert( 0, "<?php echo $LANG["CHAT_JS_INVALID_EMAIL"] ?>" ) ;
		else
		{
			$('#btn_email').attr( "disabled", true ) ;
			$('#vemail').attr( "disabled", true ) ;

			var json_data = new Object ;
			var unique = unixtime() ;
			var vname = "<?php echo $vname ?>" ;

			$.ajax({
			type: "POST",
			url: "phplive_m.php",
			data: "&action=send_email_trans&trans=1&ces=<?php echo $ces ?>&opid="+chats[ces]["opid"]+"&deptid="+chats[ces]["deptid"]+"&gid=<?php echo $gid ?>&token="+phplive_browser_token+"&vname="+vname+"&vemail="+vemail+"&emarketid=<?php echo ( isset( $emarketid ) ) ? $emarketid : 0 ; ?>&transcript="+transcript+"&download="+thedownload+"&"+unique,
			success: function(data){

				if ( thedownload )
				{
					$('#btn_email').attr( "disabled", false ) ;
					$('#vemail').attr( "disabled", false ) ;

					$('#link_download_doit').attr('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(data)) ;
					$('#link_download_doit')[0].click() ;
				}
				else
				{
					eval( data ) ;
					if ( json_data.status )
					{
						do_alert( 1, "<?php echo $LANG["CHAT_JS_EMAIL_SENT"] ?>" ) ;
						toggle_send_trans(1) ;
						setTimeout( function(){
							$('#btn_email').attr( "disabled", false ) ;
							$('#vemail').attr( "disabled", false ) ;
						}, 10000 ) ;
					}
					else
					{
						do_alert( 0, json_data.error ) ;
						$('#btn_email').attr( "disabled", false ) ;
						$('#vemail').attr( "disabled", false ) ;
					}
				}

			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Could not connect to server.  Please try again. [e551]" ) ;
				$('#btn_email').attr( "disabled", false ) ;
				$('#vemail').attr( "disabled", false ) ;
			} });
		}
	}

	function send_comment()
	{
		var json_data = new Object ;
		var unique = unixtime() ;
		var message = encodeURIComponent( $('#vcomment').val() ) ;

		if ( !message )
		{
			do_alert( 1, "<?php echo $LANG["CHAT_COMMENT_THANK"] ?>" ) ;
		}
		else
		{
			$.ajax({
			type: "POST",
			url: "ajax/chat_actions_rating.php",
			data: "&action=comment&deptid="+chats[ces]["deptid"]+"&gid=<?php echo $gid ?>&vis_token="+chats[ces]["vis_token"]+"&ces=<?php echo $ces ?>&message="+message+"&"+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					do_alert( 1, "<?php echo $LANG["CHAT_COMMENT_THANK"] ?>" ) ;
					close_misc('rating') ;
				}
				else
				{
					do_alert( 0, json_data.error ) ;
					$('#vcomment').attr( "disabled", false ) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Could not connect to server.  Please try again. [e554]" ) ;
				$('#vcomment').attr( "disabled", false ) ;
			} });
		}
	}

	function toggle_chat_sound( thetheme )
	{
		if ( chat_sound )
		{
			chat_sound = 0 ;
			console_blink_r = 1 ;
		}
		else
		{
			chat_sound = 1 ;
			console_blink_r = 0 ;
		}
		if ( phplive_session_support ) { try { localStorage.setItem( "visitor_sound", chat_sound ) ; } catch (error) {} }
		print_chat_sound_image( thetheme ) ;
	}

	function toggle_show_disconnect( theflag )
	{
		if ( theflag ) { $('#info_disconnect').show() ; }
		else { $('#info_disconnect').hide() ; }
	}

	function toggle_send_trans( theforce_close )
	{
		if ( $('#send_transcript_box').is(':visible') || theforce_close )
			$('#send_transcript_box').fadeOut("fast") ;
		else
		{
			position_send_trans() ;
			$('#send_transcript_box').center().fadeIn("fast") ;
		}
	}

	function toggle_send_trans_rate()
	{
		toggle_send_trans(0) ;
		close_misc('emo') ;
		close_misc('attach') ;
	}

	function position_send_trans()
	{
		var trans_pos = $("#chat_email").position() ;
		var height_trans_box = parseInt( $('#send_transcript_box').outerHeight() ) ;
		var trans_top = trans_pos.top - height_trans_box - 15 ;
		var trans_left = 25  ;
		if ( !isop )
		{
			// center position
		}
		else
			$("#send_transcript_box").css({'top': trans_top, 'left': trans_left}) ;
	}

	function toggle_rating( theforce_close )
	{
		if ( $('#chat_survey_rating_wrapper').is(':visible') || theforce_close )
			$('#chat_survey_rating_wrapper').fadeOut("fast") ;
		else
		{
			$('#chat_survey_rating_wrapper').center().fadeIn("fast") ;
		}
	}

	function toggle_input_text()
	{
		var height_input_text = $("textarea#input_text").height() ;

		if ( ( ( typeof( ces ) != "undefined" ) && chats[ces]["status"] && !chats[ces]["disconnected"] ) || ( height_input_text != 45 ) )
		{
			if ( height_input_text == 45 )
			{
				height_chat_body = $('#chat_body').height() ;

				var height_new = height_chat_body - (150-75) ;
				$('#chat_body').animate({
					height: height_new
				}, 300, function() {
					$("#chat_input").css({'bottom': -50}) ; 
					$("textarea#input_text").animate({
						height: 125
					}, 300, function() {
						init_scrolling() ;
						$('textarea#input_text').focus() ;
						// place cursor at end
						var temp = $('textarea#input_text').val() ; $('textarea#input_text').val(temp) ;
					});
				});
			}
			else
			{
				$("#chat_input").css({'bottom': "auto"}) ;
				$("textarea#input_text").animate({
					height: 45
				}, 300, function() {
					$('#chat_body').animate({
						height: height_chat_body
					}, 300, function() {
						init_scrolling() ;
						$('textarea#input_text').focus() ;
						// place cursor at end
						var temp = $('textarea#input_text').val() ; $('textarea#input_text').val(temp) ;
					});
				});
			}
		}
		else
			do_alert( 0, "<?php echo $LANG["CHAT_NOTIFY_DISCONNECT"] ?>" ) ;
	}
//-->
</script>

<?php include_once( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) ; ?>
<?php if ( $VARS_INI_UPLOAD && $deptinfo["vupload"] && is_file( "$CONF[DOCUMENT_ROOT]/addons/file_attach/file_attach.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/addons/file_attach/file_attach.php" ) ; } ?>

</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>