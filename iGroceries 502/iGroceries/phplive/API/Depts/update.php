<?php
	if ( defined( 'API_Depts_update' ) ) { return ; }
	define( 'API_Depts_update', true ) ;

	FUNCTION Depts_update_DeptValue( &$dbh,
					  $deptid,
					  $tbl_name,
					  $value )
	{
		if ( ( $deptid == "" ) || ( $tbl_name == "" ) )
			return false ;
		
		LIST( $deptid, $tbl_name, $value ) = database_mysql_quote( $dbh, $deptid, $tbl_name, $value ) ;

		$query = "UPDATE p_departments SET $tbl_name = '$value' WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Depts_update_DeptLangs( &$dbh,
						$prev_lang,
						$lang )
	{
		if ( ( $prev_lang == "" ) || ( $lang == "" ) )
			return false ;
		
		LIST( $prev_lang, $lang ) = database_mysql_quote( $dbh, $prev_lang, $lang ) ;

		$query = "UPDATE p_departments SET lang = '$lang' WHERE lang = '$prev_lang'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Depts_update_DeptValues( &$dbh,
					  $deptid,
					  $tbl_name,
					  $value,
					  $tbl_name2,
					  $value2 )
	{
		if ( ( $deptid == "" ) || ( $tbl_name == "" ) || ( $tbl_name2 == "" ) )
			return false ;
		
		LIST( $deptid, $tbl_name, $value, $tbl_name2, $value2 ) = database_mysql_quote( $dbh, $deptid, $tbl_name, $value, $tbl_name2, $value2 ) ;

		$query = "UPDATE p_departments SET $tbl_name = '$value', $tbl_name2 = '$value2' WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Depts_update_DeptVarsValue( &$dbh,
					  $deptid,
					  $tbl_name,
					  $value )
	{
		if ( ( $deptid == "" ) || ( $tbl_name == "" ) )
			return false ;
		
		LIST( $deptid, $tbl_name, $value ) = database_mysql_quote( $dbh, $deptid, $tbl_name, $value ) ;

		$query = "UPDATE p_dept_vars SET $tbl_name = '$value' WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$nresults = database_mysql_nresults( $dbh ) ;
			if ( !$nresults )
			{
				Depts_update_InsertDeptVars( $dbh, $deptid ) ;
				$query = "UPDATE p_dept_vars SET $tbl_name = '$value' WHERE deptID = $deptid" ;
				database_mysql_query( $dbh, $query ) ;
			}
			return true ;
		}
		return false ;
	}

	FUNCTION Depts_update_DeptVarsValues( &$dbh,
					  $deptid,
					  $tbl_name,
					  $value,
					  $tbl_name2,
					  $value2 )
	{
		if ( ( $deptid == "" ) || ( $tbl_name == "" ) || ( $tbl_name2 == "" ) )
			return false ;
		
		LIST( $deptid, $tbl_name, $value, $tbl_name2, $value2 ) = database_mysql_quote( $dbh, $deptid, $tbl_name, $value, $tbl_name2, $value2 ) ;

		$query = "UPDATE p_dept_vars SET $tbl_name = '$value', $tbl_name2 = '$value2' WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$nresults = database_mysql_nresults( $dbh ) ;
			if ( !$nresults )
			{
				Depts_update_InsertDeptVars( $dbh, $deptid ) ;
				$query = "UPDATE p_dept_vars SET $tbl_name = '$value', $tbl_name2 = '$value2' WHERE deptID = $deptid" ;
				database_mysql_query( $dbh, $query ) ;
			}
			return true ;
		}
		return false ;
	}

	FUNCTION Depts_update_InsertDeptVars( &$dbh, $deptid )
	{
		$query = "INSERT IGNORE INTO p_dept_vars VALUES( $deptid, 0, 0, 0, 1, 1, 0, 0, 5, 1, 0, '', '', '', '', '', '' )" ;
		database_mysql_query( $dbh, $query ) ;
	}
?>