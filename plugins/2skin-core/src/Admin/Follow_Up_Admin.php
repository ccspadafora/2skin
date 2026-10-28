<?php
/**
 * Administración del seguimiento: sección en el perfil de la paciente y listado general.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Admin;

use TwoSkin\Core\Catalog;
use TwoSkin\Core\Patients\Follow_Up;
use TwoSkin\Core\Patients\Repository;

defined( 'ABSPATH' ) || exit;

/**
 * Pantallas de seguimiento.
 */
final class Follow_Up_Admin {

	private const NONCE = 'tsk_seguimiento_nonce';
	private const SLUG  = 'tsk-seguimiento';

	/**
	 * Engancha las pantallas.
	 */
	public static function hooks(): void {
		add_action( 'show_user_profile', array( self::class, 'render_profile' ) );
		add_action( 'edit_user_profile', array( self::class, 'render_profile' ) );
		add_action( 'personal_options_update', array( self::class, 'save_profile' ) );
		add_action( 'edit_user_profile_update', array( self::class, 'save_profile' ) );
		add_action( 'admin_menu', array( self::class, 'menu' ) );
	}

	/**
	 * Submenú bajo Usuarios.
	 */
	public static function menu(): void {
		add_users_page( __( 'Seguimiento de pacientes', '2skin-core' ), __( 'Seguimiento', '2skin-core' ), Repository::CAPABILITY, self::SLUG, array( self::class, 'render_list' ) );
	}

	/**
	 * Opciones del selector: «Tratamiento (todas las opciones)» y cada opción. Valor «producto:variación».
	 *
	 * @return array<string, string>
	 */
	private static function choices(): array {
		$choices  = array();
		$products = wc_get_products(
			array(
				'status'  => 'publish',
				'limit'   => -1,
				'orderby' => 'menu_order',
				'order'   => 'ASC',
			)
		);
		foreach ( $products as $product ) {
			if ( ! Catalog::is_treatment( $product->get_id() ) ) {
				continue;
			}
			/* translators: %s: tratamiento. */
			$choices[ $product->get_id() . ':0' ] = sprintf( __( '%s · todas las opciones', '2skin-core' ), $product->get_name() );
			foreach ( $product->get_children() as $variation_id ) {
				$variation = wc_get_product( $variation_id );
				$values    = $variation ? array_values( $variation->get_attributes() ) : array();
				if ( $values ) {
					$choices[ $product->get_id() . ':' . $variation_id ] = '— ' . $product->get_name() . ' · ' . $values[0];
				}
			}
		}
		return $choices;
	}

