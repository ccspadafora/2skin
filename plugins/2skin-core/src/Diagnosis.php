<?php
/**
 * Diagnóstico de piel: preguntas y orientación preliminar.
 *
 * No es un diagnóstico médico ni guarda nada: las respuestas llegan por POST, se valida cada una contra
 * sus opciones y el resultado se calcula en la misma petición.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Preguntas y reglas de orientación.
 */
final class Diagnosis {

	/**
	 * Preguntas: clave => [ pregunta, ayuda, opciones valor => etiqueta ].
	 *
	 * @return array<string, array{0: string, 1: string, 2: array<string, string>}>
	 */
	public static function questions(): array {
		return (array) apply_filters(
			'tsk_diagnostico_preguntas',
			array(
				'necesidad'  => array(
					__( '¿Qué te gustaría mejorar primero?', '2skin-core' ),
					__( 'Elige lo que más te pesa hoy. En valoración revisamos el resto.', '2skin-core' ),
					array(
						'lineas'      => __( 'Las líneas que me marca el gesto', '2skin-core' ),
						'firmeza'     => __( 'La firmeza: siento la piel menos sostenida', '2skin-core' ),
						'manchas'     => __( 'Manchas, poro, textura o cicatrices', '2skin-core' ),
						'hidratacion' => __( 'La piel se ve opaca y deshidratada', '2skin-core' ),
						'contorno'    => __( 'El contorno: labios, mentón, mandíbula', '2skin-core' ),
					),
				),
				'horizonte'  => array(
					__( '¿Con qué horizonte de tiempo cuentas?', '2skin-core' ),
					__( 'Algunos protocolos muestran cambio en días; otros trabajan durante meses.', '2skin-core' ),
					array(
						'corto' => __( 'Quiero ver algo en dos o tres semanas', '2skin-core' ),
						'medio' => __( 'Puedo esperar dos o tres meses', '2skin-core' ),
						'largo' => __( 'Me interesa un plan de un año', '2skin-core' ),
					),
				),
				'previos'    => array(
					__( '¿Has hecho antes algún procedimiento?', '2skin-core' ),
					__( 'Los antecedentes cambian la indicación y a veces el orden del plan.', '2skin-core' ),
					array(
						'nunca'   => __( 'Nunca', '2skin-core' ),
						'toxina'  => __( 'Toxina botulínica', '2skin-core' ),
						'relleno' => __( 'Rellenos o ácido hialurónico', '2skin-core' ),
						'bio'     => __( 'Bioestimuladores o láser', '2skin-core' ),
					),
				),
				'visible'    => array(
					__( '¿Qué tan visible quieres que sea el cambio?', '2skin-core' ),
					__( 'Este es el punto donde más pacientes y médicas se desalinean. Prefiere ser honesto.', '2skin-core' ),
					array(
						'sutil' => __( 'Que nadie note que hice algo', '2skin-core' ),
						'medio' => __( 'Que se note que estoy mejor, sin saber por qué', '2skin-core' ),
						'claro' => __( 'Un cambio claro pero natural', '2skin-core' ),
					),
				),
				'rutina'     => array(
					__( '¿Cómo describirías tu rutina de cuidado actual?', '2skin-core' ),
					__( 'Sin cuidado en casa, buena parte del resultado clínico se pierde.', '2skin-core' ),
					array(
						'buena'   => __( 'Uso fotoprotector todos los días', '2skin-core' ),
						'media'   => __( 'Tengo rutina, pero irregular', '2skin-core' ),
						'ninguna' => __( 'Prácticamente ninguna', '2skin-core' ),
					),
				),
			)
		);
	}

	/**
	 * Respuestas enviadas y válidas (null si falta alguna).
	 *
	 * @return array<string, string>|null
	 */
	public static function posted(): ?array {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- formulario público de solo lectura: no cambia datos.
		if ( ! isset( $_POST['tsk_dx'] ) || ! is_array( $_POST['tsk_dx'] ) ) {
			return null;
		}
		$raw = array_map( static fn( $v ): string => is_string( $v ) ? sanitize_key( $v ) : '', wp_unslash( $_POST['tsk_dx'] ) );
		// phpcs:enable
		$answers = array();
		foreach ( self::questions() as $key => [ , , $options ] ) {
			if ( ! isset( $raw[ $key ], $options[ $raw[ $key ] ] ) ) {
				return null;
			}
			$answers[ $key ] = $raw[ $key ];
		}
		return $answers;
	}

	/**
	 * Orientación: título, cuerpo, cuidado en casa y tratamientos sugeridos (slugs de producto).
	 *
	 * @param array<string, string> $a Respuestas válidas.
	 * @return array{title: string, body: string, care: string, slugs: string[]}
	 */
	public static function recommend( array $a ): array {
		$slugs = match ( $a['necesidad'] ) {
			'lineas'      => 'corto' === $a['horizonte'] ? array( 'toxina-botulinica', 'hidratacion-nctf' ) : array( 'toxina-botulinica', 'bioestimuladores' ),
			'firmeza'     => array( 'bioestimuladores', 'hidratacion-nctf' ),
			'manchas'     => array( 'laser-co2', 'hidratacion-nctf' ),
			'hidratacion' => array( 'hidratacion-nctf', 'bioestimuladores' ),
			default       => array( 'acido-hialuronico', 'bioestimuladores' ),
		};
		if ( 'sutil' === $a['visible'] && ! in_array( 'hidratacion-nctf', $slugs, true ) ) {
			$slugs = array( $slugs[0], 'hidratacion-nctf' );
		}

		$titles = array(
			'lineas'      => __( 'Tu prioridad parece ser el movimiento.', '2skin-core' ),
			'firmeza'     => __( 'Tu prioridad parece ser el soporte y el colágeno.', '2skin-core' ),
			'manchas'     => __( 'Tu prioridad parece ser pigmento y textura.', '2skin-core' ),
			'hidratacion' => __( 'Tu prioridad parece ser calidad y luz de la piel.', '2skin-core' ),
			'contorno'    => __( 'Tu prioridad parece ser proporción y contorno.', '2skin-core' ),
		);
		$bodies = array(
			'corto' => __( 'Buscas cambio en semanas, así que empezamos por protocolos de instauración rápida y dejamos el trabajo de colágeno para una segunda etapa.', '2skin-core' ),
			'medio' => __( 'Con dos o tres meses de horizonte podemos plantear un protocolo con efecto acumulativo y no solo un procedimiento aislado.', '2skin-core' ),
			'largo' => __( 'Con un año de horizonte tiene sentido un plan por etapas: primero calidad de piel, después soporte y mantenimiento.', '2skin-core' ),
		);
		$cares = array(
			'buena'   => __( 'Tu rutina actual juega a favor: el resultado clínico se sostiene mejor cuando la fotoprotección ya es un hábito.', '2skin-core' ),
			'media'   => __( 'Antes de escalar el protocolo conviene ordenar la rutina en casa. Sin constancia, buena parte del resultado se pierde.', '2skin-core' ),
			'ninguna' => __( 'Empezaríamos por establecer una rutina básica en paralelo. Sin fotoprotección diaria, ningún protocolo rinde lo que puede.', '2skin-core' ),
		);

		return (array) apply_filters(
			'tsk_diagnostico_resultado',
			array(
				'title' => $titles[ $a['necesidad'] ] ?? __( 'Tenemos por dónde empezar.', '2skin-core' ),
				'body'  => $bodies[ $a['horizonte'] ] ?? '',
				'care'  => $cares[ $a['rutina'] ] ?? '',
				'slugs' => $slugs,
			),
			$a
		);
	}
}
