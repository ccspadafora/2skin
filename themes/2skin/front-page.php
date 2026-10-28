<?php
/**
 * Inicio (diseño: Inicio).
 *
 * Las secciones de tratamientos usan productos de WooCommerce y solo se muestran cuando existen.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

get_header();

$tsk_has_wc   = function_exists( 'tsk_products_by_slug' );
$tsk_featured = $tsk_has_wc ? tsk_products_by_slug( (array) apply_filters( 'tsk_home_featured', array( 'toxina-botulinica', 'bioestimuladores' ) ) ) : array();
$tsk_catalog  = $tsk_has_wc ? tsk_products_by_slug( (array) apply_filters( 'tsk_home_catalog', array( 'acido-hialuronico', 'hidratacion-nctf', 'laser-co2', 'valoracion' ) ) ) : array();
$tsk_needs    = taxonomy_exists( 'tsk_necesidad' ) ? get_terms(
	array(
		'taxonomy'   => 'tsk_necesidad',
		'hide_empty' => false,
		'orderby'    => 'term_id',
	)
) : array();
$tsk_team     = get_posts(
	array(
		'post_type'      => 'tsk_medica',
		'posts_per_page' => 2,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
$tsk_price    = tsk_cop( tsk_valoracion_precio() );
?>

<section class="tsk-home-hero">
	<div class="container tsk-split tsk-split--hero">
		<div class="tsk-stack tsk-stack--7">
			<p class="tsk-eyebrow"><?php esc_html_e( 'Medicina estética · Bogotá', '2skin' ); ?></p>
			<h1 class="display-l tsk-measure-13"><?php esc_html_e( 'Medicina estética con criterio.', '2skin' ); ?></h1>
			<p class="body-l lead tsk-text-secondary"><?php esc_html_e( 'En 2Skin somos especialistas en protocolos enfocados en mejorar la calidad de la piel, personalizando cada protocolo. Para pacientes que desean resultados naturales pero significativos: no realizamos cambios que alteren tu esencia.', '2skin' ); ?></p>
			<div class="tsk-actions">
				<?php
				/* translators: %s: precio de la valoración. */
				echo tsk_button( sprintf( __( 'Agendar valoración · %s', '2skin' ), $tsk_price ), tsk_url( 'reserva' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo tsk_button( __( 'Conocer nuestro enfoque', '2skin' ), tsk_url( 'nosotros' ), array( 'variant' => 'secondary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
			<?php echo tsk_button( __( '¿No sabes por dónde empezar? Haz el diagnóstico de piel', '2skin' ), tsk_url( 'diagnostico' ), array( 'variant' => 'text', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p class="caption tsk-note">
				<?php echo tsk_icon( 'refresh', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span>
					<?php
					/* translators: %s: precio de la valoración virtual. */
					echo esc_html( sprintf( __( 'El valor de la valoración se abona completo a tu tratamiento si lo inicias dentro del primer mes. También hay valoración virtual por %s.', '2skin' ), tsk_cop( tsk_valoracion_precio( 'virtual' ) ) ) );
					?>
				</span>
			</p>
		</div>
		<?php echo tsk_media( 'hero.jpg', array( 'alt' => __( 'Luz natural sobre piel — retrato editorial 2skin', '2skin' ), 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>

<section class="tsk-home-pillars">
	<div class="container tsk-pillars">
		<?php
		$tsk_pillars = array(
			array( 'doc', __( '01 · Criterio', '2skin' ), __( 'Ningún tratamiento se aplica sin valoración médica previa. Si es tu primera vez, en línea solo pagas la consulta.', '2skin' ) ),
			array( 'shield', __( '02 · Insumos', '2skin' ), __( 'Solo productos con registro INVIMA y estudios que los respalden. Te mostramos el registro antes de aplicar.', '2skin' ) ),
			array( 'refresh', __( '03 · Seguimiento', '2skin' ), __( 'Controles programados según tu tratamiento y recordatorios de mantenimiento por WhatsApp.', '2skin' ) ),
		);
		foreach ( $tsk_pillars as [ $tsk_icon_name, $tsk_title, $tsk_text ] ) :
			?>
			<div class="tsk-pillars__item">
				<?php echo tsk_icon( $tsk_icon_name, 'tsk-icon tsk-icon--lg tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="tsk-eyebrow"><?php echo esc_html( $tsk_title ); ?></p>
				<p class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_text ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php if ( $tsk_needs && ! is_wp_error( $tsk_needs ) ) : ?>
<section class="tsk-block">
	<div class="container">
		<div class="tsk-section-head tsk-section-head--split">
			<div>
				<p class="tsk-eyebrow"><?php esc_html_e( 'Empieza por aquí', '2skin' ); ?></p>
				<h2 class="h2 tsk-measure-22"><?php esc_html_e( '¿Qué quieres mejorar en tu piel?', '2skin' ); ?></h2>
			</div>
			<p class="body-s tsk-text-secondary tsk-measure-34"><?php esc_html_e( 'La necesidad orienta mejor que el nombre del producto. Un mismo síntoma puede tener abordajes distintos.', '2skin' ); ?></p>
		</div>
		<div class="tsk-needs">
			<?php
			foreach ( $tsk_needs as $tsk_need ) {
				get_template_part(
					'template-parts/components/need-card',
					null,
					array(
						'name' => $tsk_need->name,
						'note' => (string) get_term_meta( $tsk_need->term_id, 'tsk_nota', true ),
						'url'  => (string) get_term_link( $tsk_need ),
					)
				);
			}
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="tsk-section tsk-section--dark tsk-on-dark tsk-manifesto">
	<div class="container tsk-manifesto__grid">
		<figure class="tsk-quote-figure"><blockquote class="tsk-quote">“<?php esc_html_e( 'No hacemos más. Hacemos lo que tu piel necesita.', '2skin' ); ?>”</blockquote></figure>
		<div class="tsk-stack tsk-stack--5 tsk-measure-46">
			<p class="body tsk-text-inverse-secondary"><?php esc_html_e( 'No vendemos jeringas. Construimos protocolos: un diagnóstico, una indicación, una rutina en casa y un seguimiento.', '2skin' ); ?></p>
			<p class="body tsk-text-inverse-secondary"><?php esc_html_e( 'Por eso verás precios de referencia y, al mismo tiempo, una valoración obligatoria antes de cualquier procedimiento. El tratamiento se define y se paga después de la consulta.', '2skin' ); ?></p>
			<?php echo tsk_button( __( 'Conocer nuestro enfoque', '2skin' ), tsk_url( 'nosotros' ), array( 'variant' => 'text', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>

<?php if ( $tsk_featured ) : ?>
<section class="tsk-block tsk-block--top">
	<div class="container">
		<p class="tsk-eyebrow"><?php esc_html_e( 'Los dos protocolos más consultados', '2skin' ); ?></p>
		<h2 class="h2 tsk-measure-24 tsk-head-gap"><?php esc_html_e( 'Toxina y bioestimuladores no compiten. Se indican.', '2skin' ); ?></h2>
		<div class="tsk-half">
			<?php foreach ( $tsk_featured as $tsk_product ) : ?>
				<?php $tsk_facts = array_slice( (array) $tsk_product->get_meta( '_tsk_facts' ), 0, 3 ); ?>
				<article class="tsk-featured">
					<a href="<?php echo esc_url( $tsk_product->get_permalink() ); ?>" class="tsk-featured__media">
						<?php echo tsk_media( (int) $tsk_product->get_image_id(), array( 'alt' => $tsk_product->get_name(), 'size' => 'tsk-landscape', 'class' => 'tsk-media--sharp', 'placeholder' => $tsk_product->get_name() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<div class="tsk-featured__head">
						<h3 class="h3"><?php echo esc_html( $tsk_product->get_name() ); ?></h3>
						<span class="body-s tsk-text-secondary tsk-nowrap"><?php echo esc_html( tsk_product_from_label( $tsk_product ) ); ?></span>
					</div>
					<p class="body tsk-text-secondary tsk-measure-42"><?php echo esc_html( wp_strip_all_tags( $tsk_product->get_short_description() ) ); ?></p>
					<?php if ( $tsk_facts ) : ?>
						<dl class="tsk-facts">
							<?php foreach ( $tsk_facts as $tsk_fact ) : ?>
								<div class="tsk-facts__row"><dt><?php echo esc_html( $tsk_fact[0] ?? '' ); ?></dt><dd><?php echo esc_html( $tsk_fact[1] ?? '' ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
					<?php echo tsk_button( __( 'Ver protocolo', '2skin' ), $tsk_product->get_permalink(), array( 'variant' => 'secondary', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $tsk_catalog ) : ?>
<section class="tsk-block">
	<div class="container">
		<div class="tsk-section-head tsk-section-head--split tsk-section-head--rule">
			<h2 class="h3"><?php esc_html_e( 'Catálogo de protocolos', '2skin' ); ?></h2>
			<?php echo tsk_button( __( 'Ver todos los tratamientos', '2skin' ), tsk_url( 'tratamientos' ), array( 'variant' => 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="tsk-catalog">
			<?php foreach ( $tsk_catalog as $tsk_product ) : ?>
				<div class="tsk-stack tsk-stack--3">
					<?php get_template_part( 'template-parts/components/treatment-card', null, tsk_product_card_args( $tsk_product ) + array( 'cta' => __( 'Ver protocolo', '2skin' ) ) ); ?>
					<span class="body-s tsk-price-rule"><?php echo esc_html( tsk_product_from_label( $tsk_product ) ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="tsk-block">
	<div class="container">
		<div class="tsk-panel">
			<p class="tsk-eyebrow"><?php esc_html_e( 'Cómo es tu primera vez', '2skin' ); ?></p>
			<h2 class="h3 tsk-measure-30 tsk-head-gap"><?php esc_html_e( 'Cinco pasos, y el criterio médico siempre por delante.', '2skin' ); ?></h2>
			<ol class="tsk-steps">
				<?php
				$tsk_steps = array(
					array( 'calendar', __( 'Agendas y pagas', '2skin' ), __( 'Reservas tu valoración en línea, presencial o virtual.', '2skin' ) ),
					array( 'user', __( 'Consulta integral', '2skin' ), __( 'Asistes a tu cita de 30 minutos con una de nuestras médicas.', '2skin' ) ),
					array( 'doc', __( 'Diagnóstico', '2skin' ), __( 'Historia clínica, paraclínicos si hacen falta y análisis de tu caso.', '2skin' ) ),
					array( 'mesh', __( 'Tu protocolo', '2skin' ), __( 'Creamos el mejor plan para ti: tratamientos, rutina y hábitos.', '2skin' ) ),
					array( 'refresh', __( 'Iniciamos', '2skin' ), __( 'Empezamos los tratamientos indicados y su seguimiento.', '2skin' ) ),
				);
				foreach ( $tsk_steps as $tsk_i => [ $tsk_icon_name, $tsk_title, $tsk_text ] ) :
					?>
					<li class="tsk-steps__item">
						<div class="tsk-steps__top">
							<span class="tsk-steps__n"><?php echo esc_html( sprintf( '%02d', $tsk_i + 1 ) ); ?></span>
							<?php echo tsk_icon( $tsk_icon_name, 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<h3 class="body tsk-weight-500"><?php echo esc_html( $tsk_title ); ?></h3>
						<p class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_text ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>

<section class="tsk-block">
	<div class="container tsk-half">
		<div class="tsk-stack tsk-stack--5">
			<p class="tsk-eyebrow"><?php esc_html_e( 'Evidencia visual', '2skin' ); ?></p>
			<h2 class="h2 tsk-measure-20"><?php esc_html_e( 'Evolución observada, no promesas.', '2skin' ); ?></h2>
			<p class="body tsk-text-secondary tsk-measure-42"><?php esc_html_e( 'Cada caso indica el manejo y el tiempo entre fotos. Cada resultado es individual: lo que logramos con un paciente no significa que se logrará igual en todos.', '2skin' ); ?></p>
			<?php echo tsk_button( __( 'Conocer los casos', '2skin' ), tsk_url( 'resultados' ), array( 'variant' => 'text', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
		get_template_part(
			'template-parts/components/before-after',
			null,
			array(
				'before'   => 'antes-1.jpg',
				'after'    => 'despues-1.jpg',
				'context'  => __( 'Mujer, 41 años', '2skin' ),
				'protocol' => __( 'Bioestimulador de colágeno · 2 sesiones', '2skin' ),
				'elapsed'  => __( '6 meses', '2skin' ),
			)
		);
		?>
		<p class="caption tsk-text-muted tsk-span-all tsk-text-end"><?php esc_html_e( 'Imagen ilustrativa. Los casos reales se publicarán con consentimiento escrito de cada paciente.', '2skin' ); ?></p>
	</div>
</section>

<?php if ( $tsk_team ) : ?>
<section class="tsk-block tsk-block--last">
	<div class="container">
		<div class="tsk-team-row">
			<div class="tsk-stack tsk-stack--4">
				<p class="tsk-eyebrow"><?php esc_html_e( 'Quién indica', '2skin' ); ?></p>
				<h2 class="h3 tsk-measure-18"><?php esc_html_e( 'El equipo médico que define tu protocolo.', '2skin' ); ?></h2>
				<p class="body-s tsk-text-secondary"><?php esc_html_e( 'Dos médicas con máster en medicina estética. Ambas realizan todos los procedimientos y tu seguimiento.', '2skin' ); ?></p>
				<?php echo tsk_button( __( 'Conocer al equipo', '2skin' ), tsk_url( 'nosotros' ), array( 'variant' => 'text', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php
			foreach ( $tsk_team as $tsk_doctor ) {
				get_template_part( 'template-parts/components/doctor-card', null, array( 'post' => $tsk_doctor ) );
			}
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
get_footer();
