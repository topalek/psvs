<?php

namespace Tests\Unit;

use App\Enums\ContentStatus;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Support\Html;
use Tests\TestCase;

class SupportHelpersTest extends TestCase
{
    public function test_html_sanitizer_removes_scripts_and_event_handlers(): void
    {
        $html = '<p onclick="alert(1)">Текст</p><script>alert("xss")</script>';

        $sanitized = Html::sanitize($html);

        $this->assertStringContainsString('<p>Текст</p>', $sanitized);
        $this->assertStringNotContainsString('<script', $sanitized);
        $this->assertStringNotContainsString('onclick', $sanitized);
    }

    public function test_html_sanitizer_keeps_safe_links_and_blocks_javascript_scheme(): void
    {
        $html = '<a href="https://example.com" target="_blank">Сайт</a><a href="javascript:alert(1)">Плохо</a>';

        $sanitized = Html::sanitize($html);

        $this->assertStringContainsString('https://example.com', $sanitized);
        $this->assertStringNotContainsString('javascript:', $sanitized);
    }

    public function test_content_status_options_are_russian(): void
    {
        $options = ContentStatus::options();

        $this->assertArrayHasKey('draft', $options);
        $this->assertSame('Черновик', $options['draft']);
        $this->assertSame('Опубликовано', $options['published']);
    }

    public function test_lead_enums_expose_labels(): void
    {
        $this->assertSame('Заявка на подключение', LeadType::Connection->label());
        $this->assertSame('Новая', LeadStatus::New->label());
        $this->assertArrayHasKey('spam', LeadStatus::options());
    }
}
