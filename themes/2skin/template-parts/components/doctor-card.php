<?php
/**
 * DoctorCard: retrato, rol, nombre, credenciales y enlace.
 *
 * @package 2skin
 *
 * @var array $args post (WP_Post de tsk_medica), url, cta.
 */

defined( 'ABSPATH' ) || exit;

$tsk_post = $args['post'] ?? null;
if ( ! $tsk_post instanceof WP_Post ) {
	return;
}
$tsk_url = $args['url'] ?? tsk_url( 'nosotros' );
$tsk_cta = $args['cta'] ?? __( 'Conocer perfil', '2skin' );
?>
<div class="tsk-card">
	<?php
	echo tsk_media( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tsk_media escapa.
		(int) get_post_thumbnail_id( $tsk_post ),
		array(
			'ratio'       => 'portrait',
			'alt'         => get_the_title( $tsk_post ),
			'size'        => 'tsk-portrait',
			'placeholder' => __( 'Retrato', '2skin' ),
		)
	);
	?>
	<div class="tsk-stack tsk-stack--2">
		<span class="tsk-doctor__role"><?php echo esc_html( (string) get_post_meta( $tsk_post->ID, '_tsk_rol', true ) ); ?></span>
		<h3 class="tsk-doctor__name"><?php echo esc_html( get_the_title( $tsk_post ) ); ?></h3>
		<p class="tsk-doctor__creds"><?php echo esc_html( (string) get_post_meta( $tsk_post->ID, '_tsk_credenciales', true ) ); ?></p>
	</div>
	<?php echo tsk_button( $tsk_cta, $tsk_url, array( 'variant' => 'text', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
