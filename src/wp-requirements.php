<?php
/**
 * WP Requirements
 *
 * Utility to check current PHP version, WordPress version and PHP extensions.
 *
 * @package WP_Requirements
 * @version 1.2.0
 * @author  Fulvio Notarstefano <fulvio.notarstefano@gmail.com>
 * @link    https://github.com/nekojira/wp-requirements
 * @license GPL2+
 */

if ( ! class_exists( 'WP_Requirements' ) ) {

	class WP_Requirements {

		/**
		 * WordPress.
		 *
		 * @access private
		 * @var bool
		 */
		private $wp = true;

		/**
		 * PHP.
		 *
		 * @access private
		 * @var bool
		 */
		private $php = true;

		/**
		 * PHP Extensions.
		 *
		 * @access private
		 * @var bool
		 */
		private $extensions = true;

		/**
		 * Requirements to check.
		 * 
		 * @access private
		 * @var array
		 */
		private $requirements = array();
		
		/**
		 * Results failures.
		 *
		 * Associative array with requirements results.
		 *
		 * @access private
		 * @var array
		 */
		private $failures = array();

		/**
		 * Constructor.
		 *
		 * @param array $requirements Associative array with requirements.
		 */
		public function __construct( $requirements ) {

			$this->requirements = $requirements;
			
			if ( $requirements && is_array( $requirements ) ) {
				
				$failures = $extensions = array();
				
				$requirements = array_merge( 
					array(
						'WordPress'  => '',
						'PHP'        => '',
						'Extensions' => '',
					), $requirements
				);

				// Check for WordPress version.
				if ( $requirements['WordPress'] && is_string( $requirements['WordPress'] ) ) {
					if ( function_exists( 'get_bloginfo' ) ) {
						$wp_version = get_bloginfo( 'version' );
						if ( version_compare( $wp_version, $requirements['WordPress'] ) === - 1 ) {
							$failures['WordPress'] = $wp_version;
							$this->wp = false;
						}
					}
				}

				// Check fo PHP version.
				if ( $requirements['PHP'] && is_string( $requirements['PHP'] ) ) {
					if ( version_compare( PHP_VERSION, $requirements['PHP'] ) === -1 ) {
						$failures['PHP'] = PHP_VERSION;
						$this->php = false;
					}
				}

				// Check fo PHP Extensions.
				if ( $requirements['Extensions'] && is_array( $requirements['Extensions'] ) ) {
					foreach ( $requirements['Extensions'] as $extension ) {
						if ( $extension && is_string( $extension ) ) {
							$extensions[ $extension ] = extension_loaded( $extension );
						}
					}
					if ( in_array( false, $extensions ) ) {
						foreach ( $extensions as $extension_name => $found  ) {
							if ( $found === false ) {
								$failures['Extensions'][ $extension_name ] = $extension_name;
							}
						}
						$this->extensions = false;
					}
				}

				$this->failures = $failures;

			} else {

				trigger_error( 'WP Requirements: the requirements are invalid.', E_USER_ERROR );

			}

		}

		/**
		 * Get requirements results.
		 *
		 * @return array
		 */
		public function failures() {
			return $this->failures;
		}

		/**
		 * Check if versions check pass.
		 *
		 * @return bool
		 */
		public function pass() {
			if ( in_array( false, array(
				$this->wp,
				$this->php,
				$this->extensions,
			) ) ) {
				return false;
			}
			return true;
		}

		/**
		 * Notice message.
		 *
		 * @param  string $name Name of the plugin or theme.
		 *
		 * @return string
		 */
		public function get_notice( $name ) {

			$notice   = '';
			$name     = htmlspecialchars( strip_tags( $name ) );
			$failures = $this->failures;
			
			if ( $failures && is_array( $failures ) ) {

				$notice  = '<div class="notice notice-error">' . "\n";
				$notice .= "\t" . '<p><strong>' . sprintf( '%s could not be activated.', $name ) . '</strong><br>'; 
				
				foreach ( $failures as $requirement => $found ) {

					$required = $this->requirements[ $requirement ];

					if ( 'extensions' != $requirement ) {
						$notice .= sprintf(
							'Required %1$s version: %2$s - Version found: %3$s',
							$requirement,
							$required,
							$found
			            ) . '<br>';
					} elseif ( is_array( $requirement ) ) {
						$notice .= sprintf(
							'Required PHP Extension(s) not found: %s.',
							join( ', ', $requirement )
						) . '<br>';
					}
						
				}

				$notice .= sprintf( 'Please update to meet %s requirements.', $name );
				$notice .= "\t" . '</p>' . "\n";
				$notice .= '</div>';
			}

			return $notice;
		}

	}

}
