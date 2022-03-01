<?php
/**
 * Entrypoint for actually executing the WAF.
 *
 * @package automattic/jetpack-waf
 */

namespace Automattic\Jetpack\Waf;

/**
 * The Web Application Firewall Class
 */
class Waf {

	const RULES_FILE = JETPACK_WAF_DIR . '/rules/rules.php';

	/**
	 * Activates the product by generating the rules script and setting the version
	 *
	 * @return void
	 */
	public static function activate() {
		// errors here because this occurs before the waf mode is defined.
		if ( self::is_allowed_mode( JETPACK_WAF_MODE ) ) {
			$version = get_option( 'jetpack_waf_version' );
			if ( ! $version ) {
				add_option( 'jetpack_waf_version', JETPACK_WAF_VERSION );
			}
			if ( JETPACK_WAF_VERSION !== $version ) {
				self::generate_rules();
			}
		}
	}

	/**
	 * Did the WAF run yet or not?
	 *
	 * @return bool
	 */
	public static function did_run() {
		return defined( 'JETPACK_WAF_RUN' );
	}

	/**
	 * Error handler to be used while the WAF is being executed.
	 *
	 * @param int    $code The error code.
	 * @param string $message The error message.
	 * @param string $file File with the error.
	 * @param string $line Line of the error.
	 * @return void
	 */
	public static function errorHandler( $code, $message, $file, $line ) { // phpcs:ignore
		// Intentionally doing nothing for now.
	}

	/**
	 * Determines if the passed $option is one of the allowed WAF operation modes.
	 *
	 * @param  string $option The mode option.
	 * @return bool
	 */
	public static function is_allowed_mode( $option ) {
		$allowed_modes = array(
			'normal',
			'silent',
		);

		return in_array( $option, $allowed_modes, true );
	}

	/**
	 * Runs the WAF and potentially stops the request if a problem is found.
	 *
	 * @return void
	 */
	public static function run() {
		// Make double-sure we are only running once.
		if ( self::did_run() ) {
			return;
		}

		// if ABSPATH is defined, then WordPress has already been instantiated,
		// and we're running as a plugin (meh). Otherwise, we're running via something
		// like PHP's prepend_file setting (yay!).
		define( 'JETPACK_WAF_RUN', defined( 'ABSPATH' ) ? 'plugin' : 'preload' );

		// if the WAF is being run before a command line script, don't try to execute rules (there's no request).
		if ( PHP_SAPI === 'cli' ) {
			return;
		}

		// if something terrible happens during the WAF running, we don't want to interfere with the rest of the site,
		// so we intercept errors ONLY while the WAF is running, then we remove our handler after the WAF finishes.
		$display_errors = ini_get( 'display_errors' );
		// phpcs:ignore
		ini_set( 'display_errors', 'Off' );
		// phpcs:ignore
		set_error_handler( array( self::class, 'errorHandler' ) );

		try {
			// phpcs:ignore
			$waf = new Waf_Runtime( new Waf_Transforms(), new Waf_Operators() );

			// execute waf rules.
			// phpcs:ignore
			include self::RULES_FILE;
		} catch ( \Exception $err ) { // phpcs:ignore
			// Intentionally doing nothing.
		}

		// remove the custom error handler, so we don't interfere with the site.
		restore_error_handler();
		// phpcs:ignore
		ini_set( 'display_errors', $display_errors );
	}

	/**
	 * Updates the WAF if the version has changed
	 *
	 * @return void
	 */
	public static function update() {
		if ( JETPACK_WAF_VERSION !== get_option( 'jetpack_waf_version' ) ) {
			update_option( 'jetpack_waf_version', JETPACK_WAF_VERSION );
			self::generate_rules();
		}
	}

	/**
	 * Generates the rules.php script
	 *
	 * @throws \Exception If filesystem is not available.
	 * @throws \Exception If file writing fails.
	 * @return void
	 */
	private static function generate_rules() {
		// TODO: Switch this to using the WAF rules compiler.
		self::get_filesystem();

		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			throw new \Exception( 'No filesystem available' );
		}
		// Ensure that the folder exists.
		if ( ! $wp_filesystem->is_writable( dirname( self::RULES_FILE ) ) ) {
			$wp_filesystem->mkdir( dirname( self::RULES_FILE ) );
		}
		if ( ! $wp_filesystem->put_contents( self::RULES_FILE, "<?php\n" ) ) {
			throw new \Exception( 'Failed writing to: ' . self::RULES_FILE );
		}
	}

	/**
	 * Creates connection to the WordPress Filesystem API
	 *
	 * @throws \Exception If user does not have write access.
	 * @return void
	 */
	private static function get_filesystem() {
		if ( 'direct' === get_filesystem_method() ) {
			$url   = trailingslashit( site_url() ) . 'wp-admin/';
			$creds = request_filesystem_credentials( $url, '', false, false, array() );

			/* initialize the API */
			WP_Filesystem( $creds );
		} else {
			/* don't have direct write access. */
			throw new \Exception( 'User does not have write access' );
		}
	}
}
