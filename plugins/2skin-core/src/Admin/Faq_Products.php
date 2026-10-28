<?php
/**
 * Caja «Mostrar en tratamientos» de la pregunta frecuente.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Admin;

use TwoSkin\Core\Cases;
use TwoSkin\Core\Content_Types;

defined( 'ABSPATH' ) || exit;

/**
 * Relaciona preguntas con tratamientos (meta múltiple _tsk_producto).
 */
final class Faq_Products {

	private const NONCE = 'tsk_faq_producto_nonce';

	/**
	 * Engancha la caja y el guardado.
	 */
	public static function hooks(): void {
		add_action( 'add_meta_boxes_' . Content_Types::PREGUNTA, array( self::class, 'add' ) );
		add_action( 'save_post_' . Content_Types::PREGUNTA, array( self::class, 'save' ), 10, 2 );
	}

	/**
	 * Añade la caja.
	 */
	public static function add(): void {
		add_meta_box( 'tsk-faq-productos', __( 'Mostrar en tratamientos', '2skin-core' ), array( self::class, 'render' ), null, 'side' );
	}

	/**
	 * Pinta una casilla por tratamiento.
	 *
	 * @param \WP_Post $post Pregunta.
	 */
	public static function render( \WP_Post $post ): void {
		wp_nonce_field( self::NONCE, self::NONCE );
		$selected = array_map( 'intval', get_post_meta( $post->ID, Content_Types::PREGUNTA_PRODUCTO ) );
		echo '<p class="description">' . esc_html__( 'Sin grupo, la pregunta solo aparece en los tratamientos marcados.', '2skin-core' ) . '</p>';
		foreach ( Cases::product_choices() as $id => $name ) {
			printf(
				'<label style="display:block;margin:4px 0"><input type="checkbox" name="tsk_faq_productos[]" value="%1$d"%2$s> %3$s</label>',
				(int) $id,
				checked( in_array( $id, $selected, true ), true, false ),
				esc_html( $name )
			);
		}
	}

	/**
	 * Guarda la relación.
	 *
	 * @param int      $post_id ID.
	 * @param \WP_Post $post    Pregunta.
	 */
	public static function save( int $post_id, \WP_Post $post ): void {
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ self::NONCE ] ) ), self::NONCE ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$ids = isset( $_POST['tsk_faq_productos'] ) ? array_unique( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['tsk_faq_productos'] ) ) ) ) : array();
		$ids = array_filter( $ids, static fn( int $id ): bool => 'product' === get_post_type( $id ) );

		delete_post_meta( $post_id, Content_Types::PREGUNTA_PRODUCTO );
		foreach ( $ids as $id ) {
			add_post_meta( $post_id, Content_Types::PREGUNTA_PRODUCTO, $id );
		}
	}
}
