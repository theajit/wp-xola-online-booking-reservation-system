<div class="wrap">
	<div id="xola_step_2">
			<h2 class="xola_btn_hd"><?php _e('Choose your button format', Xolaecommerce::TEXT_DOMAIN); ?></h2>
		<form action="" method="post">
			<?php 
					wp_nonce_field('select_xola_button', 'select_xola_button_submitted'); 
					$button = get_option('xola_button_type');
					$html = stripslashes(get_option('xola_custom_html'));
			?>
			<div class="right_xola">
				<div class="xola_field">
					<input type="radio" id="default" name="xola_button" value="default" <?php if($button == 'default'){ echo 'checked';} ?>>
					<label for="default"><?php _e('Default', Xolaecommerce::TEXT_DOMAIN); ?></label>
				</div>
				<div class="xola_field">
					<input type="radio" id="custom" name="xola_button" value="custom" <?php if($button == 'custom'){ echo 'checked';} ?>>
					<label for="custom"><?php _e('Custom', Xolaecommerce::TEXT_DOMAIN); ?></label>
				</div>
			</div>
			<div class="left_xola">
				<div class="left_default" <?php if($button == 'default'){ echo 'id ="xola_checked"';} ?>>
					<span><?php _e('Preview:', Xolaecommerce::TEXT_DOMAIN); ?></span>
					<div class="xola_btn"></div>
				</div>
				<div class="left_custom" <?php if($button == 'custom'){ echo 'id ="xola_checked"';} ?>>
					<span><?php _e('Customize Xola button styling to match your site theme by adding HTML here. For sample styles visit <a href="http://cssgradientbutton.com" target="_blank">cssgradientbutton.com</a>', Xolaecommerce::TEXT_DOMAIN); ?> </span>
					<textarea id="custom_html" name="xola_custom_html"><?php echo @$html;?></textarea>
				</div>
			</div>
			<div class="xola_submit">
				<button class="xola_done" type="submit" onclick="return check_validate();"><?php _e('Submit', Xolaecommerce::TEXT_DOMAIN); ?></button>
			</div>
		</form>
	</div>
</div>
<script>
jQuery('input[name=xola_button]').click(function(){
		  var checktype = jQuery(this).val();
			if(checktype == 'custom'){
					jQuery('.left_custom').slideDown(500);
					jQuery('.left_default').hide();
				}else{
					jQuery('.left_custom').hide();
					jQuery('.left_default').slideDown(500);
				}
		 });
function check_validate(){
	var check = jQuery('input[name=xola_button]:checked').val();
		var textarea = jQuery('#custom_html').val();
			if(check == 'custom'){
				if(jQuery.trim(textarea) == ''){
					jQuery('#custom_html').css('border','1px solid red');
						return false;
												}
					 else{ 
						return true;
						 }	
								}
				else{
						return true;
					}
						}		 
</script>
