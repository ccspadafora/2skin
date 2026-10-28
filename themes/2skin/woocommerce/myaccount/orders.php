<?php
/**
 * Orders (2skin: filas del diseño Cuenta · Pagos).
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.5.0
 */

use TwoSkin\Core\Account;

defined( 'ABSPATH' ) || exit;

$tsk_states = array(
	'completed'  => array( 'check', 'tsk-state--ok' ),
	'processing' => array( 'check', 'tsk-state--ok' ),
	'on-hold'    => array( 'clock', 'tsk-state--next' ),
	'pending'    => array( 'clock', 'tsk-state--next' ),
);

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

	<h2 class="screen-reader-text"><?php esc_html_e( 'Pedidos', '2skin' ); ?></h2>
	<ul class="tsk-orders">
		<?php
		foreach ( $customer_orders->orders as $customer_order ) :
			$order = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			if ( ! $order ) {
				continue;
			}
			[ $tsk_icon, $tsk_class ] = $tsk_states[ $order->get_status() ] ?? array( 'close', 'tsk-state--muted' );
			$tsk_actions              = wc_get_account_orders_actions( $order );
			?>
			<li class="tsk-orders__row">
				<a class="caption tsk-upper tsk-orders__ref" href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
					<?php
					/* translators: %s: número de pedido. */
					echo esc_html( sprintf( __( 'Pedido %s', '2skin' ), $order->get_order_number() ) );
					?>
				</a>
				<time class="body-s" datetime="<?php echo esc_attr( $order->get_date_created() ? $order->get_date_created()->date( 'c' ) : '' ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
				<span class="body-s tsk-orders__what"><?php echo esc_html( class_exists( Account::class ) ? Account::order_summary( $order ) : '' ); ?></span>
				<span class="body-s tsk-state <?php echo esc_attr( $tsk_class ); ?>"><?php echo tsk_icon( $tsk_icon, 'tsk-icon tsk-icon--xs' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
				<span class="body-s tsk-orders__total"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
				<span class="tsk-orders__actions">
					<?php foreach ( $tsk_actions as $tsk_key => $tsk_action ) : ?>
						<?php
						/* translators: 1: acción, 2: número de pedido. */
						$tsk_aria = ! empty( $tsk_action['aria-label'] ) ? $tsk_action['aria-label'] : sprintf( __( '%1$s el pedido %2$s', '2skin' ), $tsk_action['name'], $order->get_order_number() );
						?>
						<a href="<?php echo esc_url( $tsk_action['url'] ); ?>" class="tsk-link <?php echo esc_attr( sanitize_html_class( $tsk_key ) ); ?>" aria-label="<?php echo esc_attr( $tsk_aria ); ?>"><?php echo esc_html( $tsk_action['name'] ); ?></a>
					<?php endforeach; ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
	<p class="caption tsk-orders__note"><?php esc_html_e( 'Recibes comprobante de pago por cada reserva. La factura electrónica se emite si la solicitas en la reserva.', '2skin' ); ?></p>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="tsk-actions tsk-orders__pages">
			<?php
			if ( 1 !== $current_page ) {
				echo tsk_button( __( 'Más recientes', '2skin' ), wc_get_endpoint_url( 'orders', $current_page - 1 ), array( 'variant' => 'secondary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if ( intval( $customer_orders->max_num_pages ) !== $current_page ) {
				echo tsk_button( __( 'Anteriores', '2skin' ), wc_get_endpoint_url( 'orders', $current_page + 1 ), array( 'variant' => 'secondary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	<?php endif; ?>

<?php else : ?>

	<div class="tsk-empty-box tsk-stack tsk-stack--4">
		<h2 class="h4"><?php esc_html_e( 'Todavía no tienes pedidos.', '2skin' ); ?></h2>
		<?php echo tsk_button( __( 'Ver tratamientos', '2skin' ), tsk_url( 'tratamientos' ), array( 'variant' => 'secondary', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
