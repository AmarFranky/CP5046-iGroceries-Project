// variable to prevent posting to another chat session
var past_upload_processing = 0 ;

function paste_upload_init(e)
{
	if ( typeof( e.clipboardData ) != "undefined" )
	{
		for ( var c = 0 ; c < e.clipboardData.items.length ; c++ )
		{
			var item = e.clipboardData.items[c] ;

			if ( item.type.indexOf("image") == 0 )
			{
				if ( ( typeof( chats[ces] ) == "undefined" ) || !chats[ces]["status"] || chats[ces]["disconnected"] )
				{
					if ( isop )
						do_alert( 0, "A chat session must be active." ) ;

					return false ;
				}
				past_upload_processing = 1 ;
				$('#chat_processing').show() ;
				paste_upload_doit( item.getAsFile() ) ;
			}
			else
			{
				// not an image file.
			}
		}
	}
}

function paste_upload_doit( thedata )
{
	var xhr = new XMLHttpRequest() ;
	var filetype = thedata.type ; filetype = filetype.replace( /image\//, "" ) ;
	var filename = randomstring(6) + "." + filetype ;

	xhr.upload.onprogress = function(e)
	{
		var percent_uploaded = ( e.loaded / e.total ) * 100 ;
		//console.log( "File upload: "+ percent_uploaded + "%" ) ;
	};

	xhr.onload = function()
	{
		if ( xhr.status == 200 )
		{
			var base_url_image = base_url_full+"/web/file_attach/"+filename ;
			if ( conf_extend ) { base_url_image = base_url_full+"/web/"+conf_extend+"/file_attach/"+filename ; }

			upload_success( base_url_image, filename ) ;
		}
		else { do_alert( 0, "Error uploading clipboard image." ) ; }

		$('#chat_processing').fadeOut("fast") ;
		past_upload_processing = 0 ;
	};

	xhr.onerror = function()
	{
		$('#chat_processing').fadeOut("fast") ;
		past_upload_processing = 0 ;

		do_alert( 0, "Could not connect to server.  Please refresh the console and try again." ) ;
	};

	xhr.open( "POST", base_url+"/ajax/paste_upload.php?ces="+ces+"&filename="+filename, true ) ;
	xhr.setRequestHeader( "Content-Type", thedata.type ) ;
	xhr.send( thedata ) ;
}