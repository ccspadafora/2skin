<?php
/**
 * Contenedor de Mi piel (Mi cuenta de WooCommerce, diseño: Cuenta). Las plantillas de
 * woocommerce/myaccount pintan las pestañas y su contenido.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

get_header();

$tsk_user = wp_get_current_user();

while ( have_posts() ) :
	the_post();
	?>
	<div class="container tsk-page tsk-account">
		<p class="tsk-eyebrow tsk-inline-icon"><?php echo tsk_icon( 'user', 'tsk-icon tsk-icon--sm tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( get_post_field( 'post_title', get_the_ID() ) ); ?></span></p>
		<?php if ( $tsk_user->exists() ) : ?>
			<?php $tsk_name = $tsk_user->first_name ? $tsk_user->first_name : $tsk_user->display_name; ?>
			<h1 class="h1 tsk-account__title">
				<?php
				/* translators: %s: nombre de la paciente. */
				echo esc_html( sprintf( __( 'Hola, %s.', '2skin' ), $tsk_name ) );
				?>
			</h1>
			<p class="body tsk-text-secondary tsk-measure-52 tsk-account__lead"><?php esc_html_e( 'Aquí están tus citas, tus pedidos y tus bonos. La evolución y las indicaciones de tu médica llegarán a este espacio más adelante.', '2skin' ); ?></p>
		<?php else : ?>
			<h1 class="h1 tsk-account__title"><?php esc_html_e( 'Ingresa a tu cuenta.', '2skin' ); ?></h1>
			<p class="body tsk-text-secondary tsk-measure-52 tsk-account__lead"><?php esc_html_e( 'Tu cuenta se crea al reservar. Usa el correo con el que agendaste; si no tienes contraseña, pide una nueva.', '2skin' ); ?></p>
		<?php endif; ?>
		<?php the_content(); ?>
	</div>
	<?php
endwhile;

get_footer();
