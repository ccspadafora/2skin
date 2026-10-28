<?php
/**
 * Bold (plugin oficial «Bold pagos en linea»): llaves fuera de la base de datos y script solo donde se paga.
 *
 * El cobro lo hace el plugin de Bold; aquí no hay lógica de pago. Las llaves se definen en wp-config.php
 * (o como variables de entorno) y se entregan al plugin por el filtro pre_option de cada opción.
 *
 *   define( 'TSK_BOLD_TEST_API_KEY', '…' );    define( 'TSK_BOLD_TEST_SECRET_KEY', '…' );
 *   define( 'TSK_BOLD_PROD_API_KEY', '…' );    define( 'TSK_BOLD_PROD_SECRET_KEY', '…' );
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Payments;

defined( 'ABSPATH' ) || exit;

/**
 * Integración de configuración con Bold.
 */
final class Bold {

	/**
	 * Opción del plugin de Bold => constante o variable de entorno.
	 */
	private const KEYS = array(
		'woocommerce_bold_co_test_api_key'    => 'TSK_BOLD_TEST_API_KEY',
		'woocommerce_bold_co_test_secret_key' => 'TSK_BOLD_TEST_SECRET_KEY',
		'woocommerce_bold_co_prod_api_key'    => 'TSK_BOLD_PROD_API_KEY',
		'woocommerce_bold_co_prod_secret_key' => 'TSK_BOLD_PROD_SECRET_KEY',
	);

	/**
	 * Engancha todo.
	 */
	public static function hooks(): void {
		foreach ( array_keys( self::KEYS ) as $option ) {
			add_filter( "pre_option_{$option}", array( self::class, 'read' ), 10, 2 );
			add_filter( "pre_update_option_{$option}", array( self::class, 'keep_out_of_db' ), 10, 3 );
		}
		add_action( 'wp_enqueue_scripts', array( self::class, 'limit_scripts' ), 100 );
		add_filter( 'gettext_bold-pagos-en-linea', array( self::class, 'texts' ), 10, 2 );
	}

	/**
	 * Mensaje más claro cuando Bold no logra abrir el pago (la cita ya se liberó).
	 *
	 * @param string $translation Texto traducido.
	 * @param string $text        Texto original.
	 */
	public static function texts( string $translation, string $text ): string {
		if ( 'No se logró redireccionar a la pasarela de pagos de Bold' === $text ) {
			return __( 'No pudimos abrir el pago con Bold y no se hizo ningún cobro. Elige de nuevo tu horario e intenta otra vez; si el problema sigue, escríbenos.', '2skin-core' );
		}
		return $translation;
	}

	/**
	 * Valor de la llave definida fuera del código, o '' si no hay.
	 *
	 * @param string $option Opción.
	 */
	private static function secret( string $option ): string {
		$name = self::KEYS[ $option ] ?? '';
		if ( ! $name ) {
			return '';
		}
		if ( defined( $name ) ) {
			return (string) constant( $name );
		}
		$env = getenv( $name );
		return false === $env ? '' : (string) $env;
	}

	/**
	 * La llave de wp-config.php tiene prioridad sobre la guardada en la base de datos.
	 *
	 * @param mixed  $pre    Valor previo (false para seguir con get_option).
	 * @param string $option Opción.
	 * @return mixed
	 */
	public static function read( $pre, string $option ) {
		$secret = self::secret( $option );
		return '' !== $secret ? $secret : $pre;
	}

	/**
	 * Si la llave viene de wp-config.php, no se copia a la base de datos al guardar el panel de Bold.
	 *
	 * @param mixed  $value     Nuevo valor.
	 * @param mixed  $old_value Valor anterior.
	 * @param string $option    Opción.
	 * @return mixed
	 */
	public static function keep_out_of_db( $value, $old_value, string $option ) {
		return '' !== self::secret( $option ) ? $old_value : $value;
	}

	/**
	 * Bold carga su librería externa y sus scripts del pago en todas las páginas; solo hacen falta donde se paga.
	 */
	public static function limit_scripts(): void {
		// Estilos de su panel de ajustes y de su widget de Elementor (el sitio no usa Elementor): sobran en el sitio público.
		wp_dequeue_style( 'woocommerce_bold_gateway_form_css' );
		wp_dequeue_style( 'bold-elementor-style' );
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return;
		}
		wp_dequeue_style( 'woocommerce_bold_gateway_css' );
		foreach ( array( 'woocommerce_bold_payment_button_js', 'woocommerce_bold_checkout_web_component_js', 'woocommerce_bold_checkout_basic_js' ) as $handle ) {
			wp_dequeue_script( $handle );
		}
	}
}
