/* 2skin · página Reserva: tipo de paciente, agenda (días, horas y médica desde Amelia) y estado del botón de pago. */
(function () {
  "use strict";

  var form = document.querySelector("[data-tsk-reserva]");
  if (!form) return;

  var agenda = form.querySelector("[data-tsk-agenda]");
  var hint = form.querySelector("[data-tsk-hint]");
  var button = form.querySelector("#place_order");
  var summary = {
    cita: form.querySelector('[data-tsk-summary="cita"]'),
    medica: form.querySelector('[data-tsk-summary="medica"]')
  };
  var doctors = {};
  try { doctors = JSON.parse(form.getAttribute("data-medicas") || "{}"); } catch (e) { doctors = {}; }
  var hasTreatments = form.getAttribute("data-tratamientos") === "1";
  var needsSlot = form.getAttribute("data-cita") !== "0";
  var days = [];

  function tipo() {
    var checked = form.querySelector("[data-tsk-tipo]:checked");
    return checked ? checked.value : "nuevo";
  }
  function checkedValue(name) {
    var input = form.querySelector('input[name="' + name + '"]:checked');
    return input ? input.value : "";
  }
  function el(tag, cls, text) {
    var node = document.createElement(tag);
    if (cls) node.className = cls;
    if (text) node.textContent = text;
    return node;
  }
  // Pastilla de opción: <label class="tsk-slot"><input type="radio">…</label>.
  function pill(name, value, lines, extra) {
    var label = el("label", "tsk-slot" + (extra ? " " + extra : ""));
    var input = el("input", "tsk-slot__input");
    input.type = "radio";
    input.name = name;
    input.value = value;
    label.appendChild(input);
    lines.forEach(function (line) { label.appendChild(el("span", line[0], line[1])); });
    return label;
  }

  function currentDay() {
    var date = checkedValue("tsk_fecha");
    for (var i = 0; i < days.length; i++) if (days[i].fecha === date) return days[i];
    return null;
  }
  function currentHour() {
    var day = currentDay(), time = checkedValue("tsk_hora");
    if (!day) return null;
    for (var i = 0; i < day.horas.length; i++) if (day.horas[i].hora === time) return day.horas[i];
    return null;
  }

  function renderHours() {
    var box = form.querySelector("[data-tsk-hours]");
    var day = currentDay();
    box.textContent = "";
    if (!day) return;
    day.horas.forEach(function (h, i) {
      var p = pill("tsk_hora", h.hora, [["tsk-slot__main", h.etiqueta]]);
      if (i === 0) p.querySelector("input").checked = true;
      box.appendChild(p);
    });
  }

  function renderDoctors() {
    var box = form.querySelector("[data-tsk-doctors]");
    if (!box) return;
    var hour = currentHour();
    var available = hour ? hour.medicas.map(String) : [];
    var previous = checkedValue("tsk_medica");
    box.textContent = "";
    Object.keys(doctors).forEach(function (id) {
      var p = pill("tsk_medica", id, [["tsk-slot__main", doctors[id]]], "tsk-slot--doctor");
      var input = p.querySelector("input");
      input.disabled = available.indexOf(id) < 0;
      if (input.disabled) p.classList.add("is-disabled");
      box.appendChild(p);
    });
    var keep = previous && available.indexOf(previous) >= 0 ? previous : available[0];
    var target = keep && box.querySelector('input[value="' + keep + '"]');
    if (target) target.checked = true;
  }

  function update() {
    var t = tipo();
    form.querySelectorAll("[data-tsk-if]").forEach(function (node) {
      node.hidden = node.getAttribute("data-tsk-if") !== t;
    });

    var day = currentDay(), hour = currentHour();
    if (summary.cita) summary.cita.textContent = day && hour ? day.etiqueta + " · " + hour.etiqueta : "Elige día y hora";
    if (summary.medica) {
      var id = checkedValue("tsk_medica");
      var who = t === "seguimiento" && doctors[id] ? doctors[id] : summary.medica.getAttribute("data-asignada");
      summary.medica.textContent = who + " · " + summary.medica.getAttribute("data-place");
    }

    var consents = form.querySelectorAll("[data-tsk-consent]");
    var allChecked = true;
    consents.forEach(function (box) {
      var wrap = box.closest("[data-tsk-if]");
      if (!(wrap && wrap.hidden) && !box.checked) allChecked = false;
    });
    var state = !needsSlot ? (allChecked ? "ok" : "consent")
      : !agenda ? "empty" : (t === "nuevo" && hasTreatments) ? "blocked" : !(day && hour) ? "slot" : !allChecked ? "consent" : "ok";
    if (hint) hint.textContent = hint.getAttribute("data-" + (state === "ok" && !needsSlot ? "ok-bono" : state));
    if (button) button.disabled = state !== "ok";
  }

  form.addEventListener("change", function (e) {
    if (e.target.name === "tsk_fecha") { renderHours(); renderDoctors(); }
    if (e.target.name === "tsk_hora") renderDoctors();
    update();
  });

  // Agenda desde Amelia.
  if (agenda) {
    var url = form.getAttribute("data-api") + (form.getAttribute("data-api").indexOf("?") < 0 ? "?" : "&") + "servicio=" + encodeURIComponent(form.getAttribute("data-servicio"));
    var daysBox = form.querySelector("[data-tsk-days]");
    fetch(url, { credentials: "same-origin", headers: { Accept: "application/json" } })
      .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
      .then(function (data) {
        days = data.dias || [];
        daysBox.textContent = "";
        var duration = form.querySelector("[data-tsk-duration]");
        if (duration) duration.textContent = "citas de " + data.duracion + " minutos";
        if (!days.length) {
          daysBox.appendChild(el("p", "body-s tsk-text-secondary", "No hay horarios libres en los próximos días. Escríbenos y te ponemos en lista de espera."));
        }
        days.forEach(function (d, i) {
          var p = pill("tsk_fecha", d.fecha, [["tsk-slot__dow", d.dia], ["tsk-slot__num", d.numero], ["tsk-slot__mon", d.mes]], "tsk-slot--day");
          p.setAttribute("aria-label", d.etiqueta);
          if (i === 0) p.querySelector("input").checked = true;
          daysBox.appendChild(p);
        });
        agenda.setAttribute("aria-busy", "false");
        renderHours();
        renderDoctors();
        update();
      })
      .catch(function () {
        daysBox.textContent = "";
        daysBox.appendChild(el("p", "body-s tsk-text-secondary", "No pudimos cargar la agenda. Recarga la página o escríbenos."));
        agenda.setAttribute("aria-busy", "false");
      });
  }

  update();
})();
