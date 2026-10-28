/* 2skin · comportamiento del sitio: encabezado, menú móvil, acordeón y comparador antes/después. */
(function () {
  "use strict";

  // Encabezado: 88 → 72 px al hacer scroll.
  var header = document.querySelector("[data-tsk-header]");
  if (header) {
    var onScroll = function () { header.classList.toggle("is-scrolled", window.scrollY > 24); };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  // Menú móvil.
  var toggle = document.querySelector("[data-tsk-menu-toggle]");
  if (toggle) {
    var menu = document.getElementById(toggle.getAttribute("aria-controls"));
    var label = toggle.querySelector(".screen-reader-text");
    var setOpen = function (open) {
      toggle.setAttribute("aria-expanded", String(open));
      menu.hidden = !open;
      if (label) label.textContent = open ? "Cerrar menú" : "Abrir menú";
    };
    toggle.addEventListener("click", function () { setOpen(menu.hidden); });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && !menu.hidden) { setOpen(false); toggle.focus(); }
    });
  }

  // Acordeón: sin JS todo queda abierto; con JS se cierra y se abre de a uno.
  document.querySelectorAll("[data-tsk-accordion]").forEach(function (acc) {
    var triggers = acc.querySelectorAll(".tsk-accordion__trigger");
    var set = function (trigger, open) {
      var panel = document.getElementById(trigger.getAttribute("aria-controls"));
      trigger.setAttribute("aria-expanded", String(open));
      trigger.querySelector(".tsk-accordion__sign").textContent = open ? "−" : "+";
      panel.classList.toggle("tsk-accordion__panel--open", open);
      panel.inert = !open;
    };
    triggers.forEach(function (t) {
      document.getElementById(t.getAttribute("aria-controls")).inert = t.getAttribute("aria-expanded") !== "true";
      t.addEventListener("click", function () {
        var open = t.getAttribute("aria-expanded") !== "true";
        triggers.forEach(function (o) { if (o !== t) set(o, false); });
        set(t, open);
      });
    });
  });

  // Filtros del catálogo: en móvil arrancan plegados salvo que haya alguno activo; en escritorio siempre abiertos.
  document.querySelectorAll("[data-tsk-filters]").forEach(function (panel) {
    var mobile = window.matchMedia("(max-width: 980px)");
    var sync = function () { panel.open = !mobile.matches || panel.hasAttribute("data-active"); };
    sync();
    mobile.addEventListener("change", sync);
  });

  // Opciones del tratamiento: la etiqueta de los botones sigue a la opción elegida; sin precio no se reserva en línea.
  document.querySelectorAll("[data-tsk-options]").forEach(function (form) {
    var book = form.querySelector("[data-tsk-book] .tsk-btn__label");
    var add = form.querySelector("[data-tsk-add]");
    form.addEventListener("change", function (e) {
      var input = e.target;
      if (!input.classList.contains("tsk-option__input")) return;
      if (book && input.dataset.bookLabel) book.textContent = input.dataset.bookLabel;
      if (add) {
        add.hidden = !input.dataset.addLabel;
        add.querySelector(".tsk-btn__label").textContent = input.dataset.addLabel;
      }
    });
  });

  // Comparador antes/después.
  document.querySelectorAll("[data-tsk-ba]").forEach(function (ba) {
    var frame = ba.querySelector(".tsk-ba__frame");
    var before = ba.querySelector(".tsk-ba__layer--before");
    var handle = ba.querySelector(".tsk-ba__handle");
    var pos = 50;
    var render = function (p) {
      pos = Math.max(0, Math.min(100, p));
      before.style.clipPath = "inset(0 " + (100 - pos) + "% 0 0)";
      handle.style.left = pos + "%";
      handle.setAttribute("aria-valuenow", String(Math.round(pos)));
    };
    var fromX = function (x) {
      var r = frame.getBoundingClientRect();
      render(((x - r.left) / r.width) * 100);
    };
    frame.addEventListener("pointerdown", function (e) {
      fromX(e.clientX);
      var move = function (ev) { fromX(ev.clientX); };
      var up = function () {
        window.removeEventListener("pointermove", move);
        window.removeEventListener("pointerup", up);
      };
      window.addEventListener("pointermove", move);
      window.addEventListener("pointerup", up);
    });
    handle.addEventListener("keydown", function (e) {
      if (e.key === "ArrowLeft") { render(pos - 4); e.preventDefault(); }
      if (e.key === "ArrowRight") { render(pos + 4); e.preventDefault(); }
    });
  });
})();
