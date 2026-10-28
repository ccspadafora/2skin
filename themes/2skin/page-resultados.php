<?php
/**
 * Resultados (diseño: Resultados). Se aplica a la página con slug «resultados».
 *
 * Los casos son entradas de tsk_caso; solo se listan los autorizados por escrito o marcados como ilustrativos.
 * El filtro por etiqueta es un enlace (?caso=etiqueta).
 *
 * @package 2skin
 */

use TwoSkin\Core\Cases;

defined( 'ABSPATH' ) || exit;

get_header();

$tsk_cases = class_exists( Cases::class ) ? Cases::published() : array();
$tsk_tags  = array();
foreach ( $tsk_cases as $tsk_case ) {
	if ( $tsk_case['tag'] ) {
		$tsk_tags[ $tsk_case['tag_slug'] ] = $tsk_case['tag'];
	}
}
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- filtro público de solo lectura.
$tsk_filter = isset( $_GET['caso'] ) ? sanitize_title( wp_unslash( $_GET['caso'] ) ) : '';
$tsk_filter = isset( $tsk_tags[ $tsk_filter ] ) ? $tsk_filter : '';
$tsk_shown  = $tsk_filter ? array_filter( $tsk_cases, static fn( array $c ): bool => $c['tag_slug'] === $tsk_filter ) : $tsk_cases;
$tsk_base   = get_permalink();

$tsk_method = array(
	array( 'sun', __( 'Misma luz', '2skin' ), __( 'Iluminación fija de consultorio, sin flash direccional ni filtros.', '2skin' ) ),
	array( 'face', __( 'Mismo encuadre', '2skin' ), __( 'Distancia, altura y ángulo estandarizados en cada control.', '2skin' ) ),
	array( 'doc', __( 'Sin retoque', '2skin' ), __( 'No editamos textura, tono ni contorno en ninguna imagen publicada.', '2skin' ) ),
	array( 'lock', __( 'Con autorización', '2skin' ), __( 'Fotografías autorizadas por escrito por cada paciente. Publicamos sin nombre.', '2skin' ) ),
);
$tsk_gallery = array(
	array( 'clinica-1.jpg', __( 'Preparación del insumo', '2skin' ), __( 'Preparación y trazabilidad de lote', '2skin' ) ),
	array( 'clinica-2.jpg', __( 'Espacio clínico', '2skin' ), __( 'Medicenter, Bogotá', '2skin' ) ),
	array( 'textura-2.jpg', __( 'Detalle de piel', '2skin' ), __( 'Detalle de calidad de piel', '2skin' ) ),
);
?>

<section>
	<?php echo tsk_media( has_post_thumbnail() ? (int) get_post_thumbnail_id() : 'hero-resultados.jpg', array( 'ratio' => '', 'class' => 'tsk-media--flush tsk-media--banner', 'size' => 'full', 'alt' => __( 'Registro fotográfico estandarizado 2Skin', '2skin' ), 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</section>

<div class="container tsk-page">
	<?php tsk_breadcrumbs(); ?>
	<h1 class="h1 tsk-measure-20"><?php esc_html_e( 'Casos reales, con su contexto.', '2skin' ); ?></h1>
	<p class="body-l tsk-text-secondary tsk-measure-52 tsk-head-gap-sm"><?php esc_html_e( 'Publicamos el protocolo aplicado y el tiempo transcurrido. No publicamos casos sin autorización escrita ni imágenes editadas.', '2skin' ); ?></p>

	<?php if ( count( $tsk_tags ) > 1 ) : ?>
		<nav class="tsk-pills" aria-label="<?php esc_attr_e( 'Filtrar casos', '2skin' ); ?>">
			<a class="tsk-pill<?php echo $tsk_filter ? '' : ' is-active'; ?>" href="<?php echo esc_url( $tsk_base ); ?>"<?php echo $tsk_filter ? '' : ' aria-current="true"'; ?>><?php esc_html_e( 'Todos', '2skin' ); ?></a>
			<?php foreach ( $tsk_tags as $tsk_slug => $tsk_tag ) : ?>
				<a class="tsk-pill<?php echo $tsk_slug === $tsk_filter ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'caso', $tsk_slug, $tsk_base ) ); ?>"<?php echo $tsk_slug === $tsk_filter ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $tsk_tag ); ?></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>

	<div class="tsk-cases">
		<?php foreach ( $tsk_shown as $tsk_case ) : ?>
			<?php $tsk_product = $tsk_case['product_id'] ? wc_get_product( $tsk_case['product_id'] ) : null; ?>
			<article class="tsk-half">
				<div class="tsk-stack tsk-stack--4">
					<?php if ( $tsk_case['tag'] ) : ?>
						<span class="tsk-badge tsk-self-start"><?php echo esc_html( $tsk_case['tag'] ); ?></span>
					<?php endif; ?>
					<h2 class="h3 tsk-measure-20"><?php echo esc_html( $tsk_case['title'] ); ?></h2>
					<p class="body-s tsk-text-secondary tsk-measure-42"><?php echo esc_html( $tsk_case['body'] ); ?></p>
					<?php if ( $tsk_case['rows'] ) : ?>
						<dl class="tsk-facts">
							<?php foreach ( $tsk_case['rows'] as [ $tsk_k, $tsk_v ] ) : ?>
								<div class="tsk-facts__row"><dt><?php echo esc_html( $tsk_k ); ?></dt><dd><?php echo esc_html( $tsk_v ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
					<?php if ( $tsk_product && 'publish' === $tsk_product->get_status() ) : ?>
						<?php echo tsk_button( __( 'Ver este protocolo', '2skin' ), $tsk_product->get_permalink(), array( 'variant' => 'text', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
				</div>
				<div class="tsk-stack tsk-stack--2">
					<?php get_template_part( 'template-parts/components/before-after', null, $tsk_case ); ?>
					<?php if ( $tsk_case['illustrative'] ) : ?>
						<p class="caption tsk-text-muted"><?php esc_html_e( 'Imagen ilustrativa. Los casos reales se publicarán con consentimiento escrito.', '2skin' ); ?></p>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>

	<section class="tsk-pdp-section tsk-pdp-section--rule">
		<p class="tsk-eyebrow tsk-head-gap-sm"><?php esc_html_e( 'Cómo fotografiamos', '2skin' ); ?></p>
		<ul class="tsk-steps">
			<?php foreach ( $tsk_method as [ $tsk_icon, $tsk_title, $tsk_body ] ) : ?>
				<li class="tsk-steps__item">
					<?php echo tsk_icon( $tsk_icon, 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<h3 class="body tsk-weight-500"><?php echo esc_html( $tsk_title ); ?></h3>
					<p class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_body ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
		<p class="caption tsk-measure-64 tsk-head-gap-sm"><?php esc_html_e( 'Cada resultado es individual. Que logremos un resultado con un paciente no significa que se logrará lo mismo en todos, ni con el mismo protocolo.', '2skin' ); ?></p>
	</section>

	<div class="tsk-gallery tsk-results-gallery">
		<?php foreach ( $tsk_gallery as [ $tsk_file, $tsk_alt, $tsk_caption ] ) : ?>
			<div class="tsk-stack tsk-stack--2">
				<?php echo tsk_media( $tsk_file, array( 'ratio' => 'portrait', 'alt' => $tsk_alt ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="caption tsk-text-muted"><?php echo esc_html( $tsk_caption ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<?php
get_footer();
