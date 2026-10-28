<?php
/**
 * Tipos de contenido y taxonomías propias.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Médicas, preguntas frecuentes, grupos de preguntas y necesidades.
 */
final class Content_Types {

	public const MEDICA    = 'tsk_medica';
	public const PREGUNTA  = 'tsk_pregunta';
	public const FAQ_GRUPO = 'tsk_faq_grupo';
	public const NECESIDAD = 'tsk_necesidad';
	public const ZONA      = 'tsk_zona';
	public const CASO      = 'tsk_caso';

	/**
	 * Meta de la pregunta frecuente: tratamientos donde se muestra (una fila por producto).
	 */
	public const PREGUNTA_PRODUCTO = '_tsk_producto';

	/**
	 * Campos de la médica: clave de meta => etiqueta. La bio va en el contenido y el retrato en la imagen destacada.
	 */
	public const MEDICA_FIELDS = array(
		'_tsk_nombre_completo'    => 'Nombre completo',
		'_tsk_rol'                => 'Rol',
		'_tsk_credenciales'       => 'Formación y registro médico',
		'_tsk_desde'              => 'Trayectoria',
		'_tsk_realiza'            => 'Procedimientos que realiza',
		'_tsk_amelia_employee_id' => 'ID de empleada en Amelia',
	);

	/**
	 * Registra CPT y taxonomías.
	 */
	public static function register(): void {
		register_post_type(
			self::MEDICA,
			array(
				'labels'        => array(
					'name'          => __( 'Médicas', '2skin-core' ),
					'singular_name' => __( 'Médica', '2skin-core' ),
					'add_new_item'  => __( 'Añadir médica', '2skin-core' ),
					'edit_item'     => __( 'Editar médica', '2skin-core' ),
				),
				'public'        => false,
				'show_ui'       => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-groups',
				'menu_position' => 25,
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields' ),
			)
		);

		register_post_type(
			self::PREGUNTA,
			array(
				'labels'        => array(
					'name'          => __( 'Preguntas frecuentes', '2skin-core' ),
					'singular_name' => __( 'Pregunta', '2skin-core' ),
					'add_new_item'  => __( 'Añadir pregunta', '2skin-core' ),
					'edit_item'     => __( 'Editar pregunta', '2skin-core' ),
				),
				'public'        => false,
				'show_ui'       => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-editor-help',
				'menu_position' => 26,
				'supports'      => array( 'title', 'editor', 'page-attributes' ),
			)
		);

		register_taxonomy(
			self::FAQ_GRUPO,
			array( self::PREGUNTA ),
			array(
				'labels'            => array(
					'name'          => __( 'Grupos', '2skin-core' ),
					'singular_name' => __( 'Grupo', '2skin-core' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
			)
		);

		register_post_type(
			self::CASO,
			array(
				'labels'        => array(
					'name'          => __( 'Casos', '2skin-core' ),
					'singular_name' => __( 'Caso', '2skin-core' ),
					'add_new_item'  => __( 'Añadir caso', '2skin-core' ),
					'edit_item'     => __( 'Editar caso', '2skin-core' ),
				),
				'public'        => false,
				'show_ui'       => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-images-alt2',
				'menu_position' => 27,
				'supports'      => array( 'title', 'editor', 'page-attributes' ),
			)
		);

		// Zonas: filtro del catálogo. Sin URL propia; se filtra con ?zona= en Tratamientos.
		register_taxonomy(
			self::ZONA,
			array( 'product' ),
			array(
				'labels'            => array(
					'name'          => __( 'Zonas', '2skin-core' ),
					'singular_name' => __( 'Zona', '2skin-core' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'rewrite'           => false,
			)
		);

		// Necesidades: orientan el catálogo («¿Qué quieres mejorar?») y filtran los tratamientos.
		register_taxonomy(
			self::NECESIDAD,
			array( 'product' ),
			array(
				'labels'            => array(
					'name'          => __( 'Necesidades', '2skin-core' ),
					'singular_name' => __( 'Necesidad', '2skin-core' ),
				),
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'rewrite'           => array( 'slug' => 'necesidad' ),
			)
		);
	}

	/**
	 * Registra meta de médicas y de términos con saneamiento y permisos.
	 */
	public static function register_meta(): void {
		foreach ( array_keys( self::MEDICA_FIELDS ) as $key ) {
			$is_id = '_tsk_amelia_employee_id' === $key;
			register_post_meta(
				self::MEDICA,
				$key,
				array(
					'type'              => $is_id ? 'integer' : 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => $is_id ? 'absint' : 'sanitize_text_field',
					'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
				)
			);
		}

		register_post_meta(
			self::PREGUNTA,
			self::PREGUNTA_PRODUCTO,
			array(
				'type'              => 'integer',
				'single'            => false,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
			)
		);

		Cases::register_meta();

		$term_meta = array(
			self::NECESIDAD => array( 'tsk_nota', 'tsk_icono' ),
			self::FAQ_GRUPO => array( 'tsk_icono' ),
		);
		foreach ( $term_meta as $taxonomy => $keys ) {
			foreach ( $keys as $key ) {
				register_term_meta(
					$taxonomy,
					$key,
					array(
						'type'              => 'string',
						'single'            => true,
						'show_in_rest'      => true,
						'sanitize_callback' => 'tsk_icono' === $key ? 'sanitize_key' : 'sanitize_text_field',
						'auth_callback'     => static fn() => current_user_can( 'manage_categories' ),
					)
				);
			}
		}
	}
}
