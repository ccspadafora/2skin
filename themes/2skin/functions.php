<?php
/**
 * Tema 2Skin.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

define( 'TSK_THEME_VERSION', '0.6.0' );
define( 'TSK_THEME_PATH', get_template_directory() );
define( 'TSK_THEME_URL', get_template_directory_uri() );

require TSK_THEME_PATH . '/inc/setup.php';
require TSK_THEME_PATH . '/inc/assets.php';
require TSK_THEME_PATH . '/inc/customizer.php';
require TSK_THEME_PATH . '/inc/template-tags.php';
require TSK_THEME_PATH . '/inc/navigation.php';

if ( class_exists( 'WooCommerce' ) ) {
	require TSK_THEME_PATH . '/inc/woocommerce.php';
}
