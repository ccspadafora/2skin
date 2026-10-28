<?php
/**
 * Casos de Resultados: antes/después con su contexto clínico mínimo (sin datos que identifiquen a la paciente).
 *
 * Un caso solo se publica en el sitio si tiene autorización escrita o si está marcado como imagen ilustrativa.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Campos, edición y consulta de casos.
 */
final class Cases {

	private const NONCE = 'tsk_caso_nonce';

	/**
	 * Campos: meta => [ etiqueta, tipo ]. Tipos: image, text, rows, product, flag.
	 */
	public static function fields(): array {
		return array(
			'_tsk_etiqueta'    => array( __( 'Etiqueta (filtro)', '2skin-core' ), 'text' ),
			'_tsk_antes'       => array( __( 'Imagen antes (ID de medio)', '2skin-core' ), 'image' ),
			'_tsk_despues'     => array( __( 'Imagen después (ID de medio)', '2skin-core' ), 'image' ),
			'_tsk_contexto'    => array( __( 'Contexto (ej.: Mujer, 41 años)', '2skin-core' ), 'text' ),
			'_tsk_protocolo'   => array( __( 'Protocolo aplicado', '2skin-core' ), 'text' ),
			'_tsk_tiempo'      => array( __( 'Tiempo transcurrido', '2skin-core' ), 'text' ),
			'_tsk_filas'       => array( __( 'Detalle (Dato | Valor, una línea por fila)', '2skin-core' ), 'rows' ),
			'_tsk_producto'    => array( __( 'Tratamiento relacionado', '2skin-core' ), 'product' ),
			'_tsk_autorizado'  => array( __( 'Autorización escrita de la paciente archivada', '2skin-core' ), 'flag' ),
			'_tsk_ilustrativo' => array( __( 'Imagen ilustrativa (no es un caso real)', '2skin-core' ), 'flag' ),
		);
	}

	/**
	 * Engancha la caja de edición.
	 */
	public static function hooks(): void {
		add_action( 'add_meta_boxes_' . Content_Types::CASO, array( self::class, 'add_box' ) );
		add_action( 'save_post_' . Content_Types::CASO, array( self::class, 'save' ), 10, 2 );
	}

