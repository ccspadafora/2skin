# Proyecto: 2Skin. E-commerce de reservas de diagnóstico (medicina estética)

## Contexto
Sitio WordPress con WooCommerce y Amelia. El cliente reserva y paga un diagnóstico. Entorno local: LocalWP (2skin.local). Se desarrolla un tema propio y un plugin propio.

## Estructura
- Tema: themes/2skin (solo presentación y plantillas).
- Plugin: plugins/2skin-core (toda la lógica de negocio), namespace TwoSkin\Core, clases en src/ por módulo (Booking, Gifts, Patients, Payments, Clinical, Admin).
- Nunca editar el core de WordPress ni plugins de terceros. Ajustarlos solo con hooks y filtros desde el plugin propio.
- Leer docs/ antes de tocar plantillas de WooCommerce, Amelia o la pasarela.

## Tema
- Híbrido: theme.json para tokens de diseño y plantillas PHP para las páginas.
- Los tokens (colores, tipografías, espaciados) salen de docs/diseno/. No inventar valores.
- Sobrescribir plantillas de WooCommerce solo en themes/2skin/woocommerce/, copiando el original con la misma estructura y su @version. No crear woocommerce.php.
- Declarar soporte con add_theme_support('woocommerce').
- Prefijos (el slug 2skin no es un identificador PHP válido): funciones tsk_, clases CSS .tsk-, metadatos _tsk_, opciones tsk_, endpoints REST tsk/v1.
- Checkout: clásico con shortcode y plantillas propias (page-compra.php, woocommerce/checkout/). Mi cuenta se llama «Mi piel» (/mi-piel/, page-cuenta.php).

## Amelia
- La paciente no usa el formulario de Amelia: elige día, hora y médica en la reserva (Confirmar y agendar) y el plugin crea la cita en Amelia al pagar (Booking\Amelia, Booking\Checkout). La disponibilidad sale de /wp-json/tsk/v1/disponibilidad.
- Los pagos pasan por WooCommerce: cada reserva genera un pedido y las reglas de estado de Amelia (pendiente, procesando, fallido, cancelado) sincronizan la cita.
- Amelia identifica a la clienta por correo o teléfono: no enviarle un teléfono que ya pertenece a otra clienta.
- El hook beforeBooking de Amelia no está disponible con pagos de WooCommerce. Revisar docs/amelia/ antes de usar hooks.

## Pasarela y SEO
- Bold con su plugin oficial «Bold pagos en linea». Las llaves van en wp-config.php como TSK_BOLD_TEST_API_KEY, TSK_BOLD_TEST_SECRET_KEY, TSK_BOLD_PROD_API_KEY y TSK_BOLD_PROD_SECRET_KEY (Payments\Bold las entrega al plugin sin guardarlas en la base de datos).
- SEO con Rank Math: títulos, descripciones por página, sitemap y noindex de carrito, reserva y Mi piel.

## Seguridad (obligatorio en todo código)
- Escapar toda salida: esc_html, esc_attr, esc_url, wp_kses_post.
- Sanitizar toda entrada. Usar nonces y current_user_can en formularios y AJAX.
- Usar $wpdb->prepare en cualquier consulta SQL.
- Ningún secreto en el código. Van en wp-config.php o variables de entorno.
- No programar lógica de cobro. El cobro lo maneja siempre la pasarela con su plugin oficial.
- Datos de salud: pedir solo lo mínimo en la reserva, con autorización de tratamiento de datos (Ley 1581) y sin guardar historia clínica en WordPress. Mi piel no muestra datos clínicos hasta conectar la plataforma clínica externa.

## Forma de trabajar
- Leer los archivos relevantes antes de cambiarlos.
- Construir por páginas: layout global, catálogo, ficha del servicio, reserva, carrito, checkout, mi cuenta.
- Tras cada cambio: comprobar sintaxis PHP y revisar wp-content/debug.log.
- Al terminar cada página, hacer una revisión de seguridad del código nuevo.
- Probar los flujos de pago con datos de prueba y borrarlos al terminar (pedidos, citas y clientas de Amelia, usuarios, bonos).
- Commits pequeños y descriptivos, en español. El repositorio solo incluye el tema, el plugin, docs/ y este archivo (ver .gitignore).
- Si algo no está en docs/ o es ambiguo, preguntar antes de asumir.
