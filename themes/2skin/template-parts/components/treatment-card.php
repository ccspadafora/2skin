<?php
/**
 * TreatmentCard: imagen, categoría, nombre, descripción y CTA.
 *
 * @package 2skin
 *
 * @var array $args name, category, description, image (ID o ruta en assets/img), url, cta, badges, ratio.
 */

defined( 'ABSPATH' ) || exit;

$tsk_args = wp_parse_args(
	$args,
	array(
		'name'        => '',
		'category'    => '',
		'description' => '',
		'image'       => 0,
		'url'         => '#',
		'cta'         => __( 'Ver tratamiento', '2skin' ),
		'badges'      => array(),
		'ratio'       => 'landscape',
	)
);
?>
<a class="tsk-card tsk-card--link" href="<?php echo esc_url( $tsk_args['url'] ); ?>">
	<?php
	echo tsk_media( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tsk_media escapa.
		$tsk_args['image'],
		array(
			'ratio'       => $tsk_args['ratio'],
			'alt'         => $tsk_args['name'],
			'size'        => 'tsk-landscape',
			'sizes'       => '(max-width: 640px) calc(100vw - 32px), (max-width: 980px) 50vw, 420px',
			'placeholder' => $tsk_args['name'],
		)
	);
	?>
	<?php if ( $tsk_args['category'] ) : ?>
		<span class="tsk-card__cat"><?php echo esc_html( $tsk_args['category'] ); ?></span>
	<?php endif; ?>
	<h3 class="tsk-card__title"><?php echo esc_html( $tsk_args['name'] ); ?></h3>
	<?php if ( $tsk_args['description'] ) : ?>
		<p class="tsk-card__body"><?php echo esc_html( $tsk_args['description'] ); ?></p>
	<?php endif; ?>
	<?php if ( $tsk_args['badges'] ) : ?>
		<span class="tsk-card__badges">
			<?php foreach ( $tsk_args['badges'] as $tsk_badge ) : ?>
				<span class="tsk-badge"><?php echo esc_html( $tsk_badge ); ?></span>
			<?php endforeach; ?>
		</span>
	<?php endif; ?>
	<span class="tsk-card__cta tsk-btn tsk-btn--text"><span class="tsk-btn__label"><?php echo esc_html( $tsk_args['cta'] ); ?></span><span class="tsk-btn__arrow" aria-hidden="true">→</span></span>
</a>
