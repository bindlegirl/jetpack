<?php
/**
 * Action Hooks for Jetpack WAF module.
 *
 * @package automattic/jetpack-waf
 */

namespace Automattic\Jetpack\Waf;

// We don't want to be anything in here outside WP context.
if ( ! function_exists( 'add_action' ) ) {
	return;
}

define( 'JETPACK_WAF_VERSION', '1.0.0' );
define( 'JETPACK_WAF_DIR', __DIR__ );

register_activation_hook( JETPACK__PLUGIN_FILE, array( __NAMESPACE__ . '\Waf', 'activate' ) );
add_action( 'admin_init', array( __NAMESPACE__ . '\Waf', 'update' ) );

/**
 * Runs the WAF in the WP context.
 *
 * @return void
 */
add_action(
	'plugin_loaded',
	function () {
		if ( ! defined( 'JETPACK_WAF_MODE' ) ) {
			$mode_option = get_option( 'jetpack_waf_mode' );

			if ( ! Waf::is_allowed_mode( $mode_option ) ) {
				return;
			}

			define( 'JETPACK_WAF_MODE', $mode_option );
		}

		if ( ! is_allowed_mode( JETPACK_WAF_MODE ) ) {
			return;
		}

		if ( ! Waf::did_run() ) {
			Waf::run();
		}
	}
);
