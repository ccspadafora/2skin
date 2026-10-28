<?php
/**
 * Checkout Form · Confirmar y agendar (diseño Reserva).
 *
 * Basada en la plantilla de WooCommerce: mismo formulario, ganchos, campos de facturación y nonce.
 * Secciones: tipo de paciente, datos, historia clínica (solo con plataforma clínica conectada), cita,
 * pago y consentimientos. El resumen y el botón de pago van en la columna lateral.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

use TwoSkin\Core\Booking\Checkout as Tsk_Checkout;
use TwoSkin\Core\Booking\Reservation as Tsk_Reservation;
use TwoSkin\Core\Clinical\History as Tsk_History;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
	return;
}

if ( ! class_exists( Tsk_Checkout::class ) ) {
	return;
}

$tsk_cart       = Tsk_Reservation::analyze();
$tsk_treatments = (bool) $tsk_cart['treatments'];
$tsk_cita       = 'bono' !== $tsk_cart['kind'];
$tsk_tipo       = 'tratamientos' === $tsk_cart['kind'] ? 'seguimiento' : 'nuevo';
$tsk_history    = Tsk_History::enabled();
$tsk_doctors    = Tsk_Reservation::doctors();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- solo preselecciona el procedimiento de interés.
$tsk_interes = isset( $_GET['interes'] ) ? sanitize_title( wp_unslash( $_GET['interes'] ) ) : '';
$tsk_n       = 0;
$tsk_num     = static function () use ( &$tsk_n ): string {
	return sprintf( '%02d', ++$tsk_n );
};
$tsk_tipos = array(
	'nuevo'       => array( __( 'Sí, es mi primera vez', '2skin' ), __( 'Agendas tu valoración. Es lo único que pagas en línea.', '2skin' ) ),
	'seguimiento' => array( __( 'Soy paciente en seguimiento', '2skin' ), __( 'Mantenimiento o tratamiento ya indicado por tu médica.', '2skin' ) ),
);
/* translators: %s: dirección de la sede. */
$tsk_sede = sprintf( __( '%s. Llega 10 minutos antes.', '2skin' ), tsk_contact( 'direccion' ) );
$tsk_wa   = tsk_whatsapp_url();
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout tsk-reserva" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php esc_attr_e( 'Confirmar y agendar', '2skin' ); ?>"
	data-tsk-reserva
	data-servicio="<?php echo esc_attr( (string) $tsk_cart['service_id'] ); ?>"
	data-tratamientos="<?php echo $tsk_treatments ? '1' : '0'; ?>"
	data-cita="<?php echo $tsk_cita ? '1' : '0'; ?>"
	data-medicas="<?php echo esc_attr( (string) wp_json_encode( (object) $tsk_doctors ) ); ?>"
	data-api="<?php echo esc_url( rest_url( 'tsk/v1/disponibilidad' ) ); ?>">

	<div class="tsk-reserva__main">

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<?php if ( $tsk_cita ) : ?>
		<section class="tsk-reserva__section" aria-labelledby="tsk-s-tipo">
			<p class="tsk-eyebrow tsk-reserva__eyebrow" id="tsk-s-tipo"><?php echo tsk_icon( 'doc', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $tsk_num() . ' · ' . __( '¿Es tu primera vez?', '2skin' ) ); ?></p>
			<fieldset class="tsk-choices">
				<legend class="screen-reader-text"><?php esc_html_e( '¿Es tu primera vez?', '2skin' ); ?></legend>
				<?php foreach ( $tsk_tipos as $tsk_key => [ $tsk_label, $tsk_note ] ) : ?>
					<label class="tsk-choice">
						<input type="radio" class="tsk-choice__input" name="tsk_tipo" value="<?php echo esc_attr( $tsk_key ); ?>" <?php checked( $tsk_tipo, $tsk_key ); ?> data-tsk-tipo>
						<span class="tsk-choice__label"><?php echo esc_html( $tsk_label ); ?></span>
						<span class="tsk-choice__note"><?php echo esc_html( $tsk_note ); ?></span>
					</label>
				<?php endforeach; ?>
			</fieldset>

			<div class="tsk-field tsk-reserva__interes" data-tsk-if="nuevo">
				<label class="tsk-field__label" for="tsk-interes"><?php esc_html_e( 'Procedimiento de interés', '2skin' ); ?></label>
				<select class="tsk-select" id="tsk-interes" name="tsk_interes" aria-describedby="tsk-interes-hint">
					<?php foreach ( Tsk_Checkout::interest_options() as $tsk_slug => $tsk_name ) : ?>
						<option value="<?php echo esc_attr( $tsk_slug ); ?>" <?php selected( $tsk_interes, $tsk_slug ); ?>><?php echo esc_html( $tsk_name ); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="tsk-field__hint" id="tsk-interes-hint"><?php esc_html_e( 'Nos ayuda a preparar tu consulta. La indicación final la define tu médica.', '2skin' ); ?></span>
			</div>

			<?php if ( $tsk_treatments ) : ?>
				<div class="tsk-reserva__alert" data-tsk-if="nuevo" role="status">
					<p class="body-s"><?php esc_html_e( 'Como paciente nuevo solo pagas la valoración. Tu reserva incluye tratamientos: los quitamos y dejamos la valoración, o elige «Soy paciente en seguimiento».', '2skin' ); ?></p>
					<?php echo tsk_button( __( 'Dejar solo la valoración', '2skin' ), Tsk_Checkout::keep_valoracion_url(), array( 'variant' => 'text', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
		</section>
		<?php endif; ?>

		<section class="tsk-reserva__section" aria-labelledby="tsk-s-datos" id="customer_details">
			<p class="tsk-eyebrow tsk-reserva__eyebrow" id="tsk-s-datos"><?php echo tsk_icon( 'user', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $tsk_num() . ' · ' . __( 'Tus datos', '2skin' ) ); ?></p>
			<div class="woocommerce-billing-fields tsk-form-grid">
				<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>
				<?php
				foreach ( $checkout->get_checkout_fields( 'billing' ) as $tsk_key => $tsk_field ) {
					woocommerce_form_field( $tsk_key, $tsk_field, $checkout->get_value( $tsk_key ) );
				}
				?>
				<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
			</div>
			<?php if ( ! is_user_logged_in() ) : ?>
				<p class="caption tsk-text-muted tsk-reserva__account"><?php esc_html_e( 'Con tu reserva creamos tu cuenta Mi piel: ahí ves tu cita, tu protocolo y tus próximos controles. Te enviamos el acceso por correo.', '2skin' ); ?></p>
				<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
			<?php endif; ?>
		</section>

		<?php if ( $tsk_history ) : ?>
			<section class="tsk-reserva__section" aria-labelledby="tsk-s-hc">
				<p class="tsk-eyebrow tsk-reserva__eyebrow" id="tsk-s-hc"><?php echo tsk_icon( 'shield', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $tsk_num() . ' · ' . __( 'Historia clínica previa', '2skin' ) ); ?></p>
				<p class="body-s tsk-text-secondary tsk-reserva__lead"><?php esc_html_e( 'Tus respuestas se tratan como datos sensibles, se envían a nuestra plataforma clínica y solo las ve tu médica tratante.', '2skin' ); ?></p>
				<div class="tsk-form-grid">
					<?php foreach ( Tsk_History::questions() as $tsk_key => [ $tsk_label, $tsk_type, $tsk_hint ] ) : ?>
						<?php $tsk_id = 'tsk-hc-' . $tsk_key; ?>
						<div class="tsk-field<?php echo 'textarea' === $tsk_type ? ' tsk-span-all' : ''; ?>">
							<label class="tsk-field__label" for="<?php echo esc_attr( $tsk_id ); ?>"><?php echo esc_html( $tsk_label ); ?></label>
							<?php if ( is_array( $tsk_type ) && $tsk_type ) : ?>
								<select class="tsk-select" id="<?php echo esc_attr( $tsk_id ); ?>" name="tsk_hc[<?php echo esc_attr( $tsk_key ); ?>]">
									<?php foreach ( $tsk_type as $tsk_value => $tsk_text ) : ?>
										<option value="<?php echo esc_attr( $tsk_value ); ?>"><?php echo esc_html( $tsk_text ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php elseif ( 'textarea' === $tsk_type ) : ?>
								<textarea class="tsk-textarea" id="<?php echo esc_attr( $tsk_id ); ?>" name="tsk_hc[<?php echo esc_attr( $tsk_key ); ?>]" rows="3" placeholder="<?php esc_attr_e( 'Escribe «ninguno» si no aplica', '2skin' ); ?>"></textarea>
							<?php else : ?>
								<input class="tsk-input" id="<?php echo esc_attr( $tsk_id ); ?>" name="tsk_hc[<?php echo esc_attr( $tsk_key ); ?>]" type="<?php echo 'date' === $tsk_type ? 'date' : 'text'; ?>" autocomplete="off">
							<?php endif; ?>
							<?php if ( $tsk_hint ) : ?>
								<span class="tsk-field__hint"><?php echo esc_html( $tsk_hint ); ?></span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $tsk_cita ) : ?>
		<section class="tsk-reserva__section" aria-labelledby="tsk-s-cita">
			<p class="tsk-eyebrow tsk-reserva__eyebrow" id="tsk-s-cita"><?php echo tsk_icon( 'calendar', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $tsk_num() . ' · ' . __( 'Tu cita', '2skin' ) ); ?></p>
			<p class="body-s tsk-text-secondary tsk-reserva__lead">
				<?php
				echo esc_html( $tsk_cart['virtual'] ? __( 'Valoración virtual por Google Meet. Te enviamos el enlace por WhatsApp y correo.', '2skin' ) : $tsk_sede );
				?>
			</p>
			<?php if ( $tsk_cart['virtual'] ) : ?>
				<p class="body-s tsk-note tsk-reserva__lead"><?php echo tsk_icon( 'doc', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Después de pagar te enviamos por WhatsApp el formulario completo para adjuntar fotos, videos y los productos de tu rutina en casa. Debes enviarlo antes de la cita.', '2skin' ); ?></p>
			<?php endif; ?>

			<?php if ( $tsk_cart['service_id'] ) : ?>
				<div class="tsk-agenda" data-tsk-agenda aria-busy="true">
					<p class="tsk-label" id="tsk-dia-label"><?php esc_html_e( 'Elige el día', '2skin' ); ?></p>
					<div class="tsk-slots tsk-slots--days" role="radiogroup" aria-labelledby="tsk-dia-label" data-tsk-days>
						<p class="body-s tsk-text-muted" data-tsk-loading><?php esc_html_e( 'Cargando horarios disponibles…', '2skin' ); ?></p>
					</div>
					<p class="tsk-label" id="tsk-hora-label"><?php esc_html_e( 'Elige la hora', '2skin' ); ?> · <span data-tsk-duration></span></p>
					<div class="tsk-slots" role="radiogroup" aria-labelledby="tsk-hora-label" data-tsk-hours></div>

					<p class="tsk-label" id="tsk-medica-label"><?php esc_html_e( 'Profesional', '2skin' ); ?></p>
					<p class="body-s tsk-text-secondary tsk-measure-52" data-tsk-if="nuevo"><?php esc_html_e( 'Te asignamos una de nuestras médicas según disponibilidad. Ambas realizan todos los procedimientos.', '2skin' ); ?></p>
					<div data-tsk-if="seguimiento">
						<div class="tsk-slots" role="radiogroup" aria-labelledby="tsk-medica-label" data-tsk-doctors></div>
						<p class="caption tsk-reserva__hint"><?php esc_html_e( 'Sigues con tu misma médica; si no tiene disponibilidad te lo informamos.', '2skin' ); ?></p>
					</div>

					<noscript><p class="body-s"><?php esc_html_e( 'Para elegir tu horario necesitas activar JavaScript. También puedes agendar por WhatsApp.', '2skin' ); ?></p></noscript>
				</div>
			<?php else : ?>
				<p class="body-s tsk-text-muted"><?php esc_html_e( 'Elige tu valoración en el resumen para ver los horarios disponibles.', '2skin' ); ?></p>
			<?php endif; ?>
			<p class="caption tsk-reserva__hint tsk-measure-64">
				<?php
				printf(
					/* translators: %s: enlace a WhatsApp. */
					esc_html__( 'Abrimos agenda cada dos semanas. Si no encuentras un horario, %s y te ponemos en lista de espera.', '2skin' ),
					$tsk_wa ? '<a href="' . esc_url( $tsk_wa ) . '" target="_blank" rel="noopener">' . esc_html__( 'escríbenos', '2skin' ) . '</a>' : esc_html__( 'escríbenos', '2skin' )
				);
				?>
			</p>
		</section>
		<?php endif; ?>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<section class="tsk-reserva__section" aria-labelledby="tsk-s-pago">
			<p class="tsk-eyebrow tsk-reserva__eyebrow" id="tsk-s-pago"><?php echo tsk_icon( 'lock', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $tsk_num() . ' · ' . __( 'Pago con Bold', '2skin' ) ); ?></p>
			<div class="tsk-reserva__pay">
				<?php woocommerce_checkout_payment(); ?>
				<label class="tsk-check tsk-reserva__check">
					<input type="checkbox" name="tsk_factura" value="1">
					<span class="tsk-check__box" aria-hidden="true"><?php echo tsk_icon( 'check', 'tsk-check__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span><?php esc_html_e( 'Necesito factura electrónica (si no la pides, recibes comprobante de pago)', '2skin' ); ?></span>
				</label>
			</div>
		</section>

		<section class="tsk-reserva__section" aria-labelledby="tsk-s-politicas">
			<p class="tsk-eyebrow tsk-reserva__eyebrow" id="tsk-s-politicas"><?php echo tsk_icon( 'shield', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $tsk_num() . ' · ' . __( 'Políticas y consentimiento', '2skin' ) ); ?></p>
			<?php if ( $tsk_cita ) : ?>
				<div class="tsk-policy">
					<p class="tsk-label"><?php esc_html_e( 'Política de cancelación', '2skin' ); ?></p>
					<p class="body-s"><?php echo esc_html( Tsk_Checkout::policy() ); ?></p>
					<p class="body-s tsk-text-secondary"><?php esc_html_e( 'Llegada tarde: 15 minutos de tolerancia. Después, la valoración se hace con menos tiempo y el procedimiento puede no realizarse ese día.', '2skin' ); ?></p>
				</div>
			<?php endif; ?>
			<?php if ( $tsk_cart['bonos'] ) : ?>
				<div class="tsk-policy">
					<p class="tsk-label"><?php esc_html_e( 'Condiciones del bono', '2skin' ); ?></p>
					<p class="body-s"><?php esc_html_e( 'Te enviamos el código por correo cuando se confirme el pago. Vigencia de 60 días desde la compra. Transferible solo antes de la valoración. Si el tratamiento indicado supera el valor del bono, se paga la diferencia. No se redime en efectivo ni tiene devolución.', '2skin' ); ?></p>
				</div>
			<?php endif; ?>
			<div class="tsk-consents">
				<?php foreach ( Tsk_Checkout::consents( true, $tsk_cita, (bool) $tsk_cart['bonos'] ) as $tsk_key => $tsk_text ) : ?>
					<label class="tsk-check tsk-reserva__check"<?php echo 'informado' === $tsk_key ? ' data-tsk-if="seguimiento"' : ''; ?>>
						<input type="checkbox" name="tsk_consent[<?php echo esc_attr( $tsk_key ); ?>]" value="1" data-tsk-consent>
						<span class="tsk-check__box" aria-hidden="true"><?php echo tsk_icon( 'check', 'tsk-check__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span><?php echo wp_kses( $tsk_text, array( 'a' => array( 'href' => true, 'target' => true, 'rel' => true ) ) ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</section>
	</div>

	<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

	<aside class="tsk-summary tsk-reserva__aside" aria-labelledby="order_review_heading">
		<h2 class="h4" id="order_review_heading"><?php esc_html_e( 'Tu reserva', '2skin' ); ?></h2>

		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

		<div id="order_review" class="woocommerce-checkout-review-order">
			<?php woocommerce_order_review(); ?>
		</div>

		<div class="tsk-summary__appointment"<?php echo $tsk_cita ? '' : ' hidden'; ?>>
			<span class="tsk-label"><?php esc_html_e( 'Cita seleccionada', '2skin' ); ?></span>
			<span class="body-s" data-tsk-summary="cita"><?php esc_html_e( 'Elige día y hora', '2skin' ); ?></span>
			<span class="body-s tsk-text-secondary" data-tsk-summary="medica" data-place="<?php echo esc_attr( $tsk_cart['virtual'] ? __( 'Google Meet', '2skin' ) : __( 'Medicenter', '2skin' ) ); ?>" data-asignada="<?php esc_attr_e( 'Médica asignada según disponibilidad', '2skin' ); ?>"></span>
		</div>

		<div class="form-row place-order tsk-summary__place">
			<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
			<?php
			$tsk_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
			echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="tsk-btn tsk-btn--primary tsk-btn--block" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $tsk_button_text ) . '" data-value="' . esc_attr( $tsk_button_text ) . '"><span class="tsk-btn__label">' . esc_html( $tsk_button_text ) . '</span></button>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WooCommerce.Commenting.CommentHooks.MissingHookComment
			?>
			<?php do_action( 'woocommerce_review_order_after_submit' ); ?>
			<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
			<p class="caption" data-tsk-hint
				data-ok="<?php esc_attr_e( 'Al confirmar recibes tu comprobante de pago y la confirmación por WhatsApp.', '2skin' ); ?>"
				data-ok-bono="<?php esc_attr_e( 'Al confirmar el pago te enviamos el código del bono por correo.', '2skin' ); ?>"
				data-consent="<?php esc_attr_e( 'Acepta las políticas para poder confirmar.', '2skin' ); ?>"
				data-slot="<?php esc_attr_e( 'Elige el día y la hora de tu cita.', '2skin' ); ?>"
				data-blocked="<?php esc_attr_e( 'Como paciente nuevo, tu reserva solo puede incluir la valoración.', '2skin' ); ?>"
				data-empty="<?php esc_attr_e( 'Añade tu valoración para confirmar.', '2skin' ); ?>"><?php esc_html_e( 'Acepta las políticas para poder confirmar.', '2skin' ); ?></p>
		</div>
	</aside>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
