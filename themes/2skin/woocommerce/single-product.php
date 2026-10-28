<?php
/**
 * Ficha del tratamiento (diseño: Tratamiento). Reemplaza la página de producto de WooCommerce.
 *
 * El contenido clínico sale de la «Ficha del tratamiento» (plugin 2Skin Core); las opciones son las variaciones.
 * Primera vez → valoración. Paciente en seguimiento → añade la opción al carrito (lo valida el plugin).
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package 2skin
 * @version 1.6.4
 */

use TwoSkin\Core\Cases;
use TwoSkin\Core\Catalog;
use TwoSkin\Core\Patients\Follow_Up;
use TwoSkin\Core\Patients\Repository;

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

while ( have_posts() ) :
	the_post();
	$tsk_product = wc_get_product( get_the_ID() );
	if ( ! $tsk_product ) {
		continue;
	}

	$tsk_is_val   = tsk_is_valoracion( $tsk_product );
	$tsk_follow   = $tsk_is_val ? null : tsk_follow_up( $tsk_product );
	$tsk_options  = $tsk_product->is_type( 'variable' ) ? tsk_product_options( $tsk_product ) : array();
	$tsk_first    = $tsk_options[0] ?? null;
	$tsk_cats     = get_the_terms( $tsk_product->get_id(), 'product_cat' );
	$tsk_cat      = $tsk_cats && ! is_wp_error( $tsk_cats ) ? $tsk_cats[0]->name : '';
	$tsk_icon     = (string) tsk_ficha( $tsk_product, 'icono' );
	$tsk_demanda  = (string) tsk_ficha( $tsk_product, 'demanda' );
	$tsk_facts    = (array) tsk_ficha( $tsk_product, 'facts' );
	$tsk_evalua   = (array) tsk_ficha( $tsk_product, 'evalua' );
	$tsk_que      = (string) tsk_ficha( $tsk_product, 'que' );
	$tsk_zonas    = (string) tsk_ficha( $tsk_product, 'zonas_texto' );
	$tsk_no       = (array) tsk_ficha( $tsk_product, 'no_indicado' );
	$tsk_contra   = (array) tsk_ficha( $tsk_product, 'contra' );
	$tsk_pasos    = (array) tsk_ficha( $tsk_product, 'protocolo' );
	$tsk_etapas   = (array) tsk_ficha( $tsk_product, 'etapas' );
	$tsk_marcas   = (array) tsk_ficha( $tsk_product, 'marcas' );
	$tsk_combos   = (array) tsk_ficha( $tsk_product, 'combos' );
	$tsk_incluye  = (array) tsk_ficha( $tsk_product, 'incluye' );
	$tsk_avisos   = (array) tsk_ficha( $tsk_product, 'avisos' );
	$tsk_price    = tsk_cop( tsk_valoracion_precio() );
	$tsk_case     = class_exists( Cases::class ) ? ( Cases::published( $tsk_product->get_id(), 1 )[0] ?? null ) : null;
	$tsk_faq      = class_exists( Catalog::class ) ? Catalog::faq( $tsk_product->get_id() ) : array();
	$tsk_related  = class_exists( Catalog::class ) ? Catalog::related( $tsk_product ) : array();
	$tsk_gallery  = $tsk_product->get_gallery_image_ids();
	$tsk_extras   = array(
		array( 'clinica-1.jpg', __( 'Preparación del insumo en consulta', '2skin' ) ),
		array( 'clinica-4.jpg', __( 'Producto y trazabilidad de lote', '2skin' ) ),
		array( 'textura-1.jpg', __( 'Detalle de calidad de piel', '2skin' ) ),
	);

	// Etiquetas de los botones: el JS las actualiza al cambiar de opción (data-*).
	$tsk_book_label = static fn( array $o ): string => sprintf(
		/* translators: 1: modalidad, 2: precio. */
		__( 'Agendar valoración %1$s · %2$s', '2skin' ),
		mb_strtolower( $o['name'] ),
		tsk_cop( $o['price'] )
	);
	$tsk_add_label  = static fn( array $o ): string => $o['price']
		/* translators: %s: precio. */
		? sprintf( __( 'Soy paciente en seguimiento · reservar %s', '2skin' ), tsk_cop( $o['price'] ) )
		: '';
	?>

	<div class="container tsk-page tsk-pdp-page">
		<?php tsk_breadcrumbs( array( array( __( 'Tratamientos', '2skin' ), wc_get_page_permalink( 'shop' ) ) ) ); ?>
		<?php woocommerce_output_all_notices(); ?>

		<div class="tsk-pdp">
			<div class="tsk-stack tsk-stack--4">
				<?php echo tsk_media( (int) $tsk_product->get_image_id(), array( 'alt' => $tsk_product->get_name(), 'size' => 'tsk-landscape', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="tsk-pdp__thumbs">
					<?php foreach ( $tsk_extras as $tsk_i => [ $tsk_file, $tsk_alt ] ) : ?>
						<?php echo tsk_media( isset( $tsk_gallery[ $tsk_i ] ) ? (int) $tsk_gallery[ $tsk_i ] : $tsk_file, array( 'ratio' => 'square', 'alt' => $tsk_alt, 'size' => 'medium_large' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="tsk-stack tsk-pdp__info">
				<div class="tsk-card__badges">
					<?php if ( $tsk_cat ) : ?>
						<span class="tsk-badge"><?php echo esc_html( $tsk_cat ); ?></span>
					<?php endif; ?>
					<?php if ( $tsk_demanda ) : ?>
						<span class="tsk-badge tsk-badge--sage"><?php echo esc_html( $tsk_demanda ); ?></span>
					<?php endif; ?>
				</div>
				<h1 class="h1 tsk-pdp__title">
					<?php
					if ( $tsk_icon ) {
						echo tsk_icon( $tsk_icon, 'tsk-icon tsk-icon--lg tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					the_title();
					?>
				</h1>
				<div class="body-l tsk-text-secondary tsk-measure-44"><?php echo wp_kses_post( wpautop( $tsk_product->get_description() ) ); ?></div>

				<?php if ( $tsk_options ) : ?>
					<?php if ( $tsk_is_val ) : ?>
						<form class="tsk-stack tsk-stack--3" method="get" action="<?php echo esc_url( tsk_url( 'reserva' ) ); ?>" data-tsk-options>
					<?php else : ?>
						<form class="tsk-stack tsk-stack--3" method="post" action="<?php echo esc_url( $tsk_product->get_permalink() ); ?>" data-tsk-options>
							<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( (string) $tsk_product->get_id() ); ?>">
							<input type="hidden" name="product_id" value="<?php echo esc_attr( (string) $tsk_product->get_id() ); ?>">
							<input type="hidden" name="quantity" value="1">
					<?php endif; ?>
						<fieldset class="tsk-options">
							<legend class="tsk-eyebrow"><?php esc_html_e( 'Precios de referencia · sin IVA', '2skin' ); ?></legend>
							<?php foreach ( $tsk_options as $tsk_i => $tsk_option ) : ?>
								<label class="tsk-option">
									<input
										type="radio"
										class="tsk-option__input"
										name="<?php echo $tsk_is_val ? 'modalidad' : 'variation_id'; ?>"
										value="<?php echo esc_attr( $tsk_is_val ? $tsk_option['slug'] : (string) $tsk_option['id'] ); ?>"
										data-book-label="<?php echo esc_attr( $tsk_is_val ? $tsk_book_label( $tsk_option ) : '' ); ?>"
										data-add-label="<?php echo esc_attr( $tsk_add_label( $tsk_option ) ); ?>"
										<?php checked( 0, $tsk_i ); ?>
									>
									<span class="tsk-option__dot" aria-hidden="true"></span>
									<span class="tsk-option__text">
										<span class="body"><?php echo esc_html( $tsk_option['name'] ); ?></span>
										<?php if ( $tsk_option['detail'] ) : ?>
											<span class="body-s tsk-text-secondary"><?php echo esc_html( wp_strip_all_tags( $tsk_option['detail'] ) ); ?></span>
										<?php endif; ?>
									</span>
									<span class="body tsk-nowrap"><?php echo esc_html( $tsk_option['price'] ? tsk_cop( $tsk_option['price'] ) : __( 'En valoración', '2skin' ) ); ?></span>
								</label>
							<?php endforeach; ?>
						</fieldset>

						<div class="tsk-stack tsk-stack--3">
							<?php if ( $tsk_is_val ) : ?>
								<button type="submit" class="tsk-btn tsk-btn--primary tsk-btn--block" data-tsk-book><span class="tsk-btn__label"><?php echo esc_html( $tsk_book_label( $tsk_first ) ); ?></span></button>
							<?php else : ?>
								<?php
								/* translators: %s: precio de la valoración. */
								echo tsk_button( sprintf( __( 'Primera vez · agendar valoración · %s', '2skin' ), $tsk_price ), add_query_arg( 'interes', $tsk_product->get_slug(), tsk_url( 'reserva' ) ), array( 'class' => 'tsk-btn--block' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
								<button type="submit" class="tsk-btn tsk-btn--secondary tsk-btn--block" data-tsk-add<?php echo $tsk_first['price'] ? '' : ' hidden'; ?>><span class="tsk-btn__label"><?php echo esc_html( $tsk_add_label( $tsk_first ) ); ?></span></button>
							<?php endif; ?>
							<?php if ( ! $tsk_is_val && tsk_in_cart( $tsk_product ) ) : ?>
								<a class="tsk-added" href="<?php echo esc_url( tsk_url( 'carrito' ) ); ?>">
									<?php echo tsk_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php esc_html_e( 'Añadido a tu reserva · continuar', '2skin' ); ?>
									<?php echo tsk_icon( 'arrow', 'tsk-icon tsk-added__arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php endif; ?>
						</div>
					</form>
				<?php endif; ?>

				<div class="tsk-shield">
					<?php echo tsk_icon( 'shield', 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<p class="body-s">
						<?php
						if ( $tsk_follow ) {
							echo esc_html(
								$tsk_follow['vence']
									/* translators: 1: opciones indicadas, 2: fecha. */
									? sprintf( __( 'Estás en seguimiento para este tratamiento (%1$s) hasta el %2$s. Puedes reservarlo directamente en línea.', '2skin' ), mb_strtolower( Follow_Up::options_label( $tsk_follow ) ), Repository::format_date( $tsk_follow['vence'] ) )
									/* translators: %s: opciones indicadas. */
									: sprintf( __( 'Estás en seguimiento para este tratamiento (%s). Puedes reservarlo directamente en línea.', '2skin' ), mb_strtolower( Follow_Up::options_label( $tsk_follow ) ) )
							);
						} elseif ( $tsk_is_val ) {
							esc_html_e( 'La valoración se abona completa a tu tratamiento si lo inicias dentro del primer mes. Pagas el 100% al reservar; no hay devoluciones de dinero, pero puedes reprogramar con 24 horas de anticipación.', '2skin' );
						} else {
							esc_html_e( 'Si es tu primera vez, empezamos por la valoración: en consulta definimos si este tratamiento es para ti. Solo pacientes en seguimiento pueden reservarlo directamente en línea.', '2skin' );
						}
						?>
					</p>
				</div>

				<?php if ( $tsk_facts ) : ?>
					<dl class="tsk-facts">
						<?php foreach ( $tsk_facts as [ $tsk_k, $tsk_v ] ) : ?>
							<div class="tsk-facts__row"><dt><?php echo esc_html( $tsk_k ); ?></dt><dd><?php echo esc_html( $tsk_v ); ?></dd></div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $tsk_evalua ) : ?>
			<section class="tsk-pdp-section tsk-pdp-section--rule tsk-half">
				<div>
					<p class="tsk-eyebrow"><?php esc_html_e( 'Antes de proponer', '2skin' ); ?></p>
					<h2 class="h3 tsk-measure-20 tsk-head-gap-sm"><?php esc_html_e( 'Qué evaluamos en tu caso.', '2skin' ); ?></h2>
				</div>
				<ol class="tsk-numbered">
					<?php foreach ( $tsk_evalua as $tsk_i => $tsk_item ) : ?>
						<li><span class="caption tsk-text-muted"><?php echo esc_html( sprintf( '%02d', $tsk_i + 1 ) ); ?></span><span class="body tsk-text-secondary"><?php echo esc_html( $tsk_item ); ?></span></li>
					<?php endforeach; ?>
				</ol>
			</section>
		<?php endif; ?>

		<?php if ( $tsk_que ) : ?>
			<section class="tsk-pdp-section tsk-half">
				<div class="tsk-stack tsk-stack--4">
					<p class="tsk-eyebrow"><?php esc_html_e( 'Qué es', '2skin' ); ?></p>
					<p class="body tsk-measure-52"><?php echo esc_html( $tsk_que ); ?></p>
					<?php if ( $tsk_zonas ) : ?>
						<div class="tsk-price-rule">
							<span class="tsk-label"><?php esc_html_e( 'Zonas', '2skin' ); ?></span>
							<span class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_zonas ); ?></span>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $tsk_no ) : ?>
					<div class="tsk-surface-card tsk-surface-card--lg">
						<p class="tsk-eyebrow"><?php esc_html_e( 'No está indicado si', '2skin' ); ?></p>
						<ul class="tsk-dash-list">
							<?php foreach ( $tsk_no as $tsk_item ) : ?>
								<li class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<?php if ( $tsk_contra ) : ?>
			<section class="tsk-pdp-section">
				<p class="tsk-eyebrow"><?php esc_html_e( 'Contraindicaciones', '2skin' ); ?></p>
				<h2 class="h3 tsk-measure-26 tsk-head-gap"><?php esc_html_e( 'Cuándo no lo aplicamos.', '2skin' ); ?></h2>
				<?php get_template_part( 'template-parts/components/ruled-groups', null, array( 'groups' => $tsk_contra ) ); ?>
			</section>
		<?php endif; ?>

		<?php if ( $tsk_pasos ) : ?>
			<section class="tsk-pdp-section">
				<p class="tsk-eyebrow"><?php esc_html_e( 'El proceso', '2skin' ); ?></p>
				<h2 class="h3 tsk-measure-24 tsk-head-gap"><?php esc_html_e( 'Cómo se desarrolla el protocolo.', '2skin' ); ?></h2>
				<ol class="tsk-steps">
					<?php foreach ( $tsk_pasos as [ $tsk_when, $tsk_title, $tsk_body ] ) : ?>
						<li class="tsk-steps__item">
							<div class="tsk-steps__top">
								<span class="tsk-label"><?php echo esc_html( $tsk_when ); ?></span>
								<?php echo tsk_icon( 'clock', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							<h3 class="body tsk-weight-500"><?php echo esc_html( $tsk_title ); ?></h3>
							<p class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_body ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>
		<?php endif; ?>

		<?php if ( $tsk_etapas ) : ?>
			<section class="tsk-pdp-section">
				<p class="tsk-eyebrow"><?php esc_html_e( 'Recuperación', '2skin' ); ?></p>
				<h2 class="h3 tsk-measure-26 tsk-head-gap"><?php esc_html_e( 'Qué esperar según lo que tratamos.', '2skin' ); ?></h2>
				<?php get_template_part( 'template-parts/components/ruled-groups', null, array( 'groups' => $tsk_etapas ) ); ?>
			</section>
		<?php endif; ?>

		<?php if ( $tsk_marcas ) : ?>
			<?php
			$tsk_eleccion = (string) tsk_ficha( $tsk_product, 'eleccion' );
			$tsk_present  = (string) tsk_ficha( $tsk_product, 'presentacion' );
			$tsk_traza    = (string) tsk_ficha( $tsk_product, 'trazabilidad' );
			?>
			<section class="tsk-pdp-section tsk-half">
				<div class="tsk-stack tsk-stack--4">
					<p class="tsk-eyebrow"><?php esc_html_e( 'Producto y marcas', '2skin' ); ?></p>
					<h2 class="h3 tsk-measure-20"><?php esc_html_e( 'Qué usamos y por qué.', '2skin' ); ?></h2>
					<?php foreach ( array( $tsk_eleccion, $tsk_present ) as $tsk_text ) : ?>
						<?php if ( $tsk_text ) : ?>
							<p class="body-s tsk-text-secondary tsk-measure-46"><?php echo esc_html( $tsk_text ); ?></p>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php if ( $tsk_traza ) : ?>
						<p class="body-s tsk-measure-46"><?php echo esc_html( $tsk_traza ); ?></p>
					<?php endif; ?>
					<?php if ( $tsk_combos ) : ?>
						<div class="tsk-price-rule">
							<span class="tsk-label"><?php esc_html_e( 'Se combina con', '2skin' ); ?></span>
							<div class="tsk-card__badges">
								<?php foreach ( $tsk_combos as $tsk_combo ) : ?>
									<span class="tsk-badge"><?php echo esc_html( $tsk_combo ); ?></span>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<ul class="tsk-brands">
					<?php foreach ( $tsk_marcas as [ $tsk_name, $tsk_lab, $tsk_invima, $tsk_uso ] ) : ?>
						<li>
							<div class="tsk-brands__head">
								<span class="body tsk-weight-500"><?php echo esc_html( $tsk_name ); ?></span>
								<span class="caption tsk-text-muted"><?php echo esc_html( $tsk_lab ); ?></span>
							</div>
							<span class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_uso ); ?></span>
							<?php if ( $tsk_invima ) : ?>
								<span class="caption tsk-text-muted"><?php echo esc_html( 'INVIMA ' . $tsk_invima ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php if ( $tsk_incluye || $tsk_avisos ) : ?>
			<section class="tsk-pdp-section tsk-half">
				<?php
				$tsk_boxes = array(
					array( 'check', __( 'Qué incluye', '2skin' ), $tsk_incluye, true ),
					array( 'shield', __( 'Consideraciones clínicas', '2skin' ), $tsk_avisos, false ),
				);
				foreach ( $tsk_boxes as [ $tsk_box_icon, $tsk_box_title, $tsk_box_items, $tsk_box_arrow ] ) :
					if ( ! $tsk_box_items ) {
						continue;
					}
					?>
					<div class="tsk-surface-card tsk-surface-card--lg">
						<p class="tsk-eyebrow tsk-inline-icon"><?php echo tsk_icon( $tsk_box_icon, 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $tsk_box_title ); ?></p>
						<ul class="<?php echo $tsk_box_arrow ? 'tsk-arrow-list' : 'tsk-dot-list'; ?>">
							<?php foreach ( $tsk_box_items as $tsk_item ) : ?>
								<li class="body-s">
									<?php
									if ( $tsk_box_arrow ) {
										echo tsk_icon( 'arrow', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									?>
									<span><?php echo esc_html( $tsk_item ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</section>
		<?php endif; ?>

		<?php if ( $tsk_case ) : ?>
			<section class="tsk-pdp-section tsk-half">
				<div class="tsk-stack tsk-stack--5">
					<p class="tsk-eyebrow"><?php esc_html_e( 'Un caso', '2skin' ); ?></p>
					<h2 class="h3 tsk-measure-20"><?php echo esc_html( $tsk_case['title'] ); ?></h2>
					<p class="body-s tsk-text-secondary tsk-measure-42"><?php echo esc_html( $tsk_case['body'] ); ?></p>
					<?php echo tsk_button( __( 'Ver más casos', '2skin' ), tsk_url( 'resultados' ), array( 'variant' => 'text', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="tsk-stack tsk-stack--2">
					<?php get_template_part( 'template-parts/components/before-after', null, $tsk_case ); ?>
					<?php if ( $tsk_case['illustrative'] ) : ?>
						<p class="caption tsk-text-muted"><?php esc_html_e( 'Imagen ilustrativa. Los casos reales se publicarán con consentimiento escrito.', '2skin' ); ?></p>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $tsk_faq ) : ?>
			<section class="tsk-pdp-section tsk-half">
				<div class="tsk-stack tsk-stack--5">
					<div>
						<p class="tsk-eyebrow"><?php esc_html_e( 'Dudas frecuentes', '2skin' ); ?></p>
						<h2 class="h3 tsk-measure-18 tsk-head-gap-sm"><?php esc_html_e( 'Lo que más nos preguntan.', '2skin' ); ?></h2>
					</div>
					<?php echo tsk_button( __( 'Todas las preguntas', '2skin' ), tsk_url( 'preguntas' ), array( 'variant' => 'text', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<?php get_template_part( 'template-parts/components/accordion', null, array( 'items' => $tsk_faq, 'open' => 0 ) ); ?>
			</section>
		<?php endif; ?>

		<?php if ( $tsk_related ) : ?>
			<section class="tsk-pdp-section tsk-pdp-section--rule tsk-pdp-section--last">
				<h2 class="h3 tsk-head-gap"><?php esc_html_e( 'Protocolos relacionados', '2skin' ); ?></h2>
				<div class="tsk-catalog">
					<?php foreach ( $tsk_related as $tsk_rel ) : ?>
						<div class="tsk-stack tsk-stack--3">
							<?php get_template_part( 'template-parts/components/treatment-card', null, array( 'description' => '', 'badges' => array() ) + tsk_product_card_args( $tsk_rel ) ); ?>
							<span class="body-s tsk-text-secondary"><?php echo esc_html( tsk_product_from_label( $tsk_rel ) ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</div>

	<?php
endwhile;

get_footer( 'shop' );
