function nospecials(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("abcdefghijklmnopqrstuvwxyz0123456789_-\.\(\) ").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function logins(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("abcdefghijklmnopqrstuvwxyz0123456789_-\.").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function justemails(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("abcdefghijklmnopqrstuvwxyz0123456789+_-\@\.").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function numbersonly(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("0123456789.").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function noquotes(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("\"\'").indexOf(keychar) != -1))
		return false;
	else
		return true;
}

function noquotestags(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("\"\'<>").indexOf(keychar) != -1))
		return false;
	else
		return true;
}

function noquotestagscomma(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("\"\'<>,").indexOf(keychar) != -1))
		return false;
	else
		return true;
}

function notags(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("<>").indexOf(keychar) != -1))
		return false;
	else
		return true;
}

function justips(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("abcdefghijklmnopqrstuvwxyz0123456789\.:*").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function check_email( theemail )
{
	return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( theemail ) ;
}

function do_alert( theflag, thetext, theduration )
{
	var message ;
	var delay_vis = ( theflag ) ? 3000 : 4000 ;

	if ( thetext && ( thetext.length > 600 ) ) { delay_vis = 14000 ; }
	else if ( thetext && ( thetext.length > 100 ) ) { delay_vis = 5000 ; }

	if ( typeof( theduration ) != "undefined" ) { delay_vis = parseInt( theduration ) * 1000 ; }

	if ( !theflag && thetext && ( thetext.indexOf( "Authentication error" ) == 0 ) && ( location.href.indexOf( "/setup/" ) > 0 ) ) { location.href = "../logout.php?&errno=621&menu=sa&action=logout" ; }

	if ( $('#login_alert_box').length ) { $('#login_alert_box').remove() ; }

	var padding = 25 ;
	if ( theflag )
		message = "<div id=\"login_alert_box\" class=\"info_good\" style=\"display: none; position: absolute; top: 0px; left: 0px; text-align: center; padding: "+padding+"px; font-size: 14px; font-weight: bold; border-width: 2px; z-Index: 10010; -webkit-box-shadow: -0px 7px 29px rgba(0, 0, 0, 0.34); -moz-box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34);\" onClick=\"$(this).hide();\">"+thetext+"</div>" ;
	else
		message = "<div id=\"login_alert_box\" class=\"info_error\" style=\"display: none; position: absolute; top: 0px; left: 0px; text-align: center; padding: "+padding+"px; font-size: 14px; font-weight: bold; border-width: 2px; z-Index: 10010; -webkit-box-shadow: -0px 7px 29px rgba(0, 0, 0, 0.34); -moz-box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34); box-shadow: 0px 7px 29px rgba(0, 0, 0, 0.34);\" onClick=\"$(this).hide();\">"+thetext+"</div>" ;

	if ( !theflag && ( typeof( isop ) != "undefined" ) && isop )
	{
		if ( !thetext.match(/(must be active)|(blank)/i) )
		{
			var orig = $('#error_message').val() ? $('#error_message').val()+"\r\n" : "" ;
			var thetext_timestamped = "["+unixtime()+"] "+thetext ;
			$('#error_message').val( orig+thetext_timestamped ) ;
		}
	}

	$('body').append( message ) ;
	$('#login_alert_box').center().show().fadeOut("slow").fadeIn("fast").delay(delay_vis).fadeOut("slow").hide() ;
}

function do_alert_div( thepath, theflag, thetext )
{
	if ( theflag )
		$('#div_alert').removeClass("info_good").removeClass("info_error").addClass("info_good").html( thetext ) ;
	else
		$('#div_alert').removeClass("info_good").removeClass("info_error").addClass("info_error").html( thetext ) ;

	$('#div_alert').fadeIn("fast") ;
}

function do_search( theurl )
{
	var console = ( $('#console').length && parseInt( $('#console').val() ) == 1 ) ? 1 : 0 ;
	var deptid = ( $('#deptid').val() ) ? $('#deptid').val() : 0 ;
	var opid = ( $('#opid').val() ) ? $('#opid').val() : 0 ;
	var tid = ( $('#tid').val() ) ? $('#tid').val() : 0 ;
	var input_search = encodeURIComponent( $('#input_search').val().trim() ) ;
	var s_as = $('#s_as').val() ;
	var month = $('#month').val() ;
	var year = $('#year').val() ;

	if ( s_as && !input_search )
	{
		$('#input_search').focus() ;
		do_alert( 0, "Search Text must be provided if searching a field." ) ;
		return false ;
	}
	else if ( !s_as && input_search )
	{
		do_alert( 0, "Search Field must be selected if searching a text." ) ;
		$('#s_as').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
		return false ;
	}
	else if ( !parseInt( tid ) && !input_search && !s_as && !parseInt( month ) && !parseInt( year ) )
	{
		if ( ( typeof( global_ces ) != "undefined" ) && !parseInt( deptid ) && !parseInt( opid ) )
		{
			do_alert( 0, "At least one search criteria must be selected." ) ;
			setTimeout( function(){
				$('#tr_search_criteria').fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
			}, 1000 ) ;
			return false ;
		}
		else if ( typeof( global_ces ) == "undefined" )
		{
			// Setup Admin area
			do_alert( 0, "At least one search criteria must be selected." ) ;
			setTimeout( function(){
				$('#tr_search_criteria').fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1).fadeTo("fast", .1).fadeTo("fast", 1) ;
			}, 1000 ) ;
			return false ;
		}
	}
	else if ( month && !parseInt( year ) )
	{
		do_alert( 0, "Year must be selected." ) ;
		$('#year').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
		return false ;
	}

	$('#btn_page_search').val( "searching..." ).attr( "disabled", true ) ;

	setTimeout( function(){
		location.href = theurl+'?action=search&deptid='+deptid+'&opid='+opid+'&tid='+tid+'&m='+month+'&y='+year+'&s_as='+s_as+'&console='+console+'&text='+input_search ;
	}, 1000 ) ;
}

function toggle_search_trans( theforce )
{
	if ( $('#div_trans_search').is(":visible") || theforce )
	{
		$('#div_trans_search').fadeOut("fast") ;
	}
	else
	{
		$('#div_trans_search').fadeIn("fast") ;
	}
}

window.unixtime = function() { return parseInt(new Date().getTime().toString().substring(0, 10)) ; }
function microtime( get_as_float )
{
	var now = new Date().getTime() / 1000 ;
	var s = parseInt(now, 10) ;

	return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s ;
}

function pad( number, length )
{
	var str = '' + number ;
	while ( str.length < length )
		str = '0' + str ;
	return str;
}

