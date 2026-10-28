<?php
/**
 * Thankyou page · Confirmación de la reserva (diseño Confirmacion).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 11.2.0
 *
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;

// La tabla genérica del pedido la reemplaza el detalle del diseño.
remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );

$tsk_checkout = class_exists( '\TwoSkin\Core\Booking\Checkout' );
?>

<div class="woocommerce-order tsk-done">

	<?php
	if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() );

		$tsk_paid    = $order->has_status( wc_get_is_paid_statuses() );
		$tsk_cita    = (array) $order->get_meta( '_tsk_cita' );
		$tsk_virtual = ! empty( $tsk_cita['virtual'] );
		$tsk_agenda  = ! empty( $tsk_cita['fecha'] );
		$tsk_bonos   = class_exists( '\TwoSkin\Core\Gifts\Repository' ) ? \TwoSkin\Core\Gifts\Repository::for_order( $order->get_id() ) : array();
		?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<div class="tsk-done__intro">
				<p class="tsk-eyebrow"><?php esc_html_e( 'Pago no aprobado', '2skin' ); ?></p>
				<h1 class="display-l tsk-measure-14"><?php echo esc_html( $tsk_agenda ? __( 'Tu cita no quedó agendada.', '2skin' ) : __( 'Tu compra no se completó.', '2skin' ) ); ?></h1>
				<p class="body-l tsk-text-secondary tsk-measure-44"><?php echo esc_html( $tsk_agenda ? __( 'El banco o la pasarela rechazó el pago. Puedes intentarlo de nuevo; si el horario ya no está libre, elige otro.', '2skin' ) : __( 'El banco o la pasarela rechazó el pago. Puedes intentarlo de nuevo.', '2skin' ) ); ?></p>
				<div class="tsk-actions">
					<?php echo tsk_button( __( 'Intentar de nuevo', '2skin' ), $order->get_checkout_payment_url() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo tsk_whatsapp_url() ? tsk_button( __( 'Escríbenos', '2skin' ), tsk_whatsapp_url(), array( 'variant' => 'secondary' ) ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>

		<?php else : ?>

			<div class="tsk-half">
				<div class="tsk-done__intro">
					<span class="tsk-done__check"><?php echo tsk_icon( 'check', 'tsk-icon tsk-icon--md' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php if ( ! $tsk_agenda ) : ?>
						<p class="tsk-eyebrow"><?php echo esc_html( $tsk_paid ? __( 'Compra confirmada', '2skin' ) : __( 'Compra recibida', '2skin' ) ); ?></p>
						<h1 class="display-l tsk-measure-14"><?php echo esc_html( $tsk_paid ? __( 'Tu bono está listo.', '2skin' ) : __( 'Estamos confirmando tu pago.', '2skin' ) ); ?></h1>
						<p class="body-l tsk-text-secondary tsk-measure-44"><?php echo esc_html( $tsk_paid ? __( 'Te enviamos el código y tu comprobante por correo. Quien lo reciba lo escribe en «¿Tienes un bono de regalo?» al agendar su valoración, o lo presenta en el consultorio.', '2skin' ) : __( 'Cuando la pasarela apruebe el pago te enviamos el código del bono y tu comprobante por correo.', '2skin' ) ); ?></p>
					<?php elseif ( $tsk_paid ) : ?>
						<p class="tsk-eyebrow"><?php esc_html_e( 'Reserva confirmada', '2skin' ); ?></p>
						<h1 class="display-l tsk-measure-14"><?php esc_html_e( 'Tu cita está agendada.', '2skin' ); ?></h1>
						<p class="body-l tsk-text-secondary tsk-measure-44"><?php esc_html_e( 'Te enviamos la confirmación y tu comprobante de pago. Por WhatsApp te recordamos la cita 24 horas antes —para que confirmes tu asistencia— y 2 horas antes.', '2skin' ); ?></p>
					<?php else : ?>
						<p class="tsk-eyebrow"><?php esc_html_e( 'Reserva recibida', '2skin' ); ?></p>
						<h1 class="display-l tsk-measure-14"><?php esc_html_e( 'Estamos confirmando tu pago.', '2skin' ); ?></h1>
						<p class="body-l tsk-text-secondary tsk-measure-44"><?php esc_html_e( 'Tu horario queda apartado mientras la pasarela confirma el pago. Cuando se apruebe te enviamos la confirmación y tu comprobante.', '2skin' ); ?></p>
					<?php endif; ?>
					<div class="tsk-actions">
						<?php echo tsk_button( __( 'Ver mi protocolo', '2skin' ), tsk_url( 'cuenta' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo tsk_button( __( 'Volver al inicio', '2skin' ), tsk_url( 'inicio' ), array( 'variant' => 'secondary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php echo $tsk_agenda ? tsk_media( 'agenda.jpg', array( 'alt' => __( 'Consultorio 2Skin en Medicenter', '2skin' ), 'class' => 'tsk-done__media' ) ) : tsk_media( 'giftcard.jpg', array( 'alt' => __( 'Bono de regalo 2Skin', '2skin' ), 'class' => 'tsk-done__media' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>

				<div class="tsk-summary tsk-done__card">
					<?php if ( $tsk_checkout ) : ?>
						<dl class="tsk-facts">
							<?php foreach ( \TwoSkin\Core\Booking\Checkout::order_facts( $order ) as $tsk_label => $tsk_value ) : ?>
								<div class="tsk-facts__row">
									<dt class="tsk-label"><?php echo esc_html( $tsk_label ); ?></dt>
									<dd class="body-s"><?php echo esc_html( $tsk_value ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>

					<?php if ( $tsk_bonos ) : ?>
						<div>
							<p class="tsk-eyebrow tsk-done__eyebrow"><?php echo esc_html( _n( 'Tu bono', 'Tus bonos', count( $tsk_bonos ), '2skin' ) ); ?></p>
							<ul class="tsk-codes">
								<?php foreach ( $tsk_bonos as $tsk_bono ) : ?>
									<li>
										<span class="tsk-codes__code"><?php echo esc_html( $tsk_bono['codigo'] ); ?></span>
										<span class="body-s tsk-text-secondary"><?php echo esc_html( tsk_cop( $tsk_bono['valor'] ) . ' · ' . sprintf( /* translators: %s: fecha. */ __( 'vence el %s', '2skin' ), \TwoSkin\Core\Patients\Repository::format_date( $tsk_bono['vence'] ) ) ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

					<?php
					$tsk_lines = array();
					foreach ( $order->get_items() as $tsk_item ) {
						if ( (float) $tsk_item->get_total() <= 0 && $order->get_item_count() > 1 ) {
							continue; // El ítem «Cita» de Amelia en $0 no aporta al detalle.
						}
						$tsk_lines[] = $tsk_item;
					}
					?>
					<?php if ( $tsk_lines ) : ?>
						<div>
							<p class="tsk-eyebrow tsk-done__eyebrow"><?php esc_html_e( 'Detalle', '2skin' ); ?></p>
							<dl class="tsk-summary__rows">
								<?php foreach ( $tsk_lines as $tsk_item ) : ?>
									<?php
									$tsk_name = $tsk_checkout && ! $tsk_item->get_meta( '_tsk_bono_valor' )
										? \TwoSkin\Core\Catalog::item_label( (int) $tsk_item->get_product_id(), (int) $tsk_item->get_variation_id() )
										: $tsk_item->get_name();
									?>
									<div class="tsk-summary__row">
										<dt><?php echo esc_html( ( $tsk_name ? $tsk_name : $tsk_item->get_name() ) . ( $tsk_item->get_quantity() > 1 ? ' × ' . $tsk_item->get_quantity() : '' ) ); ?></dt>
										<dd><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $tsk_item ) ); ?></dd>
									</div>
								<?php endforeach; ?>
								<?php foreach ( $order->get_items( 'coupon' ) as $tsk_coupon ) : ?>
									<div class="tsk-summary__row">
										<dt><?php echo esc_html( sprintf( /* translators: %s: código. */ __( 'Bono %s', '2skin' ), strtoupper( $tsk_coupon->get_code() ) ) ); ?></dt>
										<dd><?php echo esc_html( '−' . tsk_cop( (float) $tsk_coupon->get_discount() ) ); ?></dd>
									</div>
								<?php endforeach; ?>
							</dl>
						</div>
					<?php endif; ?>

					<?php if ( $tsk_agenda ) : ?>
					<div>
						<p class="tsk-eyebrow tsk-done__eyebrow"><?php esc_html_e( 'Antes de tu cita', '2skin' ); ?></p>
						<ul class="tsk-arrow-list tsk-done__before">
							<?php
							$tsk_before = $tsk_virtual
								? array(
									array( 'doc', __( 'Envía antes de la cita el formulario de historia clínica con fotos, videos y los productos de tu rutina en casa.', '2skin' ) ),
									array( 'clock', __( 'Conéctate a la hora exacta desde un lugar con buena luz.', '2skin' ) ),
								)
								: array(
									array( 'clock', __( 'Llega 10 minutos antes. Tienes 15 minutos de tolerancia; después, el procedimiento queda sujeto a disponibilidad.', '2skin' ) ),
									array( 'doc', __( 'Trae tus paraclínicos recientes y la lista de tus medicamentos.', '2skin' ) ),
									array( 'droplet', __( 'Si puedes, llega sin maquillaje.', '2skin' ) ),
								);
							$tsk_before[] = array( 'shield', __( 'Si estás en embarazo o lactancia, avísanos antes de la cita.', '2skin' ) );
							$tsk_before[] = array( 'calendar', __( 'Cancela o reprograma con al menos 24 horas: tu pago se mantiene abonado.', '2skin' ) );
							foreach ( $tsk_before as [ $tsk_icon, $tsk_text ] ) {
								echo '<li>' . tsk_icon( $tsk_icon, 'tsk-icon tsk-icon--sm tsk-text-sage' ) . '<span class="body-s tsk-text-secondary">' . esc_html( $tsk_text ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</ul>
					</div>

					<div class="tsk-done__foot">
						<span class="body-s tsk-inline-icon"><?php echo tsk_icon( 'pin', 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $tsk_virtual ? __( 'Google Meet · enlace por WhatsApp y correo', '2skin' ) : tsk_contact( 'direccion' ) ); ?></span>
						<span class="body-s tsk-inline-icon"><?php echo tsk_icon( 'chat', 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Para reprogramar, avísanos con al menos 24 horas de anticipación', '2skin' ); ?></span>
					</div>
					<?php else : ?>
					<p class="caption tsk-text-secondary"><?php esc_html_e( 'Vigencia de 60 días desde la compra. Transferible solo antes de la valoración. No se redime en efectivo ni tiene devolución.', '2skin' ); ?></p>
					<?php endif; ?>
				</div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<?php wc_get_template( 'checkout/order-received.php', array( 'order' => false ) ); ?>

	<?php endif; ?>

</div>
