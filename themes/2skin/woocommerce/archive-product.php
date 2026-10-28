<?php
/**
 * Tratamientos (diseño: Tratamientos). Reemplaza el archivo de la tienda de WooCommerce.
 *
 * Los filtros son enlaces (?necesidad=, ?tipo=, ?zona=; varios valores separados por comas) y el orden usa
 * el ?orderby= nativo de WooCommerce, así todo funciona sin JavaScript y cada vista tiene su URL.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package 2skin
 * @version 8.6.0
 */

use TwoSkin\Core\Catalog;

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$tsk_catalog = class_exists( Catalog::class );
$tsk_active  = $tsk_catalog ? Catalog::active_filters() : array();
$tsk_shop_id = wc_get_page_id( 'shop' );
$tsk_count   = (int) $GLOBALS['wp_query']->post_count;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- orden público.
$tsk_orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'menu_order';

$tsk_terms  = static function ( array $args ): array {
	$terms = get_terms( $args );
	return is_array( $terms ) ? $terms : array();
};
$tsk_groups = array(
	'necesidad' => array( __( 'Necesidad', '2skin' ), $tsk_terms( array( 'taxonomy' => 'tsk_necesidad', 'hide_empty' => true, 'orderby' => 'term_id' ) ) ),
	'tipo'      => array( __( 'Tipo de tratamiento', '2skin' ), $tsk_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'menu_order' => 'ASC', 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) ) ),
	'zona'      => array( __( 'Zona', '2skin' ), $tsk_terms( array( 'taxonomy' => 'tsk_zona', 'hide_empty' => true, 'orderby' => 'term_id' ) ) ),
);
$tsk_sorts  = array(
	'menu_order' => __( 'Relevancia', '2skin' ),
	'price'      => __( 'Menor precio', '2skin' ),
	'price-desc' => __( 'Mayor precio', '2skin' ),
);
$tsk_clear  = $tsk_catalog ? Catalog::url() : wc_get_page_permalink( 'shop' );
?>

