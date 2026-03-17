# План A — Поділ по ролях (Backend / Frontend)

> **Dev 1 = Backend** (Drupal конфіг: типи контенту, параграфи, Views, форми, модулі)
> **Dev 2 = Frontend** (Тема: Twig-шаблони, CSS, JS, адаптив)
>
> Технічні деталі (machine names, поля, структура) → `PROJECT_PLAN.md`

---

## Як працюємо разом

```
Dev 1 створює структуру в Drupal → drush cex → git push
Dev 2 робить git pull → drush cim → пише шаблон під цю структуру

Щоранку: git pull → drush cim → працюєш → drush cex → git push
```

**Головна залежність:** Dev 2 не може написати Twig-шаблон для CT/параграфу, поки Dev 1 його не створив. Але Dev 2 може паралельно писати CSS/JS компоненти які не потребують Drupal-структур.

---

## Тиждень 1 — Фундамент

**Ціль:** Сайт з темою, хедером, футером, робочим меню. Можна клікати.

### Dev 1 (Backend)

- [ ] Включити всі модулі (paragraphs, admin_toolbar, pathauto, metatag, webform, search_api, twig_tweak, field_group, focal_point, geofield, leaflet, redirect, config_split, redis, captcha, recaptcha, BEF, views_ajax_history, address, entity_reference_revisions)
- [ ] Налаштувати config export/import воркфлоу — перший `drush cex`, закомітити config/
- [ ] Таксономія `product_category` — наповнити термами (Two-Way Radios, Accessories, Chargers, Earpieces, Antennas, Batteries, Cases, Microphones, Headsets)
- [ ] Таксономія `industry_tax` — наповнити (Healthcare, Construction, Education, Hospitality, Manufacturing, Government, Retail, Transportation, Warehouse)
- [ ] Таксономія `state_province` — всі 50 штатів США + 13 провінцій/територій Канади
- [ ] Створити 13 стилів зображень (розміри в PROJECT_PLAN §7.7): `hero_desktop` (1920×600), `hero_mobile` (768×400), `product_card` (400×400), `product_gallery` (800×800), `product_thumb` (100×100), `industry_card` (400×300), `content_block` (600×400), `cta_block` (500×350), `location_photo` (600×400), `rental_card` (500×350), `article_hero` (1200×500), `article_inline` (800×auto), `logo_icon` (80×80)
- [ ] Головне меню — створити повну структуру:
  - Solutions → Voice Communication (дочірні), Security Solutions (дочірні), Data Solutions (дочірні)
  - Rentals (дочірні пункти)
  - Industries (список індустрій)
  - Resources (Blog, Case Studies, Webinars...)
  - About (Our Story, Careers, Contact Us)

### Dev 2 (Frontend)

- [ ] Кастомна тема `bearcom` — `bearcom.info.yml`, `bearcom.libraries.yml`, `bearcom.theme`
- [ ] `css/base/variables.css` — CSS custom properties:
  - Кольори: `--color-primary: #FC5000`, `--color-dark: #22262C`, `--color-text: #4A4F55`, `--color-light-gray: #F5F5F5`, `--color-medium-gray: #D9D9D9`, `--color-white: #FFFFFF`, `--color-blue: #0057A0`, `--color-green: #28A745`, `--color-error: #FB591F`
  - Шрифт: `--font-family: 'Roboto Condensed', sans-serif`
  - Відступи: `--spacing-xs: 8px` через `--spacing-xxl: 80px`
  - Контейнер: `--container-max: 1440px`, `--container-padding: 20px`
- [ ] `css/base/reset.css` — нормалізація
- [ ] `css/base/typography.css` — H1-H5 desktop + mobile (за таблицею з PROJECT_PLAN §12)
- [ ] `css/base/grid.css` — сітка, контейнер, утиліти
- [ ] `templates/layout/page.html.twig` — базовий лейаут
- [ ] **Хедер** (`templates/layout/header.html.twig` + `css/layout/header.css`):
  - Top bar: іконка пошуку, телефон 1-800-527-1670, Locations посилання, US/Canada перемикач
  - Навігація: лого + пункти меню + кнопка "Contact Us"
