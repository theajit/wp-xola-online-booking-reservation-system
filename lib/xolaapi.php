<div class="wrap">
<script>
function check_valid(){
var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
var email = jQuery('#xola_email').val();
    if (!emailReg.test(email) || jQuery.trim(email) =='') {
    jQuery('#xola_email').css('border','1px solid red');
        return false;    
    } else {
       jQuery('#xola_email').css('border','1px solid #ddd');  
    }

}
</script>
<h2 class="xola_setting"><?php _e('Xola Settings', Xolaecommerce::TEXT_DOMAIN); ?></h2>

<form action="" method="post" id="xola_form">
<table class="form-table">
<?php  wp_nonce_field('enable_xola', 'enable_xola_submitted');
$email = get_option('xola_user_email');
if($email){$value = 'Update Xola Email';}else{ $value = 'Sync Xola Account';}
 ?>
<tbody>
<tr valign="top">
<th scope="row">
<label for="xola_email"><?php _e('Enter Xola Email ', Xolaecommerce::TEXT_DOMAIN); ?></label>
</th>
<td>
<input id="xola_email" class="regular-text" type="text"  name="xola_email" value="<?php echo @$email;?>" >
</td>
</tr>
</tbody>
</table>
<p class="submit">
<input id="submit" class="button button-primary" type="submit" value="<?php echo $value;?>" name="xola_submit" >
</p>
</form>
<?php
if($email){
?>
<h2 style="clear:both"><?php _e('Xola Listings', Xolaecommerce::TEXT_DOMAIN); ?></h2>
<?php
$data_req = Xolaecommerce::xola_api_data($email);
	if($data_req){
	echo '<ul id="image_xola">';
	foreach($data_req['data'] as $data){
	
	echo '<li>';
	if($data['photo']['src']){
	echo '<img src="http://xola.com/'.$data['photo']['src'].'" alt="' . $data['name'] .'"/>';
	}
	$btn = htmlentities('<div class="xola-checkout" data-seller="' . $data['seller']['id'] .'" data-version="2" data-experience="' . $data['id'] . '"></div>');

    echo '<input type="text" id="x' . $data['id'] . '" value="' .$btn.'"/>';
    echo '<button class= "button button-primary" data-copytarget="#x' . $data['id'] . '">copy</button>';
	echo '<span>' . $data['name'] .'</span>';

	echo '</li>';
	$count++;
	if($count==4){
	echo '<div class="clear_li"></div>';
	$count = 0;
	}
	}
	
	echo '</ul>';
	}
	
}
?>
</div>
