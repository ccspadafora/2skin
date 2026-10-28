# 2skin — Design System

**Brand:** 2skin · Medical Aesthetics (Bogotá, Colombia) · Spanish-language site
**Version:** 1.0 · derived from the approved *2skin Web Design System v1.0* brand document
**Positioning:** medicina estética con criterio — clinical clarity + editorial elegance + generous whitespace

## Sources given to me

| Source | What it contained |
|---|---|
| `uploads/ChatGPT Image Sep 7, 2026, 02_57_19 PM.png` (copied to `assets/source-logo.png`) | The approved 2skin lockup: 2S monogram + `2SKIN` wordmark + `MEDICAL AESTHETICS` descriptor, charcoal on cream. Raster only — **no vector logo was supplied.** |
| Pasted brand document *"2skin — Web Design System v1.0"* | Full specification: principles, palette, type scale, spacing, layout, radii, borders, shadows, buttons, forms, navigation, hero, sections, cards, treatment-page order, before/after rules, testimonials, FAQ, badges, iconography direction, photography direction, motion, accessibility, responsive rules, content widths, page templates, UI copy, CTA hierarchy, empty/loading states, footer, token reference, component naming, QA checklist, do/don't, north star. |

No codebase, Figma file or slide deck was provided. Every value here comes from that document; where it is silent I followed its North Star (clearer → soberer → more editorial → easier to trust) and flagged the decision below.

## Products / surfaces

One product: the **2skin marketing website** (`ui_kits/website/`) — home, treatment detail, calidad de piel landing, about, contact/valoración. There is no app, dashboard or docs surface in the brief.

---

## CONTENT FUNDAMENTALS

**Language.** Spanish (Colombia), formal-but-warm **tú**. Sentence case everywhere; uppercase only for eyebrows, labels and the wordmark. No exclamation marks. **No emoji, ever** — not in UI, not in empty states.

**Voice.** Medical authority expressed as clarity, not jargon. Short declaratives. The brand states what it evaluates and what it will not promise.

- Titles: *"Medicina estética con criterio."* · *"Primero evaluamos. Después proponemos."*
- Lead: *"Protocolos diseñados para mejorar la calidad de tu piel preservando la naturalidad."*
- Manifesto quote: *"No hacemos más. Hacemos lo que tu piel necesita."*
- Empty state: *"No encontramos resultados con estos filtros. Puedes ajustar tu búsqueda o explorar todos los tratamientos."* — never *"Ups, no encontramos nada 😢"*

**CTA vocabulary (use verbatim).** Primary: **Agendar valoración**. Secondary: **Conocer nuestro enfoque**. Contextual: Ver tratamiento · Conocer el caso · Resolver una duda · Ver preguntas frecuentes · Conocer al equipo. Editorial: Leer más · Entender el proceso · Ver cómo lo evaluamos.

**Forbidden copy.** "Compra ahora", "Aprovecha", "Transforma tu rostro", "Resultados garantizados", "Borra las arrugas", "La solución definitiva", "Últimos cupos" (unless a real limit exists). No treatment is ever framed as universal.

**Rhythm.** Paragraphs max 65–72 characters per line (`.prose` 68ch, `.lead` 54ch); 1–3 sentences per block. Every results claim carries context (protocol, elapsed time) and the variability disclaimer.

---

## VISUAL FOUNDATIONS

**Palette.** Four brand colours only: Soft Cream `#F6F4F0` (page), white `#FFFFFF` (interior surfaces), Charcoal `#1E1E1E` (type, buttons, dark bands), Sage Green `#A6B2A0` (accent), Silver Gray `#D7D9D3` (borders, media placeholders). Distribution 60/25/10/5. Sage never becomes a large repeating field. Semantic tones are deliberately dull: error `#8A3B34`, success `#53674F`. No blue, no pink, no gold, no gradients.