- [ ] **Мега-меню** (`templates/menu/menu--main.html.twig` + `css/components/mega-menu.css` + `js/mega-menu.js`):
  - Ховер на пункті → дропдаун з помаранчевою лівою панеллю
  - JS: затримка закриття 200ms, щоб миша могла перейти в підменю
- [ ] **Пошук** (`css/components/search-expand.css` + `js/search-expand.js`):
  - Клік на іконку → поле вводу виїжджає вліво з анімацією
- [ ] **Футер повний** (`templates/layout/footer.html.twig` + `css/layout/footer.css`):
  - Лого, 2 адреси (Dallas TX + Canada), 4 колонки посилань, поле розсилки, іконки соцмереж, копірайт
- [ ] **Футер мінімальний** (`templates/layout/footer--minimal.html.twig`):
  - Тільки лого + телефон (для Landing Page, 403, 404)
- [ ] **Мобільне меню** (`css/components/mobile-menu.css` + `js/mobile-menu.js`):
  - Гамбургер → повноекранний оверлей
  - 3-рівневий акордеон (Solutions → Voice → підпункти)
  - Помаранчевий блок CTA внизу (Request a Quote, Find a Location, Contact Us)

---

## Тиждень 2 — Головна сторінка

**Ціль:** Головна виглядає як макет. Всі секції на місці.

### Dev 1 (Backend)

- [ ] Тип контенту `flexible_page`:
  - `field_hero_title` (Text), `field_hero_subtitle` (Text), `field_hero_image` (Media), `field_hero_style` (Select: product/image/solid), `field_hero_cta_text` (Text), `field_hero_cta_url` (Link)
  - `field_paragraphs` (Paragraphs — unlimited, all types)
  - `field_benefits` (Paragraphs — Checklist Item)
  - `field_form_reference` (Entity reference — Webform)
- [ ] Параграф `hero_banner`: field_title, field_subtitle, field_image, field_style (product/image/solid), field_cta_text, field_cta_url, field_product_image
- [ ] Параграф `cta_block`: field_image, field_title, field_description, field_button_text, field_button_url, field_style (default/orange)
- [ ] Параграф `card_grid`: field_title, field_subtitle, field_cards (Paragraphs: Card Item)
- [ ] Параграф `card_item`: field_icon (Media), field_title, field_description, field_link
- [ ] Параграф `stats_counter`: field_items (Paragraphs: Stat Item)
- [ ] Параграф `stat_item`: field_number (Integer), field_suffix (Text: +, %, K), field_label
- [ ] Параграф `rentals_connected`: field_title, field_cards (Paragraphs: Card Item, max 2)
- [ ] Параграф `guided_journey`: field_title, field_tabs (Paragraphs: GJ Tab)
- [ ] Параграф `gj_tab`: field_tab_title, field_body, field_checklist (Paragraphs: Checklist Item), field_image, field_cta_text, field_cta_url
- [ ] Параграф `checklist_item`: field_text
- [ ] Створити ноду Homepage (flexible_page) → додати всі параграфи з тестовим контентом → встановити як front page (`/admin/config/system/site-information`)

### Dev 2 (Frontend)

- [ ] `paragraph--hero-banner.html.twig` + `css/components/hero.css` — 3 стилі через `field_style`:
  - `product`: темний фон, текст зліва, зображення товару справа
  - `image`: фонова картинка на всю ширину, текст по центру
  - `solid`: суцільний помаранчевий фон, текст по центру
- [ ] `paragraph--cta-block.html.twig` + `css/components/cta-block.css`:
  - `default`: картинка зліва, текст + кнопка справа
  - `orange`: помаранчевий фон, текст + кнопка по центру
