<?php
	if ( defined( 'API_Util_Email_SMTP' ) ) { return ; }	
	define( 'API_Util_Email_SMTP', true ) ;

	function Util_Email_SMTP_SwiftMailer( $to, $to_name, $from, $from_name, $subject, $body, $attachment_file, $bcc = Array() ) {
		global $CONF ;
		global $smtp_array ;

		if ( isset( $smtp_array ) && isset( $smtp_array["host"] ) )
		{
			$CONF["SMTP_HOST"] = $smtp_array["host"] ;
			$CONF["SMTP_LOGIN"] = $smtp_array["login"] ;
			$CONF["SMTP_PASS"] = $smtp_array["pass"] ;
			$CONF["SMTP_PORT"] = $smtp_array["port"] ;
			$CONF["SMTP_CRYPT"] = ( isset( $smtp_array["crypt"] ) ) ? $smtp_array["crypt"] : "" ;
			$CONF["SMTP_API"] = isset( $smtp_array["api"] ) ? $smtp_array["api"] : "" ;
			$CONF["SMTP_DOMAIN"] = isset( $smtp_array["domain"] ) ? $smtp_array["domain"] : "" ;
		}
		else if ( !isset( $CONF["SMTP_PASS"] ) && is_file( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/Util_Extra.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/Util_Extra.php" ) ; }

		if ( !isset( $CONF["SMTP_PASS"] ) ) { return "Missing SMTP variables." ; }
		else if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/swift/swift_required.php" ) )
		{
			if ( isset( $CONF["SMTP_API"] ) && $CONF["SMTP_API"] && !$CONF["SMTP_HOST"] && is_file( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/api_$CONF[SMTP_API].php" ) )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/api_$CONF[SMTP_API].php" ) ;

				$error = SMTP_API_Send( $to, $to_name, $from, $from_name, $subject, $body ) ;
				return $error ;
			}
			else
			{
				include_once( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/swift/swift_required.php" ) ;

				$transport = Swift_SmtpTransport::newInstance($CONF["SMTP_HOST"], $CONF["SMTP_PORT"]) ;
				if ( ( $CONF["SMTP_LOGIN"] != "blank" ) || ( $CONF["SMTP_PASS"] != "blank" ) )
				{
					$transport->setUsername($CONF["SMTP_LOGIN"]) ;
					$transport->setPassword($CONF["SMTP_PASS"]) ;
				}
				if ( isset( $CONF["SMTP_CRYPT"] ) && $CONF["SMTP_CRYPT"] && ( $CONF["SMTP_CRYPT"] != "null" ) ) { $transport->setEncryption($CONF["SMTP_CRYPT"]) ; }
				else if ( $CONF["SMTP_PORT"] == 465 ) { $transport->setEncryption('ssl') ; }

				/*****************************************************
				/*
				/* Remove <br> to prevent double break in HTML email
				/*
				*****************************************************/
				$body_html = nl2br( preg_replace( "/<br>/i", "", $body ) ) ;
				$body_html = preg_replace( '/((http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?)/', '<a href="\1">\1</a>', $body_html ) ;
				$body_plain = strip_tags( $body ) ;

				$mailer = Swift_Mailer::newInstance($transport) ;

				$message = Swift_Message::newInstance( $subject )
				->setFrom( array($from => $from_name) )
				->setTo( array($to => $to_name) )
				->setReplyTo( array($from => $from_name) ) ;
				for( $c = 0; $c < count( $bcc ); ++$c ) { $message->addBcc( $bcc[$c] ) ; }
				$message->addPart( $body_plain, 'text/plain' ) ;
				$message->addPart( $body_html, 'text/html' ) ;
				if ( $attachment_file && is_file( "$CONF[ATTACH_DIR]/$attachment_file" ) ) { $message->attach(Swift_Attachment::fromPath("$CONF[ATTACH_DIR]/$attachment_file")) ; }

				try{
					$mailer->send($message) ;
					return "NONE" ;
				} catch (Exception $e) {
					$debug_file = "debug_smtp_".md5($CONF['API_KEY']).".txt" ;
					Util_Format_DEBUG( $e."\r\n---------- TRACE END ----------\r\n", $debug_file ) ;

					$error = "SMTP information is invalid.  Double check the values and try again. [e1]" ;
					if ( preg_match( "/ssl/i", $e ) )
						$error = "OpenSSL is not enabled on this server.  Enable the PHP OpenSSL extension and try again." ;
					else if ( preg_match( "/(host has failed to respond)/i", $e ) )
						$error = "SMTP host did not respond.  Check the server firewall settings as it may be blocking the SMTP port or the SMTP port is invalid." ;
					else if ( preg_match( "/(php_network_getaddresses)/i", $e ) )
						$error = "SMTP host is invalid." ;
					else if ( preg_match( "/(relay denied)/i", $e ) )
						$error = "Could not connect to SMTP host due to 'relay denied'.  The SMTP host may require an IP address or a domain name of the connecting server.  For PHP Live! trial accounts, the connecting IP address is not available." ;
					else if ( preg_match( "/(address is not verified)/i", $e ) )
						$error = "Login to your SES console and <a href='https://console.aws.amazon.com/ses/home?region=us-west-2#verified-senders-email:' target='_blank' style='color: #FFFFFF;'>verify the department email address</a> $from or the domain name.  Another possible reason is the SES account is not in <a href='http://docs.aws.amazon.com/ses/latest/DeveloperGuide/request-production-access.html' target='_blank' style='color: #FFFFFF;'>Production Mode</a>.  Also, make sure you are using the <a href='https://console.aws.amazon.com/iam/home?#/s=SESHome' target='_blank' style='color: #FFFFFF;'>IAM login credentials</a>.  Double check these possible areas and try again." ;
					else if ( preg_match( "/(permission denied)/i", $e ) )
						$error = "SMTP port permission denied error. Be sure the server outbound port $CONF[SMTP_PORT] is open." ;
					else if ( preg_match( "/(timed out)/i", $e ) )
						$error = "Could not establish connection to the SMTP server.  Perhaps the server firewall is restricting access to external domains.  Please contact your tech or hosting company or double check the SMTP values and try again." ;
					else if ( preg_match( "/(possible authenticators)|(Connection could not be established with host)/i", $e ) )
						$error = "Possible SMTP encryption type error.  Try another ecryption type or set the value to none.  If utilizing Gmail SMTP, be sure to provide the application specific password and set the Encryption to TLS." ;
					else if ( preg_match( "/Must issue a STARTTLS command first/i", $e ) )
						$error = "Possible SMTP encryption type error.  Try another ecryption type.  If utilizing Gmail SMTP, be sure to provide the application specific password and set the Encryption to TLS." ;
					else if ( preg_match( "/(expected response code 250)|(failed to authenticate)/i", $e ) )
					{
						$e = Util_Format_StripQuotes( $e ) ;
						if ( preg_match( "/failed to authenticate/i", $e ) )
							$error = "SMTP login or password is incorrect." ;
						else if ( preg_match( "/response code 250 but got code , with message/i", $e ) )
							$error = "SMTP login or password is incorrect.  For SendGrid API Key method, try using 'apikey' as the login." ;
						else
						{
							$error_lines = explode( "\n", $e ) ;
							$error_message = isset( $error_lines[0] ) ? preg_replace( "/ in (.*)$/i", "", $error_lines[0] ) : "SMTP login or password is incorrect or SMTP server configuration has denied sending of the email." ;
							$error = "<b>Message from the SMTP server:</b><br>" . Util_Format_Trim( $error_message ) ;
						}
					}
					else if ( preg_match( "/(refused)/i", $e ) )
					{
						$error = "SMTP host or port is invalid.  Double check the SMTP values and try again.  If the issue persists, check that the outbound port $CONF[SMTP_PORT] for your server is open." ;
						if ( function_exists( fsockopen ) )
						{
							$fp = fsockopen('localhost', $CONF["SMTP_PORT"], $errno, $errstr, 10);
							if ( !$fp ) { $error = "SMTP port is invalid or the outbound port $CONF[SMTP_PORT] for your server is closed." ; }
							else { fclose($fp); }
						}
					}
					else if ( preg_match( "/(address in mailbox given)/i", $e ) )
						$error = "Email Address is invalid [to->$to, from->$from]" ;
					return $error ;
				}
			}
		}
		else
			return "SMTP addon lib not found. Try reinstalling the SMTP addon. [e2]" ;
	}
?>