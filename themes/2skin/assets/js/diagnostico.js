/* 2skin · Diagnóstico de piel: una pregunta a la vez, avanza al elegir y envía al responder la última. */
(function () {
  "use strict";

  var form = document.querySelector("[data-tsk-dx]");
  if (!form) return;

  var steps = Array.prototype.slice.call(form.querySelectorAll("[data-tsk-dx-question]"));
  var total = steps.length;
  var back = form.querySelector("[data-tsk-dx-back]");
  var submit = form.querySelector("[data-tsk-dx-submit]");
  var label = form.querySelector("[data-tsk-dx-step]");
  var percent = form.querySelector("[data-tsk-dx-percent]");
  var fill = form.querySelector("[data-tsk-dx-fill]");
  var current = 0;

  form.classList.add("is-stepped");
  if (submit) submit.hidden = true;

  function show(index, focus) {
    current = Math.max(0, Math.min(index, total - 1));
    steps.forEach(function (step, i) { step.hidden = i !== current; });
    var pct = Math.round((current / total) * 100) + "%";
    if (label) label.textContent = "Pregunta " + (current + 1) + " de " + total;
    if (percent) percent.textContent = pct;
    if (fill) fill.style.width = pct;
    if (back) back.hidden = current === 0;
    if (focus) {
      var legend = steps[current].querySelector("legend");
      if (legend) legend.focus();
    }
  }

  form.addEventListener("change", function (e) {
    if (!e.target.matches("input[type=radio]")) return;
    var index = steps.indexOf(e.target.closest("[data-tsk-dx-question]"));
    if (index === total - 1) {
      if (fill) fill.style.width = "100%";
      if (percent) percent.textContent = "100%";
      form.submit();
      return;
    }
    // Pequeña pausa para que se vea la opción elegida.
    window.setTimeout(function () { show(index + 1, true); }, 160);
  });

  if (back) back.addEventListener("click", function () { show(current - 1, true); });

  show(0, false);
})();
