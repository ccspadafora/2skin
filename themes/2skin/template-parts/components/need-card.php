<?php
/**
 * NeedCard: fila de necesidad con flecha.
 *
 * @package 2skin
 *
 * @var array $args name, note, url, boxed.
 */

defined( 'ABSPATH' ) || exit;

$tsk_args = wp_parse_args( $args, array( 'name' => '', 'note' => '', 'url' => '#', 'boxed' => false ) );
?>
<a class="tsk-need<?php echo $tsk_args['boxed'] ? ' tsk-need--boxed' : ''; ?>" href="<?php echo esc_url( $tsk_args['url'] ); ?>">
	<span>
		<span class="tsk-need__name"><?php echo esc_html( $tsk_args['name'] ); ?></span>
		<?php if ( $tsk_args['note'] ) : ?>
			<span class="tsk-need__note"><?php echo esc_html( $tsk_args['note'] ); ?></span>
		<?php endif; ?>
	</span>
	<span class="tsk-need__arrow" aria-hidden="true">→</span>
</a>