function autolink_it( message ){
	var thismessage = message ;
	var theregx = /^\/nolink /i ;
	var match = theregx.exec( thismessage ) ;
	if ( match != null )
		message = message.replace( /^\/nolink /i, "" ).replace( /\//g, "&#47;" ).replace( /\./g, "&#46;" ) ;
	return autolinker.link( message ) ;
}

function new_win_default( theurl )
{
	var unique = unixtime() ;
	window.open(theurl, unique, 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')
}

var is_ios = 0 ; var is_android = 0 ;
function is_mobile()
{
	var userAgent = navigator.userAgent || navigator.vendor || window.opera ;
	if ( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) )
	{
		is_ios = 1 ;
		if ( userAgent.match( /iPad/i ) ) { return 3 ; }
		else { return 1 ; }
	}
	else if ( userAgent.match( /Android/i ) ) { is_android = 1 ; return 2 ; }
	else { return 2 ; }
}

function randomstring( thelength )
{
	var random_string = "";
	var chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789" ;

	for ( var c = 0; c < thelength; c++ )
		random_string += chars.charAt( Math.floor(Math.random() * chars.length) ) ;
	return random_string ;
}

function seconds_to_hhmmss( seconds )
{
	seconds = Number( seconds ) ;
	var h = pad( Math.floor(seconds / 3600), 2 ) ;
	var m = pad( Math.floor(seconds % 3600 / 60), 2 ) ;
	var s = pad( Math.floor(seconds % 3600 % 60), 2 ) ;
	return h +":"+ m +":"+ s ;
}

function External_lib_PopupCenter(url, title, w, h, winopt)
{
	// Firefox slightly off
	var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
	var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

	var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
	var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

	var left = ((width / 2) - (w / 2)) + dualScreenLeft;
	var top = ((height / 2) - (h / 2)) + dualScreenTop;
	var newwin = window.open(url, title, winopt+ ', top=' + top + ', left=' + left);
	if (window.focus) { newwin.focus(); }
	return newwin ;
}

function Is_Chrome()
{
	var isChromium = window.chrome ;
	var winNav = window.navigator ;
	var vendorName = winNav.vendor ;
	var isOpera = typeof window.opr !== "undefined" ;
	var isIEedge = winNav.userAgent.indexOf("Edge") > -1 ;
	var isIOSChrome = winNav.userAgent.match("CriOS") ;

	if ( isIOSChrome )
		return true ;
	else if ( isChromium !== null && typeof isChromium !== "undefined" && vendorName === "Google Inc." && isOpera === true && isIEedge === false )
		return true ;
	else
		return false ;
}

function obj_length( theobj )
{
	// object length
	var count = 0 ;
	for ( var i in theobj ) {
		if ( theobj.hasOwnProperty(i) ) {
			count++ ;
		}
	} return count ;
}

function HTML5_audio_support()
{
	var audio_supported = new Object ;
	if ( Modernizr.audio )
	{
		audio_supported["audio"] = 1 ;
		if ( Modernizr.audio.mp3 ){
			audio_supported["mp3"] = 1 ;
		}
		if ( Modernizr.audio.ogg ){
			audio_supported["ogg"] = 1 ;
		}
		if ( Modernizr.audio.m4a ){
			audio_supported["m4a"] = 1 ;
		}
	}
	return audio_supported ;
} var undeefined ;
/***** variable replacing *****/
String.prototype.trimreturn = function(){
	if ( this.substr((this.length-2), this.length) == "\r\n" ) { return this.substr(0, (this.length-2)) ; }
	else if ( this.substr((this.length-1), this.length) == "\n" ) { return this.substr(0, (this.length-1)) ; }
	else { return this ; }
};
String.prototype.noreturns = function(){
	return this.replace( /(\r\n)/g, ' p_br ' ).replace( /(\r)/g, ' p_br ' ).replace( /(\n)/g, ' p_br ' ) ;
};
String.prototype.nl2br = function(){
	// minor thing perhaps add " p_br " spaces for above
	return this.replace( /( p_br )/g, '<br>' ) ;
};

String.prototype.tags = function(){ var string = this.replace(/>/g, "&gt;"); return string.replace(/</g, "&lt;"); };

/***** ws: related *****/
String.prototype.curlys = function(){ var string = this.replace(/{/g, "&#123;"); return string.replace(/}/g, "&#125;"); };
String.prototype.brackets = function(){ var string = this.replace(/\[/g, "&#91;"); return string.replace(/\]/g, "&#93;"); };
String.prototype.tabs = function(){ return this.replace(/\t/g, " "); };
String.prototype.slashes = function(){ return this.replace(/\\/g, "&#92;"); };
/**********************/

String.prototype.c615 = function(){ var string = this.replace(/<c615>(.*?)<\/c615>/g, ""); return string; };
String.prototype.vars = function( thevname ){
	var string = this ;
	var the_vname = ( ( typeof( chats ) != "undefined" ) && ( typeof( chats[ces] ) != "undefined" ) ) ? chats[ces]["vname"] : "" ;
	var the_cname = ( typeof( chats ) != "undefined" ) ? cname : "" ;
	var the_cemail = ( typeof( chats ) != "undefined" ) ? cemail : "" ;
	var the_ces = ( typeof( ces ) != "undefined" ) ? ces : "" ;

	if ( thevname ) { the_vname = thevname }

	string = string.replace( /((%%user%%)|(%%visitor%%))/gi, "<span class='notranslate'>"+the_vname+"</span>" );
	string = string.replace( /%%operator%%/gi, "<span class='notranslate'>"+the_cname+"</span>" );
	string = string.replace( /%%op_email%%/gi, "<a tag='link' href='mailto:"+the_cemail+"' class='notranslate' target='"+phplive_link_target+"'>"+the_cemail+"</a>" );
	string = string.replace( /%%chatid%%/gi, "<span class='notranslate'>"+the_ces+"</span>" ) ;
	return string;
};
String.prototype.vars_global = function(){
	var string = this ;

	if ( ( string.toLowerCase().indexOf("image:") != -1 ) || ( string.toLowerCase().indexOf("image_nolink:") != -1 ) || ( string.toLowerCase().indexOf("pdf:") === 0 ) || ( string.toLowerCase().indexOf("txt:") === 0 ) || ( string.toLowerCase().indexOf("conf:") === 0 ) || ( string.toLowerCase().indexOf("zip:") === 0 ) || ( string.toLowerCase().indexOf("tar:") === 0 ) )
	{
		if ( string.match( /image:(.*?):name:(.*?)($| |<br>)/i ) )
			string = string.replace( /image:(.*?):name:(.*?)($| |<br>)/ig, function( $0, $1, $2 ) { return "<div style='padding: 5px; margin-bottom: 5px;' class='info_neutral'><a tag='link' href='"+base_url_full+"/view.php?file="+encodeURIComponent( $2 )+"' target='"+phplive_link_target+"'><img src='"+$1+"' style='max-width: 100%; max-height: 100%;' border=0 class='round'><br>"+$2+"</a></div> " } ) ;
		else if ( string.match( /image:(.*?)($| |<br>)/i ) )
			string = string.replace( /image:(.*?)($| |<br>)/ig, "<div style='padding: 5px; margin-bottom: 5px;' class='info_neutral'><a tag='link' href='$1' target='"+phplive_link_target+"'><img src='$1' style='max-width: 100%; max-height: 100%;' border=0 class='round'></a></div> " ) ;
		else if ( string.match( /image_nolink:(.*?)($| |<br>)/i ) )
			string = string.replace( /image_nolink:(.*?)($| |<br>)/ig, "<div style='padding: 5px; margin-bottom: 5px;' class='info_neutral'><img src='$1' style='max-width: 100%; max-height: 100%;' border=0 class='round'></div> " ) ;
		else if ( string.match( /pdf:(.*?)($| |<br>)/i ) )
			string = string.replace( /pdf:(.*?)($| |<br>)/ig, function( $0, $1, $2 ) { return "<div style='padding: 5px; margin-bottom: 5px;'><span class='info_neutral'><a tag='link' href='"+base_url_full+"/view.php?file="+encodeURIComponent( $1 )+"' target='"+phplive_link_target+"'><img src='"+base_url_full+"/pics/icons/pdf.gif' width='50' height='50' border=0 class='round'> "+$1+"</a></span></div> " } ) ;
		else if ( string.match( /txt:(.*?)($| |<br>)/i ) )
			string = string.replace( /txt:(.*?)($| |<br>)/ig, function( $0, $1, $2 ) { return "<div style='padding: 5px; margin-bottom: 5px;'><span class='info_neutral'><a tag='link' href='"+base_url_full+"/view.php?file="+encodeURIComponent( $1 )+"' target='"+phplive_link_target+"'><img src='"+base_url_full+"/pics/icons/txt.gif' width='50' height='50' border=0 class='round'> "+$1+"</a></span></div> " } ) ;
		else if ( string.match( /conf:(.*?)($| |<br>)/i ) )
			string = string.replace( /conf:(.*?)($| |<br>)/ig, function( $0, $1, $2 ) { return "<div style='padding: 5px; margin-bottom: 5px;'><span class='info_neutral'><a tag='link' href='"+base_url_full+"/view.php?file="+encodeURIComponent( $1 )+"' target='"+phplive_link_target+"'><img src='"+base_url_full+"/pics/icons/conf.gif' width='50' height='50' border=0 class='round'> "+$1+"</a></span></div> " } ) ;
		else if ( string.match( /zip:(.*?)($| |<br>)/i ) )
			string = string.replace( /zip:(.*?)($| |<br>)/ig, function( $0, $1, $2 ) { return "<div style='padding: 5px; margin-bottom: 5px;'><span class='info_neutral'><a tag='link' href='"+base_url_full+"/view.php?file="+encodeURIComponent( $1 )+"' target='"+phplive_link_target+"'><img src='"+base_url_full+"/pics/icons/zip.gif' width='50' height='50' border=0 class='round'> "+$1+"</a></span></div> " } ) ;
		else if ( string.match( /tar:(.*?)($| |<br>)/i ) )
			string = string.replace( /tar:(.*?)($| |<br>)/ig, function( $0, $1, $2 ) { return "<div style='padding: 5px; margin-bottom: 5px;'><span class='info_neutral'><a tag='link' href='"+base_url_full+"/view.php?file="+encodeURIComponent( $1 )+"' target='"+phplive_link_target+"'><img src='"+base_url_full+"/pics/icons/tar.gif' width='50' height='50' border=0 class='round'> "+$1+"</a></span></div> " } ) ;
	}
	else if ( string.match( /(https?:\/\/\S+(\.png|\.jpg|\.jpeg|\.gif|\.svg|\.webp))/i ) )
	{
		string = string.replace( /(https?:\/\/\S+(\.png|\.jpg|\.jpeg|\.gif|\.svg|\.webp))/ig, "<div style='padding: 5px; margin-bottom: 5px;' class='info_neutral'><a tag='image' href='$1' target='"+phplive_link_target+"'><img src='$1' style='max-width: 100%; max-height: 100%;' border=0 class='round'></a></div> " ) ;
	}
	return string;
};

String.prototype.emos = function(){
	var string = this ;

	if ( ( typeof( addon_emo ) != "undefined" ) && parseInt( addon_emo ) )
	{
		string = string.replace( /(&lt;3)/g, "<img src='"+base_url+"/addons/emoticons/heart.png' width='18' height='18' border='0'>" );
		string = string.replace( /(&gt;:\()/g, "<img src='"+base_url+"/addons/emoticons/angry.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:\))/g, "<img src='"+base_url+"/addons/emoticons/smile.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:\()/g, "<img src='"+base_url+"/addons/emoticons/sad.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:\\)/g, "<img src='"+base_url+"/addons/emoticons/confused.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:'\()/g, "<img src='"+base_url+"/addons/emoticons/cry.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:\$)/g, "<img src='"+base_url+"/addons/emoticons/embarrassed.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:-D)/g, "<img src='"+base_url+"/addons/emoticons/ecstatic.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:\|)/g, "<img src='"+base_url+"/addons/emoticons/neutral.png' width='18' height='18' border='0'>" );
		string = string.replace( /(\|_)/g, "<img src='"+base_url+"/addons/emoticons/thumbs_up.png' width='18' height='18' border='0'>" );
		string = string.replace( /(;\))/g, "<img src='"+base_url+"/addons/emoticons/wink.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:-O)/g, "<img src='"+base_url+"/addons/emoticons/omg.png' width='18' height='18' border='0'>" );
	}

	return string;
};

if ( typeof String.prototype.trim !== 'function' ) {
  String.prototype.trim = function() {
    return this.replace( /^\s+|\s+$/g, '' ) ; 
  }
}

var phplive_wp ;
function init_menu()
{
	$( '*', 'body' ).each( function(){
		var div_name = $( this ).attr('id') ;
		var class_name = $( this ).attr('class') ;
		if ( class_name == "menu" )
		{
			$(this).hover(
				function () {
					if ( $(this).attr('class') != "menu_focus" )
						$(this).removeClass('menu').addClass('menu_hover') ;
				}, 
				function () {
					if ( $(this).attr('class') != "menu_focus" )
						$(this).removeClass('menu_hover').addClass('menu') ;
				}
			);
		}
	} );
}

function init_menu_op()
{
	$( '*', 'body' ).each( function(){
		var div_name = $( this ).attr('id') ;
		var class_name = $( this ).attr('class') ;
		if ( class_name == "menu" )
		{
			$(this).hover(
				function () {
					if ( $(this).attr('class') != "menu_focus" )
						$(this).removeClass('menu').addClass('menu_hover') ;
				}, 
				function () {
					if ( $(this).attr('class') != "menu_focus" )
						$(this).removeClass('menu_hover').addClass('menu') ;
				}
			);
		}
	} );
}

function toggle_menu_op( themenu )
{
	var divs = new Object ;
	divs["go"] = "" ;
	divs["activity"] = "Online/Offline Activity" ;
	divs["reports"] = "" ;
	divs["themes"] = "" ;
	divs["notifications"] = "" ;
	divs["settings"] = "" ;

	for ( var div_name in divs )
	{
		$('#menu_'+div_name).removeClass('menu_focus').addClass('menu') ;
		$('#op_'+div_name).hide() ;
	}

	menu = themenu ;
	$('#op_title').html( divs[themenu] ) ;
	$('#menu_'+themenu).removeClass('menu').removeClass('menu_hover').addClass('menu_focus') ;
	$('#op_'+themenu).show() ;
}

function logout_op()
{
	location.href = "../logout.php?action=logout" ;
}

function toggle_menu_setup( themenu )
{
	var divs = Array( "home", "depts", "ops", "icons", "html", "trans", "rchats", "rtraffic", "interface", "settings", "extras" ) ;

	for ( var c = 0; c < divs.length; ++c )
		$('#menu_'+divs[c]).removeClass('menu_focus').addClass('menu') ;

	$('#menu_'+themenu).removeClass('menu').removeClass('menu_hover').addClass('menu_focus') ;
	menu = themenu ;
}

function preview_theme( thetheme, thewidth, theheight, thedeptid )
{
	var unique = unixtime() ;
	var thetarget = "_blank" ;

	if ( ( typeof( mapp ) != "undefined" ) && mapp )
		thetarget = "_system" ;

	var win_preview = window.open( "../phplive.php?d="+thedeptid+"&theme="+thetheme+"&preview=1&"+unique, "theme_preview", 'scrollbars=no,resizable=yes,menubar=no,location=no,screenX=50,screenY=100,width='+thewidth+',height='+theheight, thetarget, "location=yes" ) ;
	win_preview.focus() ;
}

function phplive_utf8_encode(r){if(null===r||"undefined"==typeof r)return"";var e,n,t=r+"",a="",o=0;e=n=0,o=t.length;for(var f=0;o>f;f++){var i=t.charCodeAt(f),l=null;if(128>i)n++;else if(i>127&&2048>i)l=String.fromCharCode(i>>6|192,63&i|128);else if(55296!=(63488&i))l=String.fromCharCode(i>>12|224,i>>6&63|128,63&i|128);else{if(55296!=(64512&i))throw new RangeError("Unmatched trail surrogate at "+f);var d=t.charCodeAt(++f);if(56320!=(64512&d))throw new RangeError("Unmatched lead surrogate at "+(f-1));i=((1023&i)<<10)+(1023&d)+65536,l=String.fromCharCode(i>>18|240,i>>12&63|128,i>>6&63|128,63&i|128)}null!==l&&(n>e&&(a+=t.slice(e,n)),a+=l,e=n=f+1)}return n>e&&(a+=t.slice(e,o)),a} function phplive_md5(n){var r,t,u,e,o,f,c,i,a,h,v=function(n,r){return n<<r|n>>>32-r},g=function(n,r){var t,u,e,o,f;return e=2147483648&n,o=2147483648&r,t=1073741824&n,u=1073741824&r,f=(1073741823&n)+(1073741823&r),t&u?2147483648^f^e^o:t|u?1073741824&f?3221225472^f^e^o:1073741824^f^e^o:f^e^o},s=function(n,r,t){return n&r|~n&t},d=function(n,r,t){return n&t|r&~t},l=function(n,r,t){return n^r^t},w=function(n,r,t){return r^(n|~t)},A=function(n,r,t,u,e,o,f){return n=g(n,g(g(s(r,t,u),e),f)),g(v(n,o),r)},C=function(n,r,t,u,e,o,f){return n=g(n,g(g(d(r,t,u),e),f)),g(v(n,o),r)},b=function(n,r,t,u,e,o,f){return n=g(n,g(g(l(r,t,u),e),f)),g(v(n,o),r)},m=function(n,r,t,u,e,o,f){return n=g(n,g(g(w(r,t,u),e),f)),g(v(n,o),r)},y=function(n){for(var r,t=n.length,u=t+8,e=(u-u%64)/64,o=16*(e+1),f=new Array(o-1),c=0,i=0;t>i;)r=(i-i%4)/4,c=i%4*8,f[r]=f[r]|n.charCodeAt(i)<<c,i++;return r=(i-i%4)/4,c=i%4*8,f[r]=f[r]|128<<c,f[o-2]=t<<3,f[o-1]=t>>>29,f},L=function(n){var r,t,u="",e="";for(t=0;3>=t;t++)r=n>>>8*t&255,e="0"+r.toString(16),u+=e.substr(e.length-2,2);return u},S=[],_=7,j=12,k=17,p=22,q=5,x=9,z=14,B=20,D=4,E=11,F=16,G=23,H=6,I=10,J=15,K=21;for(n=this.phplive_utf8_encode(n),S=y(n),c=1732584193,i=4023233417,a=2562383102,h=271733878,r=S.length,t=0;r>t;t+=16)u=c,e=i,o=a,f=h,c=A(c,i,a,h,S[t+0],_,3614090360),h=A(h,c,i,a,S[t+1],j,3905402710),a=A(a,h,c,i,S[t+2],k,606105819),i=A(i,a,h,c,S[t+3],p,3250441966),c=A(c,i,a,h,S[t+4],_,4118548399),h=A(h,c,i,a,S[t+5],j,1200080426),a=A(a,h,c,i,S[t+6],k,2821735955),i=A(i,a,h,c,S[t+7],p,4249261313),c=A(c,i,a,h,S[t+8],_,1770035416),h=A(h,c,i,a,S[t+9],j,2336552879),a=A(a,h,c,i,S[t+10],k,4294925233),i=A(i,a,h,c,S[t+11],p,2304563134),c=A(c,i,a,h,S[t+12],_,1804603682),h=A(h,c,i,a,S[t+13],j,4254626195),a=A(a,h,c,i,S[t+14],k,2792965006),i=A(i,a,h,c,S[t+15],p,1236535329),c=C(c,i,a,h,S[t+1],q,4129170786),h=C(h,c,i,a,S[t+6],x,3225465664),a=C(a,h,c,i,S[t+11],z,643717713),i=C(i,a,h,c,S[t+0],B,3921069994),c=C(c,i,a,h,S[t+5],q,3593408605),h=C(h,c,i,a,S[t+10],x,38016083),a=C(a,h,c,i,S[t+15],z,3634488961),i=C(i,a,h,c,S[t+4],B,3889429448),c=C(c,i,a,h,S[t+9],q,568446438),h=C(h,c,i,a,S[t+14],x,3275163606),a=C(a,h,c,i,S[t+3],z,4107603335),i=C(i,a,h,c,S[t+8],B,1163531501),c=C(c,i,a,h,S[t+13],q,2850285829),h=C(h,c,i,a,S[t+2],x,4243563512),a=C(a,h,c,i,S[t+7],z,1735328473),i=C(i,a,h,c,S[t+12],B,2368359562),c=b(c,i,a,h,S[t+5],D,4294588738),h=b(h,c,i,a,S[t+8],E,2272392833),a=b(a,h,c,i,S[t+11],F,1839030562),i=b(i,a,h,c,S[t+14],G,4259657740),c=b(c,i,a,h,S[t+1],D,2763975236),h=b(h,c,i,a,S[t+4],E,1272893353),a=b(a,h,c,i,S[t+7],F,4139469664),i=b(i,a,h,c,S[t+10],G,3200236656),c=b(c,i,a,h,S[t+13],D,681279174),h=b(h,c,i,a,S[t+0],E,3936430074),a=b(a,h,c,i,S[t+3],F,3572445317),i=b(i,a,h,c,S[t+6],G,76029189),c=b(c,i,a,h,S[t+9],D,3654602809),h=b(h,c,i,a,S[t+12],E,3873151461),a=b(a,h,c,i,S[t+15],F,530742520),i=b(i,a,h,c,S[t+2],G,3299628645),c=m(c,i,a,h,S[t+0],H,4096336452),h=m(h,c,i,a,S[t+7],I,1126891415),a=m(a,h,c,i,S[t+14],J,2878612391),i=m(i,a,h,c,S[t+5],K,4237533241),c=m(c,i,a,h,S[t+12],H,1700485571),h=m(h,c,i,a,S[t+3],I,2399980690),a=m(a,h,c,i,S[t+10],J,4293915773),i=m(i,a,h,c,S[t+1],K,2240044497),c=m(c,i,a,h,S[t+8],H,1873313359),h=m(h,c,i,a,S[t+15],I,4264355552),a=m(a,h,c,i,S[t+6],J,2734768916),i=m(i,a,h,c,S[t+13],K,1309151649),c=m(c,i,a,h,S[t+4],H,4149444226),h=m(h,c,i,a,S[t+11],I,3174756917),a=m(a,h,c,i,S[t+2],J,718787259),i=m(i,a,h,c,S[t+9],K,3951481745),c=g(c,u),i=g(i,e),a=g(a,o),h=g(h,f);var M=L(c)+L(i)+L(a)+L(h);return M.toLowerCase( )}
var phplive_base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(a){var d="",c=0;for(a=phplive_base64._utf8_encode(a);c<a.length;){var b=a.charCodeAt(c++);var e=a.charCodeAt(c++);var f=a.charCodeAt(c++);var g=b>>2;b=(b&3)<<4|e>>4;var h=(e&15)<<2|f>>6;var k=f&63;isNaN(e)?h=k=64:isNaN(f)&&(k=64);d=d+this._keyStr.charAt(g)+this._keyStr.charAt(b)+this._keyStr.charAt(h)+this._keyStr.charAt(k)}return d},decode:function(a){var d="",c=0;for(a=a.replace(/[^A-Za-z0-9\+\/\=]/g,"");c<a.length;){var b=this._keyStr.indexOf(a.charAt(c++));var e=this._keyStr.indexOf(a.charAt(c++));var f=this._keyStr.indexOf(a.charAt(c++));var g=this._keyStr.indexOf(a.charAt(c++));b=b<<2|e>>4;e=(e&15)<<4|f>>2;var h=(f&3)<<6|g;d+=String.fromCharCode(b);64!=f&&(d+=String.fromCharCode(e));64!=g&&(d+=String.fromCharCode(h))}return d=phplive_base64._utf8_decode(d)},_utf8_encode:function(a){a=a.replace(/\r\n/g,"\n");for(var d="",c=0;c<a.length;c++){var b=a.charCodeAt(c);128>b?d+=String.fromCharCode(b):(127<b&&2048>b?d+=String.fromCharCode(b>>6|192):(d+=String.fromCharCode(b>>12|224),d+=String.fromCharCode(b>>6&63|128)),d+=String.fromCharCode(b&63|128))}return d},_utf8_decode:function(a){var d="",c=0;for(c1=c2=0;c<a.length;){var b=a.charCodeAt(c);128>b?(d+=String.fromCharCode(b),c++):191<b&&224>b?(c2=a.charCodeAt(c+1),d+=String.fromCharCode((b&31)<<6|c2&63),c+=2):(c2=a.charCodeAt(c+1),c3=a.charCodeAt(c+2),d+=String.fromCharCode((b&15)<<12|(c2&63)<<6|c3&63),c+=3)}return d}};
var chat_http_error ; var st_http ; var process_throttle ;
function add_text( theces, thetext )
{
	if ( ( thetext != "" ) && ( typeof( chats[theces] ) != "undefined" ) )
	{
		thetext = init_timestamps( thetext.nl2br() ) ;
		chats[theces]["trans"] += thetext ;

		if ( theces == ces )
		{
			$('#chat_body').append( thetext.emos().extract_youtube() ).children(':last').hide().fadeIn(500) ; // .children(':last') ;
			if ( isop && mapp ) { init_external_url() ; }

			// on IE 8 (standard view) it's not remembering the srollTop... when any div takes focus away
			// scrolling goes back to ZERO... effects are minimal

			if ( thetext.match( /img src/i ) ) { setTimeout( function(){ init_scrolling() ; }, 600 ) ; }
			else { init_scrolling() ; }
		}
	}
}

