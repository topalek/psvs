<?php

namespace Tests\Feature;

use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\HelpArticles\HelpArticleResource;
use App\Filament\Resources\HelpArticles\Pages\CreateHelpArticle;
use App\Filament\Resources\HelpArticles\Pages\EditHelpArticle;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Promotions\Pages\CreatePromotion;
use App\Filament\Resources\Promotions\Pages\EditPromotion;
use App\Filament\Resources\Promotions\PromotionResource;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Sliders\Pages\CreateSlider;
use App\Filament\Resources\Sliders\Pages\EditSlider;
use App\Filament\Resources\Sliders\SliderResource;
use App\Filament\Resources\Slides\Pages\CreateSlide;
use App\Filament\Resources\Slides\Pages\EditSlide;
use App\Filament\Resources\Slides\SlideResource;
use App\Filament\Resources\Tariffs\Pages\CreateTariff;
use App\Filament\Resources\Tariffs\Pages\EditTariff;
use App\Filament\Resources\Tariffs\TariffResource;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\Document;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\Page;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Slider;
use App\Models\Tariff;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs(User::role('admin')->firstOrFail());
    }

    public function test_tariff_create_edit_and_delete(): void
    {
        Livewire::test(CreateTariff::class)
            ->fillForm([
                'name' => 'Тестовый тариф',
                'slug' => 'test-tariff',
                'regular_price' => 750,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(TariffResource::getUrl('index'));

        $tariff = Tariff::query()->where('slug', 'test-tariff')->firstOrFail();
        $this->assertSame(75000, $tariff->regular_price);

        Livewire::test(EditTariff::class, ['record' => $tariff->getRouteKey()])
            ->fillForm(['name' => 'Тестовый тариф 2'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(TariffResource::getUrl('index'));

        $this->assertSame('Тестовый тариф 2', $tariff->fresh()->name);

        Livewire::test(EditTariff::class, ['record' => $tariff->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertRedirect(TariffResource::getUrl('index'));

        $this->assertModelMissing($tariff);
    }

    public function test_role_permissions_can_be_edited_and_roles_are_fixed(): void
    {
        $role = Role::findByName('engineer');
        $permission = Permission::findByName('news.view');

        Livewire::test(EditRole::class, ['record' => $role->getKey()])
            ->fillForm(['permissions' => [$permission->getKey()]])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(RoleResource::getUrl('index'));

        $this->assertTrue($role->fresh()->hasPermissionTo('news.view'));
        $this->assertFalse(RoleResource::canCreate());
        $this->assertFalse(RoleResource::canDelete($role));
    }

    public function test_user_create_edit_and_delete(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Тестовый менеджер',
                'email' => 'test-manager@psvs.local',
                'password' => 'password123',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(UserResource::getUrl('index'));

        $user = User::query()->where('email', 'test-manager@psvs.local')->firstOrFail();

        Livewire::test(EditUser::class, ['record' => $user->getKey()])
            ->fillForm(['name' => 'Тестовый менеджер 2'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(UserResource::getUrl('index'));

        $this->assertSame('Тестовый менеджер 2', $user->fresh()->name);

        Livewire::test(EditUser::class, ['record' => $user->getKey()])
            ->callAction(DeleteAction::class)
            ->assertRedirect(UserResource::getUrl('index'));

        $this->assertModelMissing($user);
    }

    public function test_post_create_edit_and_delete(): void
    {
        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Тестовая новость',
                'slug' => 'test-post',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(PostResource::getUrl('index'));

        $post = Post::query()->where('slug', 'test-post')->firstOrFail();

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->fillForm(['title' => 'Тестовая новость 2'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(PostResource::getUrl('index'));

        $this->assertSame('Тестовая новость 2', $post->fresh()->title);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertRedirect(PostResource::getUrl('index'));

        $this->assertModelMissing($post);
    }

    public function test_promotion_create_edit_and_delete(): void
    {
        Livewire::test(CreatePromotion::class)
            ->fillForm([
                'title' => 'Тестовая акция',
                'slug' => 'test-promotion',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(PromotionResource::getUrl('index'));

        $promotion = Promotion::query()->where('slug', 'test-promotion')->firstOrFail();

        Livewire::test(EditPromotion::class, ['record' => $promotion->getRouteKey()])
            ->fillForm(['title' => 'Тестовая акция 2'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(PromotionResource::getUrl('index'));

        $this->assertSame('Тестовая акция 2', $promotion->fresh()->title);

        Livewire::test(EditPromotion::class, ['record' => $promotion->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertRedirect(PromotionResource::getUrl('index'));

        $this->assertModelMissing($promotion);
    }

    public function test_slider_create_edit_and_delete(): void
    {
        Livewire::test(CreateSlider::class)
            ->fillForm([
                'name' => 'Тестовый слайдер',
                'slug' => 'test-slider',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(SliderResource::getUrl('index'));

        $slider = Slider::query()->where('slug', 'test-slider')->firstOrFail();

        Livewire::test(EditSlider::class, ['record' => $slider->getRouteKey()])
            ->fillForm(['name' => 'Тестовый слайдер 2'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(SliderResource::getUrl('index'));

        $this->assertSame('Тестовый слайдер 2', $slider->fresh()->name);

        Livewire::test(EditSlider::class, ['record' => $slider->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertRedirect(SliderResource::getUrl('index'));

        $this->assertModelMissing($slider);
    }

    public function test_slide_create_edit_and_delete(): void
    {
        $slider = Slider::query()->firstOrFail();

        Livewire::test(CreateSlide::class)
            ->fillForm([
                'slider_id' => $slider->getKey(),
                'name' => 'Тестовый слайд',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(SlideResource::getUrl('index'));

        $slide = $slider->slides()->where('name', 'Тестовый слайд')->firstOrFail();

        Livewire::test(EditSlide::class, ['record' => $slide->getKey()])
            ->fillForm(['name' => 'Тестовый слайд 2'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(SlideResource::getUrl('index'));

        $this->assertSame('Тестовый слайд 2', $slide->fresh()->name);

        Livewire::test(EditSlide::class, ['record' => $slide->getKey()])
            ->callAction(DeleteAction::class)
            ->assertRedirect(SlideResource::getUrl('index'));

        $this->assertModelMissing($slide);
    }

    public function test_page_create_edit_and_delete(): void
    {
        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'Тестовая страница',
                'slug' => 'test-page',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(PageResource::getUrl('index'));

        $page = Page::query()->where('slug', 'test-page')->firstOrFail();

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['title' => 'Тестовая страница 2'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(PageResource::getUrl('index'));

        $this->assertSame('Тестовая страница 2', $page->fresh()->title);

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertRedirect(PageResource::getUrl('index'));

        $this->assertModelMissing($page);
    }

    public function test_document_create_edit_and_delete(): void
    {
        Livewire::test(CreateDocument::class)
            ->fillForm([
                'title' => 'Тестовый документ',
                'slug' => 'test-document',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(DocumentResource::getUrl('index'));

        $document = Document::query()->where('slug', 'test-document')->firstOrFail();

        Livewire::test(EditDocument::class, ['record' => $document->getRouteKey()])
            ->fillForm(['title' => 'Тестовый документ 2'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(DocumentResource::getUrl('index'));

        $this->assertSame('Тестовый документ 2', $document->fresh()->title);

        Livewire::test(EditDocument::class, ['record' => $document->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertRedirect(DocumentResource::getUrl('index'));

        $this->assertModelMissing($document);
    }

    public function test_faq_create_edit_and_delete(): void
    {
        $category = FaqCategory::query()->firstOrFail();

        Livewire::test(CreateFaq::class)
            ->fillForm([
                'faq_category_id' => $category->getKey(),
                'question' => 'Тестовый вопрос?',
                'answer' => '<p>Тестовый ответ</p>',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(FaqResource::getUrl('index'));

        $faq = Faq::query()->where('question', 'Тестовый вопрос?')->firstOrFail();

        Livewire::test(EditFaq::class, ['record' => $faq->getKey()])
            ->fillForm(['question' => 'Тестовый вопрос 2?'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(FaqResource::getUrl('index'));

        $this->assertSame('Тестовый вопрос 2?', $faq->fresh()->question);

        Livewire::test(EditFaq::class, ['record' => $faq->getKey()])
            ->callAction(DeleteAction::class)
            ->assertRedirect(FaqResource::getUrl('index'));

        $this->assertModelMissing($faq);
    }

    public function test_help_article_create_edit_and_delete(): void
    {
        $category = HelpCategory::query()->firstOrFail();

        Livewire::test(CreateHelpArticle::class)
            ->fillForm([
                'help_category_id' => $category->getKey(),
                'title' => 'Тестовая статья',
                'slug' => 'test-help-article',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(HelpArticleResource::getUrl('index'));

        $article = HelpArticle::query()->where('slug', 'test-help-article')->firstOrFail();

        Livewire::test(EditHelpArticle::class, ['record' => $article->getRouteKey()])
            ->fillForm(['title' => 'Тестовая статья 2'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(HelpArticleResource::getUrl('index'));

        $this->assertSame('Тестовая статья 2', $article->fresh()->title);

        Livewire::test(EditHelpArticle::class, ['record' => $article->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertRedirect(HelpArticleResource::getUrl('index'));

        $this->assertModelMissing($article);
    }
}
