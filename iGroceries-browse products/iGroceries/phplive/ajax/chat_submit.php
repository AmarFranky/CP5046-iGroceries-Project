<?php
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$isop = Util_Format_Sanatize( Util_Format_GetVar( "isop" ), "n" ) ;
	$isop_ = Util_Format_Sanatize( Util_Format_GetVar( "isop_" ), "n" ) ;
	$isop__ = Util_Format_Sanatize( Util_Format_GetVar( "isop__" ), "n" ) ;
	$opc = Util_Format_Sanatize( Util_Format_GetVar( "opc" ), "n" ) ;
	$op2op = Util_Format_Sanatize( Util_Format_GetVar( "op2op" ), "n" ) ;
	$requestid = Util_Format_Sanatize( Util_Format_GetVar( "requestid" ), "n" ) ;
	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "lns" ) ;
	$salt = Util_Format_Sanatize( Util_Format_GetVar( "salt" ), "ln" ) ;
	$mapp = Util_Format_Sanatize( Util_Format_GetVar( "mp" ), "n" ) ;
	$text = preg_replace( "/( p_br )/", "<br>", Util_Format_Sanatize( base64_decode( Util_Format_GetVar( "text" ) ), "noscripts" ) ) ;
	$t_vses = Util_Format_Sanatize( Util_Format_GetVar( "t_vses" ), "n" ) ;

	$cookie_opid = isset( $_COOKIE["cO"] ) ? $_COOKIE["cO"] : "" ;
	$cookie_ses = isset( $_COOKIE["cS"] ) ? $_COOKIE["cS"] : "" ;

	if ( ( ( md5( md5( $CONF["SALT"] ).$ces ) == $salt ) || ( md5( md5( $CONF["SALT"] ).$cookie_opid.$cookie_ses ) == $salt ) ) && is_file( "$CONF[CHAT_IO_DIR]/$ces.txt" ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;

		// override javascript timestamp
		$text = preg_replace( "/<timestamp_(\d+)_((co)|(cv))>/", "<timestamp_".$now."_$2>", $text ) ;
		$text = preg_replace( "/▒~@▒/", "", $text ) ;

		if ( ( $isop && $isop_ ) && ( $isop == $isop_ ) ) { $wid = $isop_ ; }
		else if ( $isop && $isop_ ) { $wid = $isop__ ; }
		else { $wid = $isop_ ; }

		UtilChat_AppendToChatfile( "$ces.txt", base64_encode( $text ) ) ;
		usleep( 100000 ) ; // 10th of second
		if ( file_exists( "$CONF[DOCUMENT_ROOT]/ajax/inc_submit_extra.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/ajax/inc_submit_extra.php" ) ; }

		if ( $isop )
		{
			if ( $op2op )
			{
				$filename = $ces."-".$wid ;
				UtilChat_AppendToChatfile( "$filename.text", base64_encode( $text ) ) ;

				$wid = ( $isop == $isop__ ) ? $isop_ : $isop__ ;
				if ( $wid && is_file( "$CONF[TYPE_IO_DIR]/$wid.mapp" ) )
				{
					$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
					if ( isset( $mapp_array[$wid] ) ) { $arn = $mapp_array[$wid]["a"] ; $platform = $mapp_array[$wid]["p"] ; }
					if ( isset( $arn ) && $arn )
					{
						include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;
						$text_plain = strip_tags( $text ) ;
						Util_MAPP_Publish( $wid, "new_text", $platform, $arn, $text_plain ) ;
					}
				}
			}
			else
			{
				$max_vses = ( $t_vses > $VARS_MAX_EMBED_SESSIONS ) ? $VARS_MAX_EMBED_SESSIONS : $t_vses ;
				for ( $c = 1; $c <= $max_vses; ++$c )
				{
					$filename = $ces."-".$wid."_".$c ;
					UtilChat_AppendToChatfile( "$filename.text", base64_encode( $text ) ) ;
				}
			}
			// copy to operator's chat for situations they need to see new message, such as
			// iOS external file upload method needs to write to both visitor and operator
			if ( $opc )
			{
				$filename = $ces."-$isop" ;
				UtilChat_AppendToChatfile( "$filename.text", base64_encode( $text ) ) ;
			}
		}
		else
		{
			$filename = $ces."-".$wid ;
			UtilChat_AppendToChatfile( "$filename.text", base64_encode( $text ) ) ;
			$max_vses = $VARS_MAX_EMBED_SESSIONS ;
			for ( $c = 1; $c <= $max_vses; ++$c )
			{
				if ( $c != $t_vses )
				{
					$filename = $ces."-0_".$c ;
					UtilChat_AppendToChatfile( "$filename.text", base64_encode( $text ) ) ;
				}
			}

			if ( $wid && ( $wid == $mapp ) && !is_file( "$CONF[TYPE_IO_DIR]/$mapp.mapp" ) )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
				$op_inactive = $now - 60 ;

				// check for mapp file just in case it does not exist or was deleted
				$opinfo = Ops_get_OpInfoByID( $dbh, $mapp ) ;
				if ( $opinfo["lastactive"] < $op_inactive )
					touch( "$CONF[TYPE_IO_DIR]/$mapp.mapp" ) ;
			}

			if ( $wid && is_file( "$CONF[TYPE_IO_DIR]/$wid.mapp" ) )
			{
				$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
				if ( isset( $mapp_array[$wid] ) ) { $arn = $mapp_array[$wid]["a"] ; $platform = $mapp_array[$wid]["p"] ; }
				if ( isset( $arn ) && $arn )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;
					$text_plain = strip_tags( $text ) ;
					Util_MAPP_Publish( $wid, "new_text", $platform, $arn, $text_plain ) ;
				}
			}
		}
		UtilChat_WriteIsWriting( $ces, 0, $isop, $isop_, $isop__ ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else
		$json_data = "json_data = { \"status\": -1 };" ;
	
	if ( isset( $dbh ) && isset( $dbh['con'] ) ) { database_mysql_close( $dbh ) ; }
	$json_data = Util_Format_Trim( $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	HEADER('Content-Type: text/plain; charset=utf-8') ;
	print $json_data ; exit ;
?>