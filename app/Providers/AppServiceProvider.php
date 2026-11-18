<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\ContactChannel;
use App\Models\Document;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\Lead;
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
use App\Policies\CmsPolicy;
use App\Policies\LeadPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::before(function (User $user, string $ability, array $arguments = []) {
            // Centralized full-access rule for the single administrative role.
            // An administrator must not be able to deactivate or delete their
            // own account, otherwise the last admin could lock everyone out.
            if ($user->hasRole('admin')) {
                $target = $arguments[0] ?? null;

                if (
                    in_array($ability, ['deactivate', 'delete'], true)
                    && $target instanceof User
                    && $target->is($user)
                ) {
                    return false;
                }

                return true;
            }

            // Laravel removes class-name strings from policy method arguments
            // before invoking the policy, so Filament's class-string based
            // checks (viewAny, create, deleteAny) would lose the model context.
            // Resolve the CMS permission group here, where the raw arguments
            // are still available.
            $prefix = CmsPolicy::permissionPrefix($arguments[0] ?? null);

            if ($prefix === null) {
                return null;
            }

            $suffixes = match ($ability) {
                'viewAny', 'view' => ['view', 'manage'],
                'create', 'replicate' => ['create', 'manage'],
                'update', 'restore', 'reorder' => ['update', 'manage'],
                'delete', 'deleteAny', 'forceDelete' => ['delete', 'manage'],
                default => null,
            };

            if ($suffixes === null) {
                return null;
            }

            foreach ($suffixes as $suffix) {
                if ($user->can("{$prefix}.{$suffix}")) {
                    return true;
                }
            }

            return false;
        });

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Lead::class, LeadPolicy::class);

        foreach ([
            Tariff::class,
            Technology::class,
            Service::class,
            Area::class,
            Promotion::class,
            Post::class,
            Page::class,
            PageSection::class,
            FaqCategory::class,
            Faq::class,
            HelpCategory::class,
            HelpArticle::class,
            Document::class,
            Slider::class,
            Slide::class,
            Media::class,
            Menu::class,
            MenuItem::class,
            Redirect::class,
            SiteSetting::class,
            ContactChannel::class,
        ] as $model) {
            Gate::policy($model, CmsPolicy::class);
        }

        View::composer('*', function () {
            static $loaded = false;

            if ($loaded) {
                return;
            }

            $loaded = true;

            View::share([
                'mainMenu' => Menu::query()->where('slug', 'main')->where('is_active', true)->with('items.children')->first(),
                'footerMenu' => Menu::query()->where('slug', 'footer')->where('is_active', true)->with('items.children')->first(),
                'contactPhones' => ContactChannel::query()->active()->where('type', 'phone')->get(),
                'contactEmails' => ContactChannel::query()->active()->where('type', 'email')->get(),
                'companyName' => SiteSetting::get('company_name', config('app.name')),
                'companyAddress' => SiteSetting::get('company_address', ''),
                'officeHours' => SiteSetting::get('office_hours', ''),
                'supportHours' => SiteSetting::get('support_hours', ''),
                'cabinetUrl' => SiteSetting::get('cabinet_url', 'https://lk.p-svs.ru/'),
                'paymentUrl' => SiteSetting::get('payment_url'),
                'headerCtaLabel' => SiteSetting::get('header_cta_label', 'Проверить подключение'),
                'headerCtaUrl' => SiteSetting::get('header_cta_url', '/coverage'),
                'footerNote' => SiteSetting::get('footer_note', ''),
                'consentTextUrl' => SiteSetting::get('consent_text_url', '/personal-data-consent'),
            ]);
        });
    }
}
