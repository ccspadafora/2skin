<?php
/**
 * Bonos de regalo: saldo y movimientos.
 *
 * Cada bono tiene un código, un valor inicial, un saldo y una fecha de vencimiento. Todo cambio de saldo
 * queda en {prefix}tsk_bonos_mov (emisión, redención en línea o en consultorio, reverso, anulación).
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Gifts;

use TwoSkin\Core\Patients\Repository as Patients;

defined( 'ABSPATH' ) || exit;

/**
 * Acceso a {prefix}tsk_bonos y {prefix}tsk_bonos_mov.
 */
final class Repository {

	public const DB_VERSION = '1';
	public const CAPABILITY = 'tsk_gestionar_bonos';

	private const OPTION = 'tsk_bonos_db';

	/**
	 * Tabla de bonos.
	 */
	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'tsk_bonos';
	}

	/**
	 * Tabla de movimientos.
	 */
	public static function moves_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'tsk_bonos_mov';
	}

	/**
	 * Crea o actualiza las tablas y da el permiso de gestión a administración y gerencia de tienda.
	 */
	public static function install(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table   = self::table();
		$moves   = self::moves_table();
		$charset = $wpdb->get_charset_collate();
		dbDelta(
			"CREATE TABLE {$table} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				codigo varchar(20) NOT NULL,
				valor decimal(12,2) NOT NULL DEFAULT 0,
				saldo decimal(12,2) NOT NULL DEFAULT 0,
				estado varchar(20) NOT NULL DEFAULT 'activo',
				vence date NOT NULL,
				order_id bigint(20) unsigned NULL DEFAULT NULL,
				item_id bigint(20) unsigned NULL DEFAULT NULL,
				user_id bigint(20) unsigned NULL DEFAULT NULL,
				creado datetime NOT NULL,
				actualizado datetime NOT NULL,
				PRIMARY KEY  (id),
				UNIQUE KEY codigo (codigo),
				KEY order_id (order_id),
				KEY user_id (user_id)
			) {$charset};
			CREATE TABLE {$moves} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				bono_id bigint(20) unsigned NOT NULL,
				tipo varchar(30) NOT NULL,
				monto decimal(12,2) NOT NULL,
				saldo decimal(12,2) NOT NULL,
				order_id bigint(20) unsigned NULL DEFAULT NULL,
				user_id bigint(20) unsigned NOT NULL DEFAULT 0,
				nota varchar(255) NOT NULL DEFAULT '',
				creado datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY bono_id (bono_id),
				KEY order_id (order_id)
			) {$charset};"
		);

		foreach ( array( 'administrator', 'shop_manager' ) as $role_name ) {
			$role = get_role( $role_name );
			if ( $role ) {
				$role->add_cap( self::CAPABILITY );
			}
		}
		update_option( self::OPTION, self::DB_VERSION, false );
	}

	/**
	 * Instala si la versión guardada no coincide.
	 */
	public static function maybe_install(): void {
		if ( get_option( self::OPTION ) !== self::DB_VERSION ) {
			self::install();
		}
	}

	/**
	 * Código en mayúsculas y sin espacios (los cupones de WooCommerce llegan en minúsculas).
	 *
	 * @param string $code Código.
	 */
	public static function normalize( string $code ): string {
		return strtoupper( preg_replace( '/[^A-Za-z0-9\-]/', '', $code ) );
	}

	/**
	 * ¿Tiene forma de código de bono? (2SK-XXXX-XXXX).
	 *
	 * @param string $code Código.
	 */
	public static function looks_like( string $code ): bool {
		return (bool) preg_match( '/^2SK-[A-Z0-9]{4}-[A-Z0-9]{4}$/', self::normalize( $code ) );
	}

	/**
	 * Código nuevo e irrepetible, sin caracteres que se confundan (0/O, 1/I/L).
	 */
	public static function new_code(): string {
		$alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
		do {
			$chars = '';
			for ( $i = 0; $i < 8; $i++ ) {
				$chars .= $alphabet[ random_int( 0, strlen( $alphabet ) - 1 ) ];
			}
			$code = '2SK-' . substr( $chars, 0, 4 ) . '-' . substr( $chars, 4 );
		} while ( self::find( $code ) );
		return $code;
	}

	/**
	 * Bono por código, o null.
	 *
	 * @param string $code Código.
	 */
	public static function find( string $code ): ?array {
		global $wpdb;
		$table = self::table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE codigo = %s", self::normalize( $code ) ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $row ? self::hydrate( $row ) : null;
	}

	/**
	 * Bono por ID, o null.
	 *
	 * @param int $id ID.
	 */
	public static function get( int $id ): ?array {
		global $wpdb;
		$table = self::table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $row ? self::hydrate( $row ) : null;
	}

	/**
	 * Bonos emitidos por un pedido.
	 *
	 * @param int $order_id Pedido.
	 */
	public static function for_order( int $order_id ): array {
		global $wpdb;
		$table = self::table();
		$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE order_id = %d ORDER BY id", $order_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return array_map( array( self::class, 'hydrate' ), (array) $rows );
	}

	/**
	 * Movimientos de un bono, del más reciente al más antiguo.
	 *
	 * @param int $bono_id Bono.
	 */
	public static function moves( int $bono_id ): array {
		global $wpdb;
		$table = self::moves_table();
		return (array) $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE bono_id = %d ORDER BY id DESC", $bono_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Listado para administración: búsqueda por código, correo o nombre del comprador.
	 *
	 * @param string $search Texto.
	 * @param int    $page   Página (desde 1).
	 * @param int    $per    Por página.
	 * @return array{rows: array, total: int}
	 */
	public static function search( string $search, int $page, int $per = 50 ): array {
		global $wpdb;
		$table  = self::table();
		$where  = '1=1';
		$params = array();
		if ( '' !== $search ) {
			$like     = '%' . $wpdb->esc_like( $search ) . '%';
			$where    = 'b.codigo LIKE %s OR u.user_email LIKE %s OR u.display_name LIKE %s';
			$params   = array( '%' . $wpdb->esc_like( self::normalize( $search ) ) . '%', $like, $like );
		}
		$from   = "FROM {$table} b LEFT JOIN {$wpdb->users} u ON u.ID = b.user_id WHERE {$where}";
		$total  = (int) $wpdb->get_var( $params ? $wpdb->prepare( "SELECT COUNT(*) {$from}", $params ) : "SELECT COUNT(*) {$from}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL
		$params = array_merge( $params, array( $per, max( 0, ( $page - 1 ) * $per ) ) );
		$rows   = $wpdb->get_results( $wpdb->prepare( "SELECT b.* {$from} ORDER BY b.id DESC LIMIT %d OFFSET %d", $params ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL

		return array(
			'rows'  => array_map( array( self::class, 'hydrate' ), (array) $rows ),
			'total' => $total,
		);
	}

	/**
	 * Emite un bono pagado.
	 *
	 * @param float  $valor    Valor.
	 * @param string $vence    Y-m-d.
	 * @param int    $order_id Pedido.
	 * @param int    $item_id  Ítem del pedido.
	 * @param int    $user_id  Comprador (0 = invitado).
	 * @return array Bono creado.
	 */
	public static function issue( float $valor, string $vence, int $order_id, int $item_id, int $user_id ): array {
		global $wpdb;
		$now = current_time( 'mysql' );
		$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			self::table(),
			array(
				'codigo'      => self::new_code(),
				'valor'       => $valor,
				'saldo'       => $valor,
				'estado'      => 'activo',
				'vence'       => $vence,
				'order_id'    => $order_id,
				'item_id'     => $item_id,
				'user_id'     => $user_id ? $user_id : null,
				'creado'      => $now,
				'actualizado' => $now,
			),
			array( '%s', '%f', '%f', '%s', '%s', '%d', '%d', '%d', '%s', '%s' )
		);
		$id = (int) $wpdb->insert_id;
		self::log( $id, 'emision', $valor, $valor, $order_id, '' );
		return (array) self::get( $id );
	}

	/**
	 * Descuenta saldo de forma atómica (no deja el saldo negativo ni toca bonos anulados o vencidos).
	 *
	 * @param int    $id       Bono.
	 * @param float  $amount   Monto positivo.
	 * @param string $tipo     redencion_online | redencion_consultorio.
	 * @param int    $order_id Pedido (en línea).
	 * @param string $nota     Nota.
	 * @return bool Si se descontó.
	 */
	public static function debit( int $id, float $amount, string $tipo, int $order_id = 0, string $nota = '' ): bool {
		global $wpdb;
		if ( $amount <= 0 ) {
			return false;
		}
		$table   = self::table();
		$updated = $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"UPDATE {$table} SET saldo = saldo - %f, actualizado = %s WHERE id = %d AND estado = 'activo' AND vence >= %s AND saldo >= %f", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$amount,
				current_time( 'mysql' ),
				$id,
				Patients::today(),
				$amount
			)
		);
		if ( 1 !== $updated ) {
			return false;
		}
		$bono = self::get( $id );
		self::log( $id, $tipo, -$amount, (float) $bono['saldo'], $order_id, $nota );
		return true;
	}

	/**
	 * Devuelve saldo (reverso de una redención en línea cuyo pedido no se pagó).
	 *
	 * @param int    $id       Bono.
	 * @param float  $amount   Monto positivo.
	 * @param int    $order_id Pedido.
	 * @param string $nota     Nota.
	 */
	public static function credit( int $id, float $amount, int $order_id, string $nota = '' ): void {
		global $wpdb;
		if ( $amount <= 0 ) {
			return;
		}
		$table = self::table();
		$wpdb->query( $wpdb->prepare( "UPDATE {$table} SET saldo = LEAST( valor, saldo + %f ), actualizado = %s WHERE id = %d", $amount, current_time( 'mysql' ), $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$bono = self::get( $id );
		self::log( $id, 'reverso_online', $amount, (float) $bono['saldo'], $order_id, $nota );
	}

	/**
	 * Anula un bono (su saldo deja de poder usarse).
	 *
	 * @param int    $id       Bono.
	 * @param int    $order_id Pedido relacionado.
	 * @param string $nota     Motivo.
	 */
	public static function void( int $id, int $order_id, string $nota ): void {
		global $wpdb;
		$bono = self::get( $id );
		if ( ! $bono || 'anulado' === $bono['estado'] ) {
			return;
		}
		$wpdb->update( self::table(), array( 'estado' => 'anulado', 'saldo' => 0, 'actualizado' => current_time( 'mysql' ) ), array( 'id' => $id ), array( '%s', '%f', '%s' ), array( '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		self::log( $id, 'anulacion', -$bono['saldo'], 0, $order_id, $nota );
	}

	/**
	 * El comprador eliminó su cuenta: el bono sigue siendo del portador, sin vínculo con la persona.
	 *
	 * @param int $user_id Usuario.
	 */
	public static function detach_user( int $user_id ): void {
		global $wpdb;
		$wpdb->update( self::table(), array( 'user_id' => null ), array( 'user_id' => $user_id ), array( '%d' ), array( '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * Registra un movimiento.
	 *
	 * @param int    $bono_id  Bono.
	 * @param string $tipo     Tipo.
	 * @param float  $monto    Monto con signo.
	 * @param float  $saldo    Saldo después del movimiento.
	 * @param int    $order_id Pedido.
	 * @param string $nota     Nota.
	 */
	private static function log( int $bono_id, string $tipo, float $monto, float $saldo, int $order_id, string $nota ): void {
		global $wpdb;
		$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			self::moves_table(),
			array(
				'bono_id'  => $bono_id,
				'tipo'     => $tipo,
				'monto'    => $monto,
				'saldo'    => $saldo,
				'order_id' => $order_id ? $order_id : null,
				'user_id'  => get_current_user_id(),
				'nota'     => mb_substr( $nota, 0, 255 ),
				'creado'   => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%f', '%f', '%d', '%d', '%s', '%s' )
		);
	}

	/**
	 * Tipos de dato y estado efectivo (vencido / agotado).
	 *
	 * @param array $row Fila.
	 */
	private static function hydrate( array $row ): array {
		$row['id']       = (int) $row['id'];
		$row['valor']    = (float) $row['valor'];
		$row['saldo']    = (float) $row['saldo'];
		$row['order_id'] = (int) $row['order_id'];
		$row['user_id']  = (int) $row['user_id'];
		$row['usable']   = 'activo' === $row['estado'] && $row['vence'] >= Patients::today() && $row['saldo'] > 0;
		return $row;
	}

	/**
	 * Estado legible.
	 *
	 * @param array $bono Bono.
	 */
	public static function status_label( array $bono ): string {
		if ( 'anulado' === $bono['estado'] ) {
			return __( 'Anulado', '2skin-core' );
		}
		if ( $bono['vence'] < Patients::today() ) {
			return __( 'Vencido', '2skin-core' );
		}
		return $bono['saldo'] > 0 ? __( 'Activo', '2skin-core' ) : __( 'Agotado', '2skin-core' );
	}
}
