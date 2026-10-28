/* 2skin — datos compartidos del sitio. Script clásico: expone window.SITE. */
(function () {
  var COP = function (n) { return "$" + Math.round(n).toLocaleString("es-CO").replace(/,/g, "."); };

  var SEDE = "Medicenter · Calle 120a #7-62, Bogotá";
  var VALORACION = 90000, VIRTUAL = 45000;
  var POST_COMUN = ["Sin ejercicio 24 horas.", "Sin alcohol 48 horas.", "Sin piscina, sauna ni jacuzzi durante 3 días.", "Sin sol directo; usa protector solar."];

  var NEEDS = [
    { id: "lineas", name: "Líneas de expresión", note: "Entrecejo, frente y patas de gallo.", icon: "wave" },
    { id: "firmeza", name: "Firmeza y colágeno", note: "Pérdida de soporte, flacidez leve, piel cansada.", icon: "mesh" },
    { id: "ojeras", name: "Ojeras y mirada cansada", note: "Surco lagrimal y hundimiento periocular.", icon: "eye" },
    { id: "manchas", name: "Textura, cicatrices y estrías", note: "Cicatrices de acné, estrías, poro y líneas finas.", icon: "sun" },
    { id: "hidratacion", name: "Calidad de piel e hidratación", note: "Piel opaca, deshidratada, sin luz.", icon: "droplet" },
    { id: "contorno", name: "Contorno y volumen", note: "Pómulos, labios, mentón y mandíbula.", icon: "face" },
    { id: "funcional", name: "Bruxismo y sudoración", note: "Bruxismo, hiperhidrosis y sonrisa gingival.", icon: "spark" }
  ];

  var P = [
    {
      slug: "valoracion", name: "Valoración médica integral", cat: "Primera vez", catKey: "valoracion", icon: "doc",
      kind: "servicio", demand: "Punto de partida", img: "img/valoracion.jpg",
      short: "30 minutos con una de nuestras médicas para llegar a tu diagnóstico y definir tu protocolo.",
      lead: "Toda primera vez empieza aquí. Hacemos tu historia clínica completa, examinamos tu piel y construimos un protocolo individualizado: tratamientos, rutina en casa, suplementación y hábitos.",
      from: VALORACION, needs: ["lineas", "firmeza", "ojeras", "manchas", "hidratacion", "contorno", "funcional"],
      zonas: ["Rostro completo"], marcas: [],
      variants: [
        { id: "val-pres", name: "Valoración presencial", detail: "30 min · Medicenter, Calle 120a #7-62 · abonable al tratamiento", price: VALORACION },
        { id: "val-virt", name: "Valoración virtual", detail: "30 min · Google Meet · requiere formulario, fotos y productos de tu rutina antes de la cita", price: VIRTUAL }
      ],
      facts: [["Duración", "30 minutos"], ["Realiza", "Médica con máster en medicina estética"], ["Abono", "100% al tratamiento si lo inicias dentro del primer mes"], ["Modalidad", "Presencial o virtual"], ["Sede", SEDE]],
      evalua: ["Antecedentes de piel y procedimientos previos.", "Enfermedades crónicas, medicación y alergias.", "Hábitos de vida: sueño, alimentación, sol, rutina en casa.", "Paraclínicos, cuando tu caso los necesita.", "Examen físico completo de la piel y tus expectativas."],
      protocolo: [
        { when: "Paso 1", title: "Agendas y pagas", body: "Eliges presencial o virtual y confirmas tu cita en línea." },
        { when: "Paso 2", title: "Consulta integral", body: "Historia clínica completa y examen físico de la piel." },
        { when: "Paso 3", title: "Diagnóstico", body: "Pedimos los paraclínicos necesarios y analizamos todo." },
        { when: "Paso 4", title: "Tu protocolo", body: "Creamos el mejor plan para ti e iniciamos los tratamientos indicados." }
      ],
      incluye: ["Historia clínica completa", "Examen físico de la piel", "Solicitud de paraclínicos cuando aplica", "Protocolo individualizado: tratamientos, rutina, suplementación y hábitos", "Abono del 100% al tratamiento si lo inicias dentro del primer mes"],
      avisos: ["Si estás en embarazo o lactancia, indícalo al agendar.", "Valoración virtual: antes de la cita debes enviar el formulario de historia clínica con fotos, videos y los productos de tu rutina.", "Tolerancia de 15 minutos: si llegas tarde, la valoración se hace con menos tiempo y el procedimiento puede no realizarse ese día.", "No diagnosticamos ni prescribimos por mensajería."],
      caseTitle: "De “quiero botox” a un plan de calidad de piel.", caseNote: "La paciente llegó pidiendo un procedimiento concreto. La evaluación mostró que su prioridad era hidratación y calidad de piel.",
      caseContext: "Mujer, 37 años", caseProtocol: "Hidratación con ácido hialurónico · 3 sesiones", caseElapsed: "4 meses",
      caseBefore: "img/antes-3.jpg", caseAfter: "img/despues-3.jpg",
      faq: [
        { question: "¿Cuánto vale la consulta?", answer: "La valoración presencial cuesta $90.000 y dura unos 30 minutos. La virtual cuesta $45.000." },
        { question: "¿La consulta queda como saldo a favor?", answer: "Sí. Se abona completa al procedimiento si lo inicias dentro del primer mes." },
        { question: "¿Me lo puedo hacer el mismo día de la consulta?", answer: "Sí, si la médica lo indica. Algunos procedimientos requieren preparación previa; si no la tienes, empezamos por ahí." }
      ]
    },
    {
      slug: "toxina-botulinica", name: "Toxina botulínica", cat: "Toxina", catKey: "toxina", icon: "wave",
      kind: "tratamiento", demand: "Lo más consultado", img: "img/botox.jpg",
      short: "Neuromodulador que relaja el músculo que marca la línea. También trata bruxismo, sudoración y sonrisa gingival.",
      lead: "No es un relleno: detiene el movimiento muscular por un tiempo para suavizar las líneas de expresión móviles. Genera cambios sutiles y, cuando el efecto termina, solo reaparecen las líneas que ya tenías.",
      from: 350000, fromText: "Precio según zona", needs: ["lineas", "hidratacion", "funcional"], zonas: ["Tercio superior", "Mentón y mandíbula", "Cuello y escote", "Axilas", "Manos"], marcas: ["Botox", "Xeomin"],
      variants: [
        { id: "tox-tercio", name: "Toxina botulínica · tercio superior", detail: "Frente, entrecejo y patas de gallo", price: 1100000 },
        { id: "tox-bruxismo", name: "Bruxismo", detail: "Maseteros · función y contorno mandibular", price: 1100000 },
        { id: "tox-nefertiti", name: "Nefertiti", detail: "Mentón, DAO y platisma", price: 1300000 },
        { id: "tox-gingival", name: "Sonrisa gingival", detail: "Labio superior", price: 350000 },
        { id: "tox-skin", name: "Skin quality · solo toxina", detail: "Microdosis para calidad de piel", price: 600000 },
        { id: "tox-skin-nctf", name: "Skin quality · toxina + NCTF", detail: "Microdosis + hidratación", price: 900000 },
        { id: "tox-axilas", name: "Hiperhidrosis axilas", detail: "Control de sudoración", price: 1200000 },
        { id: "tox-manos", name: "Hiperhidrosis manos", detail: "Control de sudoración", price: 1400000 }
      ],
      facts: [["Duración de la sesión", "25 minutos"], ["Dolor", "5/10 · anestesia tópica"], ["Resultado", "Cambios a los 6–8 días; final a los 12–15"], ["Duración del efecto", "3 a 4 meses; repetición cada 4 a 6"], ["Control", "Sin costo, a los 12–15 días"], ["Marcas", "Botox (Allergan–AbbVie) · Xeomin (Merz)"]],
      evalua: ["Que tengas líneas de expresión móviles o la condición específica a tratar (bruxismo, sudoración, bandas del cuello, sonrisa gingival).", "Antecedentes que contraindiquen su uso.", "Si hay demasiadas líneas en reposo o párpado muy caído: en esos casos no es la mejor opción.", "Tus expectativas: duración y cambios que la toxina sí puede lograr.", "La marca adecuada según tu fuerza muscular, si es tu primera aplicación y tu uso previo."],
      protocolo: [
        { when: "Día 0", title: "Aplicación", body: "Sesión de 25 minutos con anestesia tópica. Puede haber marcas durante unas 12 horas." },
        { when: "Días 6 a 8", title: "Primeros cambios", body: "El efecto aparece de forma progresiva." },
        { when: "Días 12 a 15", title: "Control sin costo", body: "Resultado final y revisión con tu médica." },
        { when: "Meses 4 a 6", title: "Repetición", body: "El efecto dura 3 a 4 meses; puede durar menos en fumadores o deportistas de alto rendimiento." }
      ],
      incluye: ["Aplicación por tu médica tratante", "Control sin costo a los 12–15 días", "Producto, lote, registro INVIMA y fecha de vencimiento mostrados antes de aplicar", "Indicaciones post-procedimiento por escrito"],
      avisos: ["Contraindicada en embarazo, alergia a la toxina, a la albúmina o a la proteína de leche de vaca, miastenia gravis, ELA e infecciones activas en la zona.", "Alta posibilidad de morados que pueden durar 1 a 2 semanas.", "Primeras 3 horas sin acostarte. No masajees los puntos ni uses gorra o gafas.", "Sin ejercicio 24 horas, sin alcohol 48 horas y sin piscina, sauna ni jacuzzi durante 3 días. Sin sol directo; usa protector solar."],
      caseTitle: "Movimiento preservado, línea suavizada.", caseNote: "Aplicación en tercio superior. El objetivo no era eliminar la expresión sino reducir la marca en reposo.",
      caseContext: "Mujer, 34 años", caseProtocol: "Toxina botulínica · tercio superior", caseElapsed: "15 días",
      caseBefore: "img/antes-1.jpg", caseAfter: "img/despues-1.jpg",
      faq: [
        { question: "¿Es un relleno?", answer: "No. Es un neuromodulador: detiene el movimiento muscular por un tiempo para evitar las líneas de expresión móviles." },
        { question: "¿Empeora cuando se acaba el efecto?", answer: "No. Cuando se acaba el efecto solo reaparecen las líneas que tenías antes y sigue tu proceso de envejecimiento normal." },
        { question: "¿Te cambia la cara?", answer: "No. Genera cambios sutiles en el tercio superior del rostro al relajar los músculos que generan las líneas de expresión." },
        { question: "¿Se me cae la cara o aparecen más líneas si lo dejo?", answer: "No. Tu rostro vuelve a tener las mismas líneas que antes de la aplicación; no hay estudios que muestren daño en la piel o el músculo." },
        { question: "¿Botox o Xeomin?", answer: "Botox contiene complejos proteicos; Xeomin no. La elección depende de la fuerza de tus músculos, si es tu primera aplicación y tu uso previo. La define tu médica." }
      ]
    },
    {
      slug: "bioestimuladores", name: "Bioestimuladores de colágeno", cat: "Bioestimulación", catKey: "bio", icon: "mesh",
      kind: "tratamiento", demand: "Alto valor", img: "img/bioestimuladores.jpg",
      short: "Estimulan tu propio colágeno para mejorar flacidez, grosor y calidad de piel.",
      lead: "No son un relleno: estimulan las células productoras de colágeno para mejorar la calidad y el grosor de la piel y la flacidez. El producto se elige según tu tipo de rostro y tus necesidades.",
      from: 1700000, needs: ["firmeza", "contorno"], zonas: ["Rostro completo", "Cuello y escote", "Manos", "Cuerpo"], marcas: ["Radiesse", "Sculptra", "Profhilo", "Harmonyca"],
      variants: [
        { id: "bio-profhilo", name: "Profhilo · 1 sesión", detail: "Hidratación y firmeza · resultado en 1–2 meses, dura ~12", price: 1550000 },
        { id: "bio-radiesse", name: "Radiesse", detail: "Hidroxiapatita de calcio · resultado en 3–4 meses, dura ~18", price: 1700000 },
        { id: "bio-radiesse-plus", name: "Radiesse Plus", detail: "Con lidocaína · resultado en 3–4 meses, dura ~18", price: 1800000 },
        { id: "bio-sculptra", name: "Sculptra", detail: "Ácido poliláctico · resultado en 3–4 meses, dura ~24", price: 2200000 },
        { id: "bio-harmonyca", name: "Harmonyca", detail: "Híbrido: efecto inmediato + colágeno · dura ~18 meses", price: null },
        { id: "bio-profhilo-est", name: "Profhilo estructura · 1 sesión", detail: "Precio definido en valoración", price: null }
      ],
      facts: [["Duración de la sesión", "30 minutos"], ["Dolor", "4/10 · anestesia tópica"], ["Sesiones", "En promedio 2 a 3 al año"], ["Resultado", "Según producto: inmediato a 4 meses"], ["Duración", "12 a 24 meses según producto"], ["Control", "Sin costo, a los 4 meses"]],
      evalua: ["Flacidez de la piel y pérdida de volumen.", "Número de sesiones que necesitas: depende de cada paciente.", "Hábitos de vida y edad: en general mayores de 25 años con flacidez.", "Si tu rostro es muy pesado o buscas un resultado de tipo quirúrgico: ahí no es la opción.", "Tu compromiso con más sesiones o un manejo integral si hace falta."],
      protocolo: [
        { when: "Día 0", title: "Aplicación", body: "Sesión de 30 minutos con anestesia tópica, seguida de la pauta de masaje indicada." },
        { when: "Semanas 1 a 2", title: "Recuperación", body: "Puede haber edema leve y morados durante unas 2 semanas." },
        { when: "Meses 1 a 4", title: "Colágeno nuevo", body: "Profhilo en 1–2 meses; Radiesse y Sculptra en 3–4. Harmonyca tiene además efecto inmediato." },
        { when: "Mes 4", title: "Control sin costo", body: "Evaluamos resultado y definimos la siguiente sesión." }
      ],
      incluye: ["1 sesión con el producto indicado", "Control sin costo a los 4 meses", "Producto, lote, registro INVIMA y fecha de vencimiento mostrados antes de aplicar", "Pauta de masaje en casa"],
      avisos: ["Contraindicado en embarazo, lactancia, infección o inflamación activa en la zona, queloides y presencia de biopolímeros.", "Puede haber morados durante unas 2 semanas y edema leve.", "Masajes con el tiempo y la frecuencia indicados en tu caso.", "Sin ejercicio 24 horas, sin alcohol 48 horas y sin piscina, sauna ni jacuzzi durante 3 días. Sin sol directo; usa protector solar."],
      caseTitle: "Firmeza recuperada en seis meses.", caseNote: "Dos sesiones de bioestimulador. El cambio se lee en soporte y textura, no en volumen añadido.",
      caseContext: "Mujer, 41 años", caseProtocol: "Bioestimulador de colágeno · 2 sesiones", caseElapsed: "6 meses",
      caseBefore: "img/antes-2.jpg", caseAfter: "img/despues-2.jpg",
      faq: [
        { question: "¿Es un relleno?", answer: "No. Estimula las células productoras de colágeno para mejorar la calidad y el grosor de la piel y la flacidez, sin aportar volumen." },
        { question: "¿Me va a dar volumen?", answer: "Depende del producto: normalmente se gana un poco de volumen con Radiesse y Harmonyca; con los demás no. La elección depende de tu tipo de rostro y tus necesidades." },
        { question: "¿Cuándo empiezo a ver los cambios?", answer: "A los 3–4 meses según tus hábitos y el bioestimulador; de 1 a 2 meses con Profhilo, y con efecto inmediato en Harmonyca." }
      ]
    },
    {
      slug: "acido-hialuronico", name: "Ácido hialurónico de volumen", cat: "Volumen", catKey: "ah", icon: "lips",
      kind: "tratamiento", demand: "Armonización natural", img: "img/acido-hialuronico.jpg",
      short: "Ácido hialurónico reticulado para reponer o dar volumen. Reversible.",
      lead: "Repone volumen perdido o lo aporta donde lo buscas: pómulos, ojeras, surco, labios, mentón y mandíbula. Se cobra por jeringa y, con la cantidad adecuada y sin excesos, los cambios pueden ser muy naturales.",
      from: 1000000, fromText: "Precio según procedimiento", needs: ["contorno", "ojeras"], zonas: ["Pómulos", "Ojos", "Labios", "Mentón y mandíbula"], marcas: ["Belotero", "Restylane"],
      variants: [
        { id: "ah-ojeras", name: "Ojeras", detail: "1 jeringa", price: 1000000 },
        { id: "ah-labios", name: "Labios", detail: "1 jeringa", price: 1100000 },
        { id: "ah-menton", name: "Mentón", detail: "2 jeringas", price: 1700000 },
        { id: "ah-soporte", name: "Puntos de soporte", detail: "2 jeringas", price: 1800000 },
        { id: "ah-mandibula", name: "Contorno mandibular", detail: "2 jeringas", price: 1800000 },
        { id: "ah-masc", name: "Masculinización", detail: "6 jeringas", price: 5000000 },
        { id: "ah-hialuronidasa", name: "Hialuronidasa", detail: "Retiro de ácido hialurónico previo", price: 500000 }
      ],
      facts: [["Duración de la sesión", "30 minutos por procedimiento"], ["Dolor", "Labios 8/10; otras zonas ~3/10"], ["Resultado", "Inmediato"], ["Duración", "8 a 24 meses según el producto"], ["Reversible", "Sí, con hialuronidasa"], ["Control", "Incluido, sin costo, a los 12 días"]],
      evalua: ["Tu necesidad real: pérdida de volumen o deseo de ganarlo en una zona.", "Si tienes una aplicación previa aún vigente: en ese caso hacemos control y no damos más volumen.", "Si el envejecimiento es marcado y lo indicado es estructura con bioestimulador.", "Ojeras por hundimiento marcado o labios con muy poco volumen: puede requerir otro abordaje.", "Que el cambio sea coherente con tu estructura facial."],
      protocolo: [
        { when: "Día 0", title: "Aplicación", body: "Anestesia tópica y aplicación por jeringa. El resultado es inmediato." },
        { when: "Días 3 a 8", title: "Inflamación", body: "Esperable. Sensibilidad al tacto alrededor de 1 semana." },
        { when: "Semanas 1 a 2", title: "Morados", body: "Pueden aparecer y durar 1 a 2 semanas." },
        { when: "Día 12", title: "Control sin costo", body: "Con la inflamación resuelta, revisamos el resultado." }
      ],
      incluye: ["El servicio integral con el resultado acordado (1 o más jeringas según el procedimiento)", "Anestesia tópica", "Control sin costo a los 12 días", "Producto, lote, registro INVIMA y fecha de vencimiento mostrados antes de aplicar"],
      avisos: ["No se aplica en embarazo, lactancia ni infección activa.", "La complicación más grave es la obstrucción vascular; una médica inyectora capacitada está preparada para detectarla a tiempo y resolverla.", "Sin piscina, sauna ni jacuzzi durante 3 días. Sin sol directo ni alcohol. Sin ejercicio 24 horas. Protector solar.", "No hacemos rinomodelación: si buscas un cambio estructural de la nariz, la mejor alternativa es la cirugía."],
      caseTitle: "Proporción, no volumen.", caseNote: "Una jeringa bien distribuida en labios. El objetivo era contorno e hidratación, no aumento.",
      caseContext: "Mujer, 29 años", caseProtocol: "Ácido hialurónico · labios · 1 jeringa", caseElapsed: "12 días",
      caseBefore: "img/antes-3.jpg", caseAfter: "img/despues-3.jpg",
      faq: [
        { question: "¿Se me va a deformar la cara?", answer: "No. Con la cantidad adecuada y sin excesos, los cambios pueden ser muy naturales." },
        { question: "¿Dura para siempre?", answer: "No. Cada ácido hialurónico tiene su duración, pero no se absorbe al 100%. Para removerlo por completo se aplica el antídoto." },
        { question: "¿Se puede quitar si no me gusta?", answer: "Sí, con hialuronidasa se retira por completo." },
        { question: "¿Por qué se cobra por jeringa?", answer: "Porque en una sesión se pueden aplicar varias. Algunos pacientes necesitan una; otros, más." }
      ]
    },
    {
      slug: "hidratacion-nctf", name: "Ácido hialurónico de hidratación", cat: "Calidad de piel", catKey: "hidratacion", icon: "droplet",
      kind: "tratamiento", demand: "Calidad de piel", img: "img/hidratacion.jpg",
      short: "Skinboosters para deshidratación, poro, textura y luminosidad. No da volumen.",
      lead: "No es un relleno: es un hidratante intradérmico. Mejora deshidratación, poros, textura, luminosidad, líneas muy finas y, en general, la calidad de tu piel.",
      from: 550000, needs: ["hidratacion", "ojeras", "manchas"], zonas: ["Rostro completo", "Ojos", "Cuello y escote", "Manos"], marcas: ["NCTF", "Skinvive", "Belotero Revive", "Restylane Vital", "Sunekos"],
      variants: [
        { id: "hid-nctf-1", name: "NCTF · 1 sesión", detail: "Solo junto con otro procedimiento", price: 500000 },
        { id: "hid-revive", name: "Skinbooster Belotero Revive", detail: "1 sesión", price: 550000 },
        { id: "hid-nctf-3", name: "NCTF · 3 sesiones", detail: "Protocolo · control entre sesiones", price: 1400000 },
        { id: "hid-nctf-laser", name: "NCTF + láser", detail: "Combinado", price: 1600000 }
      ],
      facts: [["Duración de la sesión", "25 a 30 minutos"], ["Dolor", "5/10 · anestesia tópica"], ["Sesiones", "Entre 3 y 6, según el producto"], ["Recuperación", "2 a 5 días"], ["Resultado", "Mejor entre los 30 y 45 días"], ["Control", "A los 15–30 días, con la siguiente aplicación"]],
      evalua: ["Grado de deshidratación, poro, textura y luminosidad.", "Qué producto conviene a tu piel y a la zona.", "Tu rutina en casa: sostiene el resultado.", "Si hay inflamación o lesiones activas en la zona."],
      protocolo: [
        { when: "Sesión 1", title: "Aplicación", body: "25 a 30 minutos con anestesia tópica. Algunos productos contienen lidocaína." },
        { when: "Días 2 a 5", title: "Recuperación", body: "Inflamación según la zona; mayor en ojeras. Alta posibilidad de morados." },
        { when: "Días 15 a 30", title: "Control", body: "Siguiente aplicación del protocolo según el producto." },
        { when: "Días 30 a 45", title: "Resultado", body: "Usualmente tras 3 sesiones aparecen los mejores resultados." }
      ],
      incluye: ["Aplicación del producto", "Anestesia tópica", "Producto, lote, registro INVIMA y fecha de vencimiento mostrados antes de aplicar"],
      avisos: ["No se aplica sobre infección o lesión activa.", "En ojeras o zona periocular la inflamación es mayor y el edema dura más.", "Sin maquillaje ni tocar el rostro repetidamente por 12–24 horas. Sin ejercicio intenso por 24–48 horas.", "Sin sauna, piscina ni fuentes de calor intenso durante 48–72 horas."],
      caseTitle: "Luz recuperada en tres sesiones.", caseNote: "Protocolo de NCTF con rutina en casa. Sin cambio de volumen.",
      caseContext: "Mujer, 37 años", caseProtocol: "NCTF · 3 sesiones", caseElapsed: "45 días",
      caseBefore: "img/antes-1.jpg", caseAfter: "img/despues-1.jpg",
      faq: [
        { question: "¿Es un relleno?", answer: "No, es un hidratante facial." },
        { question: "¿Me dará volumen facial?", answer: "No, solo hidratación y mejor calidad de piel." },
        { question: "¿Después de cuántas sesiones veo resultados?", answer: "Depende del producto; usualmente después de 3 sesiones, y los mejores resultados aparecen entre los primeros 30 y 45 días." }
      ]
    },
    {
      slug: "laser-co2", name: "Láser CO2", cat: "Láser", catKey: "laser", icon: "sun",
      kind: "tratamiento", demand: "Textura y cicatrices", img: "img/textura-2.jpg",
      short: "Láser iBlaze para cicatrices de acné, estrías, líneas finas y flacidez moderada.",
      lead: "Renovación profunda de la piel para líneas finas, flacidez moderada, cicatrices de acné, cicatrices quirúrgicas o traumáticas y estrías. Requiere preparación previa y cuidado estricto en casa.",
      from: 1200000, needs: ["manchas", "firmeza"], zonas: ["Rostro completo", "Cuello y escote", "Manos", "Cuerpo"], marcas: ["iBlaze"],
      variants: [
        { id: "co2-1", name: "Láser CO2 · 1 sesión", detail: "Rejuvenecimiento", price: 1200000 },
        { id: "co2-2", name: "Láser CO2 · 2 sesiones", detail: "Rejuvenecimiento", price: 2200000 },
        { id: "co2-3", name: "Láser CO2 · 3 sesiones", detail: "Cicatrices de acné", price: 3300000 },
        { id: "co2-nctf", name: "Láser CO2 + NCTF", detail: "1 sesión combinada", price: 1600000 },
        { id: "co2-exo", name: "Láser CO2 + exosomas", detail: "1 sesión combinada", price: 1700000 }
      ],
      facts: [["Duración de la sesión", "60 minutos con anestesia y recuperación"], ["Dolor", "8/10 · anestesia tópica y frío"], ["Sesiones", "1 a 2 rejuvenecimiento; 3 a 5 cicatrices o estrías"], ["Preparación", "2 semanas de retinol previo"], ["Resultado", "Piel más lisa a los 15 días; cambios notorios en 1–3 meses"], ["Control", "Al mes"]],
      evalua: ["Tus expectativas y el estado de tu piel.", "Tu fototipo: no está indicado en todos.", "Antecedentes de melasma, rosácea o acné.", "Mala cicatrización (queloides o hipertróficas).", "Si puedes cumplir una recuperación sin sol."],
      protocolo: [
        { when: "Semanas −2 a 0", title: "Preparación", body: "Retinol previo durante 2 semanas, indicado en consulta." },
        { when: "Día 0", title: "Sesión", body: "Anestesia tópica, láser y 10 minutos de recuperación con frío localizado y mascarilla." },
        { when: "Días 0 a 15", title: "Cuidado por etapas", body: "Sin sol ni luces blancas; crema reparadora; protector solar desde el día 5." },
        { when: "Mes 1", title: "Control", body: "Si necesitas otra sesión, a los 2 meses de la primera." }
      ],
      incluye: ["Anestesia previa", "1 sesión", "Cuidados post aplicación: frío, crema reparadora y mascarilla hidratante para casa", "Control al mes"],
      avisos: ["No indicado con melasma, fototipos altos, mala cicatrización o si prefieres una recuperación rápida.", "Primeras 24 horas: sin bloqueador, sin sol ni luces blancas; solo crema reparadora y limpiador suave no espumoso.", "Días 1 a 4: sin sol de ningún tipo, solo barrera física y crema reparadora cada 2 a 4 horas. Días 5 a 15: inicia protector solar.", "Desde el día 15: retoma activos de forma progresiva, sin ácidos fuertes ni retinol hasta que la piel esté sana."],
      caseTitle: "Textura más uniforme, piel propia.", caseNote: "Láser CO2 con preparación previa y cuidado posterior estricto.",
      caseContext: "Mujer, 39 años", caseProtocol: "Láser CO2 · 3 sesiones", caseElapsed: "3 meses",
      caseBefore: "img/antes-2.jpg", caseAfter: "img/despues-2.jpg",
      faq: [
        { question: "¿Duele?", answer: "Sí, pero es tolerable con anestesia tópica: se siente ardor y dolor leve. Al terminar hay sensación de quemadura durante 1 a 3 horas." },
        { question: "¿Requiere incapacidad?", answer: "No, pero sí cuidados en casa: lo mejor es evitar al 100% el sol para no tener complicaciones." },
        { question: "¿Cuándo se ven los resultados?", answer: "A los 15 días la piel luce más luminosa y lisa. Los cambios más notorios en líneas y cicatrices de acné llegan entre 1 y 3 meses." },
        { question: "¿Cuántas sesiones necesito?", answer: "1 a 2 para rejuvenecimiento; 3 a 5 para cicatrices de acné profundas o estrías." },
        { question: "¿Cuánto duran los resultados?", answer: "En estrías y cicatrices de acné son permanentes; en rejuvenecimiento facial se requiere mantenimiento al menos cada año." }
      ]
    }
  ];

  var EXTRA = {
    "toxina-botulinica": {
      que: "Mejora las líneas de expresión del tercio superior del rostro que se generan en movimiento y suaviza las que están estáticas, mediante la relajación del músculo. No aporta volumen y no cambia tus facciones. También trata bruxismo, sonrisa gingival, hiperhidrosis (sudoración excesiva) y calidad de piel (rosácea).",
      zonasTxt: "Tercio superior, sonrisa gingival, bruxismo, cuello, escote, axilas, manos y pies.",
      noIndicado: ["Demasiadas líneas de expresión en reposo.", "Párpado muy caído.", "Expectativas muy altas: duración excesiva o cambios que la toxina botulínica no realiza."],
      contra: [{ t: "Contraindicaciones", items: ["Embarazo.", "Alergia previa a la toxina botulínica.", "Alergia a la albúmina o a proteínas de leche de vaca.", "Miastenia gravis o ELA.", "Infecciones activas en el sitio de aplicación.", "Rellenos permanentes previos."] }],
      marcasInfo: [
        { n: "Botox", lab: "Allergan–AbbVie", invima: "2023MB-014172-R4", uso: "Contiene complejos proteicos." },
        { n: "Xeomin", lab: "Merz Pharmaceuticals", invima: "2021MB-0016054-R1", uso: "Sin complejos proteicos." }
      ],
      eleccion: "La elección depende de la fuerza de tus músculos, si es tu primera aplicación, si eres hombre o mujer, tu uso previo y cómo te fue con la marca, tu edad y si has hecho resistencia.",
      trazabilidad: "Siempre te mostramos el producto, el lote, el registro INVIMA y la fecha de vencimiento.",
      presentacion: "Se vende por sesión.",
      combos: ["Bioestimuladores de colágeno (Radiesse, Sculptra, Profhilo, Harmonyca)", "NCTF"]
    },
    "bioestimuladores": {
      que: "Mejora la calidad de la piel y la flacidez generada por el envejecimiento, estimulando tus células para que produzcan tu propio colágeno. No es un relleno, no cambia tus facciones y se absorbe con el tiempo: desaparece a los 2 años y deja el colágeno producido.",
      zonasTxt: "Rostro (caras laterales), cuello, escote, manos, abdomen y glúteos.",
      noIndicado: ["Rostro muy pesado.", "Expectativas de tipo quirúrgico.", "Pacientes que no se comprometan a hacer más sesiones o un manejo integral si es necesario.", "Malos hábitos de vida: alimentación, sueño, sedentarismo, tabaquismo, obesidad."],
      contra: [
        { t: "Radiesse", items: ["Hipersensibilidad a hidroxiapatita de calcio, carboximetilcelulosa o lidocaína.", "Infección activa o proceso inflamatorio (celulitis, dermatitis activa, herpes).", "Queloides.", "Rellenos permanentes previos.", "Embarazo y lactancia.", "Relativas: cáncer, enfermedades autoinmunes activas y anticoagulantes."] },
        { t: "Sculptra", items: ["Hipersensibilidad al ácido poliláctico o a la carboximetilcelulosa.", "Infección en la zona.", "Queloides.", "Presencia de biopolímeros.", "Embarazo y lactancia.", "Relativas: enfermedades del tejido conectivo o autoinmunes; tratamientos dermatológicos agresivos (láser o peeling)."] },
        { t: "Profhilo", items: ["Alergia al ácido hialurónico.", "Infecciones en la piel.", "Embarazo y lactancia.", "Relativas: autoinmunes en fase activa."] },
        { t: "Harmonyca", items: ["Alergia al ácido hialurónico, a la hidroxiapatita de calcio o a la lidocaína.", "Infección en la zona.", "Biopolímeros.", "Embarazo y lactancia.", "Relativas: enfermedades autoinmunes sistémicas activas."] }
      ],
      marcasInfo: [
        { n: "Radiesse", lab: "Merz", invima: "2019DM-0004463R1", uso: "Pérdida de peso marcada, falta de volumen, calidad de piel. Resultado en 3–4 meses; dura ~18." },
        { n: "Radiesse Plus", lab: "Merz", invima: "2018DM-0017846", uso: "Igual que Radiesse, con lidocaína." },
        { n: "Sculptra", lab: "Galderma", invima: "2017DM-0017014", uso: "Pérdida de peso o cirugía bariátrica, buena estructura facial sin necesidad de volumen; calidad de piel y celulitis en glúteos. Resultado en 3–4 meses; dura ~24." },
        { n: "Profhilo", lab: "Dermavan", invima: "2021DM-0024425", uso: "Calidad de piel, hidratación, zonas de piel delgada (peribucal, periocular). Resultado en 1–2 meses; dura ~12." },
        { n: "Harmonyca", lab: "Allergan–AbbVie", invima: "2022DM-0026210", uso: "Cambios inmediatos, pérdida importante de estructura facial. Inmediato y 3–4 meses; dura ~18." }
      ],
      trazabilidad: "Te mostramos empaque, lote, fecha de vencimiento e INVIMA.",
      presentacion: "Vial sellado para cada paciente.",
      combos: ["Toxina botulínica", "Exosomas o skinbooster", "Puntos de soporte con ácido hialurónico, si necesitas volumen"]
    },
    "hidratacion-nctf": {
      que: "Hidratación profunda que mejora la calidad, elasticidad y luminosidad de la piel de adentro hacia afuera, con microinyecciones en diferentes capas de la piel. No genera volumen facial.",
      zonasTxt: "NCTF: rostro con énfasis periocular y peribucal, cuello, escote, manos, estrías y celulitis. Skinboosters: rostro, mejillas, peribucal, cuello, escote, manos. Sunekos: periocular, ojeras, peribucal (código de barras), frente, mejillas, cuello, escote, manos, estrías y celulitis.",
      noIndicado: ["Alergia al ácido hialurónico.", "Expectativas muy altas o no querer hacer más de una sesión: la mayoría de pacientes requiere hidratación en múltiples sesiones."],
      contra: [{ t: "Contraindicaciones", items: ["Alergia al ácido hialurónico o a la lidocaína.", "Embarazo o lactancia.", "Enfermedades autoinmunes activas al momento de la consulta.", "Infección activa en el sitio de inyección.", "Biopolímeros en la misma zona de aplicación."] }],
      marcasInfo: [
        { n: "NCTF 135 HA", lab: "Laboratoires FILLMED (antes Filorga Medical), Francia", invima: "2019DM-0020612", uso: "Periocular y rostro completo. A los 8 días, luminosidad y efecto glow; poros y tono requieren mínimo 3 sesiones." },
        { n: "Sunekos 200 / 1200", lab: "Professional Dietetics S.p.A., Italia", invima: "2021DM-0023415", uso: "Periocular y rostro completo." },
        { n: "Belotero Revive", lab: "Merz Pharmaceuticals / Anteis S.A.", invima: "2021DM-0023812 / 2023DM-0027150", uso: "Mayor profundidad: malar, peribucal, cuello." },
        { n: "Restylane Skinboosters Vital / Vital Light", lab: "Galderma (Q-Med AB), Suecia", invima: "2016DM-0000082-R1 / 2021DM-0007970-R2", uso: "Mayor profundidad: malar, peribucal, cuello." },
        { n: "Skinvive by Juvéderm", lab: "Allergan Aesthetics, AbbVie", invima: "2019DM-0020118 / 2019DM-0002692-R1", uso: "Mayor profundidad. A los 8 días más luminosidad; a las 2–3 semanas menos líneas sutiles." }
      ],
      trazabilidad: "Te mostramos empaque, lote, fecha de vencimiento e INVIMA.",
      presentacion: "Vial o jeringa sellada para cada paciente, por sesiones. NCTF por 1 sesión solo se vende junto con otro procedimiento.",
      combos: ["Toxina botulínica", "Bioestimuladores de colágeno", "Exosomas"]
    },
    "acido-hialuronico": {
      que: "Recupera el volumen perdido o da volumen a un área específica, guardando las proporciones naturales y tus necesidades. No mejora la calidad de piel: solo mejora la estructura facial.",
      zonasTxt: "Pómulos, malar, ojeras, surco nasogeniano, labios, mentón y mandíbula.",
      noIndicado: ["Una aplicación previa todavía vigente: hacemos control y no damos volumen innecesario.", "Expectativas que no sean reales."],
      contra: [
        { t: "Absolutas", items: ["Alergia conocida al ácido hialurónico o a la lidocaína.", "Infección activa en la zona (herpes, acné severo, heridas abiertas).", "Enfermedades autoinmunes graves activas (lupus, esclerodermia, sarcoidosis).", "Historial de cicatrización con queloides."] },
        { t: "Relativas", items: ["Embarazo y lactancia.", "Anticoagulantes.", "Tratamientos dentales recientes.", "Enfermedades crónicas descontroladas.", "Eventos sociales importantes a corto plazo."] }
      ],
      marcasInfo: [
        { n: "Belotero Balance", lab: "Merz Aesthetics", invima: "2014DM-0012224", uso: "Arrugas, pliegues faciales moderados y realce labial." },
        { n: "Belotero Intense", lab: "Merz Aesthetics", invima: "2014DM-0012224", uso: "Restaurar o aumentar volumen profundo y contornos." },
        { n: "Belotero Volume", lab: "Merz Aesthetics", invima: "2014DM-0012224", uso: "Grandes volúmenes en pómulos y mejillas." },
        { n: "Restylane Classic", lab: "Galderma (Q-Med AB)", invima: "2014DM-0012247", uso: "Arrugas moderadas a severas y pliegues faciales." },
        { n: "Restylane Kysse", lab: "Galderma (Q-Med AB)", invima: "2014DM-0012247", uso: "Labios: volumen flexible, definición del borde e hidratación." },
        { n: "Restylane Lyft", lab: "Galderma (Q-Med AB)", invima: "2014DM-0012247", uso: "Soporte y proyección de pómulos, mentón, mandíbula y dorso de las manos." }
      ],
      trazabilidad: "Te mostramos el producto y su registro, y te entregamos un sticker de identificación.",
      presentacion: "Por procedimiento: labios incluye 1 jeringa; mentón incluye 2. Si necesitas más jeringas te informamos el costo adicional. Mantenimiento usualmente cada 1 a 2 años.",
      combos: ["Puntos de soporte + bioestimulador de colágeno", "Labios + hidratación peribucal (código de barras) o surco nasogeniano", "Labios + skin quality", "Labios + mentón", "Surco nasogeniano + puntos de soporte + bioestimulador"]
    },
    "laser-co2": {
      que: "Protocolo de láser CO2 fraccionado para rejuvenecimiento cutáneo: mejora calidad de piel, cicatrices de acné, luminosidad, poros dilatados, líneas finas y textura. No está enfocado en pigmentaciones tipo melasma; solo un protocolo combinado y especializado puede adaptarlo a ese objetivo.",
      zonasTxt: "Rostro, cuello, escote, manos y cuerpo (estrías o cicatrices). También blefaroplastia, queratosis seborreicas y fibromas blandos.",
      noIndicado: ["Si prefieres una recuperación más rápida.", "Melasma o fototipos altos.", "Expectativas muy altas.", "Mala cicatrización (queloides o hipertróficas).", "Embarazo, lactancia o infecciones activas."],
      contra: [
        { t: "Absolutas", items: ["Isotretinoína reciente.", "Infecciones activas en la zona (celulitis, herpes simple, impétigo).", "Lesiones sospechosas de malignidad.", "Embarazo o lactancia.", "Cicatrización hipertrófica o tendencia a queloides.", "Psoriasis, eccema, dermatitis atópica o rosácea severa activa."] },
        { t: "Relativas", items: ["Bronceado o exposición solar reciente.", "Fototipos altos (requieren preparación con despigmentantes).", "Herpes simple frecuente.", "Anticoagulantes.", "Diabetes tipo 2 o autoinmunes activas.", "Peelings químicos recientes."] }
      ],
      marcasInfo: [{ n: "iBlaze", lab: "TENTREK LASERS", invima: "Distintivo de verificación INVIMA visible en consultorio", uso: "Láser quirúrgico de dióxido de carbono con hoja de vida de equipo biomédico y mantenimiento preventivo periódico." }],
      trazabilidad: "Cada equipo tiene su hoja de vida de equipo biomédico.",
      presentacion: "Por sesiones. El resultado final llega entre 3 y 12 meses desde la primera sesión; siempre se hace más de una (1 a 3 según las lesiones).",
      combos: ["Cicatrices de acné: bioestimuladores + peeling TCA cross + exosomas", "Calidad de piel: bioestimuladores + exosomas o NCTF", "Estrías: exosomas"],
      etapas: [
        { t: "Calidad de piel, cicatrices de acné y textura", items: ["0–12 horas: inflamación importante, enrojecimiento severo, sangrado, calor y dolor.", "24–72 horas: mejora la inflamación; marcas en cuadrícula, pequeñas costras, dolor al gesticular.", "3–8 días: descamación importante; el enrojecimiento mejora.", "8–15 días: descamación casi completa; se recupera la barrera.", "15–30 días: baja el tono rosado; mejoran tono y luminosidad.", "1–3 meses: estímulo de colágeno; mejoran líneas y cicatrices.", "3–6 meses: máxima firmeza y resultado final."] },
        { t: "Cicatrices quirúrgicas o traumáticas", items: ["3–4 semanas: primeros cambios.", "2–3 meses: segunda o tercera sesión según la cicatriz.", "6–9 meses: remodelación completa y resultado final."] },
        { t: "Estrías", items: ["Primeras 2 semanas: inflamación, costra y color rojizo.", "4–6 semanas: disminuye la profundidad.", "3–4 meses: las rosadas pierden color; las blancas mejoran textura.", "6–12 meses: resultado final; no desaparecen al 100%."] },
        { t: "Queloides (siempre con corticoides)", items: ["Primera semana: microcostras, inflamación y rojez.", "2–3 semanas: la cicatriz se ablanda.", "2–4 meses: se reduce volumen y altura.", "6–12 meses: aplanamiento de la lesión."] }
      ]
    }
  };
  P.forEach(function (p) { var x = EXTRA[p.slug]; if (x) for (var k in x) p[k] = x[k]; });

  var TIENDA = [];

  var BONOS = [
    { id: "bo-500", name: "Bono $500.000", price: 500000, detail: "Cubre la valoración ($90.000) y deja $410.000 para el tratamiento que se indique." },
    { id: "bo-1000", name: "Bono $1.000.000", price: 1000000, detail: "Cubre la valoración y buena parte de un tratamiento, por ejemplo labios o hidratación." },
    { id: "bo-libre", name: "Bono de valor libre", price: 500000, detail: "Desde $500.000. El valor se define al comprar." }
  ];

  var CATS = [
    { key: "valoracion", label: "Valoración" }, { key: "toxina", label: "Toxina" },
    { key: "bio", label: "Bioestimulación" }, { key: "ah", label: "Volumen" },
    { key: "hidratacion", label: "Hidratación" }, { key: "laser", label: "Láser" }
  ];
  var ZONAS = ["Tercio superior", "Ojos", "Pómulos", "Labios", "Mentón y mandíbula", "Cuello y escote", "Manos", "Axilas", "Cuerpo", "Rostro completo"];
  var MARCAS = [];

  var QUIZ = [
    { q: "¿Qué te gustaría mejorar primero?", help: "Elige lo que más te pesa hoy. En valoración revisamos el resto.", options: [
      { label: "Las líneas que me marca el gesto", v: "lineas" },
      { label: "La firmeza: siento la piel menos sostenida", v: "firmeza" },
      { label: "Manchas, poro, textura o cicatrices", v: "manchas" },
      { label: "La piel se ve opaca y deshidratada", v: "hidratacion" },
      { label: "El contorno: labios, mentón, mandíbula", v: "contorno" }
    ] },
    { q: "¿Con qué horizonte de tiempo cuentas?", help: "Algunos protocolos muestran cambio en días; otros trabajan durante meses.", options: [
      { label: "Quiero ver algo en dos o tres semanas", v: "corto" },
      { label: "Puedo esperar dos o tres meses", v: "medio" },
      { label: "Me interesa un plan de un año", v: "largo" }
    ] },
    { q: "¿Has hecho antes algún procedimiento?", help: "Los antecedentes cambian la indicación y a veces el orden del plan.", options: [
      { label: "Nunca", v: "nunca" }, { label: "Toxina botulínica", v: "toxina" },
      { label: "Rellenos o ácido hialurónico", v: "relleno" }, { label: "Bioestimuladores o láser", v: "bio" }
    ] },
    { q: "¿Qué tan visible quieres que sea el cambio?", help: "Este es el punto donde más pacientes y médicas se desalinean. Prefiere ser honesto.", options: [
      { label: "Que nadie note que hice algo", v: "sutil" },
      { label: "Que se note que estoy mejor, sin saber por qué", v: "medio" },
      { label: "Un cambio claro pero natural", v: "claro" }
    ] },
    { q: "¿Cómo describirías tu rutina de cuidado actual?", help: "Sin cuidado en casa, buena parte del resultado clínico se pierde.", options: [
      { label: "Uso fotoprotector todos los días", v: "buena" },
      { label: "Tengo rutina, pero irregular", v: "media" },
      { label: "Prácticamente ninguna", v: "ninguna" }
    ] }
  ];

  var TEAM = [
    { name: "Dra. Laura Vacca", fullName: "Laura Daniela Vacca Lugo", role: "Cofundadora · Medicina estética", img: "img/doctora-1.jpg",
      creds: "Médica · Universidad Visión de las Américas · Máster en medicina estética, nutrición y antienvejecimiento (UDIMA/AMIR) · RM 1113793402",
      bio: "Médica especialista en medicina estética, apasionada por mejorar la calidad de piel de cada paciente. Su enfoque combina la ciencia con la cercanía, diseñando protocolos personalizados e integrales que cuidan la salud y devuelven la confianza a tu piel. Fuera de consulta disfruta del deporte, probar nuevos lugares para comer y pasar tiempo con sus seres queridos.",
      since: "En medicina estética desde 2023", tx: "Ácido hialurónico, bioestimuladores, toxina botulínica, calidad de piel, láser CO2 y láser para pigmentaciones.", cta: "Reserva tu valoración con Laura Vacca" },
    { name: "Dra. Angélica Bejarano", fullName: "María Angélica Bejarano Vargas", role: "Cofundadora · Medicina estética", img: "img/doctora-2.jpg",
      creds: "Médica · Universidad de La Sabana · Máster en medicina estética, nutrición y antienvejecimiento (UDIMA/AMIR) · RM 1019109708",
      bio: "Médica especialista en medicina estética, enfocada en resaltar la naturalidad de cada paciente y el cuidado y la calidad de su piel. Busca mejorar la piel de manera integral, respetando siempre su esencia, para lograr resultados sutiles que realcen su belleza sin generar cambios importantes. Fuera del consultorio disfruta el gimnasio, el pilates, la lectura y compartir con su familia.",
      since: "En medicina estética desde 2024 · pronto en ACICME", tx: "Ácido hialurónico, bioestimuladores de colágeno, toxina botulínica, exosomas y otros inyectables de calidad de piel, láser CO2.", cta: "Reserva tu valoración con Angélica Bejarano" }
  ];

  var CASES = [
    { tag: "Bioestimulación", title: "Firmeza recuperada en seis meses.",
      body: "Dos sesiones de bioestimulador de colágeno. El cambio se lee en soporte y textura, no en volumen añadido.",
      before: "img/antes-1.jpg", after: "img/despues-1.jpg", context: "Mujer, 41 años", protocol: "Bioestimulador · 2 sesiones", elapsed: "6 meses",
      rows: [["Se evaluó", "Pérdida de soporte y calidad de colágeno"], ["Enfoque elegido", "Estímulo progresivo, sin relleno"], ["Mantenimiento", "Refuerzo evaluado al mes 12"]] },
    { tag: "Calidad de piel", title: "Luz e hidratación en tres sesiones.",
      body: "Ácido hialurónico de hidratación, acompañado de rutina en casa y fotoprotección diaria.",
      before: "img/antes-2.jpg", after: "img/despues-2.jpg", context: "Mujer, 33 años", protocol: "Hidratación · 3 sesiones", elapsed: "3 meses",
      rows: [["Se evaluó", "Deshidratación y piel opaca"], ["Enfoque elegido", "Hidratación acumulativa + rutina en casa"], ["Mantenimiento", "Refuerzo según evolución"]] },
    { tag: "Toxina", title: "Menos marca, misma expresión.",
      body: "Dosis baja en tercio superior. El objetivo no era eliminar el gesto sino reducir la línea en reposo.",
      before: "img/antes-3.jpg", after: "img/despues-3.jpg", context: "Mujer, 34 años", protocol: "Toxina botulínica · tercio superior", elapsed: "21 días",
      rows: [["Se evaluó", "Línea dinámica del entrecejo"], ["Enfoque elegido", "Dosis baja, movimiento preservado"], ["Mantenimiento", "Cada 4 meses aproximadamente"]] }
  ];

  var FAQ = [
    { title: "Antes de venir", icon: "calendar", items: [
      { question: "¿Cómo puedo agendar?", answer: "Por esta página o por WhatsApp." },
      { question: "¿Dónde están ubicados?", answer: "En Medicenter, Calle 120a #7-62, Bogotá." },
      { question: "¿Me puedo hacer un procedimiento sin valoración?", answer: "No. Solo si eres paciente en seguimiento puedes agendar directamente un procedimiento ya indicado por tu médica." },
      { question: "¿Tienen valoración virtual?", answer: "Sí, dura 30 minutos por Google Meet y cuesta $45.000. Antes debes enviar el formulario de historia clínica con fotos y los productos de tu rutina en casa." }
    ] },
    { title: "Sobre los tratamientos", icon: "shield", items: [
      { question: "¿Quién hace los procedimientos?", answer: "Médicas con máster en medicina estética. Enfermería solo realiza procedimientos o tecnologías delegados por la médica tratante." },
      { question: "¿Los productos son originales?", answer: "Sí, todos tienen registro INVIMA, que te mostramos antes de la aplicación." },
      { question: "¿Me lo puedo hacer el mismo día de la consulta?", answer: "Sí, si la médica lo indica. Algunos procedimientos requieren preparación previa; si no la tienes, empezamos por ahí." },
      { question: "¿Qué procedimientos no hacen?", answer: "Rinomodelación, hilos tensores y cualquier procedimiento que comprometa tu seguridad: productos sin registro INVIMA, vencidos o sin estudios que los respalden." }
    ] },
    { title: "Precios y pagos", icon: "lock", items: [
      { question: "¿Cuánto vale la consulta?", answer: "$90.000 la valoración presencial, de unos 30 minutos. La virtual cuesta $45.000." },
      { question: "¿La consulta queda como saldo a favor?", answer: "Sí, se abona completa al procedimiento si lo inicias dentro del primer mes." },
      { question: "¿Debo pagar completo antes del procedimiento?", answer: "Sí. Puedes abonar para mantener el precio, pero el tratamiento inicia con el pago total." },
      { question: "¿Qué medios de pago manejan?", answer: "En línea: tarjeta débito o crédito, PSE y billeteras digitales a través de Bold. En consultorio: datáfono, tarjetas, efectivo, Llave y transferencias." },
      { question: "¿Tienen financiación?", answer: "Con tarjeta de crédito, o abonando hasta completar el pago." },
      { question: "¿Tienen descuentos?", answer: "5% por referido y 5% en el mes de tu cumpleaños si ya eres paciente. No son acumulables." },
      { question: "¿Entregan factura?", answer: "Recibes comprobante de pago. Si necesitas factura electrónica, solicítala al reservar." }
    ] },
    { title: "Agenda y cancelaciones", icon: "clock", items: [
      { question: "Si no puedo asistir, ¿me devuelven el dinero?", answer: "No. Si cancelas con 24 horas o más de anticipación, puedes reprogramar y el valor se sigue abonando al procedimiento. Si cancelas después, puedes reprogramar una vez más, solo de forma virtual." },
      { question: "¿Cuántas veces puedo reprogramar?", answer: "Dos veces, si avisas con más de 24 horas." },
      { question: "¿Y si no llego ni aviso?", answer: "Se pierde el valor pagado. Puedes reprogramar una vez más, de forma virtual." },
      { question: "¿Qué pasa si llego tarde?", answer: "Tienes 15 minutos de tolerancia. Hacemos la valoración, pero el procedimiento queda sujeto a disponibilidad." }
    ] },
    { title: "Después del procedimiento", icon: "refresh", items: [
      { question: "¿Requiere incapacidad?", answer: "Normalmente no. Solo el láser CO2 requiere unos 3 días sin exposición y luego protección física del sol." },
      { question: "¿Hacen control?", answer: "Sí, se programa según el tratamiento." },
      { question: "¿Cuáles son las complicaciones más comunes?", answer: "Inflamación de la zona, dolor al tacto y morados, habitualmente transitorios." }
    ] }
  ];

  var POLITICA_CANCELACION = "Cancela o reprograma con al menos 24 horas de anticipación: tu pago se mantiene abonado y puedes reprogramar hasta dos veces. Si cancelas después o no asistes, se pierde el valor pagado y puedes reagendar una vez más, solo de forma virtual. No hay devoluciones de dinero.";
  var DISCLAIMER = "Cada resultado es individual. Que logremos un resultado con un paciente no significa que se logrará lo mismo en todos, ni con el mismo protocolo.";

  var CART_KEY = "2skin.cart.v1";
  var ORDER_KEY = "2skin.order.v1";

  function readCart() {
    try { var v = JSON.parse(localStorage.getItem(CART_KEY)); return Array.isArray(v) ? v : []; } catch (e) { return []; }
  }
  function writeCart(c) {
    try { localStorage.setItem(CART_KEY, JSON.stringify(c)); } catch (e) {}
    try { window.dispatchEvent(new CustomEvent("2skin:cart", { detail: c })); } catch (e) {}
  }
  function addToCart(item) {
    var c = readCart(), hit = null;
    for (var i = 0; i < c.length; i++) if (c[i].id === item.id) hit = c[i];
    if (hit) hit.qty += (item.qty || 1); else c.push({ id: item.id, slug: item.slug || null, type: item.type, qty: item.qty || 1 });
    writeCart(c); return c;
  }
  function cartCount() { return readCart().reduce(function (a, l) { return a + l.qty; }, 0); }

  function lookup(id) {
    for (var i = 0; i < P.length; i++) for (var j = 0; j < P[i].variants.length; j++)
      if (P[i].variants[j].id === id) return { type: "protocolo", p: P[i], v: P[i].variants[j], name: P[i].variants[j].name, sub: P[i].name, img: P[i].img, price: P[i].variants[j].price, detail: P[i].variants[j].detail };
    for (var k = 0; k < TIENDA.length; k++) if (TIENDA[k].id === id)
      return { type: "producto", name: TIENDA[k].name, sub: TIENDA[k].cat, img: TIENDA[k].img, price: TIENDA[k].price, detail: TIENDA[k].size };
    for (var b = 0; b < BONOS.length; b++) if (BONOS[b].id === id)
      return { type: "bono", name: BONOS[b].name, sub: "Bono de regalo", img: "img/giftcard.jpg", price: BONOS[b].price, detail: BONOS[b].detail };
    return null;
  }
  function cartLines() {
    return readCart().map(function (l) {
      var m = lookup(l.id);
      if (!m) return null;
      return { id: l.id, qty: l.qty, name: m.name, sub: m.sub, img: m.img, detail: m.detail, unit: m.price, total: m.price * l.qty, type: m.type };
    }).filter(Boolean);
  }
  function setQty(id, q) {
    var c = readCart().map(function (l) { return l.id === id ? { id: l.id, slug: l.slug, type: l.type, qty: Math.max(1, q) } : l; });
    writeCart(c);
  }
  function removeLine(id) { writeCart(readCart().filter(function (l) { return l.id !== id; })); }
  function clearCart() { writeCart([]); }

  function saveOrder(o) { try { localStorage.setItem(ORDER_KEY, JSON.stringify(o)); } catch (e) {} }
  function readOrder() { try { return JSON.parse(localStorage.getItem(ORDER_KEY)); } catch (e) { return null; } }

  function bySlug(s) { for (var i = 0; i < P.length; i++) if (P[i].slug === s) return P[i]; return null; }

  // Ruta activa. En multipágina viene del path; en el archivo único, del hash.
  function route() {
    var h = (location.hash || "").replace(/^#\/?/, "");
    if (h) {
      var q = h.indexOf("?");
      return { page: (q < 0 ? h : h.slice(0, q)) || "Inicio.dc.html", query: q < 0 ? "" : h.slice(q + 1) };
    }
    var f = location.pathname.split("/").pop() || "Inicio.dc.html";
    return { page: f, query: (location.search || "").replace(/^\?/, "") };
  }
  function param(k) {
    try { return new URLSearchParams(route().query).get(k); } catch (e) { return null; }
  }

  // Navegación programática. Usa siempre esto, nunca location.href directo:
  // en el archivo único resuelve por hash, en el sitio servido por ruta.
  function go(page) {
    if (window.__2SKIN_SINGLE) {
      location.hash = "#" + page;
      window.scrollTo({ top: 0, behavior: "auto" });
    } else {
      location.href = page;
    }
  }

  // Modo archivo único: los enlaces .dc.html se resuelven contra el hash.
  function installRouter() {
    if (!window.__2SKIN_SINGLE || window.__2SKIN_ROUTER) return;
    window.__2SKIN_ROUTER = true;
    document.addEventListener("click", function (ev) {
      var a = ev.target && ev.target.closest ? ev.target.closest("a[href]") : null;
      if (!a) return;
      var href = a.getAttribute("href") || "";
      if (href.indexOf(".dc.html") < 0) return;
      ev.preventDefault();
      location.hash = "#" + href;
      window.scrollTo({ top: 0, behavior: "auto" });
    }, true);
  }
  if (typeof document !== "undefined") {
    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", installRouter);
    else installRouter();
    setTimeout(installRouter, 300);
  }

  window.SITE = {
    COP: COP, PRODUCTS: P, TIENDA: TIENDA, BONOS: BONOS, NEEDS: NEEDS, CATS: CATS, ZONAS: ZONAS,
    MARCAS: MARCAS, QUIZ: QUIZ, TEAM: TEAM, CASES: CASES, FAQ: FAQ,
    bySlug: bySlug, param: param, route: route, go: go, installRouter: installRouter, lookup: lookup,
    readCart: readCart, writeCart: writeCart, addToCart: addToCart, cartCount: cartCount,
    cartLines: cartLines, setQty: setQty, removeLine: removeLine, clearCart: clearCart,
    saveOrder: saveOrder, readOrder: readOrder,
    SEDE: SEDE, VALORACION: VALORACION, VIRTUAL: VIRTUAL, POLITICA_CANCELACION: POLITICA_CANCELACION, DISCLAIMER: DISCLAIMER,
    PQRS: "2skinpqrs@gmail.com",
    priceLabel: function (n) { return n ? COP(n) : "En valoración"; },
    fromLabel: function (p) { return p.fromText || (p.from ? "Desde " + COP(p.from) : "Precio en valoración"); }
  };
})();
