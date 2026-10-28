<?php
/**
 * Ficha clínica del tratamiento (producto de WooCommerce).
 *
 * Los campos de lista se editan como texto: una línea por elemento, columnas separadas por « | »
 * y grupos abiertos con una línea «## Título». Así el contenido es editable sin plugins de campos.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Definición, edición y lectura de la ficha.
 */
final class Treatment_Fields {

	private const NONCE = 'tsk_ficha_nonce';

	/**
	 * Campos: clave => [ etiqueta, tipo, ayuda ]. Tipos: text, textarea, lines, rows:N, groups, icon.
	 */
	public static function fields(): array {
		return array(
			'demanda'       => array( __( 'Etiqueta destacada', '2skin-core' ), 'text', __( 'Ej.: Lo más consultado.', '2skin-core' ) ),
			'icono'         => array( __( 'Icono', '2skin-core' ), 'icon', '' ),
			'precio_texto'  => array( __( 'Texto de precio', '2skin-core' ), 'text', __( 'Reemplaza «Desde $X». Ej.: Precio según zona.', '2skin-core' ) ),
			'facts'         => array( __( 'Datos clave', '2skin-core' ), 'rows:2', __( 'Una línea por dato: Dato | Valor', '2skin-core' ) ),
			'evalua'        => array( __( 'Qué evaluamos', '2skin-core' ), 'lines', __( 'Una línea por punto.', '2skin-core' ) ),
			'que'           => array( __( 'Qué es', '2skin-core' ), 'textarea', '' ),
			'zonas_texto'   => array( __( 'Zonas (texto)', '2skin-core' ), 'textarea', '' ),
			'no_indicado'   => array( __( 'No está indicado si', '2skin-core' ), 'lines', __( 'Una línea por punto.', '2skin-core' ) ),
			'contra'        => array( __( 'Contraindicaciones', '2skin-core' ), 'groups', __( '«## Título» abre un grupo; debajo, una línea por punto.', '2skin-core' ) ),
			'protocolo'     => array( __( 'El proceso', '2skin-core' ), 'rows:3', __( 'Una línea por paso: Cuándo | Título | Descripción', '2skin-core' ) ),
			'etapas'        => array( __( 'Recuperación por etapas', '2skin-core' ), 'groups', __( '«## Título» abre un grupo; debajo, una línea por etapa.', '2skin-core' ) ),
			'eleccion'      => array( __( 'Cómo se elige el producto', '2skin-core' ), 'textarea', '' ),
			'presentacion'  => array( __( 'Presentación', '2skin-core' ), 'textarea', '' ),
			'trazabilidad'  => array( __( 'Trazabilidad', '2skin-core' ), 'textarea', '' ),
			'combos'        => array( __( 'Se combina con', '2skin-core' ), 'lines', __( 'Una línea por combinación.', '2skin-core' ) ),
			'marcas'        => array( __( 'Marcas', '2skin-core' ), 'rows:4', __( 'Una línea por marca: Nombre | Laboratorio | Registro INVIMA | Uso', '2skin-core' ) ),
			'incluye'       => array( __( 'Qué incluye', '2skin-core' ), 'lines', __( 'Una línea por punto.', '2skin-core' ) ),
			'avisos'        => array( __( 'Consideraciones clínicas', '2skin-core' ), 'lines', __( 'Una línea por punto.', '2skin-core' ) ),
			'amelia_servicio' => array( __( 'Servicio de Amelia (ID)', '2skin-core' ), 'text', __( 'Servicio que se agenda cuando una paciente en seguimiento reserva este tratamiento (Amelia → Servicios).', '2skin-core' ) ),
		);
	}

	/**
	 * Engancha registro de meta y la caja de edición.
	 */
	public static function hooks(): void {
		add_action( 'init', array( self::class, 'register_meta' ) );
		if ( is_admin() ) {
			add_action( 'add_meta_boxes_product', array( self::class, 'add_box' ) );
			add_action( 'save_post_product', array( self::class, 'save' ), 10, 2 );
		}
	}

