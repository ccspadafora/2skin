<?php
/**
 * WooCommerce › Bonos de regalo: consulta de saldo, redención en consultorio y anulación.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Admin;

use TwoSkin\Core\Gifts\Repository;
use TwoSkin\Core\Patients\Repository as Patients;

defined( 'ABSPATH' ) || exit;

/**
 * Pantalla de bonos.
 */
final class Gifts_Admin {

	private const SLUG   = 'tsk-bonos';
	private const ACTION = 'tsk_bono_movimiento';

	/**
	 * Engancha el menú y el guardado.
	 */
	public static function hooks(): void {
		add_action( 'admin_menu', array( self::class, 'menu' ), 60 );
		add_action( 'admin_post_' . self::ACTION, array( self::class, 'handle' ) );
	}

	/**
	 * Submenú en WooCommerce.
	 */
	public static function menu(): void {
		add_submenu_page( 'woocommerce', __( 'Bonos de regalo', '2skin-core' ), __( 'Bonos de regalo', '2skin-core' ), Repository::CAPABILITY, self::SLUG, array( self::class, 'render' ) );
	}

	/**
	 * URL de la pantalla (listado o detalle).
	 *
	 * @param array $args Parámetros.
	 */
	private static function url( array $args = array() ): string {
		return add_query_arg( array_merge( array( 'page' => self::SLUG ), $args ), admin_url( 'admin.php' ) );
	}

	/**
	 * Pesos sin decimales.
	 *
	 * @param float $amount Monto.
	 */
	private static function money( float $amount ): string {
		return html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Registra una redención en consultorio o una anulación.
	 */
	public static function handle(): void {
		$id = isset( $_POST['bono'] ) ? absint( $_POST['bono'] ) : 0;
		if ( ! current_user_can( Repository::CAPABILITY ) || ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), self::ACTION . $id ) ) {
			wp_die( esc_html__( 'No tienes permiso para hacer esto.', '2skin-core' ), 403 );
		}
		$bono = Repository::get( $id );
		if ( ! $bono ) {
			wp_safe_redirect( self::url() );
			exit;
		}
		$tipo   = isset( $_POST['tipo'] ) ? sanitize_key( wp_unslash( $_POST['tipo'] ) ) : '';
		$nota   = isset( $_POST['nota'] ) ? sanitize_text_field( wp_unslash( $_POST['nota'] ) ) : '';
		$amount = isset( $_POST['monto'] ) ? (float) preg_replace( '/\D+/', '', sanitize_text_field( wp_unslash( $_POST['monto'] ) ) ) : 0;
		$msg    = 'error';

