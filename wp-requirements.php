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
		 * WordPress Version.
		 *
		 * Should be a string with a valid version like '4.0.0'.
		 *
		 * @access private
		 * @var string
		 */
		private $wp = true;

		/**
		 * PHP Version.
		 *
		 * Should be a string with a valid version like '5.4.0'
		 *
		 * @access private
		 * @var string
		 */
		private $php = true;

		/**
		 * PHP Extensions.
		 *
		 * Array of PHP extensions to check if they are loaded.
		 *
		 * @access private
		 * @var array
		 */
		private $ext = true;

		/**
		 * Display errors.
		 *
		 * An array of errors to display according to failure check.
		 * For example: array( 'php' => 'The minimum PHP required version is 5.4.0.' );
		 *
		 * @access private
		 * @var array
		 */
		private $errors = array();

		/**
		 * Constructor.
		 *
		 * @param array $requirements Required things.
		 * @param array $messages Error messages to display (optional).
		 */
		public function __construct( $requirements, $messages = array() ) {

			$errors = array();
			$requirements = array_merge( array( 'wp' => '', 'php' => '', 'ext' => '' ), (array) $requirements );


			// Check fo PHP version.
			if ( $requirements['php'] && is_string( $requirements['php'] ) ) {

				$php_version = version_compare( PHP_VERSION, $requirements['php'] );

				if ( $php_version === -1 ) {
					if ( isset( $errors['wp'] ) ) {
						$errors[] = $errors['wp'];
					} else {
						$errors[] = sprintf( 'The minimum PHP version required is %1$s, PHP version found: %2$s', '`' . $requirements['php'], '`' . PHP_VERSION . '``' );
					}
					$this->php = false;
				} else {
					$this->php = true;
				}

			}

			// Check for WordPress version.
			if ( $requirements['wp'] && is_string( $requirements['wp'] ) ) {

				global $wp_version;
				$wp_version = version_compare( $wp_version, $requirements['wp'] );

				if ( $wp_version === -1 ) {
					if ( isset( $errors['wp'] ) ) {
						$errors[] = $errors['wp'];
					} else {
						$errors[] = sprintf( 'The minimum WordPress version required is %1$s, WordPress version found: %2$s', '`' . $requirements['wp'] . '`', '`' . $wp_version . '`' );
					}
					$this->wp = false;
				} else {
					$this->wp = true;
				}

			}

			// Check fo PHP Extensions.
			if ( $requirements['extensions'] && is_array( $requirements['extensions'] ) ) {
				$extensions = array();
				foreach( $requirements['extensions'] as $extension ) {
					if ( $extension && is_string( $extension ) ) {
						$extensions[ $extension ] = extension_loaded( $extension );
					}
				}
				if ( in_array( false, $extensions ) ) {
					$this->ext = false;
					foreach( $extensions as $extension ) {
						if ( $extension === false ) {
							if ( isset( $errors[ $extension ] ) ) {
								$errors[] = $errors[ $extension ];
							} else {
								$errors[] = sprintf( 'The PHP extension %s is required and was not found', '`' . $extension . '`' );
							}
						}
					}
				}
			}

			$this->errors = $errors;

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