<?php
	if ( $icon_svg == "online" )
	{
		$icon_svg_outer = "#4384f5" ;
		$icon_svg_inner = "#FFFFFF" ;
		$icon_svg_dots = "#4384f5" ;

		$text_icon = $text_icons_online ;
		$svg_icon = $svg_icons_online ;
		if ( isset( $svg_icon[0] ) )
		{
			$icon_svg_outer = $svg_icons_online[2] ;
			$icon_svg_inner = $svg_icons_online[3] ;
			$icon_svg_dots = $svg_icons_online[4] ;
		}
	}
	else
	{
		$icon_svg_outer = "#3F4D5F" ;
		$icon_svg_inner = "#FFFFFF" ;
		$icon_svg_dots = "#3F4D5F" ;

		$text_icon = $text_icons_offline ;
		$svg_icon = $svg_icons_offline ;
		if ( isset( $svg_icon[0] ) )
		{
			$icon_svg_outer = $svg_icons_offline[2] ;
			$icon_svg_inner = $svg_icons_offline[3] ;
			$icon_svg_dots = $svg_icons_offline[4] ;
		}
	}
?>
<div id="div_<?php echo $icon_svg ?>_icon_svg" style="display: none; margin-top: 35px;">
	<div style="margin-top: 5px; text-shadow: none;">
		<div class="info_good" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('<?php echo $icon_svg ?>', 'svg', 1)"><input type="radio" name="svg_<?php echo $icon_svg ?>_status" id="svg_<?php echo $icon_svg ?>_status_svg" value="1"> Enable.  Use SVG</div>
		<div class="info_neutral" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('<?php echo $icon_svg ?>', 'image', 1)"><input type="radio" name="svg_<?php echo $icon_svg ?>_status" id="svg_<?php echo $icon_svg ?>_status_image" value="0"> Disable.  Use Image</div>
		<div class="info_neutral" style="float: left; cursor: pointer; text-shadow: none; padding: 3px; margin-right: 10px;" onclick="svg_submit('<?php echo $icon_svg ?>', 'text', 1)"><input type="radio" name="svg_<?php echo $icon_svg ?>_status" id="svg_<?php echo $icon_svg ?>_status_text" value="0"> Disable.  Use Text</div>
		<div style="clear: both;"></div>
	</div>
	<div style="margin-top: 15px;">
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td valign="top">
				<div id="svg_<?php echo $icon_svg ?>_image"></div>
			</td>
			<td valign="top" style="padding-left: 25px;">
				<table cellspacing=0 cellpadding=2 border=0>
				<tr>
					<td width="80">
						Outer<br>
						<input id="svg_<?php echo $icon_svg ?>_outer" class="svg_<?php echo $icon_svg ?>_outer" value="<?php echo $icon_svg_outer ?>">
					</td>
					<td width="80">
						Inner<br>
						<input id="svg_<?php echo $icon_svg ?>_inner" class="svg_<?php echo $icon_svg ?>_inner" value="<?php echo $icon_svg_inner ?>">
					</td>
					<td>
						Inner 2<br>
						<input id="svg_<?php echo $icon_svg ?>_dots" class="svg_<?php echo $icon_svg ?>_dots" value="<?php echo $icon_svg_dots ?>">
					</td>
				</tr>
				</table>

				<div style="margin-top: 25px;"><button type="button" class="btn" onClick="svg_submit('<?php echo $icon_svg ?>', 'svg')" id="btn_svg_<?php echo $icon_svg ?>">Save Changes and Use SVG</button> &nbsp; &nbsp; <span id="span_<?php echo $icon_svg ?>_cancel" style="display: none;"><a href="JavaScript:void(0)" onClick="svg_cancel('<?php echo $icon_svg ?>')">cancel</a></span></div>
			</td>
		</tr>
		</table>
	</div>
</div>