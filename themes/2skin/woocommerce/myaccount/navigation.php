<?php
/**
 * My Account navigation (2skin: pestañas con icono del diseño Cuenta).
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tsk_icons = array(
	'dashboard'              => 'calendar',
	'tratamientos-indicados' => 'doc',
	'orders'                 => 'cart',
	'mis-bonos'              => 'gift',
	'edit-account'           => 'user',
	'customer-logout'        => 'arrow',
);

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation tsk-tabs" aria-label="<?php esc_attr_e( 'Secciones de Mi piel', '2skin' ); ?>">
	<ul class="tsk-tabs__list">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?> tsk-tabs__item<?php echo 'customer-logout' === $endpoint ? ' tsk-tabs__item--end' : ''; ?>">
				<a class="tsk-tabs__link" href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
					<?php echo tsk_icon( $tsk_icons[ $endpoint ] ?? 'arrow', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo esc_html( $label ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
