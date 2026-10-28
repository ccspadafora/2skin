/* @ds-bundle: {"format":4,"namespace":"Ds2skinDesignSystem_023a03","components":[{"name":"Button","sourcePath":"components/actions/Button.jsx"},{"name":"TextLink","sourcePath":"components/actions/TextLink.jsx"},{"name":"DoctorCard","sourcePath":"components/cards/DoctorCard.jsx"},{"name":"NeedCard","sourcePath":"components/cards/NeedCard.jsx"},{"name":"TestimonialCard","sourcePath":"components/cards/TestimonialCard.jsx"},{"name":"TreatmentCard","sourcePath":"components/cards/TreatmentCard.jsx"},{"name":"Accordion","sourcePath":"components/content/Accordion.jsx"},{"name":"ArticleCard","sourcePath":"components/content/ArticleCard.jsx"},{"name":"Quote","sourcePath":"components/content/Quote.jsx"},{"name":"Checkbox","sourcePath":"components/forms/Checkbox.jsx"},{"name":"Field","sourcePath":"components/forms/Field.jsx"},{"name":"Input","sourcePath":"components/forms/Input.jsx"},{"name":"Select","sourcePath":"components/forms/Select.jsx"},{"name":"Textarea","sourcePath":"components/forms/Textarea.jsx"},{"name":"BeforeAfter","sourcePath":"components/media/BeforeAfter.jsx"},{"name":"Gallery","sourcePath":"components/media/Gallery.jsx"},{"name":"Footer","sourcePath":"components/navigation/Footer.jsx"},{"name":"Header","sourcePath":"components/navigation/Header.jsx"},{"name":"MobileNav","sourcePath":"components/navigation/MobileNav.jsx"},{"name":"Badge","sourcePath":"components/primitives/Badge.jsx"},{"name":"Eyebrow","sourcePath":"components/primitives/Eyebrow.jsx"},{"name":"Media","sourcePath":"components/primitives/Media.jsx"},{"name":"CTASection","sourcePath":"components/sections/CTASection.jsx"},{"name":"DarkSection","sourcePath":"components/sections/DarkSection.jsx"},{"name":"EditorialSplit","sourcePath":"components/sections/EditorialSplit.jsx"},{"name":"Hero","sourcePath":"components/sections/Hero.jsx"},{"name":"ResultsSection","sourcePath":"components/sections/ResultsSection.jsx"}],"sourceHashes":{"components/actions/Button.jsx":"39050a08e565","components/actions/TextLink.jsx":"5eee936b79c3","components/cards/DoctorCard.jsx":"b6cb7d76c71b","components/cards/NeedCard.jsx":"41534dd67912","components/cards/TestimonialCard.jsx":"bbcbf57a4132","components/cards/TreatmentCard.jsx":"2fa07ae4cb09","components/content/Accordion.jsx":"6fe4719a9732","components/content/ArticleCard.jsx":"30c448e8eda5","components/content/Quote.jsx":"54821de2a2cb","components/forms/Checkbox.jsx":"b395fc19b9d5","components/forms/Field.jsx":"10d247674062","components/forms/Input.jsx":"186d58080835","components/forms/Select.jsx":"6829d255bbc4","components/forms/Textarea.jsx":"8cec2c8a0935","components/media/BeforeAfter.jsx":"d456e5786cf9","components/media/Gallery.jsx":"432c60b0f901","components/navigation/Footer.jsx":"27b7cbd8318b","components/navigation/Header.jsx":"dc0e993dc81f","components/navigation/MobileNav.jsx":"4b1840bdbf45","components/primitives/Badge.jsx":"629adb0683ac","components/primitives/Eyebrow.jsx":"c0127987342f","components/primitives/Media.jsx":"898043fb85b5","components/sections/CTASection.jsx":"4447f07925b3","components/sections/DarkSection.jsx":"708ec5ec013c","components/sections/EditorialSplit.jsx":"98ede28db7fe","components/sections/Hero.jsx":"bb3c8cafa225","components/sections/ResultsSection.jsx":"c04425ceb06e","ui_kits/website/ContactScreen.jsx":"981b29764693","ui_kits/website/HomeScreen.jsx":"0fcff896fc03","ui_kits/website/SkinQualityScreen.jsx":"bfdece436e69","ui_kits/website/TreatmentScreen.jsx":"4dd000c9859d","ui_kits/website/shared.jsx":"b7d64c34d86c"},"inlinedExternals":[],"unexposedExports":[]} */

