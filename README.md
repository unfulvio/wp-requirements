#WP Requirements

Hi! I'm a little utility class that you can use in your WordPress plugin development.

Include me in your plugin and I will check if your PHP version or the installed WordPress version is the right one. If not, I will let you know and you can halt your script and display a message in WordPress dashboard so the admin will know why the plugin can't be activated.
 
Usage example:
	
	// You can place this at the beginning of your plugin, after the plugin headers.
	require_once 'wp-requirements.php';
	
	// Set your requirements.
	$my_requirements = array(
		'php' => '5.4.0',
		'wp'  => '4.0.0'
	);
	
	// Checks if the minimum WP version is 4.0.0 and minimum PHP version is 5.4.0.
	$requirements = new WP_requirements( $my_requirements );
	
	// If minimum requirements aren't met:
	if ( $requirements->pass() === false ) {
	
		// Print errors as dashboard admin notices when trying to activate the plugin.
		$errors = $requirements->errors();
		if ( $errors ) {
			foreach( $errors as $error ) {
				// WordPress supports 5.2.4 so use `create_function` instead of anonymous function.
				add_action( 'admin_notices', create_function( '', "echo $error;" ) );
			}
		}
	
	   // Stop the execution of the plugin.
	   die();
	}