**Type.** EB Garamond 400 for display/H1–H4/quotes/large numbers; Inter 400–600 for everything functional. Desktop 80/64/56/44/32/26 display; 20/17/15 body; 13 label, 12 caption. Regular weights — bold is close to absent; emphasis comes from size and space. Labels get .05em tracking, eyebrows .14em. Serif never sets long paragraphs.

**Spacing & layout.** 4px base, tokens `space-1`…`space-13`. Sections: hero 120–160px, standard 96–120px, compact 64–80px (desktop) → 80–96 / 64–80 / 48–64 mobile. Container `min(100% − 48px, 1280px)`, 12/8/4 columns with 24/20/16px gutters. Controlled asymmetry: hero 5/7, editorial split 5 / 1 air / 6.

**Backgrounds.** Flat cream and flat white — no textures, no patterns, no decorative gradients. Charcoal full-bleed bands are the only "dramatic" ground and appear once or twice per page (manifesto, high-intent CTA, footer). Photography does the atmospheric work, not the background.

**Imagery.** Real adult skin with visible texture, soft daylight, natural expressions, editorial framing; warm-neutral grade matching cream, never cool or clinical-blue, no plastic retouching, no generic stock beauty. Clinic imagery favours hands, preparation, evaluation, instruments, materials, space. Invasive procedure imagery is never a hero. Ratios 1:1 / 3:4 / 4:3 / 16:9 with 0–8px corners. Until real photography arrives, `Media` renders a silver placeholder that names what belongs there.

**Cards.** Mostly *not* cards: hairline rows and bare image+text stacks. When a container is needed: white, 1px `rgba(30,30,30,.12)` border, 8px radius, 32px padding, no shadow. Never a card inside a card. Buttons and inputs 4px, badges pill.

**Borders vs shadows.** Borders always win. Shadows only where a real hierarchy exists: `0 4px 12px rgba(30,30,30,.05)` and `0 12px 32px rgba(30,30,30,.08)`. No glow, no heavy black shadow.

**Transparency & blur.** Only two places: the sticky header (cream at 86% + 10px backdrop blur, 96% once scrolled) and the `rgba(30,30,30,.56)` overlay behind BEFORE/AFTER tags. No frosted cards, no protection gradients over photography — labels sit on solid capsules instead.

**Motion.** 120 / 180 / 320ms on `cubic-bezier(.2,.8,.2,1)`. Allowed: fade, translate-Y 8–16px reveals, underline transitions, image scale ≤1.02, accordion height, menu transitions. Forbidden: parallax, scroll hijacking, bounce, long animations, permanently floating elements. `prefers-reduced-motion` is honoured globally in `tokens/motion.css`.

**States.** Hover: primary button → near-black; secondary → charcoal border + 4% charcoal wash; links → underline colour shifts to sage-strong; text buttons → arrow slides 4px; images → scale 1.02. Press: colour only, never a shrink or bounce. Focus-visible: `2px solid #A6B2A0` at 3px offset, everywhere. Inputs focus to charcoal border + 3px sage-28% glow. Disabled: 42% opacity. Loading: discreet skeletons or a 14px charcoal spinner; the 2S monogram is the only permitted brand loader.

**Layout fixtures.** Only the header is fixed (sticky, 88 → 72px). No floating chat bubbles, no sticky promo bars, no cookie interstitials over the hero.

---

## ICONOGRAPHY

The brand document specifies direction only — **linear, geometric, thin stroke, monochrome charcoal**, at 16 (UI) / 20 (inline) / 24 (card) / 32px (feature). **No icon set, icon font or SVG assets were supplied**, so nothing was copied in.

