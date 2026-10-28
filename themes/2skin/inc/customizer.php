<?php
/**
 * Datos de contacto editables en Apariencia → Personalizar → 2Skin · Contacto.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valores por defecto de contacto.
 */
function tsk_contact_defaults(): array {
	return array(
		'direccion' => 'Medicenter · Calle 120a #7-62, Bogotá',
		'horario'   => 'Atención solo con cita previa',
		'whatsapp'  => '',
		'pqrs'      => '2skinpqrs@gmail.com',
	);
}

/**
 * Lee un dato de contacto.
 *
 * @param string $key direccion|horario|whatsapp|pqrs.
 */
function tsk_contact( string $key ): string {
	$defaults = tsk_contact_defaults();
	return (string) get_theme_mod( "tsk_contact_{$key}", $defaults[ $key ] ?? '' );
}

/**
 * URL de WhatsApp (wa.me) o '' si no hay número configurado.
 */
function tsk_whatsapp_url(): string {
	$number = preg_replace( '/\D+/', '', tsk_contact( 'whatsapp' ) );
	return $number ? 'https://wa.me/' . $number : '';
}

/**
 * Registra la sección y los controles.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function tsk_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'tsk_contact',
		array(
			'title'    => __( '2Skin · Contacto', '2skin' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'direccion' => array( __( 'Dirección', '2skin' ), 'sanitize_text_field', 'text' ),
		'horario'   => array( __( 'Horario', '2skin' ), 'sanitize_text_field', 'text' ),
		'whatsapp'  => array( __( 'WhatsApp (con indicativo, ej. 573001234567)', '2skin' ), 'sanitize_text_field', 'text' ),
		'pqrs'      => array( __( 'Correo PQRS', '2skin' ), 'sanitize_email', 'email' ),
	);

	foreach ( $fields as $key => [ $label, $sanitize, $type ] ) {
		$wp_customize->add_setting(
			"tsk_contact_{$key}",
			array(
				'default'           => tsk_contact_defaults()[ $key ],
				'sanitize_callback' => $sanitize,
			)
		);
		$wp_customize->add_control(
			"tsk_contact_{$key}",
			array(
				'label'   => $label,
				'section' => 'tsk_contact',
				'type'    => $type,
			)
		);
	}
}
add_action( 'customize_register', 'tsk_customize_register' );
