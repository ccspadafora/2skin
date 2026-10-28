<?php
/**
 * BeforeAfter: comparador con deslizador (teclado y puntero, ver assets/js/site.js).
 *
 * @package 2skin
 *
 * @var array $args before, after (ID o ruta en assets/img), context, protocol, elapsed, disclaimer.
 */

defined( 'ABSPATH' ) || exit;

$tsk_args = wp_parse_args(
	$args,
	array(
		'before'     => '',
		'after'      => '',
		'context'    => '',
		'protocol'   => '',
		'elapsed'    => '',
		'disclaimer' => __( 'Los resultados varían según la piel, la edad, los hábitos y el protocolo indicado. Las imágenes corresponden a un caso real y no representan una promesa de resultado.', '2skin' ),
	)
);

$tsk_layer = static function ( int|string $image, string $alt ): string {
	if ( is_int( $image ) && $image > 0 ) {
		return wp_get_attachment_image( $image, 'tsk-landscape', false, array( 'alt' => $alt, 'loading' => 'lazy' ) );
	}
	if ( is_string( $image ) && '' !== $image ) {
		return sprintf( '<img src="%s" alt="%s" loading="lazy" decoding="async">', esc_url( tsk_asset( 'img/' . $image ) ), esc_attr( $alt ) );
	}
	return '<div class="tsk-media__placeholder"><span>' . esc_html( $alt ) . '</span></div>';
};
?>
<div class="tsk-ba" data-tsk-ba>
	<div class="tsk-ba__frame">
		<div class="tsk-ba__layer"><?php echo $tsk_layer( $tsk_args['after'], __( 'Después', '2skin' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<div class="tsk-ba__layer tsk-ba__layer--before" style="clip-path: inset(0 50% 0 0)"><?php echo $tsk_layer( $tsk_args['before'], __( 'Antes', '2skin' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<span class="tsk-ba__tag tsk-ba__tag--before"><?php esc_html_e( 'Antes', '2skin' ); ?></span>
		<span class="tsk-ba__tag tsk-ba__tag--after"><?php esc_html_e( 'Después', '2skin' ); ?></span>
		<div class="tsk-ba__handle" style="left: 50%" role="slider" tabindex="0" aria-label="<?php esc_attr_e( 'Comparar antes y después', '2skin' ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50">
			<span class="tsk-ba__grip" aria-hidden="true">⟷</span>
		</div>
	</div>
	<?php if ( $tsk_args['context'] || $tsk_args['protocol'] || $tsk_args['elapsed'] ) : ?>
		<div class="tsk-ba__meta">
			<?php foreach ( array( 'context', 'protocol', 'elapsed' ) as $tsk_key ) : ?>
				<?php if ( $tsk_args[ $tsk_key ] ) : ?>
					<span><?php echo esc_html( $tsk_args[ $tsk_key ] ); ?></span>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<p class="tsk-ba__disclaimer"><?php echo esc_html( $tsk_args['disclaimer'] ); ?></p>
</div>