	/**
	 * Registra cada campo como meta de producto (texto crudo).
	 */
	public static function register_meta(): void {
		foreach ( self::fields() as $key => [ , $type ] ) {
			register_post_meta(
				'product',
				'_tsk_' . $key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => false,
					'sanitize_callback' => in_array( $type, array( 'text', 'icon' ), true ) ? 'sanitize_text_field' : 'sanitize_textarea_field',
					'auth_callback'     => static fn() => current_user_can( 'edit_products' ),
				)
			);
		}
	}

	/**
	 * Lee un campo ya interpretado según su tipo.
	 *
	 * @param int    $post_id Producto.
	 * @param string $key     Clave sin prefijo.
	 * @return string|array
	 */
	public static function get( int $post_id, string $key ): string|array {
		$fields = self::fields();
		$raw    = (string) get_post_meta( $post_id, '_tsk_' . $key, true );
		$type   = $fields[ $key ][1] ?? 'text';

		return self::parse( $raw, $type );
	}

	/**
	 * Interpreta el texto crudo.
	 *
	 * @param string $raw  Texto.
	 * @param string $type Tipo del campo.
	 * @return string|array
	 */
	public static function parse( string $raw, string $type ): string|array {
		if ( in_array( $type, array( 'text', 'textarea', 'icon' ), true ) ) {
			return trim( $raw );
		}

		$lines = array_values( array_filter( array_map( 'trim', preg_split( '/\R/', $raw ) ), 'strlen' ) );

		if ( 'lines' === $type ) {
			return $lines;
		}

		if ( str_starts_with( $type, 'rows:' ) ) {
			$columns = (int) substr( $type, 5 );
			return array_map(
				static fn( string $line ): array => array_pad( array_map( 'trim', explode( '|', $line, $columns ) ), $columns, '' ),
				$lines
			);
		}

		// groups.
		$groups = array();
		foreach ( $lines as $line ) {
			if ( str_starts_with( $line, '##' ) ) {
				$groups[] = array(
					'title' => trim( ltrim( $line, '#' ) ),
					'items' => array(),
				);
			} else {
				if ( ! $groups ) {
					$groups[] = array(
						'title' => '',
						'items' => array(),
					);
				}
				$groups[ count( $groups ) - 1 ]['items'][] = $line;
			}
		}
		return $groups;
	}

	/**
	 * Convierte datos estructurados al texto editable (para importaciones).
	 *
	 * @param mixed  $value Valor.
	 * @param string $type  Tipo.
	 */
	public static function serialize( mixed $value, string $type ): string {
		if ( ! is_array( $value ) ) {
			return (string) $value;
		}
		if ( 'groups' === $type ) {
			$out = array();
			foreach ( $value as $group ) {
				$out[] = '## ' . $group['title'];
				foreach ( $group['items'] as $item ) {
					$out[] = $item;
				}
			}
			return implode( "\n", $out );
		}
		return implode( "\n", array_map( static fn( $row ) => is_array( $row ) ? implode( ' | ', $row ) : (string) $row, $value ) );
	}

	/**
	 * Añade la caja.
	 */
	public static function add_box(): void {
		add_meta_box( 'tsk-ficha', __( 'Ficha del tratamiento', '2skin-core' ), array( self::class, 'render' ), 'product', 'normal', 'high' );
	}

	/**
	 * Pinta los campos.
	 *
	 * @param \WP_Post $post Producto.
	 */
	public static function render( \WP_Post $post ): void {
		wp_nonce_field( self::NONCE, self::NONCE );
		echo '<p class="description">' . esc_html__( 'Contenido de la página del tratamiento. La descripción corta del producto es el texto de la tarjeta; la descripción larga, el párrafo principal.', '2skin-core' ) . '</p>';
		echo '<table class="form-table" role="presentation"><tbody>';
		foreach ( self::fields() as $key => [ $label, $type, $help ] ) {
			$name  = '_tsk_' . $key;
			$value = (string) get_post_meta( $post->ID, $name, true );
			echo '<tr><th scope="row"><label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label></th><td>';
			if ( 'text' === $type ) {
				printf( '<input type="text" class="large-text" id="%1$s" name="%1$s" value="%2$s">', esc_attr( $name ), esc_attr( $value ) );
			} elseif ( 'icon' === $type ) {
				echo '<select id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '"><option value="">—</option>';
				foreach ( Admin\Term_Fields::ICONS as $icon ) {
					printf( '<option value="%1$s"%2$s>%1$s</option>', esc_attr( $icon ), selected( $value, $icon, false ) );
				}
				echo '</select>';
			} else {
				$rows = 'textarea' === $type ? 3 : 6;
				printf( '<textarea class="large-text code" rows="%3$d" id="%1$s" name="%1$s">%2$s</textarea>', esc_attr( $name ), esc_textarea( $value ), (int) $rows );
			}
			if ( $help ) {
				echo '<p class="description">' . esc_html( $help ) . '</p>';
			}
			echo '</td></tr>';
		}
		echo '</tbody></table>';
	}

	/**
	 * Guarda los campos.
	 *
	 * @param int      $post_id ID.
	 * @param \WP_Post $post    Producto.
	 */
	public static function save( int $post_id, \WP_Post $post ): void {
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ self::NONCE ] ) ), self::NONCE ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( array_keys( self::fields() ) as $key ) {
			$name = '_tsk_' . $key;
			if ( isset( $_POST[ $name ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- lo sanea el sanitize_callback registrado.
				update_post_meta( $post_id, $name, wp_unslash( $_POST[ $name ] ) );
			}
		}
	}
}
