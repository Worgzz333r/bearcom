# План B — Поділ по сторінках (Full-stack)

> **Dev 1** і **Dev 2** — кожен бере свої сторінки і робить їх повністю (CT + параграфи + Twig + CSS + JS).
> Тиждень 1 — спільний. Після цього — незалежна робота.
>
> Технічні деталі (machine names, поля, структура) → `PROJECT_PLAN.md`

---

### Відхилення від плану

| Дата | Що | Хто | Деталі |
|------|-----|-----|--------|
| 2026-03-18 | Модулі встановлені Dev 2 замість спільно | Dev 2 | Встановлено всі 27 contrib-модулів (включно з simple_sitemap, config_split, search_api_db, media_library_form_element, responsive_image). За планом це мав бути спільний таск Тижня 1. |
| 2026-03-18 | Image styles — Dev 1 | Dev 1 | 15 стилів зображень робить Dev 1 самостійно |

---

## Як працюємо разом

```
Кожен робить свої сторінки від А до Я — і бекенд, і фронтенд.
Config sync: перед роботою git pull → drush cim, після — drush cex → git push.
Правило: НЕ чіпай CT/параграфи іншого дева. Якщо треба — пиши в чат.
```

**Спільні параграфи** (використовуються обома):
- `faq_item`, `cta_block`, `checklist_item`, `content_block` — хто першим створив, того і параграф. Другий використовує готовий.

---

## Тиждень 1 — Спільний фундамент

**Ціль:** Сайт з темою, хедером, футером, меню, таксономіями. Готова база для незалежної роботи.

> Працюєте разом, ділите таски між собою як зручно. Головне — все зробити до кінця тижня.

### Конфігурація (розділіть між собою)

- [ ] Включити всі модулі (paragraphs, admin_toolbar, pathauto, metatag, webform, search_api, twig_tweak, field_group, focal_point, geofield, leaflet, redirect, config_split, redis, captcha, recaptcha, BEF, views_ajax_history, address, entity_reference_revisions, simple_sitemap)
- [ ] Config export/import воркфлоу — перший `drush cex`, домовитись про процес
- [ ] Таксономія `product_category` — терми (Two-Way Radios, Accessories, Chargers, Earpieces, Antennas, Batteries, Cases, Microphones, Headsets)
- [ ] Таксономія `industry_tax` — терми (Healthcare, Construction, Education, Hospitality, Manufacturing, Government, Retail, Transportation, Warehouse)
- [ ] Таксономія `state_province` — 50 штатів США + 13 провінцій Канади
- [ ] 15 стилів зображень (розміри в PROJECT_PLAN §7.7): `hero_desktop` (1920×600), `hero_mobile` (768×400), `product_card` (400×400), `product_gallery` (800×800), `product_thumb` (100×100), `industry_card` (400×300), `content_block` (600×400), `cta_block` (500×350), `location_photo` (600×400), `rental_card` (500×350), `article_hero` (1200×500), `article_inline` (800×auto), `landing_image` (600×auto), `gj_tab_image` (500×350), `logo_icon` (80×80)
- [ ] Головне меню — повна структура:
  - Solutions → Voice (дочірні), Security (дочірні), Data (дочірні)
  - Rentals (дочірні), Industries, Resources, About

### Тема (розділіть між собою)

- [ ] Кастомна тема `bearcom` — `bearcom.info.yml`, `bearcom.libraries.yml`, `bearcom.theme`
- [ ] `css/base/variables.css` — CSS custom properties:
  - Кольори: `--color-primary: #FC5000`, `--color-dark: #22262C`, `--color-text: #4A4F55`, `--color-light-gray: #F5F5F5`, `--color-medium-gray: #D9D9D9`, `--color-white: #FFFFFF`, `--color-blue: #0057A0`, `--color-green: #28A745`, `--color-error: #FB591F`
  - Шрифт: `--font-family: 'Roboto Condensed', sans-serif`
  - Відступи, контейнер
- [ ] `css/base/reset.css`, `css/base/typography.css` (H1-H5 desktop+mobile за §12), `css/base/grid.css`
- [ ] **Хедер** — top bar (пошук, телефон, локації, US/Canada) + навігація + Contact Us
- [ ] **Мега-меню** — ховер-дропдауни з помаранчевою панеллю + `js/mega-menu.js`
- [ ] **Пошук в хедері** — `js/search-expand.js`
- [ ] **Футер повний** — адреси, посилання, розсилка, соцмережі
- [ ] **Футер мінімальний** — лого + телефон
- [ ] **Мобільне меню** — повноекранний оверлей, 3-рівневий акордеон + `js/mobile-menu.js`

