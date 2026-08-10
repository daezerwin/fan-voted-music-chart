<?php

namespace App\Http\Requests\Admin;

use App\Models\Song;
use App\Support\Youtube\NormalizeYoutubeVideoId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $song = $this->route('song');

        return [
            'artist_id' => ['required', 'integer', 'exists:artists,id'],
            'genre_id' => ['required', 'integer', 'exists:genres,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash',
                Rule::unique('songs', 'slug')->ignore($song instanceof Song ? $song->id : null),
            ],
            'youtube_url' => ['required', 'string', 'max:255'],
            'release_date' => ['nullable', 'date'],
            'cover_image' => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'voting_enabled' => ['boolean'],
            'is_featured' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->filled('slug') ? Str::slug((string) $this->input('slug')) : Str::slug((string) $this->input('title')),
            'is_active' => $this->boolean('is_active'),
            'voting_enabled' => $this->boolean('voting_enabled'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('youtube_url')) {
                return;
            }

            $videoId = (new NormalizeYoutubeVideoId)($this->input('youtube_url'));

            if ($videoId === null) {
                $validator->errors()->add('youtube_url', 'Enter a valid YouTube URL or video ID.');

                return;
            }

            $song = $this->route('song');

            $exists = Song::query()
                ->where('youtube_video_id', $videoId)
                ->when($song instanceof Song, fn ($query) => $query->whereKeyNot($song->id))
                ->exists();

            if ($exists) {
                $validator->errors()->add('youtube_url', 'This YouTube video is already used by another song.');

                return;
            }

            $this->merge(['youtube_video_id' => $videoId]);
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);
        unset($data['youtube_url']);
        $data['youtube_video_id'] = $this->input('youtube_video_id');

        return $data;
    }
}
