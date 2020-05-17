<?php
	if ( defined( 'API_Util_Format' ) ) { return ; }	
	define( 'API_Util_Format', true ) ;

	FUNCTION Util_Format_Sanatize( $string, $flag )
	{
		if ( !is_array( $string ) ) { $string = trim( $string, "\x00" ) ; }
		switch ( $flag )
		{
			case ( "a" ):
				return ( is_array( $string ) ) ? $string : Array() ; break ;
			case ( "n" ):
				$varout = preg_replace( "/[^0-9.-]/i", "", $string ) ; if ( !$varout ) { $varout = 0 ; }
				return $varout ;
				break ;
			case ( "ln" ):
				$temp = preg_replace( "/[`\$*%=<>\(\)\[\]\|\{\}\/\\\]/i", "", $string ) ;
				$varout = ( $temp == "0" ) ? "" : $temp ; return $varout ; break ;
			case ( "lns" ):
				return preg_replace( "/[^a-z0-9.:\-]/i", "", $string ) ; break ;
			case ( "ip" ):
				return preg_replace( "/[^a-z0-9.:\-*]/i", "", $string ) ; break ;
			case ( "b64" ):
				return preg_replace( "/[^a-z0-9.+\/=\-_]/i", "", $string ) ; break ;
			case ( "eln" ):
				return preg_replace( "/[^a-z0-9+_.\-@]/i", "", Util_Format_Trim( $string ) ) ; break ;
			case ( "e" ):
				return strip_tags( Util_Format_Trim( $string ) ) ; break ;
			case ( "v" ):
				return preg_replace( "/(%20)|(%00)|(%3Cv%3E)|(<v>)/", "", Util_Format_Trim( $string ) ) ; break ;
			case ( "base_url" ):
				return preg_replace( "/[\$\!`\"<>'\?;]/i", "", Util_Format_Trim( preg_replace( "/hphp/i", "http", $string ) ) ) ; break ;
			case ( "url" ):
				return preg_replace( "/[\$\!`\"<>'\(\); ]/i", "", Util_Format_Trim( preg_replace( "/hphp/i", "http", $string ) ) ) ; break ;
			case ( "title" ):
				return preg_replace( "/[`\$=\!<>;]/i", "", Util_Format_Trim( preg_replace( "/hphp/i", "http", $string ) ) ) ; break ;
			case ( "htmltags" ):
				return Util_Format_ConvertTags( $string ) ; break ;
			case ( "timezone" ):
				return preg_replace( "/['`?\$*%=<>\(\)\[\]\|\{\}\\\]/i", "", $string ) ; break ;
			case ( "notags" ):
				return strip_tags( $string ) ; break ;
			case ( "noscripts" ):
				return preg_replace( "/<script(.*)\/script>/i", "", $string ) ; break ;
			case ( "query" ):
				return $string ; break ;
			default:
				return $string ;
		}
	}

	FUNCTION Util_Format_URL( $string )
	{
		return preg_replace( "/http/i", "hphp", $string ) ;
	}

	FUNCTION Util_Format_Trim( $string )
	{
		return preg_replace( "/(\r\n)|(\r)|(\n)/", "", $string ) ;
	}

	FUNCTION Util_Format_ConvertTags( $string )
	{
		$string = preg_replace( "/>/", "&gt;", $string ) ;
		return preg_replace( "/</", "&lt;", $string ) ;
	}

	FUNCTION Util_Format_ConvertQuotes( $string )
	{
		$string = preg_replace( "/'/", "&apos;", $string ) ;
		return preg_replace( "/(\")|(%22)/", "&quot;", $string ) ;
	}

	FUNCTION Util_Format_StripQuotes( $string )
	{
		return preg_replace( "/[\"']/", "", $string ) ;
	}

	FUNCTION Util_Format_Duration( $duration, $min_sec = 0 )
	{
		$string = "" ;
		$seconds = floor( $duration ) ;

		$minutes = floor( $seconds/60 ) ;
		$hours = floor( $minutes/60 ) ;
		if ( $hours )
		{
			$minutes = floor( ( $duration - (60*60*$hours) )/60 ) ;
			$string = "$hours hr $minutes min" ;
		}
		else if ( $minutes )
		{
			$seconds = floor( $duration - ($minutes*60) ) ;
			if ( $min_sec && $seconds )
				$string = "$minutes min $seconds sec" ;
			else
				$string = "$minutes min" ; // simplified for narrow rows
		}
		else if ( $seconds ) { $string = "$seconds sec" ; }
		return $string ;
	}

	FUNCTION Util_Format_GetVar( $varname, $method = "" )
	{
		$varout = 0 ;
		if ( isset( $_POST[$varname] ) )
			$varout = $_POST[$varname] ;
		else if ( isset( $_GET[$varname] ) )
			$varout = $_GET[$varname] ;
		if ( function_exists( "get_magic_quotes_gpc" ) && get_magic_quotes_gpc() && !is_array( $varout ) )
			$varout = stripslashes( $varout ) ;
		return $varout ;
	}

	FUNCTION Util_Format_GetOS( $agent, $ckpad = false )
	{
		global $CONF ;
		if ( !defined( 'API_Util_Mobile' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Mobile_Detect.php" ) ;

		$mobile_detect = new Mobile_Detect ;
		// tablets are considered mobile devices
		if ( $mobile_detect->isMobile() )
		{
			$os = 5 ;
			if ( $ckpad && $mobile_detect->isTablet() )
			{
				if ( $mobile_detect->isiPad() )
					$os = 3 ;
			}
		}
		else if ( preg_match( "/Windows/i", $agent ) ) { $os = 1 ; }
		else if ( preg_match( "/Mac/i", $agent ) ) { $os = 2 ; }
		else { $os = 4 ; }

		if ( preg_match( "/MSIE/i", $agent ) ) { $browser = 1 ; }
		else if ( preg_match( "/(Edge)|(Edg)/i", $agent ) ) { $browser = 1 ; }
		else if ( preg_match( "/Firefox/i", $agent ) ) { $browser = 2 ; }
		else if ( preg_match( "/Chrome/i", $agent ) ) { $browser = 3 ; }
		else if ( preg_match( "/Safari/i", $agent ) ) { $browser = 4 ; }
		else if ( preg_match( "/Trident/i", $agent ) ) { $browser = 1 ; }
		else { $browser = 6 ; } return Array( $os, $browser ) ;
	}

	FUNCTION Util_Format_RandomString( $length = 5, $chars = '23456789abcdeghjkmnpqrstuvwxyz')
	{
		$charLength = strlen($chars)-1 ;

		$random_string = "" ;
		for ( $c = 0 ; $c < $length ; $c++ )
			$random_string .= $chars[mt_rand(0,$charLength)] ;
		return $random_string ;
	}

	FUNCTION Util_Format_DEBUG( $string, $thefile = "" )
	{
		global $CONF ;
		$script_name = isset( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['SCRIPT_NAME'] : "" ;
		if ( $thefile ) { $thefile = Util_Format_Sanatize( $thefile, "ln" ) ; $log_file = "$CONF[CONF_ROOT]/$thefile" ; }
		else { $log_file = "$CONF[CONF_ROOT]/debug.txt" ; }
		if ( is_writeable( $CONF["CONF_ROOT"] ) ) { file_put_contents( $log_file, $script_name." -> ".$string."\n", FILE_APPEND ) ; }
	}

	FUNCTION Util_Format_DEBUG_DBQUERIES( &$dbh )
	{
		$query_string = "" ;
		if ( isset( $dbh['query_his'] ) ) { for ( $c = 0; $c < count( $dbh['query_his'] ); ++$c ) { $query_string .= $dbh['query_his'][$c]."\r\n" ; } }
		return $query_string ;
	}

	FUNCTION Util_Format_Get_Vars( &$dbh )
	{
		$query = "SELECT * FROM p_vars LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ; return $data ;
		} return false ;
	}

	FUNCTION Util_Format_TableFirstCreated( &$dbh,
								$table )
	{
		if ( $table == "" )
			return time() ;

		LIST( $table ) = database_mysql_quote( $dbh, $table ) ;

		$query = "SELECT created FROM $table ORDER BY created ASC LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			if ( isset( $data["created"] ) )
				return $data["created"] ;
		}
		return time() ;
	}

	FUNCTION Util_Format_Update_TimeStamp( &$dbh, $ts_table, $now )
	{
		if ( !preg_match( "/^(clean)|(clear)|(queue)$/", $ts_table ) ) { return false ; }
		LIST( $now ) = database_mysql_quote( $dbh, $now ) ;

		$query = "UPDATE p_vars SET ts_$ts_table = $now" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Util_Format_ExplodeString( $delim, $string )
	{
		$output = explode( $delim, $string ) ;
		for ( $c = 0; $c < count( $output ); ++$c )
		{
			if ( !$output[$c] ) { unset( $output[$c] ) ; }
		} return $output ;
	}

	FUNCTION Util_Format_SetCookie( $name, $value, $expire, $path, $domain, $secure, $samesite = "Lax" )
	{
		// there are cookie issues when setting on non-secure then secure causing cookie confusion and
		// possible loop issues.  set to non-secure for reliable cookie reading and to limit issues
		setcookie( $name, $value, $expire, $path, $domain, false ) ;

		// iPad Mobile App seems to have issues with new method.  it defaults to
		// Lax in browsers so it is not critical
		/*
		$header = 'Set-Cookie:' ;
		$header .= rawurlencode($name) . '=' . rawurlencode($value) . ';' ;
		if ( $expire != -1 )
			$header .= 'expires=' . gmdate('D, d-M-Y H:i:s T', $expire) . ';' ;
		$header .= 'path=' . $path . ';' ;
		if ( $domain )
			$header .= 'domain=' . rawurlencode($domain) . ';' ;
		if ( $secure )
			$header .= 'secure;' ;
		$header .= 'httponly;' ;
		$header .= 'SameSite=' . $samesite . ';' ;
		HEADER( $header, false ) ;
		*/
	}

	FUNCTION Util_Format_CleanDeptOnline( $deptid, $opid )
	{
		global $CONF ;
		if ( is_dir( $CONF["CHAT_IO_DIR"] ) )
		{
			if ( is_numeric( $deptid ) && is_numeric( $opid ) )
			{
				foreach ( glob( "$CONF[CHAT_IO_DIR]/online_".$deptid."_*", GLOB_NOSORT ) as $file ) {
					if( preg_match( "/online_{$deptid}_{$opid}.info/i", $file ) && is_file( $file ) )
						@unlink( $file ) ;
				}
			}
			if ( is_numeric( $deptid ) )
			{
				foreach ( glob( "$CONF[CHAT_IO_DIR]/online_".$deptid."_*", GLOB_NOSORT ) as $file ) {
					if ( is_file( $file ) ) { @unlink( $file ) ; }
				}
			}
			else if ( is_numeric( $opid ) )
			{
				foreach ( glob( "$CONF[CHAT_IO_DIR]/online_*", GLOB_NOSORT ) as $file ) {
					if( preg_match( "/online_(.*?)_{$opid}.info/i", $file ) && is_file( $file ) )
						@unlink( $file ) ;
				}
			}
			else
			{
				foreach ( glob( "$CONF[CHAT_IO_DIR]/online*", GLOB_NOSORT ) as $file ) {
					if ( is_file( $file ) ) { @unlink( $file ) ; }
				}
			}
		}
	}

	FUNCTION Util_Format_CleanIcons( $deptid )
	{
		global $CONF ;
		if ( is_dir( $CONF["CONF_ROOT"] ) && is_numeric( $deptid ) )
		{
			foreach ( glob( "$CONF[CONF_ROOT]/icon_online_".$deptid.".*", GLOB_NOSORT ) as $file ) {
				if( is_file( $file ) ) { @unlink( $file ) ; }
			}
			foreach ( glob( "$CONF[CONF_ROOT]/icon_offline_".$deptid.".*", GLOB_NOSORT ) as $file ) {
				if( is_file( $file ) ) { @unlink( $file ) ; }
			}
			foreach ( glob( "$CONF[CONF_ROOT]/logo_".$deptid.".*", GLOB_NOSORT ) as $file ) {
				if( is_file( $file ) ) { @unlink( $file ) ; }
			}
		}
	}

	FUNCTION Util_Format_IsIPExcluded( $ip, $theforce )
	{
		global $VALS ;
		if ( $ip && isset( $VALS['TRAFFIC_EXCLUDE_IPS'] ) )
		{
			$ips = explode( "-", Util_Format_Sanatize( $VALS['TRAFFIC_EXCLUDE_IPS'], "ip" ) ) ;

			for ( $c = 0; $c < count( $ips ); ++$c )
			{
				if ( $ips[$c] )
				{
					if ( preg_match( '/^\*/', $ips[$c] ) && preg_match( '/\*$/', $ips[$c] ) && !$theforce )
					{
						$temp_ip = preg_replace( '/\*/', "", $ips[$c] ) ;
						$pattern = '/'.quotemeta( $temp_ip ).'/i' ;
						if ( preg_match( $pattern, $ip ) )
							return true ;
					}
					else if ( preg_match( '/^\*/', $ips[$c] ) && !preg_match( '/\*$/', $ips[$c] ) && !$theforce )
					{
						$temp_ip = preg_replace( '/\*/', "", $ips[$c] ) ;
						$pattern = '/'.quotemeta( $temp_ip ).'$/i' ;
						if ( preg_match( $pattern, $ip ) )
							return true ;
					}
					else if ( !preg_match( '/^\*/', $ips[$c] ) && preg_match( '/\*$/', $ips[$c] ) && !$theforce )
					{
						$temp_ip = preg_replace( '/\*/', "", $ips[$c] ) ;
						$pattern = '/^'.quotemeta( $temp_ip ).'/i' ;
						if ( preg_match( $pattern, $ip ) )
							return true ;
					}
					else
					{
						if ( $ips[$c] == $ip )
							return true ;
					}
				}
			}
		}
		return false ;
	}

	FUNCTION Util_Format_GetBrowserLang()
	{
		$langs = Array(
			"ar"=>"arabic",
			"pt"=>"brazilian",
			"bg"=>"bulgarian",
			"zh"=>"chinese",
			"hr"=>"croatian",
			"da"=>"danish",
			"nl"=>"dutch",
			"en"=>"english",
			"fr"=>"french",
			"de"=>"german",
			"el"=>"greek",
			"it"=>"italian",
			"ja"=>"japanese",
			"ko"=>"korean",
			"no"=>"norwegian",
			"nb"=>"norwegian",
			"nn"=>"norwegian",
			"fa"=>"persian",
			"pl"=>"polish",
			"ru"=>"russian",
			"sr"=>"serbian",
			"es"=>"spanish",
			"sv"=>"swedish",
			"tr"=>"turkish"
		) ;
		$lang = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? strtolower( substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 ) ) : "" ;
		if ( $lang && isset( $langs[$lang] ) ) { return $langs[$lang] ; } return "" ;
	}
?>