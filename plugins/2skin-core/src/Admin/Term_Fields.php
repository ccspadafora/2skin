<?php
/**
 * Campos de nota e icono en necesidades y grupos de preguntas.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Admin;

use TwoSkin\Core\Content_Types;

defined( 'ABSPATH' ) || exit;

/**
 * Formularios de términos.
 */
final class Term_Fields {

	private const NONCE = 'tsk_term_nonce';

	/**
	 * Iconos del sprite del tema (assets/icons.svg).
	 */
	public const ICONS = array( 'arrow', 'droplet', 'wave', 'mesh', 'sun', 'eye', 'lips', 'face', 'body', 'needle', 'spark', 'shield', 'clock', 'calendar', 'cart', 'user', 'check', 'doc', 'flask', 'pin', 'chat', 'lock', 'gift', 'refresh', 'kit', 'filter' );

	/**
	 * Engancha los formularios.
	 */
	public static function hooks(): void {
		foreach ( array( Content_Types::NECESIDAD, Content_Types::FAQ_GRUPO ) as $taxonomy ) {
			add_action( "{$taxonomy}_add_form_fields", array( self::class, 'render_add' ) );
			add_action( "{$taxonomy}_edit_form_fields", array( self::class, 'render_edit' ), 10, 2 );
			add_action( "created_{$taxonomy}", array( self::class, 'save' ) );
			add_action( "edited_{$taxonomy}", array( self::class, 'save' ) );
		}
	}

	/**
	 * Campos al crear.
	 *
	 * @param string $taxonomy Taxonomía.
	 */
	public static function render_add( string $taxonomy ): void {
		wp_nonce_field( self::NONCE, self::NONCE );
		if ( Content_Types::NECESIDAD === $taxonomy ) {
			echo '<div class="form-field"><label for="tsk_nota">' . esc_html__( 'Nota', '2skin-core' ) . '</label><input type="text" id="tsk_nota" name="tsk_nota"></div>';
		}
		echo '<div class="form-field"><label for="tsk_icono">' . esc_html__( 'Icono', '2skin-core' ) . '</label>' . self::select( '' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- select() escapa.
	}

	/**
	 * Campos al editar.
	 *
	 * @param \WP_Term $term     Término.
	 * @param string   $taxonomy Taxonomía.
	 */
	public static function render_edit( \WP_Term $term, string $taxonomy ): void {
		wp_nonce_field( self::NONCE, self::NONCE );
		if ( Content_Types::NECESIDAD === $taxonomy ) {
			printf(
				'<tr class="form-field"><th scope="row"><label for="tsk_nota">%s</label></th><td><input type="text" id="tsk_nota" name="tsk_nota" value="%s"></td></tr>',
				esc_html__( 'Nota', '2skin-core' ),
				esc_attr( (string) get_term_meta( $term->term_id, 'tsk_nota', true ) )
			);
		}
		printf(
			'<tr class="form-field"><th scope="row"><label for="tsk_icono">%s</label></th><td>%s</td></tr>',
			esc_html__( 'Icono', '2skin-core' ),
			self::select( (string) get_term_meta( $term->term_id, 'tsk_icono', true ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- select() escapa.
		);
	}

	/**
	 * Guarda nota e icono.
	 *
	 * @param int $term_id Término.
	 */
	public static function save( int $term_id ): void {
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ self::NONCE ] ) ), self::NONCE ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}
		if ( isset( $_POST['tsk_nota'] ) ) {
			update_term_meta( $term_id, 'tsk_nota', sanitize_text_field( wp_unslash( $_POST['tsk_nota'] ) ) );
		}
		if ( isset( $_POST['tsk_icono'] ) ) {
			$icon = sanitize_key( wp_unslash( $_POST['tsk_icono'] ) );
			update_term_meta( $term_id, 'tsk_icono', in_array( $icon, self::ICONS, true ) ? $icon : '' );
		}
	}

	/**
	 * Selector de icono.
	 *
	 * @param string $current Icono actual.
	 */
	private static function select( string $current ): string {
		$html = '<select id="tsk_icono" name="tsk_icono"><option value="">—</option>';
		foreach ( self::ICONS as $icon ) {
			$html .= sprintf( '<option value="%1$s"%2$s>%1$s</option>', esc_attr( $icon ), selected( $current, $icon, false ) );
		}
		return $html . '</select>';
	}
}
