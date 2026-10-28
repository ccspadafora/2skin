<?php
/**
 * Plugin Name: 2Skin Core
 * Description: Lógica de negocio de 2Skin (campos, reglas e integraciones).
 * Version: 0.9.0
 * Requires at least: 6.9
 * Requires PHP: 8.2
 * Text Domain: 2skin-core
 */

defined( 'ABSPATH' ) || exit;

define( 'TSK_CORE_VERSION', '0.9.0' );
define( 'TSK_CORE_FILE', __FILE__ );
define( 'TSK_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'TSK_CORE_URL', plugin_dir_url( __FILE__ ) );

/*
 * Autoload PSR-4 simple: TwoSkin\Core\Admin\Meta_Boxes → src/Admin/Meta_Boxes.php.
 */
spl_autoload_register(
	static function ( string $class_name ): void {
		$prefix = 'TwoSkin\\Core\\';
		if ( ! str_starts_with( $class_name, $prefix ) ) {
			return;
		}
		$relative = str_replace( '\\', '/', substr( $class_name, strlen( $prefix ) ) );
		$file     = TSK_CORE_PATH . 'src/' . $relative . '.php';
		if ( is_readable( $file ) ) {
			require $file;
		}
	}
);

register_activation_hook( __FILE__, array( TwoSkin\Core\Plugin::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( TwoSkin\Core\Plugin::class, 'deactivate' ) );

add_action( 'plugins_loaded', array( TwoSkin\Core\Plugin::class, 'boot' ) );
