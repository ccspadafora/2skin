<?php
/**
 * Bonos de regalo (diseño: Bonos). Se aplica a la página con slug «bonos».
 *
 * Cada tarjeta añade al carrito el producto «Bono de regalo» con su valor (tsk_bono_valor); el valor
 * libre se escribe. El plugin valida el valor y emite el código cuando el pedido queda pagado.
 *
 * @package 2skin
 */

use TwoSkin\Core\Gifts\Gifts;

defined( 'ABSPATH' ) || exit;

get_header();

$tsk_gifts   = class_exists( Gifts::class ) && Gifts::product_id();
$tsk_limits  = $tsk_gifts ? Gifts::limits() : array( 'presets' => array(), 'min' => 500000, 'max' => 10000000 );
$tsk_in_cart = array();
if ( $tsk_gifts && WC()->cart ) {
	foreach ( WC()->cart->get_cart() as $tsk_item ) {
		if ( ! empty( $tsk_item['tsk_bono_valor'] ) ) {
			$tsk_in_cart[] = (int) $tsk_item['tsk_bono_valor'];
		}
	}
}
$tsk_details = array(
	500000  => __( 'Cubre la valoración (%1$s) y deja %2$s para el tratamiento que se indique.', '2skin' ),
	1000000 => __( 'Cubre la valoración y buena parte de un tratamiento, por ejemplo labios o hidratación.', '2skin' ),
);
$tsk_steps = array(
	array( 'gift', __( 'Compras el bono', '2skin' ), __( 'Recibes el bono digital con el valor y la vigencia de 60 días.', '2skin' ) ),
	/* translators: %s: precio de la valoración. */
	array( 'calendar', __( 'Se agenda la valoración', '2skin' ), sprintf( __( 'El bono cubre la consulta médica (%s) y el saldo se abona al tratamiento que se indique.', '2skin' ), tsk_cop( tsk_valoracion_precio() ) ) ),
	array( 'doc', __( 'Se define el plan', '2skin' ), __( 'Con criterio médico, no con lo que estaba en promoción esa semana.', '2skin' ) ),
);

/**
 * Formulario de una tarjeta de bono.
 *
 * @param int|null $value     Valor fijo, o null para valor libre.
 * @param bool     $added     Si ya está en el carrito.
 * @param array    $limits    Mínimo y máximo.
 */