### Кінець тижня 1
- [ ] `drush cex` → git push — зафіксувати спільну базу
- [ ] Перевірити: обидва можуть `git pull → drush cim` без конфліктів
- [ ] **Після цього — розходитесь по своїх сторінках**

---

## Тижні 2–9 — Незалежна робота

### Розподіл сторінок

| | **Dev 1** | **Dev 2** |
|---|---|---|
| **Сторінки** | Homepage, Каталог, Товар, Стаття, Paid Search LP, Пошук | Індустрії (батьківська + окрема), Сервіс, Локації (батьківська + окрема), Контакти, 403, 404 |
| **Типи контенту** | `flexible_page`, `product`, `article`, `landing_page` | `industry`, `service`, `location` |
| **Складність** | Каталог (BEF+AJAX) + Товар (таби, галерея, карусель) | Локації (карта + AJAX + кастомний модуль) |

---

## Dev 1 — Потижневий план

### Тиждень 2 — Головна сторінка

**Ціль:** Головна виглядає як макет `Homepage.png`.

**Бекенд:**
- [ ] Тип контенту `flexible_page` (поля за PROJECT_PLAN §7.1):
  - `field_paragraphs` (Paragraphs: hero_banner, cta_block, card_grid, stats_counter, rentals_connected, guided_journey, content_block, product_grid, video_block, accessories_grid)
  - `field_heading` (Text), `field_description` (Text formatted), `field_image` (Media)
  - `field_benefits` (Paragraphs: Checklist Item)
  - `field_webform` (Webform reference), `field_hero_style` (Select)
- [ ] Параграф `hero_banner`: field_title, field_subtitle, field_image, field_style, field_cta_text, field_cta_url, field_product_image
- [ ] Параграф `cta_block`: field_image, field_title, field_description, field_button_text, field_button_url, field_style (default/orange)
- [ ] Параграф `card_grid` + `card_item`
- [ ] Параграф `stats_counter` + `stat_item`
- [ ] Параграф `rentals_connected`
- [ ] Параграф `guided_journey` + `gj_tab`
- [ ] Параграф `checklist_item`
- [ ] Параграф `product_grid`: field_title, field_view_id (Text), field_limit (Number)
- [ ] Параграф `faq_item`: field_question, field_answer — **створити зараз, потрібен багатьом сторінкам**
- [ ] Нода Homepage → наповнити параграфами → встановити як front page

**Фронтенд:**
- [ ] `paragraph--hero-banner.html.twig` + CSS — 3 стилі (product/image/solid)
- [ ] `paragraph--cta-block.html.twig` + CSS — default + orange
- [ ] `paragraph--card-grid.html.twig` + `paragraph--card-item.html.twig` + CSS
- [ ] `paragraph--stats-counter.html.twig` + CSS + `js/stats-counter.js` (IntersectionObserver анімація)
- [ ] `paragraph--guided-journey.html.twig` + `paragraph--gj-tab.html.twig` + CSS + `js/tabs.js`
- [ ] `paragraph--rentals-connected.html.twig` + CSS
- [ ] `paragraph--checklist-item.html.twig`
- [ ] `node--flexible-page--front.html.twig` — **зібрати головну**

---

### Тиждень 3 — Каталог товарів

**Ціль:** `/products` з працюючими фільтрами, сітка 3×3, AJAX.

**Бекенд:**
- [ ] Тип контенту `product` (поля за PROJECT_PLAN §7.1): field_images (Media multiple), field_price (Decimal), field_short_description (Text formatted), field_body (Paragraphs: content_block, video_block, cta_block, accessories_grid), field_category (Taxonomy: product_category), field_specs + field_additional_specs (Paragraphs: Spec Table), field_accessories + field_related_products (Entity ref → Product), field_faq (Paragraphs: FAQ Item), field_guided_journey (Paragraphs)
- [ ] View `products_listing`: сітка, BEF чекбокси по категорії, AJAX, 9 per page, path `/products`
- [ ] 3–5 тестових товарів

**Фронтенд:**
- [ ] `node--product--teaser.html.twig` + `css/components/product-card.css`
- [ ] `css/components/filter-sidebar.css` — стилізація BEF чекбоксів
- [ ] `css/components/pagination.css`
- [ ] Views-шаблони для Products Listing (sidebar + grid 3×3)

