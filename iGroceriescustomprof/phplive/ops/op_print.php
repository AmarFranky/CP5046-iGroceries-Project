<?php
	/***************************************/
	//
	//
	// PHP Live! Support
	//
	// https://www.phplivesupport.com
	//
	/***************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;

	$opid = isset( $_COOKIE["cO"] ) ? Util_Format_Sanatize( $_COOKIE["cO"], "n" ) : "" ;
	$ses = isset( $_COOKIE["cS"] ) ? Util_Format_Sanatize( $_COOKIE["cS"], "ln" ) : "" ;
	$admininfo = Array() ;
	if ( !$opid ) { $admininfo = Util_Security_AuthSetup( $dbh ) ; }

	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$download = Util_Format_Sanatize( Util_Format_GetVar( "download" ), "n" ) ;

	LIST( $ip, $vis_token ) = Util_IP_GetIP("") ;
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;
	$theme = $CONF["THEME"] ;

	$requestinfo = Chat_get_RequestHistCesInfo( $dbh, $ces ) ;

	$department = Depts_get_DeptInfo( $dbh, $deptid ) ;
	if ( isset( $department["lang"] ) && $department["lang"] )
		$CONF["lang"] = $department["lang"] ;
	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ;

	$deptvars = Depts_get_DeptVars( $dbh, $deptid ) ;

	$dept_emo = ( isset( $VALS["EMOS"] ) ) ? unserialize( $VALS["EMOS"] ) : Array() ;
	$addon_emo = 0 ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) )
	{
		if ( !isset( $dept_emo[$deptid] ) || ( isset( $dept_emo[$deptid] ) && $dept_emo[$deptid] ) ) { $addon_emo = 1 ; }
		else if ( isset( $dept_emo[$deptid] ) && !$dept_emo[$deptid] ) { $addon_emo = 0 ; }
		else if ( !isset( $dept_emo[0] ) || ( isset( $dept_emo[0] ) && $dept_emo[0] ) ) { $addon_emo = 1 ; }
	}

	if ( isset( $requestinfo["md5_vis"] ) && ( ( $opid && is_file( "$CONF[TYPE_IO_DIR]/$opid"."_ses_$ses.ses" ) ) || ( $vis_token == $requestinfo["md5_vis"] ) || isset( $admininfo["adminID"] ) ) )
	{
		$operator = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;

		$os = "" ; $browser = "" ;
		if ( isset( $requestinfo["ces"] ) )
		{
			$os = "(".$VARS_OS[$requestinfo["os"]].")" ;
			$browser = "(".$VARS_BROWSER[$requestinfo["browser"]].")" ;
			$duration = $requestinfo["ended"] - $requestinfo["created"] ;
			if ( $duration < 60 )
				$duration = 60 ;
			if ( !$requestinfo["ended"] ) { $duration = "" ; }
			else { $duration = Util_Format_Duration( $duration ) ; }

			$tags = isset( $VALS['TAGS'] ) ? unserialize( $VALS['TAGS'] ) : Array() ;
			$tag_string = "" ;
			if ( isset( $requestinfo["tag"] ) && isset( $tags[$requestinfo["tag"]] ) )
			{
				LIST( $status, $color, $tag ) = explode( ",", $tags[$requestinfo["tag"]] ) ;
				$tag_string = rawurldecode( $tag ) ;
			}
		}
		else
		{
			database_mysql_close( $dbh ) ;
			print "Invalid action;" ; exit ;
		}

		$output = UtilChat_ExportChat( "$ces.txt" ) ;
		if ( count( $output ) <= 0 )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;

			$transcript = Chat_ext_get_Transcript( $dbh, $ces ) ;
			$output[] = $transcript["formatted"] ;
			$output[] = $transcript["plain"] ;
		}

		if ( isset( $output[0] ) )
			$output[0] = preg_replace( "/\"/", "&quot;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $output[0] ) ) ;

		$custom_string = "" ;
		if ( $requestinfo["custom"] )
		{
			$customs = explode( "-cus-", $requestinfo["custom"] ) ;
			for ( $c = 0; $c < count( $customs ); ++$c )
			{
				$custom_var = $customs[$c] ;
				if ( $custom_var && preg_match( "/-_-/", $custom_var ) )
				{
					LIST( $cus_name, $cus_val ) = explode( "-_-", rawurldecode( $custom_var ) ) ;
					if ( $cus_val )
					{
						$custom_string .= "<div>$cus_name: <b>$cus_val</b></div>" ;
					}
				}
			}
		}
	}
	else
	{
		$requestinfo = Array() ;
		$operator = Array() ;
		$output = Array() ;
		
		$operator["name"] = "invalid" ; $operator["email"] = "invalid" ;
		$ces = "invalid" ; $requestinfo["ces"] = "invalid" ; $requestinfo["vname"] = "invalid" ; $requestinfo["vemail"] = "invalid" ; $requestinfo["created"] = $now ;
		$os = 5 ;
		$browser = 5 ;
		$duration = "" ;

		$tag_string = $custom_string = "" ;
		$output[] = "" ;
		$output[] = "" ;
	}
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Print Chat Transcript </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>"> 
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/initiate/transcript.css?<?php echo $VERSION ?>">
<script data-cfasync="false" type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="../js/youtube-vimeo-url-parser.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="../js/modernizr.js?<?php echo $VERSION ?>"></script>

<script data-cfasync="false" type="text/javascript">
<!--
	var base_url = ".." ;
	var time_format = <?php echo ( !isset( $VALS['TIMEFORMAT'] ) || ( $VALS['TIMEFORMAT'] != 24 ) ) ? 12 : 24 ; ?> ;
	var widget ;
	var has_download_support = 0 ; var a = document.createElement('a') ; if (typeof a.download != "undefined") { has_download_support = 1 ; }
	var timestamp = <?php echo ( isset( $deptvars["timestamp"] ) && !$opid ) ? $deptvars["timestamp"] : 1 ; ?> ;
	var mobile = <?php echo $mobile ?> ;

	var addon_emo = <?php echo $addon_emo ?> ;

	$(document).ready(function()
	{
		var transcript = init_timestamps( "<?php echo $output[0] ?>" ) ;
		$('#chat_transcript').html( transcript.emos().extract_youtube() ) ;
		if ( has_download_support && 0 ) { $('#span_download_link').show() ; }
		window.focus() ;
	});

	function do_print()
	{
		$('#chat_body').focus() ;
		window.print() ;
	}
//-->
</script>
</head>
<body id="chat_body" style="overflow: auto; padding: 0px;">
<div id="chat_options">
	<div style="margin-bottonm: 10px;" class="info_box">
		<div id="options_print" style="font-size: 16px; font-weight: bold;"><span onClick="do_print()" style="cursor: pointer;"><img src="../themes/initiate/printer.png" width="16" height="16" border="0" alt=""> <?php echo $LANG["CHAT_PRINT"] ?></span> <span id="span_download_link" style="display: none;">&nbsp; <span style="font-weight: normal;">or</span> &nbsp; <span style="cursor: pointer;"><a href="...still in progress..." download="<?php echo $ces ?>_transcript.text"><img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> Download as TEXT File</a></span></span></div>
	</div>
	<div class="cn">
		<table cellspacing=0 cellpadding=2 border=0>
		<tr>
			<td nowrap><?php echo $LANG["TXT_DEPARTMENT"] ?> &nbsp; &nbsp; </td>
			<td><span class="text_operator" style=""><?php echo $department["name"] ?></td>
		</tr>
		<tr>
			<td style="padding-top: 15px;">Created</td>
			<td style="padding-top: 15px;"><?php echo date( "M j, Y, $VARS_TIMEFORMAT", $requestinfo["created"] ) ; ?></td>
		</tr>
		<tr>
			<td>Visitor</td>
			<td style=""><?php echo $requestinfo["vname"] ?> <?php echo ( $requestinfo["vemail"] && ( ( $requestinfo["vemail"] != "null" ) && ( $requestinfo["vemail"] != "invalid" ) ) ) ? "&lt;$requestinfo[vemail]&gt;" : "" ; ?></td>
		</tr>
		<tr>
		<tr>
			<td>Operator</td>
			<td style=""><?php echo $operator["name"] ?> &lt;<?php echo $operator["email"] ?>&gt;</td>
		</tr>
		<tr>
			<td>Duration</td>
			<td style=""><?php echo $duration ?></td>
		</tr>
		<tr>
			<td>Chat ID</td>
			<td style=""><?php echo $ces ?></td>
		</tr>
		<tr>
			<td>Visitor ID</td>
			<td style=""><?php echo isset( $requestinfo["md5_vis"] ) ? $requestinfo["md5_vis"] : "invalid" ; ?></td>
		</tr>
		<?php if ( $tag_string ): ?>
		<tr>
			<td>Tag</td>
			<td style=""><?php echo $tag_string ?></td>
		</tr>
		<?php endif ; ?>
		<?php if ( $custom_string ): ?>
		<tr>
			<td>Custom Vars</td>
			<td style="">
				<div><?php echo $custom_string ?></div>
			</td>
		</tr>
		<?php endif ; ?>
		</table>
	</div>
</div>
<div id="chat_transcript"></div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>