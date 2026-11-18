<?php

namespace App\Policies;

use App\Models\Area;
use App\Models\ContactChannel;
use App\Models\Document;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\Media;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Redirect;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Slide;
use App\Models\Slider;
use App\Models\Tariff;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CmsPolicy
{
    /**
     * Resolve the permission prefix for a model class or instance.
     *
     * Laravel strips a class-name string from policy method arguments before
     * calling the policy (see Gate::callPolicyMethod()). This helper is used
     * by the Gate::before hook so Filament's class-string based checks still
     * resolve the correct permission group.
     */
    public static function permissionPrefix(Model|string|null $model): ?string
    {
        $class = $model instanceof Model ? $model::class : $model;

        return static::permissionMap()[$class] ?? null;
    }

    /**
     * @return array<class-string, string>
     */
    public static function permissionMap(): array
    {
        return [
            Tariff::class => 'tariffs',
            Technology::class => 'technologies',
            Service::class => 'services',
            Area::class => 'areas',
            Promotion::class => 'promotions',
            Post::class => 'news',
            Page::class => 'pages',
            PageSection::class => 'pages',
            FaqCategory::class => 'faq',
            Faq::class => 'faq',
            HelpCategory::class => 'help',
            HelpArticle::class => 'help',
            Document::class => 'documents',
            Slider::class => 'sliders',
            Slide::class => 'sliders',
            Media::class => 'media',
            Menu::class => 'menus',
            MenuItem::class => 'menus',
            Redirect::class => 'redirects',
            SiteSetting::class => 'settings',
            ContactChannel::class => 'contacts',
        ];
    }

    protected function prefix(Model|string|null $model): string
    {
        return static::permissionPrefix($model) ?? 'settings';
    }

    public function viewAny(User $user, Model|string|null $model = null): bool
    {
        $prefix = $model ? $this->prefix($model) : 'settings';

        return $user->can("{$prefix}.view") || $user->can("{$prefix}.manage");
    }

    public function view(User $user, Model $model): bool
    {
        $prefix = $this->prefix($model);

        return $user->can("{$prefix}.view") || $user->can("{$prefix}.manage");
    }

    public function create(User $user, Model|string|null $model = null): bool
    {
        $prefix = $model ? $this->prefix($model) : 'settings';

        return $user->can("{$prefix}.create") || $user->can("{$prefix}.manage");
    }

    public function update(User $user, Model $model): bool
    {
        $prefix = $this->prefix($model);

        return $user->can("{$prefix}.update") || $user->can("{$prefix}.manage");
    }

    public function delete(User $user, Model $model): bool
    {
        $prefix = $this->prefix($model);

        return $user->can("{$prefix}.delete") || $user->can("{$prefix}.manage");
    }

    public function deleteAny(User $user, Model|string|null $model = null): bool
    {
        $prefix = $model ? $this->prefix($model) : 'settings';

        return $user->can("{$prefix}.delete") || $user->can("{$prefix}.manage");
    }

    public function restore(User $user, Model $model): bool
    {
        return $this->update($user, $model);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $this->delete($user, $model);
    }
}