---

### Тиждень 4 — Сторінка товару

**Ціль:** Повна сторінка товару як на макеті.

**Бекенд:**
- [ ] Параграф `spec_table` + `spec_row`
- [ ] Параграф `accessories_grid`
- [ ] Параграф `video_block`
- [ ] Field Group для Product адмінки
- [ ] View `related_products` — block, same category, exclude current, limit 4
- [ ] View `related_articles` — block, latest articles, exclude current, limit 4
- [ ] 2–3 повністю заповнених товари

**Фронтенд:**
- [ ] `node--product--full.html.twig` + `css/pages/product-page.css`
- [ ] `js/smooth-scroll.js` — табова навігація з активним станом
- [ ] Галерея (thumbnails + велике фото)
- [ ] `paragraph--spec-table.html.twig` + `paragraph--spec-row.html.twig` + акордеон JS
- [ ] `paragraph--accessories-grid.html.twig` + CSS
- [ ] `paragraph--video-block.html.twig`
- [ ] Карусель пов'язаних товарів (`js/carousel.js`)

---

### Тиждень 5 — Стаття

**Ціль:** Сторінка статті v1 (image hero) + v2 (blue hero).

**Бекенд:**
- [ ] Тип контенту `article` (поля за PROJECT_PLAN §7.1): field_hero_image, field_hero_style (image/blue), body (Text formatted — core field), field_show_share (Boolean), field_related (Entity ref → Article), field_cta (Paragraphs: CTA Block)
- [ ] Параграф `faq_item` (якщо Dev 2 ще не створив): field_question, field_answer
- [ ] 2 тестові статті

**Фронтенд:**
- [ ] `node--article--full.html.twig` + `css/pages/article-page.css`
  - v1: фонова картинка-герой
  - v2: синій фон #0057A0, Share Buttons
- [ ] `css/components/share-buttons.css`

---

### Тиждень 6 — Paid Search Landing + Пошук

**Ціль:** LP з мінімальним хедером, сторінка пошуку.

**Бекенд:**
- [ ] Тип контенту `landing_page` (поля за PROJECT_PLAN §7.1): field_headline, field_image, field_benefits (Paragraphs: Checklist Item), field_webform (Webform reference), field_cta (Paragraphs: CTA Block), field_minimal_header (Boolean)
- [ ] Webform `lead_capture`: First Name, Last Name, Email, Phone, Company, Job Title, Country, State, Message
- [ ] Search API — DB backend, server + index, індексація
- [ ] View `search_results`: поле пошуку + title (linked) + excerpt highlight + pager, path `/search`
- [ ] Pathauto-патерни: `/products/[node:title]`, `/resources/[node:title]`, `/industries/[node:title]`, `/services/[node:title]`, `/locations/[node:title]`, `/lp/[node:title]`

**Фронтенд:**
- [ ] `node--landing-page--full.html.twig` + `css/pages/landing-page.css` — мінімальний хедер, форма + переваги, мінімальний футер
- [ ] `views-view--search-results.html.twig` + `css/pages/search-results.css` — поле + результати з підсвіткою
- [ ] Стилі Webform Lead Capture

---

### Тижні 7–8 — Адаптив своїх сторінок

**Ціль:** Мобільний і планшетний вигляд для: Homepage, Каталог, Товар, Стаття, LP, Пошук.

- [ ] Мобільний адаптив (<768px) — орієнтуватись на макети `mobile/`
- [ ] Планшет (768–1024px) — наш дизайн
- [ ] Ховер/active/focus стани
- [ ] Перевірити мобільне меню на своїх сторінках

---

## Dev 2 — Потижневий план

### Тиждень 2 — Індустрії

**Ціль:** Батьківська індустрій (сітка карток) + окрема індустрія.

**Бекенд:**
- [ ] Тип контенту `industry` (поля за PROJECT_PLAN §7.1): field_icon (Media), field_hero_image (Media), field_description (Text), field_solutions (Paragraphs: Card Grid), field_cta (Paragraphs: CTA Block)
- [ ] Параграф `content_block`: field_title, field_body, field_image, field_layout (image-left/image-right)
  > Цей параграф використовується і Dev 1 — створи першим або домовтесь
- [ ] View `industries_listing`: Grid 3×3, path `/industries`, CT = industry, teaser
- [ ] 3 тестові індустрії (Healthcare, Construction, Education)

