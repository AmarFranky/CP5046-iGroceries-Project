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
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; exit ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove.php" ) ;

	$error = "" ;
	$theme = "default" ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
	$page = Util_Format_Sanatize( Util_Format_GetVar( "page" ), "n" ) ;
	$index = Util_Format_Sanatize( Util_Format_GetVar( "index" ), "n" ) ;
	$tid = Util_Format_Sanatize( Util_Format_GetVar( "tid" ), "n" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ; $deptinfo = Array() ;
	$operators = Ops_get_AllOps( $dbh ) ;

	// make hash for quick refrence
	$operators_hash = Array() ;
	for ( $c = 0; $c < count( $operators ); ++$c )
	{
		$operator = $operators[$c] ;
		$operators_hash[$operator["opID"]] = $operator["name"] ;
	}

	$dept_hash = Array() ; $dept_customs = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
		if ( $department["custom"] )
		{
			$custom = unserialize( $department["custom"] ) ;
			$dept_customs = array_merge( $dept_customs, $custom ) ;
		}
		Chat_remove_ExpiredTranscript( $dbh, $department["deptID"], $department["texpire"] ) ;
		if ( $deptid && ( $department["deptID"] == $deptid ) )
			$deptinfo = $departments[$c] ;
	}

	$custom_search_options = "" ;
	$custom_search_field_hash = Array() ; // used for page function Util_Functions_Page

	$search_field_hash = Array() ;
	$search_field_hash["text"] = "text" ;
	$search_field_hash["ces"] = "Chat ID" ;
	$search_field_hash["vid"] = "Visitor ID" ;
	for ( $c = 0; $c < count( $dept_customs ); ++$c )
	{
		$custom = $dept_customs[$c] ;
		if ( $custom && !isset( $custom_search_field_hash[$custom] ) )
		{
			$custom_search_field_hash[$custom] = 1 ;
			$custom_search_options .= "<option value='cus_$c'>$custom</option>" ;
			$search_field_hash["cus_$c"] = $custom ;
		}
		++$c ;
	}

	if ( $action === "delete" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove.php" ) ;
		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;

		if ( !Chat_remove_Transcript( $dbh, $ces ) )
			$error = "Transcript delete error: $dbh[error]" ;
	}

	$query = "SELECT created FROM p_admins WHERE adminID = 1 LIMIT 1" ;
	database_mysql_query( $dbh, $query ) ;
	$super_admin = database_mysql_fetchrow( $dbh ) ;
	if ( isset( $super_admin["created"] ) ) { $y_start = date( "Y", $super_admin["created"] ) ; }
	else { $y_start = 2011 ; }

	$text = Util_Format_Sanatize( Util_Format_GetVar( "text" ), "" ) ; $text = ( $text ) ? $text : "" ; $text_query = urlencode( $text ) ;
	$s_as = Util_Format_Sanatize( Util_Format_GetVar( "s_as" ), "ln" ) ;
	$month = Util_Format_Sanatize( Util_Format_GetVar( "m" ), "n" ) ;
	$year = Util_Format_Sanatize( Util_Format_GetVar( "y" ), "n" ) ;
	if ( $month && $year )
	{
		$stat_start = mktime( 0, 0, 1, $month, 1, $year ) ;
		$stat_end = mktime( 23, 59, 59, $month, date( "t", $stat_start ), $year ) ;
	}
	else
	{
		$stat_start = mktime( 0, 0, 1, 1, 1, $year ) ;
		$stat_end = mktime( 23, 59, 59, 12, date( "t", mktime( 23, 59, 59, 12, 1, $year ) ), $year ) ;
	}
	$transcripts = Chat_ext_get_RefinedTranscripts( $dbh, $deptid, $opid, $tid, $s_as, $text, $year, $stat_start, $stat_end, $page, 15 ) ;

	$total_index = count($transcripts) - 1 ;
	$pages = Util_Functions_Page( $page, $index, 15, $transcripts[$total_index], "transcripts.php", "s_as=$s_as&text=$text_query&deptid=$deptid&opid=$opid&tid=$tid" ) ;

	$tags = isset( $VALS['TAGS'] ) ? unserialize( $VALS['TAGS'] ) : Array() ;
	$tags_hash = Array() ;
	foreach ( $tags as $tag_index => $value )
	{
		if ( $tag_index != "c" )
		{
			LIST( $status, $color, $tag ) = explode( ",", $value ) ;
			$tag = rawurldecode( $tag ) ;
			$tags_hash[$tag_index] = $tag ;
		}
	}

	$addon_autotask = 0 ;
	if ( is_file( "$CONF[CONF_ROOT]/autotask_config.php" ) && isset( $VALS["AUTOTASK"] ) && $VALS["AUTOTASK"] )
	{
		include_once( "$CONF[CONF_ROOT]/autotask_config.php" ) ; // include it here for global reference in function Util_AutoTask_ParseCustom()
		include_once( "$CONF[DOCUMENT_ROOT]/addons/autotask/API/Util_AutoTask.php" ) ;
		$addon_autotask = 1 ;
	}
	$search_reset = ( $deptid || $opid || $year || $text || $s_as || $tid ) ? 1 : 0 ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script data-cfasync="false" type="text/javascript" src="../js/global.js?<?php echo filemtime ( "../js/global.js" ) ; ?>"></script>
