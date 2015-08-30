#WP Requirements

[![GitHub version](https://badge.fury.io/gh/nekojira%2Fwp-requirements.svg)](http://badge.fury.io/gh/nekojira%2Fwp-requirements)

Hi! I'm a little utility class that you can use in your WordPress plugin development.

Include me in your plugin and I will check if the PHP version or the installed WordPress version is the right one. If not, I will let you know and you can halt your script and display a message in WordPress dashboard so the admin will know why your plugin can't be activated.

### Usage

Pass the requirements to a new instance of this class like so:

    $my_requirements_check = new WP_Requirements( array(
        'PHP' => 'x.y.z',
        'WordPress => 'x.y.z.',
        'Extensions' => array(
            'extension_name',
            'another_extension',
        )
    );
    
You need to specify at least one value in the arguments array. **Mind the casing in the array keys**.

Then, you can use the following method to know if it passed (will return *bool*):

    $my_requirements_check->pass();

### Implementation

There are two ways you can include WP Requirements in your project.

##### Copy this class

You can copy the class found in `/src/wp-requirements.php` in this project.

> **Important!** If you choose to do so, please rename this class with the prefix used by your project (for example: from `WP_Requirements` to `My_Plyugin_Requirements` ). In this way there is less risk of a naming collision between projects.
 
##### Use Composer

Include this library with:

    $ composer require nekojira/wp-requirements
        
However, if you choose to do so, remind that Composer can only work with PHP 5.3.0 onwards. If your goal is to require a PHP version check against older versions of PHP, but want to use Composer, you need a workaround.
 
You could specify an additional 5.2 autoloader specific to PHP, for example using [PHP 5.2 Autoloading for Composer](https://bitbucket.org/xrstf/composer-php52) by including in your `package.json` file the following:
 
	{
	 "require": {
		 "xrstf/composer-php52": "1.*"
	 },
	 "scripts": {
		 "post-install-cmd": [
			 "xrstf\\Composer52\\Generator::onPostInstallCmd"
		 ],
		 "post-update-cmd": [
			 "xrstf\\Composer52\\Generator::onPostInstallCmd"
		 ],
		 "post-autoload-dump": [
			 "xrstf\\Composer52\\Generator::onPostInstallCmd"
		 ]
	 }
	}
 
#### Usage example

Either require with `include_once` or with Composer first, then at the beginning of your plugin, after the plugin headers, place some code like this:
	
	$my_plugin_requirements = new WP_Requirements( array(
		'PHP'       => '5.3.2',
		'WordPress' => '3.9.0',
	) );
	
	if ( $my_plugin_requirements->pass() === false ) {
	
	    // Get a notice message.
		if ( ! defined( 'MY_PLUGIN_NOTICE' ) ) {
			$my_plugin_notice = $my_plugin_requirements->get_notice( 'My Plugin Name' );
			define( 'MY_PLUGIN_NOTICE', $my_plugin_notice );
		}
		
		// Prints a notice.
		function my_plugin_requirements_notice() {
			if ( defined( 'MY_PLUGIN_NOTICE' ) ) {
				echo MY_PLUGIN_NOTICE;
			}
		}
		add_action( 'admin_notices', 'my_plugin_requirements_notice' );
	
	    // Do not activate this plugin.
		function my_plugin_deactivate_self() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
		add_action( 'admin_init', 'my_plugin_deactivate_self' );
	
	    // Remove an activation notice.
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	
	    // Halt the execution of the plugin.
		return;
	}
	
	// then from here on, continue with your code.

### Resources

WP Requirements was inspired by a post appeared on [wordpress.org](https://wordpress.org) at
[https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/](https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/)

You can also try [WP Update PHP](https://github.com/WPupdatePHP/wp-update-php) which however only checks for PHP but provides insightful explanations for the users on why they should keep their PHP version up to date.	
