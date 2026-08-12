<?php

namespace App\Http\Requests;

use App\Support\UploadCountGuard;
use Illuminate\Foundation\Http\FormRequest;

class UpdateImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'alt_text' => ['nullable', 'string', 'max:125'],
            'is_published' => ['sometimes', 'boolean'],
            'is_private' => ['sometimes', 'boolean'],
            'is_nsfw' => ['sometimes', 'boolean'],
            'content_warning' => ['nullable', 'string', 'max:100'],
            'artist_name' => ['nullable', 'string', 'max:100'],
            'parody' => ['nullable', 'string', 'max:100'],
            'publish_action' => ['sometimes', 'in:draft,publish'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'],
            'additional_images' => ['nullable', 'array', 'max:99'],
            'additional_images.*' => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
            'remove_main' => ['sometimes', 'boolean'],
            'image_order' => ['nullable', 'string', 'max:8000'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['required', 'string', 'max:50', 'regex:/^[\pL\pN\s\-]+$/u'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => 'The file must be a valid image.',
            'image.mimes' => 'Allowed formats: JPEG, PNG, GIF, WebP.',
            'image.max' => 'Images may not be larger than 10 MB.',
            'additional_images.max' => 'You may upload up to 100 images total (1 cover + 99 more).',
            'additional_images.*.image' => 'Additional files must be valid images.',
            'additional_images.*.mimes' => 'Allowed formats: JPEG, PNG, GIF, WebP.',
            'additional_images.*.max' => 'Additional images may not be larger than 10 MB.',
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                UploadCountGuard::check($this, $validator);
            },
        ];
    }
}