# Proyecto: 2Skin. E-commerce de reservas de diagnóstico (medicina estética)

## Contexto
Sitio WordPress con WooCommerce y Amelia. El cliente reserva y paga un diagnóstico. Entorno local: LocalWP (2skin.local). Se desarrolla un tema propio y un plugin propio.

## Estructura
- Tema: themes/2skin (solo presentación y plantillas).
- Plugin: plugins/2skin-core (toda la lógica de negocio).
- Nunca editar el core de WordPress ni plugins de terceros.
- Leer docs/ antes de tocar plantillas de WooCommerce, Amelia o la pasarela.

## Tema
- Híbrido: theme.json para tokens de diseño y plantillas PHP para las páginas.
- Los tokens (colores, tipografías, espaciados) salen de docs/diseno/. No inventar valores.
- Sobrescribir plantillas de WooCommerce solo en themes/2skin/woocommerce/, copiando el original con la misma estructura. No crear woocommerce.php.
- Declarar soporte con add_theme_support('woocommerce').
- Prefijo skin2_ en funciones, hooks, handles y clases (el slug 2skin no es un identificador PHP válido).
- Checkout: PENDIENTE de definir (clásico con shortcode o bloques). Confirmar con la pasarela.

## Amelia
- Se integra con shortcodes y se estiliza con CSS. No reescribir su flujo.
- Los pagos pasan por WooCommerce: cada reserva genera un pedido y los estados se sincronizan.
- El hook beforeBooking de Amelia no está disponible con pagos de WooCommerce. Revisar docs/amelia/ antes de usar hooks.

## Seguridad (obligatorio en todo código)
- Escapar toda salida: esc_html, esc_attr, esc_url, wp_kses_post.
- Sanitizar toda entrada. Usar nonces y current_user_can en formularios y AJAX.
- Usar $wpdb->prepare en cualquier consulta SQL.
- Ningún secreto en el código. Van en wp-config.php o variables de entorno.
- No programar lógica de cobro. El cobro lo maneja siempre la pasarela con su plugin oficial.
- Datos de salud: pedir solo lo mínimo en la reserva, con autorización de tratamiento de datos (Ley 1581) y sin guardar historia clínica en WordPress.

## Forma de trabajar
- Leer los archivos relevantes antes de cambiarlos.
- Construir por páginas: layout global, catálogo, ficha del servicio, reserva, carrito, checkout, mi cuenta.
- Tras cada cambio: comprobar sintaxis PHP y revisar wp-content/debug.log.
- Al terminar cada página, hacer una revisión de seguridad del código nuevo.
- Commits pequeños y descriptivos, en español.
- Si algo no está en docs/ o es ambiguo, preguntar antes de asumir.
