# BearCom — Project Plan

> **Status:** Complete — ready for development
> **Date:** 2026-03-17
> **Team:** 2 developers
> **Stack:** Drupal 10/11 + Docker
> **Deadline:** TBD

---

## 0. Project Overview

### What is this?
**BearCom** (bearcom.com) is a US/Canada company that sells, rents, and services two-way radios and communication equipment (Motorola, etc.). This project is a **full redesign** of their corporate website.

### What are we building?
A Drupal 10/11 website running in Docker with 15 page templates, dynamic content management (products, articles, locations, industries), AJAX-powered product filters, interactive location map, and a responsive design (desktop + mobile).

### Team & Roles

| | **Developer 1 (Backend)** | **Developer 2 (Frontend)** |
|---|---|---|
| **OS** | Windows | Linux |
| **Focus** | Drupal site-building: content types, paragraph types, taxonomies, Views, Webforms, modules, config | Drupal theming: Twig templates, CSS, JS, responsive layout, component styling |
| **Tools** | Docker, Composer, Drush, Drupal admin UI | Docker, browser DevTools, Figma mockups |

Both developers share the same Docker environment and sync Drupal config via Git.

### Mockup Files
All design mockups are in the shared Figma file (see §14 Useful Links). Exported screenshots are organized as:

```
mockups/
├── Homepage.png
├── category_page.png                  # Category Page (desktop variant)
├── Category Page/1_page-0001 (2).jpg  # Category Page (full)
├── Product Page [visual start].jpg
├── Industry - Parent page.png
├── Industry Landing Page.png
├── Service - Landing page.png
├── Article content type.png           # Article v1
├── Article content type _ v2.jpg      # Article v2 (blue hero)
├── Locations page/1_page-0001.jpg     # Location detail v1 (orange hero)
├── Locations page/3_page-0001.jpg     # Location detail v2 (grey hero)
├── locations page.png                 # Location Parent (Branch Locator)
├── Location - Parent Page.jpg         # Location Parent (alternate variant)
├── Paid Search Landing Page/2_page-0001.jpg
├── contact_us.jpg                     # Contact Us page
├── searchresult_page.jpg              # Search Results
├── Accessory Template.png             # Accessory page variant (reference)
├── 403 page.png
├── 404 page.png
├── mobile/                            # Mobile breakpoint mockups
│   ├── M_ homepage.png
│   ├── M_ Category Page.png
│   ├── M_ product page.png
│   ├── M_ Industry page.png
│   ├── M_ Paid Search..png
│   ├── M_ Paid Search. (1).png        # Paid Search mobile (variant 2)
│   └── Filters.png                    # Mobile filter overlay
├── mobile_hamburger/                  # Mobile navigation states
│   ├── M_ Nav - 1st level.png
│   ├── M_ Nav - Solutions.png
│   ├── M_ Nav - Solutions _ Voice.png
│   ├── M_ Nav - Industries..png
│   ├── M_ Nav - Rentals.png
│   ├── M_ Nav - Resources..png
│   └── M_ Nav - About..png
└── dropdown-hover/                    # Desktop dropdown menus
    ├── Solutions _ v4.png
    ├── Industries..png
    ├── Rentals..png
    ├── Resources..png
    └── about.png
```

### Key Architectural Decisions

| Decision | Choice | Why |
|----------|--------|-----|
| Content flexibility | **Paragraphs** module (not Layout Builder) | More predictable templating, easier theming, widely supported. Layout Builder is harder to style pixel-perfect from Figma. |
| Product filters | **Better Exposed Filters (BEF)** + Views AJAX | BEF renders taxonomy filters as checkboxes (not default dropdowns). AJAX avoids page reloads. |
| Maps | **Leaflet** (not Google Maps) | Free, no API key needed for dev, open-source tiles. |
| Forms | **Webform** module | Full-featured form builder with AJAX submit, conditional logic, email handlers. |
| Search | **Search API** (with DB backend) | Scalable to Solr later. Provides Views integration for custom result templates. |
| CSS approach | **Vanilla CSS** with CSS variables | No build tools required, simple for 2-dev team. Can add preprocessor later. |
| JS approach | **Vanilla JS** (no framework) | Lightweight, Drupal behaviors compatible. jQuery available via Drupal core if needed. |
| Carousel/slider | **Splide.js** (lightweight, no dependencies) | Used for Related Products, Related Content, Product gallery, mobile swipers. Include via CDN or local copy in theme `js/vendor/`. |

### Git & Collaboration Workflow

```
1. Both devs work on the same `main` branch (or feature branches if needed)
2. Drupal config sync:
   - Before export: `drush export:all -y` (saves taxonomies/menus/blocks to structure_sync.data.yml)
   - After making changes in Drupal admin: `drush export:all -y && drush cex -y` (config export to config/sync/)
   - After pulling changes: `drush cim -y` (imports config + auto-imports structure via bearcom_sync module)
   - ALWAYS commit config/sync/ YAML files to Git
3. Never edit the same content type simultaneously — communicate before making config changes
4. Frontend dev can work on Twig/CSS/JS independently (these are file-based, no config conflicts)
```

### Правила розробки (ОБОВ'ЯЗКОВІ)

> Ці правила діють протягом усього проекту. Перед початком роботи — прочитай. Перед код-рев'ю — перевір.

#### CSS

1. **Тільки CSS-змінні** — всі кольори, шрифти, розміри, відступи, радіуси, transition беруться з `var(--...)` (файл `_variables.css`). Хардкод значень заборонений. Якщо токену немає — спочатку додай його в `_variables.css`.
2. **BEM-подібне іменування** — `.block__element--modifier` (приклад: `.site-header__nav`, `.btn--primary`, `.hero-banner__title--large`).
3. **Без `!important`** — якщо потрібен `!important`, значить щось зроблено неправильно вище по каскаду. Виправ причину.
4. **Без inline styles** — ніяких `style="..."` в Twig-шаблонах. Всі стилі — у відповідному CSS-файлі.
5. **Кожен компонент = окремий CSS-файл** — не змішувати стилі різних компонентів в одному файлі.
6. **Mobile-first не потрібен** — верстаємо desktop-first (базові стилі = desktop), адаптив через `@media (max-width: ...)`.
7. **Breakpoints**: `1023px` (tablet), `767px` (mobile). Використовувати тільки ці два.
8. **Контейнер** — завжди `.container` (max-width: 1320px + padding 20px). Не створювати кастомні контейнери.

#### Twig

1. **Іменування шаблонів** — строго за Drupal conventions: `node--{type}--{view-mode}.html.twig`, `paragraph--{type}.html.twig`, `page--{type}.html.twig`.
2. **Без логіки в шаблонах** — Twig тільки для розмітки. Складна логіка (підрахунки, фільтрація) — в `bearcom.theme` (preprocess functions).
3. **Поля виводити через Twig** — `{{ content.field_name }}` для полів з форматуванням, `{{ node.field_name.value }}` тільки коли потрібне "сире" значення.
4. **Перевіряти наявність** — `{% if content.field_name|render|trim %}` перед виводом опціональних полів, щоб не залишались порожні обгортки.

#### JavaScript

1. **Тільки через Drupal behaviors** — весь JS обов'язково обгорнутий в `(function (Drupal, once) { Drupal.behaviors.behaviorName = { attach: function (context, settings) { ... } }; })(Drupal, once);`.
2. **`once()` обов'язково** — для уникнення повторної ініціалізації при AJAX-оновленнях.
3. **Без глобальних змінних** — все всередині behavior closure.
4. **Event delegation** — для динамічного контенту (AJAX-фільтри, каруселі) навішувати події на батьківський елемент.
5. **Без jQuery де можливо** — vanilla JS. jQuery тільки якщо потрібна взаємодія з Drupal jQuery-плагінами.

#### Backend (Drupal config)

