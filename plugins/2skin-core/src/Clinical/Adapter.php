<?php
/**
 * Contrato del adaptador de la plataforma clínica externa.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Clinical;

defined( 'ABSPATH' ) || exit;

/**
 * Envía la historia clínica previa a la plataforma clínica. Nunca debe guardarla en WordPress.
 */
interface Adapter {

	/**
	 * ¿Está configurado y en línea?
	 */
	public function is_available(): bool;

	/**
	 * Envía las respuestas de la paciente asociadas al pedido.
	 *
	 * @param \WC_Order $order   Pedido (identifica a la paciente y la cita).
	 * @param array     $answers Respuestas saneadas (ver History::questions()).
	 * @return bool Si la plataforma las recibió.
	 */
	public function send( \WC_Order $order, array $answers ): bool;
}
