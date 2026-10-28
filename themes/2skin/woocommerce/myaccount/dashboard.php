<?php
/**
 * My Account Dashboard (2skin: Mis citas, desde la agenda de Amelia).
 *
 * Las citas reprogramadas en consultorio se ven aquí porque se leen de Amelia, no del pedido.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

use TwoSkin\Core\Account;
use TwoSkin\Core\Booking\Checkout;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$tsk_citas  = class_exists( Account::class ) ? Account::appointments( get_current_user_id() ) : array( 'proximas' => array(), 'pasadas' => array() );
$tsk_labels = class_exists( Account::class ) ? Account::status_labels() : array();
$tsk_next   = $tsk_citas['proximas'][0] ?? null;
$tsk_all    = array_merge( $tsk_citas['proximas'], $tsk_citas['pasadas'] );
$tsk_wa     = tsk_whatsapp_url();
$tsk_states = array(
	'confirmada' => array( 'calendar', 'tsk-state--next' ),
	'pendiente'  => array( 'clock', 'tsk-state--muted' ),
	'realizada'  => array( 'check', 'tsk-state--ok' ),
	'cancelada'  => array( 'close', 'tsk-state--muted' ),
	'no_asistio' => array( 'close', 'tsk-state--muted' ),
);
?>

<div class="tsk-account-grid">
	<div>
		<h2 class="screen-reader-text"><?php esc_html_e( 'Mis citas', '2skin' ); ?></h2>
		<?php if ( $tsk_all ) : ?>
			<ol class="tsk-timeline">
				<?php foreach ( $tsk_all as $tsk_cita ) : ?>
					<?php [ $tsk_icon, $tsk_state_class ] = $tsk_states[ $tsk_cita['estado'] ] ?? $tsk_states['confirmada']; ?>
					<li class="tsk-timeline__item">
						<div class="tsk-timeline__when">
							<span class="caption tsk-upper"><?php echo esc_html( $tsk_cita['corta'] ); ?></span>
							<span class="body-s tsk-state <?php echo esc_attr( $tsk_state_class ); ?>"><?php echo tsk_icon( $tsk_icon, 'tsk-icon tsk-icon--xs' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $tsk_labels[ $tsk_cita['estado'] ] ?? '' ); ?></span>
						</div>
						<div class="tsk-timeline__what">
							<h3 class="body-l"><?php echo esc_html( $tsk_cita['servicio'] ); ?></h3>
							<p class="body-s tsk-text-secondary">
								<?php
								echo esc_html(
									implode(
										' · ',
										array_filter(
											array(
												$tsk_cita['hora'],
												$tsk_cita['virtual'] ? __( 'Virtual', '2skin' ) : __( 'Presencial', '2skin' ),
												$tsk_cita['medica'],
											)
										)
									)
								);
								?>
							</p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php else : ?>
			<div class="tsk-empty-box tsk-stack tsk-stack--4">
				<h3 class="h4"><?php esc_html_e( 'Aún no tienes citas.', '2skin' ); ?></h3>
				<p class="body-s tsk-text-secondary tsk-measure-46"><?php esc_html_e( 'Todo empieza con una valoración médica. Ahí definimos contigo qué tiene sentido y qué no.', '2skin' ); ?></p>
				<?php echo tsk_button( __( 'Agendar valoración', '2skin' ), tsk_url( 'reserva' ), array( 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
	</div>

	<aside class="tsk-stack tsk-stack--5" aria-label="<?php esc_attr_e( 'Próxima cita', '2skin' ); ?>">
		<?php if ( $tsk_next ) : ?>
			<div class="tsk-next tsk-on-dark">
				<p class="tsk-eyebrow tsk-inline-icon tsk-text-sage"><?php echo tsk_icon( 'calendar', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Próxima cita', '2skin' ); ?></p>
				<p class="tsk-next__date"><?php echo esc_html( $tsk_next['fecha'] ); ?></p>
				<p class="body-s tsk-text-inverse-secondary">
					<?php echo esc_html( implode( ' · ', array_filter( array( $tsk_next['hora'], $tsk_next['virtual'] ? __( 'Virtual', '2skin' ) : tsk_contact( 'direccion' ), $tsk_next['medica'] ) ) ) ); ?>
				</p>
				<p class="body-s tsk-text-inverse-secondary"><?php echo esc_html( $tsk_next['servicio'] ); ?></p>
				<?php if ( 'pendiente' === $tsk_next['estado'] ) : ?>
					<p class="caption tsk-text-inverse-secondary"><?php esc_html_e( 'El horario se confirma cuando se acredita el pago.', '2skin' ); ?></p>
				<?php endif; ?>
				<div class="tsk-actions">
					<?php
					if ( $tsk_next['virtual'] && $tsk_next['meet'] && 'confirmada' === $tsk_next['estado'] ) {
						echo tsk_button( __( 'Entrar a la videollamada', '2skin' ), $tsk_next['meet'], array( 'variant' => 'sage', 'attrs' => array( 'target' => '_blank', 'rel' => 'noopener' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					if ( $tsk_wa ) {
						/* translators: 1: fecha, 2: hora. */
						$tsk_msg = sprintf( __( 'Hola, quiero reprogramar mi cita del %1$s a las %2$s.', '2skin' ), $tsk_next['fecha'], $tsk_next['hora'] );
						echo tsk_button( __( 'Reprogramar', '2skin' ), add_query_arg( 'text', rawurlencode( $tsk_msg ), $tsk_wa ), array( 'variant' => 'text', 'attrs' => array( 'target' => '_blank', 'rel' => 'noopener' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
		<?php endif; ?>

		<div class="tsk-surface-card tsk-stack tsk-stack--3">
			<p class="tsk-eyebrow tsk-inline-icon"><?php echo tsk_icon( 'refresh', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Cambios y cancelaciones', '2skin' ); ?></p>
			<?php if ( class_exists( Checkout::class ) ) : ?>
				<p class="body-s tsk-text-secondary"><?php echo esc_html( Checkout::policy() ); ?></p>
			<?php endif; ?>
			<p class="caption">
				<?php
				echo $tsk_wa
					? esc_html__( 'Para reprogramar escríbenos por WhatsApp; la nueva hora se verá aquí cuando quede en la agenda.', '2skin' )
					: esc_html__( 'Para reprogramar comunícate con la clínica; la nueva hora se verá aquí cuando quede en la agenda.', '2skin' );
				?>
			</p>
		</div>
	</aside>
</div>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );
