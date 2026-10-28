<?php
/**
 * Integración básica con WooCommerce. Las plantillas propias van en woocommerce/.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

// El tema trae su propio CSS; los estilos genéricos de WooCommerce solo estorban.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Número de ítems en el carrito.
 */
function tsk_cart_count(): int {
	return WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

/**
 * Contador del carrito en el encabezado.
 */
function tsk_cart_badge(): string {
	$count = tsk_cart_count();
	return sprintf( '<span class="tsk-cart-count%s">%d</span>', $count > 0 ? ' tsk-cart-count--filled' : '', $count );
}

/**
 * Actualiza el contador tras añadir al carrito por AJAX.
 *
 * @param array $fragments Fragmentos.
 */
function tsk_cart_fragments( array $fragments ): array {
	$fragments['span.tsk-cart-count'] = tsk_cart_badge();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'tsk_cart_fragments' );

/**
 * Etiqueta de precio de referencia: texto propio, «Desde $X» o «Precio en valoración».
 *
 * @param WC_Product $product Producto.
 */
function tsk_product_from_label( WC_Product $product ): string {
	$text = (string) $product->get_meta( '_tsk_precio_texto' );
	if ( $text ) {
		return $text;
	}
	$price = $product->is_type( 'variable' ) ? $product->get_variation_price( 'min' ) : $product->get_price();
	/* translators: %s: precio. */
	return $price ? sprintf( __( 'Desde %s', '2skin' ), tsk_cop( (float) $price ) ) : __( 'Precio en valoración', '2skin' );
}

/**
 * Datos de un producto para TreatmentCard.
 *
 * @param WC_Product $product Producto.
 */
function tsk_product_card_args( WC_Product $product ): array {
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	return array(
		'name'        => $product->get_name(),
		'category'    => $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '',
		'description' => wp_strip_all_tags( $product->get_short_description() ),
		'image'       => (int) $product->get_image_id(),
		'url'         => $product->get_permalink(),
		'badges'      => array_filter( array( (string) $product->get_meta( '_tsk_demanda' ) ) ),
		'cta'         => __( 'Ver protocolo', '2skin' ),
	);
}

/**
 * Campo de la ficha del tratamiento ya interpretado (texto, lista, filas o grupos). Vacío sin el plugin.
 *
 * @param WC_Product $product Producto.
 * @param string     $key     Campo sin prefijo (ver TwoSkin\Core\Treatment_Fields::fields()).
 * @return string|array
 */
function tsk_ficha( WC_Product $product, string $key ): string|array {
	return class_exists( '\TwoSkin\Core\Treatment_Fields' ) ? \TwoSkin\Core\Treatment_Fields::get( $product->get_id(), $key ) : '';
}

/**
 * Variaciones de un tratamiento en orden, con nombre, detalle y precio (0 = en valoración).
 *
 * @param WC_Product $product Producto variable.
 * @return array<int, array{id: int, name: string, slug: string, detail: string, price: float}>
 */
function tsk_product_options( WC_Product $product ): array {
	$options = array();
	foreach ( $product->get_children() as $child_id ) {
		$variation = wc_get_product( $child_id );
		if ( ! $variation || 'publish' !== $variation->get_status() ) {
			continue;
		}
		$values    = array_values( $variation->get_attributes() );
		$options[] = array(
			'id'     => $variation->get_id(),
			'name'   => (string) ( $values[0] ?? $variation->get_name() ),
			'slug'   => sanitize_title( (string) ( $values[0] ?? '' ) ),
			'detail' => $variation->get_description(),
			'price'  => (float) $variation->get_price(),
		);
	}
	return $options;
}

/**
 * ¿Es el producto de valoración?
 *
 * @param WC_Product $product Producto.
 */
function tsk_is_valoracion( WC_Product $product ): bool {
	return class_exists( '\TwoSkin\Core\Catalog' ) && \TwoSkin\Core\Catalog::valoracion_id() === $product->get_id();
}

/**
 * ¿Hay en el carrito alguna variación de este producto?
 *
 * @param WC_Product $product Producto.
 */
function tsk_in_cart( WC_Product $product ): bool {
	if ( ! WC()->cart ) {
		return false;
	}
	foreach ( WC()->cart->get_cart() as $item ) {
		if ( (int) $item['product_id'] === $product->get_id() ) {
			return true;
		}
	}
	return false;
}

/**
 * Indicación de seguimiento vigente de la usuaria actual para un tratamiento (o null).
 *
 * @param WC_Product $product Producto.
 */
function tsk_follow_up( WC_Product $product ): ?array {
	if ( ! is_user_logged_in() || ! class_exists( '\TwoSkin\Core\Patients\Follow_Up' ) ) {
		return null;
	}
	return \TwoSkin\Core\Patients\Follow_Up::indication( get_current_user_id(), $product->get_id() );
}

// El aviso «Añadido a tu reserva» lo pinta la ficha del tratamiento; se omite el mensaje genérico.
add_filter( 'wc_add_to_cart_message_html', '__return_empty_string' );

/**
 * Productos publicados por slug, en el orden pedido. Omite los que no existan.
 *
 * @param string[] $slugs Slugs.
 * @return WC_Product[]
 */
function tsk_products_by_slug( array $slugs ): array {
	$products = array();
	foreach ( $slugs as $slug ) {
		$post = get_page_by_path( $slug, OBJECT, 'product' );
		$prod = $post && 'publish' === $post->post_status ? wc_get_product( $post ) : null;
		if ( $prod ) {
			$products[] = $prod;
		}
	}
	return $products;
}

/**
 * Datos de una línea del carrito o del pedido para Tu reserva y la Reserva.
 *
 * @param int $product_id   Producto.
 * @param int $variation_id Variación (0 si no tiene).
 * @return array{sub: string, name: string, detail: string, image: int, url: string}
 */
function tsk_line_data( int $product_id, int $variation_id = 0 ): array {
	$product   = wc_get_product( $product_id );
	$variation = $variation_id ? wc_get_product( $variation_id ) : null;
	if ( ! $product ) {
		return array( 'sub' => '', 'name' => '', 'detail' => '', 'image' => 0, 'url' => '' );
	}
	if ( class_exists( '\TwoSkin\Core\Gifts\Gifts' ) && \TwoSkin\Core\Gifts\Gifts::product_id() === $product_id ) {
		return array(
			'sub'    => __( 'Bono de regalo', '2skin' ),
			'name'   => $product->get_name(),
			'detail' => __( 'Vigencia de 60 días desde la compra. Recibes el código por correo al confirmarse el pago.', '2skin' ),
			'image'  => (int) $product->get_image_id(),
			'url'    => tsk_page_url( 'bonos' ),
		);
	}
	$terms  = get_the_terms( $product_id, 'product_cat' );
	$option = $variation ? array_values( $variation->get_attributes() )[0] ?? '' : '';
	$detail = $variation ? wp_strip_all_tags( $variation->get_description() ) : '';
	return array(
		'sub'    => tsk_is_valoracion( $product ) ? __( 'Valoración', '2skin' ) : ( $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '' ),
		'name'   => $product->get_name(),
		'detail' => trim( $option . ( $option && $detail ? ' · ' : '' ) . $detail ),
		'image'  => (int) ( $variation && $variation->get_image_id() ? $variation->get_image_id() : $product->get_image_id() ),
		'url'    => $product->is_visible() ? $product->get_permalink() : '',
	);
}

/**
 * Tu reserva y la Reserva usan su propio contenedor (sin el ancho de texto de page.php).
 *
 * @param string[] $templates Plantillas candidatas.
 */
function tsk_wc_page_template( array $templates ): array {
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
		array_unshift( $templates, 'page-compra.php' );
	} elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
		array_unshift( $templates, 'page-cuenta.php' );
	}
	return $templates;
}
add_filter( 'page_template_hierarchy', 'tsk_wc_page_template' );

