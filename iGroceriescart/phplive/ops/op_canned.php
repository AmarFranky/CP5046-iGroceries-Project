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
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	if ( !$opinfo = Util_Security_AuthOp( $dbh ) ){ ErrorHandler( 602, "Invalid operator session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; exit ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "n" ) ;
	$flag = Util_Format_Sanatize( Util_Format_GetVar( "flag" ), "n" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ; if ( !$deptid ) { $deptid = 0 ; }

	$error = "" ;

	if ( $action === "submit" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/put.php" ) ;

		$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "ln" ) ;
		$message = Util_Format_Sanatize( Util_Format_GetVar( "message" ), "" ) ;
		$catid = -1 ;

		if ( preg_match( "/_/", $deptid ) )
			LIST( $deptid, $catid ) = explode( "_", $deptid ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		if ( isset( $caninfo["opID"] ) )
			$opid = $caninfo["opID"] ;
		else
			$opid = $opinfo["opID"] ;

		$cats_extra = "" ;
		if ( $opid == 1111111111 )
		{
			$cats_extra = ( $caninfo["cats_extra"] && Util_Functions_itr_is_serialized( $caninfo["cats_extra"] ) ) ? unserialize( $caninfo["cats_extra"] ) : Array() ;
			
			$cats_extra[$opinfo["opID"]] = "$deptid,$catid" ;
			$deptid = $caninfo["deptID"] ;
			$catid = $caninfo["catID"] ;

			$cats_extra = count( $cats_extra ) ? serialize( $cats_extra ) : "" ;
		}

		if ( !$canid = Canned_put_Canned( $dbh, $canid, $opid, $deptid, $catid, $cats_extra, $title, $message ) )
		{
			$error = "Error processing canned message." ;
			$catid = -1 ; $canid = 0 ; $deptid = 0 ;
		}
		else
			$deptid = 0 ; // reset due to All Departments
		$action = "success" ;
	}
	else if ( $action === "delete" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/remove.php" ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		if ( $caninfo["opID"] == $opinfo["opID"] )
			Canned_remove_Canned( $dbh, $opinfo["opID"], $canid ) ;
		$action = "success" ; $canid = 0 ; $deptid = 0 ;
	}

	$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;
	$cans = Canned_get_OpCanned( $dbh, $opinfo["opID"], 0 ) ;
	$opvars = Ops_get_OpVars( $dbh, $opinfo["opID"] ) ;

	$vis_idle_canned = Array() ;
	if ( isset( $opvars["vis_idle_canned"] ) && $opvars["vis_idle_canned"] )
		$vis_idle_canned = unserialize( $opvars["vis_idle_canned"] ) ;

	// make hash for quick refrence
	$can_cats_prefill = "{ \"1111111111\":[], " ;
	$dept_hash = Array() ;
	$dept_hash[1111111111] = "All Departments" ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;

		$can_cats_prefill .= " \"$department[deptID]\":[], " ;
	} $can_cats_prefill = preg_replace( "/, $/", "", $can_cats_prefill ) ;
	$can_cats_prefill .= " }" ;

	$theme = $opinfo["theme"] ;
	if ( !is_file( "$CONF[DOCUMENT_ROOT]/themes/$theme/style.css" ) ) { $theme = "default" ; }
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> canned responses </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/<?php echo $theme ?>/style.css?<?php echo filemtime ( "../themes/$theme/style.css" ) ; ?>">
<script data-cfasync="false" type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script data-cfasync="false" type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>

<script data-cfasync="false" type="text/javascript">
<!--
	var global_deptid = <?php echo $deptid ?> ; var global_deptid_cat ; var global_catid ;
	var global_auto_canid = parent.auto_canid ;
	var cans = [] ;
	var cats_backup = {} ; var cats = new Object ;
	if ( typeof( JSON ) != "undefined" )
	{
		cats_backup = ( <?php echo ( isset( $opvars["can_cats"] ) && $opvars["can_cats"] ) ? 1 : 0 ; ?>  ) ? JSON.parse( decodeURIComponent( "<?php echo ( isset( $opvars["can_cats"] ) && $opvars["can_cats"] ) ? rawurlencode( $opvars["can_cats"] ) : "" ; ?>" ) ) : JSON.parse( '<?php echo $can_cats_prefill ?>' ) ;
		cats = jQuery.extend( true, {}, cats_backup ) ;
	}

	$(document).ready(function()
	{
		if ( "<?php echo $flag ?>" == "new_canned" )
			toggle_new(1) ;

		init_total_cat_cans() ;
		update_sort(<?php echo $deptid ?>) ;

		if ( global_auto_canid ) { $('#auto_can_'+global_auto_canid).prop('checked', true) ; }

		var div_height = parent.extra_wrapper_height - 45 ;
		setTimeout(function(){
			$('#canned_container').css({'min-height': div_height}).fadeIn( "slow", function() {
				if ( <?php echo $canid ?> )
				{
					var div_pos = $('#tr_div_'+<?php echo $canid ?>).position() ;
					var div_height_ = Math.round( $('#tr_div_'+<?php echo $canid ?>).height()/2 ) ;
					var scroll_to = div_pos.top - $(document).height() + div_height_ + 200 ;

					$('#canned_container').animate({
						scrollTop: scroll_to
					}, 300, function() {
						if ( scroll_to ) {
							$('#span_title_'+<?php echo $canid ?>).fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").promise( ).done(function( ) {
								//
							}) ;
						}
					});
				}
			}) ;
		}, 100) ;
		$('#canned_box_new').css({'min-height': div_height}) ;

		<?php if ( ( ( $action == "submit" ) && !$error ) || ( $action == "success" ) ): ?>
			parent.cats = cats ;
			parent.populate_cans(0) ;
			parent.do_alert( 1, "Success" ) ;
		<?php endif ; ?>

		parent.init_extra_loaded() ;
		$('#input_title').dblclick(function() {
			$('#input_title').val("") ;
			update_sort( global_deptid ) ;
		}) ;

		if ( typeof( JSON ) != "undefined" ) { $('#div_cans_categories_ok').show() ; }
		else { $('#div_cans_categories_error').show() ; }

		print_dept_select() ;
		if ( ( <?php echo count( $departments ) ?> > 1 ) || parent.global_can_exists ) { $('#div_global_cat').show() ; }
	});

	function toggle_new( theflag )
	{
		// theflag = 1 means force show, not toggle
		if ( $('#canned_box_new').is(':visible') && !theflag )
		{
			$( "input#canid" ).val( "" ) ;
			$( "input#title" ).val( "" ) ;
			$( "#deptid" ).val( $("#deptid option:first").val() ) ;
			$( "#message" ).val( "" ) ;

			$('#table_categories').hide() ;
			$('#canned_box_new').hide() ; $('#table_canned_new').hide() ;
			$('#canned_container').fadeIn("fast") ;
			toggle_menu_info( "list" ) ;
		}
		else
		{
			$('#title').attr("readonly", false) ;
			$('#message').attr("readonly", false) ;

			$('#div_alert_admin').hide() ;

			$('#table_categories').hide() ;
			$('#canned_container').hide() ;
			$('#canned_box_new').fadeIn("fast") ; $('#table_canned_new').show() ;
		}
	}

	function do_edit( thecanid, thetitle, theopid, thedeptid, thecatid, themessage )
	{
		var deptid_select = ( parseInt( thecatid ) != -1 ) ? thedeptid+"_"+thecatid+"_" : thedeptid ;

		$( "input#canid" ).val( thecanid ) ;
		$( "input#title" ).val( thetitle.replace( /&-#39;/g, "'" ) ) ;
		$( "#deptid" ).val( deptid_select ) ;
		$( "#message" ).val( themessage.replace(/<br>/g, "\r\n").replace( /&-#39;/g, "'" ) ) ;
		
		toggle_new(0) ;

		if ( parseInt( theopid ) == 1111111111 )
		{
			$('#title').attr("readonly", true) ;
			$('#message').attr("readonly", true) ;
			
			$('#div_alert_admin').show() ;
		}
		else
		{
			$('#title').attr("readonly", false) ;
			$('#message').attr("readonly", false) ;

			$('#div_alert_admin').hide() ;
		}
	}

	function do_delete( thiscanid )
	{
		var deptid = parseInt( $('#deptid_sort').val() ) ;

		if ( confirm( "Really delete this canned response?" ) )
			location.href = "op_canned.php?action=delete&deptid="+deptid+"&canid="+thiscanid ;
	}

	function do_submit()
	{
		var canid = $('#canid').val() ;
		var title = $('#title').val() ;
		var deptid = $('#deptid').val() ;
		var message = $('#message').val() ;

		if ( !parseInt( deptid ) )
			do_alert( 0, "Please select a Department." ) ;
		else if ( title == "" )
			do_alert( 0, "Please provide a Reference title." ) ;
		else if ( message == "" )
			do_alert( 0, "Please provide a Message." ) ;
		else
			$('#theform').submit() ;
	}

	function toggle_menu_info( themenu )
	{
		var divs = Array( "list", "settings" ) ;

		for ( var c = 0; c < divs.length; ++c )
		{
			$('#canned_'+divs[c]).hide() ;
			$('#menu_canned_'+divs[c]).removeClass('menu_traffic_info_focus').addClass('menu_traffic_info') ;
		}

		$('#canned_'+themenu).show() ;
		$('#menu_canned_'+themenu).removeClass('menu_traffic_info').addClass('menu_traffic_info_focus') ;
	}

	function select_auto_can( thecanid )
	{
		$('#div_confirm').show() ;
		$('#canned_list').find('*').each( function () {
			var div_name = this.id ;
			if ( div_name.indexOf( "auto_can_" ) == 0 )
				this.checked = false ;
		}) ;
		$('#auto_can_'+thecanid).prop('checked', true) ;

		$('#confirm_canid').val( thecanid ) ;
		if ( global_auto_canid != thecanid )
		{
			$('#confirm_value').val( 1 ) ;
			$('#confirm_title').html( "Automatically select and send this canned response immediately after accepting a chat?" ) ;
		}
		else
		{
			$('#confirm_value').val( 0 ) ;
			$('#confirm_title').html( "De-select this canned response?" ) ;
		}
	}

	function select_auto_can_doit( theoption )
	{
		var thecanid = parseInt( $('#confirm_canid').val() ) ;
		var thevalue = parseInt( $('#confirm_value').val() ) ;

		$('#div_confirm').hide() ;
		if ( theoption )
		{
			var unique = unixtime() ;
			var json_data = new Object ;

			$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_cans.php",
			data: "action=auto_canned&canid="+thecanid+"&value="+thevalue+"&unique="+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					if ( thevalue )
						parent.auto_canid = global_auto_canid = thecanid ;
					else
					{
						parent.auto_canid = global_auto_canid = 0 ;
						$('#auto_can_'+thecanid).prop('checked', false) ;
					}
					do_alert( 1, "Success" ) ;
				}
				else
					do_alert( 0, "Error updating.  Please refresh the console and try again." ) ;
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error updating.  Please refresh the console and try again." ) ;
			} });
		}
		else if ( global_auto_canid )
		{
			$('#auto_can_'+thecanid).prop('checked', false) ;
			$('#auto_can_'+global_auto_canid).prop('checked', true) ;
		}
		else
			$('#auto_can_'+thecanid).prop('checked', false) ;
	}

	function toggle_categories( theflag )
	{
		if ( $('#categories').is(':visible') && !theflag )
		{
			$('#canned_box_new').hide() ; $('#table_categories').hide() ;
			$('#canned_container').fadeIn("fast") ;
			toggle_menu_info( "list" ) ;
		}
		else
		{
			delete( cats ) ; cats = jQuery.extend( true, {}, cats_backup ) ;
			print_categories() ;

			$('#canned_container').hide() ;
			$('#canned_box_new').fadeIn("fast") ; $('#table_categories').show() ;
		}
	}

	function update_sort( thedeptid )
	{
		var counter = 0 ;
		var bg_color = "" ; var can_string = "" ; var output = "" ;

		global_deptid = thedeptid ;

		var thisdeptid = global_deptid ;
		if ( global_deptid.toString().match( /_/g ) )
		{
			var temp_array = global_deptid.toString().split( "_" ) ;
			thisdeptid = parseInt( temp_array[0] ) ;
		}

		var search_string = $('#input_title').val().replace( /"/g, '' ).replace( /\s/g, '' ) ;
		if ( search_string != "" )
		{
			do_search_cans( {keyCode: 13, shiftKey: false} ) ;
			return true ;
		}

		for ( var c = 0; c < cans.length; ++c )
		{
			var caninfo = cans[c] ;
			var template = decodeURIComponent( caninfo["template"] ) ;
			var title = decodeURIComponent( caninfo["title"] ) ;
			var message = decodeURIComponent( caninfo["message"] ) ;

			var matches = template.match( /tr_dept_(.*?)_(.*?)_/i ) ;
			var tempdeptid = ( ( typeof( matches[1] ) != "undefined" ) && ( matches[1] != "" ) ) ? parseInt( matches[1] ) : "" ;
			var thiscatid = ( ( typeof( matches[2] ) != "undefined" ) && ( matches[2] != "" ) ) ? parseInt( matches[2] ) : "" ;
			var cat_name = ( ( typeof( cats[tempdeptid] ) != "undefined" ) && ( typeof( cats[tempdeptid][thiscatid] ) != "undefined" ) ) ? "<div style=\"text-shadow: none;\" class=\"\"><img src=\"../themes/initiate/category.png\" width=\"16\" height=\"16\" border=0 alt=\"category\" title=\"category\"> "+cats[tempdeptid][thiscatid]+"</div>" : "" ;

			var tr_id = "tr_dept_"+global_deptid+"_" ; tr_id = tr_id.replace( /__/, "_" ) ;
			var tr_id_regex = new RegExp( tr_id, "i" ) ;

			if ( !parseInt( thedeptid ) || ( template.match( tr_id_regex ) ) )
			{
				bg_color = !( counter % 2 ) ? "" : "chat_info_tr_traffic_row" ; ++counter ;

				can_string = template.replace( /%%title%%/, title ).replace( /%%message%%/, message ).replace( /%%bg_color%%/, bg_color ) ;
				can_string = can_string.replace( /%%cat_string%%/, cat_name ) ;

				output += can_string ;
			}
		}
		$('#tbody_canned_list').html( output ) ;
		$('#canned_container').animate({
			scrollTop: 0
		}, 200);

		$('#deptid_sort').val(global_deptid).blur() ;
	}

	var search_index = 0 ;
	function do_search_cans( e )
	{
		var key = e.keyCode ;
		var shift = e.shiftKey ;
		var search_string = $('#input_title').val().replace( /"/g, '' ) ;
		var search_regex = new RegExp( search_string.replace( /([.?*+^$[\]\\(){}|-])/g, "\\$1" ), "gi" ) ;

		var deptid = $('#deptid_sort').val() ;
		var deptid_tr = ( parseInt( deptid ) == 0 ) ? "" : deptid ;

		var temp_string = search_string.replace( /\s/g, '' ) ;
		if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) && ( temp_string != "" ) )
		{
			var bg_color = "" ; var can_string = "" ;
			var search_index = 0 ;
			var counter = 0 ;

			if ( search_string != $('#input_title').val() )
				$('#input_title').val( search_string ) ;

			$('#link_reset').show() ;
			$('#tbody_canned_list').html( "<tr><td colspan=\"5\"><img src=\"../themes/"+parent.theme+"/loading_fb.gif\" border=\"0\" alt=\"\"></td></tr>" ) ;

			var output_string = "" ;
			while( search_index < cans.length )
			{
				var caninfo = cans[search_index] ;
				var template = decodeURIComponent( caninfo["template"] ) ;
				var title = decodeURIComponent( caninfo["title"] ) ;
				var message = decodeURIComponent( caninfo["message"] ) ;

				var matches = template.match( /tr_dept_(.*?)_(.*?)_/i ) ;
				var tempdeptid = ( ( typeof( matches[1] ) != "undefined" ) && ( matches[1] != "" ) ) ? parseInt( matches[1] ) : "" ;
				var thiscatid = ( ( typeof( matches[2] ) != "undefined" ) && ( matches[2] != "" ) ) ? parseInt( matches[2] ) : "" ;
				var cat_name = ( ( typeof( cats[tempdeptid] ) != "undefined" ) && ( typeof( cats[tempdeptid][thiscatid] ) != "undefined" ) ) ? "<div style=\"text-shadow: none;\" class=\"\"><img src=\"../themes/initiate/category.png\" width=\"16\" height=\"16\" border=0 alt=\"category\" title=\"category\"> "+cats[tempdeptid][thiscatid]+"</div>" : "" ;
				if ( ( title.match( search_regex ) ) || ( message.match( search_regex ) ) )
				{
					var tr_id = "tr_dept_"+deptid_tr+"_" ; tr_id = tr_id.replace( /__/, "_" ) ;
					var tr_id_regex = new RegExp( tr_id, "i" ) ;

					if ( !deptid || template.match( tr_id_regex ) )
					{
						bg_color = !( counter % 2 ) ? "" : "chat_info_tr_traffic_row" ; ++counter ;

						can_string = template.replace( /%%title%%/, title ).replace( /%%message%%/, message ).replace( /%%bg_color%%/, bg_color ) ;
						can_string = can_string.replace( /%%cat_string%%/, cat_name ) ;

						output_string += can_string ;
					}
				}
				++search_index ;
			}

			if ( output_string != "" )
				$('#tbody_canned_list').hide().html( output_string ).fadeIn("slow") ;
			else
				$('#tbody_canned_list').hide().html( "<tr><td colspan=5 class=\"chat_info_td_traffic\">Blank results.</td></tr>" ).fadeIn("slow") ;

			search_index = 0 ;
		}
		else
		{
			if ( search_string == "" )
			{
				$('#link_reset').hide() ;
				update_sort( global_deptid ) ;

				do_alert( 0, "Please provide a search text." ) ;
				$('#input_title').fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
			}
		}
	}

	var caninfo = new Object ;
	<?php
		for ( $c = 0; $c < count( $cans ); ++$c )
		{
			$caninfo = $cans[$c] ;
			$cats_extra = ( $caninfo["cats_extra"] && Util_Functions_itr_is_serialized( $caninfo["cats_extra"] ) ) ? unserialize( $caninfo["cats_extra"] ) : Array() ;
			$title = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", $caninfo["title"] ) ) ;
			$title_display = Util_Format_ConvertQuotes( $caninfo["title"] ) ;

			if ( isset( $dept_hash[$caninfo["deptID"]] ) )
			{
				$message = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $caninfo["message"] ) ) ) ;
				$message = preg_replace( "/▒~@▒/", "", $message ) ;
				$message_display = preg_replace( "/\"/", "&quot;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", Util_Format_ConvertTags( $caninfo["message"] ) ) ) ;

				$deptid = $caninfo["deptID"] ; $catid = $caninfo["catID"] ;
				if ( isset( $cats_extra[$opinfo["opID"]] ) )
				{
					LIST( $deptid, $catid ) = explode( ",", $cats_extra[$opinfo["opID"]] ) ;
				}
				$dept_name = $dept_hash[$deptid] ;

				$auto_can_div = "<div style=\"text-align: center;\" class=\"chat_info_td_traffic\"><input type=\"checkbox\" id=\"auto_can_$caninfo[canID]\" value=\"$caninfo[canID]\" onClick=\"select_auto_can($caninfo[canID])\"></div>" ;

				$delete_image = ( $caninfo["opID"] == $opinfo["opID"] ) ? "<img src=\"../themes/$theme/delete.png\" style=\"cursor: pointer;\" onClick=\"do_delete($caninfo[canID])\" title=\"delete\" alt=\"delete\" width=\"16\" height=\"16\" border=0>" : "<img src=\"../pics/space.gif\" width=\"16\" height=\"16\" border=0>" ;
				$edit_image = ( $caninfo["opID"] == $opinfo["opID"] ) ? "<img src=\"../themes/$theme/edit.png\" onClick=\"do_edit($caninfo[canID], '$title', '$caninfo[opID]', '$deptid', '$catid', '$message')\" style=\"cursor: pointer;\" title=\"edit\"  alt=\"edit\" width=\"16\" height=\"16\" border=0>" : "<img src=\"../themes/$theme/lock.png\" width=\"16\" height=\"16\" border=0 title=\"created by Setup Admin\" alt=\"created by Setup Admin\" onClick=\"do_edit($caninfo[canID], '$title', '$caninfo[opID]', '$deptid', '$catid', '$message')\" style=\"cursor: pointer;\">" ;

				$can_string = "<tr id=\"tr_div_$caninfo[canID]\" class=\"%%bg_color%%\" name=\"tr_dept_{$deptid}_{$catid}_\"><td class=\"chat_info_td_traffic\" nowrap>$delete_image &nbsp; $edit_image</td><td class=\"chat_info_td_traffic\" nowrap><button type=\"button\" style=\"font-size: 12px;\" onClick=\"parent.select_canned_pre('$title_display')\" class=\"input_op_button\">select</button> <span id=\"span_title_$caninfo[canID]\"><b>%%title%%</b></span></td><td class=\"chat_info_td_traffic\" nowrap>$dept_name%%cat_string%%</td><td class=\"chat_info_td_traffic\">$auto_can_div</td><td class=\"chat_info_td_traffic\"><div id=\"canned_message_$caninfo[canID]\">%%message%%</div></td></tr>" ;

				print "caninfo['template'] = \"".rawurlencode($can_string)."\" ; caninfo['title'] = \"".rawurlencode($title_display)."\" ; caninfo['message'] = \"".rawurlencode($message_display)."\" ; cans.push(caninfo) ; caninfo = new Object ; " ;
			}
		}
	?>

	function do_save()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		var cats_string = encodeURIComponent( JSON.stringify( cats ) ) ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_ext.php",
		data: "action=can_cats&value="+cats_string+"&"+unique,
		success: function(data){
			eval( data ) ;

			if ( parseInt( json_data.status ) )
				location.href = "op_canned.php?action=success&flag=<?php echo $flag ?>" ;
			else
				do_alert( 0, json_data.error ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Lost connection to server.  Please refresh the page and try again." ) ;
		} });
	}

	function print_dept_select()
	{
		$('#deptid').empty() ;
		$('#deptid_sort').empty() ;

		$('#deptid_sort').append('<option value="0">- quick sort -</option>') ;
		<?php
			for ( $c = 0; $c < count( $departments ); ++$c )
			{
				$department = $departments[$c] ;
				print "var total_cans = ( typeof( total_cat_cans['tr_dept_".$department["deptID"]."_'] ) != \"undefined\" ) ? ' ('+total_cat_cans['tr_dept_".$department["deptID"]."_']+')' : \"\" ; " ;
				print "\$('#deptid').append('<option value=\"".$department["deptID"]."\">".$department["name"]."</option>') ; " ;
				print "\$('#deptid_sort').append('<option value=\"".$department["deptID"]."\">".$department["name"]." '+total_cans+'</option>') ; print_dept_cats_select( $department[deptID] ) ; " ;
			}
			if ( count( $departments ) > 1 )
			{
				print "var total_cans = ( typeof( total_cat_cans['tr_dept_1111111111_'] ) != \"undefined\" ) ? \" (\"+total_cat_cans['tr_dept_1111111111_']+\")\" : \"\" ; " ;
				print "\$('#deptid').append('<option value=\"1111111111\">All Departments</option>') ;" ;
				print "\$('#deptid_sort').append('<option value=\"1111111111\">All Departments '+total_cans+'</option>') ; print_dept_cats_select( 1111111111 ) ; " ;
			}
		?>
	}

	function print_dept_cats_select( thedeptid )
	{
		var cats_array = cats[thedeptid] ;

		if ( typeof( cats_array ) != "undefined" )
		{
			for ( var c = 0; c < cats_array.length; ++c )
			{
				var tr_id = 'tr_dept_'+thedeptid+'_'+c+'_' ;
				var total_cans = ( typeof( total_cat_cans[tr_id] ) != "undefined" ) ? " ("+total_cat_cans[tr_id]+")" : "" ;

				$('#deptid').append('<option value="'+thedeptid+'_'+c+'_">&nbsp; &nbsp; &nbsp; '+cats_array[c]+'</option>') ;
				$('#deptid_sort').append('<option value="'+thedeptid+'_'+c+'_">&nbsp; &nbsp; &nbsp; '+cats_array[c]+total_cans+'</option>') ;
			}
		}
	}

	function print_categories()
	{
		for ( var thisdeptid in cats )
		{
			var cats_array = cats[thisdeptid] ;
			$('#div_dept_'+thisdeptid).empty() ;

			for ( var c = 0; c < cats_array.length; ++c )
			{
				$('#div_dept_'+thisdeptid).append( print_row( thisdeptid, c, cats_array[c] ) ) ;
			}
		}
	}

	function add_cat( thedeptid, thecatid )
	{
		global_deptid_cat = thedeptid ;
		global_catid = thecatid ;

		$('#div_add_cat').fadeIn("fast") ;
		$('#cat_name').focus() ;
	}

	function prep_add_cat()
	{
		var thisdeptid = global_deptid_cat ;

		var name = $('#cat_name').val().replace( /<(.*?)>/g, "" ) ;

		if ( name == "" )
		{
			do_alert( 0, "Please provide a Category Name." ) ;
		}
		else
		{
			if ( typeof( cats[thisdeptid] ) == "undefined" )
			{
				cats[thisdeptid] = new Array() ;
			}

			if ( typeof( global_catid ) != "undefined" )
			{
				$('#span_cat_name_'+thisdeptid+"_"+global_catid).html( name ) ;
				cats[thisdeptid][global_catid] = name ;
			}
			else
			{
				cats[thisdeptid].push( name ) ;
				var array_index = cats[thisdeptid].length - 1 ;
				$('#div_dept_'+thisdeptid).append( print_row( thisdeptid, array_index, name ) ) ;
			}

			$('#cat_name').val('') ;
			$('#div_add_cat').fadeOut("fast") ;
		}
	}

	function print_row( thedeptid, thecatid, thename )
	{
		return "<div style=\"margin-top: 15px; padding-left: 45px;\"><img src=\"../themes/initiate/category.png\" width=\"16\" height=\"16\" border=0 alt=\"category\" title=\"category\"> &nbsp; <img src=\"../themes/"+parent.theme+"/edit.png\" width=\"16\" height=\"16\" border=0 alt=\"edit\" title=\"edit\" onClick=\"prep_edit_cat("+thedeptid+", "+thecatid+" )\" style=\"cursor: pointer;\"> &nbsp; <img src=\"../themes/"+parent.theme+"/delete.png\" width=\"16\" height=\"16\" border=0 alt=\"delete\" title=\"delete\" onClick=\"prep_delete_cat("+thedeptid+", "+thecatid+" )\" style=\"cursor: pointer;\"> &nbsp; <span id=\"span_cat_name_"+thedeptid+"_"+thecatid+"\">"+thename+"</span></div>" ;
	}

	function prep_edit_cat( thedeptid, thecatid )
	{
		global_catid = thecatid ;
		$('#cat_name').val(cats[thedeptid][global_catid]) ;

		add_cat( thedeptid, global_catid ) ;
	}

	function prep_delete_cat( thedeptid, thecatid )
	{
		if ( typeof( cats[thedeptid] ) != "undefined" )
			cats[thedeptid].splice( thecatid, 1 ) ;

		var cats_string = "" ;
		for ( var c = 0; c < cats[thedeptid].length; ++c )
		{
			var name = cats[thedeptid][c] ;

			cats_string += print_row( thedeptid, c, name ) ;
		}
		$('#div_dept_'+thedeptid).hide().html(cats_string).fadeIn("slow") ;
	}

	function do_listen_cat( e )
	{
		var key = e.keyCode ;
		var shift = e.shiftKey 

		if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
		{
			prep_add_cat() ;
		}
	}

	var total_cat_cans = new Object ;
	function init_total_cat_cans()
	{
		for ( var c = 0; c < cans.length; ++c )
		{
			var caninfo = cans[c] ;
			var template = decodeURIComponent( caninfo["template"] ) ;
			var title = decodeURIComponent( caninfo["title"] ) ;
			var message = decodeURIComponent( caninfo["message"] ) ;

			var matches = template.match( /tr_dept_(.*?)_(.*?)_/i ) ;
			var tempdeptid = ( ( typeof( matches[1] ) != "undefined" ) && ( matches[1] != "" ) ) ? parseInt( matches[1] ) : "" ;
			var thiscatid = ( ( typeof( matches[2] ) != "undefined" ) && ( matches[2] != "" ) ) ? parseInt( matches[2] ) : "" ;

			var thedeptid = ( parseInt( thiscatid ) != -1 ) ? tempdeptid+"_"+thiscatid+"_" : tempdeptid ;

			var tr_id = "tr_dept_"+thedeptid+"_" ; tr_id = tr_id.replace( /__/, "_" ) ;

			if ( typeof( total_cat_cans[tr_id] ) == "undefined" )
				total_cat_cans[tr_id] = 0 ;
			++total_cat_cans[tr_id] ;

			if ( parseInt( thiscatid ) != -1 )
			{
				tr_id = tr_id.replace( /^tr_dept_(.*?)_(.*?)_$/, "tr_dept_"+tempdeptid+"_" ) ;
				if ( typeof( total_cat_cans[tr_id] ) == "undefined" )
					total_cat_cans[tr_id] = 0 ;
				++total_cat_cans[tr_id] ;
			}
		}
	}