- [ ] `paragraph--card-grid.html.twig` + `paragraph--card-item.html.twig` + `css/components/card-grid.css`
- [ ] `paragraph--stats-counter.html.twig` + `paragraph--stat-item.html.twig` + `css/components/stats-counter.css` + `js/stats-counter.js`:
  - IntersectionObserver → коли секція в viewport → числа анімуються від 0 до значення
- [ ] `paragraph--guided-journey.html.twig` + `paragraph--gj-tab.html.twig` + `css/components/guided-journey.css` + `js/tabs.js`:
  - Клік на таб → перемикає контент
- [ ] `paragraph--rentals-connected.html.twig` + `css/components/rentals.css`
- [ ] `paragraph--checklist-item.html.twig` — іконка галочки + текст
- [ ] `node--flexible-page--front.html.twig` — **зібрати головну**: рендерить `field_paragraphs` в правильному порядку
- [ ] Перевірити: все виглядає як макет `Homepage.png`

---

## Тиждень 3 — Каталог товарів

**Ціль:** `/products` — фільтри працюють без перезавантаження, товари в сітці 3×3.

### Dev 1 (Backend)

- [ ] Тип контенту `product`:
  - `field_images` (Media — multiple), `field_price` (Text), `field_body` (Text formatted long), `field_category` (Term ref → product_category), `field_short_description` (Text)
  - `field_specs` (Paragraphs: Spec Table), `field_additional_specs` (Paragraphs: Spec Table)
  - `field_accessories` (Entity ref → Product, multiple), `field_faq` (Paragraphs: FAQ Item)
  - `field_guided_journey` (Paragraphs: Guided Journey), `field_cta` (Paragraphs: CTA Block)
  - `field_related_products` (Entity ref → Product) або View
- [ ] View `products_listing`:
  - Format: Unformatted list
  - Fields: rendered entity (teaser)
  - Filter: `field_category` exposed через BEF (чекбокси)
  - AJAX: увімкнено
  - Pager: 9 items per page
  - Path: `/products`
- [ ] 3–5 тестових товарів з картинками (можна будь-які рації з інтернету)

### Dev 2 (Frontend)

- [ ] `node--product--teaser.html.twig` + `css/components/product-card.css` — картинка, назва, короткий опис, ціна
- [ ] `css/components/filter-sidebar.css` — BEF чекбокси стилізовані: помаранчеві галочки, «Clear Filters» кнопка
- [ ] `css/components/pagination.css` — номери сторінок, стрілки
- [ ] Views-шаблон `views-view--products-listing.html.twig` — лейаут: sidebar (фільтри) + main (сітка 3×3)
- [ ] `views-view-unformatted--products-listing.html.twig` — CSS Grid 3 колонки
- [ ] Перевірити: AJAX фільтрація працює, товари оновлюються без перезавантаження

---

## Тиждень 4 — Сторінка товару

**Ціль:** Повна сторінка товару як на макеті. Таби, специфікації, галерея, FAQ, пов'язані товари.

### Dev 1 (Backend)

- [ ] Параграф `spec_table`: field_title, field_rows (Paragraphs: Spec Row)
- [ ] Параграф `spec_row`: field_label, field_value
- [ ] Параграф `accessories_grid`: field_title, field_items (Entity ref → Product)
- [ ] Параграф `video_block`: field_title, field_video (Media remote video)
- [ ] Product CT — Field Group: організувати поля в вертикальних табах для адмінки (General, Media, Specs, Related, SEO)
- [ ] View `related_products`: Block display, contextual filter = current product's category, exclude current NID, limit 4, random sort
- [ ] 2–3 повністю заповнених товари (всі поля, кілька фото, специфікації, FAQ, аксесуари)

### Dev 2 (Frontend)

- [ ] `node--product--full.html.twig` + `css/pages/product-page.css`:
  - Верхня частина: галерея зліва + назва/ціна/опис справа
  - Табова навігація (якірні посилання: Overview, Specs, Accessories, FAQ)
  - Секції контенту під табами