(() => {

const __ds_ns = (window.Ds2skinDesignSystem_023a03 = window.Ds2skinDesignSystem_023a03 || {});

const __ds_scope = {};

(__ds_ns.__errors = __ds_ns.__errors || []);

// components/actions/Button.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
/**
 * 2skin Button — Button.Primary / .Secondary / .Sage / .Text
 * Primary is charcoal on cream; sage is deliberately rare.
 */
function Button({
  variant = "primary",
  size = "md",
  href,
  arrow = false,
  loading = false,
  disabled = false,
  children,
  className = "",
  ...rest
}) {
  const cls = ["tsk-btn", `tsk-btn--${variant}`, size !== "md" ? `tsk-btn--${size}` : "", loading ? "tsk-btn--loading" : "", className].filter(Boolean).join(" ");
  const inner = /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("span", {
    className: "tsk-btn__label"
  }, children), (arrow || variant === "text") && /*#__PURE__*/React.createElement("span", {
    className: "tsk-btn__arrow",
    "aria-hidden": "true"
  }, "\u2192"));
  if (href && !disabled) {
    return /*#__PURE__*/React.createElement("a", _extends({
      className: cls,
      href: href
    }, rest), inner);
  }
  return /*#__PURE__*/React.createElement("button", _extends({
    className: cls,
    type: rest.type || "button",
    disabled: disabled || loading,
    "aria-busy": loading || undefined
  }, rest), inner);
}
Object.assign(__ds_scope, { Button });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/actions/Button.jsx", error: String((e && e.message) || e) }); }

// components/actions/TextLink.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
/** Inline text link. Charcoal ink, 1px underline offset 4px. */
function TextLink({
  href = "#",
  quiet = false,
  children,
  className = "",
  ...rest
}) {
  return /*#__PURE__*/React.createElement("a", _extends({
    href: href,
    className: ["tsk-link", quiet ? "tsk-link--quiet" : "", className].filter(Boolean).join(" ")
  }, rest), children);
}
Object.assign(__ds_scope, { TextLink });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/actions/TextLink.jsx", error: String((e && e.message) || e) }); }

// components/cards/NeedCard.jsx
try { (() => {
/** Card.Need — the preferred entry point: navigate by need, not by product. */
function NeedCard({
  need,
  note,
  href = "#",
  boxed = false
}) {
  return /*#__PURE__*/React.createElement("a", {
    className: ["tsk-need", boxed ? "tsk-need--boxed" : ""].filter(Boolean).join(" "),
    href: href
  }, /*#__PURE__*/React.createElement("span", null, /*#__PURE__*/React.createElement("span", {
    className: "tsk-need__name"
  }, need), note && /*#__PURE__*/React.createElement("span", {
    style: {
      display: "block",
      marginTop: "var(--space-2)",
      fontSize: ".9375rem",
      color: "var(--color-text-secondary)"
    }
  }, note)), /*#__PURE__*/React.createElement("span", {
    className: "tsk-need__arrow",
    "aria-hidden": "true"
  }, "\u2192"));
}
Object.assign(__ds_scope, { NeedCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/NeedCard.jsx", error: String((e && e.message) || e) }); }

// components/cards/TestimonialCard.jsx
try { (() => {
/** Card.Testimonial — 2–4 visible lines, name or initial, treatment context. No star ratings. */
function TestimonialCard({
  quote,
  author,
  context
}) {
  return /*#__PURE__*/React.createElement("figure", {
    className: "tsk-testimonial",
    style: {
      margin: 0
    }
  }, /*#__PURE__*/React.createElement("blockquote", {
    className: "tsk-testimonial__quote",
    style: {
      margin: 0
    }
  }, "\u201C", quote, "\u201D"), /*#__PURE__*/React.createElement("figcaption", {
    className: "tsk-testimonial__meta"
  }, author && /*#__PURE__*/React.createElement("span", {
    className: "tsk-testimonial__author"
  }, author), author && context ? " · " : "", context));
}
Object.assign(__ds_scope, { TestimonialCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/TestimonialCard.jsx", error: String((e && e.message) || e) }); }

// components/content/Accordion.jsx
try { (() => {
/** Content.Accordion — minimal FAQ list. Hairlines, +/− sign, one open at a time by default. */
function Accordion({
  items = [],
  allowMultiple = false,
  defaultOpen = -1
}) {
  const [open, setOpen] = React.useState(defaultOpen >= 0 ? [defaultOpen] : []);
  const toggle = i => setOpen(cur => cur.includes(i) ? cur.filter(x => x !== i) : allowMultiple ? [...cur, i] : [i]);
  return /*#__PURE__*/React.createElement("div", {
    className: "tsk-accordion"
  }, items.map((item, i) => {
    const isOpen = open.includes(i);
    return /*#__PURE__*/React.createElement("div", {
      className: "tsk-accordion__item",
      key: item.question
    }, /*#__PURE__*/React.createElement("button", {
      className: "tsk-accordion__trigger",
      "aria-expanded": isOpen,
      "aria-controls": `tsk-panel-${i}`,
      onClick: () => toggle(i)
    }, /*#__PURE__*/React.createElement("span", null, item.question), /*#__PURE__*/React.createElement("span", {
      className: "tsk-accordion__sign",
      "aria-hidden": "true"
    }, isOpen ? "−" : "+")), /*#__PURE__*/React.createElement("div", {
      className: ["tsk-accordion__panel", isOpen ? "tsk-accordion__panel--open" : ""].filter(Boolean).join(" "),
      id: `tsk-panel-${i}`,
      role: "region"
    }, /*#__PURE__*/React.createElement("p", {
      className: "tsk-accordion__body"
    }, item.answer)));
  }));
}
Object.assign(__ds_scope, { Accordion });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/content/Accordion.jsx", error: String((e && e.message) || e) }); }

// components/content/Quote.jsx
try { (() => {
/** Content.Quote — large EB Garamond editorial statement. */
function Quote({
  children,
  cite,
  align = "left"
}) {
  return /*#__PURE__*/React.createElement("figure", {
    style: {
      margin: 0,
      textAlign: align === "center" ? "center" : "left"
    }
  }, /*#__PURE__*/React.createElement("blockquote", {
    className: "tsk-quote",
    style: {
      margin: align === "center" ? "0 auto" : 0
    }
  }, "\u201C", children, "\u201D"), cite && /*#__PURE__*/React.createElement("figcaption", {
    className: "tsk-quote__cite"
  }, cite));
}
Object.assign(__ds_scope, { Quote });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/content/Quote.jsx", error: String((e && e.message) || e) }); }

// components/forms/Checkbox.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
/** Checkbox or radio with a 44px-tall hit area. */
function Checkbox({
  type = "checkbox",
  label,
  className = "",
  ...rest
}) {
  return /*#__PURE__*/React.createElement("label", {
    className: ["tsk-check", className].filter(Boolean).join(" ")
  }, /*#__PURE__*/React.createElement("input", _extends({
    type: type
  }, rest)), /*#__PURE__*/React.createElement("span", {
    className: ["tsk-check__box", type === "radio" ? "tsk-check__box--radio" : ""].filter(Boolean).join(" ")
  }, type === "radio" ? /*#__PURE__*/React.createElement("span", {
    className: "tsk-check__dot"
  }) : /*#__PURE__*/React.createElement("svg", {
    className: "tsk-check__mark",
    viewBox: "0 0 10 10",
    fill: "none",
    "aria-hidden": "true"
  }, /*#__PURE__*/React.createElement("path", {
    d: "M1 5.2 3.6 7.8 9 2.2",
    stroke: "#F6F4F0",
    strokeWidth: "1.4",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }))), /*#__PURE__*/React.createElement("span", null, label));
}
Object.assign(__ds_scope, { Checkbox });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/forms/Checkbox.jsx", error: String((e && e.message) || e) }); }

// components/forms/Field.jsx
try { (() => {
/** Shared label/hint/error wrapper for every form control. */
function Field({
  label,
  htmlFor,
  optional = false,
  hint,
  error,
  children,
  className = ""
}) {
  return /*#__PURE__*/React.createElement("div", {
    className: ["tsk-field", className].filter(Boolean).join(" ")
  }, label && /*#__PURE__*/React.createElement("label", {
    className: "tsk-field__label",
    htmlFor: htmlFor
  }, label, optional && /*#__PURE__*/React.createElement("span", {
    className: "tsk-field__optional"
  }, " \xB7 opcional")), children, error ? /*#__PURE__*/React.createElement("span", {
    className: "tsk-field__error"
  }, error) : hint ? /*#__PURE__*/React.createElement("span", {
    className: "tsk-field__hint"
  }, hint) : null);
}
Object.assign(__ds_scope, { Field });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/forms/Field.jsx", error: String((e && e.message) || e) }); }

// components/forms/Input.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
/** 52px text input on white, silver border, 4px radius. */
function Input({
  invalid = false,
  className = "",
  ...rest
}) {
  return /*#__PURE__*/React.createElement("input", _extends({
    className: ["tsk-input", className].filter(Boolean).join(" "),
    "aria-invalid": invalid || undefined
  }, rest));
}
Object.assign(__ds_scope, { Input });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/forms/Input.jsx", error: String((e && e.message) || e) }); }

// components/forms/Select.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
/** Native select with a thin chevron drawn from the charcoal token. */
function Select({
  options = [],
  placeholder,
  invalid = false,
  className = "",
  ...rest
}) {
  return /*#__PURE__*/React.createElement("select", _extends({
    className: ["tsk-select", className].filter(Boolean).join(" "),
    "aria-invalid": invalid || undefined,
    defaultValue: rest.value === undefined && placeholder ? "" : undefined
  }, rest), placeholder && /*#__PURE__*/React.createElement("option", {
    value: "",
    disabled: true
  }, placeholder), options.map(o => {
    const value = typeof o === "string" ? o : o.value;
    const label = typeof o === "string" ? o : o.label;
    return /*#__PURE__*/React.createElement("option", {
      key: value,
      value: value
    }, label);
  }));
}
Object.assign(__ds_scope, { Select });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/forms/Select.jsx", error: String((e && e.message) || e) }); }

// components/forms/Textarea.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
/** Multi-line input, min 120px, vertical resize only. */
function Textarea({
  invalid = false,
  rows = 4,
  className = "",
  ...rest
}) {
  return /*#__PURE__*/React.createElement("textarea", _extends({
    className: ["tsk-textarea", className].filter(Boolean).join(" "),
    rows: rows,
    "aria-invalid": invalid || undefined
  }, rest));
}
Object.assign(__ds_scope, { Textarea });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/forms/Textarea.jsx", error: String((e && e.message) || e) }); }

// components/media/BeforeAfter.jsx
try { (() => {
/**
 * Media.BeforeAfter — accessible slider (drag or arrow keys) with BEFORE/AFTER
 * labels, protocol context and the required variability disclaimer.
 */
function BeforeAfter({
  beforeSrc,
  afterSrc,
  beforeAlt = "Antes",
  afterAlt = "Después",
  context,
  protocol,
  elapsed,
  disclaimer = "Los resultados varían según la piel, la edad, los hábitos y el protocolo indicado. Las imágenes corresponden a un caso real y no representan una promesa de resultado.",
  defaultPosition = 50
}) {
  const [pos, setPos] = React.useState(defaultPosition);
  const frame = React.useRef(null);
  const move = clientX => {
    const el = frame.current;
    if (!el) return;
    const r = el.getBoundingClientRect();
    setPos(Math.max(0, Math.min(100, (clientX - r.left) / r.width * 100)));
  };
  const onPointerDown = e => {
    move(e.clientX);
    const onMove = ev => move(ev.clientX);
    const onUp = () => {
      window.removeEventListener("pointermove", onMove);
      window.removeEventListener("pointerup", onUp);
    };
    window.addEventListener("pointermove", onMove);
    window.addEventListener("pointerup", onUp);
  };
  const onKeyDown = e => {
    if (e.key === "ArrowLeft") setPos(p => Math.max(0, p - 4));
    if (e.key === "ArrowRight") setPos(p => Math.min(100, p + 4));
  };
  const layer = (src, alt) => src ? /*#__PURE__*/React.createElement("img", {
    src: src,
    alt: alt,
    style: {
      width: "100%",
      height: "100%",
      objectFit: "cover"
    }
  }) : /*#__PURE__*/React.createElement("div", {
    className: "tsk-media__placeholder"
  }, /*#__PURE__*/React.createElement("span", null, alt));
  return /*#__PURE__*/React.createElement("div", {
    className: "tsk-ba"
  }, /*#__PURE__*/React.createElement("div", {
    className: "tsk-ba__frame",
    ref: frame,
    onPointerDown: onPointerDown
  }, /*#__PURE__*/React.createElement("div", {
    className: "tsk-ba__layer"
  }, layer(afterSrc, afterAlt)), /*#__PURE__*/React.createElement("div", {
    className: "tsk-ba__layer",
    style: {
      clipPath: `inset(0 ${100 - pos}% 0 0)`
    }
  }, layer(beforeSrc, beforeAlt)), /*#__PURE__*/React.createElement("span", {
    className: "tsk-ba__tag",
    style: {
      left: "var(--space-4)"
    }
  }, "Antes"), /*#__PURE__*/React.createElement("span", {
    className: "tsk-ba__tag",
    style: {
      right: "var(--space-4)"
    }
  }, "Despu\xE9s"), /*#__PURE__*/React.createElement("div", {
    className: "tsk-ba__handle",
    style: {
      left: `${pos}%`
    }
  }, /*#__PURE__*/React.createElement("div", {
    className: "tsk-ba__grip",
    role: "slider",
    tabIndex: 0,
    "aria-label": "Comparar antes y despu\xE9s",
    "aria-valuenow": Math.round(pos),
    "aria-valuemin": 0,
    "aria-valuemax": 100,
    onKeyDown: onKeyDown
  }, "\u27F7"))), (context || protocol || elapsed) && /*#__PURE__*/React.createElement("div", {
    className: "tsk-ba__meta"
  }, context && /*#__PURE__*/React.createElement("span", null, context), protocol && /*#__PURE__*/React.createElement("span", null, protocol), elapsed && /*#__PURE__*/React.createElement("span", null, elapsed)), disclaimer && /*#__PURE__*/React.createElement("p", {
    className: "tsk-ba__disclaimer"
  }, disclaimer));
}
Object.assign(__ds_scope, { BeforeAfter });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/media/BeforeAfter.jsx", error: String((e && e.message) || e) }); }

// components/navigation/Footer.jsx
try { (() => {
/** Navigation.Footer — charcoal ground, cream text, sage column titles. */
function Footer({
  logoSrc = "assets/2skin-monogram-cream.png",
  statement = "Medicina estética con criterio.",
  columns = [{
    title: "Navegación",
    links: ["Tratamientos", "Calidad de piel", "Resultados", "Nosotros", "Contacto"]
  }, {
    title: "Contacto",
    links: ["Bogotá", "WhatsApp", "Instagram"]
  }, {
    title: "Legal",
    links: ["Privacidad", "Términos"]
  }],
  note = "© 2skin Medical Aesthetics"
}) {
  return /*#__PURE__*/React.createElement("footer", {
    className: "tsk-footer tsk-on-dark"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement("div", {
    className: "tsk-footer__grid"
  }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    className: "tsk-footer__logo"
  }, /*#__PURE__*/React.createElement("img", {
    src: logoSrc,
    alt: "2skin"
  })), /*#__PURE__*/React.createElement("p", {
    style: {
      marginTop: "var(--space-5)",
      fontFamily: "var(--font-display)",
      fontSize: "1.375rem",
      lineHeight: 1.3,
      maxWidth: "18ch"
    }
  }, statement)), columns.map(col => /*#__PURE__*/React.createElement("div", {
    key: col.title
  }, /*#__PURE__*/React.createElement("div", {
    className: "tsk-footer__col-title"
  }, col.title), /*#__PURE__*/React.createElement("div", {
    className: "tsk-footer__links"
  }, col.links.map(l => /*#__PURE__*/React.createElement("a", {
    key: l,
    href: "#"
  }, l)))))), /*#__PURE__*/React.createElement("div", {
    className: "tsk-footer__base"
  }, /*#__PURE__*/React.createElement("span", null, note), /*#__PURE__*/React.createElement("span", null, "Los tratamientos requieren valoraci\xF3n m\xE9dica previa."))));
}
Object.assign(__ds_scope, { Footer });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/navigation/Footer.jsx", error: String((e && e.message) || e) }); }

// components/navigation/Header.jsx
try { (() => {
/** Navigation.Header — sticky, 88px, shrinks to 72px with a hairline on scroll. */
function Header({
  links = [{
    label: "Tratamientos",
    href: "#"
  }, {
    label: "Calidad de piel",
    href: "#"
  }, {
    label: "Nosotros",
    href: "#"
  }, {
    label: "Resultados",
    href: "#"
  }, {
    label: "Contacto",
    href: "#"
  }],
  current,
  logoSrc = "assets/2skin-wordmark-charcoal.png",
  cta = "Agendar valoración",
  onCtaClick,
  onMenuClick,
  onNavigate,
  scrolled: scrolledProp
}) {
  const [scrolled, setScrolled] = React.useState(false);
  React.useEffect(() => {
    if (scrolledProp !== undefined) return;
    const onScroll = () => setScrolled(window.scrollY > 24);
    onScroll();
    window.addEventListener("scroll", onScroll, {
      passive: true
    });
    return () => window.removeEventListener("scroll", onScroll);
  }, [scrolledProp]);
  const isScrolled = scrolledProp !== undefined ? scrolledProp : scrolled;
  return /*#__PURE__*/React.createElement("header", {
    className: ["tsk-header", isScrolled ? "tsk-header--scrolled" : ""].filter(Boolean).join(" ")
  }, /*#__PURE__*/React.createElement("div", {
    className: "container tsk-header__inner"
  }, /*#__PURE__*/React.createElement("a", {
    className: "tsk-header__logo",
    href: "#",
    "aria-label": "2skin \xB7 inicio"
  }, /*#__PURE__*/React.createElement("img", {
    src: logoSrc,
    alt: "2skin Medical Aesthetics"
  })), /*#__PURE__*/React.createElement("nav", {
    className: "tsk-header__nav"
  }, links.map(l => /*#__PURE__*/React.createElement("a", {
    key: l.label,
    className: "tsk-header__link",
    href: l.href || "#",
    "aria-current": current === l.label ? "page" : undefined,
    onClick: onNavigate ? e => {
      e.preventDefault();
      onNavigate(l);
    } : undefined
  }, l.label)), /*#__PURE__*/React.createElement(__ds_scope.Button, {
    size: "sm",
    onClick: onCtaClick
  }, cta)), /*#__PURE__*/React.createElement("button", {
    className: "tsk-header__burger",
    "aria-label": "Abrir men\xFA",
    onClick: onMenuClick
  }, /*#__PURE__*/React.createElement("span", null), /*#__PURE__*/React.createElement("span", null))));
}
Object.assign(__ds_scope, { Header });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/navigation/Header.jsx", error: String((e && e.message) || e) }); }

// components/navigation/MobileNav.jsx
try { (() => {
/** Navigation.Mobile — full-screen cream menu in the brand's recommended order. */
function MobileNav({
  open = false,
  onClose,
  onNavigate,
  logoSrc = "assets/2skin-wordmark-charcoal.png",
  items = ["Tratamientos", "Calidad de piel", "Resultados", "Sobre 2skin", "Preguntas frecuentes", "Contacto"],
  cta = "Agendar valoración",
  onCtaClick
}) {
  return /*#__PURE__*/React.createElement("div", {
    className: ["tsk-mobilenav", open ? "tsk-mobilenav--open" : ""].filter(Boolean).join(" "),
    "aria-hidden": !open
  }, /*#__PURE__*/React.createElement("div", {
    className: "tsk-mobilenav__top"
  }, /*#__PURE__*/React.createElement("img", {
    src: logoSrc,
    alt: "2skin Medical Aesthetics",
    style: {
      height: 26
    }
  }), /*#__PURE__*/React.createElement("button", {
    onClick: onClose,
    "aria-label": "Cerrar men\xFA",
    style: {
      width: 44,
      height: 44,
      background: "none",
      border: 0,
      fontSize: 20,
      cursor: "pointer",
      color: "var(--charcoal)"
    }
  }, "\u2715")), /*#__PURE__*/React.createElement("nav", {
    className: "tsk-mobilenav__list"
  }, items.map(label => /*#__PURE__*/React.createElement("a", {
    key: label,
    className: "tsk-mobilenav__item",
    href: "#",
    onClick: e => {
      e.preventDefault();
      onNavigate && onNavigate(label);
    }
  }, label))), /*#__PURE__*/React.createElement("div", {
    style: {
      marginTop: "var(--space-8)"
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Button, {
    size: "lg",
    onClick: onCtaClick,
    className: "tsk-mobilenav__cta",
    style: {
      width: "100%"
    }
  }, cta)));
}
Object.assign(__ds_scope, { MobileNav });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/navigation/MobileNav.jsx", error: String((e && e.message) || e) }); }

// components/primitives/Badge.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
/** Small taxonomy badge: category, modality, technology. Limited use. */
function Badge({
  variant = "outline",
  children,
  className = "",
  ...rest
}) {
  return /*#__PURE__*/React.createElement("span", _extends({
    className: ["tsk-badge", variant !== "outline" ? `tsk-badge--${variant}` : "", className].filter(Boolean).join(" ")
  }, rest), children);
}
Object.assign(__ds_scope, { Badge });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/primitives/Badge.jsx", error: String((e && e.message) || e) }); }

// components/primitives/Eyebrow.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
/** Uppercase Inter label that sits above editorial headings. */
function Eyebrow({
  as: Tag = "p",
  children,
  className = "",
  ...rest
}) {
  return /*#__PURE__*/React.createElement(Tag, _extends({
    className: ["tsk-eyebrow", className].filter(Boolean).join(" ")
  }, rest), children);
}
Object.assign(__ds_scope, { Eyebrow });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/primitives/Eyebrow.jsx", error: String((e && e.message) || e) }); }

// components/primitives/Media.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
/**
 * Image frame with the brand's aspect ratios and a discreet placeholder when
 * no photography is supplied yet.
 */
function Media({
  src,
  alt = "",
  ratio = "landscape",
  flush = false,
  placeholder = "Fotografía 2skin",
  overlay,
  className = "",
  ...rest
}) {
  return /*#__PURE__*/React.createElement("figure", _extends({
    className: ["tsk-media", `tsk-media--${ratio}`, flush ? "tsk-media--flush" : "", className].filter(Boolean).join(" "),
    style: {
      margin: 0
    }
  }, rest), src ? /*#__PURE__*/React.createElement("img", {
    src: src,
    alt: alt
  }) : /*#__PURE__*/React.createElement("div", {
    className: "tsk-media__placeholder"
  }, /*#__PURE__*/React.createElement("span", null, placeholder)), overlay);
}
Object.assign(__ds_scope, { Media });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/primitives/Media.jsx", error: String((e && e.message) || e) }); }

// components/cards/DoctorCard.jsx
try { (() => {
/** Card.Doctor — portrait, name, role, condensed credentials, profile link. */
function DoctorCard({
  name,
  role,
  credentials,
  image,
  imageAlt = "",
  href = "#",
  cta = "Conocer perfil"
}) {
  return /*#__PURE__*/React.createElement("div", {
    className: "tsk-card"
  }, /*#__PURE__*/React.createElement(__ds_scope.Media, {
    src: image,
    alt: imageAlt || name,
    ratio: "portrait",
    placeholder: "Retrato"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      flexDirection: "column",
      gap: "var(--space-2)"
    }
  }, /*#__PURE__*/React.createElement("span", {
    className: "tsk-doctor__role"
  }, role), /*#__PURE__*/React.createElement("h3", {
    className: "tsk-doctor__name"
  }, name), credentials && /*#__PURE__*/React.createElement("p", {
    className: "tsk-doctor__creds"
  }, credentials)), /*#__PURE__*/React.createElement("a", {
    className: "tsk-btn tsk-btn--text",
    href: href
  }, /*#__PURE__*/React.createElement("span", {
    className: "tsk-btn__label"
  }, cta), /*#__PURE__*/React.createElement("span", {
    className: "tsk-btn__arrow",
    "aria-hidden": "true"
  }, "\u2192")));
}
Object.assign(__ds_scope, { DoctorCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/DoctorCard.jsx", error: String((e && e.message) || e) }); }

// components/cards/TreatmentCard.jsx
try { (() => {
/** Card.Treatment — image, category, name, 1–2 line description, single arrow CTA. */
function TreatmentCard({
  name,
  category,
  description,
  image,
  imageAlt = "",
  ratio = "landscape",
  badges = [],
  href = "#",
  cta = "Ver tratamiento"
}) {
  return /*#__PURE__*/React.createElement("a", {
    className: "tsk-card tsk-card--link",
    href: href
  }, /*#__PURE__*/React.createElement(__ds_scope.Media, {
    src: image,
    alt: imageAlt,
    ratio: ratio,
    placeholder: name
  }), category && /*#__PURE__*/React.createElement("span", {
    className: "tsk-card__cat"
  }, category), /*#__PURE__*/React.createElement("h3", {
    className: "tsk-card__title"
  }, name), description && /*#__PURE__*/React.createElement("p", {
    className: "tsk-card__body"
  }, description), badges.length > 0 && /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      flexWrap: "wrap",
      gap: "var(--space-2)"
    }
  }, badges.map(b => /*#__PURE__*/React.createElement(__ds_scope.Badge, {
    key: b
  }, b))), /*#__PURE__*/React.createElement("span", {
    className: "tsk-card__cta tsk-btn tsk-btn--text",
    style: {
      marginTop: "var(--space-1)"
    }
  }, /*#__PURE__*/React.createElement("span", {
    className: "tsk-btn__label"
  }, cta), /*#__PURE__*/React.createElement("span", {
    className: "tsk-btn__arrow",
    "aria-hidden": "true"
  }, "\u2192")));
}
Object.assign(__ds_scope, { TreatmentCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/TreatmentCard.jsx", error: String((e && e.message) || e) }); }

// components/content/ArticleCard.jsx
try { (() => {
/** Content.ArticleCard — educational article teaser. */
function ArticleCard({
  title,
  category,
  readingTime,
  image,
  imageAlt = "",
  href = "#"
}) {
  return /*#__PURE__*/React.createElement("a", {
    className: "tsk-article",
    href: href
  }, /*#__PURE__*/React.createElement(__ds_scope.Media, {
    src: image,
    alt: imageAlt,
    ratio: "landscape",
    placeholder: "Educaci\xF3n"
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-article__meta"
  }, category && /*#__PURE__*/React.createElement("span", null, category), readingTime && /*#__PURE__*/React.createElement("span", null, readingTime)), /*#__PURE__*/React.createElement("h3", {
    className: "tsk-article__title"
  }, title));
}
Object.assign(__ds_scope, { ArticleCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/content/ArticleCard.jsx", error: String((e && e.message) || e) }); }

// components/media/Gallery.jsx
try { (() => {
/** Media.Gallery — clinic / space imagery in a calm grid. */
function Gallery({
  items = [],
  ratio = "landscape",
  columns
}) {
  return /*#__PURE__*/React.createElement("div", {
    className: "tsk-gallery",
    style: columns ? {
      gridTemplateColumns: `repeat(${columns}, minmax(0,1fr))`
    } : undefined
  }, items.map((item, i) => /*#__PURE__*/React.createElement(__ds_scope.Media, {
    key: item.src || item.caption || i,
    src: item.src,
    alt: item.alt || "",
    ratio: item.ratio || ratio,
    placeholder: item.caption || "Espacio 2skin"
  })));
}
Object.assign(__ds_scope, { Gallery });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/media/Gallery.jsx", error: String((e && e.message) || e) }); }

// components/sections/CTASection.jsx
try { (() => {
/** Section.CTA — the closing invitation. Charcoal by default. */
function CTASection({
  title = "Agenda tu valoración.",
  body,
  primaryCta = "Agendar valoración",
  secondaryCta,
  onPrimary,
  onSecondary,
  tone = "dark"
}) {
  const dark = tone === "dark";
  return /*#__PURE__*/React.createElement("section", {
    className: ["tsk-section", dark ? "tsk-section--dark tsk-on-dark" : "tsk-section--surface"].join(" ")
  }, /*#__PURE__*/React.createElement("div", {
    className: "container",
    style: {
      display: "grid",
      gap: "var(--space-8)",
      justifyItems: "start"
    }
  }, /*#__PURE__*/React.createElement("h2", {
    className: "h1",
    style: {
      maxWidth: "16ch"
    }
  }, title), body && /*#__PURE__*/React.createElement("p", {
    className: "body-l",
    style: {
      maxWidth: "var(--measure-lead)",
      color: dark ? "var(--color-text-inverse-secondary)" : "var(--color-text-secondary)"
    }
  }, body), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      flexWrap: "wrap",
      gap: "var(--space-4)"
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Button, {
    size: "lg",
    onClick: onPrimary
  }, primaryCta), secondaryCta && /*#__PURE__*/React.createElement(__ds_scope.Button, {
    size: "lg",
    variant: "secondary",
    onClick: onSecondary
  }, secondaryCta))));
}
Object.assign(__ds_scope, { CTASection });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/sections/CTASection.jsx", error: String((e && e.message) || e) }); }

// components/sections/DarkSection.jsx
try { (() => {
/** Section.Dark — charcoal manifesto / transition band. */
function DarkSection({
  children,
  compact = false,
  className = ""
}) {
  return /*#__PURE__*/React.createElement("section", {
    className: ["tsk-section", "tsk-section--dark", "tsk-on-dark", compact ? "tsk-section--compact" : "", className].filter(Boolean).join(" ")
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, children));
}
Object.assign(__ds_scope, { DarkSection });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/sections/DarkSection.jsx", error: String((e && e.message) || e) }); }

// components/sections/EditorialSplit.jsx
try { (() => {
/** Section.Editorial — 5 columns text / 1 air / 6 columns image. */
function EditorialSplit({
  eyebrow,
  title,
  children,
  cta,
  onCta,
  ctaHref,
  image,
  imageAlt = "",
  imagePlaceholder = "Fotografía 2skin",
  ratio = "landscape",
  flip = false,
  compact = false
}) {
  return /*#__PURE__*/React.createElement("section", {
    className: ["tsk-section", compact ? "tsk-section--compact" : ""].filter(Boolean).join(" ")
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement("div", {
    className: ["tsk-editorial", flip ? "tsk-editorial--flip" : ""].filter(Boolean).join(" ")
  }, /*#__PURE__*/React.createElement("div", {
    className: "tsk-editorial__text"
  }, eyebrow && /*#__PURE__*/React.createElement(__ds_scope.Eyebrow, {
    style: {
      marginBottom: "var(--space-5)"
    }
  }, eyebrow), /*#__PURE__*/React.createElement("h2", {
    className: "h2",
    style: {
      maxWidth: "18ch"
    }
  }, title), /*#__PURE__*/React.createElement("div", {
    className: "prose",
    style: {
      marginTop: "var(--space-5)",
      color: "var(--color-text-secondary)",
      display: "grid",
      gap: "var(--space-4)"
    }
  }, children), cta && /*#__PURE__*/React.createElement("div", {
    style: {
      marginTop: "var(--space-7)"
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Button, {
    variant: "text",
    href: ctaHref,
    onClick: onCta
  }, cta))), /*#__PURE__*/React.createElement("div", {
    className: "tsk-editorial__media"
  }, /*#__PURE__*/React.createElement(__ds_scope.Media, {
    src: image,
    alt: imageAlt,
    ratio: ratio,
    placeholder: imagePlaceholder
  })))));
}
Object.assign(__ds_scope, { EditorialSplit });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/sections/EditorialSplit.jsx", error: String((e && e.message) || e) }); }

// components/sections/Hero.jsx
try { (() => {
/** Section.Hero — 5 columns of copy, 7 of image. Max two CTAs, no sliders. */
function Hero({
  eyebrow,
  title,
  subtitle,
  primaryCta = "Agendar valoración",
  secondaryCta = "Conocer nuestro enfoque",
  onPrimary,
  onSecondary,
  image,
  imageAlt = "",
  imagePlaceholder = "Fotografía editorial · piel real",
  ratio = "portrait"
}) {
  return /*#__PURE__*/React.createElement("section", {
    className: "tsk-hero"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container tsk-hero__grid"
  }, /*#__PURE__*/React.createElement("div", null, eyebrow && /*#__PURE__*/React.createElement(__ds_scope.Eyebrow, {
    style: {
      marginBottom: "var(--space-5)"
    }
  }, eyebrow), /*#__PURE__*/React.createElement("h1", {
    className: "tsk-hero__title"
  }, title), subtitle && /*#__PURE__*/React.createElement("p", {
    className: "tsk-hero__sub"
  }, subtitle), /*#__PURE__*/React.createElement("div", {
    className: "tsk-hero__actions"
  }, primaryCta && /*#__PURE__*/React.createElement(__ds_scope.Button, {
    onClick: onPrimary
  }, primaryCta), secondaryCta && /*#__PURE__*/React.createElement(__ds_scope.Button, {
    variant: "secondary",
    onClick: onSecondary
  }, secondaryCta))), /*#__PURE__*/React.createElement(__ds_scope.Media, {
    src: image,
    alt: imageAlt,
    ratio: ratio,
    placeholder: imagePlaceholder
  })));
}
Object.assign(__ds_scope, { Hero });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/sections/Hero.jsx", error: String((e && e.message) || e) }); }

// components/sections/ResultsSection.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
/** Section.Results — one or two documented cases, with context and disclaimer. */
function ResultsSection({
  eyebrow = "Resultados",
  title = "Evolución documentada, caso por caso.",
  intro,
  cases = [],
  cta,
  onCta
}) {
  return /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--surface"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement("div", {
    className: "tsk-section__head"
  }, /*#__PURE__*/React.createElement(__ds_scope.Eyebrow, null, eyebrow), /*#__PURE__*/React.createElement("h2", {
    className: "h2",
    style: {
      maxWidth: "20ch"
    }
  }, title), intro && /*#__PURE__*/React.createElement("p", {
    className: "body",
    style: {
      color: "var(--color-text-secondary)",
      maxWidth: "var(--measure-lead)"
    }
  }, intro)), /*#__PURE__*/React.createElement("div", {
    className: cases.length > 1 ? "tsk-cols-2" : ""
  }, cases.map((c, i) => /*#__PURE__*/React.createElement(__ds_scope.BeforeAfter, _extends({
    key: c.context || i
  }, c)))), cta && /*#__PURE__*/React.createElement("div", {
    style: {
      marginTop: "var(--space-8)"
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Button, {
    variant: "secondary",
    onClick: onCta
  }, cta))));
}
Object.assign(__ds_scope, { ResultsSection });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/sections/ResultsSection.jsx", error: String((e && e.message) || e) }); }

// ui_kits/website/ContactScreen.jsx
try { (() => {
function ContactScreen() {
  const [sent, setSent] = React.useState(false);
  const [need, setNeed] = React.useState("");
  const submit = e => {
    e.preventDefault();
    setSent(true);
  };
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("section", {
    className: "tsk-hero",
    style: {
      paddingBottom: "var(--space-9)"
    }
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(Eyebrow, {
    style: {
      marginBottom: "var(--space-6)"
    }
  }, "Valoraci\xF3n"), /*#__PURE__*/React.createElement("h1", {
    className: "h1",
    style: {
      maxWidth: "20ch"
    }
  }, "Agenda tu valoraci\xF3n."), /*#__PURE__*/React.createElement("p", {
    className: "body-l lead",
    style: {
      marginTop: "var(--space-5)",
      color: "var(--color-text-secondary)"
    }
  }, "Cu\xE9ntanos qu\xE9 quieres mejorar. Te contactamos para coordinar una evaluaci\xF3n con criterio m\xE9dico."))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--compact",
    style: {
      paddingTop: 0
    }
  }, /*#__PURE__*/React.createElement("div", {
    className: "container",
    style: {
      display: "grid",
      gridTemplateColumns: "7fr 1fr 4fr",
      gap: 0
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      gridColumn: 1
    }
  }, sent ? /*#__PURE__*/React.createElement("div", {
    style: {
      border: "1px solid rgba(83,103,79,.28)",
      background: "var(--color-success-soft)",
      borderRadius: "var(--radius-md)",
      padding: "var(--space-8)"
    }
  }, /*#__PURE__*/React.createElement(Eyebrow, {
    style: {
      color: "var(--color-success)"
    }
  }, "Solicitud recibida"), /*#__PURE__*/React.createElement("h2", {
    className: "h3",
    style: {
      marginTop: "var(--space-4)",
      maxWidth: "24ch"
    }
  }, "Gracias. Te escribimos en las pr\xF3ximas horas h\xE1biles."), /*#__PURE__*/React.createElement("p", {
    className: "body",
    style: {
      marginTop: "var(--space-4)",
      color: "var(--color-text-secondary)",
      maxWidth: "56ch"
    }
  }, "Revisaremos lo que nos contaste", need ? ` sobre ${need.toLowerCase()}` : "", " y coordinaremos un horario para tu valoraci\xF3n."), /*#__PURE__*/React.createElement("div", {
    style: {
      marginTop: "var(--space-6)"
    }
  }, /*#__PURE__*/React.createElement(Button, {
    variant: "secondary",
    onClick: () => setSent(false)
  }, "Enviar otra solicitud"))) : /*#__PURE__*/React.createElement("form", {
    onSubmit: submit,
    style: {
      display: "grid",
      gap: "var(--space-5)",
      maxWidth: 640
    }
  }, /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-2",
    style: {
      gap: "var(--space-5)"
    }
  }, /*#__PURE__*/React.createElement(Field, {
    label: "Nombre",
    htmlFor: "c-name"
  }, /*#__PURE__*/React.createElement(Input, {
    id: "c-name",
    required: true,
    placeholder: "Nombre y apellido"
  })), /*#__PURE__*/React.createElement(Field, {
    label: "Tel\xE9fono",
    htmlFor: "c-tel",
    hint: "Coordinamos por WhatsApp."
  }, /*#__PURE__*/React.createElement(Input, {
    id: "c-tel",
    type: "tel",
    required: true,
    placeholder: "+57 300 000 0000"
  }))), /*#__PURE__*/React.createElement(Field, {
    label: "Email",
    htmlFor: "c-mail",
    optional: true
  }, /*#__PURE__*/React.createElement(Input, {
    id: "c-mail",
    type: "email",
    placeholder: "nombre@correo.com"
  })), /*#__PURE__*/React.createElement(Field, {
    label: "Necesidad principal",
    htmlFor: "c-need"
  }, /*#__PURE__*/React.createElement(Select, {
    id: "c-need",
    placeholder: "Elige una opci\xF3n",
    value: need,
    onChange: e => setNeed(e.target.value),
    options: ["Calidad de piel", "Firmeza", "Líneas de expresión", "Hidratación", "Manchas", "Aún no lo sé"]
  })), /*#__PURE__*/React.createElement(Field, {
    label: "\xBFQu\xE9 te gustar\xEDa mejorar?",
    htmlFor: "c-ctx",
    optional: true
  }, /*#__PURE__*/React.createElement(Textarea, {
    id: "c-ctx",
    placeholder: "Cu\xE9ntanos brevemente qu\xE9 has notado en tu piel."
  })), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    className: "tsk-field__label"
  }, "Contacto preferido"), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      gap: "var(--space-6)"
    }
  }, /*#__PURE__*/React.createElement(Checkbox, {
    name: "pref",
    type: "radio",
    label: "WhatsApp",
    defaultChecked: true
  }), /*#__PURE__*/React.createElement(Checkbox, {
    name: "pref",
    type: "radio",
    label: "Llamada"
  }), /*#__PURE__*/React.createElement(Checkbox, {
    name: "pref",
    type: "radio",
    label: "Email"
  }))), /*#__PURE__*/React.createElement(Checkbox, {
    required: true,
    label: "Autorizo el tratamiento de mis datos para coordinar la valoraci\xF3n."
  }), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(Button, {
    type: "submit",
    size: "lg"
  }, "Agendar valoraci\xF3n")))), /*#__PURE__*/React.createElement("aside", {
    style: {
      gridColumn: 3,
      display: "grid",
      gap: "var(--space-7)",
      alignContent: "start"
    }
  }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(Eyebrow, null, "WhatsApp"), /*#__PURE__*/React.createElement("p", {
    className: "body",
    style: {
      marginTop: "var(--space-3)"
    }
  }, "+57 300 000 0000"), /*#__PURE__*/React.createElement("div", {
    style: {
      marginTop: "var(--space-3)"
    }
  }, /*#__PURE__*/React.createElement(Button, {
    variant: "text"
  }, "Escribir por WhatsApp"))), /*#__PURE__*/React.createElement("div", {
    style: {
      borderTop: "var(--border-subtle)",
      paddingTop: "var(--space-5)"
    }
  }, /*#__PURE__*/React.createElement(Eyebrow, null, "Ubicaci\xF3n"), /*#__PURE__*/React.createElement("p", {
    className: "body",
    style: {
      marginTop: "var(--space-3)",
      color: "var(--color-text-secondary)"
    }
  }, "Bogot\xE1, Colombia", /*#__PURE__*/React.createElement("br", null), "Direcci\xF3n por confirmar")), /*#__PURE__*/React.createElement("div", {
    style: {
      borderTop: "var(--border-subtle)",
      paddingTop: "var(--space-5)"
    }
  }, /*#__PURE__*/React.createElement(Eyebrow, null, "Horario"), /*#__PURE__*/React.createElement("p", {
    className: "body",
    style: {
      marginTop: "var(--space-3)",
      color: "var(--color-text-secondary)"
    }
  }, "Lunes a viernes \xB7 9:00\u201318:00", /*#__PURE__*/React.createElement("br", null), "S\xE1bados \xB7 9:00\u201313:00"))))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--surface"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Qu\xE9 esperar",
    title: "Qu\xE9 esperar de la valoraci\xF3n."
  }), /*#__PURE__*/React.createElement(Steps, {
    items: [{
      title: "45 minutos",
      body: "Lectura de piel, historia clínica y objetivos, sin prisa."
    }, {
      title: "Explicación clara",
      body: "Qué tiene sentido en tu caso, qué no, y por qué."
    }, {
      title: "Sin compromiso",
      body: "Sales con un protocolo propuesto, no con una obligación."
    }]
  }))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--compact"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container tsk-cols-2",
    style: {
      gap: "var(--space-9)",
      alignItems: "start"
    }
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Preguntas frecuentes",
    title: "Antes de agendar."
  }), /*#__PURE__*/React.createElement(Accordion, {
    items: FAQ.slice(0, 3)
  }))));
}
Object.assign(window, {
  ContactScreen
});
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/website/ContactScreen.jsx", error: String((e && e.message) || e) }); }

// ui_kits/website/HomeScreen.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
function HomeScreen({
  go
}) {
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(Hero, {
    eyebrow: "Medicina est\xE9tica \xB7 Bogot\xE1",
    title: "Medicina est\xE9tica con criterio.",
    subtitle: "Protocolos dise\xF1ados para mejorar la calidad de tu piel preservando la naturalidad.",
    onPrimary: () => go("Contacto"),
    onSecondary: () => go("Nosotros"),
    imagePlaceholder: "Retrato editorial \xB7 piel real, luz suave"
  }), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--compact"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(Quote, {
    cite: "M\xE9todo 2skin"
  }, "No hacemos m\xE1s. Hacemos lo que tu piel necesita."))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--surface"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Necesidades",
    title: "Empecemos por lo que quieres mejorar.",
    intro: "Cada necesidad se eval\xFAa antes de proponer un tratamiento."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-2",
    style: {
      gap: "var(--space-9)"
    }
  }, /*#__PURE__*/React.createElement("div", null, NEEDS.slice(0, 3).map(n => /*#__PURE__*/React.createElement(NeedCard, _extends({
    key: n.need
  }, n, {
    href: "#"
  })))), /*#__PURE__*/React.createElement("div", null, NEEDS.slice(3).map(n => /*#__PURE__*/React.createElement(NeedCard, _extends({
    key: n.need
  }, n, {
    href: "#"
  }))), /*#__PURE__*/React.createElement("div", {
    style: {
      marginTop: "var(--space-6)"
    }
  }, /*#__PURE__*/React.createElement(Button, {
    variant: "text",
    onClick: () => go("Calidad de piel")
  }, "Ver c\xF3mo lo evaluamos")))))), /*#__PURE__*/React.createElement(EditorialSplit, {
    eyebrow: "M\xE9todo 2skin",
    title: "Primero evaluamos. Despu\xE9s proponemos.",
    cta: "Qu\xE9 evaluamos",
    onCta: () => go("Calidad de piel"),
    imagePlaceholder: "Valoraci\xF3n m\xE9dica \xB7 conversaci\xF3n"
  }, /*#__PURE__*/React.createElement("p", null, "Cada piel llega con una historia distinta. Antes de indicar un tratamiento revisamos calidad de piel, antecedentes, h\xE1bitos y objetivos."), /*#__PURE__*/React.createElement("p", null, "El resultado de esa lectura es un protocolo propio: lo que tiene sentido en tu caso, en qu\xE9 orden y con qu\xE9 seguimiento.")), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Tratamientos",
    title: "Tratamientos destacados."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-3"
  }, TREATMENTS.map(t => /*#__PURE__*/React.createElement("div", {
    key: t.name,
    onClick: () => go("Tratamiento"),
    style: {
      cursor: "pointer"
    }
  }, /*#__PURE__*/React.createElement(TreatmentCard, _extends({}, t, {
    href: "#"
  }))))))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--surface"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Resultados",
    title: "Evoluci\xF3n documentada, caso por caso.",
    intro: "Mismo encuadre, misma luz, misma edici\xF3n. Sin filtros que exageren diferencias."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-2"
  }, /*#__PURE__*/React.createElement(BeforeAfter, {
    context: "Mujer, 41 a\xF1os \xB7 calidad de piel",
    protocol: "Bioestimulaci\xF3n + skinboosters",
    elapsed: "4 meses de evoluci\xF3n"
  }), /*#__PURE__*/React.createElement(BeforeAfter, {
    context: "Mujer, 36 a\xF1os \xB7 textura y poro",
    protocol: "Radiofrecuencia microaguja \xB7 3 sesiones",
    elapsed: "6 meses de evoluci\xF3n",
    defaultPosition: 62
  })), /*#__PURE__*/React.createElement("div", {
    style: {
      marginTop: "var(--space-8)"
    }
  }, /*#__PURE__*/React.createElement(Button, {
    variant: "secondary",
    onClick: () => go("Tratamiento")
  }, "Conocer el caso")))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Equipo",
    title: "Criterio m\xE9dico, no cat\xE1logo."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-3"
  }, /*#__PURE__*/React.createElement(DoctorCard, {
    name: "Dra. Camila Herrera",
    role: "Medicina est\xE9tica",
    credentials: "Formaci\xF3n en bioestimulaci\xF3n y calidad de piel."
  }), /*#__PURE__*/React.createElement(DoctorCard, {
    name: "Dr. Andr\xE9s Molina",
    role: "Medicina est\xE9tica",
    credentials: "Enfoque en protocolos combinados y seguimiento."
  }), /*#__PURE__*/React.createElement(DoctorCard, {
    name: "Laura Ospina",
    role: "Coordinaci\xF3n cl\xEDnica",
    credentials: "Acompa\xF1amiento y seguimiento de protocolos."
  })))), /*#__PURE__*/React.createElement(DarkSection, null, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "grid",
      gap: "var(--space-8)",
      gridTemplateColumns: "1fr 1fr",
      alignItems: "end"
    }
  }, /*#__PURE__*/React.createElement(Quote, {
    cite: "Manifiesto"
  }, "La piel no se transforma. Se acompa\xF1a."), /*#__PURE__*/React.createElement("p", {
    className: "body-l",
    style: {
      color: "var(--color-text-inverse-secondary)",
      maxWidth: "44ch"
    }
  }, "Trabajamos con lo que tu piel puede sostener en el tiempo. Eso significa decir no cuando un tratamiento no est\xE1 indicado."))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Educaci\xF3n",
    title: "Entender antes de decidir."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-3"
  }, /*#__PURE__*/React.createElement(ArticleCard, {
    category: "Calidad de piel",
    readingTime: "4 min de lectura",
    title: "Qu\xE9 significa realmente mejorar la calidad de la piel"
  }), /*#__PURE__*/React.createElement(ArticleCard, {
    category: "Criterio m\xE9dico",
    readingTime: "6 min de lectura",
    title: "Por qu\xE9 no todos los tratamientos sirven para todas las pieles"
  }), /*#__PURE__*/React.createElement(ArticleCard, {
    category: "Proceso",
    readingTime: "3 min de lectura",
    title: "C\xF3mo es una valoraci\xF3n en 2skin, paso a paso"
  })))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--surface"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Testimonios",
    title: "Lo que cuentan nuestras pacientes."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-3"
  }, /*#__PURE__*/React.createElement(TestimonialCard, {
    quote: "Me explicaron qu\xE9 s\xED y qu\xE9 no ten\xEDa sentido en mi caso. Sal\xED entendiendo mi piel.",
    author: "Valentina R.",
    context: "Protocolo de calidad de piel \xB7 6 meses"
  }), /*#__PURE__*/React.createElement(TestimonialCard, {
    quote: "Es la primera vez que alguien me dice que espere en lugar de venderme algo.",
    author: "Mariana G.",
    context: "Valoraci\xF3n inicial"
  }), /*#__PURE__*/React.createElement(TestimonialCard, {
    quote: "El seguimiento fue lo que m\xE1s me sorprendi\xF3: revisamos cada etapa.",
    author: "Daniela P.",
    context: "Bioestimulaci\xF3n \xB7 8 meses"
  })))), /*#__PURE__*/React.createElement(CTASection, {
    title: "Agenda tu valoraci\xF3n.",
    body: "Evaluamos tu piel y te explicamos qu\xE9 tiene sentido en tu caso \u2014 y qu\xE9 no.",
    secondaryCta: "Escribir por WhatsApp",
    onPrimary: () => go("Contacto"),
    onSecondary: () => go("Contacto")
  }));
}
Object.assign(window, {
  HomeScreen
});
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/website/HomeScreen.jsx", error: String((e && e.message) || e) }); }

// ui_kits/website/SkinQualityScreen.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
function SkinQualityScreen({
  go
}) {
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(Hero, {
    eyebrow: "Calidad de piel",
    title: "La calidad de la piel es el tratamiento.",
    subtitle: "Antes de pensar en volumen o l\xEDneas, trabajamos la salud, la textura y el comportamiento de tu piel.",
    primaryCta: "Agendar valoraci\xF3n",
    secondaryCta: "Ver c\xF3mo lo evaluamos",
    onPrimary: () => go("Contacto"),
    ratio: "landscape",
    imagePlaceholder: "Piel real \xB7 textura visible, luz suave"
  }), /*#__PURE__*/React.createElement(EditorialSplit, {
    eyebrow: "Definici\xF3n",
    title: "Qu\xE9 significa calidad de piel.",
    imagePlaceholder: "Detalle de piel \xB7 macro editorial",
    flip: true
  }, /*#__PURE__*/React.createElement("p", null, "No es una piel sin poros ni una piel sin edad. Es una piel hidratada, firme, uniforme en tono y estable en su comportamiento."), /*#__PURE__*/React.createElement("p", null, "Es tambi\xE9n la base sobre la que cualquier otro tratamiento funciona mejor y dura m\xE1s.")), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--surface"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Se\xF1ales",
    title: "Se\xF1ales que solemos evaluar."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-2",
    style: {
      gap: "var(--space-9)"
    }
  }, /*#__PURE__*/React.createElement("div", null, ["Textura irregular", "Poro visible", "Falta de luminosidad"].map(n => /*#__PURE__*/React.createElement(NeedCard, {
    key: n,
    need: n,
    href: "#"
  }))), /*#__PURE__*/React.createElement("div", null, ["Deshidratación persistente", "Tono desigual", "Pérdida de firmeza temprana"].map(n => /*#__PURE__*/React.createElement(NeedCard, {
    key: n,
    need: n,
    href: "#"
  })))))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Evaluaci\xF3n",
    title: "C\xF3mo eval\xFAa 2skin.",
    intro: "Cuatro lecturas que hacemos en la valoraci\xF3n, siempre en el mismo orden."
  }), /*#__PURE__*/React.createElement(Steps, {
    items: [{
      title: "Lectura de piel",
      body: "Hidratación, textura, poro, tono y respuesta inflamatoria."
    }, {
      title: "Historia y hábitos",
      body: "Rutina, fotoexposición, tratamientos previos y antecedentes."
    }, {
      title: "Objetivo real",
      body: "Qué quieres mejorar y en qué plazo puede sostenerse."
    }]
  }))), /*#__PURE__*/React.createElement(DarkSection, {
    compact: true
  }, /*#__PURE__*/React.createElement(Quote, {
    cite: "Calidad de piel"
  }, "Una piel sana envejece mejor que una piel tratada de m\xE1s.")), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Protocolos",
    title: "Protocolos personalizados."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-3"
  }, TREATMENTS.map(t => /*#__PURE__*/React.createElement("div", {
    key: t.name,
    onClick: () => go("Tratamiento"),
    style: {
      cursor: "pointer"
    }
  }, /*#__PURE__*/React.createElement(TreatmentCard, _extends({}, t, {
    href: "#"
  }))))))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--surface"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Tecnolog\xEDa",
    title: "Equipos al servicio del criterio.",
    intro: "La tecnolog\xEDa no define el protocolo: el diagn\xF3stico s\xED."
  }), /*#__PURE__*/React.createElement(Gallery, {
    columns: 3,
    items: [{
      caption: "Radiofrecuencia microaguja"
    }, {
      caption: "Análisis de piel"
    }, {
      caption: "Espacio clínico"
    }]
  }))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Casos",
    title: "Resultados de calidad de piel."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-2"
  }, /*#__PURE__*/React.createElement(BeforeAfter, {
    context: "Mujer, 36 a\xF1os \xB7 textura y poro",
    protocol: "Skinboosters \xB7 3 sesiones",
    elapsed: "5 meses de evoluci\xF3n"
  }), /*#__PURE__*/React.createElement(BeforeAfter, {
    context: "Mujer, 44 a\xF1os \xB7 luminosidad",
    protocol: "Protocolo combinado",
    elapsed: "7 meses de evoluci\xF3n",
    defaultPosition: 58
  })))), /*#__PURE__*/React.createElement(CTASection, {
    title: "Empecemos por tu piel.",
    body: "Agenda una valoraci\xF3n y define un punto de partida real.",
    onPrimary: () => go("Contacto")
  }));
}
function AboutScreen({
  go
}) {
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("section", {
    className: "tsk-hero"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(Eyebrow, {
    style: {
      marginBottom: "var(--space-6)"
    }
  }, "Sobre 2skin"), /*#__PURE__*/React.createElement("h1", {
    className: "display-l",
    style: {
      maxWidth: "18ch"
    }
  }, "Una cl\xEDnica construida alrededor del criterio."), /*#__PURE__*/React.createElement("p", {
    className: "body-l lead",
    style: {
      marginTop: "var(--space-6)",
      color: "var(--color-text-secondary)"
    }
  }, "2skin nace de una idea simple: la medicina est\xE9tica deber\xEDa explicarse antes de aplicarse.")), /*#__PURE__*/React.createElement("div", {
    className: "container",
    style: {
      marginTop: "var(--space-10)"
    }
  }, /*#__PURE__*/React.createElement(Media, {
    ratio: "wide",
    placeholder: "Espacio cl\xEDnico \xB7 fotograf\xEDa editorial"
  }))), /*#__PURE__*/React.createElement(EditorialSplit, {
    eyebrow: "Filosof\xEDa",
    title: "Naturalidad no es hacer menos. Es hacer lo correcto.",
    imagePlaceholder: "Equipo trabajando"
  }, /*#__PURE__*/React.createElement("p", null, "Trabajamos con protocolos que respetan la anatom\xEDa y el tiempo de cada piel. Nada de resultados forzados ni de tratamientos aplicados por tendencia."), /*#__PURE__*/React.createElement("p", null, "Cada indicaci\xF3n se sostiene en una evaluaci\xF3n y se revisa en el seguimiento.")), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--surface"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Equipo m\xE9dico",
    title: "Qui\xE9n te va a evaluar."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-3"
  }, /*#__PURE__*/React.createElement(DoctorCard, {
    name: "Dra. Camila Herrera",
    role: "Medicina est\xE9tica",
    credentials: "Formaci\xF3n en bioestimulaci\xF3n y calidad de piel."
  }), /*#__PURE__*/React.createElement(DoctorCard, {
    name: "Dr. Andr\xE9s Molina",
    role: "Medicina est\xE9tica",
    credentials: "Enfoque en protocolos combinados y seguimiento."
  }), /*#__PURE__*/React.createElement(DoctorCard, {
    name: "Laura Ospina",
    role: "Coordinaci\xF3n cl\xEDnica",
    credentials: "Acompa\xF1amiento y seguimiento de protocolos."
  })))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "M\xE9todo",
    title: "El m\xE9todo, en tres tiempos."
  }), /*#__PURE__*/React.createElement(Steps, {
    items: [{
      title: "Evaluar",
      body: "Leer la piel antes de proponer cualquier procedimiento."
    }, {
      title: "Proponer",
      body: "Un protocolo propio, explicado en lenguaje claro."
    }, {
      title: "Acompañar",
      body: "Seguimiento documentado y ajustes cuando hacen falta."
    }]
  }))), /*#__PURE__*/React.createElement(DarkSection, {
    compact: true
  }, /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-2",
    style: {
      gap: "var(--space-9)",
      alignItems: "center"
    }
  }, /*#__PURE__*/React.createElement(Quote, {
    cite: "Valores"
  }, "Criterio, naturalidad, seguimiento."), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "grid",
      gap: "var(--space-4)",
      color: "var(--color-text-inverse-secondary)"
    }
  }, /*#__PURE__*/React.createElement("p", null, "Decimos no cuando un tratamiento no est\xE1 indicado."), /*#__PURE__*/React.createElement("p", null, "Explicamos el porqu\xE9 de cada paso."), /*#__PURE__*/React.createElement("p", null, "Documentamos la evoluci\xF3n y la revisamos contigo.")))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Espacio",
    title: "Bogot\xE1."
  }), /*#__PURE__*/React.createElement(Gallery, {
    columns: 3,
    items: [{
      caption: "Recepción"
    }, {
      caption: "Consultorio"
    }, {
      caption: "Materiales"
    }]
  }))), /*#__PURE__*/React.createElement(CTASection, {
    title: "Conoce el enfoque en persona.",
    body: "La valoraci\xF3n es el primer paso, y no compromete a ning\xFAn tratamiento.",
    onPrimary: () => go("Contacto")
  }));
}
Object.assign(window, {
  SkinQualityScreen,
  AboutScreen
});
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/website/SkinQualityScreen.jsx", error: String((e && e.message) || e) }); }

