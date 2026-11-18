# Laravel Project Build Instructions for the Coding Agent

## City Telecom Service — Public Website, CMS, Administration Panel, and Future Engineer Workspace

**Document type:** Implementation brief for an autonomous coding agent  
**Source brief:** `website_redesign_agent_brief.md`  
**Source website:** <https://new.p-svs.ru/> (the former `p-svs.ru` domain redirects to it)  
**Primary language of the website and admin UI:** Russian  
**Instruction language:** English  
**Product context:** Regional ISP operating in the Krymsk and Abinsk districts of Krasnodar Krai, Russia  
**Implementation approach:** Build a maintainable Laravel application with a public website and a role-based CMS/admin panel. The engineer network map is a later phase; prepare the navigation and access boundaries now, but do not fake or prematurely implement network data.

---

# 1. Your Role and Overall Objective

You are a senior Laravel engineer and solution architect. Your task is to inspect the existing repository, understand the current implementation, and then build or evolve the project according to this brief.

The result must be a production-oriented website for **City Telecom Service** with:

1. A modern, responsive public-facing website in Russian.
2. A secure administration panel where authorized employees can manage the website's business data and content without editing source code.
3. A proper content management system for news, promotions, static pages, help articles, documents, images, and sliders.
4. Management of tariffs, tariff availability, technologies, locations, and prices from one source of truth.
5. Management of internal admin users, roles, and granular permissions.
6. Lead/request management for connection requests, business enquiries, and contact forms.
7. A clearly separated **Engineer Workspace** entry point. In the initial release it is only a protected placeholder; the interactive network map and network inventory are future-phase functionality.

Do not treat this as a landing-page-only task. The CMS, data model, permissions, editorial workflow, and content relationships are core deliverables.

## 1.1 Mandatory working rules

- **Inspect before changing.** First inspect the repository, its Git status, current Laravel/PHP versions, database, frontend stack, installed packages, authentication, routing, existing CMS, tests, and deployment files.
- If a working project already exists, extend it safely. Do not replace its architecture or upgrade major dependencies without a concrete reason and an explicit technical note.
- If the repository is empty, initialize a new Laravel application using the baseline in Section 2.
- Prepare a short implementation plan and proposed entity/relationship map before making substantial changes. Then proceed with implementation; do not stop after writing a plan.
- Work in small, verifiable steps. Run migrations, formatting/static checks where configured, and tests after each logical stage.
- Never silently remove existing behavior, routes, content, uploads, or integrations.
- Never invent production prices, service conditions, coverage, legal details, support hours, contact information, connection times, SLA promises, or payment integrations.
- When source data is uncertain, create an editable field/configuration and mark the value as requiring confirmation. Do not publish fictional production values.
- Keep code identifiers, classes, database columns, routes, and permission keys in English. All visible website and admin-panel labels, navigation, validation messages, and seeded editorial examples must be in Russian.
- Do not create a local subscriber personal account or reproduce billing functionality. The existing subscriber cabinet is an external system unless the owner explicitly authorizes and documents an integration.
- Do not create a fake network map, fake devices, fake fiber routes, or fabricated coverage results.
- Do not expose internal notes, draft records, private documents, lead personal data, or admin URLs through public endpoints.
- At the end of each phase, report what was implemented, migrations/packages added, tests executed, remaining questions, and how to run the application.

---

# 2. Technology Baseline

## 2.1 New-project baseline

For a genuinely new project, use the following baseline unless repository or hosting constraints require otherwise:

- **PHP:** 8.3 or newer; prefer a currently supported PHP 8.4/8.5 runtime where the deployment environment supports it.
- **Laravel:** Laravel 13, latest compatible patch release at implementation time.
- **Admin panel:** Filament 5, latest compatible patch release.
- **Database:** MySQL 8 or a currently supported MariaDB version. Select one after checking the actual hosting environment; do not use SQLite as the production database.
- **Public frontend:** Laravel Blade, Vite, Tailwind CSS, and Alpine.js only where small interactions are needed. Prefer server-rendered pages and avoid introducing a SPA without a strong requirement.
- **Admin frontend:** Filament / Livewire.
- **Authorization:** Laravel authorization policies and gates, with `spatie/laravel-permission` for roles and granular permissions if it is compatible with the selected Laravel version.
- **Media management:** `spatie/laravel-medialibrary` or an equivalent actively maintained solution compatible with the selected stack. Do not add a package merely for its name; verify its current compatibility and security status.
- **Rich text:** Filament's supported RichEditor where it meets the editorial requirements. If its image workflow or required formatting is insufficient, integrate a maintained editor (for example, a TipTap-based editor) through a secure, documented upload endpoint.
- **Queues and scheduled tasks:** Laravel queues for image conversions, notifications, and other asynchronous work; Laravel scheduler for scheduled publishing and expiring promotions where appropriate.
- **Testing:** Pest or PHPUnit, following the repository's existing convention.

Check the current official documentation and package compatibility before installing dependencies. Pin compatible major versions in Composer and commit `composer.lock`.

## 2.2 Existing-project rule

If the repository already uses a supported Laravel version, do not automatically upgrade it to Laravel 13. First assess:

- current framework and PHP versions;
- package compatibility;
- current authentication and authorization model;
- whether an admin panel or CMS already exists;
- database schema and existing content;
- deployment/hosting limitations.

Recommend a framework upgrade only when it materially improves maintainability/security and can be performed without putting existing production behavior at risk.

## 2.3 Architectural principles

- Use standard Laravel MVC, Eloquent relationships, Form Requests, policies, jobs, notifications, events, and service classes where they add real value.
- Keep controllers and Filament resources focused. Move multi-step business logic into actions/services.
- Avoid unnecessary repository abstractions, generic CRUD frameworks, premature microservices, and over-engineering.
- Use database transactions for operations that update multiple related records.
- Use enums or equivalent constrained values for fixed statuses and content types.
- Use explicit foreign keys, indexes, unique constraints, and database-level integrity rules.
- Use soft deletes only where restoration/history is meaningful; do not soft-delete every table by default.
- Keep secrets in environment variables. Never commit production credentials.
- Keep the public website and the admin panel in the same Laravel application unless inspection reveals a compelling reason not to.

