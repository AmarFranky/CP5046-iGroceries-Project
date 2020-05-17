<?php
	if ( defined( 'API_Util_Security' ) ) { return ; }	
	define( 'API_Util_Security', true ) ;

	FUNCTION Util_Security_AuthSetup( &$dbh,
					$adminid = 0 )
	{
		global $now ;
		global $VARS_SETUP_IDLE_LOGOUT ;
		global $VARS_SETUP_DISABLE_IDLE ;
		$adminid = isset( $_COOKIE["phpliveadminID"] ) ? Util_Format_Sanatize( $_COOKIE["phpliveadminID"], "n" ) : 0 ;
		$ses = isset( $_COOKIE["phpliveadminSES"] ) ? Util_Format_Sanatize( $_COOKIE["phpliveadminSES"], "ln" ) : "" ;
		LIST( $adminid, $ses ) = database_mysql_quote( $dbh, $adminid, $ses ) ;

		if ( $adminid && $ses )
		{
			$query = "SELECT * FROM p_admins WHERE adminID = '$adminid' AND ses = '$ses' LIMIT 1" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				$admininfo = database_mysql_fetchrow( $dbh ) ;
				if ( isset( $admininfo["adminID"] ) )
				{
					if ( ( !isset( $VARS_SETUP_DISABLE_IDLE ) || ( isset( $VARS_SETUP_DISABLE_IDLE ) && !$VARS_SETUP_DISABLE_IDLE ) ) && ( $admininfo["lastactive"] < ( $now - ( $VARS_SETUP_IDLE_LOGOUT * 60 ) ) ) )
					{
						return false ;
					}
					else
					{
						$query = "UPDATE p_admins SET lastactive = $now WHERE adminID = '$adminid'" ;
						database_mysql_query( $dbh, $query ) ;
						return $admininfo ;
					}
				}
			}
		}
		return false ;
	}

	FUNCTION Util_Security_AuthOp( &$dbh,
					$opid = 0,
					$wp = 0 )
	{
		global $CONF ;
		if ( !$opid && !$wp )
			$opid = isset( $_COOKIE["cO"] ) ? Util_Format_Sanatize( $_COOKIE["cO"], "n" ) : 0 ;
		$ses = isset( $_COOKIE["cS"] ) ? Util_Format_Sanatize( $_COOKIE["cS"], "ln" ) : "" ;
		LIST( $opid, $ses ) = database_mysql_quote( $dbh, $opid, $ses ) ;

		if ( $opid && $ses && is_file( "$CONF[TYPE_IO_DIR]/$opid"."_ses_$ses.ses" ) )
		{
			$query = "SELECT * FROM p_operators WHERE opID = '$opid' AND ses = '$ses' LIMIT 1" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				$data = database_mysql_fetchrow( $dbh ) ;
				if ( isset( $data["opID"] ) )
					return $data ;
			}
		}
		return false ;
	}
?>