//-->
</script>
</head>
<body>

<div id="canned_container" style="display: none; padding: 15px; height: 200px; padding-top: 0px; overflow: auto;">
	<div style="padding-bottom: 45px;">
		<div style="position: fixed; padding-top: 15px; color: unset;" class="info_content">
			<div class="menu_traffic_info_focus" style="font-weight: normal; border: 0px; cursor: default;"><span class="chat_info_td_t" onClick="toggle_new(1)" style="cursor: pointer;"><img src="../themes/<?php echo $theme ?>/add.png" width="12" height="12" border="0" alt=""> Add Canned Response</span></div>
			<div class="menu_traffic_info_focus" style="font-weight: normal; border: 0px; cursor: default;"><span class="chat_info_td_t" onClick="toggle_categories(1)" style="cursor: pointer;"><img src="../themes/<?php echo $theme ?>/add.png" width="12" height="12" border="0" alt=""> Categories</span></div>
			<div style="float: left; margin-left: 25px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><select name="deptid_sort" id="deptid_sort" style="" onChange="update_sort(this.value)"></select></td>
					<td>&nbsp; &nbsp; &nbsp; &nbsp; <b>search canned:</b></td>
					<td>&nbsp; <input type="text" size="10" maxlength="20" class="input_text_search" style="" id="input_title" onKeyUp="return do_search_cans(event)"></td>
					<td>&nbsp; <button type="button" onClick="do_search_cans({keyCode: 13, shiftKey: false});" class="input_op_button">search</button></td>
					<td>&nbsp; &nbsp; &nbsp; <button type='button' onClick="global_deptid=0;$('#input_title').val('');update_sort(global_deptid);$(this).hide();" style="display: none; font-size: 10px;" id="link_reset" class="input_op_button">reset</button></td>
				</tr>
				</table>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div id="canned_list" style="padding-top: 70px;">
			<table cellspacing=0 cellpadding=0 border=0 width="98%" id="table_trs">
			<tr>
				<td width="60" nowrap><div class="chat_info_td_t">&nbsp;</div></td>
				<td width="180" nowrap><div class="chat_info_td_t">Title</div></td>
				<td width="180"><div class="chat_info_td_t">Department</div></td>
				<td nowrap><div class="chat_info_td_t">Auto Select</div></td>
				<td width="100%"><div class="chat_info_td_t">Message</div></td>
			</tr>
			<tbody id="tbody_canned_list"></tbody>
			</table>
			<div class="chat_info_end" style="padding: 10px;"></div>
		</div>
		<div id="canned_settings" style="display: padding-top: 55px;">
		</div>
	</div>
