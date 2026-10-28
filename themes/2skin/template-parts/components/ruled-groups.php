<?php
/**
 * Grupos con título y lista de puntos separados por líneas (contraindicaciones, etapas de recuperación).
 *
 * @package 2skin
 *
 * @var array $args groups (lista de [ 'title' => string, 'items' => string[] ]).
 */

defined( 'ABSPATH' ) || exit;

$tsk_groups = $args['groups'] ?? array();
if ( ! $tsk_groups ) {
	return;
}
?>
<div class="tsk-ruled-groups">
	<?php foreach ( $tsk_groups as $tsk_group ) : ?>
		<div class="tsk-ruled-groups__item">
			<?php if ( $tsk_group['title'] ) : ?>
				<h3 class="body tsk-weight-500"><?php echo esc_html( $tsk_group['title'] ); ?></h3>
			<?php endif; ?>
			<ul>
				<?php foreach ( $tsk_group['items'] as $tsk_item ) : ?>
					<li class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_item ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>
