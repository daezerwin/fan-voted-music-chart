<?php

namespace App\Support\Youtube;

class NormalizeYoutubeVideoId
{
    private const ID_PATTERN = '[A-Za-z0-9_-]{11}';

    /**
     * Accept either a bare YouTube video ID or a full YouTube URL
     * (watch, youtu.be, embed, or shorts) and return just the ID.
     * Returns null when the input can't be resolved to a valid ID.
     */
    public function __invoke(string $input): ?string
    {
        $input = trim($input);

        if (preg_match('/^'.self::ID_PATTERN.'$/', $input) === 1) {
            return $input;
        }

        $patterns = [
            '/[?&]v='.self::ID_PATTERN.'/',
            '#youtu\.be/'.self::ID_PATTERN.'#',
            '#/embed/'.self::ID_PATTERN.'#',
            '#/shorts/'.self::ID_PATTERN.'#',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches) === 1) {
                preg_match('/'.self::ID_PATTERN.'/', $matches[0], $idMatch);

                return $idMatch[0] ?? null;
            }
        }

        return null;
    }
}
