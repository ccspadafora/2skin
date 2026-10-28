<?php
/**
 * Traducciones de terceros para el español de Colombia.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Amelia no trae es_CO: usa su traducción es_MX (sin tocar el plugin).
 */
final class I18n {

	/**
	 * Engancha el reemplazo.
	 */
	public static function hooks(): void {
		add_filter( 'load_textdomain_mofile', array( self::class, 'amelia_mofile' ), 10, 2 );
	}

	/**
	 * Cambia wpamelia-es_CO.mo por wpamelia-es_MX.mo si el primero no existe.
	 *
	 * @param string $mofile Ruta.
	 * @param string $domain Dominio.
	 */
	public static function amelia_mofile( string $mofile, string $domain ): string {
		if ( 'wpamelia' !== $domain || ! str_ends_with( $mofile, 'wpamelia-es_CO.mo' ) || is_readable( $mofile ) ) {
			return $mofile;
		}
		$fallback = str_replace( array( '/es_CO/', 'wpamelia-es_CO.mo' ), array( '/es_MX/', 'wpamelia-es_MX.mo' ), $mofile );
		return is_readable( $fallback ) ? $fallback : $mofile;
	}
}
