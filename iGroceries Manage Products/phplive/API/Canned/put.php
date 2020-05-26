<?php
	if ( defined( 'API_Canned_put' ) ) { return ; }
	define( 'API_Canned_put', true ) ;

	FUNCTION Canned_put_Canned( &$dbh,
					$canid,
					$opid,
					$deptid,
					$catid,
					$cats_extra,
					$title,
					$message )
	{
		if ( ( $opid == "" ) || ( $deptid == "" )  || ( $title == "" )
			|| ( $message == "" ) || !is_numeric( $catid ) )
			return false ;

		LIST( $canid, $opid, $deptid, $catid, $cats_extra, $title, $message ) = database_mysql_quote( $dbh, $canid, $opid, $deptid, $catid, $cats_extra, $title, $message ) ;

		if ( $canid )
			$query = "UPDATE p_canned SET deptID = $deptid, catID = '$catid', cats_extra = '$cats_extra', title = '$title', message = '$message' WHERE canID = $canid AND opID = $opid" ;
		else
			$query = "INSERT INTO p_canned VALUES( $canid, $opid, $deptid, '$catid', '$cats_extra', '$title', '$message' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			if ( !$canid )
				$id = database_mysql_insertid( $dbh ) ;
			else
				$id = $canid ;
			return $id ;
		}

		return false ;
	}

?>