- [ ] `js/smooth-scroll.js` — клік на таб → плавний скрол до секції, active стан таба оновлюється при скролі (IntersectionObserver)
- [ ] Галерея — велике фото + thumbnails знизу, клік перемикає
- [ ] `paragraph--spec-table.html.twig` + `paragraph--spec-row.html.twig` + `css/components/spec-table.css`:
  - Заголовок-акордеон → клік розгортає таблицю рядків Label: Value
- [ ] `paragraph--accessories-grid.html.twig` + `css/components/accessories.css`:
  - Картки товарів, клік → `/products?category=microphones`
- [ ] `paragraph--video-block.html.twig` — responsive embed
- [ ] Карусель пов'язаних товарів (`js/carousel.js` + `css/components/carousel.css`)
- [ ] Перевірити: все як на макеті `Product Page [visual start].jpg`

---

## Тиждень 5 — Індустрії + Сервіси

**Ціль:** Батьківська індустрій, окрема індустрія, сторінка сервісу — все зверстано.

### Dev 1 (Backend)

- [ ] Тип контенту `industry`:
  - `field_icon` (Media — SVG), `field_hero_image` (Media), `field_hero_title`, `field_body` (Text formatted)
  - `field_solutions` (Paragraphs: Card Grid), `field_cta` (Paragraphs: CTA Block)
- [ ] Тип контенту `service`:
  - `field_hero_title`, `field_hero_subtitle`, `field_hero_image`
  - `field_body` (Paragraphs: Content Block L/R), `field_video` (Media remote video)
  - `field_faq` (Paragraphs: FAQ Item), `field_cta` (Paragraphs: CTA Block)
- [ ] Параграф `content_block`: field_title, field_body (formatted), field_image (Media), field_layout (Select: image-left / image-right)
- [ ] Параграф `faq_item`: field_question (Text), field_answer (Text formatted)
- [ ] View `industries_listing`: Grid 3×3, path `/industries`, content type = industry, teaser display
- [ ] Тестовий контент: 3 індустрії (Healthcare, Construction, Education), 2 сервіси

### Dev 2 (Frontend)

- [ ] `node--industry--teaser.html.twig` + `css/components/industry-card.css` — іконка, назва, опис, «Learn More →»
- [ ] `paragraph--content-block.html.twig` + `css/components/content-block.css`:
  - `field_layout = image-left`: картинка зліва, текст справа
  - `field_layout = image-right`: навпаки
- [ ] `paragraph--faq-item.html.twig` + `css/components/faq.css` + `js/accordion.js`:
  - Клік на питання → розгортає відповідь, іконка +/- обертається
- [ ] `views-view--industries-listing.html.twig` — сітка 3×3 карток
- [ ] `node--industry--full.html.twig` + `css/pages/industry-page.css`:
  - Герой з фоновою картинкою + заголовок
  - Блок тексту + картки рішень (Card Grid) + CTA
- [ ] `node--service--full.html.twig` + `css/pages/service-page.css`:
  - Герой + контент-блоки (чергуються ліво/право) + відео + FAQ акордеон + CTA
- [ ] Перевірити з макетами: `Industry - Parent page.png`, `Industry Landing Page.png`, `Service - Landing page.png`

---

## Тиждень 6 — Стаття + Контакти

**Ціль:** Стаття з двома варіантами, контакти з працюючою формою і модалкою "дякуємо".

### Dev 1 (Backend)

- [ ] Тип контенту `article`:
  - `field_hero_image` (Media), `field_hero_style` (Select: image/blue), `field_body` (Text formatted long)
  - `field_cta` (Paragraphs: CTA Block), `field_category` (Term ref)
- [ ] Webform `contact_us`:
  - Поля: First Name, Last Name, Email, Phone, Company Name, State (select), ZIP Code, Question Type (select), Resale (yes/no radio), Message (textarea)
  - reCAPTCHA v2
  - AJAX submit → повідомлення "Дякуємо!" (або модалка)
  - Email handler → відправка на адмін-емейл
- [ ] Нода Contact Us (`flexible_page`) на path `/contact-us`, field_form_reference → contact_us webform
- [ ] 2 тестові статті (одна з image hero, одна з blue hero)

