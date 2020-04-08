<?php
	include_once( "./web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	$url = base64_decode( Util_Format_Sanatize( Util_Format_GetVar( "url" ), "b64" ) ) ;
?>
<?php include_once( "./inc_doctype.php" ) ?>
<head>
<title> blank page </title>
<meta name="author" content="osicodesinc">
<meta name="mapp" content="active">
<meta name="description" content="phplive_c615">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<?php include_once( "./inc_meta_dev.php" ) ; ?>
<script data-cfasync="false" type="text/javascript" src="./js/jquery_md5.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript">
<!--
	var loaded = 1 ;

	<?php if ( $url && preg_match( "/api_key/", $url ) ): ?>
	var phplive_browser = navigator.appVersion ; var phplive_mime_types = "" ;
	var phplive_display_width = screen.availWidth ; var phplive_display_height = screen.availHeight ; var phplive_display_color = screen.colorDepth ; var phplive_timezone = new Date().getTimezoneOffset() ;
	if ( navigator.mimeTypes.length > 0 ) { for (var x=0; x < navigator.mimeTypes.length; x++) { phplive_mime_types += navigator.mimeTypes[x].description ; } }
	var phplive_browser_token = phplive_md5( phplive_display_width+phplive_display_height+phplive_display_color+phplive_timezone+phplive_browser+phplive_mime_types ) ;

	var win_width = screen.width ;
	var win_height = screen.height ;
	var win_dim = encodeURIComponent( win_width + " x " + win_height ) ;
	location.href = "<?php echo $url ?>&token="+phplive_browser_token+"&win_dim="+win_dim+"&<?php echo $now ?>" ;
	<?php endif ; ?>

//-->
</script>
</head>
<body style="background: transparent;"><!-- blank page for loading in various areas for iframe prep and clean --></body>
</html>