<?php
/**
 * Contenedor de Tu reserva (carrito) y Confirmar y agendar (pago). Las plantillas de WooCommerce
 * de woocommerce/cart y woocommerce/checkout pintan el contenido.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

get_header();

$tsk_endpoint = is_wc_endpoint_url( 'order-received' );

while ( have_posts() ) :
	the_post();
	?>
	<div class="container tsk-page tsk-compra<?php echo $tsk_endpoint ? ' tsk-compra--done' : ''; ?>">
		<?php
		if ( ! $tsk_endpoint ) {
			if ( is_checkout() ) {
				tsk_breadcrumbs( array( array( get_the_title( wc_get_page_id( 'cart' ) ), wc_get_cart_url() ) ), __( 'Confirmar', '2skin' ) );
			} else {
				tsk_breadcrumbs();
			}
			echo '<h1 class="h1 tsk-compra__title">' . esc_html( get_the_title() ) . '</h1>';
		}
		the_content();
		?>
	</div>
	<?php
endwhile;

get_footer();
