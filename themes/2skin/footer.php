<?php
/**
 * Banda de valoración + pie (diseño: SiteFooter).
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

$tsk_whatsapp = tsk_whatsapp_url();
$tsk_pqrs     = tsk_contact( 'pqrs' );
?>
</main>

<?php if ( ! ( function_exists( 'is_checkout' ) && ( is_checkout() || is_cart() ) ) ) : ?>
<section class="tsk-cta-band tsk-on-dark">
	<div class="container tsk-cta-band__grid">
		<div>
			<p class="tsk-eyebrow tsk-eyebrow--sage"><?php esc_html_e( 'Primero evaluamos', '2skin' ); ?></p>
			<h2 class="h2 tsk-cta-band__title"><?php esc_html_e( 'Agenda tu valoración.', '2skin' ); ?></h2>
		</div>
		<div class="tsk-cta-band__body">
			<p class="body"><?php esc_html_e( '30 minutos con una de nuestras médicas: historia clínica, examen de tu piel y un protocolo pensado para ti. El valor se abona completo a tu tratamiento si lo inicias dentro del primer mes.', '2skin' ); ?></p>
			<div class="tsk-actions">
				<?php
				/* translators: %s: precio de la valoración. */
				echo tsk_button( sprintf( __( 'Agendar valoración · %s', '2skin' ), tsk_cop( tsk_valoracion_precio() ) ), tsk_url( 'reserva' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo tsk_button( __( 'Ver preguntas frecuentes', '2skin' ), tsk_url( 'preguntas' ), array( 'variant' => 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<footer class="tsk-site-footer tsk-on-dark">
	<div class="container">
		<div class="tsk-site-footer__grid">
			<div>
				<img src="<?php echo esc_url( tsk_asset( 'img/2skin-monogram-cream.png' ) ); ?>" alt="2skin" class="tsk-site-footer__logo" height="48">
				<p class="tsk-site-footer__claim"><?php esc_html_e( 'Medicina estética con criterio.', '2skin' ); ?></p>
			</div>

			<?php
			$tsk_columns = array(
				'footer-protocolos' => __( 'Protocolos', '2skin' ),
				'footer-pacientes'  => __( 'Pacientes', '2skin' ),
			);
			foreach ( $tsk_columns as $tsk_location => $tsk_title ) :
				?>
				<div>
					<p class="tsk-eyebrow tsk-eyebrow--sage tsk-site-footer__title"><?php echo esc_html( $tsk_title ); ?></p>
					<ul class="tsk-site-footer__links">
						<?php foreach ( tsk_nav_items( $tsk_location ) as $tsk_item ) : ?>
							<li><a href="<?php echo esc_url( $tsk_item['url'] ); ?>"><?php echo esc_html( $tsk_item['title'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>

			<div>
				<p class="tsk-eyebrow tsk-eyebrow--sage tsk-site-footer__title"><?php esc_html_e( 'Contacto', '2skin' ); ?></p>
				<ul class="tsk-site-footer__links">
					<li class="tsk-site-footer__info"><?php echo tsk_icon( 'pin', 'tsk-icon tsk-icon--sm' ) . esc_html( tsk_contact( 'direccion' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
					<li class="tsk-site-footer__info"><?php echo tsk_icon( 'clock', 'tsk-icon tsk-icon--sm' ) . esc_html( tsk_contact( 'horario' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
					<?php if ( $tsk_whatsapp ) : ?>
						<li><a href="<?php echo esc_url( $tsk_whatsapp ); ?>" rel="noopener" target="_blank"><?php echo tsk_icon( 'chat', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>WhatsApp</a></li>
					<?php endif; ?>
					<li><a href="<?php echo esc_url( tsk_url( 'nosotros' ) ); ?>"><?php echo tsk_icon( 'user', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Conoce al equipo', '2skin' ); ?></a></li>
					<?php if ( $tsk_pqrs ) : ?>
						<li><a href="<?php echo esc_url( 'mailto:' . $tsk_pqrs ); ?>"><?php echo tsk_icon( 'doc', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>PQRS · <?php echo esc_html( $tsk_pqrs ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>

		<div class="tsk-site-footer__base">
			<span><?php echo tsk_icon( 'shield', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Todos los tratamientos requieren valoración médica previa.', '2skin' ); ?></span>
			<span><?php echo tsk_icon( 'check', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'La publicidad no sustituye la consulta médica.', '2skin' ); ?></span>
			<span><?php echo tsk_icon( 'lock', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php if ( get_privacy_policy_url() ) : ?><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Datos tratados conforme a la Ley 1581 de 2012.', '2skin' ); ?></a><?php else : ?><?php esc_html_e( 'Datos tratados conforme a la Ley 1581 de 2012.', '2skin' ); ?><?php endif; ?></span>
			<span><?php echo tsk_icon( 'refresh', 'tsk-icon tsk-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>© <?php echo esc_html( wp_date( 'Y' ) ); ?> 2Skin medical group · Bogotá</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
