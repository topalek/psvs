<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /** @var array<string, string> */
    public const PERMISSIONS = [
        'users.view' => 'Просмотр пользователей',
        'users.create' => 'Создание пользователей',
        'users.update' => 'Редактирование пользователей',
        'users.deactivate' => 'Блокировка пользователей',
        'roles.view' => 'Просмотр ролей',
        'roles.manage' => 'Управление ролями',
        'permissions.view' => 'Просмотр разрешений',
        'tariffs.view' => 'Просмотр тарифов',
        'tariffs.create' => 'Создание тарифов',
        'tariffs.update' => 'Редактирование тарифов',
        'tariffs.publish' => 'Публикация тарифов',
        'tariffs.delete' => 'Удаление тарифов',
        'technologies.view' => 'Просмотр технологий',
        'technologies.manage' => 'Управление технологиями',
        'services.view' => 'Просмотр услуг',
        'services.manage' => 'Управление услугами',
        'areas.view' => 'Просмотр зон покрытия',
        'areas.manage' => 'Управление зонами покрытия',
        'promotions.view' => 'Просмотр акций',
        'promotions.create' => 'Создание акций',
        'promotions.update' => 'Редактирование акций',
        'promotions.publish' => 'Публикация акций',
        'promotions.delete' => 'Удаление акций',
        'news.view' => 'Просмотр новостей',
        'news.create' => 'Создание новостей',
        'news.update' => 'Редактирование новостей',
        'news.publish' => 'Публикация новостей',
        'news.archive' => 'Архивирование новостей',
        'news.delete' => 'Удаление новостей',
        'pages.view' => 'Просмотр страниц',
        'pages.manage' => 'Управление страницами',
        'faq.view' => 'Просмотр FAQ',
        'faq.manage' => 'Управление FAQ',
        'help.view' => 'Просмотр справочных статей',
        'help.manage' => 'Управление справочными статьями',
        'documents.view' => 'Просмотр документов',
        'documents.manage' => 'Управление документами',
        'media.view' => 'Просмотр медиатеки',
        'media.upload' => 'Загрузка файлов',
        'media.update' => 'Редактирование медиа',
        'media.delete' => 'Удаление медиа',
        'sliders.view' => 'Просмотр слайдеров',
        'sliders.manage' => 'Управление слайдерами',
        'menus.view' => 'Просмотр меню',
        'menus.manage' => 'Управление меню',
        'settings.view' => 'Просмотр настроек',
        'settings.manage' => 'Управление настройками',
        'seo.manage' => 'Управление SEO',
        'redirects.view' => 'Просмотр редиректов',
        'redirects.manage' => 'Управление редиректами',
        'contacts.view' => 'Просмотр контактов',
        'contacts.manage' => 'Управление контактами',
        'leads.view' => 'Просмотр заявок',
        'leads.update' => 'Редактирование заявок',
        'leads.assign' => 'Назначение заявок',
        'leads.export' => 'Экспорт заявок',
        'leads.delete' => 'Удаление заявок',
        'audit.view' => 'Просмотр журнала действий',
        'engineer_workspace.access' => 'Доступ к рабочему месту инженера',
    ];

    /** @var array<string, list<string>> */
    public const ROLES = [
        'admin' => ['*'],
        'manager' => [
            'tariffs.view', 'tariffs.create', 'tariffs.update', 'tariffs.publish', 'tariffs.delete',
            'technologies.view', 'technologies.manage', 'services.view', 'services.manage',
            'areas.view', 'areas.manage',
            'promotions.view', 'promotions.create', 'promotions.update', 'promotions.publish', 'promotions.delete',
            'news.view', 'news.create', 'news.update', 'news.publish', 'news.archive', 'news.delete',
            'pages.view', 'pages.manage', 'faq.view', 'faq.manage', 'help.view', 'help.manage',
            'documents.view', 'documents.manage',
            'media.view', 'media.upload', 'media.update', 'media.delete',
            'sliders.view', 'sliders.manage', 'menus.view', 'menus.manage',
            'seo.manage', 'redirects.view', 'redirects.manage',
            'contacts.view', 'contacts.manage',
            'leads.view', 'leads.update', 'leads.assign', 'leads.export',
        ],
        'engineer' => [
            'engineer_workspace.access',
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $name => $label) {
            Permission::findOrCreate($name, 'web');
        }

        // The role set is intentionally fixed to admin, manager and engineer.
        // Removing legacy roles also detaches them from users through the
        // cascading pivot foreign keys.
        Role::query()
            ->whereNotIn('name', array_keys(self::ROLES))
            ->delete();

        foreach (self::ROLES as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName, 'web');

            if ($permissions === ['*']) {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($permissions);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
