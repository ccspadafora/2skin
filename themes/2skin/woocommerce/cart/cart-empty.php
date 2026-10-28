<?php
/**
 * Empty cart page · Tu reserva vacía (diseño Carrito).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

// El mensaje genérico de WooCommerce lo reemplaza la caja del diseño.
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
do_action( 'woocommerce_cart_is_empty' );
?>
<div class="tsk-empty-box">
	<?php echo tsk_icon( 'cart', 'tsk-icon tsk-icon--lg tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<h2 class="h4"><?php esc_html_e( 'Todavía no has añadido nada.', '2skin' ); ?></h2>
	<p class="body-s tsk-text-secondary tsk-measure-44"><?php esc_html_e( 'Si es tu primera vez, empieza por la valoración médica: es el paso que define tu protocolo.', '2skin' ); ?></p>
	<div class="tsk-actions">
		<?php
		/* translators: %s: precio. */
		echo tsk_button( sprintf( __( 'Añadir valoración · %s', '2skin' ), tsk_cop( tsk_valoracion_precio() ) ), add_query_arg( 'modalidad', 'presencial', wc_get_checkout_url() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo tsk_button( __( 'Ver tratamientos', '2skin' ), apply_filters( 'woocommerce_return_to_shop_redirect', tsk_url( 'tratamientos' ) ), array( 'variant' => 'secondary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WooCommerce.Commenting.CommentHooks.MissingHookComment
		?>
	</div>
</div>
