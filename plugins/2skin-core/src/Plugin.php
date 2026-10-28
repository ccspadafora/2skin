<?php
/**
 * Arranque del plugin.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Registra los módulos del plugin en sus hooks.
 */
final class Plugin {

	/**
	 * Engancha los módulos. Se llama en plugins_loaded.
	 */
	public static function boot(): void {
		add_action( 'init', array( Content_Types::class, 'register' ) );
		add_action( 'init', array( Content_Types::class, 'register_meta' ) );
		Treatment_Fields::hooks();
		I18n::hooks();

		if ( class_exists( 'WooCommerce' ) ) {
			Patients\Repository::maybe_install();
			Gifts\Repository::maybe_install();
			Catalog::hooks();
			Patients\Follow_Up::hooks();
			Booking\Reservation::hooks();
			Booking\Checkout::hooks();
			Clinical\History::hooks();
			Gifts\Gifts::hooks();
			Account::hooks();
			Payments\Bold::hooks();
		}

		if ( is_admin() ) {
			Admin\Meta_Boxes::hooks();
			Admin\Term_Fields::hooks();
			Admin\Faq_Products::hooks();
			Cases::hooks();
			if ( class_exists( 'WooCommerce' ) ) {
				Admin\Follow_Up_Admin::hooks();
				Admin\Gifts_Admin::hooks();
			}
		}
	}

	/**
	 * Activación: registra los tipos de contenido antes de regenerar las URLs.
	 */
	public static function activate(): void {
		Content_Types::register();
		Patients\Repository::install();
		Gifts\Repository::install();
		flush_rewrite_rules();
	}

	/**
	 * Desactivación: limpia las reglas de URL de los tipos propios.
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}
}
