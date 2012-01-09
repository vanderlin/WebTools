<?php
/*
Plugin Name: Instagram Feed
Plugin URI: 
Description: Add Instagram to any page
Version: 1.0
Author: Todd Vanderlin
Author URI: 
*/
?>
<?php

// -------------------------------------------------------------
function install_instagram_feed() {


}

// -------------------------------------------------------------
function instagram_create_menu() {

	//create new top-level menu
	add_menu_page('Instagram', 'Instagram', 'administrator', __FILE__, 'instagram_settings_page', plugins_url('/instagram-icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}

// -------------------------------------------------------------
function register_mysettings() {
	register_setting( 'instagram_options', 'instagram_options_values', 'instagram_check_settings');
	$options = get_option('instagram_options_values');
}

// -------------------------------------------------------------
function instagram_check_settings($input) {
	
	
	// Look for bad values
	if(empty( $input['token'] )) {
		$input['token'] = "please enter instagram token";	
	}
	
	// size
	if(empty( $input['size'] )) {
		$input['size'] = 0;	
	}
	
	// css
	if(empty($input['css-file'])) {	
		$input['css-file'] = plugins_url('/instagram.css', __FILE__);
	}
	
	// lightbox
	if(empty($input['lightbox'])) {	
		$input['lightbox'] = "lightview";
	}
	
	
	return $input;
}

// -------------------------------------------------------------
function addCssStyle() {
	$options = get_option('instagram_options_values');
	echo '<link rel="stylesheet" href="'.$options['css-file'].'" type="text/css" />', "\n";
	
	echo '<script src="http://cherne.net/brian/resources/jquery.hoverIntent.js"></script>';
	echo '<script src="'.plugins_url('/instagram.js', __FILE__).'"></script>';
}
	
// -------------------------------------------------------------
register_activation_hook(__FILE__, 'install_instagram_feed');
add_action('admin_menu', 'instagram_create_menu');
add_action('wp_head', 'addCssStyle');


// -------------------------------------------------------------
function get_instagram_feed() {
	
	$options = get_option('instagram_options_values');
	if($options['token'] != "") {		
		$url  = "https://api.instagram.com/v1/users/self/media/recent/?access_token=".$options['token']."&count=200";
		$res  = wp_remote_fopen($url);
		$size = $options['size'];
		if(isset($res)) {
		
			$json = json_decode($res);
			
			foreach($json->data as $obj) {
				$src = $obj->images->low_resolution->url;
				if($size == 0) $src = $obj->images->thumbnail->url;
				if($size == 1) $src = $obj->images->low_resolution->url;
				if($size == 2) $src = $obj->images->standard_resolution->url;
	
				$orj = $obj->images->standard_resolution->url;
				
				echo '<div class="instagram-item"><a title="'.$obj->caption->text.'" class="'.$options['lightbox'].'" href="'.$orj.'" rel="set[instagram]" ><img src="'.$src.'" title="'.$obj->caption->text.'"></a>';	
				echo '<div class="instagram-data">';
				
				echo '<span class="instagram-info">'.$obj->likes->count.' ♥</span>';
				echo '<span class="instagram-text">'.$obj->caption->text.'</span>';
				
				echo '</div>';
				
				//echo '<br><pre>'+print_r($obj->caption->text)+'</pre>';
				echo '</div>';
			}
		}
	}
}


// -------------------------------------------------------------
function instagram_settings_page() {

	?>
	<div class="wrap">
	<h2>Instagram Feed</h2>
	
	<form method="post" action="options.php">
		<?php settings_fields('instagram_options'); ?>
        <?php $options = get_option('instagram_options_values'); ?>
        <?php //$options = instagram_check_settings($options); ?>
        <?php $size    = $options['size']; ?>
       	<?php //print_r($options); ?> 	    
	    <table class="form-table">

	        <tr valign="top">
	        <th scope="row">Instagram Token</th>
	        <td>
	        <input style="width:400px;" type="text" name="instagram_options_values[token]" value="<?php echo $options['token']; ?>" /> 
			<button type="button" onclick="window.open('http://vanderlin.cc/experiments/instagram-wp', '_blank')">Get Token</button>
			</td>
	        </tr>
	        
	        <tr valign="top">
	        <th scope="row">Photo Size</th>
	        <td>
			<select name="instagram_options_values[size]">
			<option value="0" <?php if($size==0) echo "selected" ?>>Thumbnail</option>
			<option value="1" <?php if($size==1) echo "selected" ?>>Medium</option>
			<option value="2" <?php if($size==2) echo "selected" ?>>Fullsize</option>
			</select>
			</td>
	        </tr>
	        
	        <tr valign="top">
	        <th scope="row">CSS File</th>
	        <td>
			<input style="width:400px;" type="text" name="instagram_options_values[css-file]" value="<?php echo $options['css-file']; ?>" /> 
			</td>
	        </tr>
	        
	        <tr valign="top">
	        <th scope="row">Lightbox Class</th>
	        <td>
			<input style="width:400px;" type="text" name="instagram_options_values[lightbox]" value="<?php echo $options['lightbox']; ?>" /> 
			</td>
	        </tr>
	        
	        <tr valign="top">
	        <th scope="row">Functions</th>
	        <td>
	        <div style="width:400px">
			You can call <i>get_instagram_feed()</i> anywhere in your site to get your instagram feed. To create a simple page that shows your feed, see <i>template-example.php</i> located in the instagram folder. Add the php file to your theme and create a page using the file you added. 
			</div>
			</td>
	        </tr>
	        
	        

	    </table>
	    
	    <p class="submit">
	    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	    </p>
	    
	
	</form>
</div>
<?php } 
?>
