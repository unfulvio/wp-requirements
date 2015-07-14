#WP Requirements

Hi! I'm a little utility class that you can use in your WordPress plugin development.

Include me in your plugin and I will check if your PHP version or the installed WordPress version is the right one. If not, I will let you know and you can halt your script and display a message in WordPress dashboard so the admin will know why the plugin can't be activated.
 
Bare minimum usage example:
 
	// Place the class file in your project and put this code at the beginning of your plugin, after the plugin headers.
	require_once 'wp-requirements.php';
		
	// Set your requirements.
	$my_requirements = array(
		'php' => '5.4.0',
		'wp'  => '4.0.0'
	);
	 
	// Checks if the minimum WP version is 4.0.0 and minimum PHP version is 5.4.0.
	$requirements = new WP_requirements( $my_requirements );
	if ( $requirements->pass() === false ) {
		// Abort loading the rest of the plugin.
		return;
	}

However, you probably want to provide some information to your users or they will think your plugin is broken: 
	
	// Place the class file in your project and put this code at the beginning of your plugin, after the plugin headers.
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
	
		add_action( 'admin_notices', create_function( '',
			"echo '<div class=\"error\"><p>Unfortunately the plugin X cannot be activated. The minimum requirements in your installation were not met.</p></div>';"
		) );
	
		// Print errors as dashboard admin notices when trying to activate the plugin.
		$errors = $requirements->errors();
		if ( $errors ) {
			foreach( $errors as $error ) {
				// WordPress supports 5.2.4 so use `create_function` instead of anonymous function.
				add_action( 'admin_notices', create_function( '', "echo '<div class=\"error\"><p>' . $error . '</p></div>';" ) );
			}
		}
	
		// This could be useful only if your plugin isn't new and previously didn't have requirements.  
		add_action( 'admin_init', 'my_plugin_deactivate_self' );
		function my_plugin_deactivate_self() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
		
		// Stop the execution of the plugin.
	   	return;
	}

This was inspired by a post appeared on [wordpress.org](https://wordpress.org) at
[https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/](https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/)

You can also try [WP Update PHP](https://github.com/WPupdatePHP/wp-update-php) which however only checks for PHP but provides insightful explanations for the users on why they should keep their PHP version up to date.	
