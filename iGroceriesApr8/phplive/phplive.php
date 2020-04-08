<?php
	/***************************************/
	//
	//
	// PHP Live! Support
	//
	// https://www.phplivesupport.com
	//
	/***************************************/
	if ( !is_file( "./web/config.php" ) ){ HEADER("location: setup/install.php") ; exit ; }
	include_once( "./web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	$query = isset( $_SERVER["QUERY_STRING"] ) ? preg_replace( "/&&/", "&", Util_Format_Sanatize( $_SERVER["QUERY_STRING"], "query" ) ) : "" ;
	/* AUTO PATCH */
	if ( !is_file( "$CONF[CONF_ROOT]/patches/$patch_v" ) )
	{
		HEADER( "location: patch.php?from=chat&".$query."&" ) ; exit ;
	}
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/get.php" ) ;
	use GeoIp2\Database\Reader ;

	$onpage = Util_Format_Sanatize( base64_decode( Util_Format_GetVar( "pg" ) ), "url" ) ;
	$title = Util_Format_Sanatize( base64_decode( Util_Format_GetVar( "tl" ) ), "title" ) ;
	if ( !$onpage )
	{
		$onpage = Util_Format_Sanatize( Util_Format_GetVar( "onpage" ), "url" ) ;
		$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "title" ) ;
	}
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "d" ), "n" ) ;
	if ( !$deptid ) { $deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ; } $deptid_orig = $deptid ;
	$gid = ( $deptid > 100000000 ) ? $deptid : 0 ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
	$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;
	$vquestion = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "vquestion" ), "htmltags" ) ) ;
	if ( !$vquestion ) { $vquestion = "" ; } // to remove the zero (0) just in case
	$embed = Util_Format_Sanatize( Util_Format_GetVar( "embed" ), "n" ) ;
	$popout = Util_Format_Sanatize( Util_Format_GetVar( "popout" ), "n" ) ;
	$autoinvite = Util_Format_Sanatize( Util_Format_GetVar( "i" ), "n" ) ;
	$js_name = base64_decode( Util_Format_Sanatize( Util_Format_GetVar( "js_name" ), "ln" ) ) ;
	$js_email = base64_decode( Util_Format_Sanatize( Util_Format_GetVar( "js_email" ), "e" ) ) ;
	$vsubject = Util_Format_ConvertTags( Util_Format_ConvertQuotes( Util_Format_Sanatize( Util_Format_GetVar( "vsubject" ), "" ) ) ) ;
	$custom = Util_Format_Sanatize( Util_Format_GetVar( "custom" ), "htmltags" ) ;
	$lang = Util_Format_Sanatize( Util_Format_GetVar( "lang" ), "ln" ) ;
	$token = Util_Format_Sanatize( Util_Format_GetVar( "token" ), "ln" ) ;
	$proid = Util_Format_Sanatize( Util_Format_GetVar( "proid" ), "ln" ) ;
	$api_key = Util_Format_Sanatize( Util_Format_GetVar( "api_key" ), "ln" ) ;
	$redirected = Util_Format_Sanatize( Util_Format_GetVar( "r" ), "n" ) ;
	$preview = Util_Format_Sanatize( Util_Format_GetVar( "preview" ), "n" ) ;
	$dept_themes = ( isset( $VALS["THEMES"] ) ) ? unserialize( $VALS["THEMES"] ) : Array() ;
	if ( !$theme && isset( $dept_themes[$deptid] ) && $deptid ) { $theme = $dept_themes[$deptid] ; }
	else if ( !$theme ) { $theme = $CONF["THEME"] ; }
	else if ( $theme && !is_file( "$CONF[DOCUMENT_ROOT]/themes/$theme/style.css" ) ) { $theme = $CONF["THEME"] ; }
	if ( !$token ) { $query = preg_replace( "/token=0/", "", $query ) ; HEADER( "location: ./fetch_token.php?$query" ) ; exit ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	if ( !is_file( "$CONF[DOCUMENT_ROOT]/themes/$theme/style.css" ) ) { $theme = "default" ; }

	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $ip, $vis_token ) = Util_IP_GetIP( $token ) ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;
	$cookie = ( !isset( $CONF["cookie"] ) || ( $CONF["cookie"] == "on" ) ) ? 1 : 0 ;
	$js_custom_hash = "" ; if ( $autoinvite ) { $custom = ( $custom ) ? "{$custom}-cus-From-_-Automatic Chat Invite-cus-" : "From-_-Automatic Chat Invite-cus-" ; }

	$departments_pre = Depts_get_AllDepts( $dbh ) ;

	if ( is_file( "$CONF[CONF_ROOT]/addons.php" ) ) { include_once( "$CONF[CONF_ROOT]/addons.php" ) ; }

	$recaptchas = ( isset( $VALS_ADDONS ) && isset( $VALS_ADDONS["recaptcha"] ) ) ? unserialize( $VALS_ADDONS["recaptcha"] ) : Array() ;
	$recaptcha_enabled = 0 ;
	if ( isset( $recaptchas["active_string"] ) && $recaptchas["active_string"] && is_file( "$CONF[DOCUMENT_ROOT]/addons/recaptcha/config_recaptcha.php" ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/addons/recaptcha/config_recaptcha.php" ) ;
		if ( isset( $RECAPTCHA_SITE_KEY ) && isset( $RECAPTCHA_SECRET_KEY ) && ( $recaptchas["active_string"] == md5( $RECAPTCHA_SITE_KEY.$RECAPTCHA_SECRET_KEY ) ) )
			$recaptcha_enabled = 1 ;
	}

	/*********************************************************/
	//
	// automatic connect based on Setup Admin settings
	// DISABLE if reCAPTCHA addon is enabled (either automatic connect or reCAPTCHA enabled, not both)
	//
	$auto_connect_array = isset( $VALS["auto_connect"] ) ? unserialize( $VALS["auto_connect"] ) : Array() ;
	if ( !$preview && !$recaptcha_enabled && isset( $auto_connect_array[$deptid]["auto_connect"] ) && ( $auto_connect_array[$deptid]["auto_connect"] != "off" ) && !$redirected )
	{
		/*********************************************************/
		// simply append to custom variable to utilize current method
		/*********************************************************/
		$custom = preg_replace( "/opID-_-(.*?)-cus-/i", "", $custom ) ;
		$custom = preg_replace( "/deptID-_-(.*?)-cus-/i", "", $custom ) ;
		$custom = preg_replace( "/api_key-_-(.*?)-cus-/i", "", $custom ) ;

		if ( $auto_connect_array[$deptid]["opid"] )
			$custom .= "opID-_-".$auto_connect_array[$deptid]["opid"]."-cus-" ;
		else if ( $auto_connect_array[$deptid]["deptid"] )
			$custom .= "deptID-_-".$auto_connect_array[$deptid]["deptid"]."-cus-" ;
		else if ( !$auto_connect_array[$deptid]["opid"] && !$auto_connect_array[$deptid]["deptid"] && ( count( $departments_pre ) == 1 ) && isset( $departments_pre[0]["deptID"] ) )
			$custom .= "deptID-_-".$departments_pre[0]["deptID"]."-cus-" ;

		$custom .= "api_key-_-".$CONF['API_KEY']."-cus-" ;
	}
	/*********************************************************/

	if ( $custom )
	{
		$custom_pairs = explode( "-cus-", $custom ) ;
		for ( $c = 0; $c < count( $custom_pairs ); ++$c )
		{
			if ( $custom_pairs[$c] ) { LIST( $custom_var_name, $custom_var_val ) = explode( "-_-", $custom_pairs[$c] ) ; $js_custom_hash .= "custom_hash['$custom_var_name'] = '$custom_var_val' ; " ; }
		}
		preg_match( "/vquestion-_-(.*?)-cus-/i", $custom, $matches ) ;
		if ( isset( $matches[1] ) ) { $custom = preg_replace( "/vquestion-_-(.*?)-cus-/i", "", $custom ) ; $vquestion = $matches[1] ; }
		else
		{
			preg_match( "/question-_-(.*?)-cus-/i", $custom, $matches ) ;
			if ( isset( $matches[1] ) ) { $custom = preg_replace( "/question-_-(.*?)-cus-/i", "", $custom ) ; $vquestion = $matches[1] ; }
		}

		// delete sensitive vars that should not be visible to the public
		preg_match( "/deptID-_-(.*?)-cus-/i", $custom, $matches ) ;
		if ( isset( $matches[1] ) && $matches[1] ) { $custom = preg_replace( "/deptID-_-(.*?)-cus-/i", "", $custom ) ; $deptid = $matches[1] ; }
		preg_match( "/opID-_-(.*?)-cus-/i", $custom, $matches ) ;
		if ( isset( $matches[1] ) && $matches[1] ) { $custom = preg_replace( "/opID-_-(.*?)-cus-/i", "", $custom ) ; $opid = $matches[1] ; }
		preg_match( "/api_key-_-(.*?)-cus-/i", $custom, $matches ) ;
		if ( isset( $matches[1] ) ) { $custom = preg_replace( "/api_key-_-(.*?)-cus-/i", "", $custom ) ; $api_key = $matches[1] ; }
	}

	$temp_vname = ( !$js_name && ( isset( $_COOKIE["phplivevname"] ) && ( $_COOKIE["phplivevname"] != "null" ) && $cookie ) ) ? Util_Format_Sanatize( $_COOKIE["phplivevname"], "ln" ) : $js_name ;
	$temp_vemail = ( !$js_email && ( isset( $_COOKIE["phplivevemail"] ) && ( $_COOKIE["phplivevemail"] != "null" ) && $cookie ) ) ? Util_Format_Sanatize( $_COOKIE["phplivevemail"], "e" ) : $js_email ;
	$vname = ( $temp_vname ) ? $temp_vname : "" ;
	$vemail = ( $temp_vemail ) ? $temp_vemail : "" ;
	$dept_online_text = $dept_offline = $dept_settings = $dept_customs = "" ;

	if ( preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) ) { $spam_exist = 1 ; } else { $spam_exist = 0 ; }
	if ( $geoip )
	{
		if ( ( phpversion() >= 5.4 ) && is_file( "$CONF[DOCUMENT_ROOT]/addons/geo_data/vendor/autoload.php" ) )
		{
			require "$CONF[DOCUMENT_ROOT]/addons/geo_data/vendor/autoload.php" ;
			$reader = new Reader( "$CONF[DOCUMENT_ROOT]/addons/geo_data/GeoLite2-City.mmdb" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/inc_geo.php" ) ;
		}
		$spam_country = isset( $VALS["CHAT_SPAM_COUNTRY"] ) ? unserialize( $VALS['CHAT_SPAM_COUNTRY'] ) : Array() ; $request_country = isset( $geo_country_code ) ? strtoupper( $geo_country_code ) : "" ;
		if ( $request_country && isset( $spam_country[$request_country] ) ) { $spam_exist = 1 ; }
	}
	$queue_embed_query = ( $embed || $popout ) ? " AND embed = 1 " : " AND embed = 0 " ;
	$query_db = "SELECT queueID, ces, deptID FROM p_queue WHERE md5_vis = '$vis_token' $queue_embed_query LIMIT 1" ;
	database_mysql_query( $dbh, $query_db ) ; $queueinfo = database_mysql_fetchrow( $dbh ) ;
	if ( isset( $queueinfo["ces"] ) && !$preview )
	{
		if ( $popout )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Queue/update.php" ) ;
			Queue_update_QueueValue( $dbh, $queueinfo["queueID"], "embed", 0 ) ;
		}
		$deptid = $queueinfo["deptID"] ;
		if ( isset( $dept_themes[$deptid] ) && $deptid ) { $theme = $dept_themes[$deptid] ; }
		$requestinfo_onpage = isset( $requestinfo["onpage"] ) ? urlencode( Util_Format_URL( $requestinfo["onpage"] ) ) : "" ;
		database_mysql_close( $dbh ) ;
		HEADER( "location: phplive_.php?embed=$embed&popout=$popout&deptid=$deptid&token=$token&vis_token=$vis_token&theme=$theme&ces=$queueinfo[ces]&vname=null&vquestion=null&onpage=$requestinfo_onpage&queue=1&gid=$gid&".$now ) ; exit ;
	}

	$requestinfo = ( !$preview ) ? Chat_get_itr_RequestGetInfo( $dbh, 0, "", $vis_token ) : Array() ;
	// popout from embed chat
	if ( isset( $requestinfo["deptID"] ) && ( $requestinfo["md5_vis"] || $requestinfo["md5_vis_"] ) && !$preview )
	{
		$deptid = $requestinfo["deptID"] ;
		if ( isset( $dept_themes[$deptid] ) && $deptid ) { $theme = $dept_themes[$deptid] ; }
		if ( $popout )
		{
			$query = "UPDATE p_requests SET md5_vis = '' WHERE requestID = $requestinfo[requestID]" ;
			database_mysql_query( $dbh, $query ) ;
		}
		else if ( $requestinfo["initiated"] && !$requestinfo["status"] )
		{
			if ( is_file( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ) { @unlink( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ; }
			if ( $embed )
			{
				// to ensure it automatically opens the embed window
				$query = "UPDATE p_requests SET md5_vis = '$vis_token' WHERE requestID = $requestinfo[requestID]" ;
				database_mysql_query( $dbh, $query ) ;
			}
		}
		database_mysql_close( $dbh ) ;
		HEADER( "location: phplive_.php?embed=$embed&popout=$popout&deptid=$deptid&token=$token&vis_token=$vis_token&theme=$theme&ces=$requestinfo[ces]&vname=null&vquestion=null&onpage=".urlencode( Util_Format_URL( $requestinfo["onpage"] ) )."&gid=$gid&".$now ) ; exit ;
	} if ( is_file( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ) { @unlink( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ; }

	/*********************************************************/
	// start chat automatically
	/*********************************************************/
	if ( !$preview && $opid && ( $deptid > 100000000 ) )
	{
		$departments = Depts_get_OpDepts( $dbh, $opid ) ;
		if ( isset( $departments[0] ) ) { $deptid = $departments[0]["deptID"] ; }
		else { $opid = 0; $deptid = 0 ; }
	}
	if ( !$preview && ( $opid || $deptid ) && $api_key && ( $api_key == $CONF['API_KEY'] ) )
	{
		database_mysql_close( $dbh ) ;
		$vname_query = Util_Format_Sanatize( Util_Format_GetVar( "vname" ), "ln" ) ; if ( preg_replace( "/ /", "", $vname_query ) == "" ) { $vname_query = $vname ; }
		$vemail_query = Util_Format_Sanatize( Util_Format_GetVar( "vemail" ), "e" ) ; if ( !$vemail_query || ( $vemail_query == "null" ) ) { $vemail_query = $vemail ; }
		$vquestion = urlencode( $vquestion ) ;
		$url = base64_encode( "phplive_.php?embed=$embed&popout=$popout&deptid=$deptid&opid=$opid&theme=$theme&api_key=$api_key&vquestion=$vquestion&vis_token=$vis_token&custom=$custom&vname=$vname_query&vemail=$vemail&gid=$gid" ) ;
		HEADER( "location: blank.php?url=$url" ) ; exit ;
	}

	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_itr.php" ) ;
	Ops_update_itr_IdleOps( $dbh ) ;

	$popout = 0 ;
	$vars = Util_Format_Get_Vars( $dbh ) ;
	if ( $vars["ts_clear"] <= ( $now - $VARS_CYCLE_CLEAN ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Files.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

		Util_Format_Update_TimeStamp( $dbh, "clear", $now ) ;
		Footprints_remove_itr_Expired_U( $dbh ) ;
		Footprints_remove_ExpiredStats( $dbh ) ;
		Util_Files_CleanExportDir() ;
		Chat_remove_itr_OldRequests( $dbh ) ;
	}

	$total_ops = 0 ; $dept_online = Array() ; $departments = Array() ;
	/*********************************************************/
	// department groups will always be greater then 100000000
	/*********************************************************/
	if ( $deptid > 100000000 )
	{
		$dept_group = Depts_get_DeptGroup( $dbh, $deptid ) ;
		$dept_group_deptids = ( isset( $dept_group["deptids"] ) ) ? explode( ",", $dept_group["deptids"] ) : null ;

		if ( $dept_group_deptids != null )
		{
			$departments_pre_temp = $departments_pre ; $departments_pre = Array() ;
			for ( $c = 0; $c < count( $departments_pre_temp ); ++$c )
			{
				$found = 0 ;
				for ( $c2 = 0; $c2 < count( $dept_group_deptids ); ++$c2 )
				{
					if ( $dept_group_deptids[$c2] && ( $departments_pre_temp[$c]["deptID"] == $dept_group_deptids[$c2] ) )
						$found = 1 ;
				} if ( $found ) { $departments_pre[] = $departments_pre_temp[$c] ; }
			}
			if ( isset( $dept_group["lang"] ) ) { $lang = $dept_group["lang"] ; }
		}
		else { $deptid = 0 ; }
	}
	if ( $deptid && ( $deptid < 100000000 ) )
	{
		$deptinfo = Array() ;
		for ( $c = 0; $c < count( $departments_pre ); ++$c )
		{
			$deptinfo_temp = $departments_pre[$c] ;
			if ( $deptid == $deptinfo_temp["deptID"] ) { $deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ; break 1 ; }
		}
		$departments[0] = $deptinfo ;
		if ( !isset( $deptinfo["deptID"] ) )
		{
			$query = preg_replace( "/embed=(.*?)(\&|$)/", "emb=$1$2", $query ) ; // workaround for the d= situation
			$query = preg_replace( "/(d=(.*?)(&|$))/", "d=0&", $query ) ;
			$query = preg_replace( "/emb=(.*?)(\&|$)/", "embed=$1$2", $query ) ;
			$query = preg_replace( "/deptID-_-(.*?)-cus-/i", "", $query ) ; // clear custom variable to prevent looping
			database_mysql_close( $dbh ) ;
			HEADER( "location: phplive.php?$query&" ) ; exit ;
		}

		$total = Ops_get_itr_AnyOpsOnline( $dbh, $deptinfo["deptID"] ) ;
		$total_ops += $total ;
		$dept_online[$deptinfo["deptID"]] = $total ;
		$dept_offline .= "dept_offline[$deptinfo[deptID]] = '".preg_replace( "/'|&quot;/", "\"", $deptinfo["msg_offline"] )."' ; " ;
		$dept_settings .= " dept_settings[$deptinfo[deptID]] = Array( $deptinfo[remail], $deptinfo[temail], $deptinfo[rquestion], $deptinfo[rname] ) ; " ;
		$custom_fields = ( $deptinfo["custom"] ) ? unserialize( $deptinfo["custom"] ) : Array() ;
		if ( isset( $custom_fields[0] ) )
		{
			$dept_customs .= " dept_customs[$deptinfo[deptID]] = Array( '$custom_fields[0]', $custom_fields[1] " ;
			if ( isset( $custom_fields[2] ) ) { $dept_customs .= ", '$custom_fields[2]', $custom_fields[3] " ; }
			if ( isset( $custom_fields[4] ) ) { $dept_customs .= ", '$custom_fields[4]', $custom_fields[5] " ; }
			$dept_customs .= " ) ;" ;
		}
		
		if ( $deptinfo["lang"] ) { $CONF["lang"] = $deptinfo["lang"] ; }
	}
	else
	{
		for ( $c = 0; $c < count( $departments_pre ); ++$c )
		{
			$department = $departments_pre[$c] ;
			if ( $department["visible"] ) { $departments[] = $department ; }
		}

		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			if ( $spam_exist )
				$total = 0 ;
			else
				$total = Ops_get_itr_AnyOpsOnline( $dbh, $department["deptID"] ) ;
			$total_ops += $total ;

			$dept_online[$department["deptID"]] = $total ;
			$dept_offline .= "dept_offline[$department[deptID]] = '".preg_replace( "/'|&quot;/", "\"", $department["msg_offline"] )."' ; " ;
			$dept_settings .= " dept_settings[$department[deptID]] = Array( $department[remail], $department[temail], $department[rquestion], $department[rname] ) ; " ;
			$custom_fields = ( $department["custom"] ) ? unserialize( $department["custom"] ) : Array( ) ;
			if ( isset( $custom_fields[0] ) )
			{
				$dept_customs .= " dept_customs[$department[deptID]] = Array( '$custom_fields[0]', $custom_fields[1] " ;
				if ( isset( $custom_fields[2] ) ) { $dept_customs .= ", '$custom_fields[2]', $custom_fields[3] " ; }
				if ( isset( $custom_fields[4] ) ) { $dept_customs .= ", '$custom_fields[4]', $custom_fields[5] " ; }
				$dept_customs .= " ) ;" ;
			}
		}

		if ( count( $departments ) == 1 )
			$deptid = $departments[0]["deptID"] ;
	}

	$emarketings = Array() ; $emarketinginfo = Array( "id"=>0, "message"=>"", "val_1"=>"", "val_0"=>"", "isreq"=>1, "status"=>0 ) ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emarketing/emarketing.php" ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/addons/emarketing/API/Util_Emarketing.php" ) ;
		$emarketings = ( isset( $VALS_ADDONS ) && isset( $VALS_ADDONS["emarketing"] ) ) ? unserialize( base64_decode( $VALS_ADDONS["emarketing"] ) ) : Array() ;
		if ( count( $emarketings ) && !Util_Emarketing_VisExists( $dbh, $vis_token ) )
			$emarketinginfo = current( (Array)$emarketings ) ;
	}

	$deptvars_all = Depts_get_AllDeptsVars( $dbh ) ;
	$dept_offline_form = "" ; $dept_offline_hasform = 0 ; $dept_prechat_form = "" ; $dept_haspolicy = "" ; $dept_addon_emarketings = "" ;
	foreach ( $deptvars_all as $deptid_temp => $deptvar )
	{
		if ( isset( $deptvar["offline_form"] ) )
		{
			$dept_offline_form .= "dept_offline_form[$deptid_temp] = $deptvar[offline_form] ; " ;
			$dept_prechat_form .= "dept_prechat_form[$deptid_temp] = $deptvar[prechat_form] ; " ;

			if ( isset( $deptvar["gdpr_msg"] ) && preg_match( "/-_-/", $deptvar["gdpr_msg"] ) )
			{
				LIST( $text_checkbox, $gdpr_message ) = explode( "-_-", $deptvar["gdpr_msg"] ) ;
				$text_checkbox = rawurlencode( preg_replace( "/\[link\](.*?)\[\/link\]/", "<a href='JavaScript:void(0)' onClick='toggle_policy( $deptid_temp, 0 )'>$1</a>", $text_checkbox ) ) ;
				$dept_haspolicy .= "dept_haspolicy[$deptid_temp] = \"$text_checkbox\" ; " ;
			}
			if ( $deptvar["offline_form"] ) { $dept_offline_hasform = 1 ; }
			if ( $deptvar["emarketID"] ) { $dept_addon_emarketings .= "dept_addon_emarketings[$deptid_temp] = 1 ; " ; }
		}
	}
	if ( !count( $deptvars_all ) ) { $dept_offline_hasform = 1 ; }
	else if ( $total_ops ) { $dept_offline_hasform = 1 ; }
	else if ( isset( $deptvars_all[$deptid] ) ) { $dept_offline_hasform = $deptvars_all[$deptid]["offline_form"] ; }
	else if ( count( $deptvars_all ) == 1 )
	{
		if ( $deptid && !isset( $deptvars_all[$deptid] ) ) { $dept_offline_hasform = 1 ; }
		else if ( !$deptid && ( count( $departments ) > 1 ) ) { $dept_offline_hasform = 1 ; }
	}
	$deptvars = isset( $deptvars_all[$deptid] ) ? $deptvars_all[$deptid] : Array() ;
	$emlogos_hash = ( isset( $VALS["EMLOGOS"] ) ) ? unserialize( $VALS["EMLOGOS"] ) : Array() ;
	$autolinker_js_file = ( isset( $VARS_JS_AUTOLINK_FILE ) && ( ( $VARS_JS_AUTOLINK_FILE == "min" ) || ( $VARS_JS_AUTOLINK_FILE == "src" ) ) ) ? "autolinker_$VARS_JS_AUTOLINK_FILE.js" : "autolinker_min.js" ;

	if ( $lang ) { $CONF["lang"] = $lang ; }
	$CONF["lang"] = ( isset( $CONF["lang"] ) && $CONF["lang"] && is_file( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ) ? $CONF["lang"] : "english" ;
	// due to Chat_remove_itr_OldRequests include lang, need to use include just in case
	include( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ;

	/////////////////////////////////////////////
	if ( defined( "LANG_CHAT_WELCOME" ) || !isset( $LANG["CHAT_JS_CUSTOM_BLANK"] ) )
	{ ErrorHandler( 611, "Update to your custom language file is required ($CONF[lang]).  Copy an existing language file and create a new custom language file.", $PHPLIVE_FULLURL, 0, Array( ) ) ; exit ; } if ( $preview && $deptid ) { $dept_online[$deptid] = 1 ; }

	$dept_offline_urls = "" ;
	$offline = ( isset( $VALS['OFFLINE'] ) && $VALS['OFFLINE'] ) ? unserialize( $VALS['OFFLINE'] ) : Array( ) ;
	if ( $gid > $VARS_GID_MIN )
	{
		if ( isset( $offline[$gid] ) && !preg_match( "/^(icon|hide|embed|tab)$/", $offline[$gid] ) ) { $dept_offline_urls .= "dept_offline_urls[0] = '$offline[$gid]' ; " ; }
		else if ( isset( $offline[0] ) && !preg_match( "/^(icon|hide|embed|tab)$/", $offline[0] ) ) { $dept_offline_urls .= "dept_offline_urls[0] = '$offline[0]' ; " ; }
	}
	else
	{
		if ( $deptid && isset( $offline[$deptid] ) && !preg_match( "/^(icon|hide|embed|tab)$/", $offline[$deptid] ) ) { $dept_offline_urls .= "dept_offline_urls[$deptid] = '$offline[$deptid]' ; " ; }
		else if ( $deptid && isset( $offline[$deptid] ) && preg_match( "/^(icon|hide|embed|tab)$/", $offline[$deptid] ) ) { } // no offline url
		else
		{
			foreach( $offline as $this_deptid => $value )
			{
				if ( !isset( $offline[$this_deptid] ) ) { $offline[$this_deptid] = $offline[0] ; }
				$redirect_url = ( isset( $offline[$this_deptid] ) && !preg_match( "/^(icon|hide|embed|tab)$/", $offline[$this_deptid] ) ) ? $offline[$this_deptid] : "" ;
				if ( $redirect_url ) { $dept_offline_urls .= "dept_offline_urls[$this_deptid] = '$redirect_url' ; " ; }
			}
		}
	}
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
<?php
	// process it after the title display because the LANG will be overwritten here
	$LANG_TEXTS = Array() ; $LANG_TEXTS[$CONF["lang"]] = $LANG ; $depts_lang = "depts_lang[0] = '$CONF[lang]' ; " ;
	for ( $c = 0; $c < count( $departments_pre ); ++$c )
	{
		$deptinfo_temp = $departments_pre[$c] ;
		if ( ( $deptinfo_temp["lang"] != $CONF["lang"] ) && is_file( "$CONF[DOCUMENT_ROOT]/lang_packs/$deptinfo_temp[lang].php" ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$deptinfo_temp[lang].php" ) ;
			$LANG_TEXTS[$deptinfo_temp["lang"]] = $LANG ;
		}
		$depts_lang .= "depts_lang[$deptinfo_temp[deptID]] = '$deptinfo_temp[lang]' ; " ;
	}
	if ( isset( $dept_group ) && isset( $dept_group ) && $dept_group["lang"] ) { $depts_lang .= "depts_lang[$dept_group[groupID]] = '$dept_group[lang]' ; " ; }
	$LANG_DBS = Lang_get_AllLangs( $dbh ) ;
?>
<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="noindex,nofollow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>">
<?php include_once( "./inc_meta_dev.php" ) ; ?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>

<link rel="Stylesheet" href="./themes/<?php echo $theme ?>/style.css?<?php echo filemtime ( "./themes/$theme/style.css" ) ; ?>">

</head>
<body style="overflow: hidden; -webkit-text-size-adjust: 100%;">
<div id="span_loading" style="display: none; position: absolute; right: 5px; bottom: 5px; text-align: right; z-Index: 20;" class="round">
	<img src="./themes/<?php echo $theme ?>/loading_chat.gif" width="16" height="16" border="0" alt="" title="loading..." alt="loading..." class="info_neutral">
</div>
<div id="chat_canvas" style="min-height: 100%; width: 100%;"></div>
<div id="request_body_wrapper_wrapper" style="position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; opacity:0.0; filter:alpha(opacity=00);">
	<?php include_once( "inc_embed_menu.php" ) ; ?>
	<div id="request_body_wrapper">
		<div id="request_body" style="padding: 35px; overflow-y: auto; overflow-x: hidden; -webkit-overflow-scrolling: touch;">

			<?php if ( !$embed || ( $embed && isset( $emlogos_hash[0] ) && $emlogos_hash[0] ) ): ?>
			<div id="chat_logo" style="padding-bottom: 15px;"><img src="<?php echo Util_Upload_GetLogo( "logo", $deptid ) ?>" border=0 style="max-width: 100%; border: 0px;"></div>
			<?php endif ; ?>
			<div style="display: none; margin-bottom: 15px;" class="info_content" id="div_online_pics" onClick="scroll_to_form()">
				<center>
				<table cellspacing=0 cellpadding=2 border=0>
				<tr>
					<td style="display: none; padding-left: 4px; padding-right: 4px;" id="td_pic_0" align="center"></td>
					<td style="display: none; padding-left: 4px; padding-right: 4px;" id="td_pic_1" align="center"></td>
					<td style="display: none; padding-left: 4px; padding-right: 4px;" id="td_pic_2" align="center"></td>
				</tr>
				<tr><td colspan=3><div class="info_good" style="text-align: center;" id="LANG_TXT_ONLINE"></div></td></tr>
				</table>
				</center>
			</div>
			<div id="chat_text_header" style="margin-bottom: 5px;"><span id="LANG_CHAT_WELCOME"></span></div>
			<div id="chat_text_header_sub" style=""><span id="LANG_CHAT_WELCOME_SUBTEXT"></span></div>
			<form method="POST" action="phplive_.php" id="theform" accept-charset="UTF-8">
			<input type="hidden" name="deptid" id="deptid" value="<?php echo ( isset( $requestinfo["deptID"] ) ) ? $requestinfo["deptID"] : $deptid ; ?>">
			<input type="hidden" name="deptid_orig" value="<?php echo $deptid_orig ?>">
			<input type="hidden" name="gid" id="gid" value="<?php echo $gid ; ?>">
			<input type="hidden" name="ces" id="ces" value="<?php echo ( isset( $requestinfo["ces"] ) ) ? $requestinfo["ces"] : "" ; ?>">
			<input type="hidden" name="onpage" id="onpage" value="<?php echo ( isset( $requestinfo["ces"] ) ) ? urlencode( Util_Format_URL( $requestinfo["onpage"] ) ) : urlencode( Util_Format_URL( $onpage ) ) ; ?>">
			<input type="hidden" name="title" id="title" value="<?php echo ( isset( $requestinfo["ces"] ) ) ? $requestinfo["title"] : htmlentities( $title, ENT_QUOTES, "UTF-8" ) ; ?>">
			<input type="hidden" name="win_dim" id="win_dim" value="">
			<input type="hidden" name="token" id="token" value="">
			<input type="hidden" name="embed" id="embed" value="<?php echo $embed ?>">
			<input type="hidden" name="vis_token" value="<?php echo $vis_token ?>">
			<input type="hidden" name="skp" id="skp" value="0">
			<input type="hidden" name="theme" id="theme" value="<?php echo $theme ?>">
			<input type="hidden" name="popout" id="popout" value="<?php echo $popout ?>">
			<input type="hidden" name="custom" id="custom" value="<?php echo rawurlencode( $custom ) ?>">
			<input type="hidden" name="opid" id="opid" value="<?php echo $opid ?>">
			<input type="hidden" name="vname_" id="vname_" value="">
			<input type="hidden" name="vemail_" id="vemail_" value="">
			<input type="hidden" name="vquestion_" id="vquestion_" value="">
			<input type="hidden" name="proid" id="proid" value="<?php echo $proid ?>">
			<input type="hidden" name="emarketid" id="emarketid" value="0">
			<input type="hidden" name="captcha" id="captcha" value="">

			<?php if ( $js_name || $js_email ): ?><input type="hidden" id="auto_pop" name="auto_pop" value="1"><?php endif ; ?>
			<?php if ( $js_name ): ?><input type="hidden" name="vname" value="<?php echo $vname ?>"><?php endif ; ?>
			<?php if ( $js_email ): ?><input type="hidden" name="vemail" value="<?php echo $vemail ?>"><?php endif ; ?>
			<div id="pre_chat_form" style="">
				<div id="div_vdeptids" style="display: none; margin-top: 15px;">
					<div style="margin-bottom: 3px;"><span id="chat_text_department"><span id="LANG_TXT_DEPARTMENT"></span></span></div>
					<select id="vdeptid" onChange="select_dept(this.value)" style="-webkit-appearance: none;" onClick="check_mobile_view('vdeptid', 0)"><option value=<?php echo ( $deptid < 100000000 ) ? 0 : $deptid ; ?>></option>
					<?php
						$selected = "" ;
						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$department = $departments[$c] ;
							$class = "offline" ;
							if ( $dept_online[$department["deptID"]] ) { $class = "online" ; }
							if ( count( $departments ) == 1 ) { $selected = "selected" ; }
							print "<option class=\"$class\" value=\"$department[deptID]\" $selected>$department[name] ()</option>" ;
						}
					?>
					</select>
				</div>
				<div id="div_offline_url" style="display: none; margin-top: 25px;"></div>
				<div id="table_pre_chat_form" style="display: none; margin-top: 15px;">
					<table cellspacing=0 cellpadding=0 border=0 id="table_pre_chat_form_table">
					<tr>
						<td width="50%" style="display: none; padding-right: 10px;" id="div_field_1" valign="top"></td>
						<td width="50%" style="display: none; padding-left: 10px;" id="div_field_2" valign="top"></td>
					</tr>
					<tr>
						<td width="50%" style="display: none; padding-right: 10px; padding-top: 15px;" id="div_field_3" valign="top"></td>
						<td width="50%" style="display: none; padding-left: 10px; padding-top: 15px;" id="div_field_4" valign="top"></td>
					</tr>
					<tr>
						<td width="50%" style="display: none; padding-right: 10px; padding-top: 15px;" id="div_field_5" valign="top"></td>
						<td width="50%" style="display: none; padding-left: 10px; padding-top: 15px;" id="div_field_6" valign="top"></td>
					</tr>
					<tr>
						<td width="50%" style="display: none; padding-right: 10px; padding-top: 15px;" id="div_field_7" valign="top"></td>
						<td width="50%" style="display: none; padding-left: 10px; padding-top: 15px;" id="div_field_8" valign="top"></td>
					</tr>
					<tr>
						<td colspan=2 style="display: none; padding-top: 15px;" id="div_field_9" valign="top"></td>
					</tr>
					</table>
				</div>
			</div>
			<div id="div_checkbox_emarketing_wrapper" style="display: none; margin-top: 25px; padding-bottom: 15px;">
				<div><span id="span_message"><?php echo $emarketinginfo["message"] ?><?php echo ( $emarketinginfo["isreq"] ) ? "" : " ($LANG[TXT_OPTIONAL])" ; ?> </span></div>
				<div style="margin-top: 15px;"><span class="info_clear" style="padding: 3px; cursor: pointer;" onclick="$('#emarket_val_1').prop('checked', true);"><input type="radio" name="emarket_val" id="emarket_val_1" value="1" style="-webkit-transform:scale(1.2,1.2); -moz-transform:scale(1.2,1.2); -ms-transform:scale(1.2,1.2); transform:scale(1.2,1.2);"> &nbsp;<span id="span_val_1"><?php echo $emarketinginfo["val_1"] ?></span></span> &nbsp; &nbsp; <span class="info_clear" style="padding: 3px; cursor: pointer;" onclick="$('#emarket_val_0').prop('checked', true);"><input type="radio" name="emarket_val" id="emarket_val_0" value="0" style="-webkit-transform:scale(1.2,1.2); -moz-transform:scale(1.2,1.2); -ms-transform:scale(1.2,1.2); transform:scale(1.2,1.2);"> &nbsp;<span id="span_val_0"><?php echo $emarketinginfo["val_0"] ?></span></span><input type="radio" name="emarket_val" id="emarket_val_neg_1" value="-1" style="display: none;" checked></div>
			</div>
			<div id="div_checkbox_data_policy_wrapper" style="display: none; margin-top: 25px; padding-bottom: 15px;">
				<div style="padding-left: 4px;">
					<table cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td><div id="div_checkbox_data_policy"><input type="checkbox" id="checkbox_data_policy" onClick="$('#checkbox_data_policy_arrow').hide();" style="-webkit-transform:scale(1.2,1.2); -moz-transform:scale(1.2,1.2); -ms-transform:scale(1.2,1.2); transform:scale(1.2,1.2);"></div></td>
						<td style="padding-left: 10px;"><div id="div_notice_data_policy"><span id="checkbox_data_policy_arrow" style="display: none;">&larr;</span> <span id="div_notice_text_checkbox"></span></div></td>
					</tr>
					</table>
				</div>
			</div>
			</form>
			<?php if ( $recaptcha_enabled ) { include_once( "$CONF[DOCUMENT_ROOT]/addons/recaptcha/inc_google_div.php" ) ; } ?>

			<div id="pre_chat_no_depts" style="display: none; margin-top: 10px;" class="info_error">
				There are no visible live chat departments at this time.
				
				<div style="margin-top: 15px;">If you are the <b>Setup Admin</b>, create a department or set a department <b>"Visible for Selection"</b> at:</div>
				<div style="margin-top: 15px;" class="info_box"><code>Setup Admin &gt; Departments</code></div>
			</div>

		</div>
		<div id="chat_submit_btn" style="display: none; padding: 0px !important; z-Index: 15;">
			<div style="padding-top: 15px; padding-bottom: 100px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td><button id="chat_button_start" class="input_button" type="button" style="width: 160px; height: 45px; font-size: 14px; font-weight: bold; padding: 6px;"><span id="LANG_CHAT_BTN_START_CHAT"></span></button></td>
					<td align="right" valign="bottom" width="100%"><div id="chat_text_powered" style="text-align: right; font-size: 10px; opacity: 0.5; filter: alpha(opacity=50);"><?php if ( isset( $CONF["KEY"] ) && ( $CONF["KEY"] == md5($KEY."_key_phplive") ) ): ?><?php else: ?>powered by<br><a href="https://www.phplivesupport.com/?plk=pi-23-78m-m" target="_blank" style="letter-spacing: .8px;">PHP Live!</a><?php endif ; ?></div></td>
				</tr>
				</table>
			</div>
		</div>
	</div>
	<div id="div_policy_wrapper" style="display: none; padding: 10px;" class="info_content">
		<div style="text-align: center; cursor: pointer;" class="info_error" onClick="toggle_policy(0, 1)"><?php echo ( isset( $LANG["CHAT_CLOSE"] ) ) ? $LANG["CHAT_CLOSE"] : "Close" ; ?></div>
		<div id="div_policy" style="margin-top: 15px; height: 180px; overflow: auto; -webkit-overflow-scrolling: touch;"></div>
	</div>
</div>

<script data-cfasync="false" type="text/javascript" src="./js/global.js?<?php echo filemtime ( "./js/global.js" ) ; ?>"></script>
<script data-cfasync="false" type="text/javascript" src="./js/framework.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="./js/jquery_md5.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="./js/<?php echo $autolinker_js_file ?>?<?php echo $VERSION ?>"></script>
<?php if ( $recaptcha_enabled ) { include_once( "$CONF[DOCUMENT_ROOT]/addons/recaptcha/inc_google_js.php" ) ; } ?>

<script data-cfasync="false" type="text/javascript">
<!--
	"use strict" ;
	var embed = <?php echo $embed ?> ;
	var mobile = ( <?php echo $mobile ?> ) ? is_mobile() : 0 ;
	var phplive_mobile = 0 ; var phplive_ios = 0 ;
	var phplive_userAgent = navigator.userAgent || navigator.vendor || window.opera ;
	if ( phplive_userAgent.match( /iPad/i ) || phplive_userAgent.match( /iPhone/i ) || phplive_userAgent.match( /iPod/i ) )
	{
		phplive_ios = 1 ;
		if ( phplive_userAgent.match( /iPad/i ) ) { phplive_mobile = 0 ; }
		else { phplive_mobile = 1 ; }
	}
	else if ( phplive_userAgent.match( /Android/i ) ) { phplive_mobile = 2 ; }

	var popout = <?php echo ( !$preview && ( isset( $VALS["POPOUT"] ) && ( $VALS["POPOUT"] == "on" ) ) ) ? 1 : 0 ?> ; var preview = <?php echo $preview ; ?> ;
	var win_width = screen.width ;
	var win_height = screen.height ;

	var deptid = <?php echo $deptid ?> ;
	var dept_online_text = new Object ;
	var dept_offline = new Object ;
	var dept_settings = new Object ;
	var dept_customs = new Object ;
	var dept_offline_form = new Object ;
	var dept_prechat_form = new Object ;
	var dept_haspolicy = new Object ;
	var dept_offline_urls = new Object ;

	var custom_hash = new Object ;

	var dept_addon_emarketings = new Object ;

	var global_form_x ; // global var for original form top position for unset
	var global_diff_height ;
	var global_div_online_pics_scrolltop ;

	var onoff = 0 ;
	var custom_required = 0 ; var custom_required2 = 0 ; var custom_required3 = 0 ;
	var js_email = "<?php echo $js_email ?>" ;
	var phplive_browser = navigator.appVersion ; var phplive_mime_types = "" ;
	var phplive_display_width = screen.availWidth ; var phplive_display_height = screen.availHeight ; var phplive_display_color = screen.colorDepth ; var phplive_timezone = new Date().getTimezoneOffset() ;
	if ( navigator.mimeTypes.length > 0 ) { for (var x=0; x < navigator.mimeTypes.length; x++) { phplive_mime_types += navigator.mimeTypes[x].description ; } }
	var phplive_browser_token = phplive_md5( phplive_display_width+phplive_display_height+phplive_display_color+phplive_timezone+phplive_browser+phplive_mime_types ) ;
	var phplive_fetch_token = 0 ;
	if ( phplive_browser_token != "<?php echo $token ?>" )
	{
		phplive_fetch_token = 1 ;
		location.href = "fetch_token.php?<?php echo $query ?>" ;
	}
	var autolinker = new Autolinker( { newWindow: true, stripPrefix: false } ) ;
	var win_st_resizing ;
	var st_status_listener ; // prep for si_win_status
	var si_win_status ; var win_minimized ;

	var depts_lang = new Object ; <?php echo $depts_lang ?> ;
	var LANG_TEXTS = new Object ; var LANG_DBS = new Object ; var LANG_MERGED = new Object ; 
	<?php
		foreach ( $LANG_TEXTS as $lang_temp => $lang_vars )
		{
			print "LANG_TEXTS['$lang_temp'] = new Object ; " ;
			foreach ( $lang_vars as $var_name => $var_value ) { print "LANG_TEXTS['$lang_temp']['$var_name'] = '".Util_Format_ConvertQuotes( $var_value )."' ; " ; }
		}
		foreach ( $LANG_DBS as $deptid_temp => $lang_vars )
		{
			print "LANG_DBS[$deptid_temp] = new Object ; " ;
			foreach ( $lang_vars as $var_name => $var_value ) { print "LANG_DBS[$deptid_temp]['$var_name'] = '".Util_Format_Trim( $var_value )."' ; " ; }
		}
	?> var total_depts = 0 ;

	// [ START ] document ready previous code
	$('#win_dim').val( win_width + " x " + win_height ) ;

	<?php echo $dept_online_text ?>
	<?php echo $dept_offline ?>
	<?php echo $dept_settings ?>
	<?php echo $dept_customs ?>
	<?php echo $dept_offline_form ?>
	<?php echo $js_custom_hash ?>
	<?php echo $dept_prechat_form ?>
	<?php echo $dept_haspolicy ?>
	<?php echo $dept_addon_emarketings ?>
	<?php echo $dept_offline_urls ?>

	<?php if ( $preview == 2 ): ?>
	$('#chat_embed_title').css({ 'opacity': '1' }) ;
	<?php endif ; ?>

	$('#chat_button_start').html( "<?php echo $LANG["CHAT_BTN_START_CHAT"] ?>" ).unbind('click').bind('click', function( ) {
		start_chat( ) ;
	}) ;

	for ( var key_ in dept_offline ) {
		total_depts++ ;
	}
	if ( !total_depts )
	{
		$('#pre_chat_form').hide( ) ;
		$('#pre_chat_no_depts').show( ) ;
	}

	$('#token').val( phplive_browser_token ) ;

	if ( mobile ) { $('#embed_win_popout').hide() ; }
	else if ( popout ) { $('#embed_win_popout').show() ; }

	$('#chat_submit_btn').show() ;

	<?php if ( count( $departments ) > 1 ) : ?>$('#div_vdeptids').show( ) ;<?php endif ; ?>

	if ( typeof( $('#pre_chat_form').position() ) != "undefined" )
	{
		var chat_form_pos = $('#pre_chat_form').position() ; global_form_x = chat_form_pos.top ;
		var chat_body_height = $('#request_body').height() ;
		var chat_form_height = $('#pre_chat_form').height() ;
		var diff_height = parseInt(chat_body_height) - parseInt(chat_form_height) ;

		global_diff_height = diff_height - parseInt( chat_form_height ) ;
	}

	// delay so it renders correctly in some devices that may process too fast
	setTimeout( function(){ select_dept( deptid ) ; }, 100 ) ;

	if ( embed )
	{
		if ( phplive_fetch_token )
		{
			// should not be arriving here but a fallback
			if ( typeof( st_status_listener ) != "undefined" ) { clearTimeout( st_status_listener ) ; }
			st_status_listener = setTimeout( function(){ start_win_status_listener() ; }, 400 ) ;
		}
		else { start_win_status_listener() ; }
	}

	<?php if ( $redirected == 2 ): ?>do_alert( 0, "Invalid reCAPTCHA response." ) ;<?php endif ; ?>

	// [ END ] document ready previous code

	$(window).resize(function( ) {
		init_divs_pre() ;
	});

	function init_lang_texts( thedeptid )
	{
		var lang = ( typeof( depts_lang[thedeptid] ) != "undefined" ) ? depts_lang[thedeptid] : "english" ;
		var lang_var ;

		if ( typeof( LANG_TEXTS[lang] ) != "undefined" )
		{
			var LANG_TEXTS_NEW = jQuery.extend( true, {}, LANG_TEXTS[lang] ) ;
			for ( var lang_var in LANG_TEXTS_NEW )
			{
				if ( ( typeof( LANG_DBS[thedeptid] ) != "undefined" ) && ( typeof( LANG_DBS[thedeptid][lang_var] ) != "undefined" ) ) { LANG_TEXTS_NEW[lang_var] = decodeURIComponent( LANG_DBS[thedeptid][lang_var] ) ; }
			}
			LANG_MERGED = jQuery.extend( true, {}, LANG_TEXTS_NEW ) ;

			// to ensure it fits the visible space
			LANG_MERGED["TXT_LIVECHAT"] = LANG_MERGED["TXT_LIVECHAT"].substring( 0, 20 ) ;

			$( '*', 'body' ).find('span').each( function () {
				var div_name = this.id ;
				if ( div_name.indexOf( "LANG_" ) == 0 )
				{
					$(this).html( LANG_MERGED[div_name.slice(5)] ) ;
				}
			}) ;
			$('#vdeptid option').each(function() {
				var text_status = ( $(this).attr( "class" ) == "online" ) ? LANG_MERGED["TXT_ONLINE"] : LANG_MERGED["TXT_OFFLINE"] ;
				$(this).text( $(this).text().replace( / \((.*?)$/, " ("+text_status+")" ) ) ;
			});
			$('#vdeptid option').eq(0).text( LANG_MERGED["CHAT_SELECT_DEPT"] ) ;
		}
		else
			do_alert( 0, "Language file not found ["+lang+"]." ) ;
	}

	function init_divs_pre()
	{
		var chat_body_padding = $('#request_body').css('padding-left') ;
		chat_body_padding = ( typeof( chat_body_padding ) != "undefined" ) ? parseInt( chat_body_padding.replace( /px/, "" ) ) : 0 ;

		var input_padding = ( $('#vdeptid').length ) ? $('#vdeptid').css( 'padding-top' ).replace( /px/, "" ) : 8 ;
		input_padding -= 5 ; // account for v.4.7.9.9.6 themes with 8px paddings

		var browser_height = $('body').height( ) ;
		var buffer_padding = ( mobile ) ? 140 : 150 ;
		var body_height = browser_height - buffer_padding ;
		if ( embed )
		{
			body_height = body_height - 25 - $('#chat_embed_header').height() ;
		} else { body_height -= 15 ; }
		body_height -= Math.floor( chat_body_padding/2 ) ;
		body_height = body_height+"px" ;

		var submit_btn_padding_top = ( mobile ) ? 15 : 15 ;
		submit_btn_padding_top = submit_btn_padding_top+"px" ;

		var browser_width = $('body').width( ) ;
		var body_width = browser_width - (chat_body_padding*2) ;
		body_width = body_width+"px" ;

		var chat_btn_left = chat_body_padding+"px" ;

		var deptid_width = $('#request_body').width( ) ;
		var vquestion_width = deptid_width - 18 ;
		var input_width = Math.floor( deptid_width/2 ) - 25 - input_padding ;

		$('#request_body').css({'height': body_height}) ;
		$('#table_pre_chat_form_table').css({'width': body_width}) ;
		$('#chat_text_powered').css({'margin-right': chat_btn_left, 'font-size': '10px'}) ;
		$('#chat_submit_btn').css({'margin-left': chat_btn_left, 'margin-top': submit_btn_padding_top}) ;
		$('#vdeptid').css({'width': deptid_width}) ;

		$('#vname').css({'width': input_width}) ;
		if ( !mobile && $('#vname').length && !browser_filter )
		{
			// focus fixes IE7 input lock quirk
			$('#vname').focus() ;
		}
		$('#vemail').css({'width': input_width}) ;
		$('#vsubject').css({'width': input_width}) ;
		$('#vquestion').css({'width': vquestion_width}) ;
		$('#custom_field_input_1').css({'width': input_width}) ;
		$('#custom_field_input_2').css({'width': input_width}) ;
		$('#custom_field_input_3').css({'width': input_width}) ;
		$('#div_online_pics').css({'width': vquestion_width}) ;

		if ( mobile ) {
			$('#vquestion').css({'height': "45px" }) ;
			if ( <?php echo ( isset( $VARS_MISC_MOBILE_MAX_QUIRK ) && $VARS_MISC_MOBILE_MAX_QUIRK ) ? 1 : 0 ; ?> ) { $("body :input").each(function(){ $(this).css({'font-size': '16px'}) ; }); }
		}
	}

	function init_divs_input( thedeptid, theonoff )
	{
		$("#table_pre_chat_form").find('*').each( function(){
			var div_name = this.id ;
			if ( div_name.indexOf("div_field_") != -1 )
				$(this).hide() ;
		} );

		var index = 1 ;
		thedeptid = parseInt( thedeptid ) ; // needed to fix possible issues on some browsers not recognizing it as number
		if ( ( thedeptid && ( thedeptid < 100000000 ) ) && ( !theonoff || ( typeof( dept_prechat_form[thedeptid] ) == "undefined" ) || ( ( typeof( dept_prechat_form[thedeptid] ) != "undefined" ) && parseInt( dept_prechat_form[thedeptid] ) ) ) )
		{
			for ( var key in show_divs )
			{
				if ( show_divs.hasOwnProperty(key) )
				{
					var thisfield = show_divs[key] ;
					if ( typeof( thisfield["required"] ) != "undefined" )
					{
						if ( key == "vname" )
						{
							var optional_string = show_divs["vname"]["optional"] ;
							$('#div_field_'+index).html( "<div style=\"margin-bottom: 3px;\">"+LANG_MERGED["TXT_NAME"]+" "+optional_string+"</div><input type=\"input\" class=\"input_text\" id=\"vname\" name=\"vname\" maxlength=\"30\" value=\"<?php echo isset( $requestinfo["vname"] ) ? $requestinfo["vname"] : $vname ; ?>\" onKeyPress=\"return noquotestags(event);check_mobile_view('vname', 1);\" onBlur=\"check_mobile_view('vname', 0)\" <?php echo ( $js_name ) ? "disabled" : "" ?> autocomplete=\"off\">" ).show() ;
							++index ;
						}
						else if ( ( key == "vemail" ) && ( !show_divs["vemail"]["optional"] || !theonoff ) && thedeptid )
						{
							var optional_string = ( !theonoff ) ? "" : show_divs["vemail"]["optional"] ;
							$('#div_field_'+index).html( "<div style=\"margin-bottom: 3px;\">"+LANG_MERGED["TXT_EMAIL"]+" "+optional_string+"</div><input type=\"input\" class=\"input_text\" id=\"vemail\" name=\"vemail\" maxlength=\"160\" value=\"<?php echo isset( $requestinfo["vemail"] ) ? $requestinfo["vemail"] : $vemail ; ?>\" onBlur=\"check_mobile_view('vemail', 0)\" onKeyPress=\"return justemails(event);check_mobile_view('vemail', 1);\" <?php echo ( $js_email ) ? "disabled" : "" ?>>" ).show() ;
							++index ;
						}
						else if ( key == "custom_field_input_1" )
						{
							var disabled = ( show_divs["custom_field_input_1"]["disabled"] || show_divs["custom_field_input_1"]["value"] ) ? "disabled" : "" ;
							$('#div_field_'+index).html( "<div style=\"margin-bottom: 3px;\">"+show_divs["custom_field_input_1"]["title"]+show_divs["custom_field_input_1"]["optional"]+"</div><input type=\"input\" class=\"input_text\" id=\"custom_field_input_1\" name=\"custom_field_input_1\" maxlength=\"70\" onKeyPress=\"return noquotestags(event);check_mobile_view('custom_field_input_1', 1);\" onBlur=\"check_mobile_view('custom_field_input_1', 0)\" value=\""+show_divs["custom_field_input_1"]["value"]+"\" "+disabled+" autocomplete=\"off\">" ).show() ;
							++index ;
						}
						else if ( key == "custom_field_input_2" )
						{
							var disabled = ( show_divs["custom_field_input_2"]["disabled"] || show_divs["custom_field_input_2"]["value"] ) ? "disabled" : "" ;
							$('#div_field_'+index).html( "<div style=\"margin-bottom: 3px;\">"+show_divs["custom_field_input_2"]["title"]+show_divs["custom_field_input_2"]["optional"]+"</div><input type=\"input\" class=\"input_text\" id=\"custom_field_input_2\" name=\"custom_field_input_2\" maxlength=\"70\" onKeyPress=\"return noquotestags(event);check_mobile_view('custom_field_input_2', 1);\" onBlur=\"check_mobile_view('custom_field_input_2', 0)\" value=\""+show_divs["custom_field_input_2"]["value"]+"\" "+disabled+" autocomplete=\"off\">" ).show() ;
							++index ;
						}
						else if ( key == "custom_field_input_3" )
						{
							var disabled = ( show_divs["custom_field_input_3"]["disabled"] || show_divs["custom_field_input_3"]["value"] ) ? "disabled" : "" ;
							$('#div_field_'+index).html( "<div style=\"margin-bottom: 3px;\">"+show_divs["custom_field_input_3"]["title"]+show_divs["custom_field_input_3"]["optional"]+"</div><input type=\"input\" class=\"input_text\" id=\"custom_field_input_3\" name=\"custom_field_input_3\" maxlength=\"70\" onKeyPress=\"return noquotestags(event);check_mobile_view('custom_field_input_3', 1);\" onBlur=\"check_mobile_view('custom_field_input_3', 0)\" value=\""+show_divs["custom_field_input_3"]["value"]+"\" "+disabled+" autocomplete=\"off\">" ).show() ;
							++index ;
						}
						else if ( key == "vsubject" )
						{
							$('#div_field_'+index).html( "<div style=\"margin-bottom: 3px;\">"+LANG_MERGED["TXT_SUBJECT"]+"</div><input type=\"input\" class=\"input_text\" id=\"vsubject\" name=\"vsubject\" maxlength=\"125\" value=\"<?php echo ( $vsubject ) ? $vsubject : "" ; ?>\" onKeyPress=\"return noquotestags(event);check_mobile_view('vsubject', 1);\" onBlur=\"check_mobile_view('vsubject', 0)\" autocomplete=\"off\">" ).show() ;
							++index ;
						}
						else if ( ( key == "vquestion" ) && !show_divs["vquestion"]["optional"] )
						{
							$('#div_field_9').html( "<div style=\"margin-bottom: 3px;\">"+LANG_MERGED["TXT_QUESTION"]+" "+show_divs["vquestion"]["optional"]+"</div><textarea class=\"input_text\" id=\"vquestion\" name=\"vquestion\" rows=\"3\" wrap=\"virtual\" style=\"resize: vertical;\" onKeyPress=\"check_mobile_view('vquestion', 1)\" onBlur=\"check_mobile_view('vquestion', 0)\" <?php echo ( isset( $VALS["AUTOCORRECT_V"] ) && !$VALS["AUTOCORRECT_V"] ) ? "autocomplete='off' autocorrect='off'" : "" ; ?>><?php echo isset( $requestinfo["question"] ) ? $requestinfo["question"] : preg_replace( "/\r\n/", "\\r\\n", $vquestion ) ; ?></textarea>" ).show() ;
						}
					}
				}
			}
		} init_divs_pre() ;
		if ( phplive_ios ) { setTimeout( function(){ init_divs_pre() ; } ) } // fix animate stick bug
	}

	function display_window()
	{
		$('#request_body_wrapper_wrapper').animate({
			opacity: 1
		}, 500, function() {
			//setTimeout( function(){ $('#span_loading').fadeOut("fast") ; }, 300 ) ;
		});
	}

	var show_divs ;
	function select_dept( thedeptid )
	{
		init_lang_texts( thedeptid ) ;
		$('#deptid').val( thedeptid ) ;
		$('#custom_field_input_1').val('') ;
		$('#custom_field_input_2').val('') ;
		$('#custom_field_input_3').val('') ;
		$('#div_offline_url').hide() ;

		show_divs = new Object ;
		show_divs["vname"] = new Object ;
		if ( ( ( typeof( dept_settings[thedeptid] ) != "undefined" ) && dept_settings[thedeptid][3] ) || !thedeptid )
		{
			$('#optional_name').html( "" ) ;
			show_divs["vname"]["required"] = 1 ;
			show_divs["vname"]["optional"] = "" ;
		}
		else
		{
			$('#optional_name').html( " ("+LANG_MERGED["TXT_OPTIONAL"]+")" ) ;
			show_divs["vname"]["required"] = 0 ;
			show_divs["vname"]["optional"] = " ("+LANG_MERGED["TXT_OPTIONAL"]+")" ;
		}

		show_divs["vemail"] = new Object ;
		if ( ( ( typeof( dept_settings[thedeptid] ) != "undefined" ) && dept_settings[thedeptid][0] ) || !thedeptid )
		{
			$('#optional_email').html( "" ) ;
			show_divs["vemail"]["required"] = 1 ;
			show_divs["vemail"]["optional"] = "" ;
		}
		else
		{
			$('#optional_email').html( " ("+LANG_MERGED["TXT_OPTIONAL"]+")" ) ;
			show_divs["vemail"]["required"] = 0 ;
			show_divs["vemail"]["optional"] = " ("+LANG_MERGED["TXT_OPTIONAL"]+")" ;
		}

		custom_required = 0 ; custom_required2 = 0 ; custom_required3 = 0 ;
		$('#div_customs').hide( ) ; $('#div_customs2').hide( ) ; $('#div_customs3').hide( ) ;

		if ( thedeptid && ( typeof( dept_customs[thedeptid] ) != "undefined" ) )
		{
			custom_required = dept_customs[thedeptid][1] ;
			show_divs["custom_field_input_1"] = new Object ;
			if ( dept_customs[thedeptid][0] )
			{
				if ( typeof( custom_hash[dept_customs[thedeptid][0]] ) != "undefined" ) { show_divs["custom_field_input_1"]["disabled"] = 1 ; }
				else { show_divs["custom_field_input_1"]["disabled"] = 0 ; }
				show_divs["custom_field_input_1"]["required"] = custom_required ;
			}
			else { custom_required = 0 ; }
			show_divs["custom_field_input_1"]["optional"] = ( custom_required ) ? "" : " ("+LANG_MERGED["TXT_OPTIONAL"]+")" ;
			show_divs["custom_field_input_1"]["title"] = dept_customs[thedeptid][0] ;
			show_divs["custom_field_input_1"]["value"] = ( typeof( custom_hash[dept_customs[thedeptid][0]] ) != "undefined" ) ? custom_hash[dept_customs[thedeptid][0]] : "" ;

			custom_required2 = dept_customs[thedeptid][3] ;
			show_divs["custom_field_input_2"] = new Object ;
			if ( dept_customs[thedeptid][2] )
			{
				if ( typeof( custom_hash[dept_customs[thedeptid][2]] ) != "undefined" ) { show_divs["custom_field_input_2"]["disabled"] = 1 ; }
				else { show_divs["custom_field_input_1"]["disabled"] = 0 ; }
				show_divs["custom_field_input_2"]["required"] = custom_required2 ;
			}
			else { custom_required2 = 0 ; }
			show_divs["custom_field_input_2"]["optional"] = ( custom_required2 ) ? "" : " ("+LANG_MERGED["TXT_OPTIONAL"]+")" ;
			show_divs["custom_field_input_2"]["title"] = dept_customs[thedeptid][2] ;
			show_divs["custom_field_input_2"]["value"] = ( typeof( custom_hash[dept_customs[thedeptid][2]] ) != "undefined" ) ? custom_hash[dept_customs[thedeptid][2]] : "" ;

			custom_required3 = dept_customs[thedeptid][5] ;
			show_divs["custom_field_input_3"] = new Object ;
			if ( dept_customs[thedeptid][4] )
			{
				if ( typeof( custom_hash[dept_customs[thedeptid][4]] ) != "undefined" ) { show_divs["custom_field_input_3"]["disabled"] = 1 ; }
				else { show_divs["custom_field_input_3"]["disabled"] = 0 ; }
				show_divs["custom_field_input_3"]["required"] = custom_required3 ;
			}
			else { custom_required3 = 0 ; }
			show_divs["custom_field_input_3"]["optional"] = ( custom_required3 ) ? "" : " ("+LANG_MERGED["TXT_OPTIONAL"]+")" ;
			show_divs["custom_field_input_3"]["title"] = dept_customs[thedeptid][4] ;
			show_divs["custom_field_input_3"]["value"] = ( typeof( custom_hash[dept_customs[thedeptid][4]] ) != "undefined" ) ? custom_hash[dept_customs[thedeptid][4]] : "" ;
		}

		$('#chat_text_header').show() ;

		if ( ( $('#vdeptid option:selected').attr( "class" ) == "offline" ) )
		{
			onoff = 0 ;
			$('#chat_text_header').html( LANG_MERGED["MSG_LEAVE_MESSAGE"] ) ;
			$('#chat_text_header_sub').html( autolink_it( dept_offline[thedeptid] ) ) ;

			if ( parseInt( js_email ) ) { show_divs["vemail"]["disabled"] = 1 ; }
			show_divs["vsubject"] = new Object ;
			show_divs["vsubject"]["required"] = 1 ;
			show_divs["vquestion"] = new Object ;
			show_divs["vquestion"]["required"] = 1 ;
			show_divs["vquestion"]["optional"] = "" ;
			show_divs["vemail"]["required"] = 1 ;
			$('#chat_button_start').html( LANG_MERGED["CHAT_BTN_EMAIL"] ).unbind('click').bind('click', function( ) {
				send_email( ) ;
			});

			if ( ( typeof( dept_offline_urls[0] ) != "undefined" ) || ( typeof( dept_offline_urls[thedeptid] ) != "undefined" ) ) { dept_offline_form[thedeptid] = 0 ; }
			if ( typeof( dept_offline_form[thedeptid] ) != "undefined" )
			{
				if ( parseInt( dept_offline_form[thedeptid] ) ) { $('#table_pre_chat_form').show() ; $('#chat_submit_btn').css('opacity', '1') ; }
				else
				{
					$('#table_pre_chat_form').hide() ; $('#chat_submit_btn').css('opacity', '0.0') ;
					$('#chat_button_start').prop("onclick", null).off("click") ;
					if ( ( typeof( dept_offline_urls[0] ) != "undefined" ) || ( typeof( dept_offline_urls[thedeptid] ) != "undefined" ) )
					{
						var offline_url = ( typeof( dept_offline_urls[thedeptid] ) != "undefined" ) ? dept_offline_urls[thedeptid] : dept_offline_urls[0] ;
						$('#chat_button_start').unbind('click') ;
						$('#div_offline_url').html('<big><a href="'+offline_url+'" target="_top">'+offline_url+'</a></big>').show() ;
					}
				}
			} else { $('#table_pre_chat_form').show() ; $('#chat_submit_btn').css('opacity', '1') ; }
		}
		else
		{
			onoff = 1 ;
			$('#chat_text_header').html( LANG_MERGED["CHAT_WELCOME"] ) ;
			$('#chat_text_header_sub').html( autolink_it( LANG_MERGED["CHAT_WELCOME_SUBTEXT"] ) ) ;

			if ( thedeptid && ( typeof( dept_settings[thedeptid] ) != "undefined" ) )
			{
				if ( dept_settings[thedeptid][0] )
				{
					if ( parseInt( js_email ) ) { show_divs["vemail"]["disabled"] = 1 ; }
				}
				show_divs["vquestion"] = new Object ;
				if ( dept_settings[thedeptid][2] ) { show_divs["vquestion"]["required"] = 1 ; show_divs["vquestion"]["optional"] = "" ; }
				else { show_divs["vquestion"]["required"] = 0 ; show_divs["vquestion"]["optional"] = " ("+LANG_MERGED["TXT_OPTIONAL"]+")" ; }
			}

			if ( parseInt( thedeptid ) && ( thedeptid <= 100000000 ) )
			{
				$('#chat_button_start').html( LANG_MERGED["CHAT_BTN_START_CHAT"] ).unbind('click').bind('click', function( ) {
					start_chat( ) ;
				});
			}
			else
			{
				$('#chat_button_start').html( LANG_MERGED["TXT_SUBMIT"] ).attr( "disabled", false ).unbind('click').bind('click', function( ) {
					start_chat( ) ;
				});
			}

			if ( <?php echo $dept_offline_hasform ?> ) { $('#table_pre_chat_form').show() ; $('#chat_submit_btn').css('opacity', '1') ; }
		}
		if ( typeof( dept_haspolicy[thedeptid] ) != "undefined" )
		{
			if ( onoff || ( !onoff && dept_offline_form[thedeptid] ) )
			{
				$('#div_checkbox_data_policy_wrapper').show() ;
				$('#div_notice_text_checkbox').html( decodeURIComponent( dept_haspolicy[thedeptid] ) + ' <span id="span_policy_loading" style="display: none;"><img src="./themes/<?php echo $theme ?>/loading_chat.gif" width="12" height="12" border="0" alt="loading..." title="loading..." class="round"></span>' ) ;
			}
			else
				$('#div_checkbox_data_policy_wrapper').hide() ;
		}
		else { $('#div_checkbox_data_policy_wrapper').hide() ; }
		$('#checkbox_data_policy').prop( "checked", false ) ;
		if ( ( ( show_divs["vemail"]["required"] && ( ( typeof( dept_prechat_form[deptid] ) != "undefined" ) && parseInt( dept_prechat_form[deptid] ) ) ) || !onoff ) && ( typeof( dept_addon_emarketings[thedeptid] ) != "undefined" ) && <?php echo $emarketinginfo["id"] ?> )
		{
			if ( onoff || ( !onoff && dept_offline_form[thedeptid] ) )
				$('#div_checkbox_emarketing_wrapper').show() ;
			else
				$('#div_checkbox_emarketing_wrapper').hide() ;
		}
		else
		{
			$('#div_checkbox_emarketing_wrapper').hide() ;
		}

		display_window() ;
		init_divs_input( thedeptid, onoff ) ;

		if ( !parseInt( thedeptid ) || ( parseInt( thedeptid ) && onoff ) )
		{
			fetch_online_pics( thedeptid ) ;
		}
		else
			close_online_pics() ;
	}

	function close_online_pics()
	{
		$('#div_online_pics').fadeOut( "fast" ).promise( ).done(function( ) {
			/*
			$('#request_body').animate({
				scrollTop: 0
			}, 'slow');
			*/
		}) ;
	}

	function fetch_online_pics( thedeptid )
	{
		var json_data = new Object ;
		var unique = unixtime( ) ;

		$.ajax({
		type: "POST",
		url: "./ajax/actions.php",
		data: "action=fetch_online_pics&deptid="+thedeptid+"&"+unique,
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				do_alert( 0, err ) ;
				return false ;
			}

			var json_length = json_data.profile_pics.length ;
			if ( json_data.status && json_length )
			{
				$("#div_online_pics").find('*').each( function(){
					var div_name = this.id ;
					if ( div_name.indexOf("td_pic_") != -1 )
						$(this).hide() ;
				} );

				var pic_string = "" ;
				for ( var c = 0; c < json_length; ++c )
				{
					var name = decodeURIComponent( json_data.profile_pics[c]["name"] ) ;
					var pic = decodeURIComponent( json_data.profile_pics[c]["pic"] ) ;
					$('#td_pic_'+c).html( "<img src=\""+pic+"\" width=\"50\" height=\"50\" border=\"0\" alt=\"\" class=\"round\" title=\""+name+"\" alt=\""+name+"\">" ).fadeIn("fast") ;
				}

				if ( json_length )
				{
					if ( typeof( global_div_online_pics_scrolltop ) == "undefined" )
					{
						global_div_online_pics_scrolltop = ( $('#chat_text_header_sub').length ) ? $('#chat_text_header_sub').offset().top - 10 : 0 ;
					}
					$('#div_online_pics').fadeIn( "fast" ).promise( ).done(function( ) {
						$('#LANG_TXT_ONLINE').html( LANG_MERGED["TXT_ONLINE"] ) ;
					}) ;
				}
				else
					close_online_pics() ;
			}
			else
				close_online_pics() ;
		}, error:function (xhr, ajaxOptions, thrownError){ } });
	}

	function check_form( theflag )
	{
		var error = 0 ;
		var deptid = parseInt( $('#deptid').val( ) ) ;

		if ( !theflag && ( typeof( dept_prechat_form[deptid] ) != "undefined" ) && !parseInt( dept_prechat_form[deptid] ) )
		{
			if ( ( typeof( dept_haspolicy[deptid] ) != "undefined" ) && !$('#checkbox_data_policy').is(':checked') )
			{
				$('#checkbox_data_policy_arrow').show() ;
				$('#div_notice_data_policy').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
				return false ;
			}
			else
			{
				$('#skp').val(1) ;
				return true ;
			}
		}
		if ( !deptid || ( deptid >= 100000000 ) ){
			$('#vdeptid').fadeOut('fast', function(){ $(this).fadeIn('fast', function(){ $(this).addClass('input_focus') ; }) ; }) ;
			do_alert( 0, LANG_MERGED["CHAT_JS_CUSTOM_BLANK"] ) ;
			return false ;
		}

		var passed_check = 1 ;
		var vname_temp = $('#vname').val( ).replace(/ +/, "") ;
		if ( vname_temp == "" ) { $('#vname').val( "" ) ; }
		if ( !$('#vname').val( ) )
		{
			if ( dept_settings[deptid][3] )
			{
				$('#vname').fadeOut('fast', function(){ $(this).fadeIn('fast', function(){ $(this).addClass('input_focus') ; }) ; }) ;
				passed_check = 0 ;
			}
		}
		if ( !$('#vemail').val( ) ){
			if ( dept_settings[deptid][0] || theflag )
			{
				$('#vemail').fadeOut('fast', function(){ $(this).fadeIn('fast', function(){ $(this).addClass('input_focus') ; }) ; }) ;
				passed_check = 0 ;
			}
		}
		if ( !$('#vsubject').val( ) ){
			if ( theflag )
			{
				$('#vsubject').fadeOut('fast', function(){ $(this).fadeIn('fast', function(){ $(this).addClass('input_focus') ; }) ; }) ;
				if ( passed_check )
				{
					if ( $('#div_field_3').is(':visible') || $('#div_field_5').is(':visible') ) { $("#request_body").animate({ scrollTop: $(document).height() }, "slow") ; }
				}
				passed_check = 0 ;
			}
		}
		if ( custom_required && !$('#custom_field_input_1').val( ) ){
			$('#custom_field_input_1').fadeOut('fast', function(){ $(this).fadeIn('fast', function(){ $(this).addClass('input_focus') ; }) ; }) ;
			if ( passed_check )
			{
				if ( $('#div_field_3').is(':visible') || $('#div_field_5').is(':visible') ) { $("#request_body").animate({ scrollTop: $(document).height() }, "slow") ; }
			}
			passed_check = 0 ;
		}
		if ( custom_required2 && !$('#custom_field_input_2').val( ) ){
			$('#custom_field_input_2').fadeOut('fast', function(){ $(this).fadeIn('fast', function(){ $(this).addClass('input_focus') ; }) ; }) ;
			if ( passed_check )
			{
				if ( $('#div_field_3').is(':visible') || $('#div_field_5').is(':visible') ) { $("#request_body").animate({ scrollTop: $(document).height() }, "slow") ; }
			}
			passed_check = 0 ;
		}
		if ( custom_required3 && !$('#custom_field_input_3').val( ) ){
			$('#custom_field_input_3').fadeOut('fast', function(){ $(this).fadeIn('fast', function(){ $(this).addClass('input_focus') ; }) ; }) ;
			if ( passed_check )
			{
				if ( $('#div_field_3').is(':visible') || $('#div_field_5').is(':visible') ) { $("#request_body").animate({ scrollTop: $(document).height() }, "slow") ; }
			}
			passed_check = 0 ;
		}
		var vquestion_temp = ( $('#vquestion').val( ) ) ? $('#vquestion').val( ).replace(/ +/, "") : "" ;
		if ( vquestion_temp == "" ) { $('#vquestion').val( "" ) ; }
		if ( !$('#vquestion').val( ) ){
			if ( dept_settings[deptid][2] || theflag )
			{
				$('#vquestion').fadeOut('fast', function(){ $(this).fadeIn('fast', function(){ $(this).addClass('input_focus') ; }) ; }) ;
				if ( passed_check )
				{
					if ( $('#div_field_9').is(':visible') ) { $("#request_body").animate({ scrollTop: $(document).height() }, "slow") ; }
				}
				passed_check = 0 ;
			}
		}
		if ( !passed_check )
		{
			do_alert( 0, LANG_MERGED["CHAT_JS_CUSTOM_BLANK"] ) ;
			return false ;
		}
		if ( !check_email( $('#vemail').val( ) ) ){
			if ( dept_settings[deptid][0] || theflag )
			{
				$('#vemail').fadeOut('fast', function(){ $(this).fadeIn('fast', function(){ $(this).addClass('input_focus') ; }) ; }) ;
				do_alert( 0, LANG_MERGED["CHAT_JS_INVALID_EMAIL"] ) ;
				return false ;
			}
		}
		if ( typeof( dept_addon_emarketings[deptid] ) != "undefined" )
		{
			var emarket_val_1 = ( $('#emarket_val_1').is(':checked') ) ? 1 : 0 ;
			var emarket_val_0 = ( $('#emarket_val_0').is(':checked') ) ? 1 : 0 ;
			if ( <?php echo $emarketinginfo["id"] ?> && !emarket_val_1 && !emarket_val_0 )
			{
				if ( <?php echo $emarketinginfo["isreq"] ?> && show_divs["vemail"]["required"] )
				{
					$("#request_body").animate({ scrollTop: $(document).height() }, "slow") ;
					$('#emarketid').val(0) ;
					$('#div_checkbox_emarketing_wrapper').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
					return false ;
				}
			}
			$('#emarketid').val( <?php echo $emarketinginfo["id"] ?> ) ;
		}
		else
		{
			$('#emarketid').val(0) ;
		}
		if ( ( typeof( dept_haspolicy[deptid] ) != "undefined" ) && !$('#checkbox_data_policy').is(':checked') )
		{
			$("#request_body").animate({ scrollTop: $(document).height() }, "slow") ;
			$('#checkbox_data_policy_arrow').show() ;
			$('#div_notice_data_policy').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
			return false ;
		}
		return true ;
	}

	function start_chat()
	{
		if ( <?php echo $preview  ?> )
		{
			do_alert( 0, "Chat is not available for interface preview." ) ;
			return false ;
		}
		else if ( !total_depts )
		{
			$('#pre_chat_no_depts').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
			return false ;
		}

		var email_form_passed = check_form(0) ;
		if ( email_form_passed )
		{
			var unique = unixtime( ) ;
			var deptid = $('#deptid').val( ) ;
			var vemail = encodeURIComponent( $('#vemail').val() ) ;
			var custom_field_value_1 = ( typeof( $('#custom_field_input_1').val( ) ) != "undefined" ) ? $('#custom_field_input_1').val( ) : "" ;
			var custom_field_value_2 = ( typeof( $('#custom_field_input_2').val( ) ) != "undefined" ) ? $('#custom_field_input_2').val( ) : "" ;
			var custom_field_value_3 = ( typeof( $('#custom_field_input_3').val( ) ) != "undefined" ) ? $('#custom_field_input_3').val( ) : "" ;
			var custom_extra = ( typeof( dept_customs[deptid] ) != "undefined" ) ? encodeURIComponent( dept_customs[deptid][0] )+"-_-"+encodeURIComponent( custom_field_value_1 )+"-cus-"+encodeURIComponent( dept_customs[deptid][2] )+"-_-"+encodeURIComponent( custom_field_value_2 )+"-cus-"+encodeURIComponent( dept_customs[deptid][4] )+"-_-"+encodeURIComponent( custom_field_value_3 )+"-cus-" : "" ;
			var custom = encodeURIComponent( "<?php echo ( $custom ) ? "{$custom}-cus-" : "" ; ?>" ) + custom_extra ;
			$('#custom').val( custom ) ;

			$('#theform').submit( ) ;
		}
	}

	function send_email()
	{
		if ( <?php echo $preview  ?> )
		{
			do_alert( 0, "Chat is not available for interface preview." ) ;
			return false ;
		}
		var email_form_passed = check_form(1) ;
		if ( email_form_passed )
		{
			send_email_doit() ;
		}
	}

	function send_email_doit()
	{
		var json_data = new Object ;
		var unique = unixtime( ) ;
		var deptid = $('#deptid').val( ) ;
		var vname = $('#vname').val( ) ;
		var vemail = encodeURIComponent( $('#vemail').val() ) ;
		var vsubject = encodeURIComponent( $('#vsubject').val( ) ) ;
		var vquestion = encodeURIComponent( $('#vquestion').val( ) ) ;
		var onpage = encodeURIComponent( "<?php echo $onpage ?>" ).replace( /http/g, "hphp" ) ;
		var custom_field_value_1 = ( typeof( $('#custom_field_input_1').val( ) ) != "undefined" ) ? $('#custom_field_input_1').val( ) : "" ;
		var custom_field_value_2 = ( typeof( $('#custom_field_input_2').val( ) ) != "undefined" ) ? $('#custom_field_input_2').val( ) : "" ;
		var custom_field_value_3 = ( typeof( $('#custom_field_input_3').val( ) ) != "undefined" ) ? $('#custom_field_input_3').val( ) : "" ;
		var custom_extra = ( typeof( dept_customs[deptid] ) != "undefined" ) ? encodeURIComponent( dept_customs[deptid][0] )+"-_-"+encodeURIComponent( custom_field_value_1 )+"-cus-"+encodeURIComponent( dept_customs[deptid][2] )+"-_-"+encodeURIComponent( custom_field_value_2 )+"-cus-"+encodeURIComponent( dept_customs[deptid][4] )+"-_-"+encodeURIComponent( custom_field_value_3 )+"-cus-" : "" ;
		var custom = encodeURIComponent( "<?php echo ( $custom ) ? $custom : "" ; ?>" ) + custom_extra ;
		var emarket_val_1 = ( $('#emarket_val_1').is(':checked') ) ? 1 : 0 ;
		var emarket_val_0 = ( $('#emarket_val_0').is(':checked') ) ? 1 : 0 ;
		var emarket_val = ( ( emarket_val_1 || emarket_val_0 ) && emarket_val_1 ) ? 1 : 0 ;
		if ( !<?php echo $emarketinginfo["isreq"] ?> && !emarket_val_1 && !emarket_val_0 ) { emarket_val = -1 ; }
		var emarketid = $('#emarketid').val() ;

		$('#chat_button_start').attr( "disabled", true ) ;
		$.ajax({
		type: "POST",
		url: "./phplive_m.php",
		data: "action=send_email&deptid="+deptid+"&token="+phplive_browser_token+"&vname="+vname+"&vemail="+vemail+"&custom="+custom+"&vsubject="+vsubject+"&vquestion="+vquestion+"&onpage="+onpage+"&emarketid="+emarketid+"&emarket_val="+emarket_val+"&captcha="+captcha+"&unique="+unique,
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				do_alert( 0, err ) ;
				return false ;
			}

			if ( json_data.status )
			{
				do_alert( 1, LANG_MERGED["CHAT_JS_EMAIL_SENT"] ) ;
				$('#chat_button_start').attr( "disabled", true ) ;
				$('#chat_button_start').html( "<img src=\"./themes/<?php echo $theme ?>/alert_good.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\"> "+LANG_MERGED["CHAT_JS_EMAIL_SENT"] ) ;
			}
			else
			{
				do_alert( 0, json_data.error ) ;
				$('#chat_button_start').attr( "disabled", false ) ;
				$('#chat_button_start').html( LANG_MERGED["CHAT_BTN_EMAIL"] ) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error sending email.  Please refresh the page and try again." ) ;
		} });
	}

	function preview_text( thetextobject )
	{
		if ( typeof( thetextobject["TXT_LIVECHAT"] ) != "undefined" )
			$('#LANG_TXT_LIVECHAT').html( thetextobject["TXT_LIVECHAT"].substring( 0, 20 ) ) ;

		$('#chat_text_header').html( thetextobject["CHAT_WELCOME"] ) ;
		$('#chat_text_header_sub').html( autolink_it( thetextobject["CHAT_WELCOME_SUBTEXT"] ) ) ;
		$('#chat_text_department').html( thetextobject["TXT_DEPARTMENT"] ) ;
		$('#vdeptid option:eq(0)').text( thetextobject["CHAT_SELECT_DEPT"] ) ;
	}

	function check_mobile_view( theinput, theflag )
	{
		if ( $('#'+theinput).hasClass('input_focus') )
			$('#'+theinput).removeClass('input_focus') ;
	}

	function toggle_policy( thedeptid, theforce )
	{
		var expand_speed = 200 ;
		if ( $('#div_policy_wrapper').is(':visible') || theforce )
		{
			$('#span_policy_loading').hide() ;
			$('#div_policy_wrapper').hide() ;
			$('#request_body_wrapper').show() ;
			init_divs_pre() ; // reset inputs for some devices
		}
		else
		{
			var request_div_height = $('#request_body_wrapper').height() - 125 ;
			var policy_wrapper_height = request_div_height + 50 ;
			$('#div_policy').css({'height': 0}) ;
			setTimeout( function(){ $('#span_policy_loading').fadeIn("fast") ; }, 800 ) ;

			$('#div_policy').load( "./ajax/actions.php?action=fetch_policy&deptid="+thedeptid, function() {
				$('#div_policy').append("<div style='padding: 25px;'>&nbsp;</div>") ;
				$('#request_body_wrapper').hide() ;
				$('#div_policy_wrapper').css({'height': policy_wrapper_height}).show() ;
				$('#div_policy').css({height: request_div_height}).scrollTop(0) ;
			});
		}
	}

	function scroll_to_form()
	{
		$('#request_body').animate({
			scrollTop: global_div_online_pics_scrolltop
		}, 'slow') ;
	}

	var captcha ;
	var capture_captcha = function(response) {
			captcha = response ;
			$('#captcha').val( captcha ) ;
	};
//-->
</script>

</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>
