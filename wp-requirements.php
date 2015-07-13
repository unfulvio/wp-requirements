<?php
/**
 * WP Requirements
 *
 * Utility to check current PHP version, WordPress version and PHP extensions.
 *
 * @package WP_Requirements
 * @version 1.0.0
 * @author  Fulvio Notarstefano <fulvio.notarstefano@gmail.com>
 * @link    https://github.com/nekojira/wp-requirements
 * @license GPL2+
 */

if ( ! class_exists( 'WP_Requirements' ) ) {

	class WP_Requirements {

		/**
		 * PHP Version.
		 *
		 * Should be a string with a valid version like '5.4.0'
		 *
		 * @access private
		 * @var string
		 */
		private $php;

		/**
		 * WordPress Version.
		 *
		 * Should be a string with a valid version like '4.0.0'.
		 *
		 * @access private
		 * @var string
		 */
		private $wp;

		/**
		 * Display errors.
		 *
		 * An array of errors to display according to failure check.
		 * For example: array( 'php' => 'The minimum PHP required version is 5.4.0.' );
		 *
		 * @access private
		 * @var array
		 */
		private $errors;

		/**
		 * PHP Extensions.
		 *
		 * Array of PHP extensions to check if they are loaded.
		 *
		 * @access private
		 * @var array
		 */
		private $ext;

		/**
		 * Constructor.
		 *
		 * @param string $wp   WordPress version.
		 * @param string $php  PHP Version.
		 * @param array  $ext  PHP Extensions.
		 * @param array  $msgs Custom messages to print in case of check failure.
		 */
		public function __construct( $wp = '4.0.0', $php = '5.4.0', $ext = array(), $msgs = array() ) {

			$messages = array();

			// Check fo PHP version.
			if ( $php && is_string( $php ) ) {

				$php_version = version_compare( PHP_VERSION, $php );

				if ( $php_version === -1 ) {
					if ( isset( $msgs['wp'] ) ) {
						$messages[] = $msgs['wp'];
					} else {
						$messages[] = sprintf( 'The minimum PHP version required is %1$s, PHP version found: %2$s', '`' . $php, '`' . PHP_VERSION . '``' );
					}
					$this->php = false;
				} else {
					$this->php = true;
				}

			}

			// Check for WordPress version.
			if ( $wp && is_string( $wp ) ) {

				global $wp_version;
				$wp_version = version_compare( $wp_version, $wp );

				if ( $wp_version === -1 ) {
					if ( isset( $msgs['wp'] ) ) {
						$messages[] = $msgs['wp'];
					} else {
						$messages[] = sprintf( 'The minimum WordPress version required is %1$s, WordPress version found: %2$s', '`' . $wp . '`', '`' . $wp_version . '`' );
					}
					$this->wp = false;
				} else {
					$this->wp = true;
				}

			}

			// Check fo PHP Extensions.
			if ( $ext && is_array( $ext ) ) {
				$extensions = array();
				foreach( $ext as $extension ) {
					$extensions[ $extension ] = extension_loaded( $extension );
				}
				if ( in_array( false, $extensions ) ) {
					$this->ext = false;
					foreach( $extensions as $extension ) {
						if ( $extension === false ) {
							if ( isset( $msgs[ $extension ] ) ) {
								$messages[] = $msgs[ $extension ];
							} else {
								$messages[] = sprintf( 'The PHP extension %s is required and was not found', '`' . $extension . '`' );
							}
						}
					}
				}
			}

			$this->errors = $messages;

		}

		/**
		 * Get errors.
		 *
		 * @return array
		 */
		public function errors() {
			return $this->errors;
		}

		/**
		 * Check if versions check pass.
		 *
		 * @return bool
		 */
		public function pass() {
			$pass = true;
			if ( $this->php === false ) {
				$pass = false;
			}
			if ( $this->wp === false ) {
				$pass = false;
			}
			if ( $this->ext === false ) {
				$pass = false;
			}
			return $pass;
		}

	}

}