$tsk_card_form = static function ( ?int $value, bool $added, array $limits ): void {
	?>
	<form class="tsk-gift__form" method="post" action="<?php echo esc_url( get_permalink() ); ?>">
		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( (string) Gifts::product_id() ); ?>">
		<?php if ( null === $value ) : ?>
			<div class="tsk-field">
				<label class="tsk-field__label" for="tsk-bono-libre"><?php esc_html_e( 'Valor del bono', '2skin' ); ?></label>
				<input class="tsk-input" id="tsk-bono-libre" name="tsk_bono_valor" type="number" inputmode="numeric" required
					min="<?php echo esc_attr( (string) $limits['min'] ); ?>" max="<?php echo esc_attr( (string) $limits['max'] ); ?>" step="1000"
					placeholder="<?php echo esc_attr( (string) $limits['min'] ); ?>" aria-describedby="tsk-bono-libre-hint">
				<span class="tsk-field__hint" id="tsk-bono-libre-hint"><?php echo esc_html( sprintf( /* translators: 1: mínimo, 2: máximo. */ __( 'Entre %1$s y %2$s, en miles de pesos.', '2skin' ), tsk_cop( $limits['min'] ), tsk_cop( $limits['max'] ) ) ); ?></span>
			</div>
		<?php else : ?>
			<input type="hidden" name="tsk_bono_valor" value="<?php echo esc_attr( (string) $value ); ?>">
		<?php endif; ?>
		<?php if ( $added ) : ?>
			<a class="tsk-added" href="<?php echo esc_url( tsk_url( 'carrito' ) ); ?>"><?php echo tsk_icon( 'check', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Añadido · Ver tu reserva', '2skin' ); ?><span class="tsk-added__arrow" aria-hidden="true">→</span></a>
		<?php else : ?>
			<button type="submit" class="tsk-btn tsk-btn--secondary tsk-btn--block"><span class="tsk-btn__label"><?php esc_html_e( 'Añadir al carrito', '2skin' ); ?></span></button>
		<?php endif; ?>
	</form>
	<?php
};
?>

<div class="container tsk-page">
	<?php tsk_breadcrumbs( array(), __( 'Bonos de regalo', '2skin' ) ); ?>
	<?php
	if ( function_exists( 'woocommerce_output_all_notices' ) ) {
		woocommerce_output_all_notices();
	}
	?>

	<div class="tsk-half tsk-gift-hero">
		<div class="tsk-stack tsk-stack--5">
			<p class="tsk-eyebrow tsk-inline-icon"><?php echo tsk_icon( 'gift', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Bonos de regalo', '2skin' ); ?></p>
			<h1 class="h1 tsk-measure-20"><?php esc_html_e( 'Se regala un valor, no un procedimiento.', '2skin' ); ?></h1>
			<p class="body-l tsk-text-secondary tsk-measure-46"><?php esc_html_e( 'Quien lo recibe pasa primero por valoración y decide con criterio médico qué hacer con él. Nadie debería recibir de regalo una indicación que no le corresponde.', '2skin' ); ?></p>
			<ul class="tsk-gift-facts">
				<li class="body-s tsk-inline-icon"><?php echo tsk_icon( 'clock', 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Vigencia de 60 días desde la compra', '2skin' ); ?></li>
				<li class="body-s tsk-inline-icon"><?php echo tsk_icon( 'doc', 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( sprintf( /* translators: %s: valor mínimo. */ __( 'Desde %s · valor libre por encima', '2skin' ), tsk_cop( $tsk_limits['min'] ) ) ); ?></li>
				<li class="body-s tsk-inline-icon"><?php echo tsk_icon( 'check', 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Transferible antes de la valoración', '2skin' ); ?></li>
			</ul>
		</div>
		<?php echo tsk_media( has_post_thumbnail() ? (int) get_post_thumbnail_id() : 'giftcard.jpg', array( 'alt' => __( 'Bono de regalo 2Skin', '2skin' ), 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<section class="tsk-gift-section" aria-labelledby="tsk-gift-valor">
		<h2 class="h3 tsk-gift-section__title" id="tsk-gift-valor"><?php esc_html_e( 'Elige el valor', '2skin' ); ?></h2>
		<?php if ( ! $tsk_gifts ) : ?>
			<p class="body-s tsk-text-secondary"><?php esc_html_e( 'Muy pronto podrás comprar bonos en línea. Mientras tanto, escríbenos.', '2skin' ); ?></p>
		<?php else : ?>
			<div class="tsk-gift-grid">
				<?php foreach ( $tsk_limits['presets'] as $tsk_value ) : ?>
					<article class="tsk-surface-card tsk-gift">
						<?php echo tsk_icon( 'gift', 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<h3 class="tsk-gift__price"><?php echo esc_html( tsk_cop( $tsk_value ) ); ?></h3>
						<p class="body-s tsk-text-secondary">
							<?php
							$tsk_detail = $tsk_details[ $tsk_value ] ?? __( 'Para la valoración y el tratamiento que se indique.', '2skin' );
							echo esc_html( sprintf( $tsk_detail, tsk_cop( tsk_valoracion_precio() ), tsk_cop( $tsk_value - tsk_valoracion_precio() ) ) );
							?>
						</p>
						<?php $tsk_card_form( (int) $tsk_value, in_array( (int) $tsk_value, $tsk_in_cart, true ), $tsk_limits ); ?>
					</article>
				<?php endforeach; ?>
				<article class="tsk-surface-card tsk-gift">
					<?php echo tsk_icon( 'gift', 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<h3 class="tsk-gift__price"><?php echo esc_html( sprintf( /* translators: %s: valor mínimo. */ __( 'Desde %s', '2skin' ), tsk_cop( $tsk_limits['min'] ) ) ); ?></h3>
					<p class="body-s tsk-text-secondary"><?php echo esc_html( sprintf( /* translators: %s: valor mínimo. */ __( 'Desde %s. El valor se define al comprar.', '2skin' ), tsk_cop( $tsk_limits['min'] ) ) ); ?></p>
					<?php $tsk_card_form( null, (bool) array_diff( $tsk_in_cart, array_map( 'intval', $tsk_limits['presets'] ) ), $tsk_limits ); ?>
				</article>
			</div>
		<?php endif; ?>
	</section>

	<section class="tsk-gift-section tsk-half" aria-labelledby="tsk-gift-pasos">
		<div>
			<p class="tsk-eyebrow"><?php esc_html_e( 'Cómo funciona', '2skin' ); ?></p>
			<h2 class="h3 tsk-measure-20 tsk-head-gap-sm" id="tsk-gift-pasos"><?php esc_html_e( 'Tres pasos, y la consulta define el resto.', '2skin' ); ?></h2>
		</div>
		<div>
			<ol class="tsk-gift-steps">
				<?php foreach ( $tsk_steps as [ $tsk_icon, $tsk_title, $tsk_body ] ) : ?>
					<li>
						<?php echo tsk_icon( $tsk_icon, 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<div>
							<h3 class="body tsk-weight-500"><?php echo esc_html( $tsk_title ); ?></h3>
							<p class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_body ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
			<p class="caption tsk-gift-terms"><?php esc_html_e( 'Transferible solo antes de la valoración. Si el tratamiento indicado supera el valor del bono, pagas la diferencia. No se redime en efectivo ni tiene devolución. Cualquier procedimiento requiere valoración médica previa.', '2skin' ); ?></p>
		</div>
	</section>
</div>

<?php
get_footer();