### Dev 2 (Frontend)

- [ ] `node--article--full.html.twig` + `css/pages/article-page.css`:
  - v1 (`field_hero_style = image`): фонова картинка, заголовок зверху
  - v2 (`field_hero_style = blue`): синій фон #0057A0, Share Buttons справа
- [ ] `css/components/share-buttons.css` — Facebook, Twitter, LinkedIn, Email іконки
- [ ] `node--flexible-page--contact.html.twig` + `css/pages/contact-us.css`:
  - Ліва колонка: заголовок, текст, список переваг (галочки)
  - Права колонка: вебформа
- [ ] Стилізація Webform (`css/components/webform.css`):
  - Інпути, селекти, textarea, radio, кнопка submit (помаранчева)
  - Модалка/повідомлення "Дякуємо" після сабміту
- [ ] Перевірити: форма відправляється, лист приходить в MailHog (localhost:8025)

---

## Тиждень 7 — Локації

**Ціль:** `/locations` з AJAX-пошуком, картою Leaflet, довідником по штатах. Окрема локація.

### Dev 1 (Backend)

- [ ] Тип контенту `location`:
  - `field_address` (Address), `field_phone` (Text), `field_state` (Term ref → state_province)
  - `field_geofield` (Geofield — lat/lon), `field_open_hours` (Paragraphs: Open Hours Row)
  - `field_images` (Media — multiple), `field_faq` (Paragraphs: FAQ Item)
  - `field_hero_style` (Select: orange/grey)
- [ ] Параграф `open_hours_row`: field_day (Text), field_hours (Text)
- [ ] Geofield + Leaflet модуль — налаштувати
- [ ] View `locations_directory`: grouped by field_state, 5-column layout, path = block
- [ ] **Кастомний модуль `bearcom_locations`**:
  - REST resource: `GET /api/locations?q={zip_or_city}`
  - Query: шукає Location nodes за ZIP або містом в field_address
  - Response: JSON array [{nid, title, address, phone, lat, lon}]
- [ ] Нода Location Parent (`flexible_page`) на `/locations` — вбудувати View block + Leaflet map block
- [ ] 3–5 тестових локацій з адресами, координатами, годинниками

### Dev 2 (Frontend)

- [ ] `paragraph--open-hours-row.html.twig` + `css/components/open-hours.css`
- [ ] `css/components/location-card.css` — міні-картка: назва, адреса, телефон, "More Info", помаранчева ліва рамка
- [ ] `js/location-search.js` + `css/components/location-search.css`:
  - Поле вводу (ZIP/місто) + кнопка "Search"
  - Fetch → `/api/locations?q=...`
  - Результат: оновити список карток + мітки на карті Leaflet
- [ ] Стилізація Leaflet: помаранчеві мітки (custom icon), попап з назвою + адресою
- [ ] `css/components/state-directory.css` — 5 колонок CSS Grid, штати помаранчевим bold, під ними — лінки на локації
- [ ] `node--flexible-page--locations.html.twig` + `css/pages/location-parent.css`:
  - Пошукова секція зверху
  - Карта Leaflet
  - Довідник по штатах
- [ ] `node--location--full.html.twig` + `css/pages/location-page.css`:
  - v1: помаранчевий герой + фото + деталі + годинники + карта + FAQ
  - v2: сірий герой (аналогічна структура)

---

## Тиждень 8 — Landing + Пошук + Помилки

**Ціль:** Paid Search LP, сторінка пошуку, 403/404 — залишок сторінок.

### Dev 1 (Backend)

- [ ] Тип контенту `landing_page`:
  - `field_hero_title`, `field_hero_image`, `field_benefits` (Paragraphs: Checklist Item)
  - `field_form_reference` (Entity ref → Webform), `field_cta` (Paragraphs: CTA Block)
