
**This project is retired and you should not use it.**

For reference only.

---

# WP Requirements

[![GitHub version](https://badge.fury.io/gh/unfulvio%2Fwp-requirements.svg)](https://badge.fury.io/gh/unfulvio%2Fwp-requirements)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/unfulvio/wp-requirements/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/unfulvio/wp-requirements/?branch=master)

Hi! I'm a little utility that you can use in your WordPress plugin development.

Include me in your plugin and I will check if the PHP version or the installed WordPress version is the right one. If not, I will let you know and you can halt your script and display a message in WordPress dashboard so the admin will know why your plugin can't be activated.

> **Necessary foreword** - WordPress  doesn't have - sadly - a dependency management system nor supports Composer. This makes the usage of Composer packages or redistributing/reusing libraries such as this one pointless and even dangerous as more plugins sharing the same library in different versions will collide, resulting in impredictable behavior and errors. Since this is a little library, it's best that you fork it and "namespace" your own the WordPress way (i.e. rename `WP_Requirements` with `Your_Plugin_Prefix_Requirements` for example) while including in your plugin or theme.

### Usage

Pass the requirements to a new instance of this class like so:

    $my_plugin_requirements = new WP_Requirements(
      'My Plugin Name',
      plugin_basename( __FILE__ ),
      array(
        'PHP' => 'x.y.z',
        'WordPress => 'x.y.z.',
        'Extensions' => array(
            'extension_name',
            'another_extension',
        )
      )
    );
 
Replace 'x.y.z' with the semantic version number you want to require. For PHP extension, just pass the extension name as array string values.

You need to specify at least one value in the arguments array. **Mind the casing in the array keys**.

Then, you can use the following method to know if it passed (will return *bool*):

    $my_requirements_check->pass();

### Implementation

There are two ways you can include WP Requirements in your project.

##### Copy this class (recommended way) 

You can copy the class found in `/src/wp-requirements.php` in this project.

> **Important!** If you choose to do so, please rename this class with the prefix used by your project (for example: from `WP_Requirements` to `My_Plugin_Requirements` ). In this way there is less risk of a naming collision between projects.
 
##### Use Composer (not recommended)

Include this library with:

    $ composer require nekojira/wp-requirements
        
However, if you choose to do so, remind that Composer can only work with PHP 5.3.0 onwards. If your goal is to require a PHP version check against older versions of PHP, but want to use Composer, you need a workaround.
 
You could specify an additional autoloader compatible with PHP 5.2, for example using the [PHP 5.2 Autoloading for Composer](https://bitbucket.org/xrstf/composer-php52), by including in your `package.json` file the following:
 
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
	 
**Note:** it looks like Composer52 has been abandoned and currently moved to https://github.com/composer-php52/composer-php52 - as the current maintainer puts it: "Please do not use this, if you can avoid it. It's a horrible hack, often breaks and is extremely tied to Composer's interna. This package was originally developed in 2012, when PHP 5.2 was much more common on cheap webhosts." -- Just update your freackin' server to use a decent version of PHP.
	 
 
### Usage example

Either require with `include_once` or with Composer first, then at the beginning of your plugin, after the plugin headers, place some code like this:
	
	$my_plugin_requirements = new WP_Requirements(
	  'My Plugin Name',
		plugin_basename( __FILE__ ),
		array(
			'PHP'       => '5.3.2',
			'WordPress' => '3.9.0',
		) 
	);
	
	if ( $my_plugin_requirements->pass() === false ) {
		// Deactivate the plugin and print an admin notice.
		$my_plugin_requirements->halt();
		// Halt the execution of the rest of the plugin.
		return;
	}
	
	// Then from here on, continue with your code.
	// Perhaps with `include_once 'includes/main_class.php'`
	// which may contain potentially incompatible PHP code.

### Resources

WP Requirements was inspired by a post appeared on [wordpress.org](https://wordpress.org) at
[https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/](https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/)

You can also try [WP Update PHP](https://github.com/WPupdatePHP/wp-update-php) which however only checks for PHP but provides insightful explanations for the users on why they should keep their PHP version up to date.	
