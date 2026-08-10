<?php

namespace Tests\Unit\Youtube;

use App\Support\Youtube\NormalizeYoutubeVideoId;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NormalizeYoutubeVideoIdTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string|null}>
     */
    public static function inputs(): iterable
    {
        yield 'bare id' => ['dQw4w9WgXcQ', 'dQw4w9WgXcQ'];
        yield 'watch url' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'];
        yield 'watch url with extra params' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=RDdQw4w9WgXcQ', 'dQw4w9WgXcQ'];
        yield 'short url' => ['https://youtu.be/dQw4w9WgXcQ', 'dQw4w9WgXcQ'];
        yield 'short url with query' => ['https://youtu.be/dQw4w9WgXcQ?t=30', 'dQw4w9WgXcQ'];
        yield 'embed url' => ['https://www.youtube.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'];
        yield 'shorts url' => ['https://www.youtube.com/shorts/dQw4w9WgXcQ', 'dQw4w9WgXcQ'];
        yield 'id with hyphen and underscore' => ['a-b_c1D2e3F', 'a-b_c1D2e3F'];
        yield 'garbage' => ['not a url', null];
        yield 'too short' => ['dQw4w9WgX', null];
        yield 'unrelated url' => ['https://example.com/video', null];
    }

    #[DataProvider('inputs')]
    public function test_it_extracts_the_video_id(string $input, ?string $expected): void
    {
        $this->assertSame($expected, (new NormalizeYoutubeVideoId)($input));
    }
}
