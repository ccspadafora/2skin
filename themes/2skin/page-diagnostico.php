<?php
/**
 * Diagnóstico de piel (diseño: Diagnostico). Se aplica a la página con slug «diagnostico».
 *
 * Un formulario con todas las preguntas: con JS se responde una a una (assets/js/diagnostico.js); sin JS
 * se ven todas y se envían juntas. El plugin calcula la orientación sin guardar las respuestas.
 *
 * @package 2skin
 */

use TwoSkin\Core\Diagnosis;

defined( 'ABSPATH' ) || exit;

get_header();

$tsk_ready     = class_exists( Diagnosis::class );
$tsk_questions = $tsk_ready ? Diagnosis::questions() : array();
$tsk_answers   = $tsk_ready ? Diagnosis::posted() : null;
$tsk_result    = $tsk_answers ? Diagnosis::recommend( $tsk_answers ) : null;
$tsk_total     = count( $tsk_questions );
$tsk_valor     = tsk_valoracion_precio();
?>

<div class="container tsk-page tsk-dx">
	<?php if ( $tsk_result ) : ?>
		<?php
		$tsk_recs     = tsk_products_by_slug( $tsk_result['slugs'] );
		$tsk_reservar = add_query_arg(
			array_filter(
				array(
					'modalidad' => 'presencial',
					'interes'   => $tsk_recs ? $tsk_recs[0]->get_slug() : '',
				)
			),
			tsk_url( 'reserva' )
		);
		?>
		<section class="tsk-dx__result" aria-labelledby="tsk-dx-title">
			<p class="tsk-eyebrow"><?php esc_html_e( 'Orientación preliminar', '2skin' ); ?></p>
			<h1 class="h1 tsk-measure-22 tsk-dx__title" id="tsk-dx-title" tabindex="-1"><?php echo esc_html( $tsk_result['title'] ); ?></h1>
			<?php if ( $tsk_result['body'] ) : ?>
				<p class="body-l tsk-text-secondary tsk-measure-52"><?php echo esc_html( $tsk_result['body'] ); ?></p>
			<?php endif; ?>
			<?php if ( $tsk_result['care'] ) : ?>
				<p class="body-s tsk-text-secondary tsk-measure-52 tsk-dx__care"><?php echo esc_html( $tsk_result['care'] ); ?></p>
			<?php endif; ?>
			<p class="caption tsk-note tsk-dx__disclaimer"><?php echo tsk_icon( 'shield', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Esta orientación no es un diagnóstico. Solo la valoración médica define indicación, dosis y frecuencia.', '2skin' ); ?></p>

			<?php if ( $tsk_recs ) : ?>
				<div class="tsk-dx__recs">
					<?php foreach ( $tsk_recs as $tsk_product ) : ?>
						<article class="tsk-shop-card">
							<?php get_template_part( 'template-parts/components/treatment-card', null, tsk_product_card_args( $tsk_product ) ); ?>
							<span class="body-s tsk-price-rule"><?php echo esc_html( tsk_product_from_label( $tsk_product ) ); ?></span>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="tsk-actions tsk-dx__actions">
				<?php
				/* translators: %s: precio de la valoración. */
				echo tsk_button( sprintf( __( 'Agendar valoración · %s', '2skin' ), tsk_cop( $tsk_valor ) ), $tsk_reservar ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo tsk_button( __( 'Repetir el diagnóstico', '2skin' ), get_permalink(), array( 'variant' => 'secondary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo tsk_button( __( 'Ver todo el catálogo', '2skin' ), tsk_url( 'tratamientos' ), array( 'variant' => 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>

			<div class="tsk-dx__summary">
				<p class="tsk-eyebrow"><?php esc_html_e( 'Lo que respondiste', '2skin' ); ?></p>
				<dl class="tsk-summary__rows">
					<?php foreach ( $tsk_questions as $tsk_key => [ $tsk_q, , $tsk_options ] ) : ?>
						<div class="tsk-summary__row">
							<dt><?php echo esc_html( $tsk_q ); ?></dt>
							<dd><?php echo esc_html( $tsk_options[ $tsk_answers[ $tsk_key ] ] ?? '—' ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>
		</section>

	<?php elseif ( $tsk_questions ) : ?>
		<h1 class="screen-reader-text"><?php esc_html_e( 'Diagnóstico de piel', '2skin' ); ?></h1>
		<form class="tsk-dx__form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" data-tsk-dx data-total="<?php echo esc_attr( (string) $tsk_total ); ?>">
			<div class="tsk-dx__progress" aria-hidden="true" data-tsk-dx-progress>
				<div class="tsk-dx__head">
					<p class="tsk-eyebrow tsk-inline-icon"><?php echo tsk_icon( 'doc', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Diagnóstico de piel', '2skin' ); ?> · <span data-tsk-dx-step><?php echo esc_html( sprintf( /* translators: %d: total. */ __( '%d preguntas', '2skin' ), $tsk_total ) ); ?></span></span></p>
					<span class="caption" data-tsk-dx-percent></span>
				</div>
				<div class="tsk-dx__bar"><div class="tsk-dx__fill" data-tsk-dx-fill></div></div>
			</div>

			<?php $tsk_i = 0; ?>
			<?php foreach ( $tsk_questions as $tsk_key => [ $tsk_q, $tsk_help, $tsk_options ] ) : ?>
				<?php ++$tsk_i; ?>
				<fieldset class="tsk-dx__step" data-tsk-dx-question aria-describedby="tsk-dx-help-<?php echo esc_attr( $tsk_key ); ?>">
					<legend class="h2 tsk-dx__question" tabindex="-1"><?php echo esc_html( $tsk_q ); ?></legend>
					<p class="body tsk-text-secondary tsk-measure-46 tsk-dx__help" id="tsk-dx-help-<?php echo esc_attr( $tsk_key ); ?>"><?php echo esc_html( $tsk_help ); ?></p>
					<div class="tsk-dx__options">
						<?php foreach ( $tsk_options as $tsk_value => $tsk_label ) : ?>
							<label class="tsk-qopt">
								<input type="radio" class="tsk-qopt__input" name="tsk_dx[<?php echo esc_attr( $tsk_key ); ?>]" value="<?php echo esc_attr( $tsk_value ); ?>" required>
								<span class="body"><?php echo esc_html( $tsk_label ); ?></span>
								<?php echo tsk_icon( 'arrow', 'tsk-icon tsk-text-sage tsk-qopt__arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endforeach; ?>

			<div class="tsk-dx__foot">
				<button type="button" class="tsk-dx__back" data-tsk-dx-back hidden><?php esc_html_e( 'Anterior', '2skin' ); ?></button>
				<button type="submit" class="tsk-btn tsk-btn--primary" data-tsk-dx-submit><span class="tsk-btn__label"><?php esc_html_e( 'Ver mi orientación', '2skin' ); ?></span></button>
				<p class="caption tsk-dx__note"><?php esc_html_e( 'Esta orientación no es un diagnóstico médico. No guardamos tus respuestas.', '2skin' ); ?></p>
			</div>
		</form>
	<?php endif; ?>
</div>

<?php
get_footer();
