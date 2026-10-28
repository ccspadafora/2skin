<?php
/**
 * Nosotros (diseño: Nosotros). Se aplica a la página con slug «nosotros».
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

get_header();

$tsk_team = get_posts(
	array(
		'post_type'      => 'tsk_medica',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
?>

<section>
	<?php echo tsk_media( has_post_thumbnail() ? (int) get_post_thumbnail_id() : 'hero-nosotros.jpg', array( 'ratio' => '', 'class' => 'tsk-media--flush tsk-media--banner', 'size' => 'full', 'alt' => __( '2Skin, Bogotá', '2skin' ), 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</section>

<section class="tsk-about-intro">
	<div class="container tsk-split">
		<div class="tsk-stack tsk-stack--5">
			<p class="tsk-eyebrow"><?php esc_html_e( 'Nuestra historia', '2skin' ); ?></p>
			<h1 class="h1 tsk-measure-16"><?php esc_html_e( 'Primero evaluamos. Después proponemos.', '2skin' ); ?></h1>
			<p class="body-l tsk-text-secondary tsk-measure-44"><?php esc_html_e( '2Skin nace de dos médicas estéticas apasionadas por la calidad de la piel. La idea inicia a finales de 2025 como un consultorio de medicina estética; en 2026 madura hacia algo más integral y con un enfoque específico, y así nace el concepto de 2Skin.', '2skin' ); ?></p>
			<p class="body tsk-text-secondary tsk-measure-46"><?php esc_html_e( 'Decidimos abrir 2Skin con la convicción de crear un espacio donde los pacientes se sientan escuchados y acompañados, y construyan hábitos sostenibles en el tiempo. En 2Skin no solo hacemos tratamientos: buscamos mejorar la calidad de vida, porque una piel saludable es una vida saludable.', '2skin' ); ?></p>
		</div>
		<?php echo tsk_media( 'espacio-1.jpg', array( 'alt' => __( 'Espacio clínico 2skin', '2skin' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>

<section class="tsk-section tsk-section--dark tsk-on-dark">
	<div class="container">
		<blockquote class="tsk-quote tsk-quote--about"><?php esc_html_e( 'No hacemos más. Hacemos lo que tu piel necesita.', '2skin' ); ?></blockquote>
		<div class="tsk-principles">
			<?php
			$tsk_principles = array(
				array( 'doc', __( 'Evaluación', '2skin' ), __( 'Historia clínica completa: antecedentes de piel, enfermedades crónicas, hábitos de vida y paraclínicos, más examen físico completo de la piel.', '2skin' ) ),
				array( 'mesh', __( 'Decisión', '2skin' ), __( 'Con esa información llegamos a un diagnóstico y construimos un tratamiento integral: protocolo individualizado con rutina en casa, suplementación, hábitos de vida y tratamientos en consultorio.', '2skin' ) ),
				array( 'shield', __( 'Casos que rechazamos', '2skin' ), __( 'Cambios no coherentes con tu estructura facial, expectativas no realistas, pacientes con indicación quirúrgica y quienes no quieren comprometerse con el tratamiento y solo buscan cambios inmediatos.', '2skin' ) ),
			);
			foreach ( $tsk_principles as [ $tsk_icon_name, $tsk_title, $tsk_text ] ) :
				?>
				<div class="tsk-principles__item">
					<?php echo tsk_icon( $tsk_icon_name, 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<h2 class="body-l"><?php echo esc_html( $tsk_title ); ?></h2>
					<p class="body-s tsk-text-inverse-secondary"><?php echo esc_html( $tsk_text ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="tsk-about-no">
	<div class="container">
		<div class="tsk-about-no__grid">
			<div>
				<p class="tsk-eyebrow"><?php esc_html_e( 'Qué no hacemos', '2skin' ); ?></p>
				<h2 class="h2 tsk-measure-14 tsk-head-gap-sm"><?php esc_html_e( 'Decir que no también es criterio.', '2skin' ); ?></h2>
			</div>
			<ul class="tsk-ruled-list">
				<?php
				$tsk_no = array(
					array( __( 'Rinomodelación', '2skin' ), __( 'Si buscas un cambio estructural de la nariz, la mejor alternativa es la cirugía, y una rinomodelación previa puede afectar ese resultado.', '2skin' ) ),
					array( __( 'Hilos tensores', '2skin' ), __( 'Resultados limitados, de poca duración, sin beneficios adicionales y, en la mayoría de casos, generan fibrosis.', '2skin' ) ),
					array( __( 'Procedimientos que comprometan tu seguridad', '2skin' ), __( 'Productos sin registro INVIMA, vencidos, sin estudios o con resultados poco favorables.', '2skin' ) ),
				);
				foreach ( $tsk_no as [ $tsk_title, $tsk_text ] ) :
					?>
					<li class="tsk-ruled-list__item">
						<?php echo tsk_icon( 'minus', 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<div>
							<h3 class="body tsk-weight-500"><?php echo esc_html( $tsk_title ); ?></h3>
							<p class="body-s tsk-text-secondary"><?php echo esc_html( $tsk_text ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>

<?php if ( $tsk_team ) : ?>
<section class="tsk-about-team" id="equipo">
	<div class="container">
		<p class="tsk-eyebrow"><?php esc_html_e( 'El equipo', '2skin' ); ?></p>
		<h2 class="h2 tsk-measure-22 tsk-head-gap"><?php esc_html_e( 'Dos médicas. Ambas evalúan, indican y aplican.', '2skin' ); ?></h2>
		<div class="tsk-team-grid">
			<?php foreach ( $tsk_team as $tsk_doctor ) : ?>
				<?php
				$tsk_meta = static fn( string $key ): string => (string) get_post_meta( $tsk_doctor->ID, $key, true );
				$tsk_name = get_the_title( $tsk_doctor );
				$tsk_full = $tsk_meta( '_tsk_nombre_completo' );
				?>
				<article class="tsk-doctor">
					<?php echo tsk_media( (int) get_post_thumbnail_id( $tsk_doctor ), array( 'ratio' => 'portrait', 'size' => 'tsk-portrait', 'alt' => $tsk_name, 'placeholder' => __( 'Retrato', '2skin' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div>
						<h3 class="h4"><?php echo esc_html( $tsk_name ); ?></h3>
						<p class="tsk-doctor__role"><?php echo esc_html( $tsk_meta( '_tsk_rol' ) ); ?></p>
					</div>
					<p class="body-s tsk-text-secondary"><?php echo esc_html( implode( ' · ', array_filter( array( $tsk_full, $tsk_meta( '_tsk_credenciales' ) ) ) ) ); ?></p>
					<div class="body-s tsk-text-secondary tsk-doctor__bio"><?php echo wp_kses_post( wpautop( $tsk_doctor->post_content ) ); ?></div>
					<?php if ( $tsk_meta( '_tsk_desde' ) ) : ?>
						<p class="tsk-doctor__role"><?php echo esc_html( $tsk_meta( '_tsk_desde' ) ); ?></p>
					<?php endif; ?>
					<?php if ( $tsk_meta( '_tsk_realiza' ) ) : ?>
						<p class="body-s tsk-text-secondary"><span class="tsk-text-primary"><?php esc_html_e( 'Realiza:', '2skin' ); ?></span> <?php echo esc_html( $tsk_meta( '_tsk_realiza' ) ); ?></p>
					<?php endif; ?>
					<?php
					/* translators: %s: nombre de la médica sin título. */
					echo tsk_button( sprintf( __( 'Reserva tu valoración con %s', '2skin' ), preg_replace( '/^Dra?\.\s*/u', '', $tsk_name ) ), tsk_url( 'reserva' ), array( 'variant' => 'text', 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="tsk-block">
	<div class="container tsk-half">
		<div class="tsk-stack tsk-stack--5">
			<p class="tsk-eyebrow"><?php esc_html_e( 'La sede', '2skin' ); ?></p>
			<h2 class="h2 tsk-measure-18"><?php esc_html_e( 'Un espacio clínico, no un spa.', '2skin' ); ?></h2>
			<p class="body tsk-text-secondary tsk-measure-42"><?php esc_html_e( 'Atendemos en consultorios de Medicenter, al norte de Bogotá. Solo usamos productos con registro INVIMA, que te mostramos antes de cada aplicación.', '2skin' ); ?></p>
			<ul class="tsk-ruled-list tsk-ruled-list--compact">
				<?php
				$tsk_sede = array(
					array( 'pin', tsk_contact( 'direccion' ) ),
					array( 'clock', __( 'Atención solo con cita previa · no trabajamos festivos', '2skin' ) ),
					array( 'chat', __( 'Valoración también virtual, por Google Meet', '2skin' ) ),
					array( 'lock', __( 'Datos de salud protegidos conforme a la Ley 1581 de 2012', '2skin' ) ),
				);
				foreach ( $tsk_sede as [ $tsk_icon_name, $tsk_text ] ) :
					?>
					<li class="tsk-ruled-list__item"><?php echo tsk_icon( $tsk_icon_name, 'tsk-icon tsk-text-sage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="body-s"><?php echo esc_html( $tsk_text ); ?></span></li>
				<?php endforeach; ?>
			</ul>
			<?php echo tsk_button( __( 'Agendar valoración', '2skin' ), tsk_url( 'reserva' ), array( 'class' => 'tsk-self-start' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="tsk-mosaic">
			<?php echo tsk_media( 'espacio-2.jpg', array( 'ratio' => 'portrait', 'alt' => __( 'Consultorio 2skin', '2skin' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="tsk-stack tsk-stack--4">
				<?php echo tsk_media( 'clinica-2.jpg', array( 'ratio' => 'square', 'alt' => __( 'Sala de espera', '2skin' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo tsk_media( 'clinica-4.jpg', array( 'ratio' => 'square', 'alt' => __( 'Insumos con trazabilidad', '2skin' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
