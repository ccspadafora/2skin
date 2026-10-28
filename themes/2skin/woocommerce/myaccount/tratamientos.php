<?php
/**
 * Mi piel · Tratamientos indicados por la médica tratante (seguimiento).
 *
 * Plantilla propia de 2skin (la carga el plugin en el endpoint tratamientos-indicados).
 *
 * @package 2skin
 *
 * @var array $indicaciones Lista de product (WC_Product), opciones y vence.
 */

defined( 'ABSPATH' ) || exit;
?>

<h2 class="screen-reader-text"><?php esc_html_e( 'Tratamientos indicados', '2skin' ); ?></h2>
<?php if ( $indicaciones ) : ?>
	<p class="body tsk-text-secondary tsk-measure-52 tsk-account__intro"><?php esc_html_e( 'Estos son los tratamientos que tu médica dejó habilitados después de tu valoración. Puedes reservarlos directamente.', '2skin' ); ?></p>
	<ul class="tsk-indications">
		<?php foreach ( $indicaciones as $tsk_row ) : ?>
			<?php $tsk_product = $tsk_row['product']; ?>
			<li class="tsk-indications__row">
				<div class="tsk-stack tsk-stack--2">
					<h3 class="body-l"><?php echo esc_html( $tsk_product->get_name() ); ?></h3>
					<p class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_row['opciones'] ); ?></p>
				</div>
				<p class="body-s tsk-text-secondary">
					<?php
					echo $tsk_row['vence']
						/* translators: %s: fecha. */
						? esc_html( sprintf( __( 'Vigente hasta el %s', '2skin' ), $tsk_row['vence'] ) )
						: esc_html__( 'Sin fecha de vencimiento', '2skin' );
					?>
				</p>
				<?php echo tsk_button( __( 'Reservar', '2skin' ), $tsk_product->get_permalink(), array( 'variant' => 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else : ?>
	<div class="tsk-empty-box tsk-stack tsk-stack--4">
		<h3 class="h4"><?php esc_html_e( 'Aún no tienes tratamientos indicados.', '2skin' ); ?></h3>
		<p class="body-s tsk-text-secondary tsk-measure-46"><?php esc_html_e( 'Después de tu valoración, tu médica habilita aquí los tratamientos que te indicó para que los reserves sin repetir el proceso.', '2skin' ); ?></p>
		<?php echo tsk_button( __( 'Agendar valoración', '2skin' ), tsk_url( 'reserva' ), array( 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
<?php endif; ?>
