# Handoff: Сити Телеком Сервис

Документ для следующего агента/разработчика. Описывает, что уже сделано, какие решения приняты,
как проверять проект и что осталось.

Дата: 08.11.2026

## 1. Контекст проекта

- Публичный сайт + CMS для регионального провайдера «Сити Телеком Сервис».
- Исходное ТЗ: `laravel_city_telecom_agent_instructions.md` в корне проекта.
- Локальный URL: `http://psvs.local` (OSPanel, Nginx, PHP 8.5.4).
- Laravel 13.40, Filament 5.11.1, MariaDB 11.8, БД `psvs` на `127.0.1.12`.
- Git-репозиторий: `https://github.com/topalek/psvs.git` (ветка `main`).
- Админка: `http://psvs.local/admin`.
- Демо-аккаунты (только `local`/`testing`, пароль `password`):
  - `admin@psvs.test` — `admin`
  - `manager@psvs.test` — `manager`
  - `engineer@psvs.test` — `engineer`

## 2. Что сделано в этой итерации

### 2.1. Полноценный CRUD вместо модальных окон

Все контентные ресурсы переведены с `ManageRecords` на страницы `ListRecords` + `CreateRecord` +
`EditRecord`. Создание и редактирование открываются отдельными страницами, таблица ведёт на страницу
редактирования, после сохранения — возврат к списку.

Ресурсы:

- `TariffResource` — тарифы
- `UserResource` — пользователи
- `RoleResource` — роли и права (только список и редактирование, набор ролей фиксирован)
- `PostResource` — новости и оперативные объявления (единый редактор, поле `type`)
- `PromotionResource` — акции
- `SliderResource` — слайдеры
- `SlideResource` — слайды
- `PageResource` — статические страницы
- `DocumentResource` — документы
- `FaqResource` — вопросы FAQ
- `HelpArticleResource` — справочные статьи

Для каждого ресурса:

- Общие `form()` и `table()` остались в ресурсе — дублирования схем между Create и Edit нет.
- `EditAction` и `CreateAction` автоматически ведут на страницы, потому что в `getPages()` добавлены
  маршруты `create` и `edit`.
- Удаление и смена статусов остались модальными действиями.
- Хлебные крошки и заголовки формируются Filament; для Create-страниц добавлены корректные русские
  заголовки (`Создать тариф`, `Создать пользователя` и т. д.).
- Для русских названий отключён автоматический Title Case
  (`protected static bool $hasTitleCaseModelLabel = false;`).
- Старые классы `Manage*` для этих 11 ресурсов удалены.

### 2.2. Слайдеры и слайды без дублирования схемы

Поля слайда вынесены в `SlideResource::slideFields(bool $includeSlider = true)`.
Используются:

- в `SlideResource::form()` — с выбором слайдера;
- в `SliderResource` внутри `Repeater::relationship('slides')` — без выбора слайдера.

В repeater’ах добавлен `->defaultItems(0)`, иначе Filament создаёт пустой элемент и валидация
блокирует сохранение (`SliderResource`, `PageResource`, `HelpArticleResource`).

### 2.3. Роли: ровно три

Роли сведены к `admin`, `manager`, `engineer`.

- `database/seeders/RolesAndPermissionsSeeder.php`:
  - `admin` — все разрешения (`*`);
  - `manager` — контент, тарифы, акции, новости, документы, справка, медиа, меню, SEO, редиректы,
    контакты, заявки (без удаления заявок), без пользователей/ролей/настроек/аудита;
  - `engineer` — только `engineer_workspace.access`.
  - Устаревшие роли удаляются, пивот-таблицы очищаются каскадно.
- `DemoUsersSeeder` создаёт три демо-аккаунта; старые демо-email переводятся в `manager`.
- `app/Support/RoleLabels.php` — русские подписи ролей.
- `User::getRoleLabelAttribute()` выводит русские названия.
- `AppServiceProvider`:
  - `Gate::before` даёт полный доступ роли `admin`;
  - запрещает администратору деактивировать/удалять самого себя.