// ui_kits/website/TreatmentScreen.jsx
try { (() => {
function TreatmentScreen({
  go
}) {
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("section", {
    className: "tsk-hero"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container tsk-hero__grid"
  }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      gap: "var(--space-2)",
      marginBottom: "var(--space-5)"
    }
  }, /*#__PURE__*/React.createElement(Badge, null, "Bioestimulaci\xF3n"), /*#__PURE__*/React.createElement(Badge, null, "Inyectable")), /*#__PURE__*/React.createElement("h1", {
    className: "tsk-hero__title",
    style: {
      maxWidth: "16ch"
    }
  }, "Bioestimuladores de col\xE1geno"), /*#__PURE__*/React.createElement("p", {
    className: "tsk-hero__sub"
  }, "Estimulan la producci\xF3n propia de col\xE1geno para mejorar firmeza y calidad de piel de forma gradual."), /*#__PURE__*/React.createElement("div", {
    className: "tsk-hero__actions"
  }, /*#__PURE__*/React.createElement(Button, {
    onClick: () => go("Contacto")
  }, "Agendar valoraci\xF3n"), /*#__PURE__*/React.createElement(Button, {
    variant: "text",
    href: "#faq"
  }, "Ver preguntas frecuentes"))), /*#__PURE__*/React.createElement(Media, {
    ratio: "landscape",
    placeholder: "Detalle de manos \xB7 preparaci\xF3n"
  }))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--surface tsk-section--compact"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container tsk-cols-2",
    style: {
      gap: "var(--space-9)"
    }
  }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(Eyebrow, null, "Qu\xE9 puede trabajar"), /*#__PURE__*/React.createElement("ul", {
    style: {
      margin: "var(--space-5) 0 0",
      padding: 0,
      listStyle: "none",
      display: "grid",
      gap: "var(--space-3)"
    }
  }, ["Firmeza y sostén", "Calidad y grosor de la piel", "Textura general", "Pérdida gradual de volumen"].map(t => /*#__PURE__*/React.createElement("li", {
    key: t,
    style: {
      borderTop: "var(--border-subtle)",
      paddingTop: "var(--space-3)",
      fontSize: "1.0625rem"
    }
  }, t)))), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(Eyebrow, null, "C\xF3mo funciona"), /*#__PURE__*/React.createElement("p", {
    className: "body",
    style: {
      marginTop: "var(--space-5)",
      color: "var(--color-text-secondary)"
    }
  }, "El producto act\xFAa como est\xEDmulo: no rellena, induce a la piel a producir col\xE1geno propio. El cambio es progresivo y se eval\xFAa en el seguimiento, sesi\xF3n por sesi\xF3n.")))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Indicaci\xF3n",
    title: "Para qui\xE9n puede estar indicado.",
    intro: "No es un tratamiento universal. Estas son las condiciones que revisamos antes de proponerlo."
  }), /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-2",
    style: {
      gap: "var(--space-9)"
    }
  }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    className: "tsk-field__label",
    style: {
      marginBottom: "var(--space-4)"
    }
  }, "Puede tener sentido si"), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "grid",
      gap: "var(--space-3)",
      color: "var(--color-text-secondary)"
    }
  }, /*#__PURE__*/React.createElement("p", null, "Notas p\xE9rdida de firmeza progresiva."), /*#__PURE__*/React.createElement("p", null, "Buscas un cambio gradual y natural."), /*#__PURE__*/React.createElement("p", null, "Puedes sostener un protocolo con seguimiento."))), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    className: "tsk-field__label",
    style: {
      marginBottom: "var(--space-4)"
    }
  }, "Probablemente no, si"), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "grid",
      gap: "var(--space-3)",
      color: "var(--color-text-secondary)"
    }
  }, /*#__PURE__*/React.createElement("p", null, "Buscas un resultado inmediato para una fecha pr\xF3xima."), /*#__PURE__*/React.createElement("p", null, "Hay procesos inflamatorios activos en la piel."), /*#__PURE__*/React.createElement("p", null, "La necesidad principal es hidrataci\xF3n o manchas.")))))), /*#__PURE__*/React.createElement(DarkSection, {
    compact: true
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "grid",
      gap: "var(--space-6)",
      maxWidth: "60ch"
    }
  }, /*#__PURE__*/React.createElement(Eyebrow, null, "Qu\xE9 eval\xFAa 2skin antes de recomendarlo"), /*#__PURE__*/React.createElement("h2", {
    className: "h2"
  }, "Calidad de piel, antecedentes, h\xE1bitos y expectativas."), /*#__PURE__*/React.createElement("p", {
    className: "body-l",
    style: {
      color: "var(--color-text-inverse-secondary)"
    }
  }, "Si la evaluaci\xF3n indica que otro protocolo es m\xE1s adecuado, lo decimos. El criterio m\xE9dico est\xE1 antes del tratamiento."))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Proceso",
    title: "C\xF3mo es el proceso."
  }), /*#__PURE__*/React.createElement(Steps, {
    items: [{
      title: "Valoración médica",
      body: "Lectura de piel, historia clínica y objetivos. Sin compromiso de tratamiento."
    }, {
      title: "Protocolo propuesto",
      body: "Qué haríamos, en qué orden, con qué tiempos y qué esperar de cada etapa."
    }, {
      title: "Sesiones y seguimiento",
      body: "Aplicación, indicaciones de cuidado y control de evolución documentado."
    }]
  }))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section tsk-section--surface"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container"
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Resultados",
    title: "Resultados de este caso."
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 760
    }
  }, /*#__PURE__*/React.createElement(BeforeAfter, {
    context: "Mujer, 41 a\xF1os \xB7 firmeza y calidad de piel",
    protocol: "Bioestimulaci\xF3n \xB7 2 sesiones",
    elapsed: "4 meses de evoluci\xF3n"
  })))), /*#__PURE__*/React.createElement("section", {
    className: "tsk-section",
    id: "faq"
  }, /*#__PURE__*/React.createElement("div", {
    className: "container tsk-cols-2",
    style: {
      gap: "var(--space-9)",
      alignItems: "start"
    }
  }, /*#__PURE__*/React.createElement(SectionHead, {
    eyebrow: "Preguntas frecuentes",
    title: "Resolver una duda."
  }), /*#__PURE__*/React.createElement(Accordion, {
    items: FAQ,
    defaultOpen: 0
  }))), /*#__PURE__*/React.createElement(CTASection, {
    title: "\xBFTiene sentido en tu caso?",
    body: "La valoraci\xF3n existe para responder exactamente eso.",
    onPrimary: () => go("Contacto")
  }));
}
Object.assign(window, {
  TreatmentScreen
});
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/website/TreatmentScreen.jsx", error: String((e && e.message) || e) }); }