1. **Machine names** — строго за `PROJECT_PLAN.md` (секція §5–§7). Не вигадувати свої назви.
2. **Config export після кожної зміни** — змінив щось в адміні → `drush export:all -y && drush cex -y` → git commit. Не накопичувати зміни.
3. **Paragraph types** — використовувати існуючі перед створенням нових. Список спільних параграфів: `cta_block`, `content_block`, `faq_item`, `checklist_item`.
4. **Image styles** — тільки 15 визначених у §7.7. Не створювати додаткових без узгодження.
5. **Pathauto patterns** — налаштувати одразу при створенні CT (формати URL з §6).
6. **Metatag defaults** — налаштувати для кожного CT одразу при створенні.

#### Загальне

1. **Не комітити** зламаний код — перед комітом перевірити що сайт працює (`drush cr`, перезавантажити сторінку).
2. **Щоденний ритуал**: `git pull` → `drush cim -y` → працюєш → `drush export:all -y && drush cex -y` → `git push`.
3. **Конфлікт в конфігах** — НІКОЛИ не вирішувати вручну. Відкотити, домовитись хто перший пушить, потім другий імпортує і робить свої зміни поверх.
4. **Тестовий контент** — створювати 2–3 ноди одразу після CT, з усіма заповненими полями (включно з необов'язковими). Це потрібно фронтенду для верстки.
5. **Перед закриттям таски** — перевірити на desktop (1920px) і mobile (375px). Якщо не виглядає як макет — не закривати.

### How to Start (for new developer)

```bash
# 1. Clone the repository
git clone <repo-url> bearcom && cd bearcom

# 2. Copy environment file
cp .env.example .env

# 3. Build and start Docker containers
docker compose up -d --build

# 4. First-time Drupal install
docker compose exec php bash scripts/install.sh

# 5. Open in browser
# Site: http://localhost
# Admin: http://localhost/user/login (admin / admin)
# Mail: http://localhost:8025 (MailHog)

# 6. Import existing config (if config/sync/ has YAML files)
docker compose exec php bash -c "cd web && ../vendor/bin/drush cim -y"

# Useful commands:
docker compose exec php bash -c "cd web && ../vendor/bin/drush cr"                        # Clear cache
docker compose exec php bash -c "cd web && ../vendor/bin/drush export:all -y"             # Export taxonomies/menus/blocks
docker compose exec php bash -c "cd web && ../vendor/bin/drush cex -y"                    # Export config
docker compose exec php bash -c "cd web && ../vendor/bin/drush cim -y"                    # Import config + structure (auto)
docker compose exec php bash -c "cd web && ../vendor/bin/drush uli"                       # One-time login link
```

---

## 1. Site Map (15 templates)

| # | Template | Complexity | Source |
|---|----------|-----------|--------|
| 1 | **Homepage** | High | Homepage.png |
| 2 | **Category Page** | High | category_page.png, Category Page/1_page-0001 (2).jpg |
| 3 | **Product Page** | High | Product Page [visual start].jpg |
| 4 | **Industry - Parent Page** | Medium | Industry - Parent page.png |
| 5 | **Industry Landing Page** | Medium | Industry Landing Page.png |
| 6 | **Service Landing Page** | Medium | Service - Landing page.png |
| 7 | **Article (v1)** | Low | Article content type.png |
| 8 | **Article (v2 — blue hero)** | Low | Article content type _ v2.jpg |
| 9 | **Location Page (v1 — orange hero)** | Medium | Locations page/1_page-0001.jpg |
| 10 | **Location Page (v2 — grey hero)** | Medium | Locations page/3_page-0001.jpg |
| 11 | **Paid Search Landing Page** | Medium | Paid Search Landing Page/2_page-0001.jpg |
| 12 | **Location Parent Page** | High | locations page.png, Location - Parent Page.jpg |
| 13 | **Search Results Page** | Medium | searchresult_page.jpg |
| 14 | **Contact Us Page** | Medium | contact_us.jpg |
| 15 | **403 / 404 Error Pages** | Low | 403 page.png, 404 page.png |

**Total: 15 templates**

---

## 2. Design Tokens

> **ПРАВИЛО:** Всі кольори, розміри шрифтів, відступи, border-radius і transition ОБОВ'ЯЗКОВО беруться з CSS-змінних (`_variables.css`). Ніколи не хардкодити значення напряму — завжди `var(--color-primary)`, `var(--spacing-md)` і т.д. Це критично для підтримки та консистентності дизайну. Якщо потрібного токену немає — додай його в `_variables.css`, а потім використовуй.

```css
:root {
  /* Colors */
  --color-primary:        #FC5000;  /* orange — buttons, accents, hero */
  --color-primary-dark:   #D4551A;  /* hover state */
  --color-dark-blue:      #22262C;  /* hero Article v2, headings */
  --color-text-primary:   #4A4F55;
  --color-background:     #FAFAFA;
  --color-background-alt: #EFEFEF;  /* grey sections */
  --color-footer-bg:      #4A4F55;
  --color-error-orange:   #FB591F;  /* 403/404 numbers */
  --color-white:          #FFFFFF;

  /* Typography */
  --font-family:          'Roboto Condensed', sans-serif;
  --font-size-base:       16px;
  --font-weight-regular:  400;   /* h5, paragraph, details */
  --font-weight-semibold: 600;   /* h1–h4 */

  /* Headings — Desktop */
  --h1-size: 58px;  --h1-lh: 64px;
  --h2-size: 42px;  --h2-lh: 44px;
  --h3-size: 34px;  --h3-lh: 36px;
  --h4-size: 26px;  --h4-lh: 28px;
  --h5-size: 22px;  --h5-lh: 24px;
  --p-size:  16px;  --p-lh:  20px;
  --details-size: 16px; --details-lh: 18px;

  /* Headings — Mobile (applied via media query) */
  /* --h1-size: 38px;  --h1-lh: 40px; */
  /* --h2-size: 30px;  --h2-lh: 32px; */
  /* --h3-size: 24px;  --h3-lh: 26px; */
  /* --h4-size: 20px;  --h4-lh: 22px; */
  /* --h5-size: 18px;  --h5-lh: 20px; */
  /* --p-size:  16px;  --p-lh:  24px; */
  /* --details-size: 16px; --details-lh: 18px; */

  /* Spacing */
  --section-padding:      80px;
  --grid-gap:             24px;
  --container-max-width:  1320px;

  /* Border Radius */
  --radius-button:        4px;
  --radius-card:          8px;
}
```

---

## 3. Layout Components

### Top Bar
- **Search icon** → on click, search input slides out to the left (inline expand, no page redirect)
- Phone: `(800) 527-1670`
- **Locations link** (with map icon) → links to `/locations` (Location Parent Page)
- Country switcher: US / Canada flags

### Header
- Logo: BearCom "AlwaysOn"
- Navigation: Solutions · Rentals · Industries · Resources · About
- CTA: "Contact Us" (orange button)
- **Mobile:** hamburger menu, top bar collapses (phone + Contact Us button visible)

### Navigation Dropdown Menus (hover on desktop)

**Content is dynamic** — pulled from Drupal Menu system (not hardcoded). On mobile, mega menu is hidden and replaced by hamburger accordion.

All dropdowns share a common layout:
- **Left orange sidebar** with 3 CTA links: "Bearcom overview >", "Request a quote >", "Request a consultation >"
- **Right content area** with menu items

#### Solutions (mega menu)
3 columns with icons:
- **VOICE:** Two-Way Radios & Accessories (Radios → Portable, Mobile, Cellular Push to Talk; Repeaters; BDA/DAS; Emergency Mass Notification; Dispatch Consoles; Call Boxes) + Accessories (Batteries and Chargers, Antennas, Microphones, Earpieces, Cases)
- **SECURITY:** Surveillance Cameras, Body Worn Cameras, Access Control & Intercom, License Plate Recognition, Concealed Weapons Detection
- **DATA:** Private LTE, AlwaysOn Integrated Solutions, Backhaul Solutions

Product image displayed at top of content area.

#### Industries
3×4 grid with icons:
Education, Utilities & Public Works, Transportation Logistics, Hospitality, Public Safety, Manufacturing, Facilities, Healthcare, Retail / Distribution, Petro/Chem Oil and Gas, Construction, Events

#### Rentals
4 image cards:
Two-Way Radio Rentals, Video Surveillance & CCTV, Private LTE, Event Management & Staffing

#### Resources
3 image cards:
Promotions, BearCom Blog, Innovation

#### About
Right side — icon list:
Our Story, Purpose, BearCom Companies, Locations, Careers, BearCom in the News

Orange sidebar positioned in center (not left) for this dropdown.

### Footer Full
- 2 address columns: Canada HQ + US HQ (with phone numbers)
- 4 link columns: **Products, About Us, Resources, News**
- Newsletter subscription form
- Social icons: Facebook, Twitter, LinkedIn, YouTube, Instagram
- Legal links row
- Patent information
- AlwaysOn logo (static SVG, no animation)

### Footer Minimal (error pages, paid search LP)
- Logo + phone number + patent text

---

## 4. Page-Specific Behavior & Interactions

### Homepage
- **Hero image:** Static image, scales with page width (not dynamic from product entity)
- **Guided Journey Block:** Tabbed component — admin can add/remove tabs with custom content per tab. Tabs switch content on click.
- **Industries stats ("over 12,000"):** Animated count-up on scroll into viewport
  - **Implementation:** `IntersectionObserver` API in `stats-counter.js`. When `.stats-counter` enters viewport, animate numbers from 0 to target value using `requestAnimationFrame`. Target number is read from `data-target` attribute on the element.

### Category Page
- **Filters:** AJAX-based — selecting a checkbox updates the product grid without page reload. Small loading spinner appears on the product grid area during update.
  - **Implementation:** Views with AJAX enabled + `better_exposed_filters` module renders taxonomy checkboxes. `views_ajax_history` updates the URL on filter change. Loading spinner is handled by Views AJAX throbber (can be styled via CSS class `.ajax-progress`).
- **Filter logic:** AND between categories (selecting "Portable" + "Accessories" shows items that match BOTH)
- **Sort options:** By Price (low-high, high-low), By Name (A-Z, Z-A)
  - **Implementation:** Views exposed sort with `sort_by` parameter.
- **Product grid:** Updates via AJAX with loading indicator

### Product Page
- **Tab navigation:** Anchor links that smooth-scroll to sections on the same page (NOT hide/show tabs)
  - **Implementation:** `smooth-scroll.js` — each tab is `<a href="#section-specs">`, sections have matching `id`. JS intercepts click and uses `element.scrollIntoView({ behavior: 'smooth' })`. Active tab highlighted based on scroll position via `IntersectionObserver`.
- **Image gallery:** Multiple display styles for product images. Lightbox is optional (nice-to-have, not required)
- **"Build your System" accessories section:** Card grid of accessory categories — clicking a card navigates to Category Page with pre-selected filters for that accessory type (e.g., `/products?category=microphones`)

### Location Parent Page
- **Search by ZIP/City:** AJAX search → updates map pins + shows Location Card (mini)
  - **Implementation:** Custom Drupal module (`bearcom_locations`) with a REST endpoint or Views REST export. `location-search.js` sends AJAX request on input submit, receives JSON with location data (name, address, coordinates), updates Leaflet map markers and renders Location Card (mini) in the sidebar.
- **"Use my location":** Optional feature (Geolocation API) — can be implemented later
- **Map:** Leaflet with custom orange pin markers. Clicking a pin shows the Location Card (mini) as a Leaflet popup.
  - **Implementation:** `leaflet` Drupal module provides map rendering. Custom JS in `location-search.js` handles marker click events and popup content.
- **State Directory:** Static grouped listing of all locations, always visible below the map. Rendered by Locations Directory View (grouped by `field_state` taxonomy).

### Contact Us Page
- **After form submit:** Modal/popup with thank you message (stays on same page, no redirect)
  - **Implementation:** Webform AJAX submit is built-in. Configure Webform confirmation type as "Message" (not redirect). Wrap the confirmation message in a modal using custom JS or the `<dialog>` HTML element with CSS styling.
- **CAPTCHA:** reCAPTCHA v2 (checkbox "I'm not a robot")
  - **Implementation:** `recaptcha` module + configure site key/secret key in Drupal admin → `/admin/config/people/captcha`. Attach to "Contact Us Form" and "Lead Capture Form" webforms.

### Search
- **Search input in header:** Inline expand (slides left on icon click), submit navigates to `/search?keys=...`
  - **Implementation:** `search-expand.js` — click on search icon toggles `.search-expanded` class on the header search wrapper. CSS transition handles the slide animation. Form submits GET to `/search`.
- **Search results:** Full page with dedicated search input, results list, pagination
  - **Implementation:** Search API with Database backend. Views page at `/search` with fulltext contextual filter from URL query `keys`. Result row template shows title (linked) + trimmed body with keyword highlighting (Search API Highlight processor).

### Navigation Mega Menu
- **Implementation:** Custom Twig template `menu--main.html.twig` renders the Drupal Main Menu as a mega dropdown. Menu items are managed in Drupal admin (`/admin/structure/menu/manage/main`). Each top-level item has children rendered as the dropdown content. The orange sidebar CTAs ("Bearcom overview", "Request a quote", "Request a consultation") are hardcoded in the template or managed via a custom block.
- **JS:** `mega-menu.js` handles hover open/close with a small delay (prevents accidental close). On mobile, hidden entirely — replaced by `mobile-menu.js` accordion.

---

## 5. Mobile Responsive Behavior

**Breakpoints:** Desktop (≥1024px), Tablet (768–1023px), Mobile (<768px). CSS: `@media (max-width: 1023px)` та `@media (max-width: 767px)`.

### Global
- Header → logo + search icon + X (close) button
- Top bar → moves into mobile menu as secondary bar (phone icon, locations icon, US/Canada flags)
- Footer Full → single column stack, all link columns collapse

### Mobile Hamburger Menu (full-screen overlay)
**Header:** Logo + Search icon + Close (X) button
**Secondary bar:** Phone icon, Locations icon, US flag, Canada flag (dark grey background)

**1st level:** Accordion list — Solutions ˅, Rentals ˅, Industries ˅, Resources ˅, About ˅
- Active item: text turns orange, chevron rotates up (˄)

**Solutions → 2nd level:** Voice ˅, Security ˅, Data ˅ (with icons)
**Solutions → Voice → 3rd level (deepest):**
- TWO-WAY RADIOS (bold header) → Portable Radios, Mobile Vehicle Radios, Cellular Push to Talk Radios
- TWO-WAY RADIOS ACCESSORIES (bold header) → Batteries, Antennas, Headsets, Earpieces, Belt Clips
- REPEATERS, BDA/DAS, EMERGENCY MASS NOTIFICATION, DISPATCH CONSOLES, CALL BOXES, RENTAL VOICE SOLUTIONS
- Then collapsed: VOICE ˄ (open), DATA ˅, SECURITY ˅

**Industries:** Flat list with icons — Education, Hospitality, Facilities, Petro/Chem Oil and Gas, Utilities & Public Works, Public Safety, Healthcare, Construction, Transportation Logistics, Manufacturing, Retail / Distribution, Events

**Rentals:** Flat list with icons — Two way radio rentals, Video Surveillance & CCTV, Private LTE, Event Management & Staffing

**Resources:** Flat list with icons — Promotions, BearCom Blog, Innovation

**About:** Flat list with icons — Our Story, Purpose, BearCom Companies, Locations, Careers, BearCom in the News

**Bottom (always visible):** Orange background block with 3 CTAs:
1. "Bearcom overview >" + description
2. "Request a quote >" + description
3. "Request a consultation >" + description

### Homepage (M_ homepage.png)
- Hero → full-width image stacked above text, single column
- Guided Journey block → stacked vertically
- Industries stats → 1 column (cards stack)
- CTA block → full width
- Rentals/Connected → stacked cards
- Product grid → 2 columns

### Category Page (M_ Category Page.png)
- Hero → full width, product image above title
- Filters → hidden behind "FILTERS" button → opens as **full-screen overlay** (slide-in panel with checkboxes + "Clear All Filters" at bottom)
- Product grid → **2 columns**
- Sort dropdown visible
- Pagination → stays
- FAQ accordion → full width
- Footer → single column with all links

### Product Page (M_ product page.png)
- Gallery → horizontal swiper
- Tab navigation → horizontal scroll
- Specs table → full width, stacked
- Content blocks → image above text (single column)
- Guided Journey → stacked tabs (vertical)
- Related Radios → horizontal carousel
- Accessories grid → horizontal scroll
- Stats counter → 2×2 grid
- Rentals/Connected → stacked

### Industry Page (M_ Industry page.png)
- Hero → full width
- Card grid → **1 column** (cards stack vertically)
- Each card → image full width above text
- CTA block → full width

### Paid Search Landing Page (M_ Paid Search.png)
- Minimal header → product title + "Contact Us" button
- Layout → **single column** (text + benefits on top, form below)
- Form fields → single column (full width)
- Submit button → full width
- CTA block → full width stacked
- Minimal footer

### Article Page (no mobile mockup — follow layout rules below)
- Hero → full width, image scales
- Article v2 (blue hero) → solid color block, text centered
- Body text → single column, full width
- Share buttons → horizontal row below hero
- CTA block → full width
- Related articles → horizontal scroll carousel

### Service Landing Page (no mobile mockup — follow layout rules below)
- Hero → full width
- Content blocks L/R → single column, image above text (all blocks)
- Video → full width embed, 16:9 aspect ratio
- FAQ accordion → full width
- CTA block → full width
- Related content → horizontal scroll carousel

### Location Parent Page (no mobile mockup — follow layout rules below)
- Search form → full width input
- Map → full width, fixed height 300px
- "Use my location" → centered below search
- State directory → **1 column** (states stack vertically, locations list below each state)
- Location cards (mini) → full width, stacked

### Location Page (no mobile mockup — follow layout rules below)
- Hero → full width
- Address + phone → full width block
- Open hours table → full width
- Map → full width, fixed height 250px
- FAQ accordion → full width
- About section → single column

### Contact Us Page (no mobile mockup — follow layout rules below)
- Layout → **single column** (left content stacks above form)
- Benefits checklist → full width
- Webform → full width, all fields single column
- Submit button → full width

### Search Results Page (no mobile mockup — follow layout rules below)
- Search input → full width
- Results list → single column, full width
- Pagination → centered

### 403 / 404 Error Pages (no mobile mockup — follow layout rules below)
- Error number → large, centered
- Error message → centered text
- CTA button → full width
- Minimal footer

---

## 6. Reusable UI Components

| Component | Fields/Variants | Used on |
|-----------|----------------|---------|
| **Hero Banner** | Image, Title, Subtitle, CTA, Style: product / image / solid color | All pages |
| **Product Card** | Image, Title, Description | Category, Product, Homepage |
| **Industry Card** | Icon/Image, Title, Description, "Learn More" link | Industry Parent, Landing, Homepage |
| **FAQ Accordion** | H2 title + collapsible Q&A items | Category, Product, Service, Location |
| **CTA Block (default)** | Image, Title, Description, Button | Article, Location, Industry |
| **CTA Block (orange)** | Orange background, Title, Button | Service, Homepage, Landing Page |
| **Guided Journey Block** | Title, Tabs (Tab Title + Body + Checklist + Image) | Product, Homepage |
| **Stats Counter** | Number + Label items (e.g. "over 12,000") | Homepage, Product |
| **Rentals/Connected Services** | Title, 2 cards (Image + Title + Link) | Homepage, Product |
| **Content Block (L/R)** | Title, Body, Image, Layout toggle (image left/right) | Service, Industry Landing, Product |
| **Spec Table** | Title, expandable rows (Label + Value) | Product |
| **Tab Navigation** | Tab items | Product (6 tabs) |
| **Related Content Carousel** | Title, Entity references | Product, Article, Service |
| **Accessories Grid** | Title, Items (Image + Title + Link) | Product |
| **Product Grid** | Title, View/Entity references | Homepage, Category |
| **Filter Sidebar** | Category checkboxes, "Clear Filters" button | Category |
| **Pagination** | Numbered with active state | Category |
| **Breadcrumbs** | Auto-generated path | Category, Article v2, Location v2 |
| **Share Buttons** | Facebook, Twitter, LinkedIn, Email | Article v2 |
| **Open Hours Table** | Day + Hours rows | Location |
| **Leaflet Map** | Geofield coordinates, custom orange pin marker | Location |
| **Lead Form** | First/Last Name, Email, Phone, Company, Job Title, Country, State, Message, Submit | Paid Search LP |
| **Newsletter Form** | Email input + submit | Footer |
| **Video Block** | Title, Video URL (embed) | Service, Product, Flexible Page |
| **Location Search** | ZIP/City input + "Use my location" link | Location Parent |
| **Location Map** | Interactive map with pins (US + Canada) | Location Parent |
| **Location Card (mini)** | Name, Address, City, State, ZIP, "More Info" link (orange left border) | Location Parent (search results) |
| **State Directory** | State name (orange, bold) + list of Location Title links, 5 columns | Location Parent |
| **Search Input** | Text input + search icon button | Search Results |
| **Search Result Item** | Title (link) + excerpt with highlighted keyword | Search Results |

---

## 7. Drupal Architecture

### 7.1 Content Types

#### Product (`product`)
| Field | Type | Notes |
|-------|------|-------|
| title | Text | Product name |
| field_images | Media (multiple) | Product gallery |
| field_short_description | Text (formatted) | For teaser/card view |
| field_price | Decimal | For sort by price on Category Page |
| field_body | Paragraphs (content_block, video_block, cta_block, accessories_grid) | Flexible content sections |
| field_specs | Paragraphs (Spec Table) | Main specs table |
| field_additional_specs | Paragraphs (Spec Table) | Additional specs |
| field_related_products | Entity Reference (Product) | Related radios carousel |
| field_accessories | Entity Reference (Product) | Microphones, Chargers, Earpieces |
| field_category | Taxonomy (Product Category) | For filtering on Category Page |
| field_faq | Paragraphs (FAQ Item) | Q&A section |
| field_guided_journey | Paragraphs (Guided Journey) | Tabbed block |

#### Article (`article`)
| Field | Type | Notes |
|-------|------|-------|
| title | Text | Article title |
| field_hero_image | Media | Hero image |
| field_hero_style | Select | "blue" / "image" |
| body | Text (formatted, full HTML) | Rich text content |
| field_show_share | Boolean | Show/hide share buttons |
| field_related | Entity Reference (Article) | Related articles carousel |
| field_cta | Paragraphs (CTA Block) | Bottom CTA |

#### Industry (`industry`)
| Field | Type | Notes |
|-------|------|-------|
| title | Text | Industry name |
| field_icon | Media | Icon or image for card (teaser view) |
| field_hero_image | Media | Hero banner image (full view) |
| field_description | Text | Short description |
| field_solutions | Paragraphs (Card Grid) | 3-column card sections (repeatable) |
| field_cta | Paragraphs (CTA Block) | Bottom CTA |

#### Service (`service`)
| Field | Type | Notes |
|-------|------|-------|
| title | Text | Service name |
| field_hero_image | Media | Hero image |
| field_hero_style | Select | "image" / "color" — hero variant |
| field_body | Paragraphs (Content Block L/R) | Alternating text+image sections |
| field_video | Paragraphs (Video Block) | Embedded video section |
| field_faq | Paragraphs (FAQ Item) | Q&A section |
| field_cta | Paragraphs (CTA Block) | Bottom CTA (orange variant) |
| field_related | Entity Reference (Service, Article) | Related content carousel |

#### Location (`location`)
| Field | Type | Notes |
|-------|------|-------|
| title | Text | City name |
| field_state | Taxonomy (State / Province) | For directory grouping |
| field_address | Address | Full address |
| field_phone | Text | Phone number |
| field_open_hours | Paragraphs (Day + Hours) | Hours table |
| field_photo | Media | Office photo |
| field_geo | Geofield | Map coordinates |
| field_about | Text (formatted) | About the office |
| field_faq | Paragraphs (FAQ Item) | Q&A section |
| field_hero_style | Select | "orange" / "grey" |

#### Landing Page (`landing_page`) — for Paid Search
| Field | Type | Notes |
|-------|------|-------|
| title | Text | Page title |
| field_headline | Text | Main headline |
| field_benefits | Paragraphs (Checklist Item) | Benefits list with checkmarks |
| field_image | Media | Left-side image |
| field_webform | Webform reference | Lead capture form |
| field_cta | Paragraphs (CTA Block) | Bottom CTA |
| field_minimal_header | Boolean | Use minimal header/footer |

#### Flexible Page (`flexible_page`) — for Homepage, Location Parent, Contact Us

A single "Flexible Page" content type used for unique one-off pages that need custom layouts. Each page uses different combinations of fields.

| Field | Type | Notes |
|-------|------|-------|
| title | Text | Page title |
| field_paragraphs | Paragraphs (hero_banner, cta_block, card_grid, stats_counter, rentals_connected, guided_journey, content_block, product_grid, video_block, accessories_grid) | Main flexible content (used by Homepage) |
| field_heading | Text | Secondary heading (used by Contact Us: "Contact us today!") |
| field_description | Text (formatted) | Intro text (Contact Us, Location Parent) |
| field_benefits | Paragraphs (Checklist Item) | Checkmark items (Contact Us, Paid Search) |
| field_image | Media | Supporting image |
| field_webform | Webform reference | Embedded form (Contact Us) |
| field_hero_style | Select | Hero variant if needed |

Fields are optional — each page uses only what it needs. Field visibility per page is managed via **Field Group** (conditional tabs in edit form).

**Contact Us Webform — "Contact Us Form":**

| Form Field | Type | Required |
|------------|------|----------|
| Name | Text | Yes |
| Email | Email | Yes |
| Phone | Tel | No |
| Company | Text | Yes |
| State | Select (US states) | No |
| Zip code | Text | No |
| I have a question about | Select ("Two-Way Radio Sales", etc.) | Yes |
| Message | Textarea | No |
| Is this request for resale? | Radio (Yes/No) | Yes |
| CAPTCHA | reCAPTCHA v2 | Yes |
| Submit | Button "REQUEST A FREE QUOTE" | — |

**On submit:** Modal thank-you message (no page redirect).

**Paid Search Lead Form Webform — "Lead Capture Form":**

| Form Field | Type | Required |
|------------|------|----------|
| First Name | Text | Yes |
| Last Name | Text | Yes |
| Email Address | Email | Yes |
| Phone | Tel | Yes |
| Company | Text | No |
| Job Title | Text | No |
| Country | Select | No |
| State | Select | No |
| Message | Textarea | No |
| CAPTCHA | reCAPTCHA v2 | Yes |
| Submit | Button "SUBMIT" | — |

### 7.2 Paragraph Types

| Paragraph Type | Fields |
|----------------|--------|
| **Hero Banner** (`hero_banner`) | field_image (Media), field_title (Text), field_subtitle (Text), field_cta_text (Text), field_cta_url (Link), field_style (Select: product/image/color), field_product_image (Media — для product-стилю) |
| **Content Block** (`content_block`) | field_title (Text), field_body (Text formatted), field_image (Media), field_layout (Select: image-left/image-right) |
| **Card Grid** (`card_grid`) | field_title (Text), field_subtitle (Text), field_cards (Paragraphs: Card Item) |
| **Card Item** (`card_item`) | field_icon (Media), field_title (Text), field_description (Text), field_link (Link) |
| **FAQ Item** (`faq_item`) | field_question (Text), field_answer (Text formatted) |
| **CTA Block** (`cta_block`) | field_image (Media), field_title (Text), field_description (Text), field_button_text (Text), field_button_url (Link), field_style (Select: default/orange) |
| **Guided Journey** (`guided_journey`) | field_title (Text), field_tabs (Paragraphs: GJ Tab) |
| **GJ Tab** (`gj_tab`) | field_tab_title (Text), field_body (Text formatted), field_checklist (Text multiple), field_image (Media) |
| **Stats Counter** (`stats_counter`) | field_items (Paragraphs: Stat Item) |
| **Stat Item** (`stat_item`) | field_number (Text), field_label (Text) |
| **Rentals Connected** (`rentals_connected`) | field_title (Text), field_card_1_image (Media), field_card_1_title (Text), field_card_1_link (Link), field_card_2_image (Media), field_card_2_title (Text), field_card_2_link (Link) |
| **Spec Table** (`spec_table`) | field_title (Text), field_rows (Paragraphs: Spec Row) |
| **Spec Row** (`spec_row`) | field_label (Text), field_value (Text) |
| **Video Block** (`video_block`) | field_title (Text), field_video (Media remote video) |
| **Accessories Grid** (`accessories_grid`) | field_title (Text), field_items (Entity Reference: Product) |
| **Product Grid** (`product_grid`) | field_title (Text), field_view_id (Text — machine name of View to embed, e.g. "products_listing"), field_limit (Number — max items to show). Used on Homepage to embed a product listing. |
| **Checklist Item** (`checklist_item`) | field_text (Text formatted) — renders with checkmark icon. Used in Contact Us benefits and Paid Search LP benefits. |
| **Open Hours Row** (`open_hours_row`) | field_day (Text), field_hours (Text) — single row in Location hours table. |

### 7.3 Taxonomies

| Vocabulary | Purpose | Example terms |
|------------|---------|---------------|
| **Product Category** (`product_category`) | Category Page filters | Two-Way Radios, Accessories, Chargers, Earpieces |
| **Industry** (`industry_tax`) | Tag industries | Healthcare, Construction, Education, Hospitality |
| **State / Province** (`state_province`) | Location grouping (directory + Location node field) | Alabama, ..., Wyoming, Alberta, Ontario, etc. |

### 7.4 Views

| View | Display | Page |
|------|---------|------|
| **Products Listing** | Grid 3×3 + exposed filters (taxonomy) + pager | Category Page `/products` |
| **Industries Listing** | Grid 3×3 cards | Industry Parent `/industries` |
| **Related Products** | Carousel (same category, exclude current) | Product Page (block) |
| **Related Articles** | Carousel (latest articles, exclude current) | Article Page (block) |
| **Related Services** | Carousel (other services, exclude current) | Service Page (block) |
| **Search Results** | List: title (link) + excerpt with keyword highlight + pager | Search `/search` |
| **Locations Directory** | Grouped by State/Province, 5-column layout, each group = State name + location links | Location Parent `/locations` |
| **Location Search** | Custom REST endpoint (`bearcom_locations` module), NOT a View. AJAX search by ZIP/City → JSON response | Location Parent (block) |

### 7.5 Page Architecture

How each page is built in Drupal:

| Page | Drupal implementation |
|------|----------------------|
| **Homepage** | Node type "Flexible Page" (set as front page) with Paragraphs (Hero, Guided Journey, Stats, CTA, Rentals, Product Grid). Fully editable through admin. |
| **Category Page** | Views page at `/products`. Exposed filters (taxonomy checkboxes) via Better Exposed Filters module. AJAX-enabled with loading indicator. |
| **Product Page** | Node type "Product" at `/products/[title]`. Sections rendered via Paragraphs. Tab nav = anchor links to section IDs. |
| **Industry Parent** | Views page at `/industries`. Grid of Industry nodes in teaser view mode. |
| **Industry Landing** | Node type "Industry" at `/industries/[title]`. |
| **Service Landing** | Node type "Service" at `/services/[title]`. |
| **Article (v1, v2)** | Node type "Article" at `/resources/[title]`. Hero style toggled via `field_hero_style`. |
| **Location Parent** | Node type "Flexible Page" at `/locations` with embedded View blocks: Location Search (AJAX) + Leaflet Map + Locations Directory (grouped by state). |
| **Location Page** | Node type "Location" at `/locations/[title]`. Hero style toggled via `field_hero_style`. |
| **Contact Us** | Node type "Flexible Page" at `/contact-us`. Left side = node fields (heading, description, benefits, image). Right side = embedded Webform via `field_webform`. |
| **Paid Search LP** | Node type "Landing Page" at custom path. `field_minimal_header` = true → switches to minimal header/footer via page template. |
| **Search Results** | Search API page at `/search`. Custom Views display. |
| **403 / 404** | System error pages → custom Twig templates `page--403.html.twig`, `page--404.html.twig`. |

### 7.6 URL Patterns (Pathauto)

| Content Type | Pattern | Example |
|-------------|---------|---------|
| Product | `/products/[node:title]` | `/products/mototrbo-r2-radio` |
| Article | `/resources/[node:title]` | `/resources/two-way-radio-guide` |
| Industry | `/industries/[node:title]` | `/industries/healthcare` |
| Service | `/services/[node:title]` | `/services/radio-repair` |
| Location | `/locations/[node:title]` | `/locations/garland-texas` |
| Flexible Page | `/[node:title]` | `/contact-us`, `/locations` (Homepage = front page, no alias) |
| Landing Page | `/lp/[node:title]` | `/lp/two-way-radio-quote` (can be overridden manually per node) |

### 7.7 Image Styles

| Style Name | Dimensions | Used for |
|-----------|------------|----------|
| `hero_desktop` | 1920×600 (crop) | Hero banners on all pages |
| `hero_mobile` | 768×400 (crop) | Hero banners on mobile |
| `product_card` | 400×400 (scale & crop) | Product grid cards on Category/Homepage |
| `product_gallery` | 800×800 (scale) | Product Page main gallery image |
| `product_thumb` | 100×100 (crop) | Product Page gallery thumbnails |
| `industry_card` | 400×300 (crop) | Industry cards on Parent/Homepage |
| `content_block` | 600×400 (scale & crop) | Content Block L/R images |
| `cta_block` | 500×350 (scale & crop) | CTA block image |
| `location_photo` | 600×400 (crop) | Location Page office photo |
| `rental_card` | 500×350 (crop) | Rentals dropdown + Rentals/Connected section |
| `article_hero` | 1200×500 (crop) | Article hero image |
| `article_inline` | 800×auto (scale width) | Inline images in article body |
| `landing_image` | 600×auto (scale width) | Landing Page left-side image |
| `gj_tab_image` | 500×350 (scale & crop) | Guided Journey tab images |
| `logo_icon` | 80×80 (scale) | Industry/menu icons |

---

## 8. Key Drupal Modules

| Module | Purpose |
|--------|---------|
| `paragraphs` | Flexible content blocks |
| `media` (core) | Image/video management |
| `metatag` | SEO meta tags, Open Graph |
| `pathauto` | Automatic URL aliases |
| `webform` | Contact Us form + Lead form (Paid Search LP) |
| `views` (core) | Product listings, content lists |
| `taxonomy` (core) | Product categories, industries |
| `search_api` | Site search |
| `twig_tweak` | Template helpers |
| `admin_toolbar` | Better admin UX |
| `field_group` | Organize edit forms |
| `focal_point` | Smart image cropping |
| `geofield` + `leaflet` | Maps on Location pages |
| `redirect` | URL redirects |
| `config_split` | Environment-specific config |
| `redis` | Cache backend |
| `captcha` + `recaptcha` | reCAPTCHA v2 on Contact Us form + Lead Capture Form |
| `better_exposed_filters` | AJAX filters with checkboxes on Category Page |
| `views_ajax_history` | URL update on AJAX filter change (SEO-friendly) |
| `address` | Structured address field for Location CT (`field_address`) |
| `simple_sitemap` | XML sitemap generation for SEO |
| `entity_reference_revisions` | Required by Paragraphs module for entity reference fields |

---

## 9. Theme Structure

```
web/themes/custom/bearcom/
├── bearcom.info.yml
├── bearcom.libraries.yml
├── bearcom.theme                    # Preprocess functions
│
├── css/
│   ├── base/
│   │   ├── _variables.css           # Design tokens
│   │   ├── _typography.css
│   │   └── _reset.css
│   ├── layout/
│   │   ├── _header.css
│   │   ├── _footer.css
│   │   └── _grid.css
│   ├── components/
│   │   ├── _button.css
│   │   ├── _accordion.css
│   │   ├── _card.css
│   │   ├── _hero.css
│   │   ├── _pagination.css
│   │   ├── _breadcrumbs.css
│   │   ├── _filter-sidebar.css
│   │   ├── _tabs.css
│   │   ├── _specs-table.css
│   │   ├── _form.css
│   │   ├── _share-buttons.css
│   │   ├── _stats-counter.css
│   │   ├── _carousel.css
│   │   ├── _cta-block.css
│   │   ├── _content-block.css
│   │   ├── _guided-journey.css
│   │   ├── _rentals.css
│   │   ├── _product-grid.css
│   │   ├── _checklist.css
│   │   ├── _open-hours.css
│   │   ├── _video-block.css
│   │   ├── _webform.css
│   │   ├── _newsletter.css
│   │   ├── _mega-menu.css
│   │   ├── _mobile-menu.css
│   │   ├── _location-search.css
│   │   ├── _location-card.css
│   │   ├── _state-directory.css
│   │   └── _search-expand.css
│   └── pages/
│       ├── _homepage.css
│       ├── _category.css
│       ├── _product.css
│       ├── _article.css
│       ├── _industry.css
│       ├── _service.css
│       ├── _location.css
│       ├── _landing-page.css
│       ├── _location-parent.css
│       ├── _search-results.css
│       ├── _contact-us.css
│       └── _error.css
│
├── js/
│   ├── accordion.js
│   ├── tabs.js
│   ├── filter.js
│   ├── carousel.js
│   ├── mobile-menu.js
│   ├── mega-menu.js
│   ├── stats-counter.js          # Count-up animation on scroll
│   ├── search-expand.js          # Header search input slide
│   ├── location-search.js        # AJAX location search + map update
│   ├── smooth-scroll.js          # Product page anchor tab scrolling
│   └── vendor/
│       └── splide.min.js         # Splide.js carousel library (+ splide.min.css)
│
├── templates/
│   ├── layout/
│   │   ├── page.html.twig
│   │   ├── page--front.html.twig
│   │   ├── page--search.html.twig
│   │   ├── page--403.html.twig
│   │   ├── page--404.html.twig
│   │   └── region--header.html.twig
│   ├── navigation/
│   │   ├── menu--main.html.twig           # Desktop mega menu
│   │   └── menu--mobile.html.twig         # Mobile hamburger accordion
│   ├── node/
│   │   ├── node--flexible-page--front.html.twig     # Homepage
│   │   ├── node--flexible-page--locations.html.twig # Location Parent
│   │   ├── node--flexible-page--contact.html.twig   # Contact Us + Webform
│   │   ├── node--product--full.html.twig
│   │   ├── node--product--teaser.html.twig
│   │   ├── node--article--full.html.twig
│   │   ├── node--industry--full.html.twig
│   │   ├── node--industry--teaser.html.twig
│   │   ├── node--service--full.html.twig
│   │   ├── node--location--full.html.twig
│   │   ├── node--location--teaser.html.twig
│   │   └── node--landing-page--full.html.twig
│   ├── paragraph/
│   │   ├── paragraph--hero-banner.html.twig
│   │   ├── paragraph--content-block.html.twig
│   │   ├── paragraph--card-grid.html.twig
│   │   ├── paragraph--card-item.html.twig
│   │   ├── paragraph--faq-item.html.twig
│   │   ├── paragraph--cta-block.html.twig
│   │   ├── paragraph--guided-journey.html.twig
│   │   ├── paragraph--gj-tab.html.twig
│   │   ├── paragraph--stats-counter.html.twig
│   │   ├── paragraph--stat-item.html.twig
│   │   ├── paragraph--spec-table.html.twig
│   │   ├── paragraph--spec-row.html.twig
│   │   ├── paragraph--video-block.html.twig
│   │   ├── paragraph--rentals-connected.html.twig
│   │   ├── paragraph--accessories-grid.html.twig
│   │   ├── paragraph--product-grid.html.twig
│   │   ├── paragraph--checklist-item.html.twig
│   │   └── paragraph--open-hours-row.html.twig
│   ├── views/
│   │   ├── views-view--products-listing.html.twig
│   │   ├── views-view-unformatted--products-listing.html.twig
│   │   ├── views-view--industries-listing.html.twig
│   │   ├── views-view--locations-directory.html.twig
│   │   └── views-view--search-results.html.twig
│   └── field/
│       └── field--field-images.html.twig
│
├── images/
│   ├── logo.svg
│   ├── logo-white.svg
│   └── alwayson-icon.svg
│
└── screenshot.png
```

---

## 10. Docker Setup

```
bearcom/                               # D:\Work\bearcom (Win) or ~/Work/bearcom (Linux)
├── docker-compose.yml
├── .env
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── Dockerfile
├── drupal/
│   ├── composer.json
│   ├── composer.lock
│   ├── config/
│   │   └── sync/                    # Drupal config export
│   └── web/
│       ├── themes/custom/bearcom/
│       └── modules/custom/
└── README.md
```

### Services:
- **nginx** — reverse proxy, port 80
- **php** — PHP 8.2+ FPM with Drupal extensions
- **db** — MariaDB 10.6+
- **redis** — cache backend
- **mailhog** (dev) — email testing

---

## 11. Implementation Plan

### How the split works

```
Developer 1 (Backend) creates the DATA STRUCTURES in Drupal admin:
  → Content types, fields, paragraph types, taxonomies, Views, Webforms, modules, config

Developer 2 (Frontend) creates the VISUAL OUTPUT from those structures:
  → Twig templates, CSS, JS, responsive behavior

DEPENDENCY: Dev 1 must create a content type/paragraph BEFORE Dev 2 can write its Twig template.
Within each phase, Dev 1 works on the first half of the week, Dev 2 joins once structures exist.
Dev 2 can work on CSS/JS components in parallel (they don't need Drupal structures).
```

### Phase 1 — Foundation (Week 1–2)

> **⚠ Dev 1 starts first.** Dev 2 can begin theme scaffolding on local files, but needs Docker running to preview.

**Developer 1 (Backend) — Week 1:**
- [ ] Docker Compose setup (nginx + php + mariadb + redis)
- [ ] Drupal install via Composer
- [ ] Enable & configure contrib modules
- [ ] Config export/import workflow setup
- [ ] Create taxonomy vocabularies (`product_category`, `industry_tax`, `state_province`)
- [ ] Set up Image Styles (all 15 defined in §7.7)
- [ ] Create Main Menu structure in Drupal admin (Solutions, Rentals, Industries, Resources, About + all children)

**Developer 2 (Frontend) — starts Week 1 (files), full speed Week 2 (with Drupal running):**
- [ ] Custom theme scaffolding (`bearcom.info.yml`, `bearcom.libraries.yml`)
- [ ] Design tokens as CSS variables (`_variables.css`)
- [ ] Base styles: reset, typography, grid system
- [ ] Header component: top bar + navigation (desktop)
- [ ] Mega menu templates + `mega-menu.js` (hover dropdown with delay)
- [ ] Search expand in header (`search-expand.js` + CSS transition)
- [ ] Footer component (full variant + minimal variant)
- [ ] Mobile hamburger menu (full-screen overlay, 3-level accordion) + `mobile-menu.js`

---

### Phase 2 — Homepage + Catalog (Week 3–4)

> **Dev 1 creates CTs + Paragraphs early in week 3.** Dev 2 starts CSS/JS components immediately, writes Twig once structures exist.

**Developer 1 (Backend):**
- [ ] Flexible Page content type (`flexible_page`) with all fields
- [ ] Create Homepage node + set as Drupal front page
- [ ] Paragraph types: `hero_banner`, `cta_block`, `card_grid`, `card_item`, `stats_counter`, `stat_item`, `rentals_connected`, `guided_journey`, `gj_tab`, `checklist_item`, `product_grid`, `faq_item`, `content_block`
- [ ] Product content type (`product`) with all fields including `field_price`
- [ ] Products Listing View (grid 3×3 + exposed filters via BEF + AJAX + pager)
- [ ] Create 3-5 sample Product nodes for testing

**Developer 2 (Frontend):**
- [ ] Hero Banner template — 3 style variants (product / image / solid color)
- [ ] Product Card + Industry Card CSS components
- [ ] CTA Block template (default + orange variants)
- [ ] Guided Journey Block template + `tabs.js`
- [ ] Stats Counter template + `stats-counter.js` (count-up animation)
- [ ] Rentals/Connected Services template
- [ ] **Homepage** — full page assembly (`node--flexible-page--front.html.twig`)
- [ ] Filter Sidebar CSS + Pagination component
- [ ] **Category Page** — Views template + filters + grid + FAQ accordion

---

### Phase 3 — Product Page (Week 5)

> **Parallel work possible.** Dev 1 adds remaining paragraphs + Views. Dev 2 builds complex JS components.

**Developer 1 (Backend):**
- [ ] Paragraph types: `spec_table`, `spec_row`, `accessories_grid`, `video_block`
- [ ] Product content type: finalize all paragraph references + field_group organization
- [ ] Related Products View (carousel, same category, exclude current node)
- [ ] Create 2-3 fully populated Product nodes with specs, accessories, FAQ for testing

**Developer 2 (Frontend):**
- [ ] Tab Navigation component + `smooth-scroll.js` (anchor links with active state tracking)
- [ ] Product image gallery (multi-image display, optional lightbox)
- [ ] Specs Table template (expandable/collapsible sections) + `accordion.js`
- [ ] Related Products Carousel + `carousel.js`
- [ ] Accessories Grid template (cards that link to Category Page with filter params)
- [ ] **Product Page** — full page assembly (`node--product--full.html.twig`)

---

### Phase 4 — Content Pages (Week 6–7)

> **Most parallel phase.** Many CTs + many templates, minimal dependencies between pages.

**Developer 1 (Backend):**
- [ ] Article content type (`article`) with all fields
- [ ] Industry content type (`industry`) with all fields including `field_hero_image`
- [ ] Service content type (`service`) with all fields
- [ ] Paragraph types: `content_block`, `faq_item` (already created in Phase 2 — verify)
- [ ] Industries Listing View (grid 3×3 of Industry teasers)
- [ ] Webform: "Contact Us Form" (Name, Email, Phone, Company, State, ZIP, Question type, Message, Resale radio, reCAPTCHA v2) + AJAX submit + confirmation message
- [ ] Flexible Page node for Contact Us at `/contact-us` with webform reference
- [ ] View `related_articles`: Block, latest articles, exclude current, limit 4
- [ ] View `related_services`: Block, other services, exclude current, limit 4
- [ ] Create sample content: 2 Articles, 3 Industries, 2 Services

**Developer 2 (Frontend):**
- [ ] Content Block (L/R) template (alternating image left/right)
- [ ] FAQ Accordion component + `accordion.js` (reused from Product Page)
- [ ] Share Buttons component (Facebook, Twitter, LinkedIn, Email)
- [ ] **Article template** — v1 (image hero) + v2 (blue hero) via `field_hero_style`
- [ ] **Industry Parent Page** — Views template (grid of Industry teasers)
- [ ] **Industry Landing Page** — `node--industry--full.html.twig`
- [ ] **Service Landing Page** — `node--service--full.html.twig`
- [ ] **Contact Us Page** — split layout template (left text + right webform)

---

### Phase 5 — Locations + Marketing (Week 7–8)

> **Most complex backend phase.** Custom module + AJAX + maps. Dev 2 has many simpler templates.

**Developer 1 (Backend):**
- [ ] Location content type (`location`) with all fields including `field_state` taxonomy
- [ ] Paragraph type: `open_hours_row`
- [ ] Geofield + Leaflet module configuration
- [ ] Locations Directory View (grouped by State/Province, 5-column layout)
- [ ] **Custom module `bearcom_locations`:** REST endpoint for AJAX location search (input: ZIP/City, output: JSON with name, address, coordinates)
- [ ] Flexible Page node for Location Parent at `/locations` + embed View blocks + Leaflet map block
- [ ] Landing Page content type (`landing_page`) with all fields
- [ ] Webform: "Lead Capture Form" (9 fields + submit)
- [ ] Metatag configuration (global defaults + per content type overrides)
- [ ] Pathauto patterns for all content types (see §7.6)
- [ ] Redirect module setup

**Developer 2 (Frontend):**
- [ ] Open Hours Table component (styled `paragraph--open-hours-row.html.twig`)
- [ ] Location Card (mini) component (orange left border, "More Info" link)
- [ ] `location-search.js` — AJAX search form, renders results + updates Leaflet map markers
- [ ] Interactive Leaflet map styling (custom orange pin markers, popup on click)
- [ ] State Directory layout (5-column CSS grid, state names orange bold)
- [ ] **Location Parent Page** — `node--flexible-page--locations.html.twig`
- [ ] **Location Page** template (v1 orange hero + v2 grey hero)
- [ ] Lead Form styling (Webform CSS)
- [ ] **Paid Search Landing Page** — `node--landing-page--full.html.twig` (minimal header/footer)
- [ ] **403 Page** — `page--403.html.twig`
- [ ] **404 Page** — `page--404.html.twig`

---

### Phase 6 — Search + Responsive + Polish (Week 9–10)

> **Both devs polish together.** Dev 1 handles backend optimization. Dev 2 handles responsive. Both do QA.

**Developer 1 (Backend):**
- [ ] Search API configuration (DB backend) + content indexing
- [ ] Search Results View (title + excerpt with keyword highlight + pager)
- [ ] Config export & environment split (dev/staging/prod)
- [ ] Redis cache backend configuration
- [ ] Performance: CSS/JS aggregation, image lazy loading
- [ ] Robots.txt, sitemap.xml (via `simple_sitemap` module)
- [ ] Security review (user permissions, text formats, file upload restrictions)
- [ ] **Help Dev 2 with responsive QA** — test on real devices, fix backend-side issues

**Developer 2 (Frontend):**
- [ ] **Search Results Page** template + search keyword highlighting CSS
- [ ] Responsive: mobile breakpoint (<1024px) for ALL 15 page templates
- [ ] Responsive: tablet breakpoint (768–1024px) — our design, based on common sense
- [ ] Verify mobile hamburger menu works on all pages
- [ ] Hover / active / focus states for all interactive elements (buttons, cards, links, form fields)
- [ ] Cross-browser testing (Chrome, Firefox, Safari, Edge)
- [ ] Accessibility audit (ARIA labels, keyboard navigation, color contrast)
- [ ] **Final QA pass with Dev 1** — both devs review all 15 pages together

---

## 12. Typography Scale

Font: **Roboto Condensed** (Google Fonts)

### Desktop (≥ 1024px)

| Element | Font Size | Line Height | Weight |
|---------|-----------|-------------|--------|
| H1 | 58px | 64px | 600 |
| H2 | 42px | 44px | 600 |
| H3 | 34px | 36px | 600 |
| H4 | 26px | 28px | 600 |
| H5 | 22px | 24px | 400 |
| Paragraph | 16px | 20px | 400 |
| Details | 16px | 18px | 400 |

### Mobile (< 1024px)

| Element | Font Size | Line Height | Weight |
|---------|-----------|-------------|--------|
| H1 | 38px | 40px | 600 |
| H2 | 30px | 32px | 600 |
| H3 | 24px | 26px | 600 |
| H4 | 20px | 22px | 600 |
| H5 | 18px | 20px | 400 |
| Paragraph | 16px | 24px | 400 |
| Details | 16px | 18px | 400 |

---

## 13. Open Questions

### Resolved
- [x] ~~Font family~~ → Roboto Condensed
- [x] ~~Font weights~~ → 600 (h1–h4), 400 (h5, paragraph, details)
- [x] ~~Navigation dropdowns~~ → Hover = dropdown submenu, Click = goes to page. All 5 dropdowns analyzed.
- [x] ~~US/Canada switcher~~ → Switches region content (at least map on homepage). Exact scope TBD
- [x] ~~Redesign~~ → Yes, this is a redesign of bearcom.com
- [x] ~~Mobile mockups~~ → Analyzed: Homepage, Category, Product, Industry, Paid Search, Filters overlay
- [x] ~~Location Parent Page~~ → Branch Locator & Directory with search, map, state directory
- [x] ~~Search Results Page~~ → Search input + results list (title + excerpt with highlight) + pagination
- [x] ~~Mobile hamburger menu~~ → Full-screen overlay, 3-level accordion, orange CTA block at bottom
- [x] ~~Contact Us page~~ → Split layout with Webform (Name, Email, Phone, Company, State, ZIP, Question type, Message, Resale radio, reCAPTCHA)

### Decisions made
- **Breakpoints:** Desktop (≥1024px), Tablet (768–1023px), Mobile (<768px). CSS breakpoints: `1023px` and `767px` (see §0 Правила розробки)
- **Hover/focus/active states:** Our discretion, consistent with design language
- **Empty states:** Simple text message ("No results found", "No products match your filters", etc.)
- **Git:** GitHub (`Worgzz333r/bearcom`), deploy key on production server
- **Local domain:** `localhost` (Docker default, port 80)
- **Content volume:** Unknown, design for scalability (Views with pager, lazy loading)

### TBD (decide later, not blocking development)
- [ ] Newsletter form — integration service (Mailchimp, HubSpot, other?)
- [ ] Lead form / Contact form — where do submissions go? (email, CRM?)
- [ ] Analytics — Google Analytics / Tag Manager?
- [ ] Cookie consent banner needed?
- [ ] Content migration from current bearcom.com?
- [x] ~~Hosting environment for production~~ → Server set up, deploy key added, `scripts/deploy.sh` ready
- [ ] CI/CD pipeline (auto-deploy on git push — can set up later if needed)
- [x] ~~Bitbucket repository setup~~ → Using GitHub: `Worgzz333r/bearcom`

---

## 14. Useful Links

- Figma mockups: *(add link here)*
- Staging URL: *(TBD)*
- Git repository: [github.com/Worgzz333r/bearcom](https://github.com/Worgzz333r/bearcom)
- Current site: [bearcom.com](https://www.bearcom.com)

---

*Last updated: 2026-03-17*
