<?php

namespace App\Support;

class FlashMessage
{
    /**
     * Store a translation key and replacements for rendering in the active locale.
     *
     * @param array<string, mixed> $replace
     * @return array{__flash_translation: string, replace: array<string, mixed>}
     */
    public static function translated(string $key, array $replace = []): array
    {
        return [
            '__flash_translation' => $key,
            'replace' => $replace,
        ];
    }

    /**
     * Render translated flash payloads while retaining compatibility with strings.
     */
    public static function render(mixed $message): string
    {
        if (is_array($message) && isset($message['__flash_translation'])) {
            return __($message['__flash_translation'], $message['replace'] ?? []);
        }

        return (string) $message;
    }
}
