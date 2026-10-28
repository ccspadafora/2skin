<?php
/**
 * Plantilla de respaldo.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container tsk-page">
	<?php if ( have_posts() ) : ?>
		<?php if ( ! is_singular() ) : ?>
			<h1 class="h1 tsk-page__title"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
		<?php endif; ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'tsk-entry' ); ?>>
				<?php if ( is_singular() ) : ?>
					<h1 class="h1 tsk-page__title"><?php the_title(); ?></h1>
					<div class="tsk-prose"><?php the_content(); ?></div>
				<?php else : ?>
					<h2 class="h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="body tsk-text-secondary"><?php the_excerpt(); ?></div>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<h1 class="h1 tsk-page__title"><?php esc_html_e( 'No encontramos esta página.', '2skin' ); ?></h1>
		<p class="body-l tsk-text-secondary"><?php esc_html_e( 'Puede que haya cambiado de dirección. Puedes volver al inicio o explorar los tratamientos.', '2skin' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
