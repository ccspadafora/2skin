<?php
/**
 * Encabezado (diseño: SiteHeader). Fijo, 88 → 72 px al hacer scroll.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

$tsk_nav = tsk_nav_items( 'primary' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="tsk-skip-link" href="#contenido"><?php esc_html_e( 'Saltar al contenido', '2skin' ); ?></a>

<header class="tsk-site-header" data-tsk-header>
	<div class="container tsk-site-header__bar">
		<a class="tsk-site-header__logo" href="<?php echo esc_url( tsk_url( 'inicio' ) ); ?>" aria-label="<?php esc_attr_e( '2skin · inicio', '2skin' ); ?>">
			<img src="<?php echo esc_url( tsk_asset( 'img/2skin-monogram-charcoal.png' ) ); ?>" alt="" class="tsk-site-header__monogram" width="38" height="38">
			<img src="<?php echo esc_url( tsk_asset( 'img/2skin-wordmark-charcoal.png' ) ); ?>" alt="2skin Medical Aesthetics" class="tsk-site-header__wordmark" height="20">
		</a>

		<nav class="tsk-site-header__nav" aria-label="<?php esc_attr_e( 'Principal', '2skin' ); ?>">
			<?php foreach ( $tsk_nav as $tsk_item ) : ?>
				<a href="<?php echo esc_url( $tsk_item['url'] ); ?>" class="tsk-nav-link<?php echo $tsk_item['current'] ? ' is-current' : ''; ?>"<?php echo $tsk_item['current'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $tsk_item['title'] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="tsk-site-header__actions">
			<a href="<?php echo esc_url( tsk_url( 'cuenta' ) ); ?>" class="tsk-site-header__account tsk-only-desktop">
				<?php echo tsk_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'Mi piel', '2skin' ); ?></span>
			</a>
			<a href="<?php echo esc_url( tsk_url( 'carrito' ) ); ?>" class="tsk-site-header__cart" aria-label="<?php esc_attr_e( 'Ver tu reserva', '2skin' ); ?>">
				<?php echo tsk_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo function_exists( 'tsk_cart_badge' ) ? tsk_cart_badge() : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<?php echo tsk_button( __( 'Agendar valoración', '2skin' ), tsk_url( 'reserva' ), array( 'size' => 'sm', 'class' => 'tsk-only-desktop' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<button type="button" class="tsk-site-header__toggle tsk-only-mobile" aria-expanded="false" aria-controls="tsk-mobile-nav" data-tsk-menu-toggle>
				<span class="screen-reader-text"><?php esc_html_e( 'Abrir menú', '2skin' ); ?></span>
				<?php echo tsk_icon( 'menu', 'tsk-icon tsk-icon--open' ) . tsk_icon( 'close', 'tsk-icon tsk-icon--close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
	</div>

	<nav id="tsk-mobile-nav" class="container tsk-mobile-nav" aria-label="<?php esc_attr_e( 'Menú móvil', '2skin' ); ?>" hidden>
		<?php foreach ( $tsk_nav as $tsk_item ) : ?>
			<a href="<?php echo esc_url( $tsk_item['url'] ); ?>" class="tsk-mobile-nav__item"<?php echo $tsk_item['current'] ? ' aria-current="page"' : ''; ?>>
				<?php echo $tsk_item['icon'] ? tsk_icon( $tsk_item['icon'] ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( $tsk_item['title'] ); ?>
			</a>
		<?php endforeach; ?>
		<a href="<?php echo esc_url( tsk_url( 'cuenta' ) ); ?>" class="tsk-mobile-nav__item"><?php echo tsk_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Mi piel', '2skin' ); ?></a>
		<?php echo tsk_button( __( 'Agendar valoración', '2skin' ), tsk_url( 'reserva' ), array( 'class' => 'tsk-mobile-nav__cta' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</nav>
</header>

<main id="contenido" class="tsk-main">
