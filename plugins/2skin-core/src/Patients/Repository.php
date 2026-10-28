<?php
/**
 * Tabla de indicaciones de seguimiento.
 *
 * Una indicación habilita a una paciente para reservar en línea un tratamiento (o algunas de sus opciones)
 * hasta una fecha. Solo guarda lo operativo: nada de diagnóstico, notas ni historia clínica.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Patients;

defined( 'ABSPATH' ) || exit;

/**
 * Acceso a {prefix}tsk_seguimiento.
 */
final class Repository {

	public const DB_VERSION = '1';
	public const CAPABILITY = 'tsk_gestionar_seguimiento';

	private const OPTION = 'tsk_seguimiento_db';

	/**
	 * Nombre de la tabla.
	 */
	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'tsk_seguimiento';
	}

	/**
	 * Crea o actualiza la tabla y da el permiso de gestión a administración y gerencia de tienda.
	 */
	public static function install(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table   = self::table();
		$charset = $wpdb->get_charset_collate();
		dbDelta(
			"CREATE TABLE {$table} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				user_id bigint(20) unsigned NOT NULL,
				product_id bigint(20) unsigned NOT NULL,
				variation_ids varchar(255) NOT NULL DEFAULT '',
				vence date NULL DEFAULT NULL,
				estado varchar(20) NOT NULL DEFAULT 'activa',
				origen varchar(20) NOT NULL DEFAULT 'manual',
				order_id bigint(20) unsigned NULL DEFAULT NULL,
				creado_por bigint(20) unsigned NOT NULL DEFAULT 0,
				creado datetime NOT NULL,
				actualizado datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY user_product (user_id,product_id),
				KEY product_id (product_id)
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
	 * Instala si la versión guardada no coincide (el plugin ya estaba activo antes de este módulo).
	 */
	public static function maybe_install(): void {
		if ( get_option( self::OPTION ) !== self::DB_VERSION ) {
			self::install();
		}
	}

	/**
	 * Indicaciones de una paciente (todas, o solo las vigentes).
	 *
	 * @param int  $user_id     Paciente.
	 * @param bool $only_active Solo activas y no vencidas.
	 * @return array<int, array<string, mixed>>
	 */
	public static function for_user( int $user_id, bool $only_active = false ): array {
		global $wpdb;
		$table = self::table();
		$sql   = $only_active
			? $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d AND estado = 'activa' AND ( vence IS NULL OR vence >= %s ) ORDER BY vence IS NULL, vence", $user_id, self::today() ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			: $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d ORDER BY estado, actualizado DESC", $user_id ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return array_map( array( self::class, 'hydrate' ), (array) $wpdb->get_results( $sql, ARRAY_A ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Listado para administración, paginado y con búsqueda por nombre o correo.
	 *
	 * @param string $search  Texto.
	 * @param int    $page    Página (desde 1).
	 * @param int    $per     Por página.
	 * @return array{rows: array, total: int}
	 */
	public static function search( string $search, int $page, int $per = 50 ): array {
		global $wpdb;
		$table  = self::table();
		$where  = '1=1';
		$params = array();
		if ( '' !== $search ) {
			$like     = '%' . $wpdb->esc_like( $search ) . '%';
			$where    = 'u.user_email LIKE %s OR u.display_name LIKE %s';
			$params[] = $like;
			$params[] = $like;
		}
		$from   = "FROM {$table} s INNER JOIN {$wpdb->users} u ON u.ID = s.user_id WHERE {$where}";
		$total  = (int) $wpdb->get_var( $params ? $wpdb->prepare( "SELECT COUNT(*) {$from}", $params ) : "SELECT COUNT(*) {$from}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL
		$params = array_merge( $params, array( $per, max( 0, ( $page - 1 ) * $per ) ) );
		$rows   = $wpdb->get_results( $wpdb->prepare( "SELECT s.* {$from} ORDER BY s.estado, s.actualizado DESC LIMIT %d OFFSET %d", $params ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL

		return array(
			'rows'  => array_map( array( self::class, 'hydrate' ), (array) $rows ),
			'total' => $total,
		);
	}

	/**
	 * Crea la indicación o amplía la vigente del mismo tratamiento (une opciones y toma el vencimiento mayor).
	 *
	 * @param int         $user_id       Paciente.
	 * @param int         $product_id    Tratamiento.
	 * @param int[]       $variation_ids Opciones (vacío = todas).
	 * @param string|null $vence         Fecha Y-m-d o null (sin vencimiento).
	 * @param string      $origen        manual | pedido.
	 * @param int         $order_id      Pedido de origen.
	 * @return int ID de la indicación.
	 */
	public static function grant( int $user_id, int $product_id, array $variation_ids, ?string $vence, string $origen = 'manual', int $order_id = 0 ): int {
		global $wpdb;
		$table         = self::table();
		$variation_ids = array_values( array_unique( array_filter( array_map( 'absint', $variation_ids ) ) ) );
		$now           = current_time( 'mysql' );
		$current       = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d AND product_id = %d AND estado = 'activa' ORDER BY id DESC LIMIT 1", $user_id, $product_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( $current ) {
			$current = self::hydrate( $current );
			$merged  = ( ! $current['variation_ids'] || ! $variation_ids ) ? array() : array_values( array_unique( array_merge( $current['variation_ids'], $variation_ids ) ) );
			$until   = ( null === $current['vence'] || null === $vence ) ? null : max( $current['vence'], $vence );
			$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$table,
				array(
					'variation_ids' => implode( ',', $merged ),
					'vence'         => $until,
					'actualizado'   => $now,
				),
				array( 'id' => $current['id'] ),
				array( '%s', '%s', '%s' ),
				array( '%d' )
			);
			return $current['id'];
		}

		$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$table,
			array(
				'user_id'       => $user_id,
				'product_id'    => $product_id,
				'variation_ids' => implode( ',', $variation_ids ),
				'vence'         => $vence,
				'estado'        => 'activa',
				'origen'        => $origen,
				'order_id'      => $order_id ? $order_id : null,
				'creado_por'    => get_current_user_id(),
				'creado'        => $now,
				'actualizado'   => $now,
			),
			array( '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s' )
		);
		return (int) $wpdb->insert_id;
	}

	/**
	 * Revoca una indicación de una paciente.
	 *
	 * @param int $id      Indicación.
	 * @param int $user_id Paciente (evita revocar la de otra persona).
	 */
	public static function revoke( int $id, int $user_id ): void {
		global $wpdb;
		$wpdb->update( self::table(), array( 'estado' => 'revocada', 'actualizado' => current_time( 'mysql' ) ), array( 'id' => $id, 'user_id' => $user_id ), array( '%s', '%s' ), array( '%d', '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * Borra todas las indicaciones de una paciente (cuenta eliminada o solicitud de supresión).
	 *
	 * @param int $user_id Paciente.
	 * @return int Filas borradas.
	 */
	public static function delete_user( int $user_id ): int {
		global $wpdb;
		return (int) $wpdb->delete( self::table(), array( 'user_id' => $user_id ), array( '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * Fecha de hoy en la zona horaria del sitio.
	 */
	public static function today(): string {
		return wp_date( 'Y-m-d' );
	}

	/**
	 * Fecha Y-m-d legible en la zona horaria del sitio (ej. 27 de octubre de 2027).
	 *
	 * @param string $date   Fecha Y-m-d.
	 * @param string $format Formato de wp_date.
	 */
	public static function format_date( string $date, string $format = 'j \\d\\e F \\d\\e Y' ): string {
		$parsed = \DateTimeImmutable::createFromFormat( '!Y-m-d', $date, wp_timezone() );
		return $parsed ? (string) wp_date( $format, $parsed->getTimestamp() ) : $date;
	}

	/**
	 * Tipos de PHP para una fila.
	 *
	 * @param array $row Fila cruda.
	 */
	private static function hydrate( array $row ): array {
		$row['id']            = (int) $row['id'];
		$row['user_id']       = (int) $row['user_id'];
		$row['product_id']    = (int) $row['product_id'];
		$row['order_id']      = (int) $row['order_id'];
		$row['variation_ids'] = '' === $row['variation_ids'] ? array() : array_map( 'intval', explode( ',', $row['variation_ids'] ) );
		$row['vence']         = $row['vence'] ? (string) $row['vence'] : null;
		$row['vigente']       = 'activa' === $row['estado'] && ( null === $row['vence'] || $row['vence'] >= self::today() );
		return $row;
	}
}
