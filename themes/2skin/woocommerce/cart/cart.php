<?php
/**
 * Cart Page · Tu reserva (diseño Carrito).
 *
 * Basada en la plantilla de WooCommerce; mantiene el formulario, los ganchos y el nonce del carrito.
 * Las cantidades se cambian con botones que envían cart[clave][qty] y quitan con el enlace estándar.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 11.2.0
 */

defined( 'ABSPATH' ) || exit;

$tsk_has_valoracion = false;
foreach ( WC()->cart->get_cart() as $tsk_item ) {
	$tsk_product = wc_get_product( $tsk_item['product_id'] );
	if ( $tsk_product && tsk_is_valoracion( $tsk_product ) ) {
		$tsk_has_valoracion = true;
	}
}
$tsk_keep_url = class_exists( '\TwoSkin\Core\Booking\Checkout' ) ? \TwoSkin\Core\Booking\Checkout::keep_valoracion_url() : wc_get_checkout_url();

do_action( 'woocommerce_before_cart' ); ?>

<div class="tsk-cart">
	<form class="woocommerce-cart-form tsk-cart__lines" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<?php do_action( 'woocommerce_before_cart_table' ); ?>

		<ul class="tsk-lines woocommerce-cart-form__contents">
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				$visible    = apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment

				if ( ! ( $_product instanceof WC_Product && $_product->exists() && $cart_item['quantity'] > 0 && $visible ) ) {
					continue;
				}
				$tsk_line     = tsk_line_data( (int) $product_id, (int) $cart_item['variation_id'] );
				$product_name = apply_filters( 'woocommerce_cart_item_name', $tsk_line['name'], $cart_item, $cart_item_key ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
				$tsk_qty      = (int) $cart_item['quantity'];
				$tsk_max      = $_product->is_sold_individually() ? 1 : $_product->get_max_purchase_quantity();
				?>
				<li class="tsk-line woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<?php
					$tsk_thumb = $tsk_line['image']
						? wp_get_attachment_image( $tsk_line['image'], 'thumbnail', false, array( 'class' => 'tsk-line__img', 'alt' => '' ) )
						: '<span class="tsk-line__img tsk-media__placeholder" aria-hidden="true"></span>';
					echo $tsk_line['url'] ? '<a href="' . esc_url( $tsk_line['url'] ) . '" tabindex="-1" aria-hidden="true">' . $tsk_thumb . '</a>' : $tsk_thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- imagen de WordPress o marcado fijo.
					?>
					<div class="tsk-line__body">
						<?php if ( $tsk_line['sub'] ) : ?>
							<span class="tsk-label"><?php echo esc_html( $tsk_line['sub'] ); ?></span>
						<?php endif; ?>
						<h2 class="body-l tsk-line__name">
							<?php
							if ( $tsk_line['url'] ) {
								printf( '<a href="%s">%s</a>', esc_url( $tsk_line['url'] ), wp_kses_post( $product_name ) );
							} else {
								echo wp_kses_post( $product_name );
							}
							?>
						</h2>
						<?php if ( $tsk_line['detail'] ) : ?>
							<p class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_line['detail'] ); ?></p>
						<?php endif; ?>
						<?php do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key ); ?>

						<div class="tsk-line__actions">
							<?php if ( 1 !== $tsk_max ) : ?>
								<div class="tsk-qty" role="group" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: producto. */ __( 'Cantidad de %s', '2skin' ), wp_strip_all_tags( $product_name ) ) ); ?>">
									<button type="submit" class="tsk-qty__btn" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="<?php echo esc_attr( (string) max( 0, $tsk_qty - 1 ) ); ?>" aria-label="<?php esc_attr_e( 'Quitar uno', '2skin' ); ?>"><?php echo tsk_icon( 'minus', 'tsk-icon tsk-icon--xs' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
									<span class="body-s tsk-qty__value" aria-live="polite"><?php echo esc_html( (string) $tsk_qty ); ?></span>
									<button type="submit" class="tsk-qty__btn" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="<?php echo esc_attr( (string) ( $tsk_qty + 1 ) ); ?>" aria-label="<?php esc_attr_e( 'Añadir uno', '2skin' ); ?>"<?php disabled( $tsk_max > 0 && $tsk_qty >= $tsk_max ); ?>><?php echo tsk_icon( 'plus', 'tsk-icon tsk-icon--xs' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
								</div>
							<?php endif; ?>
							<?php
							echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a role="button" href="%s" class="remove tsk-line__remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s%s</a>',
									esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
									/* translators: %s: producto. */
									esc_attr( sprintf( __( 'Quitar %s de tu reserva', '2skin' ), wp_strip_all_tags( $product_name ) ) ),
									esc_attr( $product_id ),
									esc_attr( $_product->get_sku() ),
									tsk_icon( 'close', 'tsk-icon tsk-icon--xs' ),
									esc_html__( 'Quitar', '2skin' )
								),
								$cart_item_key
							);
							?>
						</div>
					</div>
					<span class="body tsk-nowrap tsk-line__total">
						<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				</li>
				<?php
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>
			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</ul>

		<input type="hidden" name="update_cart" value="1">
		<?php do_action( 'woocommerce_cart_actions' ); ?>
		<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>

		<p class="caption tsk-cart__note">
			<?php echo tsk_icon( 'shield', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php esc_html_e( 'Los tratamientos solo se reservan en línea si ya eres paciente en seguimiento y tu médica los indicó. Se paga el 100% al reservar y no hay devoluciones de dinero; puedes reprogramar con 24 horas de anticipación.', '2skin' ); ?>
		</p>

		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>

	<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

	<aside class="tsk-summary cart-collaterals" aria-labelledby="tsk-summary-title">
		<h2 class="h4" id="tsk-summary-title"><?php esc_html_e( 'Resumen', '2skin' ); ?></h2>
		<dl class="tsk-summary__rows">
			<div class="tsk-summary__row">
				<dt><?php esc_html_e( 'Subtotal', '2skin' ); ?></dt>
				<dd><?php wc_cart_totals_subtotal_html(); ?></dd>
			</div>
			<?php foreach ( WC()->cart->get_coupons() as $tsk_code => $tsk_coupon ) : ?>
				<div class="tsk-summary__row">
					<dt><?php wc_cart_totals_coupon_label( $tsk_coupon ); ?></dt>
					<dd><?php wc_cart_totals_coupon_html( $tsk_coupon ); ?></dd>
				</div>
			<?php endforeach; ?>
			<div class="tsk-summary__row">
				<dt><?php esc_html_e( 'Valoración abonable', '2skin' ); ?></dt>
				<dd><?php echo $tsk_has_valoracion ? esc_html__( 'Se abona si inicias en 1 mes', '2skin' ) : esc_html__( 'No incluida', '2skin' ); ?></dd>
			</div>
			<div class="tsk-summary__row tsk-summary__row--total">
				<dt class="body"><?php esc_html_e( 'Total a pagar hoy', '2skin' ); ?></dt>
				<dd class="tsk-summary__total"><?php wc_cart_totals_order_total_html(); ?></dd>
			</div>
		</dl>

		<?php echo tsk_button( __( 'Continuar', '2skin' ), wc_get_checkout_url(), array( 'class' => 'tsk-btn--block checkout-button' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<?php if ( ! $tsk_has_valoracion ) : ?>
			<div class="tsk-summary__box">
				<p class="body-s"><?php esc_html_e( '¿Es tu primera vez? La valoración es obligatoria y es lo único que pagas en línea.', '2skin' ); ?></p>
				<?php
				/* translators: %s: precio. */
				echo tsk_button( sprintf( __( 'Reservar solo la valoración · %s', '2skin' ), tsk_cop( tsk_valoracion_precio() ) ), $tsk_keep_url, array( 'variant' => 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		<?php endif; ?>

		<div class="tsk-summary__notes">
			<span class="caption tsk-inline-icon"><?php echo tsk_icon( 'lock', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Pago seguro con Bold · precios sin IVA', '2skin' ); ?></span>
			<span class="caption tsk-inline-icon"><?php echo tsk_icon( 'calendar', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Reprograma hasta 2 veces con 24 h de anticipación', '2skin' ); ?></span>
		</div>
	</aside>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
