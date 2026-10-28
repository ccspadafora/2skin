<?php
/**
 * Página no encontrada.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container tsk-page tsk-stack tsk-stack--5">
	<p class="tsk-eyebrow">404</p>
	<h1 class="h1"><?php esc_html_e( 'No encontramos esta página.', '2skin' ); ?></h1>
	<p class="body-l tsk-text-secondary lead"><?php esc_html_e( 'Puede que haya cambiado de dirección. Puedes volver al inicio o explorar los tratamientos.', '2skin' ); ?></p>
	<div class="tsk-actions">
		<?php
		echo tsk_button( __( 'Volver al inicio', '2skin' ), tsk_url( 'inicio' ), array( 'variant' => 'secondary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo tsk_button( __( 'Ver tratamientos', '2skin' ), tsk_url( 'tratamientos' ), array( 'variant' => 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</div>
<?php
get_footer();