---

# 3. Product Scope and User Journeys

The website must make four primary visitor journeys clear:

### 3.1 New residential customer

1. Understand which internet technologies/services are offered.
2. Check an address or request a manual coverage check.
3. Review available tariffs and their complete pricing conditions.
4. Submit a connection request.
5. Receive confirmation that the request was received — not a false guarantee that connection is available.

### 3.2 Existing subscriber

1. Open the existing external personal account.
2. Find payment instructions and other service information.
3. Find documents, router instructions, speed-test guidance, and troubleshooting.
4. Contact support if self-service does not solve the issue.

### 3.3 Customer with a network/service problem

1. Find current outage or planned-maintenance announcements.
2. Understand affected locations/services and the current status.
3. Open a basic troubleshooting article.
4. Contact the company through a confirmed channel.

### 3.4 Business customer

1. Review only the business services that the company actually provides.
2. Submit a business enquiry with company, contact, address, and requirement details.
3. The enquiry becomes a manageable lead in the admin panel.

---

# 4. Public Website Information Architecture

Build the public navigation around the following recommended structure. Do not create empty pages solely to fill the sitemap. A section must either have real content or remain unpublished/hidden until ready.

## 4.1 Main navigation

1. **Интернет для дома** — `/internet/`
2. **Интернет для бизнеса** — `/business/`
3. **Тарифы** — `/tariffs/`
4. **Покрытие / Проверить адрес** — `/coverage/`
5. **Акции** — `/promotions/`
6. **Абонентам** — `/clients/`
7. **Новости и работы на сети** — `/news/`
8. **Контакты** — `/contacts/`

The header must also expose, where appropriate:

- confirmed contact phone number(s), using `tel:` links;
- office hours and support hours as separate values if they differ;
- primary CTA: **Проверить подключение**;
- external **Личный кабинет** link;
- **Оплатить** link/action only when its destination and payment method are confirmed.

On mobile, use a compact header and a small fixed action area with no more than 2–3 primary actions, such as connection request, call, and personal cabinet.

## 4.2 Required public routes and sections

| Route | Purpose | Required content/capabilities |
|---|---|---|
| `/` | Home page | Hero, address check/request, popular tariffs, services, benefits based on verified facts, connection steps, coverage, active promotions, subscriber shortcuts, latest news/network updates, contacts. |
| `/internet/` | Home internet | GPON and WiMAX explanations, relevant residential offerings, equipment/connection information only when confirmed, CTA. |
| `/business/` | Business internet | Verified B2B offerings, use cases, enquiry form. Do not promise SLA, static IP, backup links, VPN, telephony, or other options unless confirmed. |
| `/tariffs/` | Tariff catalogue | Filterable tariff list; technology, speed, location, price, promo terms, included services, equipment and connection fees where applicable. |
| `/coverage/` | Coverage/address check | Town/locality selector and address form. Use a real integration if available; otherwise clearly offer manual verification. |
| `/promotions/` | Promotions | Active offers, dates, terms, eligibility, links to applicable tariffs. Expired offers must not appear as active. |
| `/clients/` | Subscriber center | Clear self-service task cards and a searchable help/FAQ area. |
| `/clients/payment/` | Payment guidance | Confirmed payment methods, instructions, processing times, link to external cabinet/payment. |
| `/clients/trust-payment/` | Trust payment | Current terms and activation method only after confirmation. |
| `/clients/speed-test/` | Speed test | Clear testing instructions and external speed-test link if approved. |
| `/clients/no-internet/` | No internet | Safe basic troubleshooting steps, affected-area status link, support contact. |
| `/clients/router/` | Router setup | Approved device-specific instructions, if available. |
| `/clients/documents/` | Documents | Downloadable current contracts, applications, policies, and forms. |
| `/clients/change-tariff/` | Change tariff | Current process and request/call route; do not modify billing directly. |
| `/clients/cabinet/` | Personal cabinet guide | Link to the existing cabinet and a useful explanation of its functions. |
| `/news/` | News and network updates | Categories/types: company news, tariff/terms changes, planned works, outages, coverage expansion. |
| `/news/{slug}/` | News detail | Rich content, publication/update dates, optional affected location/service/status fields. |
| `/about/` | About company | Verified company/network facts, technology, geography, approved photos. |
| `/contacts/` | Contacts | Confirmed phones, address, hours, email, map, contact form. Lazy-load heavy map embeds. |
| `/privacy/` | Privacy policy | Current approved legal text. |
| `/personal-data-consent/` | Personal data consent | Separate document if legally required by the operator/counsel. |
| `/terms/` | Terms/contract | Publish only current approved documents. |
| `/404` | Helpful not-found page | Search or key links, contacts, and route back to connection check. |

## 4.3 Existing URL migration

The old site contains routes such as `/tariff/`, `/promo/`, `/info/`, and `/policy/`. Preserve important existing URLs or create exact 301 redirects to the most relevant new route.

Suggested initial mappings, subject to a full crawl and content review:

- `/tariff/` → `/tariffs/`
- `/promo/` → `/promotions/`
- `/info/` → `/clients/`
- `/policy/` → `/privacy/`
- old `/info/...` child pages → their matching `/clients/...` pages

Do not redirect every old URL to the home page. Do not change news slugs without a matching redirect. Build an editable redirect management feature or a centrally maintained redirect map with tests that detect loops and invalid targets.

Before migration, crawl the source website and inventory all public URLs, response codes, canonical tags, internal links, images, downloadable documents, and metadata. The source brief's discovered URL list is not guaranteed to be exhaustive.

---

# 5. Administration Panel — Core Requirements

Create a protected admin panel, preferably at `/admin`, using Filament. The panel language must be Russian. It must be usable by non-technical staff and must not require code changes for routine content and tariff updates.

## 5.1 Admin dashboard

Show useful operational summaries, not decorative charts without actionable meaning:

- new connection requests;
- new contact/business enquiries;
- leads grouped by status;
- currently active promotions;
- published news and scheduled posts;
- tariffs currently visible on the public website;
- pending/draft content that requires review.