// ui_kits/website/shared.jsx
try { (() => {
const {
  Hero,
  EditorialSplit,
  DarkSection,
  CTASection,
  Quote,
  Eyebrow,
  Button,
  NeedCard,
  TreatmentCard,
  DoctorCard,
  TestimonialCard,
  ArticleCard,
  BeforeAfter,
  Gallery,
  Accordion,
  Badge,
  Media,
  Field,
  Input,
  Select,
  Textarea,
  Checkbox
} = window.Ds2skinDesignSystem_023a03;
const NEEDS = [{
  need: "Calidad de piel",
  note: "Textura, poro, luminosidad."
}, {
  need: "Firmeza",
  note: "Pérdida de sostén y definición."
}, {
  need: "Líneas de expresión",
  note: "Movimiento y marcas tempranas."
}, {
  need: "Hidratación",
  note: "Piel apagada o deshidratada."
}, {
  need: "Manchas",
  note: "Tono desigual y melasma."
}];
const TREATMENTS = [{
  category: "Bioestimulación",
  name: "Bioestimuladores de colágeno",
  description: "Estimula la producción propia de colágeno para mejorar firmeza y calidad de piel.",
  badges: ["Inyectable"]
}, {
  category: "Calidad de piel",
  name: "Protocolo de skinboosters",
  description: "Hidratación profunda y mejora de textura en sesiones planificadas.",
  badges: ["Inyectable", "Calidad de piel"]
}, {
  category: "Tecnología",
  name: "Radiofrecuencia microaguja",
  description: "Trabaja textura, poro y firmeza desde las capas profundas de la piel.",
  badges: ["Tecnología"]
}];
const FAQ = [{
  question: "¿Cuánto dura el resultado?",
  answer: "Depende del protocolo, de tu piel y de tus hábitos. En la valoración te damos un rango realista y lo revisamos en el seguimiento."
}, {
  question: "¿Es doloroso?",
  answer: "Usamos anestesia tópica cuando el procedimiento lo requiere. La mayoría de pacientes describe una molestia leve y tolerable."
}, {
  question: "¿Qué evalúan antes de recomendarlo?",
  answer: "Calidad de piel, antecedentes médicos, hábitos, expectativas y tratamientos previos. Si algo no está indicado en tu caso, te lo decimos."
}, {
  question: "¿Puedo volver a mi rutina el mismo día?",
  answer: "En la mayoría de protocolos sí, con indicaciones específicas de cuidado para las primeras 48 horas."
}];
function SectionHead({
  eyebrow,
  title,
  intro,
  wide
}) {
  return /*#__PURE__*/React.createElement("div", {
    className: "tsk-section__head",
    style: wide ? {
      maxWidth: "none"
    } : undefined
  }, eyebrow && /*#__PURE__*/React.createElement(Eyebrow, null, eyebrow), /*#__PURE__*/React.createElement("h2", {
    className: "h2",
    style: {
      maxWidth: "22ch"
    }
  }, title), intro && /*#__PURE__*/React.createElement("p", {
    className: "body",
    style: {
      color: "var(--color-text-secondary)",
      maxWidth: "var(--measure-lead)"
    }
  }, intro));
}
function Steps({
  items
}) {
  return /*#__PURE__*/React.createElement("div", {
    className: "tsk-cols-3"
  }, items.map((s, i) => /*#__PURE__*/React.createElement("div", {
    key: s.title,
    style: {
      borderTop: "var(--border-subtle)",
      paddingTop: "var(--space-5)"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "var(--font-display)",
      fontSize: "2.5rem",
      lineHeight: 1,
      color: "var(--color-accent-strong)"
    }
  }, String(i + 1).padStart(2, "0")), /*#__PURE__*/React.createElement("h3", {
    className: "h4",
    style: {
      marginTop: "var(--space-4)"
    }
  }, s.title), /*#__PURE__*/React.createElement("p", {
    className: "body-s",
    style: {
      marginTop: "var(--space-3)",
      color: "var(--color-text-secondary)"
    }
  }, s.body))));
}
Object.assign(window, {
  NEEDS,
  TREATMENTS,
  FAQ,
  SectionHead,
  Steps
});
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/website/shared.jsx", error: String((e && e.message) || e) }); }

__ds_ns.Button = __ds_scope.Button;

__ds_ns.TextLink = __ds_scope.TextLink;

__ds_ns.DoctorCard = __ds_scope.DoctorCard;

__ds_ns.NeedCard = __ds_scope.NeedCard;

__ds_ns.TestimonialCard = __ds_scope.TestimonialCard;

__ds_ns.TreatmentCard = __ds_scope.TreatmentCard;

__ds_ns.Accordion = __ds_scope.Accordion;

__ds_ns.ArticleCard = __ds_scope.ArticleCard;

__ds_ns.Quote = __ds_scope.Quote;

__ds_ns.Checkbox = __ds_scope.Checkbox;

__ds_ns.Field = __ds_scope.Field;

__ds_ns.Input = __ds_scope.Input;

__ds_ns.Select = __ds_scope.Select;

__ds_ns.Textarea = __ds_scope.Textarea;

__ds_ns.BeforeAfter = __ds_scope.BeforeAfter;

__ds_ns.Gallery = __ds_scope.Gallery;

__ds_ns.Footer = __ds_scope.Footer;

__ds_ns.Header = __ds_scope.Header;

__ds_ns.MobileNav = __ds_scope.MobileNav;

__ds_ns.Badge = __ds_scope.Badge;

__ds_ns.Eyebrow = __ds_scope.Eyebrow;

__ds_ns.Media = __ds_scope.Media;

__ds_ns.CTASection = __ds_scope.CTASection;

__ds_ns.DarkSection = __ds_scope.DarkSection;

__ds_ns.EditorialSplit = __ds_scope.EditorialSplit;

__ds_ns.Hero = __ds_scope.Hero;

__ds_ns.ResultsSection = __ds_scope.ResultsSection;

})();