<script data-cfasync="false" type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>

<script data-cfasync="false" type="text/javascript">
<!--
	var global_ces ; var global_created ;

	$(document).ready(function()
	{
		$("html").css({'background': '#272727'}) ; $("body").css({'background': '#D6E4F2'}) ;
		init_menu() ;
		toggle_menu_setup( "trans" ) ;

		show_div( "list" ) ;

		$('#form_search').bind("submit", function() { return false ; }) ;

		<?php if ( $action && ( $action == "success" ) && !$error ): ?>do_alert( 1, "Update Success" ) ;
		<?php elseif ( $action && ( $action != "search" ) && !$error ): ?>do_alert( 1, "Delete Success" ) ;
		<?php elseif ( $error ): ?>do_alert( 0, "<?php echo $error ?>" ) ;
		<?php endif ; ?>

		<?php if ( $search_reset ): ?>toggle_search_trans(0) ;<?php endif ; ?>

	});

	function open_transcript( theces, theopname )
	{
		var url = "../ops/op_trans_view.php?ces="+theces+"&id=<?php echo $admininfo["adminID"] ?>&text=<?php echo urlencode( $text ) ?>&auth=setup&"+unixtime() ;

		$('#transcripts').find('*').each( function(){
			var div_name = this.id ;
			if ( div_name.indexOf("img_") != -1 )
				$(this).css({ 'opacity': 1 }) ;
		} );

		$('#img_'+theces).css({ 'opacity': '0.4' }) ;

		External_lib_PopupCenter( url, theces, <?php echo $VARS_CHAT_WIDTH+100 ?>, <?php echo $VARS_CHAT_HEIGHT+85 ?>, "scrollbars=yes,menubar=no,resizable=1,location=no,width=<?php echo $VARS_CHAT_WIDTH+100 ?>,height=<?php echo $VARS_CHAT_HEIGHT+85 ?>,status=0" ) ;
	}

	function input_text_listen_search( e )
	{
		var key = -1 ;
		var shift ;

		key = e.keyCode ;
		shift = e.shiftKey ;

		if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
			e.preventDefault() ;
	}

	function show_div( thediv )
	{
		var divs = Array( "list", "tags", "encr", "export" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#transcripts_'+divs[c]).hide() ;
			$('#menu_trans_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('#transcripts_'+thediv).show() ;
		$('#menu_trans_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function do_notice( thediv, theces, thecreated )
	{
		var pos = $('#tr_'+theces).position() ;
		var width = $('#tr_'+theces).outerWidth() ;
		var height = $('#tr_'+theces).outerHeight() - 15 ;

		global_ces = theces ; global_created = thecreated ;
		show_div_notice(thediv, pos, width, height) ;
	}

	function do_delete_doit()
	{
		if ( global_ces && global_created )
			location.href = "transcripts.php?tid=<?php echo $tid ?>&ces="+global_ces+"&created="+global_created+"&y=<?php echo $year ?>&action=delete&index=<?php echo $index ?>&page=<?php echo $page ?>&deptid=<?php echo $deptid ?>&opid=<?php echo $opid ?>" ;
	}

	function show_div_notice( thediv, thepos, thewidth, theheight )
	{
		$('#div_notice_'+thediv).css({'top': thepos.top, 'left': thepos.left, 'width': thewidth, 'height': theheight}).fadeIn("fast") ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" style="margin-left: 0px;" onClick="location.href='transcripts.php'" id="menu_trans_list">Transcripts</div>
			<div class="op_submenu" onClick="location.href='transcripts_tags.php'" id="menu_trans_tags">Tags</div>
			<!-- <div class="op_submenu" onClick="show_div('encr')" id="menu_trans_encr">Encryption</div> -->
			<div style="clear: both"></div>
		</div>

		<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/export_transcripts/export_transcripts.php" ) ): ?>
		<div style="margin-top: 25px;">
			<div class="op_submenu_focus" style="margin-left: 0px;">View Transcripts</div>
			<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/export_transcripts/export_transcripts.php" ) ): ?><div class="op_submenu2" onClick="location.href='../addons/export_transcripts/export_transcripts.php'">Export Transcripts</div><?php endif ; ?>
			<div class="op_submenu2" onClick="location.href='transcripts_settings.php'">Automatic Delete</div>
			<div style="clear: both"></div>
		</div>
		<?php endif ; ?>

		<div id="transcripts_list" style="margin-top: 25px;">

			<div style="">
				<?php
					$trans_script = "/setup/transcripts.php" ;
				?>
				<div style=""><span class="info_neutral" style="cursor: pointer;" onClick="toggle_search_trans(0)"><img src="../pics/icons/search.png" width="16" height="16" border="0" alt=""> search transcripts</span></div>
				<div class="info_neutral" id="div_trans_search" style="display: none;">
					<form method="POST" action="" id="form_trans_search" style="">
					<div style="">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td colspan=5 style="padding-bottom: 15px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td style="padding-right: 15px;">
										Department<br>
										<select name="deptid" id="deptid" style="font-size: 16px;">
										<option value="0"></option>
										<?php
											for ( $c = 0; $c < count( $departments ); ++$c )
											{
												$department = $departments[$c] ;
												$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
												print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
											}
										?>
										</select>
									</td>
									<td style="padding-right: 15px;">
										Operator<br>
										<select name="opid" id="opid" style="font-size: 16px;">
										<option value="0"></option>
										<?php
											for ( $c = 0; $c < count( $operators ); ++$c )
											{
												$operator = $operators[$c] ;
												$selected = ( $opid == $operator["opID"] ) ? "selected" : "" ;
												print "<option value=\"$operator[opID]\" $selected>$operator[name]</option>" ;
											}
										?>
										</select>
									</td>
								</tr>
								</table>
							</td>
						</tr>
						<tr id="tr_search_criteria">
							<?php if ( count( $tags ) ): ?>
							<td style="padding-right: 15px;">
								Tag<br>
								<select id="tid" style="font-size: 16px;"><option value="0"></option>
								<?php
									foreach ( $tags as $index => $value )
									{
										if ( $index != "c" )
										{
											LIST( $status, $color, $tag ) = explode( ",", $value ) ;
											$tag = rawurldecode( $tag ) ;

											if ( $status )
											{
												$selected = ( $index == $tid ) ? "selected" : "" ;
												print "<option value=\"$index\" $selected>$tag</option>" ;
											}
										}
									}
								?>
								</select>
							</td>
							<?php else: ?>
							<td><input type="hidden" id="tid" name="tid" value=0></td>
							<?php endif; ?>
							<td style="padding-right: 15px;">
								Month<br>
								<select name="month" id="month">
								<?php
									for ( $c = 0; $c <= 12; ++$c )
									{
										$selected = ( $month == $c ) ? "selected" : "" ;
										$month_expanded = ( $c ) ? date( "F", mktime( 0, 0, 1, $c, 1, 2010 ) ) : "" ;
										print "<option value='$c' $selected>$month_expanded</option>" ;
									}
								?>
								</select>
							</td>
							<td style="padding-right: 15px;">
								Year<br>
								<?php
									$y = date( "Y", time() ) ;
									$year_string = "<select name='year' id='year' style='font-size: 16px;'>" ;
									$year_string .= ( isset( $page ) ) ? "<option value='0'></option>" : "" ;
									for ( $c = $y; $c >= $y_start; --$c ) { $selected = "" ; if ( $year == $c ) { $selected = "selected" ; } $year_string .= "<option value=$c $selected>$c</option>" ; } $year_string .= "</select>" ;
									print $year_string ;
								?>
							</td>
							<td style="padding-right: 15px;">
								Search Text<br>
								<input type="text" class="input_text_search input" size="15" maxlength="55" style="" id="input_search" value="<?php echo $text ?>" onKeydown="input_text_listen_search(event);" autocomplete="off">
							</td>
							<td style="padding-right: 15px;">
								Search Field<br>
								<select name="s_as" id="s_as" style="">
									<option value=""></option>
									<option value="text">text</option>
									<option value="ces">chat ID</option>
									<option value="vid">visitor ID</option> <?php echo $custom_search_options ?>
								</select>
							</td>
							<td>
								&nbsp;<br>
								<div>
									<input type="button" id="btn_page_search" style="" class="btn" value="search" onClick="do_search('<?php echo $CONF["BASE_URL"].$trans_script ?>')">
									<?php if ( $search_reset ): ?>
									&nbsp; &nbsp; &nbsp; <span class="info_blue"><a href="transcripts.php">reset</a></span>
									<?php endif ; ?>
								</div>
								<script data-cfasync="false" type="text/javascript">
									$('#s_as').val('<?php echo ( $s_as ) ? $s_as : "" ; ?>') ;
								</script>
							</td>
						</tr>
						</table>
					</div>
					</form>
				</div>
			</div>

			<div style="margin-top: 25px;"><img src="../pics/icons/flag_blue.png" width="14" height="14" border="0" alt=""> Flag icon indicates the transcript includes the visitor's comment.</div>

			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_transcripts">
				<tr><td colspan="10"><?php echo $pages ?></td></tr>
				<tr>
					<td width="20" nowrap><div class="td_dept_header"><select style="padding: 2px; font-size: 10px; opacity:0.0; filter:alpha(opacity=0); width: 1px;"><option value="">&nbsp;</option></select></div></td>
					<td width="120" nowrap><div class="td_dept_header">Operator<select style="padding: 2px; font-size: 10px; opacity:0.0; filter:alpha(opacity=0); width: 1px;"><option value="">&nbsp;</option></select></div></td>
					<td width="120" nowrap><div class="td_dept_header">Visitor<select style="padding: 2px; font-size: 10px; opacity:0.0; filter:alpha(opacity=0); width: 1px;"><option value="">&nbsp;</option></select></div></td>
					<td width="140"><div class="td_dept_header">Created<select style="padding: 2px; font-size: 10px; opacity:0.0; filter:alpha(opacity=0); width: 1px;"><option value="">&nbsp;</option></select></div></td>
					<td width="90"><div class="td_dept_header">Duration<select style="padding: 2px; font-size: 10px; opacity:0.0; filter:alpha(opacity=0); width: 1px;"><option value="">&nbsp;</option></select></div></td>
					<td><div class="td_dept_header">Question</div></td>
				</tr>
				<?php
					for ( $c = 0; $c < count( $transcripts )-1; ++$c )
					{
						$transcript = $transcripts[$c] ;
						if ( $transcript["opID"] )
						{
							// intercept nulled operator accounts that have been deleted
							if ( !isset( $operators_hash[$transcript["op2op"]] ) ) { $operators_hash[$transcript["op2op"]] = "&nbsp;" ; }
							if ( !isset( $operators_hash[$transcript["opID"]] ) ) { $operators_hash[$transcript["opID"]] = "&nbsp;" ; }

							$operator = ( $transcript["op2op"] ) ? $operators_hash[$transcript["op2op"]] : $operators_hash[$transcript["opID"]] ;
							$created_date = date( "M j, Y", $transcript["created"] ) ;
							$created_time = date( "$VARS_TIMEFORMAT", $transcript["created"] ) ;
							$duration = Util_Format_Duration( $transcript["ended"] - $transcript["created"] ) ;
							$fsize = Util_Functions_Bytes( $transcript["fsize"] ) ;
							$question = preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $transcript["question"] ) ;
							$vname = ( $transcript["op2op"] ) ? $operators_hash[$transcript["opID"]] : Util_Format_Sanatize( $transcript["vname"], "v" ) ;
							$rating = ( $transcript["rating"] ) ? Util_Functions_Stars( "..", $transcript["rating"] ) : "" ;
							$initiated = ( $transcript["initiated"] ) ?  "<img src=\"../pics/icons/info_initiate.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" title=\"Operator Initiated Chat Invite\" alt=\"Operator Initiated Chat Invite\" class=\"info_misc\"> " : "" ;

							$autotask = "" ;
							if ( $addon_autotask && $transcript["atID"] )
							{
								$autotask_dlink_ticketid = Util_AutoTask_ParseCustom( $transcript["custom"] ) ;

								if ( $autotask_dlink_ticketid )
									$autotask = "<a href=\"$autotask_dlink_ticketid\" target=\"_blank\"><img src=\"../pics/icons/autotask.png\" width=\"20\" height=\"16\" border=\"0\" alt=\"\" title=\"Saved to Autotask\" alt=\"Saved to Autotask\" style=\"cursor: pointer;\" class=\"round\"></a> " ;
							}

							$note = ( $transcript["noteID"] ) ?  "<div style='margin-top: 5px; text-align: left;'><img src=\"../pics/icons/flag_blue.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\" title=\"includes visitor comment\" alt=\"includes visitor comment\" style=\"cursor: pointer;\" onClick=\"open_transcript('$transcript[ces]', '$operator')\"></div>" : "" ;
							$tag_string = "" ;
							if ( isset( $tags[$transcript["tag"]] ) )
							{
								LIST( $sthistatus, $thiscolor, $thistag ) = explode( ",", $tags[$transcript["tag"]] ) ;
								$tag_string = "<span class=\"info_neutral\" style=\"padding: 2px; background: #$thiscolor; border: 1px solid #C2C2C2; color: #474747; text-shadow: none;\">".rawurldecode( preg_replace( "/(.*?),/", "", $tags[$transcript["tag"]] ) )."</span> " ;
							}
							$accepted_op = Util_Format_Duration( $transcript["accepted_op"] ) ;
							$accepted_op_string = ( !$initiated ) ? "<div style=\"margin-top: 5px; cursor: help;\" class=\"info_neutral\" alt=\"chat accept time\" title=\"chat accept time\"><img src=\"../pics/icons/clock2.png\" width=\"16\" height=\"16\" border=\"0\"> <span>$accepted_op</span></div>" : "" ;	

							if ( $transcript["op2op"] )
								$question = " <img src=\"../pics/icons/agent.png\" width=\"16\" height=\"16\" border=\"0\" title=\"Operator to Operator Chat\" alt=\"Operator to Operator Chat\" style=\"cursor: help;\"> " ;

							$btn_view = "<div onClick=\"open_transcript('$transcript[ces]', '$operator')\" style=\"cursor: pointer;\" id=\"img_$transcript[ces]\"><img src=\"../pics/btn_view.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;
							$btn_delete = "<div onClick=\"do_notice('delete', '$transcript[ces]', $transcript[created]);\" style=\"margin-top: 10px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

							$bg_color = ( ($c+1) % 2 ) ? "FFFFFF" : "EDEDED" ;
							$td1 = "td_dept_td" ;

							$custom_vars_string = "" ;
							if ( $transcript["custom"] )
							{
								$custom_vars_string = "" ;
								$customs = explode( "-cus-", $transcript["custom"] ) ;
								for ( $c2 = 0; $c2 < count( $customs ); ++$c2 )
								{
									$custom_var = $customs[$c2] ;
									if ( $custom_var && preg_match( "/-_-/", $custom_var ) )
									{
										LIST( $cus_name, $cus_val ) = explode( "-_-", rawurldecode( $custom_var ) ) ;
										if ( $cus_val )
										{
											if ( preg_match( "/^((http)|(www))/", $cus_val ) )
											{
												if ( preg_match( "/^(www)/", $cus_val ) ) { $cus_val = "http://$cus_val" ; }
												$cus_val_snap = ( strlen( $cus_val ) > 40 ) ? substr( $cus_val, 0, 15 ) . "..." . substr( $cus_val, -15, strlen( $cus_val ) ) : $cus_val ;
												$custom_vars_string .= "<div style=\"padding: 2px;\"><b>$cus_name:</b> <a href=\"$cus_val\" target=_blank>$cus_val_snap</a></div>" ;
											}
											else
											{
												$custom_vars_string .= "<div style=\"padding: 2px;\"><b>$cus_name:</b> $cus_val</div>" ;
											}
										}
									}
								}
								$custom_vars_string = ( $custom_vars_string ) ? "<div style=\"margin-top: 15px;\"><table cellspacing=0 cellpadding=0 border=0 width=\"100%\"><tr><td width=\"118\">Custom Variables</td><td style=\"padding-left: 15px;\"><div style=\"max-height: 65px; overflow: auto;\">$custom_vars_string</div></td></tr></table></div>" : "" ;
							}

							print "<tr id=\"tr_$transcript[ces]\" style=\"background: #$bg_color\">
								<td class=\"$td1\">$btn_view$btn_delete</td>
								<td class=\"$td1\">
									<div id=\"transcript_$transcript[ces]\">$initiated$operator</div>
									$accepted_op_string
								</td>
								<td class=\"$td1\">$vname$rating$note</td><td class=\"$td1\" nowrap>$created_date<div style=\"font-size: 10px; margin-top: 3px;\">($created_time)</div></td>
								<td class=\"$td1\" nowrap>
									<div style=\"cursor: help;\" class=\"info_neutral\" alt=\"chat duration\" title=\"chat duration\"><img src=\"../pics/icons/clock3.png\" width=\"16\" height=\"16\" border=\"0\"> <span>$duration</span></div>
									<div style=\"margin-top: 5px; font-size: 10px; margin-top: 3px; text-align: right;\">($fsize)</div>
								</td>
								<td class=\"$td1\" style=\"word-break: break-word; word-wrap: break-word;\">$tag_string $autotask $question$custom_vars_string</td>
							</tr>" ;
						}
					}
					if ( $c == 0 )
						print "<tr><td colspan=9 class=\"td_dept_td\">Blank results.</td></tr>" ;
				?>
				<tr><td colspan="10"><?php echo $pages ?></td></tr>
				</table>
			</div>

		</div>

		<div id="transcripts_encr" style="display: none; margin-top: 25px;">
		</div>

		<div id="div_notice_delete" style="display: none; position: absolute;" class="info_error">
			<div style="padding: 10px;">
				<div class="edit_title">Delete this transcript permanently?</div>

				<div style="margin-top: 5px;"><button type="button" onClick="$(this).attr('disabled', true);do_delete_doit();">Delete</button> &nbsp; &nbsp; &nbsp; <a href="JavaScript:void(0)" style="color: #FFFFFF" onClick="$('#div_notice_delete').fadeOut('fast')">cancel</a></div>
			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ?>
