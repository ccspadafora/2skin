<?php
/**
 * Página genérica: textos legales, políticas y páginas de WooCommerce sin plantilla propia.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="container tsk-page">
		<?php tsk_breadcrumbs(); ?>
		<h1 class="h1 tsk-page__title"><?php the_title(); ?></h1>
		<div class="tsk-prose"><?php the_content(); ?></div>
	</div>
	<?php
endwhile;

get_footer();