Dashboard widgets must respect permissions. A content editor must not see personal lead data or administrative user information unless explicitly permitted.

## 5.2 Admin navigation

Organize the panel into these sections:

**Content**
- Pages
- News and network announcements
- Promotions
- FAQ / Help articles
- Documents
- Media library
- Sliders and banners
- Navigation menus

**Commercial data**
- Tariffs
- Technologies / service types
- Coverage areas / localities
- Connection and business enquiries (leads)

**Website configuration**
- Contacts and working hours
- Header/footer settings
- SEO defaults and redirects
- Legal pages/documents
- Integrations and external links (configuration only; do not invent credentials/endpoints)

**Administration**
- Admin users
- Roles
- Permissions
- Audit log

**Engineering**
- Engineer Workspace (future-phase placeholder; see Section 13)

Navigation items and records must be hidden or disabled according to permission rules, but hiding UI elements is not a security control. Every action and endpoint must also be protected server-side.

---

# 6. Admin Users, Roles, and Permissions

## 6.1 Distinguish internal users from subscribers

The `users` table is for employees and other authorized internal users of the admin panel in the initial release. Do not create public self-registration or a duplicate subscriber account database.

A connection form submission or a contact request is a **Lead**, not an authenticated subscriber account. The external personal cabinet at `https://lk.p-svs.ru/` remains authoritative for subscriber authentication and billing unless a documented integration is approved.

## 6.2 User management

Authorized administrators must be able to:

- create/invite an internal user;
- edit name, email, phone (optional), status, and assigned roles;
- activate/deactivate an account;
- trigger a password reset/invitation flow;
- view last login where available;
- revoke access without deleting audit history;
- prevent an administrator from accidentally removing their own last administrative access.

Do not display password hashes or allow administrators to set/read a user's existing password.

## 6.3 Initial role set

Create the following starting roles. The exact permissions should be configurable in the admin panel.

| Role | Intended access |
|---|---|
| `super_admin` | Full access, including roles, permissions, admin users, settings, and audit log. Protect this role carefully. |
| `administrator` | Operational admin access; user/role management only if explicitly granted. |
| `content_manager` | Pages, news, promotions, FAQ, documents, media, sliders, menus, and SEO fields. No access to sensitive leads by default. |
| `tariff_manager` | Tariffs, tariff visibility, prices, tariff-location relations, and promotion-to-tariff relations. Publishing price changes may require a separate permission. |
| `lead_manager` | View/update/assign leads, add internal notes, change lead statuses, export only if specifically authorized. |
| `support_manager` | Subscriber help content, network announcements, relevant contact requests. No billing access. |
| `engineer` | Access only to the Engineer Workspace and future network-related functions. No unrestricted access to CMS, personal data, or admin settings. |
| `viewer` | Read-only access to explicitly permitted sections. |

Use permission keys such as:

- `users.view`, `users.create`, `users.update`, `users.deactivate`;
- `roles.view`, `roles.manage`, `permissions.view`;
- `tariffs.view`, `tariffs.create`, `tariffs.update`, `tariffs.publish`;
- `promotions.view`, `promotions.create`, `promotions.update`, `promotions.publish`, `promotions.delete`;
- `news.view`, `news.create`, `news.update`, `news.publish`, `news.archive`, `news.delete`;
- `pages.manage`, `faq.manage`, `documents.manage`;
- `media.view`, `media.upload`, `media.update`, `media.delete`;
- `sliders.manage`, `menus.manage`, `settings.manage`, `seo.manage`, `redirects.manage`;
- `leads.view`, `leads.update`, `leads.assign`, `leads.export`, `leads.delete`;
- `audit.view`;
- `engineer_workspace.access`, and future network-specific permissions.

Do not rely on role-name checks scattered through code. Authorize using permissions/policies. Avoid giving `super_admin` privileges through an undocumented hardcoded bypass; centralize and test any super-admin rule.

## 6.4 Security and accountability

- Require authentication for every admin route.
- Rate-limit login and password-reset endpoints.
- Use secure session/cookie settings and HTTPS in production.
- Consider mandatory MFA for privileged users if the chosen authentication package and operational policy support it.
- Log important administrative actions: user/role changes, price changes, publish/unpublish, deletions, settings changes, and document replacements.
- Audit records should include actor, action, subject, timestamp, and safe before/after values where appropriate. Never store passwords, authentication tokens, or sensitive secrets in audit payloads.
- Protect against mass assignment and insecure direct object references.
- Use policies for records and queries, not only menu visibility.

---

# 7. Content Management and Editorial Workflow

## 7.1 News and network announcements

News and network announcements must be managed through one clear editor interface, with a content type field:

- company news;
- tariff/price/terms change;
- planned network maintenance;
- service outage/incident;
- network coverage expansion.

Each record should support:

- title;
- slug (editable, unique, and URL-safe);
- short excerpt/summary;
- rich-text body;
- cover image and optional gallery/media;
- content type/category and optional tags;
- publication status: draft, scheduled, published, archived;
- publish date/time and optional unpublish date/time;
- author/editor;
- SEO title, meta description, canonical override where needed, Open Graph image;
- created/updated timestamps;
- optional `starts_at`, `ends_at`, affected locations, affected services, and incident status for planned works/outages;
- a visible last-updated timestamp for operational notices.

For outages and maintenance, support a lifecycle such as `planned`, `in_progress`, `resolved`, and `cancelled`. Completed incidents should remain available in the archive but must no longer look active.

Important price changes, outages, and service notices must have real text content. Do not publish key information only as an image.

## 7.2 Promotions

Promotion management must support:

- title, slug, short description, rich-text conditions;
- cover image and optional gallery;
- start and end date/time;
- optional ongoing/indefinite flag only when explicitly approved;
- publication status: draft, scheduled, active/published, expired/archived;
- applicable tariffs (many-to-many);
- eligible locations/technologies/customer types where applicable;
- discount/bonus information as structured data where practical (for example percentage or fixed amount), plus human-readable terms;
- CTA label and destination;
- SEO metadata;
- sort order and featured flag.

The application must determine whether a promotion is active using status and dates, not just a manually assigned label. Prevent expired promotions from appearing in the active promotions list. Do not automatically claim that a promotion is compatible with every tariff.