- [ ] Webform `lead_capture`: First Name, Last Name, Email, Phone, Company, Job Title, Country, State, Message
- [ ] Search API — налаштувати DB backend, створити server + index, проіндексувати весь контент
- [ ] View `search_results`: Full text search input, fields = title (linked) + excerpt with highlight, pager, path `/search`
- [ ] Pathauto-патерни:
  - Product: `/products/[node:title]`
  - Article: `/resources/[node:title]`
  - Industry: `/industries/[node:title]`
  - Service: `/solutions/[node:title]`
  - Location: `/locations/[node:title]`
- [ ] Metatag — глобальні дефолти + override для кожного CT

### Dev 2 (Frontend)

- [ ] `node--landing-page--full.html.twig` + `css/pages/landing-page.css`:
  - Мінімальний хедер (лого + телефон)
  - Герой з формою справа + переваги (галочки) зліва
  - Мінімальний футер
- [ ] `views-view--search-results.html.twig` + `css/pages/search-results.css`:
  - Поле пошуку зверху
  - Результати: заголовок-посилання + уривок з підсвіченими ключовими словами
  - Пагінація
- [ ] `page--403.html.twig` + `css/pages/error-pages.css`:
  - Великий помаранчевий «403», текст "Access Denied", кнопка "Go Home"
- [ ] `page--404.html.twig`:
  - Великий помаранчевий «404», текст "Page Not Found", кнопка "Go Home"
- [ ] Перевірити: пошук працює, LP рендериться з мінімальним хедером

---

## Тиждень 9 — Адаптив + SEO

**Ціль:** Всі 15 сторінок виглядають добре на мобілці і планшеті.

### Dev 1 (Backend)

- [ ] Config export фінальний + config_split (dev/prod)
- [ ] Redis як кеш-бекенд — налаштувати `settings.php`
- [ ] Robots.txt конфігурація
- [ ] Sitemap.xml (через simple_sitemap або вручну)
- [ ] Ревю безпеки: права анонімних/авторизованих користувачів, текстові формати, обмеження завантажень
- [ ] Перевірити всі Webforms — листи приходять, reCAPTCHA працює
- [ ] Допомагати Dev 2 з QA — тестувати на реальних пристроях

### Dev 2 (Frontend)

- [ ] **Мобільний адаптив** (<768px) для всіх 15 сторінок:
  - Головна, Каталог, Товар, Батьківська індустрій, Індустрія, Сервіс, Стаття, Контакти, Батьківська локацій, Локація, Landing Page, Пошук, 403, 404
  - Орієнтуватись на мобільні макети з папки `mobile/`
- [ ] **Планшетний адаптив** (768–1024px) — дизайнимо самі:
  - 2 колонки замість 3, зменшені відступи, адаптивні герої
- [ ] Перевірити мобільне меню на кожній сторінці
- [ ] CSS/JS агрегація — увімкнути для тестування продуктивності

---

## Тиждень 10 — Полірування + QA

**Ціль:** Сайт готовий до показу. Все бездоганно.

### Dev 1 + Dev 2 (разом)

- [ ] Ховер / active / focus стани: кнопки, картки, посилання, поля форм, меню
- [ ] Lazy loading картинок (Drupal `loading="lazy"`)
- [ ] Кросбраузерне тестування: Chrome, Firefox, Safari, Edge (desktop + mobile)
- [ ] Accessibility:
  - ARIA labels на інтерактивних елементах
  - Клавіатурна навігація (Tab, Enter, Escape)
  - Контраст кольорів (помаранчевий на білому = перевірити!)
  - Skip-to-content посилання
- [ ] **Фінальне QA — разом обійти всі 15 сторінок:**
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

## Ризики цього варіанту

| Ризик | Як мінімізувати |
|-------|----------------|
| Dev 2 чекає Dev 1 | Dev 1 створює CT/параграфи на початку тижня, Dev 2 пише CSS/JS паралельно |
| Config конфлікти | Тільки Dev 1 править конфіг. Dev 2 тільки робить `drush cim` |
| Dev 1 застряг | Dev 2 працює над CSS компонентами які не потребують Drupal структур |