	/**
	 * Registra la meta.
	 */
	public static function register_meta(): void {
		foreach ( self::fields() as $key => [ , $type ] ) {
			$is_int = in_array( $type, array( 'image', 'product', 'flag' ), true );
			register_post_meta(
				Content_Types::CASO,
				$key,
				array(
					'type'              => $is_int ? 'integer' : 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => $is_int ? 'absint' : ( 'rows' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field' ),
					'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
				)
			);
		}
	}

	/**
	 * Casos publicables, en orden.
	 *
	 * @param int $product_id Limita a un tratamiento (0 = todos).
	 * @param int $limit      Máximo (-1 = todos).
	 * @return array<int, array<string, mixed>>
	 */
	public static function published( int $product_id = 0, int $limit = -1 ): array {
		$meta_query = array(
			'relation' => 'OR',
			array(
				'key'   => '_tsk_autorizado',
				'value' => 1,
			),
			array(
				'key'   => '_tsk_ilustrativo',
				'value' => 1,
			),
		);
		if ( $product_id ) {
			$meta_query = array(
				'relation' => 'AND',
				$meta_query,
				array(
					'key'   => '_tsk_producto',
					'value' => $product_id,
				),
			);
		}

		$posts = get_posts(
			array(
				'post_type'      => Content_Types::CASO,
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- pocos casos.
			)
		);

		return array_map( array( self::class, 'data' ), $posts );
	}

	/**
	 * Datos de un caso listos para la plantilla.
	 *
	 * @param \WP_Post $post Caso.
	 */
	public static function data( \WP_Post $post ): array {
		$meta = static fn( string $key ) => get_post_meta( $post->ID, $key, true );
		$tag  = (string) $meta( '_tsk_etiqueta' );

		return array(
			'id'          => $post->ID,
			'title'       => get_the_title( $post ),
			'body'        => wp_strip_all_tags( $post->post_content ),
			'tag'         => $tag,
			'tag_slug'    => sanitize_title( $tag ),
			'before'      => (int) $meta( '_tsk_antes' ),
			'after'       => (int) $meta( '_tsk_despues' ),
			'context'     => (string) $meta( '_tsk_contexto' ),
			'protocol'    => (string) $meta( '_tsk_protocolo' ),
			'elapsed'     => (string) $meta( '_tsk_tiempo' ),
			'rows'        => Treatment_Fields::parse( (string) $meta( '_tsk_filas' ), 'rows:2' ),
			'product_id'  => (int) $meta( '_tsk_producto' ),
			'illustrative' => (bool) $meta( '_tsk_ilustrativo' ),
		);
	}

	/**
	 * Añade la caja.
	 */
	public static function add_box(): void {
		add_meta_box( 'tsk-caso-datos', __( 'Datos del caso', '2skin-core' ), array( self::class, 'render' ), null, 'normal', 'high' );
	}

	/**
	 * Pinta los campos.
	 *
	 * @param \WP_Post $post Caso.
	 */
	public static function render( \WP_Post $post ): void {
		wp_nonce_field( self::NONCE, self::NONCE );
		echo '<p class="description">' . esc_html__( 'El título y el contenido son el titular y el texto del caso. No incluyas nombres ni datos que identifiquen a la paciente. Solo se publican casos con autorización escrita o marcados como ilustrativos.', '2skin-core' ) . '</p>';
		echo '<table class="form-table" role="presentation"><tbody>';
		foreach ( self::fields() as $key => [ $label, $type ] ) {
			$value = get_post_meta( $post->ID, $key, true );
			echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td>';
			switch ( $type ) {
				case 'image':
					printf( '<input type="number" min="0" class="small-text" id="%1$s" name="%1$s" value="%2$s"> ', esc_attr( $key ), esc_attr( (string) $value ) );
					if ( $value ) {
						echo wp_get_attachment_image( (int) $value, array( 80, 80 ) );
					}
					break;
				case 'rows':
					printf( '<textarea class="large-text code" rows="4" id="%1$s" name="%1$s">%2$s</textarea>', esc_attr( $key ), esc_textarea( (string) $value ) );
					break;
				case 'product':
					echo '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '"><option value="0">—</option>';
					foreach ( self::product_choices() as $id => $name ) {
						printf( '<option value="%1$d"%2$s>%3$s</option>', (int) $id, selected( (int) $value, $id, false ), esc_html( $name ) );
					}
					echo '</select>';
					break;
				case 'flag':
					printf( '<input type="hidden" name="%1$s" value="0"><input type="checkbox" id="%1$s" name="%1$s" value="1"%2$s>', esc_attr( $key ), checked( (int) $value, 1, false ) );
					break;
				default:
					printf( '<input type="text" class="large-text" id="%1$s" name="%1$s" value="%2$s">', esc_attr( $key ), esc_attr( (string) $value ) );
			}
			echo '</td></tr>';
		}
		echo '</tbody></table>';
	}

	/**
	 * Tratamientos publicados: ID => nombre.
	 *
	 * @return array<int, string>
	 */
	public static function product_choices(): array {
		$choices = array();
		foreach ( get_posts( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $product ) {
			$choices[ $product->ID ] = get_the_title( $product );
		}
		return $choices;
	}

	/**
	 * Guarda los campos.
	 *
	 * @param int      $post_id ID.
	 * @param \WP_Post $post    Caso.
	 */
	public static function save( int $post_id, \WP_Post $post ): void {
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ self::NONCE ] ) ), self::NONCE ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( array_keys( self::fields() ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- lo sanea el sanitize_callback registrado.
				update_post_meta( $post_id, $key, wp_unslash( $_POST[ $key ] ) );
			}
		}
	}
}