## 7.3 Rich text editor requirements

News, promotions, static information pages, FAQs, and help articles must use a WYSIWYG rich-text editor.

At minimum, support:

- headings with a valid hierarchy;
- paragraphs;
- bold and italic text;
- ordered and unordered lists;
- links;
- block quotes;
- tables where needed;
- images inserted from the media library or uploaded through a controlled media endpoint;
- captions and meaningful alternative text for content images;
- undo/redo;
- a safe preview of the final public rendering.

Requirements:

- Store editor output in a consistent format (HTML or a structured document format) and document the choice.
- Sanitize rendered HTML using an allowlist. Never render arbitrary untrusted HTML without sanitization.
- Validate links and uploaded file types.
- Do not permit arbitrary scripts, event handlers, unsafe iframes, or inline JavaScript.
- Embedded videos, if supported, must use an explicit provider allowlist and safe URL validation.
- Editor image uploads must go through the media management layer; do not store base64 images in article bodies.
- Provide draft preview without making the content publicly accessible.
- Preserve text content and media references when updating the editor/package.

## 7.4 Static pages and flexible homepage sections

Provide CMS-managed pages for About, Home Internet, Business, Coverage explanations, subscriber instructions, contacts-related information, and legal pages.

For the home page, use a controlled set of reusable page sections/blocks rather than a completely unrestricted page builder in the first release. Suggested blocks:

- hero/lead section;
- address-check or manual coverage-request form;
- featured tariffs;
- service/technology cards (GPON, WiMAX, IPTV where currently offered);
- verified benefits;
- connection steps;
- coverage/localities section;
- active promotions;
- latest news/network notices;
- subscriber quick links;
- contacts and final CTA.

Admins should be able to reorder, enable/disable, and edit section content. The system should enforce required fields for each block type. Keep the public templates consistent and responsive.

---

# 8. Tariffs, Technologies, Services, and Coverage

Tariffs are a core business entity. They must be stored once and reused consistently on the home page, tariff catalogue, promotion pages, and any related content.

## 8.1 Tariff fields

A tariff should support, where applicable:

- name and slug;
- internal code/reference (optional);
- technology: GPON, WiMAX, or another explicitly configured technology;
- speed in Mbps; separate download/upload speeds only if the operator supplies both values;
- target customer type: residential/business;
- one or more coverage areas/localities;
- regular price;
- promotional price;
- promotion start/end or promotional duration;
- explicit price period/unit (the current tariff page describes prices per 30 calendar days; do not silently translate that into a calendar month);
- price after the promotional period;
- connection/installation fee;
- equipment fee or equipment rental details;
- included services/features (for example IPTV and channel count only when confirmed);
- eligibility/limitations/terms;
- linked contract/document if applicable;
- display order, badge/featured flag;
- `is_active` and `is_published` or an equivalent controlled publication status;
- effective date and last verified date.

Do not encode prices directly in Blade templates or JavaScript. Do not show two unlabeled prices. Clearly label the regular price, promotional price, promo period, and price thereafter.

## 8.2 Technology and service catalogues

Represent technologies and service components as manageable/reference data where this makes the tariff model clearer. GPON and WiMAX must not be mixed into a single ambiguous label. IPTV should be represented as an included service/feature rather than hardcoded in every tariff card.

The first implementation may use a small controlled list managed by an administrator. Avoid building a complex product configurator unless the source data requires it.

## 8.3 Coverage locations

Create manageable coverage areas such as districts, towns, settlements, and other approved localities. Associate tariffs with the areas where they are actually available.

A manual coverage model may store:

- region/district;
- settlement/locality;
- optional street/house records only if confirmed source data exists;
- supported technology;
- verification status and last verified date;
- internal notes not visible to the public.

**Critical:** if no authoritative address-availability API/database exists, do not return “available” based on a broad locality record or a static list. The public form must say that a specialist will verify the address.

## 8.4 Tariff list and comparison

Implement the catalogue with server-side filters for supported fields, such as location, technology, customer type, speed, service bundle, and price range. Do not expose filter options that have no real data.

Support comparison of 2–3 tariffs if it can be implemented accessibly. On mobile, comparison must remain readable and must not hide price conditions, technology, equipment costs, or limitations.

---

# 9. Media Library, Images, Sliders, and Banners

## 9.1 Central media library

Provide a reusable media library in the admin panel. Editors should not need to upload the same image again for every news item or page.

Required capabilities:

- upload one or multiple files;
- drag-and-drop upload where supported;
- image thumbnail/preview;
- search by filename/title and filter by media type/date;
- title, description/caption, and required alt text for meaningful images;
- associate media with pages, news, promotions, slides, and other records;
- reuse the same media record across multiple places;
- replace an asset safely and show where it is used;
- prevent deletion of media that is still referenced, or require an explicit safe detach/replace workflow;
- generate optimized image variants/thumbnails;
- store originals separately from generated variants;
- support public images and private documents with different access rules.

Validate file extensions and actual MIME types, file size, and image dimensions. Reject executable files and unsafe formats. Do not trust the client-provided filename or MIME type.

Prefer WebP/AVIF variants where supported, while keeping a safe fallback. Use queued conversions for large image-processing tasks. Configure storage via Laravel disks so local storage can later be replaced by S3-compatible object storage without changing business logic.

## 9.2 Slider and banner management

Create a CMS section for sliders/banners with two levels:

- **Slider** — a named collection/placement (for example, home hero or a campaign placement).
- **Slide** — an individual slide belonging to a slider.

A slide should support:

- internal name;
- headline and subtitle;
- desktop image;
- optional mobile image/art direction;
- optional alt text;
- CTA label and URL;
- display order;
- enabled/disabled status;
- optional start/end scheduling;
- optional background/text theme where supported by the design system.

Allow authorized users to reorder slides. Show a preview at common desktop and mobile aspect ratios.

The public site should not depend on an auto-rotating slider for its primary message. Prefer a single strong hero section by default. If a carousel is used, keep the primary CTA visible, provide keyboard controls and accessible labels, respect reduced-motion preferences, and avoid aggressive auto-rotation.

## 9.3 Documents and downloads

