<?php
	$CONF = Array() ;
$CONF['DOCUMENT_ROOT'] = addslashes( '/home/techbizinnovator/domains/techbizinnovators.com.au/public_html/demo/phplive' ) ;
$CONF['BASE_URL'] = '//techbizinnovators.com.au/demo/phplive' ;
$CONF['SQLTYPE'] = 'SQLi.php' ;
$CONF['SQLHOST'] = 'localhost' ;
$CONF['SQLPORT'] = '0' ;
$CONF['SQLLOGIN'] = 'techbizinnovator_db' ;
$CONF['SQLPASS'] = 'Techbizinnovator_db1' ;
$CONF['DATABASE'] = 'techbizinnovator_db' ;
$CONF['THEME'] = 'default' ;
$CONF['TIMEZONE'] = 'Indian/Antananarivo' ;
$CONF['icon_online'] = '' ;
$CONF['icon_offline'] = '' ;
$CONF['lang'] = 'english' ;
$CONF['logo'] = 'logo_0.PNG' ;
$CONF['CONF_ROOT'] = '/home/techbizinnovator/domains/techbizinnovators.com.au/public_html/demo/phplive/web' ;
$CONF['UPLOAD_HTTP'] = '//techbizinnovators.com.au/demo/phplive/web' ;
$CONF['UPLOAD_DIR'] = '/home/techbizinnovator/domains/techbizinnovators.com.au/public_html/demo/phplive/web' ;
$CONF['ATTACH_DIR'] = '/home/techbizinnovator/domains/techbizinnovators.com.au/public_html/demo/phplive/web/file_attach' ;
$CONF['TEMP_DIR'] = '/home/techbizinnovator/domains/techbizinnovators.com.au/public_html/demo/phplive/web/file_temp' ;
$CONF['EXPORT_DIR'] = '/home/techbizinnovator/domains/techbizinnovators.com.au/public_html/demo/phplive/web/exported_files' ;
$CONF['geo'] = '' ;
$CONF['SALT'] = '398mxk73m5' ;
$CONF['API_KEY'] = '8cdva42a27' ;
$CONF['screen'] = 'same' ;
	if ( phpversion() >= '5.1.0' ){ date_default_timezone_set( $CONF['TIMEZONE'] ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vars.php" ) ;
?>