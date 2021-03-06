<?php
	if ( $icon_text == "online" )
	{
		$icon_text_outer = "#4384F5" ;
		$icon_text_inner = "#4384F5" ;
		$icon_text_dots = "#FFFFFF" ;
		$text = "Click for live chat." ;

		$svg_icon = $svg_icons_online ;
		$text_icon = $text_icons_online ;
		if ( isset( $text_icon[0] ) && preg_match( "/^<span /i", $text_icon[5] ) )
		{
			$icon_text_outer = $text_icon[2] ;
			$icon_text_inner = $text_icon[3] ;
			$icon_text_dots = $text_icon[4] ;
			$text = $text_icon[6] ;
		}
	}
	else
	{
		$icon_text_outer = "#3F4D5F" ;
		$icon_text_inner = "#3F4D5F" ;
		$icon_text_dots = "#FFFFFF" ;
		$text = "Send email." ;

		$svg_icon = $svg_icons_offline ;
		$text_icon = $text_icons_offline ;
		if ( isset( $text_icon[0] ) && preg_match( "/^<span /i", $text_icon[5] ) )
		{
			$icon_text_outer = $text_icon[2] ;
			$icon_text_inner = $text_icon[3] ;
			$icon_text_dots = $text_icon[4] ;
			$text = $text_icon[6] ;
		}
	}
?>
<div id="div_<?php echo $icon_text ?>_icon_text" style="display: none; margin-top: 35px;">
	<div style="margin-top: 5px; text-shadow: none;">
		<div class="info_good" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('<?php echo $icon_text ?>', 'text', 1)"><input type="radio" name="text_<?php echo $icon_text ?>_status" id="text_<?php echo $icon_text ?>_status_text" value="1"> Enable.  Use Text</div>
		<div class="info_neutral" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('<?php echo $icon_text ?>', 'image', 1)"><input type="radio" name="text_<?php echo $icon_text ?>_status" id="text_<?php echo $icon_text ?>_status_image" value="0"> Disable.  Use Image</div>
		<div class="info_neutral" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('<?php echo $icon_text ?>', 'svg', 1)"><input type="radio" name="text_<?php echo $icon_text ?>_status" id="text_<?php echo $icon_text ?>_status_svg" value="0"> Disable.  Use SVG</div>
		<div style="clear: both;"></div>
	</div>
	<div style="margin-top: 15px;">

		<div id="text_<?php echo $icon_svg ?>_image">
		</div>
		<div style="margin-top: 15px;">
			<input type="text" class="input" id="input_text_<?php echo $icon_text ?>" size="20" maxlength="160" value="<?php echo $text ?>" onKeyPress="return noquotes(event)" onKeyUp="return display_text(event, '<?php echo $icon_text ?>')" autocomplete="off">
		</div>

		<div style="margin-top: 25px;">
			<table cellspacing=0 cellpadding=2 border=0>
			<tr>
				<td width="80">
					Border<br>
					<input id="text_<?php echo $icon_text ?>_outer" class="text_<?php echo $icon_text ?>_outer" value="<?php echo $icon_text_outer ?>">
				</td>
				<td width="80">
					Background<br>
					<input id="text_<?php echo $icon_text ?>_inner" class="text_<?php echo $icon_text ?>_inner" value="<?php echo $icon_text_inner ?>">
				</td>
				<td>
					Text Color<br>
					<input id="text_<?php echo $icon_text ?>_dots" class="text_<?php echo $icon_text ?>_dots" value="<?php echo $icon_text_dots ?>">
				</td>
			</tr>
			</table>

			<div style="margin-top: 25px;"><button type="button" class="btn" onClick="svg_submit('<?php echo $icon_text ?>', 'text')" id="btn_svg_<?php echo $icon_text ?>">Save Changes and Use Text</button> &nbsp; &nbsp; <span id="span_<?php echo $icon_text ?>_text_cancel" style="display: none;"><a href="JavaScript:void(0)" onClick="text_cancel('<?php echo $icon_text ?>')">cancel</a></span></div>
		</div>

	</div>
</div>