Documents must be managed separately from public images. Support title, category, file, version, effective date, publish status, and optional description. Store files using safe private/public access rules. Do not expose draft or obsolete legal documents as current.

---

# 10. Additional CMS Sections That Must Be Included

These sections are required or strongly recommended by the source brief and current site structure, even though they may not have been explicitly listed in the short request.

## 10.1 FAQ and help center

- FAQ categories and searchable questions/answers.
- Help articles with title, slug, category, rich content, steps, images, related links, and last-reviewed date.
- Optional featured/most-used articles.
- Each help article must show a clear next step/contact route if it does not solve the issue.

## 10.2 Contacts and working hours

Manage business contact details centrally:

- company display name;
- phone numbers and labels;
- email;
- office address;
- office opening hours;
- technical support hours;
- external cabinet URL;
- payment URL where confirmed;
- map location/link;
- approved social/messenger links.

Do not duplicate contact data in multiple page templates. All public uses should read from the same settings/contact source.

## 10.3 Lead and request management

Create a manageable inbox for:

- residential connection requests;
- manual coverage-check requests;
- business enquiries;
- contact/callback requests.

Store only the data actually needed to process a request, such as:

- request type;
- person/contact name;
- phone and/or email;
- locality, street, house, and object type where relevant;
- selected tariff (optional);
- business name and request description for B2B enquiries;
- page/source and UTM parameters where consent and policy permit;
- consent record/version and submission timestamp;
- status: new, in progress, waiting for customer, completed, rejected, spam;
- assigned manager;
- internal notes and status history.

Admin capabilities:

- search/filter by status, date, type, and assigned manager;
- open a request and review its history;
- assign/reassign;
- add private notes;
- change status;
- optionally export data only with a dedicated permission and audit record.

Protect lead data as personal data. Apply access restrictions and define a retention/deletion policy with the data controller. Never expose leads in public API responses or analytics payloads.

Form behavior:

- client-side and server-side validation;
- accessible labels and error messages;
- clear loading/success/error states;
- anti-spam/rate limiting;
- preserve user input after recoverable validation errors;
- record consent only when applicable and with the correct current legal text;
- confirmation means the request was received, not that coverage or connection has been approved;
- configurable notification delivery (mail/CRM/webhook) using real approved settings only.

## 10.4 Navigation and footer management

Provide admin controls to manage header/footer links and ordering. Validate internal and external URLs. Keep the primary site navigation compact and do not allow accidental removal of critical links without a warning.

## 10.5 SEO management and redirects

For public pages and content records, provide:

- SEO title;
- meta description;
- canonical URL override where required;
- Open Graph title/description/image;
- index/follow controls with safe defaults;
- slug editing with uniqueness validation.

Also provide:

- sitemap generation;
- robots.txt management/configuration under developer control;
- canonical URL consistency;
- 404 page;
- redirects with source path, destination, 301/302 status, enabled flag, and loop/invalid-target validation;
- preservation/migration of old page metadata where possible.

Do not create automatically generated thin location pages. Locality landing pages should be published only when there is unique, verified content.

## 10.6 Site-wide settings

Manage editable site settings such as logo, favicon, default SEO metadata, header CTA, footer content, contacts, external cabinet URL, social links, and analytics IDs where approved.

Do not store secrets such as API keys or SMTP passwords as ordinary visible CMS text fields. Secret integrations must use environment variables or an appropriately encrypted settings mechanism with strict authorization.

---

# 11. Database / Domain Model Guidance

The following is a conceptual starting point, not a command to create every table exactly as written. Inspect existing models first and adapt names/normalization to the actual repository. Avoid storing the same fact in several unrelated tables.

| Entity | Purpose / key relationships |
|---|---|
| `users` | Internal admin/staff accounts only in the first release. |
| `roles`, `permissions` and pivot tables | Role-based and granular authorization; use the selected permission package if compatible. |
| `tariffs` | Single source of truth for tariff data and pricing. |
| `technologies` | GPON, WiMAX, and future explicitly approved technologies. |
| `services` or tariff feature records | IPTV and other included service features, with structured data where useful. |
| `areas` / `locations` | Districts, towns, settlements, and verified coverage geography. |
| `area_tariff` (or equivalent) | Many-to-many relation between tariffs and supported areas. |
| `promotions` | Offer content, validity period, status, conditions, and structured benefit fields. |
| `promotion_tariff` | Promotions applicable to particular tariffs. |
| `posts` / `news` | News and network notices with a controlled content type and operational status. |
| `categories` / `tags` | Organize posts and help content if required. |
| `pages` | Static/CMS pages, slugs, publication status, and SEO metadata. |
| `page_sections` / `content_blocks` | Typed, ordered homepage/page blocks. Keep block types controlled. |
| `sliders` | Named slider/banner placements. |
| `slides` | Ordered, scheduled items belonging to a slider; reference media records. |
| `media` | Central media library, preferably managed through the chosen media package. |
| `documents` | Public document metadata and file/media reference, version, effective date, visibility. |
| `faq_categories`, `faqs` | FAQ hierarchy and searchable Q&A. |
| `help_articles` / `help_categories` | Subscriber knowledge base and troubleshooting guides. |
| `leads` | Connection, coverage-check, contact, callback, and business requests. |
| `lead_notes` / `lead_status_histories` | Private notes and traceable lead workflow history. |
| `site_settings` | Non-secret global configuration. |
| `contact_channels` | Phone/email/address/hours if these need structured multi-value management. |
| `menus`, `menu_items` | Managed navigation/footer menu structure. |
| `redirects` | Legacy URL and SEO redirect management. |
| `activity_log` | Audit trail for critical admin changes. |

### 11.1 Important model rules

- Use database transactions when saving tariffs and pivot relations or publishing multi-record content.
- Index slugs, publication states/dates, foreign keys, lead status/date, and other frequently filtered columns.
- Use unique constraints for public slugs and other identifiers that must be unique.
- Define explicit deletion behavior. For example, a tariff used by historical leads should normally be deactivated rather than hard-deleted.
- Store money as integer minor units or an appropriate fixed-precision decimal; never use floating-point arithmetic for prices.
- Store timestamps consistently and display them in the configured business timezone. Make timezone configurable; do not hardcode a developer machine's timezone.
- Keep structured data for dates, statuses, prices, and relationships out of rich-text bodies.
- Add factories and seeders for development/test data. Do not seed unconfirmed live commercial details as production-ready content.