</div>

<div id="canned_box_new" style="display: none; padding: 15px; height: 200px; overflow: auto;">
	<table cellspacing=0 cellpadding=0 border=0 width="98%" id="table_canned_new" style="display: none;">
	<tr>
		<td valign="top" nowrap width="100%">
			<div style="display: inline-block;" class="info_box"><img src="../themes/<?php echo $theme ?>/info.png" width="14" height="14" border="0" alt=""> Canned responses created here will be available just for your account.</div>
			<div style="display: none; margin-top: 5px;" id="div_alert_admin"><span class="info_error"><img src="../pics/icons/lock.png" width="16" height="16" border="0" alt=""> This canned response was created by the Setup Admin.  Only the <b>Department</b> field can be updated.</span></div>
			<div style="margin-top: 10px;">
				<form method="POST" action="op_canned.php?<?php echo time() ?>" id="theform">
				<input type="hidden" name="action" value="submit">
				<input type="hidden" name="canid" id="canid" value="0">
				<div style="margin-top: 25px; padding-bottom: 25px;">
					Reference (example: "Greeting", "Just a moment")
					<div><input type="text" name="title" id="title" class="input_text" style="width: 98%; margin-bottom: 10px;" maxlength="25"></div>

					<div>Available to Department</div>
					<div><select name="deptid" id="deptid" style="width: 99%; margin-bottom: 10px;"></select></div>

					<div>Canned Message</div>
					<div><textarea name="message" id="message" class="input_text" rows="8" style="white-space: pre-wrap; min-width: 98%; resize: vertical;" wrap="virtual" spellcheck="true"></textarea></div>

					<div style="margin-top: 10px;"><button type="button" onClick="do_submit()" class="input_op_button">Submit</button> &nbsp; &nbsp; &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="toggle_new(0)">cancel</span></div>
				</div>
				</form>
			</div>
		</td>
		<td valign="center" nowrap style="padding-left: 25px;">
			<ul>
				<li> HTML will be converted to raw code.
				<li style="margin-top: 5px;"> Dynamically populated variables:
					<ul style="margin-top: 10px;">
						<li> <b>%%visitor%%</b> = visitor's name
						<li> <b>%%operator%%</b> = your name
						<li> <b>%%op_email%%</b> = your email
						<li> <b>%%chatid%%</b> = chat ID of the current chat session
					</ul>
				<li style="margin-top: 10px;"> To display an image, use the <b>image:</b> prefix
					<ul style="margin-top: 10px;">
						example:
						<li style=""> <b>image:</b><i>https://www.phplivesupport.com/pics/logo.png</i>
					</ul>
			</ul>
		</td>
	</tr>
	</table>
	<table cellspacing=0 cellpadding=0 border=0 width="98%" id="table_categories" style="display: none;">
	<tr>
		<td valign="top" nowrap width="100%">
			<div style="">Canned response <big><b>categories</b></big>.</div>

			<div style="margin-top: 25px; padding-bottom: 25px;">
				<div id="div_cans_categories_ok" style="display: none;">
					<?php
						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$department = $departments[$c] ;

							print "<div style=\"margin-bottom: 15px;\" class=\"info_content\">
									<div><span>$department[name] <img src=\"../pics/icons/arrows/bk_add.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"add category\" title=\"add category\" style=\"cursor: pointer;\" class=\"info_misc\" onClick=\"add_cat($department[deptID], undeefined)\"></span></div>
									<div id=\"div_dept_$department[deptID]\"></div>
								</div>" ;
						}
						print "<div style=\"display: none; margin-bottom: 15px;\" class=\"info_content\" id=\"div_global_cat\">
							<div><span>All Departments <img src=\"../pics/icons/arrows/bk_add.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"add category\" title=\"add category\" style=\"cursor: pointer;\" class=\"info_misc\" onClick=\"add_cat(1111111111, undeefined)\"></span></div>
							<div id=\"div_dept_1111111111\"></div>
						</div>" ;
					?>

					<div style="margin-top: 25px;"><button type="button" onClick="do_save()" class="input_op_button">Save Changes</button> &nbsp; &nbsp; &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="toggle_new(0)">cancel</span></div>
				</div>
				<div id="div_cans_categories_error" style="display: none;">
					<div class="info_error">Canned response categories not available on this web browser.  For best online and chatting experience, please use a modern web browser (example: Google Chrome, Firefox, IE Edge).</div>
					<div style="margin-top: 25px;"><span style="text-decoration: underline; cursor: pointer;" onClick="toggle_new(0)">cancel</span></div>
				</div>
			</div>
		</td>
	</tr>
	</table>
