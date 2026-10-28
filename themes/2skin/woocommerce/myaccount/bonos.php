<?php
/**
 * Mi piel · Bonos de regalo comprados por la paciente, con saldo y vencimiento.
 *
 * Plantilla propia de 2skin (la carga el plugin en el endpoint mis-bonos).
 *
 * @package 2skin
 *
 * @var array $bonos Bonos (filas del repositorio de bonos).
 */

use TwoSkin\Core\Gifts\Repository;
use TwoSkin\Core\Patients\Repository as Patients;

defined( 'ABSPATH' ) || exit;
?>

<h2 class="screen-reader-text"><?php esc_html_e( 'Mis bonos', '2skin' ); ?></h2>
<?php if ( $bonos ) : ?>
	<ul class="tsk-indications">
		<?php foreach ( $bonos as $tsk_bono ) : ?>
			<li class="tsk-indications__row<?php echo $tsk_bono['usable'] ? '' : ' is-off'; ?>">
				<div class="tsk-stack tsk-stack--2">
					<span class="tsk-codes__code"><?php echo esc_html( $tsk_bono['codigo'] ); ?></span>
					<span class="body-s tsk-text-secondary">
						<?php
						/* translators: 1: saldo, 2: valor inicial. */
						echo esc_html( sprintf( __( 'Saldo %1$s de %2$s', '2skin' ), tsk_cop( $tsk_bono['saldo'] ), tsk_cop( $tsk_bono['valor'] ) ) );
						?>
					</span>
				</div>
				<p class="body-s tsk-text-secondary">
					<?php
					/* translators: %s: fecha. */
					echo esc_html( sprintf( __( 'Vence el %s', '2skin' ), Patients::format_date( $tsk_bono['vence'] ) ) );
					?>
				</p>
				<span class="body-s tsk-state <?php echo $tsk_bono['usable'] ? 'tsk-state--ok' : 'tsk-state--muted'; ?>"><?php echo esc_html( Repository::status_label( $tsk_bono ) ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<p class="caption tsk-orders__note"><?php esc_html_e( 'Quien tenga el código puede usarlo al pagar una reserva en línea o en el consultorio. Compártelo solo con la persona a quien lo regalas.', '2skin' ); ?></p>
<?php else : ?>
	<div class="tsk-empty-box tsk-stack tsk-stack--4">
		<h3 class="h4"><?php esc_html_e( 'No has comprado bonos de regalo.', '2skin' ); ?></h3>
		<p class="body-s tsk-text-secondary tsk-measure-46"><?php esc_html_e( 'Un bono sirve para valoraciones y tratamientos, en línea o en el consultorio.', '2skin' ); ?></p>
		<?php echo tsk_button( __( 'Regalar un bono', '2skin' ), tsk_url( 'bonos' ), array( 'variant' => 'secondary', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
<?php endif; ?>