<section>
	<?php echo tsk_media( has_post_thumbnail( $tsk_shop_id ) ? (int) get_post_thumbnail_id( $tsk_shop_id ) : 'hero-tratamientos.jpg', array( 'ratio' => '', 'class' => 'tsk-media--flush tsk-media--banner', 'size' => 'full', 'alt' => __( 'Preparación en consulta, 2Skin', '2skin' ), 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</section>

<div class="container tsk-page">
	<?php tsk_breadcrumbs( array(), __( 'Tratamientos', '2skin' ) ); ?>

	<header class="tsk-shop-head">
		<div>
			<h1 class="h1 tsk-measure-18"><?php esc_html_e( 'Tratamientos', '2skin' ); ?></h1>
			<p class="body tsk-text-secondary tsk-measure-52 tsk-head-gap-sm"><?php esc_html_e( 'Filtra por lo que quieres mejorar, no solo por el nombre del producto. Los precios son de referencia y no incluyen IVA; el plan final se define en valoración.', '2skin' ); ?></p>
		</div>
		<?php echo tsk_button( __( 'No sé qué necesito · agendar valoración', '2skin' ), tsk_url( 'reserva' ), array( 'variant' => 'secondary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</header>

	<?php woocommerce_output_all_notices(); ?>

	<div class="tsk-rail">
		<aside class="tsk-filters" aria-label="<?php esc_attr_e( 'Filtros', '2skin' ); ?>">
			<details class="tsk-filters__panel" open data-tsk-filters<?php echo $tsk_active ? ' data-active' : ''; ?>>
			<summary class="tsk-filters__head">
				<?php echo tsk_icon( 'filter', 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="tsk-eyebrow"><?php esc_html_e( 'Filtrar', '2skin' ); ?></span>
				<?php if ( $tsk_active ) : ?>
					<span class="tsk-filters__count"><?php echo esc_html( (string) array_sum( array_map( 'count', $tsk_active ) ) ); ?></span>
				<?php endif; ?>
				<?php echo tsk_icon( 'plus', 'tsk-icon tsk-icon--sm tsk-filters__toggle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</summary>
			<div class="tsk-filters__body">
			<?php foreach ( $tsk_groups as $tsk_param => [ $tsk_label, $tsk_options ] ) : ?>
				<?php
				if ( ! $tsk_options || ! $tsk_catalog ) {
					continue;
				}
				?>
				<div class="tsk-filters__group">
					<p class="tsk-eyebrow"><?php echo esc_html( $tsk_label ); ?></p>
					<ul>
						<?php foreach ( $tsk_options as $tsk_term ) : ?>
							<?php $tsk_on = in_array( $tsk_term->slug, $tsk_active[ $tsk_param ] ?? array(), true ); ?>
							<li>
								<a class="tsk-filter<?php echo $tsk_on ? ' is-active' : ''; ?>" href="<?php echo esc_url( Catalog::toggle_url( $tsk_active, $tsk_param, $tsk_term->slug ) ); ?>" rel="nofollow"<?php echo $tsk_on ? ' aria-current="true"' : ''; ?>>
									<span><?php echo esc_html( $tsk_term->name ); ?></span>
									<span class="tsk-filter__dot" aria-hidden="true"></span>
									<?php if ( $tsk_on ) : ?>
										<span class="screen-reader-text"><?php esc_html_e( '(activo, quitar filtro)', '2skin' ); ?></span>
									<?php endif; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
			<a class="tsk-filters__clear" href="<?php echo esc_url( $tsk_clear ); ?>">
				<?php echo tsk_icon( 'refresh', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Limpiar filtros', '2skin' ); ?>
			</a>
			</div>
			</details>
		</aside>

		<div class="tsk-stack tsk-shop-results">
			<div class="tsk-shop-toolbar">
				<p class="body-s tsk-text-secondary" role="status">
					<?php
					/* translators: %d: número de tratamientos. */
					echo esc_html( sprintf( _n( '%d protocolo', '%d protocolos', $tsk_count, '2skin' ), $tsk_count ) );
					?>
				</p>
				<nav class="tsk-sorts" aria-label="<?php esc_attr_e( 'Ordenar', '2skin' ); ?>">
					<?php foreach ( $tsk_sorts as $tsk_key => $tsk_label ) : ?>
						<a class="tsk-sort<?php echo $tsk_key === $tsk_orderby ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'orderby', $tsk_key ) ); ?>" rel="nofollow"<?php echo $tsk_key === $tsk_orderby ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $tsk_label ); ?></a>
					<?php endforeach; ?>
				</nav>
			</div>

			<?php if ( have_posts() ) : ?>
				<div class="tsk-shop-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						$tsk_product = wc_get_product( get_the_ID() );
						if ( ! $tsk_product ) {
							continue;
						}
						?>
						<article class="tsk-shop-card">
							<?php get_template_part( 'template-parts/components/treatment-card', null, tsk_product_card_args( $tsk_product ) ); ?>
							<span class="body-s tsk-price-rule"><?php echo esc_html( tsk_product_from_label( $tsk_product ) ); ?></span>
						</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="tsk-panel tsk-empty">
					<h2 class="h4"><?php esc_html_e( 'No encontramos resultados con estos filtros.', '2skin' ); ?></h2>
					<p class="body-s tsk-text-secondary tsk-measure-46"><?php esc_html_e( 'Puedes ajustar tu búsqueda o explorar todos los tratamientos.', '2skin' ); ?></p>
					<?php echo tsk_button( __( 'Ver todos', '2skin' ), $tsk_clear, array( 'variant' => 'secondary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>

			<div class="tsk-first-time">
				<div class="tsk-first-time__text">
					<?php echo tsk_icon( 'doc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div>
						<h2 class="h4"><?php esc_html_e( '¿Es tu primera vez?', '2skin' ); ?></h2>
						<p class="body-s tsk-text-secondary">
							<?php
							/* translators: %s: precio de la valoración presencial. */
							echo esc_html( sprintf( __( 'Empieza por la valoración: %s, 30 minutos, abonable completa a tu tratamiento si lo inicias dentro del primer mes. Los tratamientos solo se reservan en línea si ya eres paciente en seguimiento.', '2skin' ), tsk_cop( tsk_valoracion_precio() ) ) );
							?>
						</p>
					</div>
				</div>
				<?php echo tsk_button( __( 'Agendar valoración', '2skin' ), tsk_url( 'reserva' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer( 'shop' );