		if ( 'consultorio' === $tipo && $amount > 0 && '' !== $nota ) {
			$msg = Repository::debit( $id, $amount, 'redencion_consultorio', 0, $nota ) ? 'redimido' : 'sin_saldo';
		} elseif ( 'anular' === $tipo && '' !== $nota ) {
			Repository::void( $id, 0, $nota );
			$msg = 'anulado';
		}
		wp_safe_redirect( self::url( array( 'bono' => $id, 'tsk_msg' => $msg ) ) );
		exit;
	}

	/**
	 * Listado o detalle.
	 */
	public static function render(): void {
		if ( ! current_user_can( Repository::CAPABILITY ) ) {
			wp_die( esc_html__( 'No tienes permiso para ver esta página.', '2skin-core' ) );
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- navegación de solo lectura.
		$id = isset( $_GET['bono'] ) ? absint( $_GET['bono'] ) : 0;
		$id ? self::render_detail( $id ) : self::render_list();
	}

	/**
	 * Listado con búsqueda.
	 */
	private static function render_list(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- búsqueda de solo lectura.
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$page   = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
		// phpcs:enable
		$result = Repository::search( $search, $page );
		$pages  = (int) ceil( $result['total'] / 50 );

		// Un código exacto abre directamente su detalle.
		if ( 1 === $result['total'] && Repository::looks_like( $search ) ) {
			self::render_detail( $result['rows'][0]['id'] );
			return;
		}
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Bonos de regalo', '2skin-core' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Busca el código que presenta la paciente para ver su saldo y registrar lo que se usa en el consultorio. Los usos en línea se registran solos.', '2skin-core' ); ?></p>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( self::SLUG ); ?>">
				<p class="search-box">
					<label class="screen-reader-text" for="tsk-bonos-s"><?php esc_html_e( 'Buscar bono', '2skin-core' ); ?></label>
					<input type="search" id="tsk-bonos-s" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Código, nombre o correo', '2skin-core' ); ?>">
					<?php submit_button( __( 'Buscar', '2skin-core' ), '', '', false ); ?>
				</p>
			</form>
			<table class="widefat striped">
				<thead><tr>
					<th><?php esc_html_e( 'Código', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Comprador', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Valor', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Saldo', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Vence', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Estado', '2skin-core' ); ?></th>
				</tr></thead>
				<tbody>
					<?php if ( ! $result['rows'] ) : ?>
						<tr><td colspan="6"><?php esc_html_e( 'No hay bonos que coincidan.', '2skin-core' ); ?></td></tr>
					<?php endif; ?>
					<?php foreach ( $result['rows'] as $bono ) : ?>
						<?php $buyer = $bono['user_id'] ? get_userdata( $bono['user_id'] ) : null; ?>
						<tr>
							<td><a href="<?php echo esc_url( self::url( array( 'bono' => $bono['id'] ) ) ); ?>"><strong><?php echo esc_html( $bono['codigo'] ); ?></strong></a></td>
							<td><?php echo $buyer ? esc_html( $buyer->display_name . ' · ' . $buyer->user_email ) : '—'; ?></td>
							<td><?php echo esc_html( self::money( $bono['valor'] ) ); ?></td>
							<td><?php echo esc_html( self::money( $bono['saldo'] ) ); ?></td>
							<td><?php echo esc_html( Patients::format_date( $bono['vence'], 'j M Y' ) ); ?></td>
							<td><?php echo esc_html( Repository::status_label( $bono ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php
			if ( $pages > 1 ) {
				echo '<div class="tablenav"><div class="tablenav-pages">';
				echo wp_kses_post( paginate_links( array( 'base' => add_query_arg( 'paged', '%#%' ), 'current' => $page, 'total' => $pages ) ) );
				echo '</div></div>';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Detalle: saldo, movimientos y formularios.
	 *
	 * @param int $id Bono.
	 */
	private static function render_detail( int $id ): void {
		$bono = Repository::get( $id );
		if ( ! $bono ) {
			echo '<div class="wrap"><p>' . esc_html__( 'El bono no existe.', '2skin-core' ) . '</p></div>';
			return;
		}
		$messages = array(
			'redimido'  => array( 'success', __( 'Redención registrada.', '2skin-core' ) ),
			'anulado'   => array( 'success', __( 'Bono anulado.', '2skin-core' ) ),
			'sin_saldo' => array( 'error', __( 'No se registró: el monto supera el saldo o el bono está vencido o anulado.', '2skin-core' ) ),
			'error'     => array( 'error', __( 'Completa el monto y la nota.', '2skin-core' ) ),
		);
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- solo muestra un aviso.
		$msg   = isset( $_GET['tsk_msg'] ) ? sanitize_key( wp_unslash( $_GET['tsk_msg'] ) ) : '';
		$order = $bono['order_id'] ? wc_get_order( $bono['order_id'] ) : null;
		$types = array(
			'emision'               => __( 'Compra', '2skin-core' ),
			'redencion_online'      => __( 'Uso en línea', '2skin-core' ),
			'reverso_online'        => __( 'Devolución (pedido no pagado)', '2skin-core' ),
			'redencion_consultorio' => __( 'Uso en consultorio', '2skin-core' ),
			'anulacion'             => __( 'Anulación', '2skin-core' ),
		);
		?>
		<div class="wrap">
			<h1><?php echo esc_html( sprintf( /* translators: %s: código. */ __( 'Bono %s', '2skin-core' ), $bono['codigo'] ) ); ?></h1>
			<p><a href="<?php echo esc_url( self::url() ); ?>">&larr; <?php esc_html_e( 'Todos los bonos', '2skin-core' ); ?></a></p>
			<?php if ( isset( $messages[ $msg ] ) ) : ?>
				<div class="notice notice-<?php echo esc_attr( $messages[ $msg ][0] ); ?>"><p><?php echo esc_html( $messages[ $msg ][1] ); ?></p></div>
			<?php endif; ?>

			<table class="widefat striped" style="max-width:640px">
				<tbody>
					<tr><th><?php esc_html_e( 'Estado', '2skin-core' ); ?></th><td><strong><?php echo esc_html( Repository::status_label( $bono ) ); ?></strong></td></tr>
					<tr><th><?php esc_html_e( 'Saldo', '2skin-core' ); ?></th><td><strong><?php echo esc_html( self::money( $bono['saldo'] ) ); ?></strong> <?php echo esc_html( sprintf( /* translators: %s: valor. */ __( 'de %s', '2skin-core' ), self::money( $bono['valor'] ) ) ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Vence', '2skin-core' ); ?></th><td><?php echo esc_html( Patients::format_date( $bono['vence'] ) ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Pedido de compra', '2skin-core' ); ?></th><td><?php echo $order ? '<a href="' . esc_url( $order->get_edit_order_url() ) . '">#' . esc_html( $order->get_order_number() ) . '</a>' : '—'; ?></td></tr>
				</tbody>
			</table>

			<?php if ( $bono['usable'] ) : ?>
				<h2><?php esc_html_e( 'Registrar uso en consultorio', '2skin-core' ); ?></h2>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
					<input type="hidden" name="bono" value="<?php echo esc_attr( (string) $bono['id'] ); ?>">
					<input type="hidden" name="tipo" value="consultorio">
					<?php wp_nonce_field( self::ACTION . $bono['id'] ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th><label for="tsk-bono-monto"><?php esc_html_e( 'Monto', '2skin-core' ); ?></label></th>
							<td><input type="text" inputmode="numeric" id="tsk-bono-monto" name="monto" class="regular-text" required placeholder="<?php echo esc_attr( (string) (int) $bono['saldo'] ); ?>">
							<p class="description"><?php esc_html_e( 'En pesos, sin puntos. No puede superar el saldo.', '2skin-core' ); ?></p></td>
						</tr>
						<tr>
							<th><label for="tsk-bono-nota"><?php esc_html_e( 'Nota', '2skin-core' ); ?></label></th>
							<td><input type="text" id="tsk-bono-nota" name="nota" class="regular-text" required maxlength="255" placeholder="<?php esc_attr_e( 'Ej.: valoración presencial 12 nov, factura 1234', '2skin-core' ); ?>">
							<p class="description"><?php esc_html_e( 'Sin datos clínicos: solo el servicio y el comprobante.', '2skin-core' ); ?></p></td>
						</tr>
					</table>
					<?php submit_button( __( 'Registrar uso', '2skin-core' ) ); ?>
				</form>
			<?php endif; ?>

			<h2><?php esc_html_e( 'Movimientos', '2skin-core' ); ?></h2>
			<table class="widefat striped">
				<thead><tr>
					<th><?php esc_html_e( 'Fecha', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Movimiento', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Monto', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Saldo', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Pedido', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Registró', '2skin-core' ); ?></th>
					<th><?php esc_html_e( 'Nota', '2skin-core' ); ?></th>
				</tr></thead>
				<tbody>
					<?php foreach ( Repository::moves( $bono['id'] ) as $move ) : ?>
						<?php
						$move_order = $move['order_id'] ? wc_get_order( (int) $move['order_id'] ) : null;
						$who        = $move['user_id'] ? get_userdata( (int) $move['user_id'] ) : null;
						?>
						<tr>
							<td><?php echo esc_html( mysql2date( 'j M Y · g:i a', $move['creado'] ) ); ?></td>
							<td><?php echo esc_html( $types[ $move['tipo'] ] ?? $move['tipo'] ); ?></td>
							<td><?php echo esc_html( ( (float) $move['monto'] > 0 ? '+' : '−' ) . self::money( abs( (float) $move['monto'] ) ) ); ?></td>
							<td><?php echo esc_html( self::money( (float) $move['saldo'] ) ); ?></td>
							<td><?php echo $move_order ? '<a href="' . esc_url( $move_order->get_edit_order_url() ) . '">#' . esc_html( $move_order->get_order_number() ) . '</a>' : '—'; ?></td>
							<td><?php echo esc_html( $who ? $who->display_name : '—' ); ?></td>
							<td><?php echo esc_html( $move['nota'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php if ( 'anulado' !== $bono['estado'] ) : ?>
				<h2><?php esc_html_e( 'Anular bono', '2skin-core' ); ?></h2>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( '¿Anular este bono? Su saldo dejará de poder usarse.', '2skin-core' ) ); ?>');">
					<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
					<input type="hidden" name="bono" value="<?php echo esc_attr( (string) $bono['id'] ); ?>">
					<input type="hidden" name="tipo" value="anular">
					<?php wp_nonce_field( self::ACTION . $bono['id'] ); ?>
					<p>
						<label for="tsk-bono-motivo"><?php esc_html_e( 'Motivo', '2skin-core' ); ?></label>
						<input type="text" id="tsk-bono-motivo" name="nota" class="regular-text" required maxlength="255">
						<?php submit_button( __( 'Anular', '2skin-core' ), 'delete', '', false ); ?>
					</p>
				</form>
			<?php endif; ?>
		</div>
		<?php
	}
}
