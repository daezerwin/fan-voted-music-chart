<?php

namespace App\Http\Requests\Admin;

use App\Models\Artist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArtistRequest extends FormRequest
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
        $artist = $this->route('artist');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash',
                Rule::unique('artists', 'slug')->ignore($artist instanceof Artist ? $artist->id : null),
            ],
            'bio' => ['nullable', 'string'],
            'image' => ['nullable', 'url', 'max:2048'],
            'country' => ['nullable', 'string', 'max:2'],
            'website' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->filled('slug') ? Str::slug((string) $this->input('slug')) : Str::slug((string) $this->input('name')),
            'is_active' => $this->boolean('is_active'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