---

# 12. Design and UX Requirements

## 12.1 Visual direction

Use a clean, modern regional ISP visual identity:

- strong typography and clear visual hierarchy;
- spacious layout and readable tariff cards;
- high contrast for key actions;
- dark navy/graphite as a possible foundation and one clear accent color for primary CTAs;
- preserve the existing logo/brand assets when available;
- do not invent a new logo or unrelated visual identity;
- use real company/network/team photographs only when the company owns them or has permission to use them;
- do not use competitor assets or images that falsely imply ownership of infrastructure.

The visual design should communicate reliability, clear prices, local presence, and technical competence — not exaggerated claims.

## 12.2 UX rules

- One clear primary CTA per major screen.
- Start with address verification/request before promising connection availability.
- Show regular price, promotional price, promo duration, and price after the promo in a clear hierarchy.
- Provide direct access to the external subscriber cabinet and support.
- Make forms short and explain why each field is needed.
- Avoid aggressive countdowns, flashing banners, unnecessary popups, and autoplay video.
- Every interactive component must have useful loading, empty, success, and error states.

## 12.3 Responsive and accessible behavior

Test at minimum: 360, 390, 768, 1024, and 1440 px viewport widths.

Requirements:

- no horizontal overflow;
- semantic headings and landmarks;
- real labels for form controls;
- keyboard-operable menus, forms, editor controls, and carousels;
- visible focus states;
- meaningful alt text for content images;
- decorative images hidden from assistive technology where appropriate;
- errors must be described in text, not color alone;
- target WCAG 2.2 AA where applicable;
- respect reduced-motion preferences.

---

# 13. Engineer Workspace and Future Network Map

The company requires a separate area for engineers. The interactive network map will be implemented in later project stages.

## 13.1 Initial release requirements

In the first release:

- create an **Engineer Workspace** navigation entry in the admin area;
- protect it with the `engineer_workspace.access` permission;
- make it visible only to authorized engineer/admin roles;
- create a clear placeholder page that explains that network-map functionality is planned for a later phase;
- optionally provide a simple dashboard shell or list of planned modules, but no fake network objects;
- keep the module isolated enough that a future implementation can be added without restructuring the CMS.

Do not build an interactive map in this phase unless explicitly re-prioritized. Do not create fictitious fiber routes, optical splitters, OLTs, towers, access points, customer endpoints, signal values, or outage locations.

## 13.2 Future-phase capabilities to anticipate

The later engineering module may include:

- geospatial network map;
- network nodes and equipment inventory;
- fiber cable routes and connections;
- GPON elements such as OLTs, splitters, and distribution points;
- WiMAX sites/sectors and related equipment;
- buildings/addresses and coverage status;
- network incidents and planned maintenance;
- attachments, photos, technical notes, and history;
- role-specific permissions and audit records.

Do not implement a speculative schema now. Before that phase, inspect the operator's real network inventory, source formats, coordinate systems, required map operations, and access policy. Choose a map stack (for example, MapLibre or Leaflet) only after those requirements are known.

---

# 14. External Integrations and Business Boundaries

## 14.1 Personal cabinet and billing

The current public website points to `https://lk.p-svs.ru/`. Preserve this external destination unless the billing owner approves a change.

- Do not implement a fake login form.
- Do not store subscriber passwords.
- Do not create a second balance or payment ledger.
- Do not process card details or store bank-card data.
- Do not assume billing APIs exist.
- If the cabinet is unavailable, provide an approved status message and alternative contact route; do not silently hide it.

Any future billing integration requires documented API access, a test environment, an integration owner, and explicit approval.

## 14.2 Notifications / CRM

Connection and contact requests need a configurable delivery mechanism. First inspect whether the company has a CRM, mailbox, or approved webhook/API. Do not invent an endpoint or recipient email. Implement a safe adapter/configuration point and log delivery failures without leaking personal data.

## 14.3 Maps and address verification

If there is an authoritative internal coverage/address service, integrate it through a dedicated service class and test environment. If there is no authoritative source, use a manual review workflow. A third-party map showing a street or settlement does not prove that the company can connect that address.

## 14.4 Analytics

Prepare a privacy-aware event plan. Possible events include:

- `coverage_check_started`;
- `coverage_check_completed`;
- `lead_form_started`;
- `lead_form_submitted`;
- `lead_form_error`;
- `tariff_compare_used`;
- `tariff_cta_clicked`;
- `phone_clicked`;
- `cabinet_clicked`;
- `payment_clicked`;
- `support_article_opened`.

Do not send names, phone numbers, addresses, subscriber identifiers, passwords, or payment data to analytics tools. Load analytics only according to the approved privacy/consent setup.

---

# 15. SEO, Performance, Security, and Operations

## 15.1 SEO

- Unique `title` and `meta description` for each indexable public page.
- One meaningful H1 per page and logical H2/H3 hierarchy.
- Consistent human-readable URLs and trailing-slash policy.
- Correct canonical URLs.
- XML sitemap and robots.txt.
- Open Graph metadata.
- Structured data only from confirmed facts and where valid.
- No accidental `noindex` on tariff or important local service pages.
- Avoid duplicate indexable filter URLs.
- Preserve old URLs or create precise 301 redirects.
- Migrate relevant news content, images, metadata, and document links.

## 15.2 Performance

- Mobile-first rendering.
- Optimize image sizes and generate responsive variants.
- Lazy-load below-the-fold media and map embeds.
- Avoid large JavaScript bundles and unnecessary third-party scripts.
- Cache public content/tariff queries where appropriate, with reliable cache invalidation after admin updates.
- Queue expensive image conversions.
- Check Core Web Vitals on representative mobile pages.

## 15.3 Security