**Фронтенд:**
- [ ] `node--industry--teaser.html.twig` + `css/components/industry-card.css`
- [ ] `paragraph--content-block.html.twig` + `css/components/content-block.css`
- [ ] `views-view--industries-listing.html.twig` — сітка
- [ ] `node--industry--full.html.twig` + `css/pages/industry-page.css`

---

### Тиждень 3 — Сервіс

**Ціль:** Сторінка сервісу — герой + контент-блоки + відео + FAQ + CTA.

**Бекенд:**
- [ ] Тип контенту `service` (поля за PROJECT_PLAN §7.1): field_hero_image (Media), field_hero_style (Select: image/color), field_body (Paragraphs: Content Block L/R), field_video (Paragraphs: Video Block), field_faq (Paragraphs: FAQ Item), field_cta (Paragraphs: CTA Block), field_related (Entity ref → Service, Article)
- [ ] Параграф `faq_item`: field_question, field_answer
  > Якщо Dev 1 вже створив — використай готовий
- [ ] View `related_services` — block, other services, exclude current, limit 4
- [ ] 2 тестові сервіси

**Фронтенд:**
- [ ] `paragraph--faq-item.html.twig` + `css/components/faq.css` + `js/accordion.js`
- [ ] `node--service--full.html.twig` + `css/pages/service-page.css`
  - Герой + контент-блоки (чергуються L/R) + відео + FAQ акордеон + CTA

---

### Тижні 4–5 — Локації (найскладніша частина)

**Ціль:** `/locations` з AJAX-пошуком, картою, довідником. Окрема локація з двома стилями.

**Бекенд:**
- [ ] Тип контенту `location` (поля за PROJECT_PLAN §7.1): field_address (Address), field_phone (Text), field_state (Taxonomy: state_province), field_geo (Geofield), field_open_hours (Paragraphs: Open Hours Row), field_photo (Media), field_about (Text formatted), field_faq (Paragraphs: FAQ Item), field_hero_style (Select: orange/grey)
- [ ] Параграф `open_hours_row`: field_day, field_hours
- [ ] Geofield + Leaflet налаштування
- [ ] View `locations_directory`: grouped by state, 5-column
- [ ] **Кастомний модуль `bearcom_locations`**: REST endpoint `GET /api/locations?q={zip_or_city}` → JSON [{nid, title, address, phone, lat, lon}]
- [ ] Нода Location Parent (`flexible_page` — Dev 1 вже створив цей CT) на `/locations`
- [ ] 3–5 тестових локацій з адресами, координатами, годинниками
- [ ] Metatag — SEO для кожного типу (глобальні + per CT)

**Фронтенд:**
- [ ] `paragraph--open-hours-row.html.twig` + `css/components/open-hours.css`
- [ ] `css/components/location-card.css` — помаранчева ліва рамка
- [ ] `js/location-search.js` + `css/components/location-search.css` — AJAX пошук → картки + Leaflet мітки
- [ ] Стилізація Leaflet — помаранчеві мітки, попапи
- [ ] `css/components/state-directory.css` — 5 колонок
- [ ] `node--flexible-page--locations.html.twig` + `css/pages/location-parent.css`
- [ ] `node--location--full.html.twig` + `css/pages/location-page.css` — v1 (orange) + v2 (grey)

---

### Тиждень 6 — Контакти

**Ціль:** `/contact-us` з працюючою формою, модалка "дякуємо".

**Бекенд:**
- [ ] Webform `contact_us`: First Name, Last Name, Email, Phone, Company, State, ZIP, Question Type, Resale radio, Message, reCAPTCHA v2, AJAX submit
- [ ] Нода Contact Us (`flexible_page` — Dev 1 створив CT) на `/contact-us`, field_webform → webform

**Фронтенд:**
- [ ] `node--flexible-page--contact.html.twig` + `css/pages/contact-us.css`
  - Ліва колонка: текст + переваги (галочки)
  - Права: вебформа
- [ ] `css/components/webform.css` — стилізація інпутів, модалка "Дякуємо"
- [ ] Перевірити: лист приходить в MailHog

---

### Тиждень 7 — 403 + 404

**Ціль:** Сторінки помилок.

- [ ] `page--403.html.twig` + `page--404.html.twig` + `css/pages/error-pages.css`
  - Великий помаранчевий номер, текст, кнопка "Go Home"
  - Мінімальний хедер/футер

> Якщо залишився час — допомагай Dev 1 або починай адаптив своїх сторінок.