- What this system actually uses: **typographic glyphs, not icons** — `→` for every forward action (text buttons, cards, need rows), `+` / `−` for the accordion, `✕` for closing the mobile menu, `⟷` on the before/after grip. That matches the document's "no innecesary iconography" rule and needs zero assets.
- The two real SVGs in the system are the checkbox tick and radio dot inside `Checkbox.jsx` (1.4px stroke, charcoal/cream), drawn only because a form control cannot be a glyph.
- **If a broader icon set is needed, substitute [Lucide](https://lucide.dev) via CDN** (`https://unpkg.com/lucide@latest`) at `stroke-width: 1.25`, colour `currentColor` — closest available match to the specified thin geometric line direction. **This is a flagged substitution, not brand-approved.** Ask 2skin for the real set before shipping.
- Emoji: never. Unicode arrows and math signs: yes, as above.

## Logo

`assets/` holds cutouts derived from the single supplied raster: lockup, wordmark and 2S monogram, each in charcoal (for cream/white) and cream (for charcoal). **No vector master exists** — the PNGs are alpha-keyed from the upload, so they are fine at display sizes but should be replaced with SVG before production. Never redraw, stretch, shadow, gradient or 3D the mark; keep generous air around it; favicon and loader use the monogram alone.

---

## Index

**Root**
- `styles.css` — the single entry point consumers link (imports only).
- `tokens/` — `fonts.css` (EB Garamond + Inter from Google Fonts), `colors.css`, `typography.css`, `spacing.css`, `layout.css`, `borders.css`, `motion.css`, `base.css` (element resets).
- `css/components.css` — the class layer the React primitives render (`.tsk-*`).
- `thumbnail.html` — homepage tile. `SKILL.md` — Agent-Skills entry point.
- `assets/` — `2skin-lockup-{charcoal,cream}.png`, `2skin-wordmark-{charcoal,cream}.png`, `2skin-monogram-{charcoal,cream}.png`, `source-logo.png`.
- `guidelines/` — 19 specimen cards (Colors, Type, Spacing, Brand).

**Components** (`components/<group>/`, exported on the design-system namespace)

- `actions/` — **Button**, **TextLink**
- `primitives/` — **Badge**, **Eyebrow**, **Media**
- `forms/` — **Field**, **Input**, **Select**, **Textarea**, **Checkbox**
- `cards/` — **TreatmentCard**, **NeedCard**, **DoctorCard**, **TestimonialCard**
- `content/` — **Accordion**, **Quote**, **ArticleCard**
- `media/` — **BeforeAfter**, **Gallery**
- `navigation/` — **Header**, **MobileNav**, **Footer**
- `sections/` — **Hero**, **EditorialSplit**, **DarkSection**, **ResultsSection**, **CTASection**

Mapping to the brand document's naming (§36): `Button.Primary/.Secondary/.Text` → `Button variant`; `Card.Treatment/.Need/.Doctor/.Testimonial` → the four card components; `Form.*` → `forms/`; `Navigation.Header/.Mobile/.Footer` → `navigation/`; `Section.Hero/.Editorial/.Dark/.Results/.CTA` → `sections/`; `Media.BeforeAfter/.Gallery` → `media/`; `Content.Accordion/.Quote/.ArticleCard` → `content/`.

**Intentional additions** (not named in §36, each with a reason)
- `Media` — one frame owning the brand ratios, radii, hover scale and the honest "photography pending" placeholder.
- `Eyebrow` — the uppercase label that §5.4 and every section head implies but never names.
- `Field` — §13 mandates real labels, hints and error text; this is that wrapper.
- `TextLink` — §12 defines link styling but no component.

**UI kit** — `ui_kits/website/` — click-through recreation of the marketing site: `index.html` (shell, routing, header/mobile menu/footer), `HomeScreen.jsx`, `TreatmentScreen.jsx`, `SkinQualityScreen.jsx` (+ `AboutScreen`), `ContactScreen.jsx` (working submit → success state), `shared.jsx` (sample content + `SectionHead` / `Steps` helpers). See `ui_kits/website/README.md` for the mapping to the brand document's page templates.

## Open items

- No vector logo, no photography, no icon set, no self-hosted font binaries were supplied. Fonts are the specified originals via Google Fonts; icons are typographic; imagery is placeholders.
- Sage is used only for: focus rings, footer column titles, badge accents, the `sage` button variant and small diagram marks.