// La dirección de la sede que muestra el plugin (confirmación, pedido) sale del Personalizador.
add_filter( 'tsk_direccion_sede', static fn(): string => tsk_contact( 'direccion' ) );

/**
 * Campos de WooCommerce con los átomos del design system (.tsk-field, .tsk-input).
 *
 * @param array $args Argumentos del campo.
 */
function tsk_form_field_args( array $args ): array {
	$args['class']       = array_merge( (array) $args['class'], array( 'tsk-field' ) );
	$args['label_class'] = array_merge( (array) $args['label_class'], array( 'tsk-field__label' ) );
	if ( in_array( $args['type'], array( 'text', 'email', 'tel', 'password', 'number', 'date', 'textarea', 'select', 'country', 'state' ), true ) ) {
		$input = 'select' === $args['type'] || 'country' === $args['type'] || 'state' === $args['type'] ? 'tsk-select' : ( 'textarea' === $args['type'] ? 'tsk-textarea' : 'tsk-input' );
		$args['input_class'] = array_merge( (array) $args['input_class'], array( $input ) );
	}
	return $args;
}
add_filter( 'woocommerce_form_field_args', 'tsk_form_field_args' );

// La autorización de datos (Ley 1581) va en los consentimientos de la Reserva; se omite el texto genérico.
remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20 );

/**
 * Datos estructurados de Rank Math: el producto se llama como el tratamiento, sin « – 2Skin».
 *
 * @param array $data Grafo JSON-LD.
 */
function tsk_rank_math_product_name( array $data ): array {
	if ( ! is_product() ) {
		return $data;
	}
	foreach ( $data as $key => $entity ) {
		if ( is_array( $entity ) && 'Product' === ( $entity['@type'] ?? '' ) ) {
			$data[ $key ]['name'] = wp_strip_all_tags( get_the_title( get_queried_object_id() ) );
		}
	}
	return $data;
}
add_filter( 'rank_math/json_ld', 'tsk_rank_math_product_name', 99 );
