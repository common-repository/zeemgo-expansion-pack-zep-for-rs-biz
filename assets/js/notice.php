function license_notice() {
if(!_license_valid()){
echo'<div class="error"><p>Your License Key is invalid, update your license here <a href="options-general.php?page=sample.wp2.php">HERE</a></p></div>';
	}
}

function license_footer () {
if(!_license_valid()){
echo '<div class="footer">Powered by <a href="http://wpbuz.com">WP Licensing System</a><div';
	}
}

add_action( 'admin_notices', 'license_notice' );
add_action('wp_footer', 'license_footer');
add_action('admin_menu', 'license_sample_menu');
add_option( 'sample_form_license', '0' );

function sample_admin_form() {
if($_POST['license_key']){
update_option( 'sample_form_license', $_POST['license_key'] );
}

echo '
<div><p>To remove license notice, fill form bellow.</p></div>
<form action="options-general.php?page=sample.wp2.php" method="post">
	<input id="license_key" name="license_key" size="70" type="text" value="'.get_option('sample_form_license').'" />
	<input type="submit" value="Validate" />
</form>
';
}

function license_sample_menu() {
	add_options_page('WP Licensing System Sample Form', 'Sample License Form', 'manage_options', 'sample.wp2.php', 'sample_admin_form');
}

//-- LICENSE VALIDATION SCRIPT START--//
function _get_license() {
	if ($license = get_option('sample_form_license')) {
		return $license;
	}
		
	return false;
}
	
function _license_valid() {
		
	if ($_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "localhost:" . $_SERVER['SERVER_PORT']) {
		return true;	
	} else {

		if ($license = _get_license()) {
		
			$license = get_option('sample_form_license');
			$prod_name = "Zeemgo Expansion Pack";
			//$domain = $_SERVER['HTTP_HOST']; 
			$domain = $_SERVER['SERVER_NAME']; 
			if (substr($domain, 0, 4) == "www.") { $domain = substr($domain, 4);}
			$userip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']; 
			$ip = gethostbyname($domain);
			
			$validdir = dirname(__FILE__);
			$validdomain = str_replace("www.", "", $domain);
				
			$key_info['key'] = $license;
			$key_info['domain'] = $validdomain;
			$key_info['validip'] = $userip;
			$key_info['validdir'] = $validdir;
			$key_info['product'] = $prod_name;
				
			$serverurl = "http://zeemgo.com/";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $serverurl."wp-content/plugins/wp-licensing/auth/verify.php");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $key_info);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
				$result = curl_exec($ch);
				curl_close($ch);
				
			$results = array();
			$result = json_decode($result, true);
				
			if($result['valid'] == "true"){
				return true;
				}
					
			}

		}		
	return false;
}

//-- LICENSE VALIDATION SCRIPT END--//

?>