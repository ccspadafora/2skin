<?php
/**
 * Preguntas frecuentes (diseño: Preguntas). Se aplica a la página con slug «preguntas».
 *
 * Las preguntas son entradas de tsk_pregunta agrupadas por tsk_faq_grupo (orden: «Orden» del término y de cada pregunta).
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

get_header();

$tsk_groups = taxonomy_exists( 'tsk_faq_grupo' ) ? get_terms(
	array(
		'taxonomy'   => 'tsk_faq_grupo',
		'hide_empty' => true,
		'orderby'    => 'term_id',
	)
) : array();
$tsk_groups = is_wp_error( $tsk_groups ) ? array() : $tsk_groups;
$tsk_schema = array();
?>

<div class="container tsk-page">
	<?php tsk_breadcrumbs(); ?>
	<h1 class="h1 tsk-measure-22"><?php esc_html_e( 'Preguntas frecuentes.', '2skin' ); ?></h1>
	<p class="body-l tsk-text-secondary tsk-measure-52 tsk-page__lead"><?php esc_html_e( 'Si tu duda es clínica y depende de tu caso, la resolvemos en valoración. Aquí respondemos lo que sí podemos responder sin verte.', '2skin' ); ?></p>

	<div class="tsk-faq">
		<div class="tsk-faq__groups">
			<?php foreach ( $tsk_groups as $tsk_group ) : ?>
				<?php
				$tsk_questions = get_posts(
					array(
						'post_type'      => 'tsk_pregunta',
						'posts_per_page' => -1,
						'orderby'        => array(
							'menu_order' => 'ASC',
							'ID'         => 'ASC',
						),
						'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
							array(
								'taxonomy'         => 'tsk_faq_grupo',
								'terms'            => $tsk_group->term_id,
								'include_children' => false,
							),
						),
					)
				);
				$tsk_items     = array_map(
					static fn( WP_Post $q ): array => array(
						'question' => get_the_title( $q ),
						'answer'   => wpautop( $q->post_content ),
					),
					$tsk_questions
				);
				$tsk_schema    = array_merge( $tsk_schema, $tsk_items );
				$tsk_icon_name = (string) get_term_meta( $tsk_group->term_id, 'tsk_icono', true );
				?>
				<section>
					<h2 class="h4 tsk-faq__title"><?php echo $tsk_icon_name ? tsk_icon( $tsk_icon_name, 'tsk-icon tsk-text-sage' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $tsk_group->name ); ?></h2>
					<?php get_template_part( 'template-parts/components/accordion', null, array( 'items' => $tsk_items, 'id' => 'faq-' . $tsk_group->slug ) ); ?>
				</section>
			<?php endforeach; ?>
		</div>

		<aside class="tsk-faq__aside">
			<div class="tsk-surface-card tsk-stack tsk-stack--4">
				<?php echo tsk_icon( 'chat', 'tsk-icon tsk-icon--md tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<h2 class="h4"><?php esc_html_e( '¿Tu duda es sobre tu caso?', '2skin' ); ?></h2>
				<p class="body-s tsk-text-secondary"><?php esc_html_e( 'No diagnosticamos por mensaje. En valoración hacemos tu historia clínica y examinamos tu piel antes de recomendar un plan.', '2skin' ); ?></p>
				<?php
				/* translators: %s: precio de la valoración. */
				echo tsk_button( sprintf( __( 'Agendar valoración · %s', '2skin' ), tsk_cop( tsk_valoracion_precio() ) ), tsk_url( 'reserva' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
			<div class="tsk-soft-card tsk-stack tsk-stack--3">
				<p class="tsk-eyebrow"><?php esc_html_e( 'Atención', '2skin' ); ?></p>
				<span class="body-s tsk-inline-icon"><?php echo tsk_icon( 'pin', 'tsk-icon tsk-icon--sm' ) . esc_html( tsk_contact( 'direccion' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="body-s tsk-inline-icon"><?php echo tsk_icon( 'clock', 'tsk-icon tsk-icon--sm' ) . esc_html__( 'Solo con cita previa', '2skin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php if ( tsk_contact( 'pqrs' ) ) : ?>
					<span class="body-s tsk-inline-icon"><?php echo tsk_icon( 'doc', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( sprintf( /* translators: %s: correo PQRS. */ __( 'PQRS: %s · respuesta en 15 días hábiles', '2skin' ), tsk_contact( 'pqrs' ) ) ); ?></span>
				<?php endif; ?>
			</div>
		</aside>
	</div>
</div>

<?php
if ( $tsk_schema ) {
	$tsk_json = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array_map(
			static fn( array $item ): array => array(
				'@type'          => 'Question',
				'name'           => $item['question'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $item['answer'] ),
				),
			),
			$tsk_schema
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $tsk_json, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ). '</script>';
}

get_footer();