function add_text_prepare( theflag, thestring )
{
	var thetext = ( typeof( thestring ) != "undefined" ) ? thestring : $( "textarea#input_text" ).val() ;
	thetext = thetext.replace( /▒~@▒/g, "" ) ;

	if ( ( ( typeof( chats[ces] ) == "undefined" ) || ( chats[ces]["status"] !== 1 )  || chats[ces]["disconnected"] ) && ( !thetext.match( /^\// ) || thetext.match( /^\/nolink / ) || !shortcut_enabled ) )
	{
		if ( isop )
		{
			if ( ( typeof( chats[ces] ) != "undefined" ) && !chats[ces]["status"] && chats[ces]["initiated"] )
				do_alert( 0, "Chat invite connecting..." ) ;
			else
				do_alert( 0, "A chat session must be active." ) ;
		}
		else
		{
			if ( chats[ces]["disconnected"] )
				toggle_input_text() ; // call this function to process the language text
		}
		return false ;
	}
	var process_start = get_microtime() ;
	if ( typeof( process_throttle ) == "undefined" ) { process_throttle = process_start ; }
	else
	{
		var process_diff = process_start - process_throttle ;
		process_throttle = process_start ;

		// throttle check don't send but keep it in textarea
		if ( process_diff <= 500 )
			return true ;
	}

	if ( isop )
		thetext = thetext.trimreturn().noreturns().tags().vars(null).vars_global() ;
	else
		thetext = thetext.trimreturn().noreturns().tags().vars_global() ;

	if ( isop && shortcut_enabled ) {
		if ( thetext.match( /^\// ) && !thetext.match( /^\/nolink / ) ) { process_shortcuts( thetext ) ; return true ; }
	}
	thetext = autolink_it( thetext ) ;

	if ( ( thetext != "" ) && ( typeof( chats[ces] ) != "undefined" ) )
	{
		var cdiv ;
		var now = unixtime() ;

		if ( isop )
		{
			if ( chats[ces]["op2op"] )
			{
				if ( parseInt( chats[ces]["op2op"] ) == parseInt( isop ) )
					cdiv = "co" ;
				else
					cdiv = "cv" ;
			}
			else
				cdiv = "co" ;
		}
		else
		{
			cdiv = "cv" ;
			if ( chats[ces]["processed"] <= ( unixtime() - 25 ) )
			{
				chatting() ;
			}
		}

		thetext = "<div class='"+cdiv+"'><span class='notranslate'><b>"+cname+"<timestamp_"+now+"_"+cdiv+">:</b></span> "+thetext+"</div>" ;

		if ( theflag ) { add_text( ces, thetext ) ; }

		if ( addon_ws )
		{
			var ws_text = "{ \"a\": \"chwr\", \"c\": \""+ces+"\", \"o\": \""+isop+"\", \"o_\": \""+isop_+"\", \"o__\": \""+isop__+"\", \"tx\": \""+thetext+"\" }" ;
			ws_init_message_send( ws_text ) ;
		}
		else
		{
			if ( typeof( st_http ) != "undefined" ) { clearTimeout( st_http ) ; st_http = undeefined ; }
			st_http = setTimeout( function(){ $('#chat_processing').show() ; }, 5000 ) ;
			http_text( thetext ) ;
		}
	}

	toggle_input_btn_enable( true ) ;
	if ( typeof( thestring ) == "undefined" ) { $('textarea#input_text').val( "" ) ; }

	if ( !mapp && !mobile ) { $('textarea#input_text').focus() ; }
}

function toggle_input_btn_enable( thevalue )
{
	return true ;
}

var st_http_backlog_responses = "" ;
var st_http_text ;
function http_text( thetext )
{
	var json_data = new Object ;
	var unique = unixtime() ;

	var sendtext = encodeURIComponent( phplive_base64.encode( thetext ) ) ;
	var thesalt = ( typeof( salt ) != "undefined" ) ? salt : "nosalt" ;
	var this_mapp = ( !isop ) ? chats[ces]["mapp"] : mapp ;

	if ( typeof( chats[ces] ) != "undefined" )
	{
		$.ajax({
		type: "POST",
		url: base_url+"/ajax/chat_submit.php",
		data: "requestid="+chats[ces]["requestid"]+"&t_vses="+chats[ces]["t_ses"]+"&isop="+isop+"&isop_="+isop_+"&isop__="+isop__+"&op2op="+chats[ces]["op2op"]+"&opc="+opc+"&ces="+ces+"&mp="+this_mapp+"&text="+sendtext+"&salt="+thesalt+"&unique="+unique+"&",
		success: function(data){
			try {
				eval(data) ;
				if ( chat_http_error )
				{
					st_http_text = undeefined ;
					if ( !chats[ces]["disconnected"] )
					{
						do_alert( 1, "Reconnect success!" ) ;
						chat_http_error = 0 ;
					} else { $('#chat_processing').hide() ; }
				}
			} catch(err) {
				if ( !chats[ces]["disconnected"] )
				{
					if ( st_http_backlog_responses != thetext )
					{
						st_http_backlog_responses += thetext + "<>" ;
					}
					do_alert( 0, "Error sending response.  Retrying..." ) ; chat_http_error = 1 ;
					if ( typeof( st_http_text ) != "undefined" ) { clearTimeout( st_http_text ) ; }
					st_http_text = setTimeout( function(){ http_text( st_http_backlog_responses  ) ; }, 6000 ) ;
				} else { $('#chat_processing').hide() ; }
				return false ;
			}

			if ( json_data.status ) {
				if ( typeof( st_http ) != "undefined" ) { clearTimeout( st_http ) ; st_http = undeefined ; }
				$('#chat_processing').hide() ;
				clearTimeout( st_typing ) ; st_typing = undeefined ;
			}
			else { do_alert( 0, "Error sending message.  Please refresh the page and try again." ) ; }
		},
		error:function (xhr, ajaxOptions, thrownError){
			if ( !chats[ces]["disconnected"] )
			{
				if ( st_http_backlog_responses != thetext )
				{
					st_http_backlog_responses += thetext + "<>" ;
				}
				do_alert( 0, "Error contacting server.  Retrying..." ) ; chat_http_error = 1 ;
				if ( typeof( st_http_text ) != "undefined" ) { clearTimeout( st_http_text ) ; }
				st_http_text = setTimeout( function(){ http_text( st_http_backlog_responses  ) ; }, 6000 ) ;
			} else { $('#chat_processing').hide() ; }
		} });
	}
}

function get_microtime()
{
	return new Date().getTime() ;
}

function input_text_listen( e )
{
	var key = e.keyCode ;
	var shift = e.shiftKey ;

	if ( ( typeof( addon_typewriter ) != "undefined" ) && ( typeof( addon_typewriter_play_sound ) == "function" ) ) { addon_typewriter_play_sound( key ) ; }
	if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
	{
		if ( typeof( e.target ) != "undefined" )
		{
			var thetext = $( "textarea#input_text" ).val() ;
			if ( e.target.selectionStart != thetext.length )
			{
				var index_total = thetext.length - 1 ;
				var index_begin = e.target.selectionStart-1 ;
				var index_end = e.target.selectionStart ;
				var new_text = thetext.substr(0, index_begin) + thetext.substr(e.target.selectionStart, index_total) ;
				$( "textarea#input_text" ).val( new_text ) ;
			}
		}
		add_text_prepare(1) ;
	}
	else if ( ( key == 8 ) || ( key == 46 ) )
	{
		if ( $( "textarea#input_text" ).val() == "" )
			toggle_input_btn_enable( true ) ;
	}
	else if ( $( "textarea#input_text" ).val() == "" )
		toggle_input_btn_enable( true ) ;
	else
		toggle_input_btn_enable( false ) ;
}

function input_text_listen_check( e )
{
	// iOS seems to prefer onKeypress for key detection and action
	// onKeyup is used to ensure no no return char left behind

	//*******************************/
	// not used until further testing
	//*******************************/

	var key = e.keyCode ;

	if ( key == 13 )
		$( "textarea#input_text" ).val('') ;
}

function input_text_typing( e )
{
	input_focus() ;
	if ( isop && shortcut_enabled )
	{
		var thetext = $( "textarea#input_text" ).val() ;
		if ( thetext.match( /^\// ) ) { return true ; }
	}

	if ( $( "textarea#input_text" ).val() )
	{
		if ( typeof( st_typing ) == "undefined" )
		{
			send_istyping() ;
			st_typing = setTimeout( function(){ clear_istyping() ; }, 5000 ) ;
		}
	}
}

function init_typing()
{
	si_typing = setInterval(function(){
		if ( typeof( chats[ces] ) != "undefined" )
		{
			if ( chats[ces]["istyping"] )
			{
				$('#chat_vistyping_wrapper').show() ;
				$('#chat_vistyping').fadeTo( "slow", 1 ).fadeTo( "slow", 0.2 ) ;
			}
			else
			{
				$('#chat_vistyping_wrapper').hide() ;
				$('#chat_vistyping').fadeOut("slow") ;
			}
		}
	}, 1500) ;
}

function send_istyping()
{
	var json_data = new Object ;
	var unique = unixtime() ;

	if ( typeof( chats[ces] ) != "undefined" )
	{
		if ( addon_ws )
		{
			var ws_text = "{ \"a\": \"ty\", \"o\": \""+isop+"\", \"c\": \""+ces+"\", \"ty\": 1 }" ;
			ws_init_message_send( ws_text ) ;
		}
		else
		{
			$.ajax({
			type: "GET",
			url: base_url+"/ajax/chat_actions_istyping.php",
			data: "a=t&isop="+isop+"&isop_="+isop_+"&c="+ces+"&f=1&"+unique+"&",
			success: function(data){
				try {
					eval(data) ;
				} catch(err) {
					do_alert( 0, err ) ;
					return false ;
				}

				if ( json_data.status ) {
					return true ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				// suppress error to limit confusion... if error here, there will be error reporting in more crucial areas
			} });
		}
	}
}

function clear_istyping( theforce )
{
	var json_data = new Object ;
	var unique = unixtime() ;

	if ( typeof( theforce ) == "undefined" ) { theforce = 0 ; }
	if ( typeof( chats[ces] ) != "undefined" )
	{
		if ( addon_ws )
		{
			var ws_text = "{ \"a\": \"ty\", \"c\": \""+ces+"\", \"ty\": 0 }" ;
			ws_init_message_send( ws_text ) ;
			st_typing = undeefined ;
		}
		else
		{
			$.ajax({
			type: "GET",
			url: base_url+"/ajax/chat_actions_istyping.php",
			data: "a=t&isop="+isop+"&isop_="+isop_+"&c="+ces+"&f="+theforce+"&"+unique+"&",
			success: function(data){
				try {
					eval(data) ;
				} catch(err) {
					do_alert( 0, err ) ;
					return false ;
				}

				if ( json_data.status ) {
					clearTimeout( st_typing ) ;
					st_typing = undeefined ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				// suppress error to limit confusion... if error here, there will be error reporting in more crucial areas
			} });
		}
	}
}

function init_clear_istyping_check()
{
	// is typing indicator check and clear
	if ( ( typeof( ces ) != "undefined" ) && ( typeof( chats[ces] ) != "undefined" ) && !chats[ces]["disconnected"] )
	{
		if ( $('#chat_vistyping_wrapper').is(':visible') )
		{
			chats[ces]["istyping_counter"] += 1 ;
			if ( chats[ces]["istyping_counter"] > 60 )
			{
				$('#chat_vistyping_wrapper').hide() ;
				$('#chat_vistyping').hide() ;
				chats[ces]["istyping"] = 0 ;
				clear_istyping(2) ;
			}
		}
		else if ( chats[ces]["istyping_counter"] > 0  )
			chats[ces]["istyping_counter"] = 0 ;
	}
	else if ( $('#chat_vistyping_wrapper').is(':visible') )
	{
		$('#chat_vistyping_wrapper').hide() ;
		$('#chat_vistyping').hide() ;
	}
}

function init_scrolling()
{
	if ( ( typeof( chats ) != "undefined" ) && ( typeof( chats[ces] ) != "undefined" ) && ( parseInt( chats[ces]["status"] ) != 2 ) )
	{
		// slight delay for chat end custom message content
		if ( chats[ces]["disconnected"] )
			setTimeout( function(){ $('#chat_body').prop( "scrollTop", $('#chat_body').prop( "scrollHeight" ) ) ; }, 500 ) ;
		else
			$('#chat_body').prop( "scrollTop", $('#chat_body').prop( "scrollHeight" ) ) ;
	}
}

function init_textarea()
{
	return true ;
}

function init_divs( theresize )
{
	phplive_init_orientation_set() ;
	if ( theresize > 1 ) { mapp = theresize ; theresize = 0 ; } // mapp intercept, mapp=1 emulate

	var chat_footer_height = ( $('#chat_footer').height() ) ? $('#chat_footer').height() : 0 ;

	var chat_body_padding = $('#chat_body').css('padding-left') ;
	var chat_body_padding_diff = ( typeof( chat_body_padding ) != "undefined" ) ? 20 - ( chat_body_padding.replace( /px/, "" ) * 2 ) : 0 ;

	var browser_height = ( mapp && mobile && ( mapp != 1 ) ) ? mapp : $(window).height() ;
	var browser_width = ( mapp && mobile && ( mapp != 1 ) ) ? screen.width : $(window).width() ;

	// $(window).height() ; for WKWebView (UIWebView previously)

	var body_height = browser_height ;
	var body_width = ( ( typeof( isop ) != "undefined" ) && parseInt( isop ) ) ? browser_width - 450 : browser_width - 42 ;

	if ( embed )
	{
		var top_disconnect = $('#chat_embed_header').height() + 7 ;

		$('#chat_embed_header').show( ) ;
		body_height = body_height - $('#chat_embed_header').height() - 5 ; // -5 because of transparent background on some themes
		$('#info_disconnect').css({'top': top_disconnect}) ;
	}

	var chat_body_width = body_width ;
	var chat_body_height = body_height ;

	if ( ( typeof( isop ) != "undefined" ) && parseInt( isop ) )
	{
		var chat_panel_top;
		var intro_height = browser_height - 153 ;
		if ( mobile )
			chat_panel_top = intro_height + 55 ;
		else
		{
			chat_panel_top = intro_height + 30 ;
		}

		var data_top = 30 ;
		var intro_left = body_width + 40 ;
		var chat_status_offline_top = intro_height - 80 ;
		var chat_data_height = intro_height ;
		var chat_extra_wrapper_height = browser_height - 90 ;

		var chat_info_body_height = intro_height - ( $('#chat_info_header').height() + $('#chat_info_menu_list').height() ) - 24 ;
		var chat_info_network_height = chat_info_body_height - 55 ;

		var intro_top = 30 ;
		var intro_left = body_width + 40 ;
		var chat_btn_left = intro_left + 5;
		var chat_btn_top = chat_panel_top - 5 ;

		var chat_panel_left = intro_left + $('#chat_btn').outerWidth() ;
		var chat_status_offline_left = chat_panel_left - 20 ;

		var textarea_padding = ( typeof( $('#input_text').css('padding-top') ) != "undefined" ) ? parseInt( $('#input_text').css('padding-top').replace( /px/, "" ) ) : 0 ;

		if ( mapp == 1 )
		{
			chat_body_height = chat_body_height - chat_footer_height - 205 ;
		}
		else
		{ 
			chat_body_height = chat_body_height - chat_footer_height - 215 ;
		}
		chat_body_width = body_width + chat_body_padding_diff ;

		var input_text_width = body_width + 17 ;
		if ( mapp )
		{
			chat_body_height += 45 ;
			if ( mobile == 2 )
			{
				chat_body_height -= 25 ;
			}
			else
			{
				chat_body_height -= $('#chat_options').outerHeight() ;
			}

			input_text_width = browser_width - $('#chat_btn').width() - 50 ;

			$('#chat_btn').css({'bottom': '85px', 'right': '5px'}) ;
		}
		else
		{
			var input_padding = textarea_padding - 5 ;

			input_text_width = input_text_width - 70 - input_padding ;

			$('#chat_btn').css({'top': chat_btn_top, 'left': chat_btn_left}) ;
		}

		if ( mapp && ( textarea_padding > 5 ) ) { $('#input_text').css({'padding': '5px'}) ; }
		else if ( !theresize && ( textarea_padding > 5 ) )
		{
			input_text_width = input_text_width - ( ( textarea_padding - 5 ) * 2 ) ;
			var input_text_height = parseInt( $('#input_text').css('height').replace( /px/, "" ) ) ;
			input_text_height = input_text_height - ( ( textarea_padding - 5 ) * 2 ) ;
			$('#input_text').css({'height': input_text_height}) ;
		}

		$("#chat_input").css({'bottom': "auto"}) ;
		$('#input_text').css({'width': input_text_width}) ;
		$('#chat_body').css({'height': chat_body_height, 'width': chat_body_width}) ;
		$('#chat_data').css({'top': intro_top, 'left': intro_left, 'height': chat_data_height, 'width': 410}) ;
		$('#chat_info_body').css({'max-height': chat_info_network_height}) ;
		$('#chat_info_wrapper_network').css({'height': chat_info_network_height}) ;
		$('#chat_panel').css({'bottom': 55, 'left': chat_panel_left}) ;

		if ( mapp ) { $('#chat_status_offline').css({'bottom': 200, 'left': 15}) ; }
		else { $('#chat_status_offline').css({'top': chat_status_offline_top, 'left': chat_status_offline_left}) ; }

		$('#chat_extra_wrapper').css({'height': chat_extra_wrapper_height}).hide() ;

		if ( theresize )
		{
			clearTimeout( st_resize ) ;
			st_resize = setTimeout( function(){ close_extra( extra ) ; }, 800 ) ;
		}
		else { close_extra( extra ) ; }
	}
	else
	{
		chat_body_width = body_width + chat_body_padding_diff ;
		if ( typeof( view ) != "undefined" )
		{
			chat_body_height -= $('#table_info').height() ;
			chat_body_height -= 100 ; // lift it up so more stats show
		}
		else
		{
			chat_body_height = chat_body_height - $('#chat_profile_pic').height() - $('#chat_options').outerHeight() - $('#chat_input_wrapper').height() - 60 ;

			if ( chat_body_height < 50 )
			{
				chat_body_height = chat_body_height + $('#chat_profile_pic').height() + 15 ;

				$('#chat_profile_pic').hide() ;
			}
			else
			{
				$('#chat_profile_pic').show() ;
			}
		}

		// mobile == 2 added v.4.7.9.9.8 due to update in browser
		if ( ( mobile && !phplive_orientation_isportrait ) && ( mobile != 3 ) && ( mobile != 2 ) )
		{
			chat_body_height = chat_body_height + 35 ;
		}

		if ( typeof( view ) != "undefined" )
		{
			$('#chat_body').css({'height': chat_body_height, 'width': chat_body_width}) ;
		}
		else
		{
			var input_text_width = $('#input_text').width() ;
			$('#chat_body').css({'height': chat_body_height}) ;
		}
	}
}

function update_ces( thejson_data )
{
	thejson_data["text"] = text_decode( thejson_data["text"] ) ;
	var thisces = thejson_data["ces"] ;
	var orig_text = thejson_data["text"] ;
	var append_text = init_timestamps( thejson_data["text"] ) ;

	if ( ( typeof( chats[thisces] ) != "undefined" ) && orig_text )
	{
		chats[thisces]["chatting"] = 1 ;
		chats[thisces]["trans"] += append_text ;

		// parse for flags before doing functions
		if ( ( append_text.indexOf("</top>") != -1 ) && !parseInt( isop ) )
		{
			var regex_trans = /<top>(.*?)</ ;
			var regex_trans_match = regex_trans.exec( append_text ) ;
			
			chats[ces]["oname"] = regex_trans_match[1] ;
			$('#chat_vname').empty().html( regex_trans_match[1] ) ;

			var regex_opid = /<!--opid:(.*?)-->/ ;
			var regex_opid_match = regex_opid.exec( append_text ) ;
			isop_ = regex_opid_match[1] ;

			var regex_mapp = /<!--mapp:(.*?)-->/ ;
			var regex_mapp_match = regex_mapp.exec( append_text ) ;
			chats[ces]["mapp"] = regex_mapp_match[1] ;

			var regex_mapp = /<!--name:(.*?)-->/ ;
			var regex_mapp_match = regex_mapp.exec( append_text ) ;
			$('#chat_profile_name').html( regex_mapp_match[1] ) ;

			var regex_mapp = /<!--department:(.*?)-->/ ;
			var regex_mapp_match = regex_mapp.exec( append_text ) ;
			$('#chat_department_name').html( regex_mapp_match[1] ) ;

			var regex_mapp = /<!--profile_pic:(.*?)-->/ ;
			var regex_mapp_match = regex_mapp.exec( append_text ) ;
			if ( regex_mapp_match[1] )
			{
				$('#td_chat_profile_pic_img').fadeOut("fast").promise( ).done(function( ) {
					$('#chat_profile_pic_img').html( "<img src='"+regex_mapp_match[1]+"' width='55' height='55' border='0' alt='' class='profile_pic_img'>" ) ;
					$('#td_chat_profile_pic_img').fadeIn("fast") ;
				}) ;
			}
			else
				$('#td_chat_profile_pic_img').fadeOut("fast") ;
		}

		if ( ( thejson_data["text"].indexOf( "<disconnected>" ) != -1 ) && !chats[thisces]["disconnected"] )
		{
			chats[thisces]["disconnected"] = unixtime() ;
			if ( isop )
			{
				var btn_close_chat = "<c615><div style='margin-top: 5px; margin-bottom: 15px;'><button onClick='cleanup_disconnect(ces)' style='padding: 10px;' class='input_op_button'>close chat</button></div></c615>" ;
				append_text += btn_close_chat ; chats[thisces]["trans"] += btn_close_chat ;
				clearInterval( chats[thisces]["timer_si"] ) ; chats[thisces]["timer_si"] = undeefined ;
			}
			else
			{
				stopit(0) ;
				if ( addon_ws )
				{
					ws_connection.close() ;
				}
			}
		}
		if ( ( thejson_data["text"].indexOf( "<restart_router>" ) != -1 ) && !isop )
		{
			if ( ( thejson_data["text"].indexOf( "<reset_all>" ) != -1 ) && !isop )
			{
				var regex_rtype = /<!--rtype:(.*?)-->/ ;
				var regex_rtype_match = regex_rtype.exec( append_text ) ;
				rtype = regex_rtype_match[1] ;

				var regex_deptid = /<!--deptid:(.*?)-->/ ;
				var regex_deptid_match = regex_deptid.exec( append_text ) ;
				chats[thisces]["deptid"] = regex_deptid_match[1] ;
				chats[thisces]["status"] = 0 ;
				chats[thisces]["processed"] = unixtime() ;

				deptid = chats[thisces]["deptid"] ;
				inqueue = 0 ; loop = 1 ; rstring = "" ;
			}
			else { chats[thisces]["status"] = 2 ; }
			routing(0) ;
		}

		if ( ces == thisces )
		{
			if ( !isop && addon_ws && chats[ces]["disconnect_click"] )
			{
				// skip redundant duplicate message
			}
			else
			{
				$('#chat_body').append( append_text.emos().extract_youtube() ).children(':last').hide().fadeIn(500) ; // .children(':last') ;
			}

			if ( isop && mapp ) { init_external_url() ; }

			if ( append_text.match( /img src/i ) ) { setTimeout( function(){ init_scrolling() ; }, 600 ) ; }
			else { setTimeout( function(){ init_scrolling() ; }, 100 ) ; }

			init_textarea() ;

			$('#chat_vistyping_wrapper').hide() ; // wrapper just incase fadeIn quirk at chat_vistyping
			$('#chat_vistyping').hide() ;
			chats[ces]["istyping"] = 0 ;

			if ( stopped )
			{
				chat_survey() ;
			}
		}

		var flash_console_on = 0 ;
		if ( isop )
		{
			chats[thisces]["recent_res"] = unixtime() ;
			if ( ces != thisces )
			{
				menu_blink( "green", thisces ) ;
			}
			else
			{
				toggle_last_response(1) ;
			}

			var reg = RegExp( chats[thisces]["vname"]+": ", "g" ) ;
			if ( ( typeof( dn_enabled_response ) != "undefined" ) && dn_enabled_response && chats[thisces]["status"] )
			{
				dn_show( 'new_response', thisces, "Response: " + chats[thisces]["vname"], orig_text.replace( /<(.*?)>/g, '' ).replace( reg, ' ' ).replace( /\s+/g, ' ' ), 900000 ) ;
			}
		}
		if ( console_blink_r ) { flash_console_on = 1 ; }

		if ( chats[thisces]["status"] || chats[thisces]["initiated"] )
		{
			if ( chat_sound )
			{
				if ( addon_ws && chats[ces]["disconnect_click"] )
				{
					// skip sound due to instant notify
				}
				else
					play_sound( 0, "new_text", "new_text_"+sound_new_text ) ;
			}
			if ( !isop && embed )
			{
				if ( win_minimized ) { flash_console_on = 1 ; }
			}
			title_blink_init() ;
		}

		if ( flash_console_on ) { flash_console(0) ; }
	}
	if ( isop && !mapp ) { init_maxc() ; }
}

function text_decode( thetext )
{
	var text_array = thetext.split( "<>" ) ;
	var text_output = "" ;
	for ( var c = 0; c < text_array.length; ++c )
	{
		var text = phplive_base64.decode( text_array[c] ) ;
		text_output += text + "<>" ;
	}
	return text_output ;
}

function disconnect( theclick, theces, thevclick )
{
	if ( typeof( theces ) == "undefined" ) { theces = ces ; }
	if ( typeof( thevclick ) == "undefined" ) { thevclick = 0 ; }
	vclick = thevclick ;
	
	if ( theclick )
	{
		document.getElementById('info_disconnect')._onclick = document.getElementById('info_disconnect').onclick ;
		$('#info_disconnect').prop( "onclick", null ).html('<img src="'+base_url+'/themes/'+theme+'/loading_fb.gif" width="16" height="11" border="0" alt="">') ;
		if ( mapp ) { $('#info_disconnect_mapp').prop( "onclick", null ).html('<img src="'+base_url+'/themes/'+theme+'/loading_fb.gif" width="16" height="11" border="0" alt="">') ; }
	}

	else if ( theces == ces ) { $('#chat_vistyping_wrapper').hide() ; $('#chat_vistyping').hide() ; }

	if ( ( ( typeof( theces ) != "undefined" ) && ( typeof( chats[theces] ) != "undefined" ) ) )
	{
		var json_data = new Object ;
		var unique = unixtime() ;

		// limit multiple clicks during internet lag
		if ( !chats[theces]["disconnect_click"] )
		{
			chats[theces]["disconnect_click"] = theclick ;

			$.ajax({
			type: "POST",
			url: base_url+"/ajax/chat_actions_disconnect.php",
			data: "action=disconnect&isop="+isop+"&isop_="+isop_+"&isop__="+isop__+"&ces="+theces+"&vis_token="+chats[ces]["vis_token"]+"&ip="+chats[theces]["ip"]+"&t_vses="+chats[theces]["t_ses"]+"&vclick="+thevclick+"&ws="+addon_ws+"&unique="+unique+"&",
			success: function(data){
				try {
					eval(data) ;
				} catch(err) {
					do_alert( 0, "Error processing disconnect.  Please refresh the page and try again. [e1]" ) ;
					return false ;
				}

				if ( theclick )
				{
					document.getElementById('info_disconnect').onclick = document.getElementById('info_disconnect')._onclick ;
					if ( mapp ) { document.getElementById('info_disconnect_mapp').onclick = document.getElementById('info_disconnect')._onclick ; }
				}
				if ( json_data.status )
				{
					if ( parseInt( isop ) && !theclick )
					{
						chats[theces]["disconnect_click"] = 0 ;
						chats[theces]["disconnected"] = unixtime() ;
						if ( !$('textarea#input_text').is(':disabled') ) { $('textarea#input_text').val( "" ).attr("disabled", true) ; }
					}
					else
						cleanup_disconnect( json_data.ces ) ;

					if ( isop && !mapp ) { init_maxc() ; }
				}
				else { do_alert( 0, "Error processing disconnect.  Please refresh the page and try again. [e2]" ) ; }
			},
			statusCode: {
				500: function() {
					do_alert( 0, "Error processing disconnect.  Please refresh the page and try again. [e500]" ) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error processing disconnect.  Please refresh the page and try again. [e3 - "+xhr.status+"]" ) ;
			} });
		}
	}
}

function init_disconnect()
{
	$('#info_disconnect').hover(
		function () {
			$(this).removeClass('info_disconnect').addClass('info_disconnect_hover') ;
		}, 
		function () {
			$(this).removeClass('info_disconnect_hover').addClass('info_disconnect') ;
		}
	);
}

function init_timer()
{
	if ( typeof( chats[ces] ) != "undefined" )
	{
		start_timer( chats[ces]["timer"] ) ;
		if ( ( ( parseInt( chats[ces]["status"] ) == 1 ) && !parseInt( chats[ces]["disconnected"] ) ) || ( parseInt( chats[ces]["initiated"] ) && !parseInt( chats[ces]["disconnected"] ) ) )
		{
			if ( typeof( chats[ces]["timer_si"] ) != "undefined" ) { clearInterval( chats[ces]["timer_si"] ) ; chats[ces]["timer_si"] = undeefined ; }
			chats[ces]["timer_si"] = setInterval(function(){ if ( typeof( chats[ces] ) != "undefined" ) { start_timer( chats[ces]["timer"] ) ; } }, 1000) ;
		}
	}
}

function start_timer( thetimer )
{
	var diff ; var now = unixtime() ;
	if ( chats[ces]["disconnected"] )
		diff = chats[ces]["disconnected"] - thetimer ;
	else
		diff = now - thetimer ;

	var hours = Math.floor( diff/3600 ) ;
	var mins =  Math.floor( ( diff - ( hours * 3600 ) )/60 ) ;
	var secs = diff - ( hours * 3600 ) - ( mins * 60 ) ;

	var display = pad( mins, 2 )+":"+pad( secs, 2 ) ;
	if ( hours ) { display = pad( hours, 2 )+":"+display ; }

	if ( chats[ces]["status"] || chats[ces]["initiated"] )
	{
		$('#chat_vtimer').html(display) ;
		if ( !isop && !( diff % 10 ) && ( chats[ces]["processed"] <= ( now - 25 ) ) )
		{
			chatting() ;
		}
	}
	else
		$('#chat_vtimer').html("00:00") ;
}

function chat_survey()
{
	if ( !isop && !chats[ces]["survey"] )
	{
		chats[ces]["survey"] = 1 ;
		close_misc("all") ;

		add_text( ces, chat_end_message+"<div style='margin-top: 15px; height: 1px;'></div>" ) ;
		if ( chats[ces]["rate"] && !win_minimized )
		{
			if ( chat_end_message == "" )
			{
				$('#chat_body').removeAttr('onclick') ;
				if ( !mobile )
				{
					var survery_wrapper = $('#chat_survey_rating').html() ;
					var chat_survey = "<div id='chat_survey_content' class='info_content'>"+survery_wrapper.replace( /\r?\n|\r/g, '' )+"</div>" ;

					$('#chat_survey_rating').remove() ;
					$('#chat_body').append( chat_survey ).children(':last').hide().fadeIn(500) ;
					init_scrolling() ;

					chats[ces]["trans"] += chat_survey ; // append to trans so it is visible after minimize and maximize
				}
				else
				{
					var text = $('#div_chat_rate_title').html() ;
					$('#div_chat_rate_title').css({'text-align': 'center'}) ;
					add_text( ces, "<div class='ctitle cl' onClick=\"toggle_rating(0);close_misc('attach');close_misc('trans');close_misc('emo');\" style=\"padding: 10px; text-align: center; cursor: pointer;\">"+text+"</div>" ) ;
				}
			}
			else
			{
				$('#div_chat_rate_title').css({'text-align': 'center'}) ;
				$('#chat_survey_wrapper').show() ;
			}
		}
	}
	window.onbeforeunload = null ;

	if ( embed && !win_minimized ) { $('#embed_win_close').show() ; }
	$('#info_disconnect').hide() ;
}

function submit_survey( thevalue, thetexts )
{
	var json_data = new Object ;
	var unique = unixtime() ;

	if ( parseInt( thevalue ) )
	{
		$.ajax({
		type: "POST",
		url: base_url+"/ajax/chat_actions_rating.php",
		data: "action=rating&requestid="+chats[ces]["requestid"]+"&ces="+ces+"&opid="+chats[ces]["opid"]+"&deptid="+chats[ces]["deptid"]+"&rating="+thevalue+"&unique="+unique+"&",
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				do_alert( 0, err ) ;
				return false ;
			}

			if ( json_data.status )
			{
				chats[ces]["survey"] = 2 ;
				//do_alert( 1, thetexts[0] ) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			// suppress error to limit confusion... if error here, there will be error reporting in more crucial areas
		} });
	}
}

function do_print( theces, thedeptid, theopid, thewidth, theheight )
{
	var winname = "Print_"+theces ;
	var deptid = ( typeof( chats[theces]["deptid"] ) != "undefined" ) ? parseInt( chats[theces]["deptid"] ) : parseInt( thedeptid ) ;
	var opid = ( typeof( chats[theces]["opid"] ) != "undefined" ) ? parseInt( chats[theces]["opid"] ) : parseInt( theopid ) ;

	var url = base_url_full+"/ops/op_print.php?ces="+theces+"&deptid="+deptid+"&opid="+theopid+"&"+unixtime()+"&" ;

	if ( !wp ) { newwin_print = window.open( url, winname, "scrollbars=yes,menubar=no,resizable=1,location=no,width="+thewidth+",height="+theheight+",status=0" ) ; }
	else
	{
		location.href = url ;
	}
}

function init_timestamps( thetranscript )
{
	var lines = thetranscript.split( "<>" ) ;

	var transcript = "" ;
	for ( var c = 0; c < lines.length; ++c )
	{
		var line = lines[c] ;
		var matches = line.match( /timestamp_(\d+)_/ ) ;
		
		var timestamp_value = "" ;
		if ( matches != null )
		{
			var time = extract_time( matches[1] ) ;
			timestamp_value = ( timestamp ) ? " (<span class='ct'>"+time+"</span>) " : "" ;
			transcript += line.replace( /<timestamp_(\d+)_((co)|(cv))>/, timestamp_value ) ;
		}
		else { transcript += line ; }
	}
	return transcript ;
}

function extract_time( theunixtime )
{
	var time_expanded = new Date( parseInt( theunixtime ) * 1000) ;
	var hours = time_expanded.getHours() ;
	if ( ( hours >= 13 ) && ( time_format == 12 ) ) { hours -= 12 ; }
	var output = pad(hours,2)+":"+pad(time_expanded.getMinutes(), 2)+":"+pad(time_expanded.getSeconds(), 2) ;
	return output ;
}

function input_focus() { if ( !focused ) { focused = 1 ; } }

function play_sound( theloop, thediv, thesound )
{
	var unique = unixtime() ;
	var this_mobile = 0 ; // for now loop on mobile as well

	var div_content = $('#div_sounds_'+thediv).html() ;
	if ( mp3_support )
	{
		if ( thesound == "new_text_default" )
			thesound = "new_text_return" ; // new sound file to fix rare sound file issue pre v.4.7.9.1

		if ( thesound == "new_text_return" )
			thesound = "new_text_return_android" ; // new_text_return_android is a bit louder volume

		var audio_obj = $("#div_sounds_audio_"+thediv) ;
		if ( ( ( thediv == "new_request" ) && !div_content && audio_obj[0].paused ) || ( thediv != "new_request" ) )
		{
			$('#div_sounds_'+thediv).html( "on" ) ;
			$("#div_sounds_audio_"+thediv).attr("src", base_url+"/media/"+thesound+'.mp3') ;
			if ( theloop && !this_mobile ) { audio_obj[0].loop = true ; }
			audio_obj[0].volume = sound_volume ;
			audio_obj[0].play() ;
		}
	}
	else
	{
		// no sound support
	}
}

function clear_sound( thediv )
{
	if ( mp3_support )
	{
		var audio_obj = $("#div_sounds_audio_"+thediv) ;
		audio_obj[0].pause() ;
	}
	$('#div_sounds_'+thediv).html("") ;
}

function title_blink_init()
{
	if ( mapp ) { return true ; }
	if ( ( typeof( title_orig ) != "undefined" ) && !parseInt( focused ) )
	{
		if ( typeof( si_title ) != "undefined" )
			clearInterval( si_title ) ;

		if ( ( typeof( embed ) != "undefined" ) && parseInt( embed ) ) {  }
		else { si_title = setInterval(function(){ title_blink( 1, title_orig, "Alert __________________ " ) ; }, 800) ; }
	}
}

function title_blink( theflag, theorig, thenew )
{
	if ( mapp ) { return true ; }
	if( !parseInt( focused ) && ( thenew != "reset" ) )
	{
		if ( ( si_counter % 2 ) && theflag ) { document.title = thenew ; toggle_favicon(true) ; }
		else { document.title = theorig ; toggle_favicon(false) ; }

		++si_counter ;
	}
	else
	{
		if ( typeof( si_title ) != "undefined" )
		{
			clearInterval( si_title ) ; si_title = undeefined ;
			document.title = theorig ;
			toggle_favicon(false) ;
		}
	}
}

function toggle_favicon( thefocus )
{
	var favicon_default = base_url+"/favicon.ico" ;
	var favicon_focus = base_url+"/favicon_focus.ico" ;

	document.head = document.head || document.getElementsByTagName('head')[0] ;

	var thefavicon = document.createElement( 'link' ) ;
	thefavicon.rel = 'shortcut icon' ;
	thefavicon.href = ( thefocus ) ? favicon_focus : favicon_default ;
	if ( document.head ) { document.head.appendChild( thefavicon ) ; }
}

function print_chat_sound_image( thetheme )
{
	if ( chat_sound )
		$('#chat_sound').attr('src', base_url+'/themes/'+thetheme+'/sound_on.png') ;
	else
		$('#chat_sound').attr('src', base_url+'/themes/'+thetheme+'/sound_off.png') ;
}

function flash_console( thecounter )
{
	if ( ( typeof( mapp ) != "undefined" ) && mapp ) { return true ; }
	++thecounter ;
	if ( ( thecounter % 2 ) )
		$('#chat_canvas').addClass('chat_canvas_alert') ;
	else
		$('#chat_canvas').removeClass('chat_canvas_alert') ;

	if ( typeof( st_flash_console ) != "undefined" )
		clearTimeout( st_flash_console ) ;
	st_flash_console = setTimeout( function(){ flash_console( thecounter ) ; }, 1000 ) ;
}

function clear_flash_console()
{
	if ( ( typeof( mapp ) != "undefined" ) && mapp ) { return true ; }
	if ( typeof( st_flash_console ) != "undefined" )
	{
		clearTimeout( st_flash_console ) ; st_flash_console = undeefined ;

		$('#chat_canvas').removeClass('chat_canvas_alert') ;
		if ( typeof( title_orig ) != "undefined" ) { title_blink( 0, title_orig, "reset" ) ; }
	}
}

function close_misc( thediv )
{
	if ( isop )
	{
		clear_flash_console() ;
		toggle_last_response(1) ;
		//clear_sound( "new_request" ) ;
	}

	var divs = Array() ;
	if ( thediv == "all" )
	{
		divs.push( "toggle_emo_box") ;
		divs.push( "toggle_file_attach" ) ;
		divs.push( "toggle_send_trans" ) ;
		divs.push( "toggle_rating" ) ;
	}
	else if ( thediv == "emo" ) { divs.push( "toggle_emo_box" ) ; }
	else if ( thediv == "attach" ) { divs.push( "toggle_file_attach" ) ; }
	else if ( thediv == "trans" ) { divs.push( "toggle_send_trans" ) ; }
	else if ( thediv == "rating" ) { divs.push( "toggle_rating" ) ; }

	for ( var c = 0; c < divs.length; ++c )
	{
		var thisdiv = divs[c] ;
		if ( ( thisdiv == "toggle_emo_box" ) && ( typeof( toggle_emo_box ) == "function" ) ) { toggle_emo_box(1) ; }
		else if ( ( thisdiv == "toggle_file_attach" ) && ( typeof( toggle_file_attach ) == "function" ) ) { toggle_file_attach(1) ; }
		else if ( ( thisdiv == "toggle_send_trans" ) && ( typeof( toggle_send_trans ) == "function" ) ) { toggle_send_trans(1) ; }
		else if ( ( thisdiv == "toggle_rating" ) && ( typeof( toggle_rating ) == "function" ) ) { toggle_rating(1) ; }
	}
}

function textarea_listen()
{
	if ( typeof( si_textarea ) != "undefined" ) { clearInterval( si_textarea ) ; }
	si_textarea = setInterval(function(){
		var temp = $('textarea#input_text').val() ;
		temp = temp.replace( / /g, "" ) ;
		if ( temp ) { toggle_input_btn_enable( false ) ; }
		else { toggle_input_btn_enable( true ) ; }
	}, 200) ;
}

function start_win_status_listener()
{
	if ( typeof( si_win_status ) != "undefined" ) { clearInterval( si_win_status ) ; }
	si_win_status = setInterval(function( ){
		var this_win_width = 0 ;
		// outdated browsers (IE7) throws error when closed but still runs the code due to delay speed
		// need to try first to supress error
		try{
			this_win_width = $('#chat_embed_header').width( ) ;
		} catch(e){
			//
		}
		if ( this_win_width )
		{
			if ( this_win_width < 300 )
			{
				if ( !win_minimized )
				{
					$('#embed_win_maximize').show() ; $('#chat_embed_title').css({ 'opacity': '1' }) ;
					$('#embed_win_minimize').hide() ;
					$('#embed_win_popout').hide() ;
					$('#embed_win_close').hide() ;
					$('#info_disconnect').hide() ;
					$('#login_alert_box').hide() ;
					$('#chat_input_wrapper').hide() ;
					if ( $('#div_policy').length )
						toggle_policy( deptid, 1 ) ;

					if ( typeof( chats ) == "undefined" )
					{
						$(window).scrollTop(0) ;
						$("#request_body").animate({
							scrollTop: 0
						}, 200, function() {
							$(window).scrollTop(0) ; // again to fix quirky IE7 random issues
						});
					}
					else { close_misc("all") ; }
				}
				win_minimized = 1 ;
			}
			else
			{
				if ( win_minimized || ( typeof( win_minimized ) == "undefined" ) )
				{
					$('#embed_win_maximize').hide() ; if ( ( typeof( preview ) == "undefined" ) || ( parseInt( preview ) != 2 ) ) { $('#chat_embed_title').css({ 'opacity': '0' }) ; }
					$('#embed_win_minimize').show() ;
					$('#chat_input_wrapper').show() ;

					// hide on mobile for now because there is setting in Setup Admin
					if ( !mobile && popout ) { $('#embed_win_popout').show() ; }

					if ( ( typeof( chats ) != "undefined" ) && !chats[ces]["disconnected"] )
						$('#info_disconnect').show() ;

					if ( ( typeof( chats ) == "undefined" ) || chats[ces]["disconnected"] )
						$('#embed_win_close').show() ;

					if ( typeof( chats ) == "undefined" )
					{
						$(window).scrollTop(0) ;
						$("#request_body").animate({
							scrollTop: 0
						}, 200, function() {
							$(window).scrollTop(0) ; // again to fix quirky IE7 random issues
						});
					}

					clear_flash_console() ;
				}
				win_minimized = 0 ;
			}
		}
	}, 10) ;
}

function start_new_response_listner()
{
	var new_response = 0 ;
	if ( typeof( si_new_response ) != "undefined" ) { clearInterval( si_new_response ) ; }
	si_new_response = setInterval(function( ){
		if ( win_minimized )
		{
			if ( ( typeof( st_flash_console ) != "undefined" ) && !new_response )
			{
				new_response = 1 ;
				// IE7 slight delay to fix quirk on some situations
				setTimeout( function(){ $('#embed_win_maximize_img').attr('src', new_response_image) ; }, 200 ) ;
			}
		}
		else
		{
			new_response = 0 ;
			$('#embed_win_maximize_img').attr('src', './themes/'+theme+'/win_max.png?'+version) ;
		}
	}, 150) ;
}

function phplive_init_orientation_set()
{
	var width = $(window).width() ;
	var height = $(window).height() ;
	if ( phplive_mobile === 2 )
	{
		// Android keyboard resize must use screen instead of window
		width = screen.width ;
		height = screen.height ;
	}
	if ( height > width ) { phplive_orientation_isportrait = 1 ; }
	else { phplive_orientation_isportrait = 0 ; }
}

var browser_filter = ( [].filter ) ? 1 : 0 ;
function webkit_version()
{
	// for browser webkit, not significant
	// but for iOS mapp webkit:
	// * <= 537.36 (iOS 9.2.1) var device_height = $(window).height() ;
	var result = /AppleWebKit\/([\d.]+)/.exec(navigator.userAgent) ;
	if ( result ) { return parseFloat(result[1]) ; }
	return 0 ;
}

/***********************/
// BEGIN chat engine
/***********************/
function init_chatting()
{
	if ( !loaded )
		st_init_chatting = setTimeout(function(){ init_chatting() }, 300) ;
	else
	{
		if ( typeof( st_init_chatting ) != "undefined" )
		{
			clearTimeout( st_init_chatting ) ; st_init_chatting = undeefined ;
		}

		// only start chatting() if not operator... operators are started with requesting()
		if ( !isop )
			chatting() ;
	}
}

function queueing()
{
	var unique = unixtime() ;
	var json_data = new Object ;

	var this_rstring = "" ;
	for ( var this_opid in chats[ces]["q_opids"] )
	{
		if ( this_opid ) { this_rstring = this_opid+","+this_rstring ; }
	} rstring = this_rstring ;

	var minutes = Math.floor( ( c_queueing * parseInt( VARS_JS_REQUESTING ) )/60 ) ;
	if ( minutes >= parseInt( VARS_EXPIRED_QUEUE_IDLE ) )
	{
		leave_a_mesg(0, "") ;
		return false ;
	}

	$.ajax({
	type: "GET",
	url: base_url+"/ajax/chat_queueing.php",
	data: "&a=queueing&e="+embed+"&c="+ces+"&q="+queue+"&ql="+qlimit+"&d="+deptid+"&t="+phplive_browser_token+"&cq="+c_queueing+"&r="+rtype+"&rs="+rstring+"&"+unique,
	success: function(data){
		try {
			eval(data) ;
		} catch(err) {
			// suppress
		}

		if ( typeof( st_queueing ) != "undefined" )
		{
			clearTimeout( st_queueing ) ;
			st_queueing = undeefined ;
		}

		if ( json_data.status == 1 )
		{
			var total_ops_online = ( typeof( json_data.total_ops_online ) != "undefined" ) ? parseInt( json_data.total_ops_online ) : -1 ;

			if ( ( ( total_ops_online == -1 ) || total_ops_online ) && ( json_data.created != 615 ) )
			{
				process_queue( false, parseInt( json_data.qpos ), parseInt( json_data.est ), parseInt( json_data.created ) ) ;
				++c_queueing ;
				st_queueing = setTimeout( "queueing()" , VARS_JS_REQUESTING * 1000 ) ;
			}
			else
			{
				leave_a_mesg(0, "") ;
			}
		}
		else if ( json_data.status == 2 )
		{
			// operator is available
			if ( ces == json_data.ces ) { process_queue( json_data.ces, parseInt( json_data.qpos ), 0, parseInt( json_data.created ) ) ; }
			else
			{
				process_queue( false, parseInt( json_data.qpos ), 0, parseInt( json_data.created ) ) ;
				++c_queueing ;
				st_queueing = setTimeout( "queueing()" , VARS_JS_REQUESTING * 1000 ) ;
			}
		}
		else { do_alert( 0, json_data.error ) ; stopit(0) ; }
	},
	error:function (xhr, ajaxOptions, thrownError){
		if ( typeof( st_queueing ) != "undefined" )
		{
			clearTimeout( st_queueing ) ;
			st_queueing = undeefined ;
		}
		st_queueing = setTimeout( "queueing()" , VARS_JS_REQUESTING * 1000 ) ;
		++dc_c_queueing ;
		if ( dc_c_queueing > 1 ) { do_alert( 0, CHAT_ERROR_DC ) ; }
	} });
}

function routing( theopid )
{
	var unique = unixtime() ;
	var json_data = new Object ;

	$.ajax({
	type: "GET",
	url: base_url+"/ajax/chat_routing.php",
	data: "&a=routing&c="+ces+"&d="+deptid+"&r="+rtype+"&rt="+rtime+"&cr="+c_routing+"&rl="+rloop+"&l="+loop+"&lg="+lang+"&q="+queue+"&iq="+inqueue+"&o="+theopid+"&pr="+proto+"&"+unique,
	success: function(data){
		try {
			eval(data) ;
		} catch(err) {
			// suppress
		}

		if ( typeof( st_routing ) != "undefined" )
		{
			clearTimeout( st_routing ) ;
			st_routing = undeefined ;
		}

		if ( json_data.status == 1 )
			init_connect( json_data ) ;
		else if ( json_data.status == 2 )
		{
			// routed to new operator
			var opid = parseInt( json_data.opid ) ;

			if ( typeof( json_data.reset ) != "undefined" )
			{
				++loop ;
			}
			if ( typeof( json_data.rtime ) != "undefined" )
			{
				rtime = parseInt( json_data.rtime ) ;
			}
			++c_routing ;

			if ( opid ) { chats[ces]["q_opids"][opid] = 1 ; }
			st_routing = setTimeout( "routing(0)" , VARS_JS_REQUESTING * 1000 ) ;
		}
		else if ( json_data.status == 10 )
		{
			stopit(0) ;

			var q_ops = ( typeof( json_data.q_ops ) != "undefined" ) ? json_data.q_ops : "" ;
			leave_a_mesg(1, q_ops) ;
		}
		else if ( json_data.status == 12 )
		{
			stopit(0) ;

			leave_a_mesg(1, "") ;
		}
		else if ( json_data.status == 13 )
		{
			stopit(0) ;

			var q_ops = ( typeof( json_data.q_ops ) != "undefined" ) ? json_data.q_ops : "" ;
			leave_a_mesg(1, q_ops) ;
		}
		else if ( json_data.status == 11 )
		{
			stopit(0) ;

			var q_ops = ( typeof( json_data.q_ops ) != "undefined" ) ? json_data.q_ops : "" ;
			vclick = 2 ; // 2=flag not to store stats
			leave_a_mesg(1, q_ops) ;
		}
		else if ( json_data.status == 0 )
		{
			++c_routing ;
			st_routing = setTimeout( "routing(0)" , VARS_JS_REQUESTING * 1000 ) ;
		}
	},
	error:function (xhr, ajaxOptions, thrownError){
		if ( typeof( st_routing ) != "undefined" )
		{
			clearTimeout( st_routing ) ;
			st_routing = undeefined ;
		}
		st_routing = setTimeout( "routing(0)" , VARS_JS_REQUESTING * 1000 ) ;
	} });
}

function requesting()
{
	var start = microtime( true ) ;
	var unique = unixtime() ;
	var json_data = new Object ; c_chatting = c_requesting ;
	var chatting_query = get_chatting_query() ; if ( chatting_query ) { chatting_query = "&"+chatting_query ; }
	var q_ces = "" ;
	var addon_ws_q_ces = " \"cess\": [  " ;

	for ( var ces in chats )
	{
		q_ces += "qc[]="+ces+"&" ;
		if ( !chats[ces]["disconnected"] )
			addon_ws_q_ces += "{ \"c\": \""+ces+"\" }," ;
	}
	addon_ws_q_ces = addon_ws_q_ces.slice(0, -1) ;
	addon_ws_q_ces += " ] " ;

	if ( typeof( st_network ) != "undefined" ) { clearTimeout( st_network ) ; st_network = undeefined ; }
	if ( typeof( st_requesting ) != "undefined" ) { clearTimeout( st_requesting ) ; st_requesting = undeefined ; }

	if ( !reconnect )
	{ st_network = setTimeout( function(){ stopit(0) ; check_network( 715, undeefined, undeefined ) }, parseInt( VARS_JS_OP_CONSOLE_TIMEOUT ) * 1000 ) ; }
	else
	{ st_network = setTimeout( function(){ stopit(0) ; check_network( 717, undeefined, undeefined ) }, parseInt( VARS_JS_REQUESTING ) * 1000 ) ; }

	$.ajax({
	type: "GET",
	url: base_url+"/ajax/chat_op_requesting.php",
	data: "cs="+cs+"&m="+mapp+"&a=rq&st="+current_status+"&pr="+proto+"&tr="+traffic+"&cr="+c_requesting+"&qu="+depts_queue_enabled+"&"+q_ces+chatting_query+"&"+unique+"&ws="+addon_ws,
	success: function(data, textstatus, request){
		if ( typeof( st_network ) != "undefined" ) { clearTimeout( st_network ) ; st_network = undeefined ; }
		try {
			eval(data) ;
			++ping_counter_req ;
		} catch(err) {
			// most likely internet disconnect or server response error will cause console to disconnect automatically
			// suppress error and let the console reconnect
			if ( !reconnect )
			{
				check_network( 719, undeefined, err ) ;
				write_debug( "719: "+data, err ) ;
			}
			else
			{
				stopit(0) ;
				st_reconnect = setTimeout(function(){ check_network( 716, undeefined, undeefined ) ; }, 3000) ;
			} return false ;
		}

		if ( typeof( request.responseText.length ) != "undefined" )
			ping_total_bytes_received += parseInt( request.responseText.length ) ;

		// hide mapp spinner for visual indication of request process connected
		if ( $('#div_mapp_spinner').is(":visible") )
			$('#div_mapp_spinner').hide() ;

		chatting_err_815 = undeefined ;
		if ( !stopped || ( stopped && reconnect ) )
		{
			stopped = 0 ; // reset it for disconnect situation
			reconnect = 0 ;
			reconnect_success() ;

			// reset it here for network status
			unique = unixtime() ;

			if ( json_data.status == -1 )
			{
				dup = 1 ; // most likely another login at another location
				toggle_status( 3 ) ;
			}
			else if ( json_data.status )
			{
				var json_length = ( typeof( json_data.requests ) != "undefined" ) ? json_data.requests.length : 0 ;
				for ( var c = 0; c < json_length; ++c )
				{
					var thisces = json_data.requests[c]["ces"] ;
					var thisdeptid = json_data.requests[c]["did"] ;
					//var rupdated = ( typeof( depts_rtime_hash[thisdeptid] ) != "undefined" ) ? parseInt( json_data.requests[c]["vup"] ) + parseInt( depts_rtime_hash[thisdeptid] ) : unique ;
					// ( unique <= rupdated ) - need to plan further

					if ( json_data.requests[c]["op2op"] || ( typeof( op_depts_hash[thisdeptid] ) != "undefined" ) )
					{
						new_chat( json_data.requests[c], unique ) ;
					}
				}

				init_chat_list( unique ) ;
				update_traffic_counter( pad( json_data.traffics, 2 ) ) ;

				if ( typeof( st_requesting ) == "undefined" )
					st_requesting = setTimeout( "requesting()" , VARS_JS_REQUESTING * 1000 ) ;

				var end = microtime( true ) ;
				var diff = end - start ;

				check_network( diff, unique, json_data.pd ) ;

				// process chats, same as in chatting() function
				if ( addon_ws )
				{
					// send action to process DB updates so the connection does not timeout
					var ws_text = "{ \"o\": \""+isop+"\", \"a\": \"upv\", \"cr\": \""+c_requesting+"\", "+addon_ws_q_ces+" }" ;
					ws_init_message_send( ws_text ) ;
				}
				else
				{
					process_chat_messages( json_data.chats, json_data.istyping ) ;
				}
			}
			++c_requesting ;
		}
	},
	error:function (xhr, ajaxOptions, thrownError){
		$('#img_mapp_spinner').addClass('info_error') ; // visual indication network error

		if ( mapp && !$('#div_mapp_spinner').is(':visible') )
			$('#div_mapp_spinner').show().center() ;

		if ( typeof( chatting_err_815 ) == "undefined" )
		{
			chatting_err_815 = 1 ;
			update_network_log( "<tr id='div_network_his_"+network_counter+"' style='display: none'><td class='chat_info_td' colspan='3'>xhr: 815: "+xhr.status+"</td></tr>" ) ;
			setTimeout(function(){ requesting() ; }, 3000) ;
			write_debug( "815: "+xhr.status, "thrownError: "+xhr.responseText ) ;
		}
		else
		{
			// for Mobile Apps, some devices pauses network at pause/resume.  add some buffer so the disconnect message is only
			// displayed on actual network disconnect
			if ( mapp && chatting_err_815 && ( chatting_err_815 < 3 ) ) { ++chatting_err_815 ; }
			else
			{
				stopit(0) ;
				st_reconnect = setTimeout(function(){ check_network( 815+":"+xhr.status, undeefined, undeefined ) ; }, 3000) ;
			}
		}
	} });
}

function chatting()
{
	var json_data = new Object ;
	var chatting_query = get_chatting_query() ;

	if ( typeof( st_chatting ) != "undefined" )
	{
		clearTimeout( st_chatting ) ; st_chatting = undeefined ;
	}

	if ( addon_ws )
	{
		// keep the looping for fallback AJAX method
		if ( !isop && chatting_query )
		{
			var regex = /rq=(.*?)&/ ;
			var requestid = ( typeof( chatting_query.match( regex )[1] ) != "undefined" ) ? parseInt( chatting_query.match( regex )[1] ) : 0 ;

			regex = /mo=(.*?)&/ ;
			var mobile = ( typeof( chatting_query.match( regex )[1] ) != "undefined" ) ? parseInt( chatting_query.match( regex )[1] ) : 0 ;

			regex = /mp=(.*?)&/ ;
			var mapp = ( typeof( chatting_query.match( regex )[1] ) != "undefined" ) ? parseInt( chatting_query.match( regex )[1] ) : 0 ;

			regex = /o_=(.*?)&/ ;
			var isop_ = ( typeof( chatting_query.match( regex )[1] ) != "undefined" ) ? parseInt( chatting_query.match( regex )[1] ) : 0 ;

			if ( requestid )
			{
				var ws_text = "{ \"a\": \"upv\", \"cess\": [ { \"c\": \""+ces+"\" } ], \"o\": \""+isop+"\", \"o_\": \""+isop_+"\", \"rq\": \""+requestid+"\", \"mo\": \""+mobile+"\", \"mp\": \""+mapp+"\", \"ch\": \""+c_chatting+"\" }" ;
				ws_init_message_send( ws_text ) ;
			}
			if ( typeof( st_chatting ) == "undefined" )
				st_chatting = setTimeout( "chatting()" , VARS_JS_REQUESTING * 1000 ) ;
			++c_chatting ;
		}
	}
	else if ( chatting_query )
	{
		var unique = unixtime() ;

		$.ajax({
		type: "GET",
		url: base_url+"/ajax/chat_op_requesting.php",
		data: chatting_query+"&pr="+proto+"&"+unique+"&",
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				// if operator, the console will attempt to reconnect
				// if visitor, keep trying to send the data
				if ( !isop ) { visitor_reconnect() ; }
			}

			chatting_err_915 = undeefined ;
			if ( !stopped || ( stopped && reconnect ) )
			{
				stopped = 0 ; // reset it for disconnect situation
				reconnect = 0 ;

				if ( json_data.status )
				{
					// process chats
					process_chat_messages( json_data.chats, json_data.istyping ) ;

					// only apply to visitor... for operator requesting() calls it for disconnection detection
					if ( !isop )
					{
						if ( typeof( st_chatting ) == "undefined" )
							st_chatting = setTimeout( "chatting()" , VARS_JS_REQUESTING * 1000 ) ;
					}
				}
			}
			else
			{
				clearTimeout( st_chatting ) ; st_chatting = undeefined ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			if ( isop )
			{
				if ( typeof( chatting_err_915 ) == "undefined" )
				{
					chatting_err_915 = 1 ;
					update_network_log( "<tr id='div_network_his_"+network_counter+"' style='display: none'><td class='chat_info_td' colspan='3'>xhr: 915: "+xhr.status+"</td></tr>" ) ;
					setTimeout(function(){ chatting() ; }, 3000) ;
				}
				else
				{
					stopit(0) ;
					st_reconnect = setTimeout(function(){ check_network( 915+":"+xhr.status, undeefined, undeefined ) ; }, 1000) ;
				}
			}
			else { visitor_reconnect() ; }
		} });
		++c_chatting ;
	}
	else
	{
		if ( !isop ) { st_chatting = setTimeout( "chatting()" , VARS_JS_REQUESTING * 1000 ) ; }
	}
}

function get_chatting_query()
{
	var query = "" ;
	var start = 0 ;
	var q_ces = "" ;
	var q_chattings = "" ;
	var q_isop_ = "" ;
	var q_isop__ = "" ;

	for ( var this_ces in chats )
	{
		// only check chats that are in session...
		if ( ( ( chats[this_ces]["status"] == 1 ) || ( !isop && ( chats[this_ces]["status"] == 2 ) ) || chats[this_ces]["op2op"] || chats[this_ces]["initiated"] ) && !chats[this_ces]["disconnected"] && !chats[this_ces]["tooslow"] )
		{
			q_ces += "qcc[]="+this_ces+"&" ;
			q_chattings += "qch[]="+chats[this_ces]["chatting"]+"&" ;
			q_isop_ += "qo_[]="+chats[this_ces]["op2op"]+"&" ;
			q_isop__ += "qo__[]="+chats[this_ces]["opid"]+"&" ;
			start = 1 ;
		}
	}

	if ( start && ( typeof( ces ) != "undefined" ) && ( typeof( chats[ces] ) != "undefined" ) )
	{
		var requestid = chats[ces]["requestid"] ;
		var t_vses = chats[ces]["t_ses"] ;
		var this_mobile = ( typeof( mobile ) != "undefined" ) ? mobile : 0 ;
		var this_mapp = ( !isop ) ? chats[ces]["mapp"] : mapp ;

		query = "rq="+requestid+"&t="+t_vses+"&o="+isop+"&o_="+isop_+"&o__="+isop__+"&c="+ces+"&ch="+c_chatting+"&"+q_ces+q_chattings+q_isop_+q_isop__+"&mo="+this_mobile+"&mp="+this_mapp+"&" ;
	}
	return query ;
}

function process_chat_messages( thechat_sessions, theistyping )
{
	var thisces = ( typeof( ces ) != "undefined" ) ? ces : "" ;
	var json_length = ( typeof( thechat_sessions ) != "undefined" ) ? thechat_sessions.length : 0 ;
	for ( var c = 0; c < json_length; ++c )
		update_ces( thechat_sessions[c] ) ;

	init_chats() ;

	if ( typeof( chats[thisces] ) != "undefined" )
		chats[thisces]["istyping"] = theistyping ;
}

function reset_chatting()
{
	stopped = 0 ; reconnect = 0 ;
}

function restart_requesting()
{
	requesting() ;
}

function visitor_reconnect()
{
	// keep trying to reconnect the chat engine
	// todo: maximum number of attempts before final disconnect
	if ( typeof( st_chatting ) != "undefined" )
	{
		clearTimeout( st_chatting ) ;
		st_chatting = undeefined ;
	}
	st_chatting = setTimeout( "chatting()" , VARS_JS_REQUESTING * 1000 ) ;
}

function stopit( thereconnect )
{
	reconnect = thereconnect ;
	clear_timeouts() ;
	if ( !isop ) { disconnect_complete() ; }
}

function clear_timeouts()
{
	if ( typeof( st_routing ) != "undefined" ) { clearTimeout( st_routing ) ; st_routing = undeefined ; }
	if ( typeof( st_chatting ) != "undefined" ) { clearTimeout( st_chatting ) ; st_chatting = undeefined ; }
	if ( typeof( st_requesting ) != "undefined" ) { clearTimeout( st_requesting ) ; st_requesting = undeefined ; }
	if ( typeof( st_queueing ) != "undefined" ) { clearTimeout( st_queueing ) ; st_queueing = undeefined ; }
	if ( typeof( st_network ) != "undefined" ) { clearTimeout( st_network ) ; st_network = undeefined ; }
	if ( typeof( st_reconnect ) != "undefined" ) { clearTimeout( st_reconnect ) ; st_reconnect = undeefined ; }
	stopped = 1 ;
}
/***********************/
// END chat engine
/***********************/

String.prototype.extract_youtube = function(){
	var string = this ;

	if ( browser_filter )
	{
		if ( mobile && isop )
		{
			// need to update few areas on the Mobile App before enable
		}
		else
		{
			var temp = string ;
			while ( temp.match( /<a href='((http:|https:|)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?)'/i ) )
			{
				var id = "href_" + unixtime() ;
				var href = RegExp.$1 ;

				var document_width = $('#chat_body').width() ;
				var document_height = $('#chat_body').height() ;

				var video_height = ( document_width > document_height ) ? document_height : document_width ;
				video_height = ( video_height < 300 ) ? 300 : video_height - 25 ;

				var youtube_embed_output = createVideo( href, "100%", video_height ) ;
				if ( typeof( youtube_embed_output ) != "boolean" )
				{
					temp = temp.replace( /<a href='(http:|https:|)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?' target='_blank' rel='noopener noreferrer'>(http:|https:|)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?<\/a>/i, youtube_embed_output.get(0).outerHTML ) ;
					string = temp ;
				}
			}
		}
	}
	return string ;
};