- `CreateAdminCommand` создаёт роль `admin`.
- `RoleResource`:
  - `canCreate()` и `canDelete()` возвращают `false`;
  - роль `admin` нельзя редактировать;
  - имя роли только для чтения;
  - редактируется матрица разрешений;
  - в таблице у роли `admin` скрыто действие «Изменить»;
  - заголовок страницы: «Роль: Менеджер».
- `UserResource` показывает русские названия ролей в форме и фильтре.

### 2.4. Тёмная и светлая темы админки

`app/Providers/Filament/AdminPanelProvider.php`:

- `Color::hex('#7f56d8')` — фиолетовый primary из референса;
- кастомная slate-палитра `gray` для обеих тем: тёмный фон `#1e2130`, панели `#262a3a`,
  светлый фон `#f6f7fb`, границы `#2f3446`;
- `->darkMode()` — тёмная тема разрешена, но не принудительная;
- `->defaultThemeMode(ThemeMode::Dark)` — при первом входе открывается тёмная тема;
- `->themeSwitcher()` — переключатель «светлая / тёмная / системная» в верхней панели.

Проверено в браузере:

- тёмная тема: `<html class="fi dark">`, фон `oklch(0.252 0.029 275.403)`,
  primary-кнопки `oklch(0.598 0.169 294)`;
- светлая тема: `<html class="fi">`, фон `oklch(0.977 0.005 274.967)`, белые панели,
  тёмный текст, фиолетовый active nav;
- выбор темы сохраняется в `localStorage` (`filament:theme:mode`) и переживает перезагрузку.

### 2.5. Прочее

- Добавлены серверные правила загрузки: изображения `mimes:jpg,jpeg,png,gif,webp|max:10240`,
  документы `mimes:pdf,doc,xls,xlsx` + `maxSize(20480)`.
- Из медиатеки удалено действие «Создать» (открывало пустую модалку и могло создать невалидную
  запись).
- README переписан под проект (стек, запуск, роли, тесты, продакшен-чеклист).
- Шрифты собираются через `laravel-vite-plugin/fonts`: Manrope (400–800, latin + cyrillic)
  из npm-пакета `@fontsource/manrope`, поэтому сборка не обращается к внешним CDN.
  Оптимизированные фолбэки включены (`fontaine` в `devDependencies`).- Тесты обновлены под три роли и полный CRUD.

## 3. Проверка

Выполнено:

```bash
php artisan test          # 61 тест, 399 утверждений — OK
vendor/bin/pint --test    # OK
npm run build             # OK, без предупреждений (fontaine 1.0.3)
```

Браузерная проверка (Chromium, `http://psvs.local`):

- тёмная и светлая темы, фиолетовый primary, сохранение выбора темы;
- `/admin/tariffs`: «Создать» ведёт на `/admin/tariffs/create`;
- создание тарифа «Браузерный тест» → редирект на список, запись появилась;
- редактирование → «Сохранить» → редирект на список, имя обновилось;
- «Отмена» возвращает к списку;
- удаление через модальное подтверждение → редирект на список, уведомление «Удалено»;
- для всех 11 ресурсов проверены index, наличие Create/Edit-ссылок и корректные breadcrumbs;
- роли: 3 записи, у `admin` нет кнопки редактирования, у `manager`/`engineer` есть;
- страница роли: имя disabled, матрица разрешений отображается.

Автотесты `AdminCrudTest` дополнительно прогоняют Create → Save → Edit → Save → Delete для каждого
ресурса через Livewire (10 CRUD-ресурсов + отдельный тест роли).

## 4. Архитектурные решения и ограничения

- **Не обновлять** Laravel/Filament и не менять схему БД без необходимости.
- Цены — в копейках, в формах рубли (`FieldHelpers::minorToRubles()` / `rublesToMinor()`).
- Публичные модели используют `slug` как route key; в тестах и ссылках использовать
  `$record->getRouteKey()`, а не `id`.
