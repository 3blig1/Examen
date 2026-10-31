<?php

namespace Tests\Feature;

use App\Support\FlashMessage;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class FlashMessageLocalizationTest extends TestCase
{
    public function test_flash_translation_uses_locale_active_when_rendered(): void
    {
        App::setLocale('fr');
        $flash = FlashMessage::translated('flash.already_registered');

        App::setLocale('en');
        $this->assertSame('You are already registered for this exam.', FlashMessage::render($flash));

        App::setLocale('de');
        $this->assertSame('Sie sind bereits für diese Prüfung angemeldet.', FlashMessage::render($flash));
    }

    public function test_flash_translation_replaces_dynamic_values_at_render_time(): void
    {
        App::setLocale('fr');
        $flash = FlashMessage::translated('flash.results_published_count', ['count' => 3]);

        App::setLocale('en');
        $this->assertSame('Results published successfully (3).', FlashMessage::render($flash));
    }
}
