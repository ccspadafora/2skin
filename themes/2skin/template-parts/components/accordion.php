<?php
/**
 * Accordion: preguntas y respuestas. Sin JS los paneles quedan abiertos (ver .tsk-js en site.css).
 *
 * @package 2skin
 *
 * @var array $args items (lista de [ 'question' => string, 'answer' => string HTML ]), id, open (índice abierto al cargar; -1 = ninguno).
 */

defined( 'ABSPATH' ) || exit;

$tsk_items = $args['items'] ?? array();
$tsk_id    = $args['id'] ?? wp_unique_id( 'tsk-acc-' );
$tsk_open  = (int) ( $args['open'] ?? -1 );
if ( ! $tsk_items ) {
	return;
}
?>
<div class="tsk-accordion" data-tsk-accordion>
	<?php foreach ( $tsk_items as $tsk_i => $tsk_item ) : ?>
		<?php
		$tsk_panel   = $tsk_id . '-' . $tsk_i;
		$tsk_is_open = $tsk_i === $tsk_open;
		?>
		<div class="tsk-accordion__item">
			<h3 class="tsk-accordion__heading">
				<button type="button" class="tsk-accordion__trigger" aria-expanded="<?php echo $tsk_is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $tsk_panel ); ?>">
					<span><?php echo esc_html( $tsk_item['question'] ); ?></span>
					<span class="tsk-accordion__sign" aria-hidden="true"><?php echo $tsk_is_open ? '−' : '+'; ?></span>
				</button>
			</h3>
			<div class="tsk-accordion__panel<?php echo $tsk_is_open ? ' tsk-accordion__panel--open' : ''; ?>" id="<?php echo esc_attr( $tsk_panel ); ?>" role="region">
				<div class="tsk-accordion__body"><?php echo wp_kses_post( $tsk_item['answer'] ); ?></div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