- Validate all input on the server.
- Escape output by default and sanitize rich-text HTML.
- Protect against XSS, CSRF, SQL injection, mass assignment, insecure uploads, and authorization bypasses.
- Validate file type, MIME, file size, and storage location.
- Keep private documents and lead details protected.
- Rate-limit public forms and admin authentication.
- Use least-privilege permissions.
- Use HTTPS and secure session settings in production.
- Audit critical administrative operations.
- Keep dependencies current and review security advisories.

## 15.4 Backups and deployment

- Provide environment example/configuration documentation.
- Document queue workers and scheduler requirements.
- Document storage symlink and media storage configuration.
- Provide safe database migration/deployment instructions.
- Require a database and media backup before content migration or production release.
- Provide a rollback procedure.
- Monitor the homepage, lead submission endpoint, admin login, and external cabinet link/status as agreed.

---

# 16. Implementation Phases

Implement the project in phases. Do not attempt to complete every future feature before the foundation is tested.

## Phase 0 — Repository and requirements audit

1. Inspect the codebase and Git state.
2. Record framework/runtime/package versions and database schema.
3. Identify existing public pages, forms, routes, authentication, CMS, and integrations.
4. Crawl the source website and export its URL/content/media/document inventory.
5. Identify existing brand assets and confirm how they may be reused.
6. Produce a concise technical plan, model map, route map, and list of blocked/unknown business facts.

**Deliverable:** audit notes and implementation plan.

## Phase 1 — Laravel foundation and admin access

1. Establish/repair Laravel application setup.
2. Configure environment, database, cache, queues, storage, and frontend build.
3. Install/configure Filament.
4. Implement admin authentication.
5. Implement internal users, roles, permissions, and policies.
6. Add admin dashboard shell and audit logging.
7. Add automated tests for authentication and authorization boundaries.

**Deliverable:** secure working admin panel with role-based access.

## Phase 2 — CMS and media management

1. Implement media library and safe file upload workflow.
2. Implement pages and controlled homepage blocks.
3. Implement news/network announcements with rich editor and scheduling/statuses.
4. Implement promotions with dates, tariff relations, and rich editor.
5. Implement slider/banner management.
6. Implement FAQ/help articles and documents.
7. Implement SEO fields, menus, footer, site settings, and redirects.
8. Add preview, publication, and archival workflows.

**Deliverable:** content team can manage the website without editing code.

## Phase 3 — Commercial data and lead management

1. Implement technologies/services, tariffs, pricing, and availability relations.
2. Implement editable coverage locations with explicit verification status.
3. Implement tariff list, filters, and clear price presentation.
4. Implement connection, coverage-check, business, and contact forms.
5. Implement lead inbox, statuses, assignments, notes, and safe notifications.
6. Add form abuse protection and privacy-aware event tracking hooks.

**Deliverable:** an editable commercial catalogue and functioning lead workflow.

## Phase 4 — Public website

1. Implement shared public layout/header/footer/contact bar.
2. Implement homepage sections.
3. Implement home internet and business pages.
4. Implement tariff catalogue and tariff details/comparison as scoped.
5. Implement coverage/manual verification flow.
6. Implement promotions listing/details.
7. Implement subscriber center, FAQ/help pages, and document downloads.
8. Implement news/maintenance/outage archive and detail pages.
9. Implement About, Contacts, and legal pages.
10. Implement responsive and accessible behavior.

**Deliverable:** complete public-facing website connected to CMS data.

## Phase 5 — Migration, SEO, integrations, and release hardening

1. Migrate reviewed content, images, and documents.
2. Confirm tariff values, promotional conditions, contacts, office/support hours, legal texts, and external URLs with the owner.
3. Implement/test legacy redirects.
4. Verify sitemap, robots, canonical, metadata, and analytics.
5. Verify form delivery to an approved destination.
6. Verify external personal cabinet and payment links with the billing owner.
7. Run security, accessibility, performance, and end-to-end checks.
8. Create backup and rollback instructions.

**Deliverable:** release candidate with a documented go-live checklist.

## Phase 6 — Engineer Workspace foundation

1. Add the protected engineer entry point and placeholder.
2. Verify engineer-only access and cross-role isolation.
3. Document the data discovery required before building the network map.

**Deliverable:** secure module boundary ready for later network-map development, without fake network data.

## Later phase — Interactive network map

Do not start until the operator supplies real network inventory, source data, map requirements, and permissions. Deliver this as a separately scoped phase.

---

# 17. Required Automated Tests and Acceptance Criteria

The implementation is not complete merely because the pages render. Add automated tests for the core rules.

## 17.1 Admin and permissions

- Unauthenticated users cannot access admin routes.
- A content manager can manage permitted content but cannot manage users/roles or view leads by default.
- A tariff manager can edit tariff data but cannot change roles or legal settings.
- An engineer can open the Engineer Workspace but cannot access unrelated admin functions.
- A user without a specific permission receives a server-side 403 even if they call the endpoint directly.
- Deactivated users cannot authenticate.

## 17.2 Content lifecycle

- Draft content is not publicly visible.
- Scheduled content becomes visible only at the correct publish time/timezone.
- Archived content is not shown in active lists.
- Expired promotions are not displayed as active.
- Rich-text output is sanitized and unsafe scripts/attributes are removed.
- Slugs are unique and redirects do not create loops.

## 17.3 Tariffs and promotions

- Tariff prices are read from the database, not templates.
- Regular and promotional prices are labeled correctly.
- The promotional period and post-promotion price are displayed when configured.
- A tariff is shown only for the areas/technologies to which it is actually assigned.
- Unknown coverage never produces a guaranteed “available” result.
- Deactivating a tariff does not corrupt historical leads or published historical content.

## 17.4 Forms and personal data

- Valid form submissions create a lead.
- Invalid requests return accessible validation errors.
- Spam/rate limits work.
- The confirmation message does not claim that coverage is verified.
- Consent/version/timestamp are stored where legally required.
- Lead details are accessible only to authorized staff.
- Personal data is not sent to analytics events.

## 17.5 Media and frontend

- Invalid/executable uploads are rejected.
- Media references remain valid when used by multiple records.
- Referenced media cannot be accidentally removed without a safe workflow.
- Public pages are responsive at the required widths.
- No broken internal links or missing required content pages.
- Header contact links use `tel:` and approved external cabinet/payment links.
- Images have appropriate alt behavior and optimized variants.