	/**
	 * Sección del perfil.
	 *
	 * @param \WP_User $user Usuario editado.
	 */
	public static function render_profile( \WP_User $user ): void {
		if ( ! current_user_can( Repository::CAPABILITY ) ) {
			return;
		}
		$rows    = Repository::for_user( $user->ID );
		$default = wp_date( 'Y-m-d', strtotime( '+' . Follow_Up::DEFAULT_MONTHS . ' months' ) );
		wp_nonce_field( self::NONCE, self::NONCE );
		?>
		<h2 id="tsk-seguimiento"><?php esc_html_e( '2Skin · Seguimiento', '2skin-core' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Tratamientos que esta paciente puede reservar en línea sin pasar por valoración. Registra solo la indicación; los datos clínicos van en la historia clínica, no aquí.', '2skin-core' ); ?></p>
		<?php if ( $rows ) : ?>
			<table class="widefat striped" style="max-width:900px;margin:12px 0">
				<thead><tr>
					<th><?php esc_html_e( 'Tratamiento', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Opciones', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Vence', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Estado', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Origen', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Revocar', '2skin-core' ); ?></th>
				</tr></thead>
				<tbody>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><?php echo esc_html( get_the_title( $row['product_id'] ) ); ?></td>
							<td><?php echo esc_html( Follow_Up::options_label( $row ) ); ?></td>
							<td><?php echo esc_html( $row['vence'] ? Repository::format_date( $row['vence'], 'j M Y' ) : __( 'Sin vencimiento', '2skin-core' ) ); ?></td>
							<td><?php echo esc_html( self::status_label( $row ) ); ?></td>
							<td>
								<?php
								$order = $row['order_id'] ? wc_get_order( $row['order_id'] ) : null;
								if ( $order ) {
									/* translators: %d: número de pedido. */
									printf( '<a href="%s">%s</a>', esc_url( $order->get_edit_order_url() ), esc_html( sprintf( __( 'Pedido #%d', '2skin-core' ), $row['order_id'] ) ) );
								} else {
									esc_html_e( 'Manual', '2skin-core' );
								}
								?>
							</td>
							<td>
								<?php if ( 'activa' === $row['estado'] ) : ?>
									<input type="checkbox" name="tsk_seguimiento_revocar[]" value="<?php echo esc_attr( (string) $row['id'] ); ?>" aria-label="<?php esc_attr_e( 'Revocar', '2skin-core' ); ?>">
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="tsk_seguimiento_tratamiento"><?php esc_html_e( 'Indicar tratamiento', '2skin-core' ); ?></label></th>
				<td>
					<select id="tsk_seguimiento_tratamiento" name="tsk_seguimiento[tratamiento]">
						<option value=""><?php esc_html_e( '— Ninguno —', '2skin-core' ); ?></option>
						<?php foreach ( self::choices() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="tsk_seguimiento_vence"><?php esc_html_e( 'Vigente hasta', '2skin-core' ); ?></label></th>
				<td>
					<input type="date" id="tsk_seguimiento_vence" name="tsk_seguimiento[vence]" value="<?php echo esc_attr( $default ); ?>" min="<?php echo esc_attr( Repository::today() ); ?>">
					<p class="description"><?php esc_html_e( 'Si ya tiene ese tratamiento indicado, se suman las opciones y se toma la fecha más lejana.', '2skin-core' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Guarda altas y revocaciones desde el perfil.
	 *
	 * @param int $user_id Usuario editado.
	 */
	public static function save_profile( int $user_id ): void {
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ self::NONCE ] ) ), self::NONCE ) ) {
			return;
		}
		if ( ! current_user_can( Repository::CAPABILITY ) || ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		$revoke = isset( $_POST['tsk_seguimiento_revocar'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['tsk_seguimiento_revocar'] ) ) : array();
		foreach ( $revoke as $id ) {
			Repository::revoke( $id, $user_id );
		}

		$new    = isset( $_POST['tsk_seguimiento'] ) ? (array) wp_unslash( $_POST['tsk_seguimiento'] ) : array();
		$choice = sanitize_text_field( (string) ( $new['tratamiento'] ?? '' ) );
		if ( ! preg_match( '/^(\d+):(\d+)$/', $choice, $m ) ) {
			return;
		}
		$product_id   = (int) $m[1];
		$variation_id = (int) $m[2];
		if ( ! Catalog::is_treatment( $product_id ) || ( $variation_id && wp_get_post_parent_id( $variation_id ) !== $product_id ) ) {
			return;
		}
		$vence = sanitize_text_field( (string) ( $new['vence'] ?? '' ) );
		$date  = \DateTimeImmutable::createFromFormat( '!Y-m-d', $vence );
		$vence = $date && $date->format( 'Y-m-d' ) === $vence ? $vence : null;

		Repository::grant( $user_id, $product_id, $variation_id ? array( $variation_id ) : array(), $vence );
	}

	/**
	 * Listado general.
	 */
	public static function render_list(): void {
		if ( ! current_user_can( Repository::CAPABILITY ) ) {
			wp_die( esc_html__( 'No tienes permiso para ver esta página.', '2skin-core' ) );
		}
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- búsqueda de solo lectura.
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$page   = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
		// phpcs:enable
		$result = Repository::search( $search, $page );
		$pages  = (int) ceil( $result['total'] / 50 );
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Seguimiento de pacientes', '2skin-core' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Para indicar un tratamiento, abre el perfil de la paciente (Usuarios) y usa la sección «2Skin · Seguimiento». Los pedidos completados con un tratamiento lo indican solos por 12 meses.', '2skin-core' ); ?></p>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( self::SLUG ); ?>">
				<p class="search-box">
					<label class="screen-reader-text" for="tsk-seguimiento-s"><?php esc_html_e( 'Buscar paciente', '2skin-core' ); ?></label>
					<input type="search" id="tsk-seguimiento-s" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Nombre o correo', '2skin-core' ); ?>">
					<?php submit_button( __( 'Buscar', '2skin-core' ), '', '', false ); ?>
				</p>
			</form>
			<table class="widefat striped">
				<thead><tr>
					<th><?php esc_html_e( 'Paciente', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Tratamiento', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Opciones', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Vence', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Estado', '2skin-core' ); ?></th>
				</tr></thead>
				<tbody>
					<?php if ( ! $result['rows'] ) : ?>
						<tr><td colspan="5"><?php esc_html_e( 'No hay indicaciones registradas.', '2skin-core' ); ?></td></tr>
					<?php endif; ?>
					<?php foreach ( $result['rows'] as $row ) : ?>
						<?php $patient = get_userdata( $row['user_id'] ); ?>
						<tr>
							<td>
								<?php if ( $patient ) : ?>
									<a href="<?php echo esc_url( get_edit_user_link( $patient->ID ) . '#tsk-seguimiento' ); ?>"><?php echo esc_html( $patient->display_name ); ?></a><br>
									<span class="description"><?php echo esc_html( $patient->user_email ); ?></span>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( get_the_title( $row['product_id'] ) ); ?></td>
							<td><?php echo esc_html( Follow_Up::options_label( $row ) ); ?></td>
							<td><?php echo esc_html( $row['vence'] ? Repository::format_date( $row['vence'], 'j M Y' ) : __( 'Sin vencimiento', '2skin-core' ) ); ?></td>
							<td><?php echo esc_html( self::status_label( $row ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php
			if ( $pages > 1 ) {
				echo '<div class="tablenav"><div class="tablenav-pages">';
				echo wp_kses_post(
					paginate_links(
						array(
							'base'    => add_query_arg( 'paged', '%#%' ),
							'current' => $page,
							'total'   => $pages,
						)
					)
				);
				echo '</div></div>';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Estado legible.
	 *
	 * @param array $row Indicación.
	 */
	private static function status_label( array $row ): string {
		if ( 'revocada' === $row['estado'] ) {
			return __( 'Revocada', '2skin-core' );
		}
		return $row['vigente'] ? __( 'Vigente', '2skin-core' ) : __( 'Vencida', '2skin-core' );
	}
}