---

### Тижні 7–8 — Адаптив своїх сторінок

**Ціль:** Мобільний і планшетний вигляд для: Індустрії, Сервіс, Локації, Контакти, 403, 404.

- [ ] Мобільний адаптив (<768px)
- [ ] Планшет (768–1024px)
- [ ] Ховер/active/focus стани
- [ ] Перевірити мобільне меню на своїх сторінках

---

## Тижні 9–10 — Спільне полірування

### Dev 1 + Dev 2 (разом)

**Бекенд-оптимізація:**
- [ ] Config export фінальний + config_split (dev/prod)
- [ ] Redis як кеш-бекенд
- [ ] CSS/JS агрегація, lazy loading
- [ ] Robots.txt, sitemap.xml
- [ ] Redirect module — налаштувати потрібні редіректи
- [ ] Ревю безпеки: права користувачів, формати тексту, обмеження завантажень

**Кросбраузер + Accessibility:**
- [ ] Chrome, Firefox, Safari, Edge (desktop + mobile)
- [ ] ARIA labels, клавіатурна навігація, контраст кольорів
- [ ] Skip-to-content посилання

**Фінальне QA — разом обійти всі 15 сторінок:**
1. Homepage
2. Category Page (Products Listing)
3. Product Page
4. Industry Parent
5. Industry Landing
6. Service Landing
7. Article v1 + v2
8. Contact Us
9. Location Parent
10. Location Page v1 + v2
11. Paid Search Landing
12. Search Results
13. 403
14. 404

- [ ] Виправити все що знайдете
- [ ] Фінальний `drush cex` → git push

---

## Config sync — правила для двох

Оскільки обидва створюєте CT/параграфи:

1. **Перед роботою:** `git pull` → `drush cim -y`
2. **Після роботи:** `drush export:all -y` → `drush cex -y` → `git add .` → commit → push
3. **Хто першим запушив** — того конфіг. Другий мерджить
4. **Не чіпай чужі CT/параграфи** без домовленості
5. **Спільні параграфи** (`faq_item`, `cta_block`, `checklist_item`, `content_block`): хто створив першим — відразу пушить, другий робить `drush cim`

### Structure Sync (таксономії, меню, блоки)

Таксономічні терми, меню і блоки — це контент, а не конфігурація. Drupal їх не експортує через `drush cex`.
Для синхронізації використовуємо модуль **Structure Sync** + кастомний модуль **bearcom_sync**.

**Як це працює:**
- `drush export:all -y` — зберігає терми/меню/блоки у файл `config/sync/structure_sync.data.yml`
- `drush cex -y` — експортує цей файл разом з рештою конфігурації
- `drush cim -y` — на іншій машині/сервері імпортує конфігурацію, а модуль `bearcom_sync` автоматично запускає імпорт термів/меню/блоків

**Тому завжди перед пушем:**
```bash
drush export:all -y && drush cex -y
git add . && git commit -m "..." && git push
```

**Після git pull / на сервері:**
```bash
drush cim -y    # все підтягнеться автоматично
```

---

## Ризики цього варіанту

| Ризик | Як мінімізувати |
|-------|----------------|
| Config конфлікти | Чітко розділені CT. Пушити часто. Не чіпати чуже |
| Неконсистентні стилі | Спільна база (тиждень 1): змінні, типографіка, компоненти |
| Обидва мають знати Drupal | Якщо хтось слабкий в site-building — краще План A |
| Спільні параграфи | Домовитись хто створює першим. Пушити одразу після створення |

---

## Порівняння навантаження

| | Dev 1 | Dev 2 |
|---|---|---|
| Типи контенту | 4 (flexible_page, product, article, landing_page) | 3 (industry, service, location) |
| Параграфи | ~14 (hero, cta, card_grid, card_item, stats, stat_item, guided_journey, gj_tab, rentals, spec_table, spec_row, accessories, video, checklist, product_grid, faq_item) | ~3 (content_block, open_hours_row + reuse решти) |
| Views | 4 (products_listing, related_products, related_articles, search_results) | 3 (industries_listing, locations_directory, related_services) |
| JS компоненти | stats-counter, tabs, smooth-scroll, carousel | accordion, location-search |
| Кастомний модуль | — | bearcom_locations (REST API) |
| Webforms | lead_capture | contact_us |
| Складне | BEF + AJAX фільтри, табова навігація, галерея | Leaflet карта + AJAX пошук, кастомний модуль |
