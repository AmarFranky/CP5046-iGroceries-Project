<?php
	include_once( "$CONF[DOCUMENT_ROOT]/addons/API/Util_Addons.php" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$icon = Util_Format_Sanatize( Util_Format_GetVar( "icon" ), "ln" ) ;
	$outer = Util_Format_Sanatize( Util_Format_GetVar( "o" ), "ln" ) ;
	$inner = Util_Format_Sanatize( Util_Format_GetVar( "i" ), "ln" ) ;
	$dots = Util_Format_Sanatize( Util_Format_GetVar( "d" ), "ln" ) ;
	$type = Util_Format_Sanatize( Util_Format_GetVar( "t" ), "ln" ) ;
	$text = Util_Format_StripQuotes( Util_Format_Sanatize( Util_Format_GetVar( "text" ), "noscripts" ) ) ;

	if ( ( $icon == "online" ) || ( $icon == "offline" ) )
	{
		if ( is_file( "$CONF[CONF_ROOT]/addons.php" ) ) { include_once( "$CONF[CONF_ROOT]/addons.php" ) ; }
		$svg_icons = ( isset( $VALS_ADDONS ) && isset( $VALS_ADDONS["svg_icons"] ) ) ? unserialize( base64_decode( $VALS_ADDONS["svg_icons"] ) ) : Array() ;

		if ( !isset( $svg_icons[$deptid] ) )
			$svg_icons[$deptid] = Array() ;

		if ( $icon == "online" )
		{
			if ( $type == "svg" )
				$svg_icon = '<svg id="svg_online_'.$deptid.'" style="filter: drop-shadow(2px 2px 5px rgba(0,0,0,0.2)) !important;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" viewBox="0 0 640 640" width="60" height="60"><defs><path d="M628.58 320C628.58 490.64 490.31 629.18 320 629.18C149.69 629.18 11.42 490.64 11.42 320C11.42 149.36 149.69 10.82 320 10.82C490.31 10.82 628.58 149.36 628.58 320Z" id="i8VVhIQhAy"></path><path d="M154.39 327.31C145.47 242.82 213.28 166.39 305.86 156.61C398.44 146.83 480.72 207.38 489.65 291.88C498.58 376.38 430.78 452.8 338.2 462.58C315.71 464.96 293.83 463.18 273.46 457.94C269.61 461.08 250.36 476.82 215.72 505.14L217.78 432.34C178.58 391.28 157.45 356.27 154.39 327.31Z" id="c2VPbzTLxw"></path><path d="M259.47 304.13C259.47 316.88 249.12 327.24 236.37 327.24C223.62 327.24 213.27 316.88 213.27 304.13C213.27 291.38 223.62 281.03 236.37 281.03C249.12 281.03 259.47 291.38 259.47 304.13Z" id="bpomRosYj"></path><path d="M345.13 304.13C345.13 316.88 334.77 327.24 322.02 327.24C309.27 327.24 298.92 316.88 298.92 304.13C298.92 291.38 309.27 281.03 322.02 281.03C334.77 281.03 345.13 291.38 345.13 304.13Z" id="cS831ETxc"></path><path d="M431.94 304.13C431.94 316.88 421.59 327.24 408.84 327.24C396.09 327.24 385.73 316.88 385.73 304.13C385.73 291.38 396.09 281.03 408.84 281.03C421.59 281.03 431.94 291.38 431.94 304.13Z" id="a1W8EpizTc"></path></defs><g><g><g><use xlink:href="#i8VVhIQhAy" opacity="1" fill="'.$outer.'" fill-opacity="1"></use></g><g><use xlink:href="#c2VPbzTLxw" opacity="1" fill="'.$inner.'" fill-opacity="1"></use></g><g><use xlink:href="#bpomRosYj" opacity="1" fill="'.$dots.'" fill-opacity="1"></use></g><g><use xlink:href="#cS831ETxc" opacity="1" fill="'.$dots.'" fill-opacity="1"></use></g><g><use xlink:href="#a1W8EpizTc" opacity="1" fill="'.$dots.'" fill-opacity="1"></use></g></g></g></svg>' ;
			else if ( $type == "text" )
				$svg_icon = '<span style="padding: 15px !important; text-decoration: none !important; display: inline-block !important; background: '.$inner.' !important; color: '.$dots.' !important; border: 1px solid '.$outer.' !important; -moz-border-radius: 10px !important; border-radius: 10px !important; -webkit-box-shadow: 2px 2px 5px rgba(0,0,0,0.2) !important; -moz-box-shadow: 2px 2px 5px rgba(0,0,0,0.2) !important; 2px 2px 5px rgba(0,0,0,0.2) !important;" id="phplive_text"><table cellspacing=0 cellpadding=2 border=0><tr><td><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" viewBox="0 0 640 640" width="20" height="20"><defs><path d="M51.72 294.49C51.72 159.02 172.06 49.19 320.51 49.19C468.95 49.19 589.29 159 589.29 294.49C589.29 429.98 468.97 539.81 320.52 539.81C284.46 539.81 250.05 533.32 218.63 521.59C212 525.93 178.85 547.66 119.16 586.77L134.62 471.67C79.35 399.99 51.72 340.93 51.72 294.49Z" id="biq1Fbxfy"></path></defs><g><g><g><g><filter id="shadow11189699" x="42.72" y="40.19" width="556.58" height="556.58" filterUnits="userSpaceOnUse" primitiveUnits="userSpaceOnUse"><feFlood></feFlood><feComposite in2="SourceAlpha" operator="in"></feComposite></filter><path d="M51.72 294.49C51.72 159.02 172.06 49.19 320.51 49.19C468.95 49.19 589.29 159 589.29 294.49C589.29 429.98 468.97 539.81 320.52 539.81C284.46 539.81 250.05 533.32 218.63 521.59C212 525.93 178.85 547.66 119.16 586.77L134.62 471.67C79.35 399.99 51.72 340.93 51.72 294.49Z" id="b17d8ou18z" fill="white" fill-opacity="1" filter="url(#shadow11189699)"></path></g><use xlink:href="#biq1Fbxfy" opacity="1" fill="'.$dots.'" fill-opacity="1"></use></g></g></g></svg></td><td>'.$text.'</td></tr></table></span>' ;
		}
		else
		{
			if ( $type == "svg" )
				$svg_icon = '<svg id="svg_offline_'.$deptid.'" style="filter: drop-shadow(2px 2px 5px rgba(0,0,0,0.2)) !important;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" viewBox="0 0 640 640" width="60" height="60"><defs><path d="M628.58 320C628.58 490.64 490.31 629.18 320 629.18C149.69 629.18 11.42 490.64 11.42 320C11.42 149.36 149.69 10.82 320 10.82C490.31 10.82 628.58 149.36 628.58 320Z" id="akAxA96t8"></path><path d="M154.39 327.31C145.47 242.82 213.28 166.39 305.86 156.61C398.44 146.83 480.72 207.38 489.65 291.88C498.58 376.38 430.78 452.8 338.2 462.58C315.71 464.96 293.83 463.18 273.46 457.94C269.61 461.08 250.36 476.82 215.72 505.14L217.78 432.34C178.58 391.28 157.45 356.27 154.39 327.31Z" id="b84wtcrNnc"></path><path d="M247.91 360.53L247.91 250.94L398.14 250.94L398.14 360.53L247.91 360.53ZM322.07 324L262 279.02L262 345.92L384.05 345.92L384.05 277.3L322.26 323.57L322.07 324ZM321.88 302.77L371.58 265.55L272.18 265.55L321.88 302.77Z" id="a4ikl1BvW"></path></defs><g><g><g><use xlink:href="#akAxA96t8" opacity="1" fill="'.$outer.'" fill-opacity="1"></use></g><g><use xlink:href="#b84wtcrNnc" opacity="1" fill="'.$inner.'" fill-opacity="1"></use></g><g><use xlink:href="#a4ikl1BvW" opacity="1" fill="'.$dots.'" fill-opacity="1"></use></g></g></g></svg>' ;
			else if ( $type == "text" )
				$svg_icon = '<span style="padding: 15px !important; text-decoration: none !important; display: inline-block !important; background: '.$inner.' !important; color: '.$dots.' !important; border: 1px solid '.$outer.' !important; -moz-border-radius: 10px !important; border-radius: 10px !important; -webkit-box-shadow: 2px 2px 5px rgba(0,0,0,0.2) !important; -moz-box-shadow: 2px 2px 5px rgba(0,0,0,0.2) !important; box-shadow: 2px 2px 5px rgba(0,0,0,0.2) !important;" id="phplive_text"><table cellspacing=0 cellpadding=2 border=0><tr><td><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" viewBox="0 0 640 640" width="20" height="20"><defs><path d="M51.21 554.44L51.21 71.41L588.79 71.41L588.79 554.44L51.21 554.44ZM316.58 393.43L101.61 195.17L101.61 490.04L538.39 490.04L538.39 187.61L317.27 391.55L316.58 393.43ZM315.9 299.85L493.76 135.82L138.04 135.82L315.9 299.85Z" id="c13HOhMVvH"></path></defs><g><g><g><g><filter id="shadow2371485" x="42.21" y="62.41" width="556.58" height="502.03" filterUnits="userSpaceOnUse" primitiveUnits="userSpaceOnUse"><feFlood></feFlood><feComposite in2="SourceAlpha" operator="in"></feComposite></filter><path d="M51.21 554.44L51.21 71.41L588.79 71.41L588.79 554.44L51.21 554.44ZM316.58 393.43L101.61 195.17L101.61 490.04L538.39 490.04L538.39 187.61L317.27 391.55L316.58 393.43ZM315.9 299.85L493.76 135.82L138.04 135.82L315.9 299.85Z" id="aQ6F24gjr" fill="white" fill-opacity="1" filter="url(#shadow2371485)"></path></g><use xlink:href="#c13HOhMVvH" opacity="1" fill="'.$dots.'" fill-opacity="1"></use></g></g></g></svg></td><td>'.$text.'</td></tr></table></span>' ;
		}

		// first 1 = status always enabled due to new text type added.  no more retaining values when changed
		// second 1 = placeholder for the SVG image ID of pre-packaged SVG images
		$svg_icons[$deptid][$icon] = Array( 1, 1, $outer, $inner, $dots, $svg_icon, $text ) ;

		if ( Util_Addons_WriteToFile( "svg_icons", base64_encode( serialize( $svg_icons ) ) ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Error writing SVG data to the addons file.\" };" ;
	}
	else
		$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid action. [svg]\" };" ;
?>
