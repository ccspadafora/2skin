<?php
/**
 * Caja de datos de la médica.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Admin;

use TwoSkin\Core\Content_Types;

defined( 'ABSPATH' ) || exit;

/**
 * Edición de los campos de tsk_medica en el administrador.
 */
final class Meta_Boxes {

	private const NONCE = 'tsk_medica_nonce';

	/**
	 * Engancha la caja y el guardado.
	 */
	public static function hooks(): void {
		add_action( 'add_meta_boxes_' . Content_Types::MEDICA, array( self::class, 'add' ) );
		add_action( 'save_post_' . Content_Types::MEDICA, array( self::class, 'save' ), 10, 2 );
	}

	/**
	 * Añade la caja.
	 */
	public static function add(): void {
		add_meta_box( 'tsk-medica-datos', __( 'Datos de la médica', '2skin-core' ), array( self::class, 'render' ), null, 'normal', 'high' );
	}

	/**
	 * Pinta los campos.
	 *
	 * @param \WP_Post $post Médica.
	 */
	public static function render( \WP_Post $post ): void {
		wp_nonce_field( self::NONCE, self::NONCE );
		echo '<p class="description">' . esc_html__( 'La bio va en el contenido y el retrato en la imagen destacada. El orden se controla con «Orden» en Atributos.', '2skin-core' ) . '</p>';
		echo '<table class="form-table" role="presentation"><tbody>';
		foreach ( Content_Types::MEDICA_FIELDS as $key => $label ) {
			$value = get_post_meta( $post->ID, $key, true );
			$type  = '_tsk_amelia_employee_id' === $key ? 'number' : 'text';
			printf(
				'<tr><th scope="row"><label for="%1$s">%2$s</label></th><td><input type="%3$s" class="large-text" id="%1$s" name="%1$s" value="%4$s"></td></tr>',
				esc_attr( $key ),
				esc_html( $label ),
				esc_attr( $type ),
				esc_attr( (string) $value )
			);
		}
		echo '</tbody></table>';
	}

	/**
	 * Guarda los campos (nonce + permisos; el saneamiento lo hace register_post_meta).
	 *
	 * @param int      $post_id ID.
	 * @param \WP_Post $post    Médica.
	 */
	public static function save( int $post_id, \WP_Post $post ): void {
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ self::NONCE ] ) ), self::NONCE ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( array_keys( Content_Types::MEDICA_FIELDS ) as $key ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- se sanea con el sanitize_callback registrado.
			$value = wp_unslash( $_POST[ $key ] );
			if ( '' === $value ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}
}
