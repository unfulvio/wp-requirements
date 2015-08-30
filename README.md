#WP Requirements

[![GitHub version](https://badge.fury.io/gh/nekojira%2Fwp-requirements.svg)](http://badge.fury.io/gh/nekojira%2Fwp-requirements)

Hi! I'm a little utility class that you can use in your WordPress plugin development.

Include me in your plugin and I will check if your PHP version or the installed WordPress version is the right one. If not, I will let you know and you can halt your script and display a message in WordPress dashboard so the admin will know why the plugin can't be activated.

>#### Important Note 
Use this as a template to implement your own requirements check. Althought the class as presented in this repository is wrapped in a `class_exist` conditional, there could be other plugin developers implementing the same class - to avoid naming collisions or duplicate classes, rename the class with a most unique prefix of your own.
 
### Usage
 
Bare minimum usage example:
 
	// Place the class file in your project and put this code at the beginning of your plugin, after the plugin headers.
	require_once 'wp-requirements.php';
		
	// Set your requirements.
	$plugin_name_requirements = array(
		'php' => '5.4.0',
		'wp'  => '3.9.0'
	);
	 
	// Checks if the minimum WP version is 3.9.0 and minimum PHP version is 5.4.0.
	$requirements = new WP_Requirements( $plugin_name_requirements );
	if ( $requirements->pass() === false ) {
		
		// Halt loading the rest of the plugin.
		return;
		
	} else {
	
		// Load the rest of the plugin that may contain non legacy compatible PHP code.
		require_once 'the_plugin.php';
	
	}

But you probably want to provide information to your users or they might think your plugin is broken: 
	
	// Place the class file in your project and put this code at the beginning of your plugin, after the plugin headers.
	require_once 'wp-requirements.php';
	
	// Set your requirements.
	$plugin_name_requirements = array(
		'php' => '5.4.0',
		'wp'  => '3.9.0'
	);
	
	// Checks if the minimum WP version is 3.9.0 and minimum PHP version is 5.4.0.
	$requirements = new WP_requirements( $plugin_name_requirements );
	
	// If minimum requirements aren't met:
	if ( $requirements->pass() === false ) {

		function plugin_name_requirements() {
			$wp_version = get_bloginfo( 'version' );
			echo '<div class="error"><p>' . sprintf( __( 'Plugin Name requires PHP 5.4 and WordPress 3.9.0 to function properly. PHP version found: %1$s. WordPress installed version: %2$s. Please upgrade. The Plugin has been auto-deactivated.', 'plugin-name' ), PHP_VERSION, $wp_version ) . '</p></div>';
			// Removes the activation notice if set.
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		} 
		add_action( 'admin_notices', 'plugin_name_requirements' );
		
		// This could be useful only if your plugin isn't new and previously didn't have requirements.
		function plugin_name_deactivate_self() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
		add_action( 'admin_init', 'plugin_name_deactivate_self' );
		
		// Halt loading the rest of the plugin.
	   	return;
	   	
	} else {
      	
    	// Load the rest of the plugin that may contain non legacy compatible PHP code.
    	require_once 'the_plugin.php';
      	
    }

### Resources

WP Requirements was inspired by a post appeared on [wordpress.org](https://wordpress.org) at
[https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/](https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/)

You can also try [WP Update PHP](https://github.com/WPupdatePHP/wp-update-php) which however only checks for PHP but provides insightful explanations for the users on why they should keep their PHP version up to date.	