- Политики: `CmsPolicy` + `Gate::before` для class-string проверок Filament.
- Богатый текст очищается `App\Support\Html` (Symfony HtmlSanitizer).
- Медиатека только просмотр/удаление; загрузка — через поля ресурсов.
- Легаси-редиректы выполняются до нормализации завершающего слэша.
- `php artisan serve` на этой Windows-машине не может открыть сокет — использовать OSPanel.
- `php artisan storage:link` падает с ошибкой доступа; используется junction
  `public/storage` → `storage/app/public`.
- Тесты используют SQLite `:memory:`; рабочая MariaDB не затрагивается.
- Демо-пользователи создаются только в `local`/`testing`.

## 5. Что осталось сделать

### Высокий приоритет

1. **Подтвердить контент у владельца.** Тарифы, цены, контакты, зоны покрытия, реквизиты и
   юридические тексты взяты из открытых источников и помечены как требующие утверждения.
2. **Проверить загрузку медиа вручную.** Серверная валидация добавлена, автотесты CRUD не
   загружают файлы. FilePond инициализируется только когда секция формы видима; в headless-браузере
   синтетическая загрузка не сработала. Нужен ручной тест: изображение тарифа/новости/слайда,
   PDF-документ, удаление файла.
3. **Настроить почту и уведомления о заявках.** `LEAD_NOTIFICATION_EMAIL` пуст; проверить
   `LeadManager`/`LeadRequest` и очередь в продакшене.
4. **Продакшен-развёртывание:** `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, `APP_URL`,
   `SESSION_SECURE_COOKIE=true`, `storage:link`, `optimize`, queue worker, бэкапы.

### Средний приоритет

5. **Русские подписи разрешений.** Сейчас матрица ролей показывает технические ключи
   (`tariffs.update`). Можно сгруппировать их по разделам и вывести русские подписи из
   `RolesAndPermissionsSeeder::PERMISSIONS` (лучше вынести карту в `App\Support\PermissionLabels`).
6. **Экспорт CSV заявок.** Есть действие и право `leads.export`, но нет автотеста на скачивание.
7. **Аудит доступности.** Пройти клавиатурой по формам и модалкам, проверить фокус, `aria-label`,
   контраст.
8. **SEO на реальном контенте.** Проверить canonical, Open Graph, sitemap после замены демо-данных.
9. **Легаси-редиректы.** Сверить полный список старых URL с текущим сайтом; сейчас покрыты
   `/tariff/`, `/promo/`, `/info/`, `/policy/`, `/lk/` и дочерние страницы.

### Низкий приоритет / улучшения

10. Soft deletes и восстановление для контента (сейчас удаление необратимо).
11. Массовые действия в таблицах (кроме удаления) — публикация, архивирование.
12. Загрузка нескольких изображений и conversions для медиатеки.
13. Уведомления в интерфейсе о новых заявках.
14. Замена дефолтных `example.ru`/пустых значений в `.env.example` на реальные при передаче.

## 6. Полезные команды

```bash
php artisan migrate:fresh --seed     # локальная пересборка БД
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=DemoUsersSeeder
php artisan app:create-admin admin@example.ru --name="Администратор"
php artisan test --filter=AdminCrudTest
vendor/bin/pint
npm run build
```

## 7. Ключевые файлы

- `app/Providers/Filament/AdminPanelProvider.php` — тема и панель.
- `app/Providers/AppServiceProvider.php` — `Gate::before`, политики.
- `database/seeders/RolesAndPermissionsSeeder.php` — роли и разрешения.
- `database/seeders/DemoUsersSeeder.php` — демо-аккаунты.
- `app/Support/RoleLabels.php` — русские подписи ролей.
- `app/Support/Html.php` — санитайзер.
- `app/Filament/Resources/*/Pages/` — List/Create/Edit страницы.
- `app/Filament/Resources/Slides/SlideResource.php` — общая схема полей слайда.
- `tests/Feature/AdminCrudTest.php` — сквозные CRUD-тесты.
- `tests/Feature/AdminPanelTest.php` — доступ к страницам и ролям.
- `tests/Feature/AuthorizationTest.php` — матрица прав.
- `README.md` — инструкция по запуску и эксплуатации.