## 17.6 Release checklist

Before calling the project ready for launch:

- [ ] All approved routes exist and return expected status codes.
- [ ] Old important URLs are preserved or redirected precisely.
- [ ] Tariffs/prices/terms have been confirmed by the owner.
- [ ] Promotions have confirmed dates and conditions.
- [ ] Coverage copy does not overpromise.
- [ ] Lead forms deliver to the approved system and show accurate confirmation.
- [ ] Personal cabinet/payment flow has been checked with the billing owner.
- [ ] Contacts and working hours are consistent across the website.
- [ ] Legal documents are current and approved by the responsible party.
- [ ] News and network notices contain readable text, not just images.
- [ ] Admin users/roles/permissions have been tested.
- [ ] Engineer Workspace is protected and does not show fabricated network data.
- [ ] SEO metadata, canonical URLs, sitemap, robots, and redirects are checked.
- [ ] Media, backups, queues, and scheduled tasks are configured.
- [ ] Mobile, keyboard, screen-reader basics, and accessibility have been checked.
- [ ] No secrets or production personal data are committed to the repository.
- [ ] Backup and rollback steps are documented.

---

# 18. Content and Business Facts Requiring Owner Confirmation

Treat the following as migration/reference information, **not automatically approved production truth**. Confirm values before publishing or changing them.

The currently observed website includes:

- Brand/display name: **Сити Телеком Сервис**.
- Technologies mentioned: GPON and WiMAX.
- IPTV is advertised; the live site has used wording about up to/more than 130 channels. Confirm the accurate current number and package conditions.
- The tariff page currently describes prices as being for **30 calendar days**.
- The tariff page shows residential GPON and WiMAX groups and separate “район” tariff variants.
- Tariff cards display pairs of prices; confirm which is the regular price, promotional price, and the period/eligibility for the lower price.
- The promotions page has shown a bonus for paying several months in advance; the stated 10%/15% terms and “indefinite” validity must be confirmed.
- Current website contact details include `8 (918) 066-08-33`, `8 (918) 493-41-88`, `info@p-svs.ru`, and `г. Крымск, ул. Фадеева, д. 64/1`; verify before migration.
- The office is currently shown as open daily 09:00–21:00; confirm separately from telephone/technical-support hours.
- The current personal cabinet link is `https://lk.p-svs.ru/`; verify availability and keep it external unless an approved integration exists.
- The current site names ИП Месяченко В.М. and publishes legal identifiers; the privacy-policy operator and legal documents require legal confirmation.
- The source brief notes that the current site claims service in Krymsk and Abinsk districts; exact coverage must be verified by locality/address.
- Existing legacy URLs, article titles, and some slugs may not match. Review each item before migration and preserve useful search value.

Also obtain from the owner:

1. Approved legal/business name and personal-data operator details.
2. Confirmed address, phones, email, office hours, and support hours.
3. Current tariff table: speed, prices, billing period, promo period, eligibility, connection/equipment costs, geographic availability, and contract terms.
4. Current IPTV package/channel count and compatibility information.
5. Verified list of served districts/settlements and address-level availability source, if any.
6. Actual connection lead time and service commitments, if these may be published.
7. Active promotion rules and dates.
8. Current trust-payment, tariff-change, temporary-blocking, and reconnection procedures.
9. Payment methods, settlement time, and official links/instructions.
10. Personal cabinet owner, test access, and API documentation if integration is requested.
11. Approved destination for website leads and who processes them.
12. Current contracts, privacy policy, consent text, and document versions.
13. Confirmed B2B services and limitations.
14. Brand assets, approved photographs, and usage rights.
15. Engineer network data sources and map requirements for the future phase.

---

# 19. Final Agent Deliverables

When implementation is complete, provide:

1. A concise architecture summary and the final entity/relationship overview.
2. A list of installed packages and why each is used.
3. Database migrations and seeders.
4. Admin panel URL and instructions for creating the first super administrator securely.
5. A permissions matrix for the implemented roles.
6. A list of public routes and old-URL redirects.
7. A list of implemented CMS sections and their capabilities.
8. Environment variables and infrastructure requirements (database, storage, queue, scheduler, mail, etc.).
9. Commands to install, migrate, seed development data, build assets, run tests, and start workers.
10. Test results and known limitations.
11. A list of unresolved business/legal values that require owner confirmation.
12. Deployment, backup, and rollback instructions.
13. A changelog describing the files/modules changed.

Do not claim that an integration, test, deployment, or data migration succeeded unless it was actually executed and verified.

---

# 20. Source References

## Project brief

Use the supplied `website_redesign_agent_brief.md` as the primary business/functional source. Preserve its terminology, priorities, restrictions, public sitemap, migration requirements, and acceptance criteria.

## Website reviewed for this instruction set

- Home: <https://new.p-svs.ru/>
- Tariffs: <https://new.p-svs.ru/tariff/>
- Promotions: <https://new.p-svs.ru/promo/>
- Subscriber section: <https://new.p-svs.ru/info/>
- About: <https://new.p-svs.ru/about/>
- Contacts: <https://new.p-svs.ru/contacts/>

The website can change at any time. Re-crawl it before migration and verify all business facts with the owner.

## Technical documentation to consult during implementation

- Laravel release/support documentation: <https://laravel.com/docs/releases>
- Filament documentation: <https://filamentphp.com/docs/>
- Spatie Laravel Permission: <https://spatie.be/docs/laravel-permission/>
- Spatie Laravel Media Library: <https://spatie.be/docs/laravel-medialibrary/>

Always check the current compatibility matrix and security advisories before installing/upgrading packages.

---

# Final Instruction

Build a maintainable Laravel application, not a collection of hardcoded pages. The admin panel must be the source of truth for tariffs, promotions, news, pages, media, sliders, contacts, documents, and other editable website content. Internal staff access must be permission-based and auditable. Public visitors must receive accurate, accessible information and must never be promised coverage, pricing, support, or billing behavior that the operator has not confirmed. Keep the future Engineer Workspace isolated and ready for expansion, but do not fabricate or implement the network map before the required real network data and scope are supplied.
