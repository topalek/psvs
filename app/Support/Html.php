<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class Html
{
    protected static ?HtmlSanitizer $sanitizer = null;

    public static function sanitizer(): HtmlSanitizer
    {
        if (static::$sanitizer === null) {
            $config = (new HtmlSanitizerConfig)
                ->allowSafeElements()
                ->allowRelativeLinks()
                ->allowRelativeMedias()
                ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
                ->allowMediaSchemes(['http', 'https'])
                ->allowAttribute('class', allowedElements: '*')
                ->allowAttribute('id', allowedElements: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'section'])
                ->allowAttribute('target', allowedElements: ['a'])
                ->allowAttribute('rel', allowedElements: ['a'])
                ->allowAttribute('title', allowedElements: '*')
                ->allowAttribute('alt', allowedElements: ['img'])
                ->allowAttribute('width', allowedElements: ['img', 'td', 'th'])
                ->allowAttribute('height', allowedElements: ['img'])
                ->allowAttribute('colspan', allowedElements: ['td', 'th'])
                ->allowAttribute('rowspan', allowedElements: ['td', 'th'])
                ->forceHttpsUrls(false)
                ->withMaxInputLength(500_000);

            static::$sanitizer = new HtmlSanitizer($config);
        }

        return static::$sanitizer;
    }

    public static function sanitize(?string $html): string
    {
        return static::sanitizer()->sanitize($html ?? '');
    }

    public static function rich(?string $html): HtmlString
    {
        return new HtmlString(static::sanitize($html));
    }
}
