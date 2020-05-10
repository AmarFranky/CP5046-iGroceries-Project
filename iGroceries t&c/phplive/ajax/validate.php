<?php
	/***************************************/
	//
	//
	// PHP Live! Support
	//
	// https://www.phplivesupport.com
	//
	/***************************************/
	/*
	/* DO NOT MODIFY THIS FILE
	/*
	*/
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	$vemail = Util_Format_Sanatize( Util_Format_GetVar( "vemail" ), "e" ) ;
	$custom_vars = Util_Format_Sanatize( Util_Format_GetVar( "custom" ), "htmltags" ) ;
	$custom_vars_hash = Array() ;
	$customs = explode( "-cus-", trim( $custom_vars ) ) ;
	for ( $c = 0; $c < count( $customs ); ++$c )
	{
		$custom_var = $customs[$c] ;
		if ( $custom_var && preg_match( "/-_-/", $custom_var ) )
		{
			LIST( $cus_name, $cus_val ) = explode( "-_-", preg_replace( "/%20/", " ", $custom_var ) ) ; $cus_name_orig = $cus_name ;
			if ( $cus_val && !isset( $custom_vars_hash[$cus_name] ) )
				$custom_vars_hash[$cus_name] = $cus_val ;
		}
	}
	if ( is_file( "$CONF[CONF_ROOT]/autotask_config.php" ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/addons/autotask/API/Util_Extra_Validate.php" ) ;
		if ( !isset( $json_data ) ) { $json_data = "json_data = { \"status\": 0, \"error\": \"Validation must set the \$json_data variable.\" };" ; }
	}
	else { $json_data = "json_data = { \"status\": 1 };" ; }

	if ( isset( $dbh ) && isset( $dbh['con'] ) ) { database_mysql_close( $dbh ) ; }
	HEADER('Content-Type: text/plain; charset=utf-8') ;
	print $json_data ; exit ;
?>