</div>

<div id="div_confirm" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">
	<div id="div_confirm_body" class="info_info" style="position: relative; width: 350px; margin: 0 auto; top: 100px;">
		<div class="info_box" style="padding: 25px; -webkit-box-shadow: -0px 7px 29px rgba(0, 0, 0, 0.34); -moz-box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34);">
			<div id="confirm_title"></div>
			<form><input type="hidden" id="confirm_canid" name="confirm_canid" value=""><input type="hidden" id="confirm_value" name="confirm_value" value=""></form>
			<div style="margin-top: 15px;"><button type="button" onClick="select_auto_can_doit(1)" class="input_op_button">Yes</button> &nbsp; &nbsp; &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="select_auto_can_doit(0)">cancel</span></div>
		</div>
	</div>
</div>

<div id="div_add_cat" style="display: none; position: fixed; top: 0px; left: 0px; height: 100%; padding: 25px; background: url( ../themes/initiate/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20; box-shadow: 1px 1px 15px rgba(0, 0, 0, 0.2);">
	<div class="info_neutral" style="position: relative; margin: 0 auto; top: 170px; padding: 25px; box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);">
		<span style="font-size: 14px; font-weight: bold;">Category Name:</span> <input type="text" size="25" maxlength="45" id="cat_name" onKeyUp="return do_listen_cat(event)" class="input_text_search" style=""> &nbsp; <button type="button" onClick="prep_add_cat()" class="input_op_button">Submit</button> &nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="global_catid=0;$('#div_add_cat').fadeOut('fast');$('#cat_name').val('');">cancel</a>
	</div>
</div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>